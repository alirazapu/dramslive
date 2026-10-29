<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Helpers_Databank — data source of the DRAMS Databank search
 * (Controller_Databank::action_all_sources). The records come from the
 * Databank integration API (Helpers_DatabankApi, POST integration/search);
 * DRAMS does not query the external databases for that page.
 *
 * Records of one source differ in their fields, so each source's table
 * takes its columns from the records (records_table()).
 *
 * Errors are NOT swallowed: Helpers_DatabankApi_Exception reaches the
 * controller, which shows it instead of "no matching records".
 *
 * dlms_pdo() is unrelated to the Databank page: the person dashboard's
 * DLMS panel (Helpers_Person::get_person_external_profile_driving_license)
 * still reads the DLMS server directly through it.
 */
class Helpers_Databank
{
    /**
     * lu_user_access_type.internal_name of the "DRAMS Databank" right
     * (docs/sql/drams_databank.sql), granted per user in Access Control
     * List -> Manage Rights. Looked up by name, not id, so the right's
     * auto-increment id may differ between servers.
     */
    const RIGHT_INTERNAL_NAME = 'drams_databank';

    /** The user holds the DRAMS Databank right (menu and page). */
    public static function has_access($user_id)
    {
        $sql = "SELECT t1.permission
                FROM user_access_matrix AS t1
                join lu_user_access_type AS t2 on t2.id = t1.user_activity_type
                where t1.user_id = " . (int) $user_id . " and t2.internal_name = '" . self::RIGHT_INTERNAL_NAME . "' and t1.permission = 1
                LIMIT 1";
        $result = DB::query(Database::SELECT, $sql)->execute()->current();
        return !empty($result);
    }

    /**
     * Cached raw PDO handle for the DLMS SQL Server. We bypass Kohana's
     * Database_PDO driver for this one connection because the driver
     * runs `SET NAMES 'utf8'` after connect (MySQL-only syntax) which
     * SQL Server rejects, and surfaces other unsupported PDO attributes
     * (ATTR_TIMEOUT) to pdo_sqlsrv. A direct PDO with the verified-
     * working DSN + ATTR_ERRMODE only is more reliable.
     *
     * Set by self::dlms_pdo() on first call, reused for the request.
     */
    private static $_dlms_pdo = null;

    /**
     * Open (or return the cached) raw PDO connection to DLMS.
     *
     * Reads the DSN, username and password from
     * application/config/database.php (the `dlms_sqlsrv` block — same
     * source of truth as Kohana's Database::instance), but constructs
     * the PDO object directly with only PDO::ATTR_ERRMODE set and
     * skips Kohana's driver layer entirely.
     *
     * @return PDO|null  PDO on success, NULL on connect failure (logged
     *                   via Model_ErrorLog::log so callers can fail
     *                   silently without losing diagnostic info).
     */
    public static function dlms_pdo()
    {
        if (self::$_dlms_pdo instanceof PDO) {
            return self::$_dlms_pdo;
        }
        try {
            $cfg = Kohana::$config->load('database')->get('dlms_sqlsrv');
            if (!is_array($cfg) || empty($cfg['connection'])) {
                throw new Exception('dlms_sqlsrv block missing from application/config/database.php');
            }
            $conn = $cfg['connection'];
            $dsn  = isset($conn['dsn'])      ? (string) $conn['dsn']      : '';
            $user = isset($conn['username']) ? (string) $conn['username'] : '';
            $pass = isset($conn['password']) ? (string) $conn['password'] : '';

            // ATTR_ERRMODE is the ONLY attribute we set at construction.
            // pdo_sqlsrv rejects most others (ATTR_TIMEOUT,
            // ATTR_DEFAULT_FETCH_MODE) with "SQLSTATE[IMSSP]: An
            // unsupported attribute was designated on the PDO object".
            self::$_dlms_pdo = new PDO($dsn, $user, $pass, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ));
            return self::$_dlms_pdo;
        } catch (Exception $e) {
            self::_log_failure('dlms_pdo_connect', $e, array());
            return null;
        }
    }

    /**
     * One identifier in every source the API user may read, one block
     * per source.
     *
     * @return array ['blocks' => list of [code, label, status, statusLabel,
     *               message, rows, columns], 'linked' => list of steps
     *               followed, 'limit' => int, 'request_id' => string]
     * @throws Helpers_DatabankApi_Exception
     */
    public static function search_all_sources($type, $value, $linked)
    {
        $json   = Helpers_DatabankApi::search_all($type, $value, $linked);
        $text   = function ($a, $k) { return (isset($a[$k]) && is_scalar($a[$k])) ? (string) $a[$k] : ''; };
        $blocks = array();
        foreach ($json['sources'] as $s) {
            if (!is_array($s)) {
                continue;
            }
            $records = (isset($s['records']) && is_array($s['records'])) ? array_filter($s['records'], 'is_array') : array();
            $table   = self::records_table(array_values($records));
            $blocks[] = array(
                'code'        => $text($s, 'code'),
                'label'       => $text($s, 'label'),
                'status'      => $text($s, 'status'),
                'statusLabel' => $text($s, 'statusLabel'),
                'message'     => $text($s, 'message'),
                'rows'        => $table['rows'],
                'columns'     => $table['columns'],
            );
        }
        $steps = array();
        if (isset($json['linked']['steps']) && is_array($json['linked']['steps'])) {
            foreach ($json['linked']['steps'] as $step) {
                if (is_string($step)) {
                    $steps[] = $step;
                }
            }
        }
        return array('blocks' => $blocks, 'linked' => $steps,
                     'limit' => max(1, min(50, (int) Kohana::$config->load('databank')->get('records_per_search', 25))),
                     'request_id' => isset($json['requestId']) ? preg_replace('/[^A-Za-z0-9\-]/', '', (string) $json['requestId']) : '');
    }

    /**
     * Records whose fields differ as one table: a column per field label
     * (in order of first appearance; images as image columns), then
     * "Reference" and "Found through" when any record has them.
     *
     * @return array ['columns' => results-view column configs, 'rows' => rows keyed by column key]
     */
    public static function records_table(array $records)
    {
        $columns = array();
        $rows    = array();
        $extra   = array();
        foreach ($records as $rec) {
            $row = array();
            foreach (self::_fields($rec) as $label => $value) {
                $image = strpos($value, 'data:image/') === 0;
                if ($value === '' || ($image && !preg_match('#^data:image/(jpeg|png|gif|webp);base64,[A-Za-z0-9+/=]+$#', $value))) {
                    continue;
                }
                $key = 'f_' . md5($label);
                if (!isset($columns[$key])) {
                    $columns[$key] = array('key' => $key, 'label' => $label, 'formatter' => $image ? 'image_jpeg' : 'text');
                }
                $row[$key] = $value;
            }
            foreach (array('_reference' => 'reference', '_found' => 'foundThrough') as $key => $prop) {
                if (isset($rec[$prop]) && is_string($rec[$prop]) && trim($rec[$prop]) !== '') {
                    $row[$key]   = trim($rec[$prop]);
                    $extra[$key] = TRUE;
                }
            }
            $rows[] = $row;
        }
        if (isset($extra['_reference'])) {
            $columns['_reference'] = array('key' => '_reference', 'label' => 'Reference');
        }
        if (isset($extra['_found'])) {
            $columns['_found'] = array('key' => '_found', 'label' => 'Found through');
        }
        return array('columns' => array_values($columns), 'rows' => $rows);
    }

    /* ------------------------------------------------------------------ */
    /*  Internal helpers                                                  */
    /* ------------------------------------------------------------------ */

    /**
     * A record's fields as label => value (the first value of a label
     * wins). An ECP image is added as "<label> (image)" when it is a
     * plain base64 data URI — anything else is dropped, never rendered.
     */
    private static function _fields(array $rec)
    {
        $out    = array();
        $fields = (isset($rec['fields']) && is_array($rec['fields'])) ? $rec['fields'] : array();
        foreach ($fields as $f) {
            if (!is_array($f) || !isset($f['label']) || !is_string($f['label']) || isset($out[$f['label']])) {
                continue;
            }
            $out[$f['label']] = (isset($f['value']) && is_scalar($f['value'])) ? trim((string) $f['value']) : '';
            if (isset($f['image']) && is_string($f['image'])
                && preg_match('#^data:image/(jpeg|png|gif|webp);base64,[A-Za-z0-9+/=]+$#', $f['image'])) {
                $out[$f['label'] . ' (image)'] = $f['image'];
            }
        }
        return $out;
    }

    /**
     * Centralised exception logger for dlms_pdo(). Mirrors the call
     * style used by zong.inc / Cronjob.php / the cdr parsers — keeps
     * every failure searchable in system_error_log via the same
     * `error_source` prefix.
     */
    private static function _log_failure($helper, Exception $e, array $filters)
    {
        try {
            Model_ErrorLog::log(
                'helpers_databank_' . $helper,
                $e->getMessage(),
                array(
                    'filters' => $filters,
                    'class'   => get_class($e),
                ),
                $e->getTraceAsString(),
                'database_error',
                'databank_search',
                'error'
            );
        } catch (Exception $log_failed) {
            // Logger itself blew up — last-ditch fallback so we don't
            // double-fault the search.
            error_log('Helpers_Databank::' . $helper . ' failed; logger also failed: '
                . $e->getMessage() . ' | logger: ' . $log_failed->getMessage());
        }
    }
}
