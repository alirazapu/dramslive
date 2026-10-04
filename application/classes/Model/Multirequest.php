<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Multi Number Request - a list of mobile numbers sent as one Telco request
 * per number and parsed like any other user request.
 *
 * A submission (user_request_batch) becomes ordinary user_request rows, one
 * per number, queued the way Controller_Email::action_send() queues a single
 * request (and the way the migration's subscriber.php queued its requests).
 * The existing email_send / email_receive / email_parse_* crons then send,
 * match and parse every row with no special casing.
 *
 * The CDR and Location parsers need a profile, so a number without one gets a
 * profile without Name/CNIC (as migratecdr152.php created them) plus an
 * automatic Subscriber request. fill_in_person_id() lets that Subscriber reply
 * complete the same profile instead of creating a second one.
 */
class Model_Multirequest {

    /* lu_user_access_type.id of the "Multi Number Request" and "Multi Number
       Request Report" rights (docs/sql/multi_number_request.sql) */
    const ACCESS_TYPE_ID = 13;
    const REPORT_ACCESS_TYPE_ID = 14;

    /* Numbers per submission. Each number adds one or two emails to the shared
       normal priority queue (send_other/low.inc sends 8 per run and 1450 per
       day across all Telcos), so one batch cannot hold that queue for long. */
    const MAX_NUMBERS = 50;

    const TYPE_CDR = 1;
    const TYPE_SUBSCRIBER = 3;
    const TYPE_LOCATION = 4;
    const TYPE_CDR_SMS = 6;

    const MNC_UFONE = 3;

    /* Mobile number request types that have a parser, and the Telcos (mnc)
       whose replies that parser reads. SCOM (8) is left out: its Subscriber and
       CDR replies are never parsed and its requests need FIR + cover letter
       attachments. CDR with SMS templates exist for Ufone and Zong only. */
    public static $request_types = array(
        3 => array('label' => 'Subscriber (Name / CNIC)', 'companies' => array(1, 7, 3, 4, 6)),
        1 => array('label' => 'CDR Against Mobile Number', 'companies' => array(1, 7, 3, 4, 6)),
        6 => array('label' => 'CDR Against Mobile Number with SMS Detail', 'companies' => array(3, 4)),
        4 => array('label' => 'Current Location', 'companies' => array(1, 7, 3, 4, 6)),
    );

    public static function has_access($user_id) {
        return Helpers_Profile::get_user_access_permission((int) $user_id, self::ACCESS_TYPE_ID) == 1;
    }

    /* The batch report has its own right, whatever the user's role. */
    public static function has_report_access($user_id) {
        return Helpers_Profile::get_user_access_permission((int) $user_id, self::REPORT_ACCESS_TYPE_ID) == 1;
    }

    public static function needs_dates($request_type) {
        return in_array((int) $request_type, array(self::TYPE_CDR, self::TYPE_CDR_SMS));
    }

    /**
     * Accepts the number variants migratecdr152.php matched (3XXXXXXXXX,
     * 03XXXXXXXXX, 923XXXXXXXXX, +923XXXXXXXXX, 0923XXXXXXXXX, 00923XXXXXXXXX)
     * and returns the 10 digit form stored in user_request.requested_value,
     * or '' when the value is not a valid mobile number.
     */
    public static function normalize_number($value) {
        $digits = preg_replace('/\D/', '', (string) $value);
        if (strlen($digits) == 14 && substr($digits, 0, 4) === '0092') {
            $digits = substr($digits, 4);
        } elseif (strlen($digits) == 13 && substr($digits, 0, 3) === '092') {
            $digits = substr($digits, 3);
        } elseif (strlen($digits) == 12 && substr($digits, 0, 2) === '92') {
            $digits = substr($digits, 2);
        } elseif (strlen($digits) == 11 && substr($digits, 0, 1) === '0') {
            $digits = substr($digits, 1);
        }
        return preg_match('/^3\d{9}$/', $digits) ? $digits : '';
    }

    /**
     * Splits the pasted list on new lines, commas, semicolons, tabs and spaces.
     * Returns array('valid' => normalized unique numbers, 'invalid' => raw
     * values that are not mobile numbers, 'duplicates' => repeated values).
     */
    public static function parse_numbers($raw) {
        $result = array('valid' => array(), 'invalid' => array(), 'duplicates' => array());
        $values = preg_split('/[\s,;]+/', str_replace('-', '', (string) $raw), -1, PREG_SPLIT_NO_EMPTY);
        foreach ($values as $value) {
            $number = self::normalize_number($value);
            if ($number === '') {
                $result['invalid'][] = $value;
            } elseif (in_array($number, $result['valid'], TRUE)) {
                $result['duplicates'][] = $value;
            } else {
                $result['valid'][] = $number;
            }
        }
        return $result;
    }

    /**
     * Creates the batch: one user_request (and one email) per number, plus an
     * automatic Subscriber request for every number whose profile is missing
     * or has no Name/CNIC. Returns array('batch_id', 'items') or
     * array('error' => message) when no number could be requested.
     */
    public static function create_batch($user_id, $request_type, $company, $project_id, $reason, $start_date, $end_date, array $numbers) {
        // One submission at a time, so a double click or a second tab cannot
        // pass the open request check before the first submission inserts.
        $lock = fopen(DOCROOT . 'application/logs/user_request_batch.lock', 'c');
        flock($lock, LOCK_EX);
        try {
            $plan = self::plan($request_type, $company, $start_date, $end_date, $numbers);
            $to_create = array_filter($plan, function ($item) {
                return $item['skip'] === '';
            });
            if (empty($to_create)) {
                $skipped = array();
                foreach ($plan as $item) {
                    $skipped[] = array('number' => $item['number'], 'reason' => $item['skip']);
                }
                return array('error' => 'No request was created: every number was skipped.', 'skipped' => $skipped);
            }

            $batch = DB::insert('user_request_batch', array('user_id', 'user_request_type_id', 'company_name'))
                    ->values(array($user_id, $request_type, $company))
                    ->execute();
            $batch_id = $batch[0];

            $items = array();
            foreach ($plan as $item) {
                if ($item['skip'] === '') {
                    $item = self::create_number_requests($batch_id, $item, $user_id, $request_type, $company, $project_id, $reason, $start_date, $end_date);
                }
                if ($item['skip'] !== '') {
                    // Failing to record one skipped number must not stop the numbers after it.
                    try {
                        self::insert_item($batch_id, $item['number'], NULL, 0, 0, $item['skip']);
                    } catch (Exception $e) {
                        Model_ErrorLog::log(
                            'multirequest_create',
                            $e->getMessage(),
                            array('mobile_requested' => $item['number'], 'batch_id' => $batch_id, 'skip' => $item['skip']),
                            $e->getTraceAsString(),
                            'insert_failure',
                            'batch_create'
                        );
                    }
                }
                $items[] = $item;
            }
            return array('batch_id' => $batch_id, 'items' => $items);
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    /* Decides, without writing anything, what each number needs. */
    private static function plan($request_type, $company, $start_date, $end_date, array $numbers) {
        $open = self::get_open_requests($numbers, array_unique(array($request_type, self::TYPE_SUBSCRIBER, self::TYPE_LOCATION)));
        $plan = array();
        foreach ($numbers as $number) {
            $item = array(
                'number' => $number,
                'skip' => '',
                'profile' => NULL,
                'create_profile' => FALSE,
                'auto_subscriber' => FALSE,
                'note' => '',
                'request_id' => 0,
                'reference_id' => 0,
                'subscriber_request_id' => 0,
                'subscriber_reference_id' => 0,
                'profile_id' => 0,
                'profile_created' => FALSE,
            );
            $item['skip'] = self::conflict($number, $request_type, $company, $open);
            // Same "prohibited duration" rule as Userrequest::action_cdrrequestpermission().
            if ($item['skip'] === '' && self::needs_dates($request_type) && Helpers_Utilities::get_cdr_duration_with_msisdn($number, $start_date, $end_date)) {
                $item['skip'] = 'CDR already exists for this number in the selected period';
            }
            if ($item['skip'] === '') {
                $profile = self::find_profile($number);
                if ($profile['owner_id'] == 0 && $profile['user_id'] == 0 && $profile['missing_id'] > 0) {
                    $item['skip'] = 'Number is linked to profile #' . $profile['missing_id'] . ', which does not exist';
                }
                $item['profile'] = $profile;
            }
            if ($item['skip'] === '' && $request_type != self::TYPE_SUBSCRIBER) {
                $profile = $item['profile'];
                // The CDR and Location parsers attach data to an existing profile.
                $item['create_profile'] = ($profile['owner_id'] == 0 && $profile['user_id'] == 0);
                $subscriber_person_id = $profile['owner_id'] ? $profile['owner_id'] : $profile['user_id'];
                if ($item['create_profile'] || self::profile_needs_subscriber($subscriber_person_id)) {
                    $item['note'] = self::conflict($number, self::TYPE_SUBSCRIBER, $company, $open);
                    if ($item['note'] === '' && $request_type == self::TYPE_LOCATION && $company == self::MNC_UFONE) {
                        $item['note'] = 'Subscriber request not queued: Ufone replies to Location and Subscriber requests are matched by mobile number, so request it after the Location reply arrives';
                    } elseif ($item['note'] !== '') {
                        $item['note'] = 'Subscriber request not queued: ' . $item['note'];
                    }
                    $item['auto_subscriber'] = ($item['note'] === '');
                }
            }
            $plan[] = $item;
        }
        return $plan;
    }

    /* Inserts the profile (if needed) and the request(s) of one number in one transaction. */
    private static function create_number_requests($batch_id, array $item, $user_id, $request_type, $company, $project_id, $reason, $start_date, $end_date) {
        $number = $item['number'];
        $profile = $item['profile'];
        // Ids come from the shared id_generator before the transaction starts,
        // so its row is never locked while this number's rows are written.
        $new_person_id = $item['create_profile'] ? Helpers_Utilities::id_generator("person_id") : 0;
        $item['reference_id'] = Helpers_Utilities::id_generator("reference_id");
        if ($item['auto_subscriber']) {
            $item['subscriber_reference_id'] = Helpers_Utilities::id_generator("reference_id");
        }
        if ($new_person_id) {
            $profile['owner_id'] = $profile['user_id'] = $new_person_id;
        }
        $owner_first = $profile['owner_id'] ? $profile['owner_id'] : $profile['user_id'];
        $user_first = $profile['user_id'] ? $profile['user_id'] : $profile['owner_id'];
        // Subscriber replies describe the SIM owner; CDR and Location data belong to its user.
        $concerned_person_id = ($request_type == self::TYPE_SUBSCRIBER) ? $owner_first : $user_first;

        $DB = Database::instance();
        $DB->begin();
        try {
            if ($new_person_id) {
                self::insert_profile($new_person_id, $number, $company, $project_id, $user_id, $profile['unowned_row_id']);
            }
            $item['request_id'] = self::insert_request($user_id, $request_type, $company, $number, $concerned_person_id, $project_id, $reason, $start_date, $end_date, $item['reference_id']);
            self::insert_item($batch_id, $number, $item['request_id'], 0, $new_person_id ? 1 : 0, $item['note'] !== '' ? $item['note'] : NULL);
            if ($item['auto_subscriber']) {
                $item['subscriber_request_id'] = self::insert_request($user_id, self::TYPE_SUBSCRIBER, $company, $number, $owner_first, $project_id, $reason, NULL, NULL, $item['subscriber_reference_id']);
                self::insert_item($batch_id, $number, $item['subscriber_request_id'], 1, 0, NULL);
            }
            $DB->commit();
        } catch (Exception $e) {
            $DB->rollback();
            Model_ErrorLog::log(
                'multirequest_create',
                $e->getMessage(),
                array('mobile_requested' => $number, 'batch_id' => $batch_id, 'request_type' => $request_type, 'company_name' => $company),
                $e->getTraceAsString(),
                'insert_failure',
                'batch_create'
            );
            $item['skip'] = 'System error while creating the request';
            $item['request_id'] = $item['subscriber_request_id'] = 0;
            return $item;
        }

        $item['profile_id'] = $concerned_person_id;
        $item['profile_created'] = (bool) $new_person_id;
        if ($new_person_id) {
            Helpers_Profile::user_activity_log($user_id, 76, NULL, NULL, $new_person_id);
        }
        Helpers_Profile::user_activity_log($user_id, 10, $request_type, $number, $concerned_person_id, $company);
        if ($item['auto_subscriber']) {
            Helpers_Profile::user_activity_log($user_id, 10, self::TYPE_SUBSCRIBER, $number, $owner_first, $company);
        }
        return $item;
    }

    /**
     * Requests still in flight for the numbers, grouped by number. Open means
     * queued (0), sent and waiting for the reply (1), a send error that
     * Model_Generic::resend_error_in_queue() will retry (3/1), or received and
     * waiting for the parser (processing_index 4) - the states
     * Helpers_Email::check_request_in_queue_status() blocks, plus the retry.
     */
    private static function get_open_requests(array $numbers, array $request_types) {
        if (empty($numbers)) {
            return array();
        }
        // $numbers are normalized digits only (normalize_number()).
        $sql = "SELECT request_id, requested_value, user_request_type_id, company_name
                FROM user_request
                WHERE requested_value IN ('" . implode("','", $numbers) . "')
                  AND user_request_type_id IN (" . implode(',', array_map('intval', $request_types)) . ")
                  AND (status IN (0, 1) OR processing_index = 4 OR (status = 3 AND processing_index = 1))";
        $open = array();
        foreach (DB::query(Database::SELECT, $sql)->execute()->as_array() as $row) {
            $open[$row['requested_value']][] = $row;
        }
        return $open;
    }

    /* Why a new request of $request_type for $number must not be created, or ''. */
    private static function conflict($number, $request_type, $company, array $open) {
        $rows = isset($open[$number]) ? $open[$number] : array();
        foreach ($rows as $row) {
            if ($row['user_request_type_id'] == $request_type) {
                return 'Open ' . Helpers_Utilities::get_request_type_name($request_type) . ' request #' . $row['request_id'];
            }
        }
        // Ufone Subscriber and Location emails carry "92<number>" instead of the
        // reference, and Helpers_Email::emailreadstatuscheckUpdate() hands such a
        // reply to any open type 3 or 4 request of that number, whatever the Telco.
        if (in_array($request_type, array(self::TYPE_SUBSCRIBER, self::TYPE_LOCATION))) {
            foreach ($rows as $row) {
                if (in_array($row['user_request_type_id'], array(self::TYPE_SUBSCRIBER, self::TYPE_LOCATION))
                        && ($company == self::MNC_UFONE || $row['company_name'] == self::MNC_UFONE)) {
                    return 'Open ' . Helpers_Utilities::get_request_type_name($row['user_request_type_id']) . ' request #' . $row['request_id'] . ' (Ufone replies are matched by mobile number)';
                }
            }
        }
        return '';
    }

    /**
     * Profile of the number from person_phone_number (latest row that points at
     * an existing profile, as migratecdr152.php looked numbers up): owner_id is
     * the SIM owner, user_id the SIM user. unowned_row_id is set when the only
     * rows have no owner and no user; missing_id when they point at a profile
     * that does not exist.
     */
    private static function find_profile($number) {
        $profile = array('owner_id' => 0, 'user_id' => 0, 'unowned_row_id' => 0, 'missing_id' => 0);
        $rows = DB::query(Database::SELECT, 'SELECT id, sim_owner, person_id FROM person_phone_number WHERE phone_number = :number ORDER BY id DESC')
                ->param(':number', $number)
                ->execute()
                ->as_array();
        foreach ($rows as $row) {
            $owner_id = self::existing_person_id($row['sim_owner']);
            $user_id = self::existing_person_id($row['person_id']);
            if ($owner_id || $user_id) {
                $profile['owner_id'] = $owner_id;
                $profile['user_id'] = $user_id;
                return $profile;
            }
            if ((int) $row['sim_owner'] == 0 && (int) $row['person_id'] == 0) {
                if (!$profile['unowned_row_id']) {
                    $profile['unowned_row_id'] = (int) $row['id'];
                }
            } elseif (!$profile['missing_id']) {
                $profile['missing_id'] = (int) ($row['sim_owner'] ? $row['sim_owner'] : $row['person_id']);
            }
        }
        return $profile;
    }

    private static function existing_person_id($person_id) {
        $person_id = (int) $person_id;
        if ($person_id <= 0) {
            return 0;
        }
        $row = DB::select('person_id')->from('person')->where('person_id', '=', $person_id)->limit(1)->execute()->current();
        return empty($row) ? 0 : $person_id;
    }

    /* Same condition subscriber.php used to pick migration profiles: no name or no CNIC. */
    private static function profile_needs_subscriber($person_id) {
        $row = DB::query(Database::SELECT, 'SELECT p.first_name, pi.cnic_number, pi.cnic_number_foreigner
                FROM person AS p
                LEFT JOIN person_initiate AS pi ON pi.person_id = p.person_id
                WHERE p.person_id = :person_id
                LIMIT 1')
                ->param(':person_id', (int) $person_id)
                ->execute()
                ->current();
        if (empty($row)) {
            return TRUE;
        }
        return trim((string) $row['first_name']) === '' || (empty($row['cnic_number']) && empty($row['cnic_number_foreigner']));
    }

    /**
     * Profile without Name/CNIC: the rows migratecdr152.php wrote for a number
     * with no profile (person, person_initiate, person_category and the phone
     * link), with the app's person_id generator.
     */
    private static function insert_profile($person_id, $number, $company, $project_id, $user_id, $unowned_row_id) {
        $date = date('Y-m-d H:i:s');
        DB::insert('person', array('person_id', 'first_name', 'middle_name', 'last_name', 'father_name', 'address', 'user_id', 'view_access_level_id', 'edit_access_level_id', 'is_complete', 'is_deleted', 'view_count'))
                ->values(array($person_id, '', '', '', '', '', $user_id, 0, 0, 0, 0, 0))
                ->execute();
        // cnic_number and cnic_number_foreigner stay NULL: blank means "not known
        // yet" to the fill-in, and NULL (unlike '') never matches the foreigner
        // lookup Helpers_Utilities::get_person_id_with_cnic('') runs.
        DB::insert('person_initiate', array('person_id', 'is_foreigner', 'is_fingerprints_exist', 'user_id', 'created_from', 'access_by', 'created_at'))
                ->values(array($person_id, 0, 0, $user_id, 0, 0, $date))
                ->execute();
        DB::insert('person_category', array('person_id', 'category_id', 'project_id', 'reason', 'user_id', 'added_on'))
                ->values(array($person_id, 0, $project_id, '', $user_id, $date))
                ->execute();
        if ($unowned_row_id) {
            // Claim the existing row that has no owner, as update_subscriber_details() does.
            DB::update('person_phone_number')
                    ->set(array('sim_owner' => $person_id, 'person_id' => $person_id, 'user_id' => $user_id))
                    ->where('id', '=', $unowned_row_id)
                    ->where('sim_owner', '=', 0)
                    ->where('person_id', '=', 0)
                    ->execute();
        } else {
            DB::insert('person_phone_number', array('sim_owner', 'person_id', 'phone_number', 'status', 'mnc', 'connection_type', 'contact_type', 'user_id'))
                    ->values(array($person_id, $person_id, $number, 1, $company, 1, 1, $user_id))
                    ->execute();
        }
    }

    /**
     * One user_request + its email_messages row + person_linked_projects, i.e.
     * what Model_Email::user_request() and Model_Email::email_sended() write for
     * a single request, queued at status 0 / processing_index 0 for the send
     * cron. Priority is always Normal (1), so a batch never uses up the High
     * priority daily quota single Administrator requests rely on.
     */
    private static function insert_request($user_id, $request_type, $company, $number, $person_id, $project_id, $reason, $start_date, $end_date, $reference_id) {
        $email = self::build_email($request_type, $company, $number, $reference_id, $start_date, $end_date);
        $date = date('Y-m-d H:i:s');
        $message = DB::insert('email_messages', array('sender_id', 'message_body', 'message_subject', 'message_date'))
                ->values(array($email['to'], $email['body'], $email['subject'], $date))
                ->execute();
        $request = DB::insert('user_request', array('reference_id', 'user_id', 'user_request_type_id', 'message_id', 'company_name', 'status', 'processing_index', 'concerned_person_id', 'project_id', 'requested_value', 'startDate', 'endDate', 'reason', 'request_priority'))
                ->values(array($reference_id, $user_id, $request_type, $message[0], $company, 0, 0, $person_id, $project_id, $number, $start_date, $end_date, $reason, 1))
                ->execute();
        DB::insert('person_linked_projects', array('user_id', 'request_type_id', 'person_id', 'project_id', 'requested_value', 'request_time'))
                ->values(array($user_id, $request_type, $person_id, $project_id, $number, $date))
                ->execute();
        return $request[0];
    }

    /* Subject, body and recipient built exactly as Controller_Email::action_send() builds them for one number. */
    private static function build_email($request_type, $company, $number, $reference_id, $start_date, $end_date) {
        $template = Model_Email::get_email_tempalte($request_type, $company);
        $subject = str_replace("[case_number]", $reference_id, $template['subject']);
        $subject = str_replace("[mobile_number]", $number, $subject);

        $body = str_replace("[mobile_number]", $number, $template['body_txt']);
        $body = str_replace("[ptcl_number]", $number, $body);
        $body = str_replace("[case_number]", $reference_id, $body);
        if (!empty($start_date) && !empty($end_date)) {
            $start = strtotime($start_date);
            $end = strtotime($end_date);
            $body = str_replace("[start_date_dot]", date('d.m.Y', $start), $body);
            $body = str_replace("[end_date_dot]", date('d.m.Y', $end), $body);
            $body = str_replace("[start_date_slash]", date('d/m/Y', $start), $body);
            $body = str_replace("[end_date_slash]", date('d/m/Y', $end), $body);
            $body = str_replace("[start_date_slash_mdy]", date('m/d/Y', $start), $body);
            $body = str_replace("[end_date_slash_mdy]", date('m/d/Y', $end), $body);
            $body = str_replace("[start_date_hyphen]", date('d-m-Y', $start), $body);
            $body = str_replace("[end_date_hyphen]", date('d-m-Y', $end), $body);
        }
        $body = str_replace("[current_date]", date('d/M/Y'), $body);

        return array('subject' => $subject, 'body' => $body, 'to' => Helpers_CompanyEmail::get_email_address($company, $request_type));
    }

    public static function template_exists($request_type, $company) {
        $template = Model_Email::get_email_tempalte($request_type, $company);
        return !empty($template) && !empty($template['subject']) && !empty($template['body_txt']);
    }

    private static function insert_item($batch_id, $number, $request_id, $is_auto_subscriber, $profile_created, $note) {
        DB::insert('user_request_batch_item', array('batch_id', 'requested_value', 'request_id', 'is_auto_subscriber', 'profile_created', 'note'))
                ->values(array($batch_id, $number, $request_id, $is_auto_subscriber, $profile_created, $note === NULL ? NULL : mb_substr($note, 0, 255)))
                ->execute();
    }

    /**
     * Parser hook for Model_Generic::ManualSubInfoinsert(). Returns the profile a
     * Subscriber reply should fill in (blank CNIC / name / address only, the
     * mapping used for the migration projects in
     * Model_Generic::PROJECTS_SKIP_CNIC_CREATE), or 0 to keep the normal CNIC
     * find-or-create path. It applies only when all of these hold:
     *   - the request belongs to a Multi Number Request batch,
     *   - the reply is for the requested number,
     *   - the linked profile has no CNIC yet,
     *   - the reply CNIC is not already on another profile (that would make two
     *     profiles with one CNIC; the normal path links the SIM to that
     *     existing profile instead).
     */
    public static function fill_in_person_id($data) {
        if (empty($data['requestid'])) {
            return 0;
        }
        try {
            $request = DB::query(Database::SELECT, 'SELECT ur.request_id, ur.concerned_person_id, ur.requested_value
                    FROM user_request_batch_item AS bi
                    JOIN user_request AS ur ON ur.request_id = bi.request_id
                    WHERE bi.request_id = :request_id
                    LIMIT 1')
                    ->param(':request_id', (int) $data['requestid'])
                    ->execute()
                    ->current();
        } catch (Exception $e) {
            // Every subscriber reply passes through here; if this lookup ever
            // fails (e.g. the batch tables are missing) fall back to the normal
            // path rather than failing the parse of unrelated requests.
            Model_ErrorLog::log('multirequest_parse', $e->getMessage(), array('request_id' => (int) $data['requestid']), $e->getTraceAsString(), 'lookup_failure', 'subscriber_parsing');
            return 0;
        }
        if (empty($request)) {
            return 0;
        }
        $person_id = (int) $request['concerned_person_id'];
        $mobile_number = isset($data['mobile_number']) ? self::normalize_number($data['mobile_number']) : '';
        if ($mobile_number !== $request['requested_value']) {
            Model_ErrorLog::log(
                'multirequest_parse',
                'Reply is for ' . $mobile_number . ', not the requested number; normal subscriber rule applied',
                array('request_id' => $request['request_id'], 'mobile_requested' => $request['requested_value']),
                NULL,
                'number_mismatch',
                'subscriber_parsing',
                'warning'
            );
            return 0;
        }
        if ($person_id <= 0) {
            return 0;
        }
        $initiate = DB::select('cnic_number', 'cnic_number_foreigner')->from('person_initiate')
                ->where('person_id', '=', $person_id)
                ->limit(1)
                ->execute()
                ->current();
        if (empty($initiate) || !empty($initiate['cnic_number']) || !empty($initiate['cnic_number_foreigner'])) {
            return 0;
        }
        $cnic = !empty($data['cnic_number']) ? trim($data['cnic_number']) : (!empty($data['cnic_number_foreigner']) ? trim($data['cnic_number_foreigner']) : '');
        // Same shape the subscriber parser validates (13 digits, or 13
        // alphanumerics for a foreigner) before it is used in a lookup.
        if (!preg_match('/^[A-Za-z0-9]{13}$/', $cnic)) {
            return 0;
        }
        $cnic_owner = (int) Helpers_Utilities::get_person_id_with_cnic($cnic);
        if ($cnic_owner > 0 && $cnic_owner != $person_id) {
            return 0;
        }
        return $person_id;
    }

    /**
     * Rows for the batch report (DataTables server side parameters plus
     * the report filters), or the row count when $count is TRUE.
     */
    public static function report($post, $count = FALSE) {
        $where = array('1');
        $params = array();
        if (!empty($post['batch_id'])) {
            $where[] = 'bi.batch_id = ' . (int) $post['batch_id'];
        }
        if (!empty($post['company_name'])) {
            $where[] = 'b.company_name = ' . (int) $post['company_name'];
        }
        if (!empty($post['request_type'])) {
            $where[] = 'COALESCE(ur.user_request_type_id, b.user_request_type_id) = ' . (int) $post['request_type'];
        }
        if (!empty($post['from_date']) && strtotime($post['from_date'])) {
            $where[] = 'b.created_at >= :from_date';
            $params[':from_date'] = date('Y-m-d 00:00:00', strtotime($post['from_date']));
        }
        if (!empty($post['to_date']) && strtotime($post['to_date'])) {
            $where[] = 'b.created_at <= :to_date';
            $params[':to_date'] = date('Y-m-d 23:59:59', strtotime($post['to_date']));
        }
        if (isset($post['sSearch']) && trim($post['sSearch']) !== '') {
            $search = trim($post['sSearch']);
            $where[] = '(bi.requested_value LIKE :search OR bi.request_id = :search_id OR ur.reference_id = :search_id OR bi.batch_id = :search_id)';
            $params[':search'] = '%' . $search . '%';
            $params[':search_id'] = ctype_digit($search) ? $search : '-1';
        }

        $from = 'FROM user_request_batch_item AS bi
                JOIN user_request_batch AS b ON b.batch_id = bi.batch_id
                LEFT JOIN user_request AS ur ON ur.request_id = bi.request_id
                LEFT JOIN email_messages AS em ON em.message_id = ur.message_id
                LEFT JOIN person AS p ON p.person_id = ur.concerned_person_id
                WHERE ' . implode(' AND ', $where);

        if ($count) {
            $query = DB::query(Database::SELECT, 'SELECT COUNT(*) AS cnt ' . $from);
            foreach ($params as $key => $value) {
                $query->param($key, $value);
            }
            $row = $query->execute()->current();
            return (int) $row['cnt'];
        }

        $sortable = array(0 => 'bi.batch_id', 1 => 'bi.request_id', 2 => 'bi.requested_value', 5 => 'ur.created_at');
        $sort_column = (isset($post['iSortCol_0']) && isset($sortable[(int) $post['iSortCol_0']])) ? $sortable[(int) $post['iSortCol_0']] : 'bi.batch_id';
        $sort_dir = (isset($post['sSortDir_0']) && strtolower($post['sSortDir_0']) === 'asc') ? 'ASC' : 'DESC';
        $offset = isset($post['iDisplayStart']) ? max(0, (int) $post['iDisplayStart']) : 0;
        $limit = isset($post['iDisplayLength']) ? (int) $post['iDisplayLength'] : 25;
        $limit = ($limit > 0 && $limit <= 500) ? $limit : 25;

        $sql = 'SELECT bi.id, bi.batch_id, bi.requested_value, bi.request_id, bi.is_auto_subscriber, bi.profile_created, bi.note,
                       b.user_id, b.company_name, b.created_at AS batch_created_at,
                       COALESCE(ur.user_request_type_id, b.user_request_type_id) AS user_request_type_id,
                       ur.reference_id, ur.status, ur.processing_index, ur.concerned_person_id,
                       ur.created_at, ur.sending_date, ur.request_send_count, em.received_date,
                       p.first_name, p.last_name,
                       (SELECT ppn.sim_owner FROM person_phone_number AS ppn
                         WHERE ppn.phone_number = bi.requested_value
                         ORDER BY ppn.id DESC LIMIT 1) AS current_owner_id
                ' . $from . '
                ORDER BY ' . $sort_column . ' ' . $sort_dir . ', bi.id ASC
                LIMIT ' . $offset . ', ' . $limit;
        $query = DB::query(Database::SELECT, $sql);
        foreach ($params as $key => $value) {
            $query->param($key, $value);
        }
        return $query->execute()->as_array();
    }

    /* Latest error or warning system_error_log holds for each request (array of error_source, error_message). */
    public static function latest_errors(array $request_ids) {
        $request_ids = array_filter(array_map('intval', $request_ids));
        if (empty($request_ids)) {
            return array();
        }
        $sql = "SELECT request_id, error_source, error_message
                FROM system_error_log
                WHERE request_id IN (" . implode(',', $request_ids) . ")
                  AND severity IN ('error', 'warning')
                ORDER BY id DESC";
        $errors = array();
        foreach (DB::query(Database::SELECT, $sql)->execute()->as_array() as $row) {
            if (!isset($errors[$row['request_id']])) {
                $errors[$row['request_id']] = $row;
            }
        }
        return $errors;
    }

}
