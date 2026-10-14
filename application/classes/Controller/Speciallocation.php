<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Special Location Request: the same Current Location request for one number
 * sent every N minutes, up to a number of times, until an end time (see
 * Model_Speciallocation), and its report.
 *
 *   /speciallocation/index        request form           "Special Location Request" right
 *   /speciallocation/submit       AJAX, JSON             "Special Location Request" right
 *   /speciallocation/report       list of requests       either right (own requests only without the report right)
 *   /speciallocation/report_data  DataTables JSON        either right
 *   /speciallocation/detail       rounds and replies     owner, or the report right
 *   /speciallocation/cancel       AJAX, JSON             owner, or the report right
 *
 * The normal location request (Userrequest::action_requestlocation) is not
 * changed by any of this.
 */
class Controller_Speciallocation extends Controller_Working {

    public function __Construct(Request $request, Response $response) {
        parent::__construct($request, $response);
        $this->request = $request;
        $this->response = $response;
    }

    public function action_index() {
        $user = Auth::instance()->get_user();
        if (!Model_Speciallocation::has_access($user->id)) {
            $this->template->content = View::factory('templates/user/access_denied');
            return;
        }
        // Opened from a profile: number and profile come prefilled.
        $msisdn = Model_Multirequest::normalize_number($this->request->query('msisdn'));
        $pid = $this->request->query('pid');
        $person_id = !empty($pid) ? (int) Helpers_Utilities::encrypted_key($pid, 'decrypt') : 0;
        $this->template->content = View::factory('templates/user/special_location_form')
                ->set('companies', Helpers_Utilities::get_companies_map_by_mnc())
                ->set('msisdn', $msisdn)
                ->set('pid', $person_id > 0 ? $pid : '');
    }

    public function action_submit() {
        $this->auto_render = FALSE;
        $this->response->headers('Content-Type', 'application/json');
        $user = Auth::instance()->get_user();
        if (!Model_Speciallocation::has_access($user->id)) {
            $this->_json(array('status' => 0, 'message' => 'You are not permitted to send Special Location Requests.'), 403);
            return;
        }
        if ($this->request->method() !== Request::POST) {
            $this->_json(array('status' => 0, 'message' => 'Invalid request.'), 405);
            return;
        }

        $post = $this->request->post();
        $number = Model_Multirequest::normalize_number(isset($post['msisdn']) ? $post['msisdn'] : '');
        $company = isset($post['company_name']) ? (int) $post['company_name'] : 0;
        $project_id = isset($post['project_id']) ? (int) $post['project_id'] : 0;
        $reason = isset($post['reason']) ? trim($post['reason']) : '';
        $interval = isset($post['interval_minutes']) ? (int) $post['interval_minutes'] : 0;
        $max_requests = isset($post['max_requests']) ? (int) $post['max_requests'] : 0;
        $duration = isset($post['duration_hours']) ? (int) $post['duration_hours'] : 0;
        $pid = !empty($post['pid']) ? (int) Helpers_Utilities::encrypted_key($post['pid'], 'decrypt') : 0;
        $errors = array();

        if ($number === '') {
            $errors[] = 'Enter a valid mobile number (3XXXXXXXXX).';
        } elseif (Helpers_Email::check_in_blocked_number_list($number)) {
            $errors[] = 'This number is in the blocked number list.';
        }
        if (!in_array($company, Model_Speciallocation::$companies)) {
            $errors[] = 'Select a company.';
        } elseif (!Model_Multirequest::template_exists(Model_Speciallocation::TYPE_LOCATION, $company)) {
            $errors[] = 'No Current Location email template is set up for this company.';
        }
        // get_projects_list() with an id only returns an open project the user may choose.
        if ($project_id <= 0 || !Helpers_Utilities::get_projects_list($project_id)) {
            $errors[] = 'Select a linked project.';
        }
        // Same rule as the single request forms.
        if (mb_strlen($reason) < 5 || mb_strlen($reason) > 500 || !preg_match('/^[-a-zA-Z0-9_ .,\/]+$/', $reason)) {
            $errors[] = 'Reason must be 5 to 500 characters: letters, numbers, space and - _ . , / only.';
        }
        if (!isset(Model_Speciallocation::$intervals[$interval])) {
            $errors[] = 'Select the time between requests.';
        }
        if (!in_array($max_requests, Model_Speciallocation::$counts)) {
            $errors[] = 'Select the number of requests.';
        }
        if (!isset(Model_Speciallocation::$durations[$duration])) {
            $errors[] = 'Select the end time.';
        }
        if (empty($errors)) {
            $fit = Model_Speciallocation::rounds_that_fit($interval, $duration);
            if ($max_requests > $fit) {
                $errors[] = 'Only ' . $fit . ' requests fit in ' . Model_Speciallocation::$durations[$duration] . ' at one every '
                        . Model_Speciallocation::$intervals[$interval] . '. Choose fewer requests, a shorter interval or a later end time.';
            }
        }
        $person_id = 0;
        if (empty($errors)) {
            // The location parser attaches the reply to a profile.
            $person_id = Model_Speciallocation::profile_for_number($number, $pid);
            if (!$person_id) {
                $errors[] = 'This number is not linked to any profile. Create the profile (or send a Subscriber request) first.';
            }
        }
        if (!empty($errors)) {
            $this->_json(array('status' => 0, 'errors' => $errors));
            return;
        }

        try {
            $result = Model_Speciallocation::create($user->id, $person_id, $number, $company, (string) $project_id, $reason, $interval, $max_requests, $duration);
        } catch (Exception $e) {
            Model_ErrorLog::log('action_speciallocation_submit', $e->getMessage(), array('mobile_requested' => $number, 'company_name' => $company), $e->getTraceAsString(), 'exception', 'special_location');
            $this->_json(array('status' => 0, 'message' => 'System error. Contact DRAMS Support Team.'));
            return;
        }
        if (isset($result['error'])) {
            $this->_json(array('status' => 0, 'message' => $result['error']));
            return;
        }
        $this->_json(array(
            'status' => 1,
            'id' => $result['id'],
            'request_id' => $result['request_id'],
            'note' => $result['note'],
            'detail_url' => URL::site('speciallocation/detail') . '?id=' . rawurlencode(Helpers_Utilities::encrypted_key($result['id'], 'encrypt')),
        ));
    }

    public function action_report() {
        $user = Auth::instance()->get_user();
        $all = Model_Speciallocation::has_report_access($user->id);
        if (!$all && !Model_Speciallocation::has_access($user->id)) {
            $this->template->content = View::factory('templates/user/access_denied');
            return;
        }
        $this->template->content = View::factory('templates/user/special_location_report')
                ->set('companies', Helpers_Utilities::get_companies_map_by_mnc())
                ->set('all_users', $all)
                ->set('can_send', Model_Speciallocation::has_access($user->id));
    }

    public function action_report_data() {
        $this->auto_render = FALSE;
        $this->response->headers('Content-Type', 'application/json');
        $output = array(
            'sEcho' => isset($_GET['sEcho']) ? intval($_GET['sEcho']) : 1,
            'iTotalRecords' => 0,
            'iTotalDisplayRecords' => 0,
            'aaData' => array(),
        );
        $user = Auth::instance()->get_user();
        $all = Model_Speciallocation::has_report_access($user->id);
        if (!$all && !Model_Speciallocation::has_access($user->id)) {
            $this->_json($output, 403);
            return;
        }
        try {
            $post = $this->request->query();
            $only_user = $all ? 0 : (int) $user->id;
            $total = Model_Speciallocation::report($post, $only_user, TRUE);
            $rows = Model_Speciallocation::report($post, $only_user);
            $output['iTotalRecords'] = $output['iTotalDisplayRecords'] = $total;

            foreach ($rows as $i => $row) {
                if ((int) $row['replies'] < (int) $row['sent_count'] && Model_Speciallocation::backfill_rounds($row)) {
                    $rows[$i]['replies'] = Model_Speciallocation::reply_count($row['id']);
                }
            }
            $user_names = Helpers_Utilities::get_user_names_by_ids(array_column($rows, 'user_id'));
            $companies = Helpers_Utilities::get_companies_map_by_mnc();
            $last = Model_Speciallocation::last_locations(array_column($rows, 'id'));
            foreach ($rows as $row) {
                $output['aaData'][] = $this->_report_row($row, $user_names, $companies, $last, $user->id, $all);
            }
        } catch (Exception $e) {
            Model_ErrorLog::log('action_speciallocation_report_data', $e->getMessage(), array(), $e->getTraceAsString(), 'exception', 'report');
        }
        $this->_json($output);
    }

    private function _report_row(array $row, array $user_names, array $companies, array $last, $user_id, $all) {
        $key = Helpers_Utilities::encrypted_key($row['id'], 'encrypt');
        $detail = URL::site('speciallocation/detail') . '?id=' . rawurlencode($key);
        $company = isset($companies[$row['company_name']]) ? $companies[$row['company_name']]->company_name : $row['company_name'];
        $cells = array();
        $cells[] = '<a href="' . $detail . '"><b>#' . (int) $row['id'] . '</b></a>';
        $cells[] = HTML::chars($row['msisdn']) . '<br><b>' . HTML::chars($company) . '</b>';

        $name = trim($row['first_name'] . ' ' . $row['last_name']);
        $cells[] = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($row['person_id'], 'encrypt')) . '">#' . (int) $row['person_id'] . '</a> '
                . ($name !== '' ? HTML::chars($name) : '<i>No name yet</i>');
        $cells[] = 'Every ' . HTML::chars(isset(Model_Speciallocation::$intervals[$row['interval_minutes']]) ? Model_Speciallocation::$intervals[$row['interval_minutes']] : $row['interval_minutes'] . ' min')
                . '<br><small>Until ' . HTML::chars($row['end_at']) . '</small>';
        $cells[] = HTML::chars(isset($user_names[$row['user_id']]) ? $user_names[$row['user_id']] : 'Unknown');
        $cells[] = HTML::chars($row['created_at']);
        $cells[] = '<b>' . (int) $row['sent_count'] . '</b> / ' . (int) $row['max_requests'] . ' sent<br><small>' . (int) $row['replies'] . ' replies</small>';

        if (isset($last[$row['id']])) {
            $cells[] = self::location_html($last[$row['id']]);
        } else {
            $cells[] = '<i>No reply yet</i>';
        }

        $cells[] = $this->_status_html($row);

        $actions = '<a href="' . $detail . '" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> Replies</a>';
        if ((int) $row['status'] === Model_Speciallocation::STATUS_ACTIVE && ($row['user_id'] == $user_id || $all)) {
            $actions .= ' <button type="button" class="btn btn-xs btn-danger special-cancel" data-id="' . HTML::chars($key) . '"><i class="fa fa-stop"></i> Cancel</button>';
        }
        $cells[] = $actions;
        return $cells;
    }

    public function action_detail() {
        $user = Auth::instance()->get_user();
        $id = (int) Helpers_Utilities::encrypted_key((string) $this->request->query('id'), 'decrypt');
        $special = $id > 0 ? Model_Speciallocation::get($id) : NULL;
        if (empty($special) || !Model_Speciallocation::can_view($special, $user->id)) {
            $this->template->content = View::factory('templates/user/access_denied');
            return;
        }
        $person = DB::select('first_name', 'last_name')->from('person')->where('person_id', '=', (int) $special['person_id'])->execute()->current();
        $user_names = Helpers_Utilities::get_user_names_by_ids(array($special['user_id']));
        Model_Speciallocation::backfill_rounds($special);
        $rounds = Model_Speciallocation::rounds($special['id']);
        $this->template->content = View::factory('templates/user/special_location_detail')
                ->set('special', $special)
                ->set('key', Helpers_Utilities::encrypted_key($special['id'], 'encrypt'))
                ->set('person', $person)
                ->set('requested_by', isset($user_names[$special['user_id']]) ? $user_names[$special['user_id']] : 'Unknown')
                ->set('companies', Helpers_Utilities::get_companies_map_by_mnc())
                ->set('rounds', $rounds)
                ->set('errors', Model_Multirequest::latest_errors(array_column($rounds, 'request_id')))
                ->set('can_cancel', (int) $special['status'] === Model_Speciallocation::STATUS_ACTIVE
                        && ($special['user_id'] == $user->id || Model_Speciallocation::has_report_access($user->id)))
                ->set('status_html', $this->_status_html($special));
    }

    public function action_cancel() {
        $this->auto_render = FALSE;
        $this->response->headers('Content-Type', 'application/json');
        $user = Auth::instance()->get_user();
        if ($this->request->method() !== Request::POST) {
            $this->_json(array('status' => 0, 'message' => 'Invalid request.'), 405);
            return;
        }
        $id = (int) Helpers_Utilities::encrypted_key((string) $this->request->post('id'), 'decrypt');
        $error = $id > 0 ? Model_Speciallocation::cancel($id, $user->id) : 'Request not found.';
        $this->_json($error === '' ? array('status' => 1) : array('status' => 0, 'message' => $error));
    }

    /* Status label plus next round or the stop note. */
    private function _status_html(array $special) {
        $status = (int) $special['status'];
        $classes = array(1 => 'label-primary', 2 => 'label-success', 3 => 'label-default', 4 => 'label-danger');
        $html = '<span class="label ' . (isset($classes[$status]) ? $classes[$status] : 'label-default') . '">'
                . HTML::chars(isset(Model_Speciallocation::$status_names[$status]) ? Model_Speciallocation::$status_names[$status] : $status) . '</span>';
        if ($status === Model_Speciallocation::STATUS_ACTIVE && !empty($special['next_run_at'])) {
            $html .= '<br><small>Next: ' . HTML::chars($special['next_run_at']) . '</small>';
        }
        if (!empty($special['note'])) {
            $html .= '<br><small>' . HTML::chars($special['note']) . '</small>';
        }
        return $html;
    }

    /* Parsed location of a round: time, cell, coordinates (map link) and address. Used by the detail view too. */
    public static function location_html(array $round) {
        $networks = array(1 => '2G', 2 => '3G', 3 => '4G');
        $html = '<b>' . HTML::chars($round['location_at']) . '</b>';
        $cell = array();
        if ($round['lac_id'] !== NULL && $round['lac_id'] !== '') {
            $cell[] = 'LAC ' . HTML::chars($round['lac_id']);
        }
        if ($round['cell_id'] !== NULL && $round['cell_id'] !== '') {
            $cell[] = 'Cell ' . HTML::chars($round['cell_id']);
        }
        if (isset($networks[(int) $round['network']])) {
            $cell[] = $networks[(int) $round['network']];
        }
        if ($cell) {
            $html .= '<br><small>' . implode(', ', $cell) . '</small>';
        }
        if ((float) $round['latitude'] != 0 && (float) $round['longitude'] != 0) {
            $coords = (float) $round['latitude'] . ',' . (float) $round['longitude'];
            $html .= '<br><a href="https://www.google.com/maps?q=' . $coords . '" target="_blank"><i class="fa fa-map-marker"></i> ' . $coords . '</a>';
        }
        if (!empty($round['address'])) {
            $html .= '<br><small>' . HTML::chars($round['address']) . '</small>';
        }
        return $html;
    }

    private function _json(array $data, $status = 200) {
        $this->response->status($status);
        $this->response->body(json_encode($data));
    }

}
