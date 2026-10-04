<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Multi Number Request: a list of mobile numbers sent as one Telco request per
 * number (see Model_Multirequest), and the batch report.
 *
 *   /multirequest/index        request form      "Multi Number Request" right (Access Control List)
 *   /multirequest/submit       AJAX, JSON        "Multi Number Request" right
 *   /multirequest/report       batch report      "Multi Number Request Report" right
 *   /multirequest/report_data  DataTables JSON   "Multi Number Request Report" right
 *
 * Every action checks its own rule, so the URLs cannot be used directly by a
 * user the sidebar hides them from.
 */
class Controller_Multirequest extends Controller_Working {

    public function __Construct(Request $request, Response $response) {
        parent::__construct($request, $response);
        $this->request = $request;
        $this->response = $response;
    }

    public function action_index() {
        $user = Auth::instance()->get_user();
        if (!Model_Multirequest::has_access($user->id)) {
            $this->template->content = View::factory('templates/user/access_denied');
            return;
        }
        $this->template->content = View::factory('templates/user/multi_request_form')
                ->set('request_types', Model_Multirequest::$request_types)
                ->set('companies', Helpers_Utilities::get_companies_map_by_mnc())
                ->set('max_numbers', Model_Multirequest::MAX_NUMBERS)
                ->set('has_report_access', Model_Multirequest::has_report_access($user->id));
    }

    public function action_submit() {
        $this->auto_render = FALSE;
        $this->response->headers('Content-Type', 'application/json');
        $user = Auth::instance()->get_user();
        if (!Model_Multirequest::has_access($user->id)) {
            $this->_json(array('status' => 0, 'message' => 'You are not permitted to send Multi Number Requests.'), 403);
            return;
        }
        if ($this->request->method() !== Request::POST) {
            $this->_json(array('status' => 0, 'message' => 'Invalid request.'), 405);
            return;
        }

        $post = $this->request->post();
        $request_types = Model_Multirequest::$request_types;
        $request_type = isset($post['request_type']) ? (int) $post['request_type'] : 0;
        $company = isset($post['company_name']) ? (int) $post['company_name'] : 0;
        $project_id = isset($post['project_id']) ? (int) $post['project_id'] : 0;
        $reason = isset($post['reason']) ? trim($post['reason']) : '';
        $errors = array();

        if (!isset($request_types[$request_type])) {
            $errors[] = 'Select a request type.';
        } elseif (!in_array($company, $request_types[$request_type]['companies'])) {
            $errors[] = 'Select a Telco that supports this request type.';
        } elseif (!Model_Multirequest::template_exists($request_type, $company)
                || ($request_type != Model_Multirequest::TYPE_SUBSCRIBER && !Model_Multirequest::template_exists(Model_Multirequest::TYPE_SUBSCRIBER, $company))) {
            $errors[] = 'No email template is set up for this request type and Telco.';
        }
        // get_projects_list() with an id only returns an open project the user may choose.
        if ($project_id <= 0 || !Helpers_Utilities::get_projects_list($project_id)) {
            $errors[] = 'Select a linked project.';
        }
        // Same rule as the single request forms (templates/requests/subsciber.php).
        if (mb_strlen($reason) < 5 || mb_strlen($reason) > 500 || !preg_match('/^[-a-zA-Z0-9_ .,\/]+$/', $reason)) {
            $errors[] = 'Reason must be 5 to 500 characters: letters, numbers, space and - _ . , / only.';
        }

        $start_date = $end_date = NULL;
        if (isset($request_types[$request_type]) && Model_Multirequest::needs_dates($request_type)) {
            $start = $this->_date(isset($post['start_date']) ? $post['start_date'] : '');
            $end = $this->_date(isset($post['end_date']) ? $post['end_date'] : '');
            if (!$start || !$end) {
                $errors[] = 'Select the CDR period (mm/dd/yyyy).';
            } elseif ($start > $end) {
                $errors[] = 'Date From must not be after Date To.';
            } elseif ($end > new DateTime('today')) {
                $errors[] = 'Date To cannot be in the future.';
            } else {
                $start_date = $start->format('Y-m-d');
                $end_date = $end->format('Y-m-d');
            }
        }

        $numbers = Model_Multirequest::parse_numbers(isset($post['numbers']) ? $post['numbers'] : '');
        if (!empty($numbers['invalid'])) {
            $errors[] = 'Not valid mobile numbers: ' . implode(', ', array_slice($numbers['invalid'], 0, 20)) . (count($numbers['invalid']) > 20 ? ' ...' : '');
        } elseif (empty($numbers['valid'])) {
            $errors[] = 'Enter at least one mobile number.';
        } elseif (count($numbers['valid']) > Model_Multirequest::MAX_NUMBERS) {
            $errors[] = 'At most ' . Model_Multirequest::MAX_NUMBERS . ' numbers can be sent at once (' . count($numbers['valid']) . ' entered).';
        }

        if (!empty($errors)) {
            $this->_json(array('status' => 0, 'errors' => $errors));
            return;
        }

        try {
            $result = Model_Multirequest::create_batch($user->id, $request_type, $company, (string) $project_id, $reason, $start_date, $end_date, $numbers['valid']);
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'action_multirequest_submit',
                $e->getMessage(),
                array('request_type' => $request_type, 'company_name' => $company, 'numbers' => count($numbers['valid'])),
                $e->getTraceAsString(),
                'exception',
                'batch_create'
            );
            $this->_json(array('status' => 0, 'message' => 'System error. Contact DRAMS Support Team.'));
            return;
        }
        if (isset($result['error'])) {
            $this->_json(array('status' => 0, 'message' => $result['error'], 'skipped' => $result['skipped'], 'duplicates' => $numbers['duplicates']));
            return;
        }

        $created = $skipped = $notes = array();
        foreach ($result['items'] as $item) {
            if ($item['skip'] !== '') {
                $skipped[] = array('number' => $item['number'], 'reason' => $item['skip']);
                continue;
            }
            $created[] = array(
                'number' => $item['number'],
                'request_id' => $item['request_id'],
                'reference_id' => $item['reference_id'],
                'subscriber_request_id' => $item['subscriber_request_id'],
                'profile_created' => $item['profile_created'],
            );
            if ($item['note'] !== '') {
                $notes[] = array('number' => $item['number'], 'note' => $item['note']);
            }
        }
        $this->_json(array(
            'status' => 1,
            'batch_id' => $result['batch_id'],
            'created' => $created,
            'skipped' => $skipped,
            'notes' => $notes,
            'duplicates' => $numbers['duplicates'],
        ));
    }

    public function action_report() {
        $user = Auth::instance()->get_user();
        if (!Model_Multirequest::has_report_access($user->id)) {
            $this->template->content = View::factory('templates/user/access_denied');
            return;
        }
        $this->template->content = View::factory('templates/user/multi_request_report')
                ->set('request_types', Model_Multirequest::$request_types)
                ->set('companies', Helpers_Utilities::get_companies_map_by_mnc());
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
        if (!Model_Multirequest::has_report_access($user->id)) {
            $this->_json($output, 403);
            return;
        }
        try {
            $post = $this->request->query();
            $total = Model_Multirequest::report($post, TRUE);
            $rows = Model_Multirequest::report($post);
            $output['iTotalRecords'] = $output['iTotalDisplayRecords'] = $total;

            $user_names = Helpers_Utilities::get_user_names_by_ids(array_column($rows, 'user_id'));
            $companies = Helpers_Utilities::get_companies_map_by_mnc();
            $errors = Model_Multirequest::latest_errors(array_column($rows, 'request_id'));
            foreach ($rows as $row) {
                $output['aaData'][] = $this->_report_row($row, $user_names, $companies, $errors);
            }
        } catch (Exception $e) {
            Model_ErrorLog::log('action_multirequest_report_data', $e->getMessage(), array(), $e->getTraceAsString(), 'exception', 'report');
        }
        $this->_json($output);
    }

    private function _report_row(array $row, array $user_names, array $companies, array $errors) {
        $company = isset($companies[$row['company_name']]) ? $companies[$row['company_name']]->company_name : $row['company_name'];
        $cells = array();
        $cells[] = '<b>#' . (int) $row['batch_id'] . '</b><br><small>' . HTML::chars($row['batch_created_at']) . '</small>';

        if (empty($row['request_id'])) {
            $cells[] = '<span class="label label-default">Skipped</span>';
        } else {
            $link = URL::site('userrequest/request_status_detail/' . Helpers_Utilities::encrypted_key($row['request_id'], 'encrypt'));
            $cells[] = '<a href="' . $link . '">' . (int) $row['request_id'] . '</a><br><small>Ref# ' . (int) $row['reference_id'] . '</small>'
                    . ($row['is_auto_subscriber'] ? '<br><span class="label label-info">Auto Subscriber</span>' : '');
        }
        $cells[] = HTML::chars($row['requested_value']);
        $cells[] = HTML::chars(Helpers_Utilities::get_request_type_name((int) $row['user_request_type_id'])) . '<br><b>' . HTML::chars($company) . '</b>';
        $cells[] = HTML::chars(isset($user_names[$row['user_id']]) ? $user_names[$row['user_id']] : 'Unknown');
        $cells[] = empty($row['created_at']) ? '--' : HTML::chars($row['created_at']);

        if (empty($row['request_id'])) {
            $cells[] = '--';
            $cells[] = '--';
            $cells[] = '--';
        } else {
            $status = (int) $row['status'];
            $status_class = array(0 => 'label-default', 1 => 'label-primary', 2 => 'label-success', 3 => 'label-danger', 4 => 'label-warning');
            $send = '<span class="label ' . (isset($status_class[$status]) ? $status_class[$status] : 'label-default') . '">'
                    . HTML::chars(Helpers_Utilities::get_request_status_name($status)) . '</span>';
            if (!empty($row['sending_date'])) {
                $send .= '<br><small>' . HTML::chars($row['sending_date']) . ' (' . (int) $row['request_send_count'] . 'x)</small>';
            }
            $cells[] = $send;
            $cells[] = ($status == 2 && strtotime($row['received_date']) > 0) ? HTML::chars($row['received_date']) : ($status == 1 ? 'Awaiting reply' : '--');
            $cells[] = ($status == 2) ? HTML::chars(Helpers_Utilities::get_parsing_status_name((int) $row['processing_index'])) : '--';
        }

        if (!empty($row['concerned_person_id'])) {
            $name = trim($row['first_name'] . ' ' . $row['last_name']);
            $profile = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($row['concerned_person_id'], 'encrypt')) . '">#' . (int) $row['concerned_person_id'] . '</a> '
                    . ($name !== '' ? HTML::chars($name) : '<i>No name yet</i>');
            $cells[] = $profile . ($row['profile_created'] ? '<br><span class="label label-warning">Created by batch</span>' : '');
        } elseif (!empty($row['request_id']) && !empty($row['current_owner_id']) && (int) $row['processing_index'] == 5) {
            // Subscriber request with no profile: the parser linked the number from the reply CNIC.
            $cells[] = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($row['current_owner_id'], 'encrypt')) . '">#' . (int) $row['current_owner_id'] . '</a><br><small>Linked from reply</small>';
        } else {
            $cells[] = empty($row['request_id']) ? '--' : '<i>From reply</i>';
        }

        $note = (string) $row['note'];
        if (!empty($row['request_id']) && isset($errors[$row['request_id']])) {
            $error = $errors[$row['request_id']];
            $failed = (int) $row['status'] == 3 || in_array((int) $row['processing_index'], array(1, 3));
            // Errors of failed requests, and the parser's reply-number mismatch warning.
            if ($failed || $error['error_source'] == 'multirequest_parse') {
                $note = trim($note . ' ' . $error['error_message']);
            }
        }
        $cells[] = HTML::chars($note);
        return $cells;
    }

    /* mm/dd/yyyy as posted by the datepicker, strictly (no 13/45 roll over). */
    private function _date($value) {
        $value = trim((string) $value);
        $date = DateTime::createFromFormat('!m/d/Y', $value);
        return ($date && $date->format('m/d/Y') === $value) ? $date : NULL;
    }

    private function _json(array $data, $status = 200) {
        $this->response->status($status);
        $this->response->body(json_encode($data));
    }

}
