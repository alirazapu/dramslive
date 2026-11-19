<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Helpers_PatternOfLife — "Pattern of Life" analysis of one person's CDRs
 * (persons/pattern_of_life). Read-only: it only SELECTs from
 * person_call_log and person_sms_log.
 *
 * Every query is aggregated in MySQL and filtered on
 * (person_id, phone_number, call_at / sms_at), which the indexes
 * idx_pid_phone_callat / idx_pid_phone_smsat cover, so the browser gets
 * summaries (places, hour x weekday grid, daily totals, daily track,
 * devices) rather than raw rows.
 *
 * A "place" is a cell tower: its coordinates (rounded to 5 decimals,
 * ~1 m) when the operator sent them, otherwise its tower address text
 * (some operators send only e.g. "Jananabad - Karak"). Only places with
 * coordinates can be drawn on the map or measured in km. "Home" and
 * "work" mean the tower most used at night / in weekday working hours,
 * not an exact address.
 */
class Helpers_PatternOfLife
{
    /** Longest date range one analysis may cover. */
    const MAX_RANGE_DAYS = 366;

    /** Default range: this many days ending at the person's latest record. */
    const DEFAULT_RANGE_DAYS = 90;

    /** Night = 22:00-05:59, work = Mon-Fri 09:00-16:59 (tower location). */
    const NIGHT_FROM = 22;
    const NIGHT_TO   = 6;
    const WORK_FROM  = 9;
    const WORK_TO    = 17;

    /** A place needs at least this many night / work events to count as home / work. */
    const MIN_ANCHOR_EVENTS = 5;

    /** Places farther than this from home are reported as travel. */
    const TRAVEL_KM = 50;

    /** No activity for at least this many days is reported as a silent period. */
    const SILENT_DAYS = 3;

    /** Row caps for the browser payload. */
    const MAX_PLACES = 400;
    const MAX_TRACK  = 20000;

    /**
     * Phone numbers seen in the person's call / SMS logs, newest first.
     *
     * @return array list of ['phone' => string, 'last' => 'Y-m-d H:i:s']
     */
    public static function phone_numbers($pid)
    {
        $sql = "SELECT phone_number AS phone, MAX(t) AS last FROM (
                    SELECT phone_number, MAX(call_at) AS t FROM person_call_log
                     WHERE person_id = :pid AND phone_number IS NOT NULL GROUP BY phone_number
                    UNION ALL
                    SELECT phone_number, MAX(sms_at) AS t FROM person_sms_log
                     WHERE person_id = :pid AND phone_number IS NOT NULL GROUP BY phone_number
                ) x GROUP BY phone_number ORDER BY last DESC";
        $rows = DB::query(Database::SELECT, $sql)->param(':pid', (int) $pid)->execute()->as_array();
        $out = array();
        foreach ($rows as $r) {
            $out[] = array('phone' => (string) $r['phone'], 'last' => (string) $r['last']);
        }
        return $out;
    }

    /**
     * Validate the requested phone and dates, defaulting the range to the
     * DEFAULT_RANGE_DAYS before the person's latest record.
     *
     * @return array ['phone' => string ('' = all numbers), 'from' => 'Y-m-d', 'to' => 'Y-m-d']
     * @throws Kohana_Exception with a message safe to show the user
     */
    /**
     * The chosen number as digits ('' = all numbers); it must be one of the
     * person's own numbers.
     *
     * @throws Kohana_Exception
     */
    public static function validate_phone($pid, $phone)
    {
        $phone = preg_replace('/\D/', '', (string) $phone);
        if ($phone === '') {
            return '';
        }
        foreach (self::phone_numbers($pid) as $p) {
            if ($p['phone'] === $phone) {
                return $phone;
            }
        }
        throw new Kohana_Exception('This number has no call or SMS records for this person.');
    }

    public static function resolve_filters($pid, $phone, $from, $to)
    {
        $phone = self::validate_phone($pid, $phone);

        $from = self::parse_date($from);
        $to   = self::parse_date($to);
        if ($from === NULL || $to === NULL) {
            $latest = self::latest_record($pid, $phone);
            if ($latest === NULL) {
                throw new Kohana_Exception('No call or SMS records found for this person.');
            }
            $to   = ($to !== NULL) ? $to : $latest;
            $from = ($from !== NULL) ? $from : date('Y-m-d', strtotime($to . ' -' . (self::DEFAULT_RANGE_DAYS - 1) . ' days'));
        }
        if ($from > $to) {
            list($from, $to) = array($to, $from);
        }
        $days = (int) round((strtotime($to) - strtotime($from)) / 86400) + 1;
        if ($days > self::MAX_RANGE_DAYS) {
            throw new Kohana_Exception('Choose a date range of at most ' . self::MAX_RANGE_DAYS . ' days.');
        }
        return array('phone' => $phone, 'from' => $from, 'to' => $to);
    }

    /**
     * The full analysis for one person (optionally one number) and range.
     *
     * @param array $f resolve_filters() result
     * @return array JSON-ready analysis
     */
    public static function analyse($pid, array $f)
    {
        $places  = self::places($pid, $f);
        $grid    = self::activity_grid($pid, $f);
        $daily   = self::daily($pid, $f);
        $track   = self::track($pid, $f);
        $devices = self::devices($pid, $f);

        $home = self::anchor($places, 'night', NULL);
        $work = self::anchor($places, 'work', $home);

        $totals = array('calls' => 0, 'sms' => 0, 'with_place' => 0, 'on_map' => 0, 'days_active' => count($daily));
        foreach ($daily as $d) {
            $totals['calls'] += $d['calls'];
            $totals['sms']   += $d['sms'];
        }
        foreach ($places as $p) {
            $totals['with_place'] += $p['n'];
            if ($p['lat'] !== NULL) {
                $totals['on_map'] += $p['n'];
            }
        }

        return array(
            'filters'   => $f,
            'totals'    => $totals,
            'home'      => $home,
            'work'      => $work,
            'places'    => array_slice($places, 0, self::MAX_PLACES),
            'grid'      => $grid,
            'peak'      => self::peak_hours($grid),
            'daily'     => $daily,
            'track'     => $track,
            'devices'   => $devices,
            'devices_all_time' => self::devices_all_time($pid, $f['phone']),
            'anomalies' => self::anomalies($f, $daily, $places, $home, $devices),
        );
    }

    /* ------------------------------------------------------------------ */
    /*  "Where was he at ...?" (incident check)                           */
    /* ------------------------------------------------------------------ */

    /** Window choices (minutes either side of the incident time). */
    public static $AT_WINDOWS = array(15, 30, 60, 180, 360, 720);

    /**
     * Validate an incident check request.
     *
     * @return array ['at' => 'Y-m-d H:i:00', 'window' => minutes, 'site' => ['lat','lng'] | NULL]
     * @throws Kohana_Exception with a message safe to show the user
     */
    public static function resolve_at($at, $window, $lat, $lng)
    {
        $t = strtotime(trim((string) $at));
        if ($t === FALSE || trim((string) $at) === '') {
            throw new Kohana_Exception('Enter the date and time of the incident.');
        }
        $window = (int) $window;
        if (!in_array($window, self::$AT_WINDOWS, TRUE)) {
            $window = 60;
        }
        $site = NULL;
        if (trim((string) $lat) !== '' || trim((string) $lng) !== '') {
            if (!is_numeric($lat) || !is_numeric($lng) || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
                throw new Kohana_Exception('The incident site must be a latitude and longitude, e.g. 34.0151, 71.5249.');
            }
            $site = array('lat' => round((float) $lat, 6), 'lng' => round((float) $lng, 6));
        }
        return array('at' => date('Y-m-d H:i:00', $t), 'window' => $window, 'site' => $site);
    }

    /**
     * The person's calls / SMS within $q['window'] minutes of $q['at'],
     * plus the nearest record before and after that window, each with its
     * time offset and distance from the incident site and from home.
     *
     * @param array $q     resolve_at() result
     * @param string $phone '' for all the person's numbers
     * @param array|NULL $home home place from analyse(), for km-from-home
     */
    public static function at_time($pid, $phone, array $q, $home = NULL)
    {
        $at    = strtotime($q['at']);
        $start = date('Y-m-d H:i:s', $at - 60 * $q['window']);
        $end   = date('Y-m-d H:i:s', $at + 60 * $q['window']);

        $in     = self::at_rows($pid, $phone, "BETWEEN :s AND :e", 'ASC', 200, $start, $end);
        $before = self::at_rows($pid, $phone, "< :s", 'DESC', 1, $start, $end);
        $after  = self::at_rows($pid, $phone, "> :e", 'ASC', 1, $start, $end);

        $decorate = function (array $r) use ($at, $q, $home) {
            $r['minutes'] = (int) round((strtotime($r['t']) - $at) / 60);
            $r['km_site'] = ($q['site'] !== NULL) ? self::round_km(self::km($r, $q['site'])) : NULL;
            $r['km_home'] = ($home !== NULL) ? self::round_km(self::km($r, $home)) : NULL;
            $r['at_home'] = ($home !== NULL && $r['place'] !== NULL && $r['place'] === $home['key']);
            return $r;
        };
        $in     = array_map($decorate, $in);
        $before = $before ? $decorate($before[0]) : NULL;
        $after  = $after ? $decorate($after[0]) : NULL;

        // The record closest in time to the incident (inside the window first).
        $closest = NULL;
        foreach ($in ? $in : array_filter(array($before, $after)) as $r) {
            if ($closest === NULL || abs($r['minutes']) < abs($closest['minutes'])) {
                $closest = $r;
            }
        }

        return array(
            'query'   => $q,
            'records' => $in,
            'before'  => $before,
            'after'   => $after,
            'closest' => $closest,
            'summary' => self::at_summary($q, $in, $closest),
        );
    }

    protected static function at_rows($pid, $phone, $time_cond, $order, $limit, $start, $end)
    {
        $where = ($phone !== '') ? ' AND phone_number = :phone' : '';
        $sql = "SELECT * FROM (
                    (SELECT call_at AS t, 'call' AS kind, is_outgoing, other_person_phone_number AS other,
                            duration_in_seconds AS duration, phone_number AS phone,
                            latitude, longitude, address, imei_number AS imei
                       FROM person_call_log
                      WHERE person_id = :pid{$where} AND call_at {$time_cond}
                   ORDER BY call_at {$order} LIMIT {$limit})
                    UNION ALL
                    (SELECT sms_at, 'sms', is_outgoing, other_person_phone_number, 0, phone_number,
                            latitude, longitude, address, imei_number
                       FROM person_sms_log
                      WHERE person_id = :pid{$where} AND sms_at {$time_cond}
                   ORDER BY sms_at {$order} LIMIT {$limit})
                ) x ORDER BY t {$order} LIMIT {$limit}";
        $q = DB::query(Database::SELECT, $sql)
            ->param(':pid', (int) $pid)->param(':s', $start)->param(':e', $end);
        if ($phone !== '') {
            $q->param(':phone', $phone);
        }
        $out = array();
        foreach ($q->execute()->as_array() as $r) {
            $located = ($r['latitude'] > 0 && $r['longitude'] > 0);
            $lat = $located ? round((float) $r['latitude'], 5) : NULL;
            $lng = $located ? round((float) $r['longitude'], 5) : NULL;
            $addr = trim((string) $r['address']);
            $out[] = array(
                't' => $r['t'], 'kind' => $r['kind'], 'outgoing' => (int) $r['is_outgoing'] === 1,
                'other' => (string) $r['other'], 'duration' => (int) $r['duration'], 'phone' => (string) $r['phone'],
                'lat' => $lat, 'lng' => $lng, 'address' => self::clean_address($addr),
                'place' => $located ? ($lat . ',' . $lng) : ($addr !== '' ? $addr : NULL),
                'imei' => ((string) $r['imei'] !== '0') ? (string) $r['imei'] : '',
            );
        }
        if ($order === 'DESC') {
            $out = array_reverse($out);
        }
        return $out;
    }

    /** One plain sentence answering "where was he?". */
    protected static function at_summary(array $q, array $in, $closest)
    {
        $when = date('d M Y H:i', strtotime($q['at']));
        if ($closest === NULL) {
            return 'No call or SMS records at all around ' . $when . '.';
        }
        $off   = abs($closest['minutes']);
        $rel   = ($closest['minutes'] === 0) ? 'at the incident time'
               : (self::duration_text($off) . ($closest['minutes'] < 0 ? ' before' : ' after'));
        $place = ($closest['address'] !== '') ? $closest['address']
               : (($closest['lat'] !== NULL) ? $closest['lat'] . ', ' . $closest['lng'] : 'an unknown tower');
        $text  = $in
            ? count($in) . ' record(s) within ±' . self::duration_text($q['window']) . ' of ' . $when . '. Closest: '
            : 'No records within ±' . self::duration_text($q['window']) . ' of ' . $when . '. Nearest: ';
        $text .= $closest['kind'] . ' at ' . date('H:i', strtotime($closest['t'])) . ' on ' . date('d M', strtotime($closest['t']))
               . ' (' . $rel . ') via ' . $place;
        if ($closest['km_site'] !== NULL) {
            $text .= ', ' . $closest['km_site'] . ' km from the incident site';
        } elseif ($q['site'] !== NULL) {
            $text .= ' (tower has no coordinates, distance unknown)';
        }
        if ($closest['at_home']) {
            $text .= ' — this is the home tower';
        }
        return $text . '.';
    }

    protected static function duration_text($minutes)
    {
        if ($minutes < 60) {
            return $minutes . ' min';
        }
        if ($minutes >= 2880) {
            return round($minutes / 1440) . ' days';
        }
        $h = floor($minutes / 60);
        $m = $minutes % 60;
        return $h . ' h' . ($m ? ' ' . $m . ' min' : '');
    }

    protected static function round_km($km)
    {
        return ($km === NULL) ? NULL : (($km < 10) ? round($km, 1) : round($km));
    }

    /* ------------------------------------------------------------------ */
    /*  Report integrity                                                  */
    /* ------------------------------------------------------------------ */

    /**
     * SHA-256 over the report's content and metadata (canonical JSON).
     * Logged when the report is generated, so a printed copy can be
     * checked later: same hash in the log = report not altered.
     */
    public static function report_hash(array $meta, array $analysis, $at = NULL)
    {
        return hash('sha256', json_encode(array('meta' => $meta, 'analysis' => $analysis, 'at' => $at)));
    }

    /* ------------------------------------------------------------------ */
    /*  Queries                                                           */
    /* ------------------------------------------------------------------ */

    /**
     * Calls and SMS of the person in the range as one derived table with
     * columns t, kind ('c' / 's'), lat, lng (NULL when not sent), place
     * (the tower key: "lat,lng" or the address; NULL when neither), imei,
     * imsi, address. Placeholders :pid, :from, :to (and :phone when a
     * number is chosen).
     */
    protected static function events_sql(array $f)
    {
        $phone = ($f['phone'] !== '') ? ' AND phone_number = :phone' : '';
        $cols  = function ($time, $kind) {
            return "{$time} AS t, '{$kind}' AS kind,
                    IF(latitude > 0 AND longitude > 0, ROUND(latitude, 5), NULL) AS lat,
                    IF(latitude > 0 AND longitude > 0, ROUND(longitude, 5), NULL) AS lng,
                    IF(latitude > 0 AND longitude > 0,
                       CONCAT(ROUND(latitude, 5), ',', ROUND(longitude, 5)),
                       NULLIF(TRIM(address), '')) AS place,
                    imei_number AS imei, imsi_number AS imsi, address";
        };
        return "(SELECT " . $cols('call_at', 'c') . "
                   FROM person_call_log
                  WHERE person_id = :pid{$phone} AND call_at BETWEEN :from AND :to
                 UNION ALL
                 SELECT " . $cols('sms_at', 's') . "
                   FROM person_sms_log
                  WHERE person_id = :pid{$phone} AND sms_at BETWEEN :from AND :to) e";
    }

    /** Tower address for display: drops the "|lat|lng" some operators append. */
    protected static function clean_address($address)
    {
        $address = trim((string) $address);
        $cut = strpos($address, '|');
        return ($cut === FALSE) ? $address : trim(substr($address, 0, $cut));
    }

    protected static function run($sql, $pid, array $f)
    {
        $q = DB::query(Database::SELECT, $sql)
            ->param(':pid', (int) $pid)
            ->param(':from', $f['from'] . ' 00:00:00')
            ->param(':to', $f['to'] . ' 23:59:59');
        if ($f['phone'] !== '') {
            $q->param(':phone', $f['phone']);
        }
        return $q->execute()->as_array();
    }

    /** Distinct towers with event counts, night / work counts and first / last seen. */
    protected static function places($pid, array $f)
    {
        $sql = "SELECT place, MAX(lat) AS lat, MAX(lng) AS lng, COUNT(*) AS n,
                       SUM(HOUR(t) >= " . self::NIGHT_FROM . " OR HOUR(t) < " . self::NIGHT_TO . ") AS night,
                       SUM(WEEKDAY(t) < 5 AND HOUR(t) >= " . self::WORK_FROM . " AND HOUR(t) < " . self::WORK_TO . ") AS work,
                       COUNT(DISTINCT DATE(t)) AS days, MIN(t) AS first, MAX(t) AS last,
                       MAX(address) AS address
                  FROM " . self::events_sql($f) . "
                 WHERE place IS NOT NULL
              GROUP BY place
              ORDER BY n DESC";
        $out = array();
        foreach (self::run($sql, $pid, $f) as $r) {
            $out[] = array(
                'key' => (string) $r['place'],
                'lat' => ($r['lat'] === NULL) ? NULL : (float) $r['lat'],
                'lng' => ($r['lng'] === NULL) ? NULL : (float) $r['lng'],
                'n' => (int) $r['n'], 'night' => (int) $r['night'], 'work' => (int) $r['work'],
                'days' => (int) $r['days'], 'first' => $r['first'], 'last' => $r['last'],
                'address' => self::clean_address($r['address']),
            );
        }
        return $out;
    }

    /** 7 x 24 grid (Mon = 0) of calls and SMS. */
    protected static function activity_grid($pid, array $f)
    {
        $grid = array();
        for ($d = 0; $d < 7; $d++) {
            $grid[$d] = array_fill(0, 24, 0);
        }
        $sql = "SELECT WEEKDAY(t) AS d, HOUR(t) AS h, COUNT(*) AS n
                  FROM " . self::events_sql($f) . "
              GROUP BY WEEKDAY(t), HOUR(t)";
        foreach (self::run($sql, $pid, $f) as $r) {
            $grid[(int) $r['d']][(int) $r['h']] = (int) $r['n'];
        }
        return $grid;
    }

    /** One row per active day: calls, SMS and distinct towers. */
    protected static function daily($pid, array $f)
    {
        $sql = "SELECT DATE(t) AS day, SUM(kind = 'c') AS calls, SUM(kind = 's') AS sms,
                       COUNT(DISTINCT place) AS places
                  FROM " . self::events_sql($f) . "
              GROUP BY DATE(t)
              ORDER BY day";
        $out = array();
        foreach (self::run($sql, $pid, $f) as $r) {
            $out[] = array('day' => $r['day'], 'calls' => (int) $r['calls'],
                           'sms' => (int) $r['sms'], 'places' => (int) $r['places']);
        }
        return $out;
    }

    /** Towers visited per day in time order (for the playback slider). */
    protected static function track($pid, array $f)
    {
        $sql = "SELECT DATE(t) AS day, place, MAX(lat) AS lat, MAX(lng) AS lng, MAX(address) AS address,
                       DATE_FORMAT(MIN(t), '%H:%i') AS first, DATE_FORMAT(MAX(t), '%H:%i') AS last, COUNT(*) AS n
                  FROM " . self::events_sql($f) . "
                 WHERE place IS NOT NULL
              GROUP BY DATE(t), place
              ORDER BY day, MIN(t)
                 LIMIT " . (self::MAX_TRACK + 1);
        $rows = self::run($sql, $pid, $f);
        $out  = array();
        foreach (array_slice($rows, 0, self::MAX_TRACK) as $r) {
            $out[] = array('day' => $r['day'], 'key' => (string) $r['place'],
                           'lat' => ($r['lat'] === NULL) ? NULL : (float) $r['lat'],
                           'lng' => ($r['lng'] === NULL) ? NULL : (float) $r['lng'],
                           'address' => self::clean_address($r['address']),
                           'first' => $r['first'], 'last' => $r['last'], 'n' => (int) $r['n']);
        }
        return array('stops' => $out, 'truncated' => count($rows) > self::MAX_TRACK);
    }

    /** Handsets (IMEI) and SIMs (IMSI) used, with first / last seen. */
    protected static function devices($pid, array $f)
    {
        $out = array();
        foreach (array('imei', 'imsi') as $col) {
            $sql = "SELECT {$col} AS id, MIN(t) AS first, MAX(t) AS last, COUNT(*) AS n
                      FROM " . self::events_sql($f) . "
                     WHERE {$col} > 0
                  GROUP BY {$col}
                  ORDER BY first";
            $out[$col] = array();
            foreach (self::run($sql, $pid, $f) as $r) {
                $out[$col][] = array('id' => (string) $r['id'], 'first' => $r['first'],
                                     'last' => $r['last'], 'n' => (int) $r['n']);
            }
        }
        return $out;
    }

    /**
     * Distinct handsets / SIMs in all the person's CDRs (any date), so the
     * page can say how many fall outside the chosen range. (The Person
     * Dashboard's device count is device x number pairs from
     * person_phone_device, so it can be higher than these.)
     */
    protected static function devices_all_time($pid, $phone)
    {
        $where = ($phone !== '') ? ' AND phone_number = :phone' : '';
        $out   = array();
        foreach (array('imei' => 'imei_number', 'imsi' => 'imsi_number') as $key => $col) {
            $sql = "SELECT COUNT(DISTINCT id) AS n FROM (
                        SELECT {$col} AS id FROM person_call_log WHERE person_id = :pid AND {$col} > 0{$where}
                        UNION ALL
                        SELECT {$col} FROM person_sms_log WHERE person_id = :pid AND {$col} > 0{$where}
                    ) x";
            $q = DB::query(Database::SELECT, $sql)->param(':pid', (int) $pid);
            if ($phone !== '') {
                $q->param(':phone', $phone);
            }
            $out[$key] = (int) $q->execute()->get('n');
        }
        return $out;
    }

    /** Latest call / SMS date of the person (or one number), or NULL. */
    protected static function latest_record($pid, $phone)
    {
        $where = ($phone !== '') ? ' AND phone_number = :phone' : '';
        $sql = "SELECT MAX(t) AS t FROM (
                    SELECT MAX(call_at) AS t FROM person_call_log WHERE person_id = :pid{$where}
                    UNION ALL
                    SELECT MAX(sms_at) FROM person_sms_log WHERE person_id = :pid{$where}
                ) x";
        $q = DB::query(Database::SELECT, $sql)->param(':pid', (int) $pid);
        if ($phone !== '') {
            $q->param(':phone', $phone);
        }
        $t = $q->execute()->get('t');
        return empty($t) ? NULL : date('Y-m-d', strtotime($t));
    }

    /* ------------------------------------------------------------------ */
    /*  Analysis                                                          */
    /* ------------------------------------------------------------------ */

    /**
     * The place with most $key ('night' or 'work') events, excluding
     * $exclude itself (and, when both have coordinates, any place within
     * 500 m of it). NULL when no place has MIN_ANCHOR_EVENTS.
     */
    protected static function anchor(array $places, $key, $exclude)
    {
        $best = NULL;
        foreach ($places as $p) {
            if ($p[$key] < self::MIN_ANCHOR_EVENTS) {
                continue;
            }
            if ($exclude !== NULL) {
                $near = self::km($p, $exclude);
                if ($p['key'] === $exclude['key'] || ($near !== NULL && $near < 0.5)) {
                    continue;
                }
            }
            if ($best === NULL || $p[$key] > $best[$key]) {
                $best = $p;
            }
        }
        if ($best !== NULL) {
            $total = 0;
            foreach ($places as $p) {
                $total += $p[$key];
            }
            $best['share'] = $total > 0 ? round(100 * $best[$key] / $total) : 0;
        }
        return $best;
    }

    /** The three busiest hours of the day (all weekdays together). */
    protected static function peak_hours(array $grid)
    {
        $hours = array_fill(0, 24, 0);
        foreach ($grid as $row) {
            foreach ($row as $h => $n) {
                $hours[$h] += $n;
            }
        }
        arsort($hours);
        $out = array();
        foreach (array_slice($hours, 0, 3, TRUE) as $h => $n) {
            if ($n > 0) {
                $out[] = array('hour' => (int) $h, 'n' => $n);
            }
        }
        return $out;
    }

    /**
     * Findings worth an investigator's attention, newest first: silent
     * periods, activity spikes, new handsets / SIMs and travel far from home.
     */
    protected static function anomalies(array $f, array $daily, array $places, $home, array $devices)
    {
        $out = array();

        // Silent periods: gaps between active days (and at the range edges).
        $prev = strtotime($f['from'] . ' -1 day');
        $days = array();
        foreach ($daily as $d) {
            $days[] = $d['day'];
        }
        $days[] = date('Y-m-d', strtotime($f['to'] . ' +1 day'));
        foreach ($days as $day) {
            $t   = strtotime($day);
            $gap = (int) round(($t - $prev) / 86400) - 1;
            if ($gap >= self::SILENT_DAYS) {
                $out[] = array('type' => 'silent', 'date' => date('Y-m-d', $prev + 86400),
                               'text' => 'No calls or SMS for ' . $gap . ' days ('
                                       . date('d M', $prev + 86400) . ' – ' . date('d M Y', $t - 86400) . ')');
            }
            $prev = $t;
        }

        // Activity spikes: days far above the person's normal daily volume.
        $counts = array();
        foreach ($daily as $d) {
            $counts[] = $d['calls'] + $d['sms'];
        }
        if (count($counts) >= 7) {
            $mean = array_sum($counts) / count($counts);
            $var  = 0;
            foreach ($counts as $c) {
                $var += ($c - $mean) * ($c - $mean);
            }
            $limit = max(20, $mean + 3 * sqrt($var / count($counts)));
            foreach ($daily as $i => $d) {
                if ($counts[$i] > $limit) {
                    $out[] = array('type' => 'spike', 'date' => $d['day'],
                                   'text' => 'Unusually busy day: ' . $counts[$i] . ' calls/SMS on '
                                           . date('d M Y', strtotime($d['day'])) . ' (normal ≈ ' . round($mean) . ')');
                }
            }
        }

        // New handsets / SIMs: first used after the range began (devices
        // already in use in the first days are the person's existing ones).
        $labels  = array('imei' => 'handset (IMEI)', 'imsi' => 'SIM (IMSI)');
        $settled = date('Y-m-d', strtotime($f['from'] . ' +2 days'));
        foreach ($labels as $col => $label) {
            foreach ($devices[$col] as $dev) {
                if (substr($dev['first'], 0, 10) <= $settled) {
                    continue;
                }
                $out[] = array('type' => 'device', 'date' => substr($dev['first'], 0, 10),
                               'text' => 'New ' . $label . ' ' . $dev['id'] . ' first used on '
                                       . date('d M Y', strtotime($dev['first'])));
            }
        }

        // Travel: towers far from home (only measurable with coordinates).
        if ($home !== NULL && $home['lat'] !== NULL) {
            foreach ($places as $p) {
                $km = self::km($p, $home);
                if ($km !== NULL && $km >= self::TRAVEL_KM) {
                    $out[] = array('type' => 'travel', 'date' => substr($p['first'], 0, 10),
                                   'text' => 'Seen ' . round($km) . ' km from home'
                                           . ($p['address'] !== '' ? ' (' . $p['address'] . ')' : '')
                                           . ' on ' . $p['days'] . ' day(s), first ' . date('d M Y', strtotime($p['first'])),
                                   'lat' => $p['lat'], 'lng' => $p['lng']);
                }
            }
        }

        usort($out, function ($a, $b) { return strcmp($b['date'], $a['date']); });
        return $out;
    }

    /** Great-circle distance in km between two places, or NULL when either has no coordinates. */
    public static function km(array $a, array $b)
    {
        if ($a['lat'] === NULL || $b['lat'] === NULL) {
            return NULL;
        }
        $r    = 6371;
        $dLat = deg2rad($b['lat'] - $a['lat']);
        $dLng = deg2rad($b['lng'] - $a['lng']);
        $h    = sin($dLat / 2) * sin($dLat / 2)
              + cos(deg2rad($a['lat'])) * cos(deg2rad($b['lat'])) * sin($dLng / 2) * sin($dLng / 2);
        return 2 * $r * asin(min(1, sqrt($h)));
    }

    protected static function parse_date($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return NULL;
        }
        $t = strtotime($value);
        return ($t === FALSE) ? NULL : date('Y-m-d', $t);
    }
}
