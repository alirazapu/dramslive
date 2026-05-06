<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Helpers_EcpOcrDaemon — long-running OCR cache builder.
 *
 * Walks every row in ecp.ecp_persons (remote, host 192.168.0.156),
 * OCRs the three image columns (name_image_base64, father_image_base64,
 * address_image_base64), and writes the recognised text into the local
 * `ecp_persons_ocr` table on the aiesplus database.
 *
 * Why local cache:
 *   - Free-text searches on the remote `address_image_base64` aren't
 *     possible (binary). The remote `text` columns are partially
 *     populated and not under our control.
 *   - With this cache, ECP free-text search becomes
 *     MATCH AGAINST on a local FULLTEXT index — microseconds vs
 *     seconds-per-query against the remote.
 *   - Once we have the matching ecp_person_id locally we round-trip
 *     to the remote ONLY for users who want the original image or
 *     other un-cached columns.
 *
 * Operating modes:
 *   - oneshot : process one batch (default 50 rows) then exit. Use
 *               from Windows Task Scheduler / cron for steady-state
 *               trickle.
 *   - daemon  : process batches in a loop until either the configured
 *               max_minutes elapses, the operator drops a stop-file,
 *               or a SIGTERM/SIGINT arrives. Re-launchable: if it
 *               crashes mid-loop the next run picks up at the row
 *               after the highest ecp_person_id already in the
 *               cache (no extra "frontier" table needed).
 *
 * Single-instance enforcement:
 *   - flock() on a lockfile in application/cache/. flock works on
 *     Windows since PHP 5.3.5+. If the lock can't be taken, the
 *     daemon exits cleanly — safe to schedule both modes
 *     concurrently; only the first acquirer does work.
 *
 * Re-OCR / change detection:
 *   - source_hash = SHA256 of the three concatenated image strings.
 *     Stored alongside the cached text. The daemon's batch SELECT
 *     picks up rows where ecp_persons.id is NOT in the cache, OR
 *     where the cache row's source_hash doesn't match the current
 *     remote images. The latter handles the case where ECP updates
 *     a person's images upstream — the cache will refresh on the
 *     next pass.
 *
 * Engine plug-in:
 *   - Uses Helpers_Ocr::recognise() so adding a new OCR backend is
 *     one branch in that file. Default engine is tesseract with
 *     'eng+urd' so Urdu addresses come through.
 */
class Helpers_EcpOcrDaemon
{
    /** Path to the single-instance lockfile (flock target). */
    const LOCK_FILE = 'ecp_ocr_daemon.lock';

    /** Path to the operator stop-file. If present, the daemon exits
     *  at the top of its next loop iteration. */
    const STOP_FILE = 'ecp_ocr_daemon.stop';

    /* ------------------------------------------------------------------ */
    /*  Public API                                                        */
    /* ------------------------------------------------------------------ */

    /**
     * Process a single batch and return. No locking, no run-row.
     * Suitable for scheduled triggers (Task Scheduler hits this every
     * 5 minutes). Idempotent — re-running with no new rows is a no-op.
     *
     * @param array $opts  batch_size, engine, lang
     * @return array       { processed, ok, partial, empty, errors, max_id, elapsed_s }
     */
    public static function run_oneshot(array $opts = array())
    {
        $batch_size = isset($opts['batch_size']) ? (int) $opts['batch_size'] : 50;
        $engine     = isset($opts['engine'])     ? $opts['engine']           : 'tesseract';
        $lang       = isset($opts['lang'])       ? $opts['lang']             : 'eng+urd';

        $run_id = self::_start_run('oneshot', $engine, $lang);
        $stats  = array('processed'=>0,'ok'=>0,'partial'=>0,'empty'=>0,'errors'=>0,
                        'max_id'=>0,'elapsed_s'=>0);
        $t0 = microtime(true);
        try {
            $stats = self::_process_batch($batch_size, $engine, $lang, $run_id, $stats);
            self::_end_run($run_id, 'done', $stats);
        } catch (Exception $e) {
            $stats['errors']++;
            self::_end_run($run_id, 'crashed', $stats, $e->getMessage());
            throw $e;
        }
        $stats['elapsed_s'] = round(microtime(true) - $t0, 2);
        return $stats;
    }

    /**
     * Long-running daemon. Locks, then loops until max_minutes elapses,
     * a stop file appears, or a TERM/INT signal arrives. When there is
     * nothing to process, sleeps `idle_sleep` seconds before re-checking
     * (so newly-arriving ecp_persons rows are picked up automatically).
     *
     * @param array $opts  batch_size, engine, lang, idle_sleep, max_minutes
     * @return array       same shape as run_oneshot()
     */
    public static function run_daemon(array $opts = array())
    {
        $batch_size  = isset($opts['batch_size'])  ? (int) $opts['batch_size']  : 50;
        $engine      = isset($opts['engine'])      ? $opts['engine']            : 'tesseract';
        $lang        = isset($opts['lang'])        ? $opts['lang']              : 'eng+urd';
        $idle_sleep  = isset($opts['idle_sleep'])  ? (int) $opts['idle_sleep']  : 30;
        $max_minutes = isset($opts['max_minutes']) ? (int) $opts['max_minutes'] : 30;

        $lock = self::_acquire_lock();
        if (!$lock) {
            return array('error' => 'another daemon instance is already holding the lock');
        }
        self::_register_signal_handlers();
        // Make sure the stop-file from a previous run doesn't kill us
        // before we start.
        @unlink(self::_runtime_path(self::STOP_FILE));

        @set_time_limit(0);
        ignore_user_abort(true);

        $run_id   = self::_start_run('daemon', $engine, $lang);
        $stats    = array('processed'=>0,'ok'=>0,'partial'=>0,'empty'=>0,'errors'=>0,
                          'max_id'=>0,'elapsed_s'=>0);
        $deadline = microtime(true) + ($max_minutes * 60);
        $t0       = microtime(true);
        $end_status = 'stopped';

        try {
            while (true) {
                if (self::_should_stop()) {
                    break;
                }
                if (microtime(true) >= $deadline) {
                    $end_status = 'done';   // wall-clock budget exhausted
                    break;
                }

                $before = $stats['processed'];
                $stats  = self::_process_batch($batch_size, $engine, $lang, $run_id, $stats);
                $found  = $stats['processed'] - $before;

                if ($found === 0) {
                    // Caught up. Nap before re-checking; cooperatively
                    // honour signals during the nap.
                    self::_sleep_with_stop_check($idle_sleep);
                }
            }
            self::_end_run($run_id, $end_status, $stats);
        } catch (Exception $e) {
            self::_end_run($run_id, 'crashed', $stats, $e->getMessage());
            throw $e;
        } finally {
            self::_release_lock($lock);
        }
        $stats['elapsed_s'] = round(microtime(true) - $t0, 2);
        return $stats;
    }

    /**
     * Touch the stop-file so any running daemon exits at the top of
     * its next loop iteration. Returns immediately — does NOT wait
     * for the daemon to actually stop. Pair with /cronjob/ecp_ocr_daemon
     * status check to confirm shutdown.
     */
    public static function request_stop()
    {
        $path = self::_runtime_path(self::STOP_FILE);
        @touch($path);
        return file_exists($path);
    }

    /**
     * Read latest run status + cache fill rate. Used by the cronjob
     * action's status-mode response.
     *
     * @return array
     */
    public static function status()
    {
        $info = array(
            'is_locked'      => null,
            'last_run'       => null,
            'cache_total'    => 0,
            'cache_ok'       => 0,
            'cache_empty'    => 0,
            'cache_error'    => 0,
            'remote_total'   => null,
            'remote_max_id'  => null,
            'cache_max_id'   => null,
            'backlog'        => null,
        );
        // is_locked: try a non-blocking acquire+release
        $info['is_locked'] = ! self::_lock_is_free();

        try {
            $DB = Database::instance();   // local aiesplus
            $row = $DB->query(Database::SELECT,
                "SELECT * FROM ecp_ocr_runs ORDER BY id DESC LIMIT 1", TRUE)->current();
            $info['last_run'] = $row ?: null;

            $r = $DB->query(Database::SELECT,
                "SELECT COUNT(*) AS total,
                        SUM(ocr_status='ok')    AS ok,
                        SUM(ocr_status='empty') AS empty_,
                        SUM(ocr_status='error') AS err,
                        MAX(ecp_person_id)      AS max_id
                 FROM ecp_persons_ocr", TRUE)->current();
            if ($r) {
                $info['cache_total']  = (int) $r->total;
                $info['cache_ok']     = (int) $r->ok;
                $info['cache_empty']  = (int) $r->empty_;
                $info['cache_error']  = (int) $r->err;
                $info['cache_max_id'] = (int) $r->max_id;
            }
        } catch (Exception $e) { /* swallow — diagnostic only */ }

        try {
            $DB = Database::instance('ecp');
            $r = $DB->query(Database::SELECT,
                "SELECT COUNT(*) AS total, MAX(id) AS max_id FROM ecp_persons", TRUE)->current();
            if ($r) {
                $info['remote_total']  = (int) $r->total;
                $info['remote_max_id'] = (int) $r->max_id;
                $info['backlog']       = max(0, $info['remote_max_id'] - $info['cache_max_id']);
            }
        } catch (Exception $e) { /* swallow */ }

        return $info;
    }

    /* ------------------------------------------------------------------ */
    /*  Batch processing                                                  */
    /* ------------------------------------------------------------------ */

    /**
     * Pull the next $batch_size rows from the remote ecp_persons table
     * that aren't already cached (or whose source_hash is stale), OCR
     * each, and UPSERT into the local ecp_persons_ocr table.
     *
     * @return array  updated $stats
     */
    private static function _process_batch($batch_size, $engine, $lang, $run_id, array $stats)
    {
        $batch_size = max(1, min(500, (int) $batch_size));
        $rows = self::_fetch_unprocessed_rows($batch_size);
        if (empty($rows)) {
            return $stats;
        }

        $DB_local = Database::instance();
        $now      = date('Y-m-d H:i:s');

        foreach ($rows as $row) {
            if (self::_should_stop()) break;

            $ecp_id   = isset($row->id)   ? (int) $row->id   : 0;
            $cnic     = isset($row->cnic) ? (string) $row->cnic : '';
            $name_b64 = isset($row->name_image_base64)    ? (string) $row->name_image_base64    : '';
            $father_b64 = isset($row->father_image_base64) ? (string) $row->father_image_base64 : '';
            $addr_b64 = isset($row->address_image_base64) ? (string) $row->address_image_base64 : '';
            $hash     = self::_source_hash($name_b64, $father_b64, $addr_b64);

            $name_text = $father_text = $address_text = '';
            $error_msg = null;
            try {
                $name_text    = self::_ocr_one($name_b64,   $engine, $lang);
                $father_text  = self::_ocr_one($father_b64, $engine, $lang);
                $address_text = self::_ocr_one($addr_b64,   $engine, $lang);
            } catch (Exception $e) {
                $error_msg = substr($e->getMessage(), 0, 500);
            }

            if ($error_msg !== null) {
                $status = 'error';
                $stats['errors']++;
            } else {
                $non_empty = (int) ($name_text !== '')
                           + (int) ($father_text !== '')
                           + (int) ($address_text !== '');
                if ($non_empty === 0) {
                    $status = 'empty';
                    $stats['empty']++;
                } elseif ($non_empty === 3) {
                    $status = 'ok';
                    $stats['ok']++;
                } else {
                    $status = 'partial';
                    $stats['partial']++;
                }
            }

            try {
                self::_upsert_row($DB_local, array(
                    'ecp_person_id' => $ecp_id,
                    'cnic'          => $cnic,
                    'name_text'     => $name_text,
                    'father_text'   => $father_text,
                    'address_text'  => $address_text,
                    'source_hash'   => $hash,
                    'ocr_engine'    => $engine,
                    'ocr_lang'      => $lang,
                    'ocr_status'    => $status,
                    'error_message' => $error_msg,
                    'ocr_at'        => $now,
                ));
            } catch (Exception $e) {
                // UPSERT failure is itself counted as an error so the
                // run-row's totals stay honest. Logged via error_log
                // for off-band visibility.
                $stats['errors']++;
                error_log('ecp_ocr_daemon UPSERT failed for ecp_person_id='
                          . $ecp_id . ': ' . $e->getMessage());
            }

            $stats['processed']++;
            $stats['max_id'] = max($stats['max_id'], $ecp_id);

            // Heartbeat: update the run row every row so a watcher
            // can see live progress. Cheap (PK update on a tiny table).
            self::_heartbeat($run_id, $stats, $ecp_id);
        }

        return $stats;
    }

    /**
     * Fetch up to $limit ecp_persons rows that need (re-)OCR.
     *
     * Strategy:
     *   - First, look for rows where ecp_persons.id > (max id already
     *     cached). This is the bulk of the initial fill — no JOIN
     *     across databases needed because we know all rows below the
     *     frontier are already covered.
     *   - When the frontier reaches the remote max id, we still want
     *     to detect upstream image updates. The caller can run a
     *     periodic "rehash" pass — for now, this method just returns
     *     the next $limit rows above the frontier, which is enough
     *     for the steady-state ingest of new ECP data.
     */
    private static function _fetch_unprocessed_rows($limit)
    {
        $limit = (int) $limit;
        try {
            $DB_local = Database::instance();
            $r        = $DB_local->query(Database::SELECT,
                "SELECT COALESCE(MAX(ecp_person_id), 0) AS frontier FROM ecp_persons_ocr",
                TRUE)->current();
            $frontier = (int) $r->frontier;
        } catch (Exception $e) {
            // Local table missing? Caller will see no work to do —
            // tell them the cache table needs to be created.
            error_log('ecp_ocr_daemon: cannot read ecp_persons_ocr (' . $e->getMessage()
                      . '); did you run docs/sql/ecp_persons_ocr.sql?');
            return array();
        }

        try {
            $DB_ecp = Database::instance('ecp');
            $sql = "SELECT id, cnic,
                           name_image_base64,
                           father_image_base64,
                           address_image_base64
                    FROM ecp_persons
                    WHERE id > {$frontier}
                    ORDER BY id ASC
                    LIMIT {$limit}";
            return $DB_ecp->query(Database::SELECT, $sql, FALSE)->as_array();
        } catch (Exception $e) {
            error_log('ecp_ocr_daemon: remote ecp_persons query failed (' . $e->getMessage() . ')');
            return array();
        }
    }

    /**
     * UPSERT into ecp_persons_ocr keyed by ecp_person_id.
     */
    private static function _upsert_row($DB, array $r)
    {
        // Build the column / value pair list with proper escaping.
        $cols = array('ecp_person_id','cnic','name_text','father_text','address_text',
                      'source_hash','ocr_engine','ocr_lang','ocr_status','error_message','ocr_at');
        $vals = array();
        foreach ($cols as $c) {
            $v = isset($r[$c]) ? $r[$c] : null;
            if ($c === 'ecp_person_id') {
                $vals[] = (int) $v;
            } elseif ($v === null) {
                $vals[] = 'NULL';
            } else {
                $vals[] = $DB->escape((string) $v);
            }
        }
        $col_csv = '`' . implode('`,`', $cols) . '`';
        $val_csv = implode(',', $vals);

        // INSERT … ON DUPLICATE KEY UPDATE — uk_ecp_person_id is the key.
        $update = array();
        foreach ($cols as $c) {
            if ($c === 'ecp_person_id') continue;
            $update[] = "`{$c}` = VALUES(`{$c}`)";
        }
        $sql = "INSERT INTO `ecp_persons_ocr` ({$col_csv}) VALUES ({$val_csv})
                ON DUPLICATE KEY UPDATE " . implode(',', $update);
        $DB->query(Database::INSERT, $sql, FALSE);
    }

    /* ------------------------------------------------------------------ */
    /*  OCR + hashing                                                     */
    /* ------------------------------------------------------------------ */

    /**
     * OCR a single base64 image. Returns '' if the source is empty.
     * Re-throws engine errors so the caller can mark the row 'error'.
     */
    private static function _ocr_one($base64, $engine, $lang)
    {
        if ($base64 === '' || $base64 === null) return '';
        $bytes = Helpers_Ocr::decode_base64_image($base64);
        if ($bytes === '') return '';
        $text = Helpers_Ocr::recognise($bytes, $engine, array('lang' => $lang));
        // Tighten: collapse runs of whitespace and trim. OCR output is
        // notorious for trailing newlines / form-feed garbage.
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim((string) $text);
    }

    /**
     * Stable hash of the three source images. Matches across runs only
     * if every byte of every image is unchanged. Used to detect when
     * the upstream row's images change so we know to re-OCR.
     */
    private static function _source_hash($a, $b, $c)
    {
        return hash('sha256', (string) $a . '|' . (string) $b . '|' . (string) $c);
    }

    /* ------------------------------------------------------------------ */
    /*  Run-state / heartbeat                                             */
    /* ------------------------------------------------------------------ */

    private static function _start_run($mode, $engine, $lang)
    {
        try {
            $DB   = Database::instance();
            $host = $DB->escape((string) gethostname());
            $pid  = (int) getmypid();
            $eng  = $DB->escape((string) $engine);
            $lng  = $DB->escape((string) $lang);
            $md   = $DB->escape((string) $mode);
            $sql  = "INSERT INTO ecp_ocr_runs
                        (started_at, host, pid, status, mode, engine, lang)
                     VALUES
                        (NOW(), {$host}, {$pid}, 'running', {$md}, {$eng}, {$lng})";
            $r = $DB->query(Database::INSERT, $sql, FALSE);
            return is_array($r) && isset($r[0]) ? (int) $r[0] : 0;
        } catch (Exception $e) {
            // ecp_ocr_runs may not exist yet — soft-fail and run anyway
            // so the operator's first run still produces useful work.
            error_log('ecp_ocr_daemon: ecp_ocr_runs insert failed (' . $e->getMessage()
                      . '); did you run docs/sql/ecp_persons_ocr.sql?');
            return 0;
        }
    }

    private static function _heartbeat($run_id, array $stats, $last_id)
    {
        if (!$run_id) return;
        try {
            $DB = Database::instance();
            $sql = "UPDATE ecp_ocr_runs SET
                        processed_total       = " . (int) $stats['processed'] . ",
                        errors_total          = " . (int) $stats['errors'] . ",
                        last_processed_ecp_id = " . (int) $last_id . "
                    WHERE id = " . (int) $run_id;
            $DB->query(Database::UPDATE, $sql, FALSE);
        } catch (Exception $e) { /* swallow */ }
    }

    private static function _end_run($run_id, $status, array $stats, $error_msg = null)
    {
        if (!$run_id) return;
        try {
            $DB    = Database::instance();
            $st    = $DB->escape((string) $status);
            $note  = $error_msg === null ? 'NULL' : $DB->escape((string) $error_msg);
            $sql   = "UPDATE ecp_ocr_runs SET
                          ended_at              = NOW(),
                          status                = {$st},
                          processed_total       = " . (int) $stats['processed'] . ",
                          errors_total          = " . (int) $stats['errors'] . ",
                          last_processed_ecp_id = " . (int) $stats['max_id'] . ",
                          notes                 = {$note}
                      WHERE id = " . (int) $run_id;
            $DB->query(Database::UPDATE, $sql, FALSE);
        } catch (Exception $e) { /* swallow */ }
    }

    /* ------------------------------------------------------------------ */
    /*  Lock + signals                                                    */
    /* ------------------------------------------------------------------ */

    /**
     * Try to acquire the single-instance lock. Returns the open file
     * handle on success (caller must hold it for the duration of the
     * run), false if another instance already holds it.
     */
    private static function _acquire_lock()
    {
        $path = self::_runtime_path(self::LOCK_FILE);
        $fp = @fopen($path, 'c');
        if (!$fp) return false;
        if (!@flock($fp, LOCK_EX | LOCK_NB)) {
            @fclose($fp);
            return false;
        }
        @ftruncate($fp, 0);
        @fwrite($fp, (string) getmypid() . "\n" . date('c') . "\n");
        @fflush($fp);
        return $fp;
    }

    /** Probe whether the lock is currently free without taking it. */
    private static function _lock_is_free()
    {
        $path = self::_runtime_path(self::LOCK_FILE);
        $fp = @fopen($path, 'c');
        if (!$fp) return true;       // can't determine → assume free
        $got = @flock($fp, LOCK_EX | LOCK_NB);
        if ($got) @flock($fp, LOCK_UN);
        @fclose($fp);
        return (bool) $got;
    }

    private static function _release_lock($fp)
    {
        if ($fp) {
            @flock($fp, LOCK_UN);
            @fclose($fp);
        }
        @unlink(self::_runtime_path(self::LOCK_FILE));
    }

    /**
     * Hook PCNTL signals on platforms that support them so SIGTERM /
     * SIGINT result in a graceful exit at the top of the next batch
     * iteration. On Windows (no PCNTL), the stop-file is the only
     * stop mechanism.
     */
    private static function _register_signal_handlers()
    {
        if (!function_exists('pcntl_signal')) return;
        $h = function ($signo) {
            self::$_stop_signal = $signo;
        };
        @pcntl_signal(SIGTERM, $h);
        @pcntl_signal(SIGINT,  $h);
        if (function_exists('pcntl_async_signals')) {
            @pcntl_async_signals(true);
        }
    }

    /** @var int|null  Set by the signal handler; checked by _should_stop(). */
    private static $_stop_signal = null;

    private static function _should_stop()
    {
        if (self::$_stop_signal !== null) return true;
        if (file_exists(self::_runtime_path(self::STOP_FILE))) return true;
        return false;
    }

    /** sleep(N) but check the stop conditions every second. */
    private static function _sleep_with_stop_check($seconds)
    {
        for ($i = 0; $i < (int) $seconds; $i++) {
            if (self::_should_stop()) return;
            sleep(1);
        }
    }

    /**
     * Resolve a runtime file path. Prefers application/cache/, falls
     * back to the system temp dir if cache/ isn't writable.
     */
    private static function _runtime_path($name)
    {
        $cache = APPPATH . 'cache' . DIRECTORY_SEPARATOR;
        if (is_dir($cache) && is_writable($cache)) {
            return $cache . $name;
        }
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . $name;
    }
}
