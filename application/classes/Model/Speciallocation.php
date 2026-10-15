<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Special Location Request - the same Current Location request for one number
 * sent again every N minutes, up to a number of times, until an end time.
 *
 * Every round is an ordinary user_request row (type 4) queued the way
 * Model_Multirequest queues one, so the existing email_send_loc /
 * email_receive / email_parse_loc crons send, match and parse it, and the
 * reply lands in person_location_history (the profile's location history) like
 * any other location reply. The parser also copies the reply onto the round
 * (record_reply()) for the Special Location report.
 *
 * Unlike the normal location request (Userrequest::action_locrequestpermission)
 * a round is sent even when an earlier location request for the same number
 * and Telco is still waiting for its reply. Ufone is not offered: Ufone replies
 * carry the mobile number instead of the reference, and
 * Helpers_Email::emailreadstatuscheckUpdate() hands such a reply to any open
 * request of that number, so overlapping Ufone requests would get each
 * other's replies.
 *
 * The first round is created on submit; the special_location_tick cron
 * (Controller_Cronjob) creates the following ones.
 */
class Model_Speciallocation {

    /* lu_user_access_type.id of the "Special Location Request" and "Special
       Location Report" rights (docs/sql/special_location_request.sql) */
    const ACCESS_TYPE_ID = 16;
    const REPORT_ACCESS_TYPE_ID = 17;

    const STATUS_ACTIVE = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_CANCELLED = 3;
    const STATUS_STOPPED = 4;

    const TYPE_LOCATION = 4;
    const MNC_UFONE = 3;

    /* Active Special Location Requests one user may run at the same time. */
    const MAX_ACTIVE_PER_USER = 10;

    /* Telcos offered (mnc): Mobilink, Warid, Zong, Telenor. Ufone is left out
       (see the class comment) and SCOM needs FIR + cover letter attachments. */
    public static $companies = array(1, 7, 4, 6);

    /* Form choices: minutes between rounds, rounds in total, hours until the end time. */
    public static $intervals = array(10 => '10 Minutes', 30 => '30 Minutes', 60 => '1 Hour', 360 => '6 Hours', 1440 => '24 Hours');
    public static $counts = array(1, 2, 3, 5, 10, 20, 30, 50);
    public static $durations = array(24 => '1 Day', 48 => '2 Days', 72 => '3 Days');

    public static $status_names = array(1 => 'Active', 2 => 'Completed', 3 => 'Cancelled', 4 => 'Stopped');

    public static function has_access($user_id) {
        return Helpers_Profile::get_user_access_permission((int) $user_id, self::ACCESS_TYPE_ID) == 1;
    }

    /* Lists every user's requests (administrators always); without it a user only lists their own. */
    public static function has_report_access($user_id) {
        return Helpers_Utilities::get_user_permission((int) $user_id) == 1
                || Helpers_Profile::get_user_access_permission((int) $user_id, self::REPORT_ACCESS_TYPE_ID) == 1;
    }

    /* Rounds that fit between now and the end time, the first one sent now. */
    public static function rounds_that_fit($interval_minutes, $duration_hours) {
        return (int) floor(($duration_hours * 60) / $interval_minutes) + 1;
    }

    /**
     * Profile the locations belong to. With $person_id (the profile the form was
     * opened from) the number must be linked to that profile; otherwise the
     * SIM user (or owner) Model_Multirequest::find_profile() finds.
     * Returns 0 when the number has no profile.
     */
    public static function profile_for_number($number, $person_id = 0) {
        $person_id = (int) $person_id;
        if ($person_id > 0) {
            $row = DB::query(Database::SELECT, 'SELECT id FROM person_phone_number WHERE phone_number = :number AND (person_id = :person_id OR sim_owner = :person_id) LIMIT 1')
                    ->param(':number', $number)
                    ->param(':person_id', $person_id)
                    ->execute()
                    ->current();
            if (!empty($row)) {
                return $person_id;
            }
        }
        $profile = Model_Multirequest::find_profile($number);
        // Location data belongs to the SIM user, as in Model_Multirequest.
        return $profile['user_id'] ? (int) $profile['user_id'] : (int) $profile['owner_id'];
    }

    public static function active_count($user_id) {
        $row = DB::query(Database::SELECT, 'SELECT COUNT(*) AS cnt FROM special_location_request WHERE user_id = :user_id AND status = ' . self::STATUS_ACTIVE)
                ->param(':user_id', (int) $user_id)
                ->execute()
                ->current();
        return (int) $row['cnt'];
    }

    /**
     * Creates the request and sends its first round at once.
     * Returns array('id', 'request_id', 'note') or array('error' => message).
     */
    public static function create($user_id, $person_id, $number, $company, $project_id, $reason, $interval_minutes, $max_requests, $duration_hours) {
        $lock = self::lock();
        try {
            if (self::active_count($user_id) >= self::MAX_ACTIVE_PER_USER) {
                return array('error' => 'You already have ' . self::MAX_ACTIVE_PER_USER . ' active Special Location Requests. Cancel one or wait for one to finish.');
            }
            $now = date('Y-m-d H:i:s');
            $result = DB::insert('special_location_request', array('user_id', 'person_id', 'msisdn', 'company_name', 'project_id', 'reason', 'interval_minutes', 'max_requests', 'duration_hours', 'end_at', 'next_run_at', 'status'))
                    ->values(array($user_id, $person_id, $number, $company, $project_id, $reason, $interval_minutes, $max_requests, $duration_hours,
                        date('Y-m-d H:i:s', strtotime($now) + $duration_hours * 3600), $now, self::STATUS_ACTIVE))
                    ->execute();
            $special = self::get($result[0]);
            $round = self::run_round($special);
            return array('id' => (int) $special['id'], 'request_id' => $round['request_id'], 'note' => $round['note']);
        } finally {
            self::unlock($lock);
        }
    }

    /**
     * Cron: creates the round of every active request that is due.
     * Returns the number of rounds created.
     */
    public static function run_due() {
        $lock = self::lock();
        $created = 0;
        try {
            $due = DB::query(Database::SELECT, 'SELECT * FROM special_location_request
                    WHERE status = ' . self::STATUS_ACTIVE . ' AND next_run_at <= NOW()
                    ORDER BY next_run_at ASC
                    LIMIT 200')
                    ->execute()
                    ->as_array();
            foreach ($due as $special) {
                // One failing request must not stop the others.
                try {
                    $round = self::run_round($special);
                    if ($round['request_id']) {
                        $created++;
                    }
                } catch (Exception $e) {
                    Model_ErrorLog::log('speciallocation_tick', $e->getMessage(), array('special_id' => $special['id'], 'mobile_requested' => $special['msisdn']), $e->getTraceAsString(), 'processing_failure', 'special_location');
                }
            }
        } finally {
            self::unlock($lock);
        }
        return $created;
    }

    /**
     * Sends one round of $special if it may still run, then moves next_run_at
     * on (or finishes the request). Returns array('request_id', 'note');
     * request_id is 0 when no round was created.
     */
    private static function run_round(array $special) {
        $id = (int) $special['id'];
        $now = time();

        if ((int) $special['sent_count'] >= (int) $special['max_requests']) {
            self::finish($id, self::STATUS_COMPLETED, 'All ' . (int) $special['max_requests'] . ' requests sent');
            return array('request_id' => 0, 'note' => 'All requests sent');
        }
        // A round due right at the end time is still sent when the cron runs a little late.
        if ($now > strtotime($special['end_at']) + 300) {
            self::finish($id, self::STATUS_COMPLETED, 'End time reached after ' . (int) $special['sent_count'] . ' requests');
            return array('request_id' => 0, 'note' => 'End time reached');
        }
        if (!self::has_access($special['user_id'])) {
            self::finish($id, self::STATUS_STOPPED, 'Requester no longer has the Special Location Request right');
            return array('request_id' => 0, 'note' => 'Right removed');
        }
        if (Helpers_Email::check_in_blocked_number_list($special['msisdn'])) {
            self::finish($id, self::STATUS_STOPPED, 'Number is in the blocked number list');
            return array('request_id' => 0, 'note' => 'Number blocked');
        }

        $interval = (int) $special['interval_minutes'] * 60;
        // Keep the rhythm from the due time, but never catch up with a burst
        // when the cron was late.
        $next = strtotime($special['next_run_at']) + $interval;
        if ($next <= $now) {
            $next = $now + $interval;
        }

        // An open Ufone Subscriber/Location request of this number (sent by
        // anyone) would take this round's reply or give it its own: Ufone
        // replies are matched by mobile number only. Wait for the next round.
        $ufone = self::open_ufone_request($special['msisdn']);
        if ($ufone) {
            $note = 'Round postponed: Ufone request #' . $ufone . ' for this number is still open (Ufone replies are matched by mobile number)';
            self::reschedule($id, $next, (int) $special['sent_count'], (int) $special['max_requests'], $special['end_at'], $note);
            return array('request_id' => 0, 'note' => $note);
        }

        $round_no = (int) $special['sent_count'] + 1;
        $reference_id = Helpers_Utilities::id_generator("reference_id");
        $DB = Database::instance();
        $DB->begin();
        try {
            $request_id = Model_Multirequest::insert_request($special['user_id'], self::TYPE_LOCATION, (int) $special['company_name'], $special['msisdn'], (int) $special['person_id'],
                    $special['project_id'], $special['reason'], NULL, NULL, $reference_id);
            DB::insert('special_location_round', array('special_id', 'round_no', 'request_id'))
                    ->values(array($id, $round_no, $request_id))
                    ->execute();
            self::reschedule($id, $next, $round_no, (int) $special['max_requests'], $special['end_at'], NULL);
            $DB->commit();
        } catch (Exception $e) {
            $DB->rollback();
            Model_ErrorLog::log('speciallocation_round', $e->getMessage(), array('special_id' => $id, 'mobile_requested' => $special['msisdn'], 'round_no' => $round_no), $e->getTraceAsString(), 'insert_failure', 'special_location');
            // Try again at the next interval rather than every minute.
            self::reschedule($id, $next, (int) $special['sent_count'], (int) $special['max_requests'], $special['end_at'], 'System error while creating round ' . $round_no);
            return array('request_id' => 0, 'note' => 'System error while creating the request');
        }
        Helpers_Profile::user_activity_log($special['user_id'], 10, self::TYPE_LOCATION, $special['msisdn'], (int) $special['person_id'], (int) $special['company_name']);
        return array('request_id' => (int) $request_id, 'note' => '');
    }

    /* Moves the request to its next round, or completes it when none is left. */
    private static function reschedule($id, $next, $sent_count, $max_requests, $end_at, $note) {
        $values = array('sent_count' => $sent_count, 'note' => $note);
        if ($sent_count >= $max_requests) {
            $values = array_merge($values, array('status' => self::STATUS_COMPLETED, 'next_run_at' => NULL, 'note' => 'All ' . $max_requests . ' requests sent'));
        } elseif ($next > strtotime($end_at)) {
            $values = array_merge($values, array('status' => self::STATUS_COMPLETED, 'next_run_at' => NULL, 'note' => 'End time reached after ' . $sent_count . ' requests'));
        } else {
            $values['next_run_at'] = date('Y-m-d H:i:s', $next);
        }
        DB::update('special_location_request')->set($values)->where('id', '=', $id)->execute();
    }

    private static function finish($id, $status, $note) {
        DB::update('special_location_request')
                ->set(array('status' => $status, 'next_run_at' => NULL, 'note' => $note))
                ->where('id', '=', $id)
                ->where('status', '=', self::STATUS_ACTIVE)
                ->execute();
    }

    /* Open Ufone Subscriber or Location request of the number, as Model_Multirequest::conflict() checks it. */
    private static function open_ufone_request($number) {
        $row = DB::query(Database::SELECT, 'SELECT request_id FROM user_request
                WHERE requested_value = :number AND user_request_type_id IN (3, 4) AND company_name = ' . self::MNC_UFONE . '
                  AND (status IN (0, 1) OR processing_index = 4 OR (status = 3 AND processing_index = 1))
                LIMIT 1')
                ->param(':number', $number)
                ->execute()
                ->current();
        return empty($row) ? 0 : (int) $row['request_id'];
    }

    /* Owner, or a user with the report right, may cancel. Returns '' or why not. */
    public static function cancel($id, $user_id) {
        // Under the lock, so the cron cannot be halfway through a round of it.
        $lock = self::lock();
        try {
            $special = self::get($id);
            if (empty($special)) {
                return 'Request not found.';
            }
            if ($special['user_id'] != $user_id && !self::has_report_access($user_id)) {
                return 'You can only cancel your own requests.';
            }
            if ((int) $special['status'] !== self::STATUS_ACTIVE) {
                return 'This request is no longer active.';
            }
            // Rounds already queued are still sent; only the next ones are not created.
            DB::update('special_location_request')
                    ->set(array('status' => self::STATUS_CANCELLED, 'next_run_at' => NULL, 'cancelled_by' => $user_id, 'cancelled_at' => date('Y-m-d H:i:s'), 'note' => 'Cancelled after ' . (int) $special['sent_count'] . ' requests'))
                    ->where('id', '=', (int) $id)
                    ->where('status', '=', self::STATUS_ACTIVE)
                    ->execute();
            return '';
        } finally {
            self::unlock($lock);
        }
    }

    public static function get($id) {
        return DB::select()->from('special_location_request')->where('id', '=', (int) $id)->execute()->current();
    }

    /* Whether $user_id may see $special (own request, or the report right). */
    public static function can_view(array $special, $user_id) {
        return $special['user_id'] == $user_id || self::has_report_access($user_id);
    }

    /* Rounds of one request with their request status and the parsed location. */
    public static function rounds($special_id) {
        return DB::query(Database::SELECT, 'SELECT r.*, ur.reference_id, ur.status, ur.processing_index, ur.sending_date, ur.request_send_count, em.received_date
                FROM special_location_round AS r
                LEFT JOIN user_request AS ur ON ur.request_id = r.request_id
                LEFT JOIN email_messages AS em ON em.message_id = ur.message_id
                WHERE r.special_id = :special_id
                ORDER BY r.round_no DESC')
                ->param(':special_id', (int) $special_id)
                ->execute()
                ->as_array();
    }

    /**
     * Location parser hook (Model_Generic::ManualLocationinsert()): copies the
     * parsed reply onto its round. Requests that are not Special Location
     * rounds match no row, so the update does nothing for them.
     */
    public static function record_reply($data, $location_at) {
        if (empty($data['requestid'])) {
            return;
        }
        try {
            DB::update('special_location_round')
                    ->set(array(
                        'location_at' => $location_at,
                        'network' => isset($data['locnetwork']) ? (int) $data['locnetwork'] : NULL,
                        'lac_id' => isset($data['loclac']) ? mb_substr((string) $data['loclac'], 0, 20) : NULL,
                        'cell_id' => isset($data['loccellid']) ? mb_substr((string) $data['loccellid'], 0, 20) : NULL,
                        'sector' => isset($data['locsector']) ? mb_substr((string) $data['locsector'], 0, 10) : NULL,
                        'latitude' => (isset($data['loclat']) && is_numeric($data['loclat'])) ? $data['loclat'] : NULL,
                        'longitude' => (isset($data['loclong']) && is_numeric($data['loclong'])) ? $data['loclong'] : NULL,
                        'address' => isset($data['locaddress']) ? mb_substr((string) $data['locaddress'], 0, 500) : NULL,
                        'imei' => isset($data['locimei']) ? mb_substr((string) $data['locimei'], 0, 25) : NULL,
                        'location_status' => isset($data['locstatus']) ? (int) $data['locstatus'] : NULL,
                        'parsed_at' => date('Y-m-d H:i:s'),
                    ))
                    ->where('request_id', '=', (int) $data['requestid'])
                    ->execute();
        } catch (Exception $e) {
            // Every location reply passes through here; never fail its parse.
            Model_ErrorLog::log('speciallocation_parse', $e->getMessage(), array('request_id' => (int) $data['requestid']), $e->getTraceAsString(), 'update_failure', 'location_parsing');
        }
    }

    /**
     * Rows for the report (DataTables server side parameters plus the report
     * filters), or the row count when $count is TRUE. $user_id limits the rows
     * to that user's requests (users without the report right).
     */
    public static function report($post, $user_id = 0, $count = FALSE) {
        $where = array('1');
        $params = array();
        if ($user_id) {
            $where[] = 's.user_id = ' . (int) $user_id;
        }
        if (!empty($post['status'])) {
            $where[] = 's.status = ' . (int) $post['status'];
        }
        if (!empty($post['company_name'])) {
            $where[] = 's.company_name = ' . (int) $post['company_name'];
        }
        if (!empty($post['from_date']) && strtotime($post['from_date'])) {
            $where[] = 's.created_at >= :from_date';
            $params[':from_date'] = date('Y-m-d 00:00:00', strtotime($post['from_date']));
        }
        if (!empty($post['to_date']) && strtotime($post['to_date'])) {
            $where[] = 's.created_at <= :to_date';
            $params[':to_date'] = date('Y-m-d 23:59:59', strtotime($post['to_date']));
        }
        if (isset($post['sSearch']) && trim($post['sSearch']) !== '') {
            $search = trim($post['sSearch']);
            $where[] = '(s.msisdn LIKE :search OR s.id = :search_id OR s.person_id = :search_id)';
            $params[':search'] = '%' . $search . '%';
            $params[':search_id'] = ctype_digit($search) ? $search : '-1';
        }
        $from = 'FROM special_location_request AS s
                LEFT JOIN person AS p ON p.person_id = s.person_id
                WHERE ' . implode(' AND ', $where);

        if ($count) {
            $query = DB::query(Database::SELECT, 'SELECT COUNT(*) AS cnt ' . $from);
            foreach ($params as $key => $value) {
                $query->param($key, $value);
            }
            $row = $query->execute()->current();
            return (int) $row['cnt'];
        }

        $sortable = array(0 => 's.id', 1 => 's.msisdn', 5 => 's.created_at');
        $sort_column = (isset($post['iSortCol_0']) && isset($sortable[(int) $post['iSortCol_0']])) ? $sortable[(int) $post['iSortCol_0']] : 's.id';
        $sort_dir = (isset($post['sSortDir_0']) && strtolower($post['sSortDir_0']) === 'asc') ? 'ASC' : 'DESC';
        $offset = isset($post['iDisplayStart']) ? max(0, (int) $post['iDisplayStart']) : 0;
        $limit = isset($post['iDisplayLength']) ? (int) $post['iDisplayLength'] : 25;
        $limit = ($limit > 0 && $limit <= 500) ? $limit : 25;

        $sql = 'SELECT s.*, p.first_name, p.last_name,
                       (SELECT COUNT(*) FROM special_location_round AS r WHERE r.special_id = s.id AND r.parsed_at IS NOT NULL) AS replies
                ' . $from . '
                ORDER BY ' . $sort_column . ' ' . $sort_dir . '
                LIMIT ' . $offset . ', ' . $limit;
        $query = DB::query(Database::SELECT, $sql);
        foreach ($params as $key => $value) {
            $query->param($key, $value);
        }
        return $query->execute()->as_array();
    }

    /**
     * Fills rounds whose reply was parsed (processing_index 5) but never copied
     * onto the round, e.g. parsed by code without the record_reply() hook. Each
     * such round gets the profile's first location history row of this number
     * uploaded after the round was created and not already taken by an earlier
     * round. Returns the number of rounds filled.
     */
    public static function backfill_rounds(array $special) {
        $missing = DB::query(Database::SELECT, 'SELECT r.id, r.created_at
                FROM special_location_round AS r
                JOIN user_request AS ur ON ur.request_id = r.request_id
                WHERE r.special_id = :special_id AND r.parsed_at IS NULL AND ur.status = 2 AND ur.processing_index = 5
                ORDER BY r.round_no ASC')
                ->param(':special_id', (int) $special['id'])
                ->execute()
                ->as_array();
        if (empty($missing)) {
            return 0;
        }
        // History rows already copied onto a round, so two rounds never share one.
        $taken = array();
        foreach (DB::query(Database::SELECT, 'SELECT location_at, lac_id, cell_id FROM special_location_round WHERE special_id = :special_id AND parsed_at IS NOT NULL')
                ->param(':special_id', (int) $special['id'])->execute()->as_array() as $row) {
            $taken[$row['location_at'] . '|' . $row['lac_id'] . '|' . $row['cell_id']] = TRUE;
        }
        // idx_person_id keeps this cheap on the large history table.
        $history = DB::query(Database::SELECT, 'SELECT network, lac_id, cell_id, sector, latitude, longitude, address, moved_in_at, status, upload_at
                FROM person_location_history
                WHERE person_id = :person_id AND phone_number = :number AND upload_at >= :since
                ORDER BY upload_at ASC
                LIMIT 500')
                ->param(':person_id', (int) $special['person_id'])
                ->param(':number', $special['msisdn'])
                ->param(':since', $missing[0]['created_at'])
                ->execute()
                ->as_array();
        $filled = 0;
        foreach ($missing as $round) {
            foreach ($history as $i => $row) {
                $key = $row['moved_in_at'] . '|' . $row['lac_id'] . '|' . $row['cell_id'];
                if (strtotime($row['upload_at']) < strtotime($round['created_at']) || isset($taken[$key])) {
                    continue;
                }
                DB::update('special_location_round')
                        ->set(array(
                            'location_at' => $row['moved_in_at'],
                            'network' => $row['network'],
                            'lac_id' => $row['lac_id'],
                            'cell_id' => $row['cell_id'],
                            'sector' => $row['sector'],
                            'latitude' => $row['latitude'],
                            'longitude' => $row['longitude'],
                            'address' => $row['address'],
                            'location_status' => $row['status'],
                            'parsed_at' => $row['upload_at'],
                        ))
                        ->where('id', '=', (int) $round['id'])
                        ->where('parsed_at', 'IS', NULL)
                        ->execute();
                $taken[$key] = TRUE;
                unset($history[$i]);
                $filled++;
                break;
            }
        }
        return $filled;
    }

    public static function reply_count($special_id) {
        $row = DB::query(Database::SELECT, 'SELECT COUNT(*) AS cnt FROM special_location_round WHERE special_id = :special_id AND parsed_at IS NOT NULL')
                ->param(':special_id', (int) $special_id)
                ->execute()
                ->current();
        return (int) $row['cnt'];
    }

    /* Latest parsed round of each request: array(special_id => round row). */
    public static function last_locations(array $special_ids) {
        $special_ids = array_filter(array_map('intval', $special_ids));
        if (empty($special_ids)) {
            return array();
        }
        $sql = 'SELECT r.* FROM special_location_round AS r
                WHERE r.special_id IN (' . implode(',', $special_ids) . ') AND r.parsed_at IS NOT NULL
                ORDER BY r.round_no DESC';
        $last = array();
        foreach (DB::query(Database::SELECT, $sql)->execute()->as_array() as $row) {
            if (!isset($last[$row['special_id']])) {
                $last[$row['special_id']] = $row;
            }
        }
        return $last;
    }

    /* Serializes request creation (submit and cron) so a round is never created twice. */
    private static function lock() {
        $lock = fopen(DOCROOT . 'application/logs/special_location_request.lock', 'c');
        flock($lock, LOCK_EX);
        return $lock;
    }

    private static function unlock($lock) {
        flock($lock, LOCK_UN);
        fclose($lock);
    }

}
