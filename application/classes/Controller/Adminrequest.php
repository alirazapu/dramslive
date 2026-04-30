<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Adminrequest extends Controller_Working
{

    public function __Construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->request = $request;
        $this->response = $response;
    }

    /*     * Admin Request*/

    public function action_admin_request_sent_form()
    {

        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 34) == 1) {
                /* File Included */
                $this->template->content = View::factory('templates/user/admin_request_sent_form');
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_admin_request_sent_form',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'page_load'
            );
            error_log("[" . date('c') . "] action_admin_request_sent_form exception: " . $ex->getMessage());
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /*     * Admin Request*/

    public function action_admin_custom_request_form()
    {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 34) == 1) {
                /* File Included */
                $this->template->content = View::factory('templates/user/admin_custom_request_form');
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_admin_custom_request_form',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'page_load'
            );
            error_log("[" . date('c') . "] action_admin_custom_request_form exception: " . $ex->getMessage());
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /* Admin Request Status (requests count) */

    public function action_admin_sent_request_count()
    {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            if (Helpers_Utilities::chek_role_access($this->role_id, 14) == 1) {
                /* Posted Data */

                $post = $this->request->post(); //            echo '<pre>';
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['message']) && $post['message'] == 1) {
                    $message = 'Congratulation! Request Sent successfully';
                }
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('request_status_post', $post);
                /* File Included */
                include 'user_functions/admin_sent_request_count.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_admin_sent_request_count',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'page_load'
            );
            error_log("[" . date('c') . "] action_admin_sent_request_count exception: " . $ex->getMessage());
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /* Admin Request Status (users requests count) */

    public function action_user_request_count()
    {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            if (Helpers_Utilities::chek_role_access($this->role_id, 14) == 1) {
                /* Posted Data */

                $post = $this->request->post(); //            echo '<pre>';
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                if (!empty($post['message']) && $post['message'] == 1) {
                    $message = 'Congratulation! Request Sent successfully';
                }
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('request_status_post', $post);
                /* File Included */
                include 'user_functions/user_request_count.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_user_request_count',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'page_load'
            );
            error_log("[" . date('c') . "] action_user_request_count exception: " . $ex->getMessage());
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /* Admin Request Status (request_status) */

    public function action_admin_sent_request_status()
    {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            if (Helpers_Utilities::chek_role_access($this->role_id, 14) == 1) {
                /* Posted Data */

                $post = $this->request->post(); //            echo '<pre>';
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['message']) && $post['message'] == 1) {
                    $message = 'Congratulation! Request Sent successfully';
                }
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('request_status_post', $post);
                /* File Included */
                include 'user_functions/admin_sent_request_status.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_admin_sent_request_status',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'page_load'
            );
            error_log("[" . date('c') . "] action_admin_sent_request_status exception: " . $ex->getMessage());
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxadminsentrequeststatus()
    {
        try {
            $this->auto_rednder = false;
            $reply_flag = '';
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('request_status_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_AdminRequest();
                $rows_count = $data->admin_sent_request_status($post, 'true');
                $profiles = $data->admin_sent_request_status($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $request_type_id = (isset($item['user_request_type_id'])) ? $item['user_request_type_id'] : 0;
                        $request_id = (isset($item['request_id'])) ? $item['request_id'] : 'NA';
                        $request_reference_id = (isset($item['reference_id'])) ? $item['reference_id'] : 0;
                        $user_id = (isset($item['user_id'])) ? $item['user_id'] : 0;
                        $user_role_name = (isset($user_id)) ? Helpers_Utilities::get_user_role_name($user_id) : 'N/A';
                        $user_name = (isset($item['user_id'])) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $username = (isset($item['user_id'])) ? '<br><span><b>' . Helpers_Utilities::get_username($item['user_id']) . '</b></span>' : '--';
                        $user_request = (isset($item['email_type_name'])) ? $item['email_type_name'] : 'NA';
                        $user_request .= '<span><br><b>ID:';
                        $user_request .= (isset($item['request_id'])) ? $item['request_id'] : 'NA';
                        $user_request .= ' Ref#' . $request_reference_id;
                        $user_request .= '</b></span>';
                        $company_name = '<span><b>';
                        $company_name .= (isset($item['company_name']) && !empty($item['company_name'])) ? Helpers_Utilities::get_companies_data($item['company_name'])->company_name : '--';
                        $company_name .= '</b></span><br><span>';
                        $company_name .= (isset($item['requested_value']) && !empty($item['requested_value'])) ? $item['requested_value'] : '--';
                        $company_name .= '</span>';
                        $requested_value = (isset($item['requested_value'])) ? $item['requested_value'] : 'NA';
                        $reply = (isset($item['reply'])) ? $item['reply'] : 0;
                        $reason = (isset($item['reason'])) ? $item['reason'] : 'NA';

                        $concerned_person_id = (isset($item['concerned_person_id'])) ? $item['concerned_person_id'] : 'NA';
                        $enc_request_id = trim(Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt'));
                        $created_at = (isset($item['created_at'])) ? $item['created_at'] : 'NA';
                        $status = (isset($item['status'])) ? $item['status'] : 0;
                        $system_status_flag = '';
                        if ($request_type_id == 8) {
                            switch ($status) {
                                case 0:
                                    $status_flag = '<span class="label label-info">Request In Queue</span>';
                                    break;
                                case 1:
                                    $status_flag = '<span class="label label-success">Request Send</span>';
                                    break;
                                case 2:
                                    $status_flag = '<span class="label label-success">Request Completed</span>';
                                    break;
                                case 3:
                                    $status_flag = '<span class="label label-danger">Request Error</span>';
                                    break;
                            }
                            if ($status == 1) {
                                $system_status_flag = '<span class="badge badge-pill badge-warning">Response waiting</span>';
                            } else {
                                $system_status_flag = '<span class="badge badge-pill badge-success">Processed</span>';
                            }

                            //this part will run if request is other then nadra
                        } else {

                            switch ($status) {
                                case 0:
                                    $status_flag = '<span class="label label-info">Request in Queue</span>';
                                    break;
                                case 1:
                                    $status_flag = '<span class="label label-primary">Request Send</span>';
                                    break;
                                case 2:
                                    if ($request_type_id == 2 && $item['processing_index'] == 6) {
                                        $recievedfilepath = "'" . trim($item['received_file_path']) . "'";
                                        $recievedbody = str_replace(PHP_EOL, ' ', strip_tags(trim($item['received_body'])));
                                        $recievedbody = "'" . $recievedbody . "'";

                                        $status_flag = '<span class="label label-success">Email Received</span>';
                                        $status_flag .= '<span class="badge badge-pill badge-success"><a href="#" onclick="fullparseimeicdr(' . $request_id . ',' . $recievedfilepath . ',' . $recievedbody . ',' . $requested_value . ')" style="color: white">Parse</a></span>';
                                    } else {
                                        $status_flag = '<span class="label label-success">Email Received</span>';
                                    }

                                    break;
                                case 3:
                                    $status_flag = '<span class="label label-danger">Email Sending Error</span>';
                                    break;
                                case 4:
                                    $status_flag = '<span class="label label-warning">Request Rejected</span>';
                                    break;
                            }
                            $reply_flag = '<span class="badge badge-pill badge-warning">Pending</span>';
                            //$system_status_flag = '';
                            if ($status == 2) {
                                // system status for FO sent/pending
                                if ($reply == 1) {
                                    $reply_flag = '<span class="badge badge-pill badge-success">Sent</span>';
                                } else {
                                    $reply_flag = '<span class="badge badge-pill badge-warning">Pending</span>';
                                }
                                //system processing status
                                $system_status = (isset($item['processing_index'])) ? $item['processing_index'] : 0;
                                switch ($system_status) {
                                    case 1:
                                        $system_status_flag = '<span class="badge badge-pill badge-danger">Format Error</span>';
                                        break;
                                    case 2:
                                        $system_status_flag = '<span class="badge badge-pill badge-secondary">No Data</span>';
                                        break;
                                    case 3:
                                        $system_status_flag = '<span class="badge badge-pill badge-danger">Parsing Error</span>';
                                        break;
                                    case 4:
                                        $system_status_flag = '<span class="badge badge-pill badge-info">Waiting Parsing</span>';
                                        break;
                                    case 5:
                                        $system_status_flag = '<span class="badge badge-pill badge-success">Parsing completed</span>';
                                        if ($request_type_id == 2) {
                                            $imei_link = (isset($item['requested_value']) && !empty($item['requested_value'])) ? $item['requested_value'] : '--';
                                            $system_status_flag .= '<a href="' . URL::site('User/upload_against_imei') . '?imei=' . $imei_link . '" <span class="badge badge-pill badge-success">Check IMEI</span></a>';
                                        }
                                        break;
                                    case 6:
                                        $system_status_flag = '<span class="badge badge-pill badge-primary">Partially Parsed</span>';
                                        break;
                                    case 7:
                                        $system_status_flag = '<span class="badge badge-pill badge-success">Marked Complete</span>';
                                        break;
                                }
                            } else {
                                $system_status_flag = '<span class="badge badge-pill badge-warning">Response waiting</span>';
                            }
                        }
                        $member_name_link = '<a class="btn btn-block btn-info btn-xs" href="' . URL::site('userrequest/request_status_detail/' . $enc_request_id) . '/ad" > View Detail </a> ';
                        $login_user = Auth::instance()->get_user();
                        $permission = Helpers_Utilities::get_user_permission($login_user->id);
                        /* if (($permission == 1 || $permission == 2) && ($request_type_id != 8) && ($status == 2)) {
                          $member_name_link .= '<a class="btn btn-block btn-warning btn-xs" style="background-color:#ff82b6" href="' . URL::site('userrequest/request_reread_status_detail/' . $enc_request_id) . '" > Req.Reread </a>';
                          } */
                        //Add user ID of Iqra and Neelam
                        $userslist = [842, 137, 2031, 1761, 2597, 2603];
                        if (($permission == 1 || $permission == 5) && (in_array($login_user->id, $userslist))) {
//                            $member_name_link .= '<a class="btn btn-block btn-primary btn-xs"  href="#" onclick="UpdateRequestStatus(' . $item['request_id'] . ',' . $request_reference_id . ',' . $item['status'] . ',' . $item['processing_index'] . ')"> Update Status  </a>';
                            $enc_request_id = "'" . $enc_request_id . "'";
                            $member_name_link .= '<a  style="display: none;" class="btn btn-block btn-danger btn-xs"  href="#" onclick="deleteuserrequest(' . $enc_request_id . ')"> Delete Request  </a>';
                        }
                        $row = array(
                            $user_name . $username,
//                      $user_role_name,
                            $user_request,
                            $company_name,
                            $reason,
                            $created_at,
                            $status_flag,
//                            $reply_flag,
//                            $system_status_flag,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_ajaxadminsentrequeststatus',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'ajax_request'
            );
            error_log("[" . date('c') . "] action_ajaxadminsentrequeststatus exception: " . $ex->getMessage());
        }
    }

    //ajax call for data requests
    public function action_ajaxadminsentrequests()
    {
        try {
            $this->auto_rednder = false;
            $reply_flag = '';
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('request_status_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_AdminRequest();
                $rows_count = $data->admin_sent_request_single_user($post, 'true');
                $profiles = $data->admin_sent_request_single_user($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $request_type_id = (isset($item['user_request_type_id'])) ? $item['user_request_type_id'] : 0;
                        $request_id = (isset($item['request_id'])) ? $item['request_id'] : 'NA';
                        $request_reference_id = (isset($item['reference_id'])) ? $item['reference_id'] : 0;
                        $user_id = (isset($item['user_id'])) ? $item['user_id'] : 0;
                        $user_role_name = (isset($user_id)) ? Helpers_Utilities::get_user_role_name($user_id) : 'N/A';
                        $user_name = (isset($item['user_id'])) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $username = (isset($item['user_id'])) ? '<br><span><b>' . Helpers_Utilities::get_username($item['user_id']) . '</b></span>' : '--';
                        $user_request = (isset($item['email_type_name'])) ? $item['email_type_name'] : 'NA';
                        $user_request .= '<span><br><b>ID:';
                        $user_request .= (isset($item['request_id'])) ? $item['request_id'] : 'NA';
                        $user_request .= ' Ref#' . $request_reference_id;
                        $user_request .= '</b></span>';
                        $company_name = '<span><b>';
                        $company_name .= (isset($item['company_name']) && !empty($item['company_name'])) ? Helpers_Utilities::get_companies_data($item['company_name'])->company_name : '--';
                        $company_name .= '</b></span><br><span>';
                        $company_name .= (isset($item['requested_value']) && !empty($item['requested_value'])) ? $item['requested_value'] : '--';
                        $company_name .= '</span>';
                        $requested_value = (isset($item['requested_value'])) ? $item['requested_value'] : 'NA';
                        $reply = (isset($item['reply'])) ? $item['reply'] : 0;
                        $reason = (isset($item['reason'])) ? $item['reason'] : 'NA';

                        $concerned_person_id = (isset($item['concerned_person_id'])) ? $item['concerned_person_id'] : 'NA';
                        $enc_request_id = trim(Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt'));
                        $created_at = (isset($item['created_at'])) ? $item['created_at'] : 'NA';
                        $status = (isset($item['status'])) ? $item['status'] : 0;
                        $system_status_flag = '';
                        if ($request_type_id == 8) {
                            switch ($status) {
                                case 0:
                                    $status_flag = '<span class="label label-info">Request In Queue</span>';
                                    break;
                                case 1:
                                    $status_flag = '<span class="label label-success">Request Send</span>';
                                    break;
                                case 2:
                                    $status_flag = '<span class="label label-success">Request Completed</span>';
                                    break;
                                case 3:
                                    $status_flag = '<span class="label label-danger">Request Error</span>';
                                    break;
                            }
                            if ($status == 1) {
                                $system_status_flag = '<span class="badge badge-pill badge-warning">Response waiting</span>';
                            } else {
                                $system_status_flag = '<span class="badge badge-pill badge-success">Processed</span>';
                            }

                            //this part will run if request is other then nadra
                        } else {

                            switch ($status) {
                                case 0:
                                    $status_flag = '<span class="label label-info">Request in Queue</span>';
                                    break;
                                case 1:
                                    $status_flag = '<span class="label label-primary">Request Send</span>';
                                    break;
                                case 2:
                                    if ($request_type_id == 2 && $item['processing_index'] == 6) {
                                        $recievedfilepath = "'" . trim($item['received_file_path']) . "'";
                                        $recievedbody = str_replace(PHP_EOL, ' ', strip_tags(trim($item['received_body'])));
                                        $recievedbody = "'" . $recievedbody . "'";

                                        $status_flag = '<span class="label label-success">Email Received</span>';
                                        $status_flag .= '<span class="badge badge-pill badge-success"><a href="#" onclick="fullparseimeicdr(' . $request_id . ',' . $recievedfilepath . ',' . $recievedbody . ',' . $requested_value . ')" style="color: white">Parse</a></span>';
                                    } else {
                                        $status_flag = '<span class="label label-success">Email Received</span>';
                                    }

                                    break;
                                case 3:
                                    $status_flag = '<span class="label label-danger">Email Sending Error</span>';
                                    break;
                                case 4:
                                    $status_flag = '<span class="label label-warning">Request Rejected</span>';
                                    break;
                            }
                            $reply_flag = '<span class="badge badge-pill badge-warning">Pending</span>';
                            //$system_status_flag = '';
                            if ($status == 2) {
                                // system status for FO sent/pending
                                if ($reply == 1) {
                                    $reply_flag = '<span class="badge badge-pill badge-success">Sent</span>';
                                } else {
                                    $reply_flag = '<span class="badge badge-pill badge-warning">Pending</span>';
                                }
                                //system processing status
                                $system_status = (isset($item['processing_index'])) ? $item['processing_index'] : 0;
                                switch ($system_status) {
                                    case 1:
                                        $system_status_flag = '<span class="badge badge-pill badge-danger">Format Error</span>';
                                        break;
                                    case 2:
                                        $system_status_flag = '<span class="badge badge-pill badge-secondary">No Data</span>';
                                        break;
                                    case 3:
                                        $system_status_flag = '<span class="badge badge-pill badge-danger">Parsing Error</span>';
                                        break;
                                    case 4:
                                        $system_status_flag = '<span class="badge badge-pill badge-info">Waiting Parsing</span>';
                                        break;
                                    case 5:
                                        $system_status_flag = '<span class="badge badge-pill badge-success">Parsing completed</span>';
                                        if ($request_type_id == 2) {
                                            $imei_link = (isset($item['requested_value']) && !empty($item['requested_value'])) ? $item['requested_value'] : '--';
                                            $system_status_flag .= '<a href="' . URL::site('User/upload_against_imei') . '?imei=' . $imei_link . '" <span class="badge badge-pill badge-success">Check IMEI</span></a>';
                                        }
                                        break;
                                    case 6:
                                        $system_status_flag = '<span class="badge badge-pill badge-primary">Partially Parsed</span>';
                                        break;
                                    case 7:
                                        $system_status_flag = '<span class="badge badge-pill badge-success">Marked Complete</span>';
                                        break;
                                }
                            } else {
                                $system_status_flag = '<span class="badge badge-pill badge-warning">Response waiting</span>';
                            }
                        }
                        $member_name_link = '<a class="btn btn-block btn-info btn-xs" href="' . URL::site('userrequest/request_status_detail/' . $enc_request_id) . '/ad" > View Detail </a> ';
                        $login_user = Auth::instance()->get_user();
                        $permission = Helpers_Utilities::get_user_permission($login_user->id);
                        /* if (($permission == 1 || $permission == 2) && ($request_type_id != 8) && ($status == 2)) {
                          $member_name_link .= '<a class="btn btn-block btn-warning btn-xs" style="background-color:#ff82b6" href="' . URL::site('userrequest/request_reread_status_detail/' . $enc_request_id) . '" > Req.Reread </a>';
                          } */
                        //Add user ID of Iqra and Neelam
                        $userslist = [842, 137, 2031, 1761, 2597, 2603];
                        if (($permission == 1 || $permission == 5) && (in_array($login_user->id, $userslist))) {
//                            $member_name_link .= '<a class="btn btn-block btn-primary btn-xs"  href="#" onclick="UpdateRequestStatus(' . $item['request_id'] . ',' . $request_reference_id . ',' . $item['status'] . ',' . $item['processing_index'] . ')"> Update Status  </a>';
                            $member_name_link .= '<a  style="display: none;" class="btn btn-block btn-danger btn-xs"  href="#" onclick="deleteuserrequest(' . $enc_request_id . ',' . $enc_request_id . ')"> Delete Request  </a>';
                        }
                        $row = array(
                            $user_name . $username,
//                      $user_role_name,
                            $user_request,
                            $company_name,
                            $reason,
                            $created_at,
                            $status_flag,
//                            $reply_flag,
//                            $system_status_flag,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_ajaxadminsentrequests',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'ajax_request'
            );
            error_log("[" . date('c') . "] action_ajaxadminsentrequests exception: " . $ex->getMessage());
        }
    } //ajax call for data requests

    public function action_ajaxusersentrequests()
    {
        try {
            $this->auto_rednder = false;
            $reply_flag = '';
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('request_status_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_AdminRequest();
                $rows_count = $data->user_sent_request_single_user($post, 'true');
                $profiles = $data->user_sent_request_single_user($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $request_type_id = (isset($item['user_request_type_id'])) ? $item['user_request_type_id'] : 0;
                        $request_id = (isset($item['request_id'])) ? $item['request_id'] : 'NA';
                        $request_reference_id = (isset($item['reference_id'])) ? $item['reference_id'] : 0;
                        $user_id = (isset($item['user_id'])) ? $item['user_id'] : 0;
                        $user_role_name = (isset($user_id)) ? Helpers_Utilities::get_user_role_name($user_id) : 'N/A';
                        $user_name = (isset($item['user_id'])) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $username = (isset($item['user_id'])) ? '<br><span><b>' . Helpers_Utilities::get_username($item['user_id']) . '</b></span>' : '--';
                        $user_request = (isset($item['email_type_name'])) ? $item['email_type_name'] : 'NA';
                        $user_request .= '<span><br><b>ID:';
                        $user_request .= (isset($item['request_id'])) ? $item['request_id'] : 'NA';
                        $user_request .= ' Ref#' . $request_reference_id;
                        $user_request .= '</b></span>';
                        $company_name = '<span><b>';
                        $company_name .= (isset($item['company_name']) && !empty($item['company_name'])) ? Helpers_Utilities::get_companies_data($item['company_name'])->company_name : '--';
                        $company_name .= '</b></span><br><span>';
                        $company_name .= (isset($item['requested_value']) && !empty($item['requested_value'])) ? $item['requested_value'] : '--';
                        $company_name .= '</span>';
                        $requested_value = (isset($item['requested_value'])) ? $item['requested_value'] : 'NA';
                        $reply = (isset($item['reply'])) ? $item['reply'] : 0;
                        $reason = (isset($item['reason'])) ? $item['reason'] : 'NA';

                        $concerned_person_id = (isset($item['concerned_person_id'])) ? $item['concerned_person_id'] : 'NA';
                        $enc_request_id = trim(Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt'));
                        $created_at = (isset($item['created_at'])) ? $item['created_at'] : 'NA';
                        $status = (isset($item['status'])) ? $item['status'] : 0;
                        $system_status_flag = '';
                        if ($request_type_id == 8) {
                            switch ($status) {
                                case 0:
                                    $status_flag = '<span class="label label-info">Request In Queue</span>';
                                    break;
                                case 1:
                                    $status_flag = '<span class="label label-success">Request Send</span>';
                                    break;
                                case 2:
                                    $status_flag = '<span class="label label-success">Request Completed</span>';
                                    break;
                                case 3:
                                    $status_flag = '<span class="label label-danger">Request Error</span>';
                                    break;
                            }
                            if ($status == 1) {
                                $system_status_flag = '<span class="badge badge-pill badge-warning">Response waiting</span>';
                            } else {
                                $system_status_flag = '<span class="badge badge-pill badge-success">Processed</span>';
                            }

                            //this part will run if request is other then nadra
                        } else {

                            switch ($status) {
                                case 0:
                                    $status_flag = '<span class="label label-info">Request in Queue</span>';
                                    break;
                                case 1:
                                    $status_flag = '<span class="label label-primary">Request Send</span>';
                                    break;
                                case 2:
                                    if ($request_type_id == 2 && $item['processing_index'] == 6) {
                                        $recievedfilepath = "'" . trim($item['received_file_path']) . "'";
                                        $recievedbody = str_replace(PHP_EOL, ' ', strip_tags(trim($item['received_body'])));
                                        $recievedbody = "'" . $recievedbody . "'";

                                        $status_flag = '<span class="label label-success">Email Received</span>';
                                        $status_flag .= '<span class="badge badge-pill badge-success"><a href="#" onclick="fullparseimeicdr(' . $request_id . ',' . $recievedfilepath . ',' . $recievedbody . ',' . $requested_value . ')" style="color: white">Parse</a></span>';
                                    } else {
                                        $status_flag = '<span class="label label-success">Email Received</span>';
                                    }

                                    break;
                                case 3:
                                    $status_flag = '<span class="label label-danger">Email Sending Error</span>';
                                    break;
                                case 4:
                                    $status_flag = '<span class="label label-warning">Request Rejected</span>';
                                    break;
                            }
                            $reply_flag = '<span class="badge badge-pill badge-warning">Pending</span>';
                            //$system_status_flag = '';
                            if ($status == 2) {
                                // system status for FO sent/pending
                                if ($reply == 1) {
                                    $reply_flag = '<span class="badge badge-pill badge-success">Sent</span>';
                                } else {
                                    $reply_flag = '<span class="badge badge-pill badge-warning">Pending</span>';
                                }
                                //system processing status
                                $system_status = (isset($item['processing_index'])) ? $item['processing_index'] : 0;
                                switch ($system_status) {
                                    case 1:
                                        $system_status_flag = '<span class="badge badge-pill badge-danger">Format Error</span>';
                                        break;
                                    case 2:
                                        $system_status_flag = '<span class="badge badge-pill badge-secondary">No Data</span>';
                                        break;
                                    case 3:
                                        $system_status_flag = '<span class="badge badge-pill badge-danger">Parsing Error</span>';
                                        break;
                                    case 4:
                                        $system_status_flag = '<span class="badge badge-pill badge-info">Waiting Parsing</span>';
                                        break;
                                    case 5:
                                        $system_status_flag = '<span class="badge badge-pill badge-success">Parsing completed</span>';
                                        if ($request_type_id == 2) {
                                            $imei_link = (isset($item['requested_value']) && !empty($item['requested_value'])) ? $item['requested_value'] : '--';
                                            $system_status_flag .= '<a href="' . URL::site('User/upload_against_imei') . '?imei=' . $imei_link . '" <span class="badge badge-pill badge-success">Check IMEI</span></a>';
                                        }
                                        break;
                                    case 6:
                                        $system_status_flag = '<span class="badge badge-pill badge-primary">Partially Parsed</span>';
                                        break;
                                    case 7:
                                        $system_status_flag = '<span class="badge badge-pill badge-success">Marked Complete</span>';
                                        break;
                                }
                            } else {
                                $system_status_flag = '<span class="badge badge-pill badge-warning">Response waiting</span>';
                            }
                        }
                        $member_name_link = '<a class="btn btn-block btn-info btn-xs" href="' . URL::site('userrequest/request_status_detail/' . $enc_request_id) . '/ad" > View Detail </a> ';
                        $login_user = Auth::instance()->get_user();
                        $permission = Helpers_Utilities::get_user_permission($login_user->id);
                        /* if (($permission == 1 || $permission == 2) && ($request_type_id != 8) && ($status == 2)) {
                          $member_name_link .= '<a class="btn btn-block btn-warning btn-xs" style="background-color:#ff82b6" href="' . URL::site('userrequest/request_reread_status_detail/' . $enc_request_id) . '" > Req.Reread </a>';
                          } */
                        //Add user ID of Iqra and Neelam
                        $userslist = [842, 137, 2031, 1761, 2603];
                        if (($permission == 1 || $permission == 5) && (in_array($login_user->id, $userslist))) {
//                            $member_name_link .= '<a class="btn btn-block btn-primary btn-xs"  href="#" onclick="UpdateRequestStatus(' . $item['request_id'] . ',' . $request_reference_id . ',' . $item['status'] . ',' . $item['processing_index'] . ')"> Update Status  </a>';
                            $member_name_link .= '<a  style="display: none;" class="btn btn-block btn-danger btn-xs"  href="#" onclick="deleteuserrequest(' . $enc_request_id . ',' . $enc_request_id . ')"> Delete Request  </a>';
                        }
                        $row = array(
                            $user_name . $username,
//                      $user_role_name,
                            $user_request,
                            $company_name,
                            $reason,
                            $created_at,
                            $status_flag,
//                            $reply_flag,
//                            $system_status_flag,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_ajaxusersentrequests',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'ajax_request'
            );
            error_log("[" . date('c') . "] action_ajaxusersentrequests exception: " . $ex->getMessage());
        }
    }

    //ajax call for data
    public function action_ajaxadminsentrequestcount()
    {
        try {
            $this->auto_rednder = false;
            $reply_flag = '';
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('request_status_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_AdminRequest();
                $rows_count = $data->admin_sent_request_count($post, 'true');
                $profiles = $data->admin_sent_request_count($post, 'false');
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $count = (isset($item['count'])) ? $item['count'] : 0;
                        $date = (isset($item['created_at'])) ? $item['created_at'] : 0;
                        $request_by = (isset($item['rqtbyname'])) ? $item['rqtbyname'] : 'NA';
                        $user_request_type_id = (isset($item['user_request_type_id'])) ? $item['user_request_type_id'] : 0;

                        $name_encrypted = Helpers_Utilities::encrypted_key($request_by, "encrypt");
                        $dquery = '';
                        if (!empty($post['startdate'])) {
                            $start_date = date("Y-m-d", strtotime($post['startdate']));
                            $dquery .= '?fr=' . $start_date;
                        }
                        if (!empty($post['enddate'])) {
                            $end_date = date("Y-m-d", strtotime($post['enddate']));
                            $dquery .= '&to=' . $end_date;
                        }
                        //  $req_id='';
                        if (!empty($user_request_type_id)) {
                            $dquery .= '/&req=' . $user_request_type_id;
                        }

                        $html = '<a class="btn btn-small action" href="' . URL::site('adminrequest/requests/' . $name_encrypted . $dquery) . '"><i class="fa fa-folder-open-o"></i> View Requests</a>';


                        $row = array(
                            $count,
                            $request_by,
                            //$date,
                            $html

                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_ajaxadminsentrequestcount',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'ajax_request'
            );
            error_log("[" . date('c') . "] action_ajaxadminsentrequestcount exception: " . $ex->getMessage());
        }
    }

    //ajax call for data
    public function action_ajaxuserrequestcount()
    {
        try {
            $this->auto_rednder = false;
            $reply_flag = '';
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('request_status_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_AdminRequest();
                $rows_count = $data->user_request_count($post, 'true');
                $profiles = $data->user_request_count($post, 'false');
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
//                        echo '<pre>';
//                        print_r($item);
//                        exit();

                        $count = (isset($item['count'])) ? $item['count'] : 0;
                        $user_id = (isset($item['user_id'])) ? $item['user_id'] : 0;

                        $u_name = Helpers_Utilities::get_user_name($user_id);
                        $date = (isset($item['created_at'])) ? $item['created_at'] : 0;
                        $request_by = (isset($item['rqtbyname'])) ? $item['rqtbyname'] : 'NA';
                        $user_request_type_id = (isset($item['user_request_type_id'])) ? $item['user_request_type_id'] : 0;

                        $uid_encrypted = Helpers_Utilities::encrypted_key($user_id, "encrypt");
                        $dquery = '';
                        if (!empty($post['startdate'])) {
                            $start_date = date("Y-m-d", strtotime($post['startdate']));
                            $dquery .= '?fr=' . $start_date;
                        }
                        if (!empty($post['enddate'])) {
                            $end_date = date("Y-m-d", strtotime($post['enddate']));
                            $dquery .= '&to=' . $end_date;
                        }
                        //  $req_id='';
                        if (!empty($user_request_type_id)) {
                            $dquery .= '/&req=' . $user_request_type_id;
                        }

                        $html = '<a class="btn btn-small action" href="' . URL::site('adminrequest/u_requests/' . $uid_encrypted . $dquery) . '"><i class="fa fa-folder-open-o"></i> View Requests</a>';


                        $row = array(
                            $count,
                            $u_name,
                            //$date,
                            $html

                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_ajaxuserrequestcount',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'ajax_request'
            );
            error_log("[" . date('c') . "] action_ajaxuserrequestcount exception: " . $ex->getMessage());
        }
    }

    /* Delete record form blocked number */

    public function action_deleteuserrequest()
    {
        try {
            if (Auth::instance()->logged_in()) {
                $user_obj = Auth::instance()->get_user();
                $login_user_id = $user_obj->id;
                $permission = Helpers_Utilities::get_user_permission($login_user_id);
                if ($permission == 1) {
                    $request_id_encrypted = $this->request->param('id');
                    $request_id_encrypted = Helpers_Utilities::remove_injection($request_id_encrypted);
                    $request_id = Helpers_Utilities::encrypted_key($request_id_encrypted, 'decrypt');
                    // print_r($blocked_id); exit;
                    $user = new Model_AdminRequest();

                    $result = !empty($request_id) ? $user->delete_user_request($request_id, $login_user_id) : '';
                    //print_r($result); exit;
                    echo 1;
                } else {
                    $this->redirect();
                }
            } else {
                return 0;
            }
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_deleteuserrequest',
                $ex->getMessage(),
                [
                    'request_id_encrypted' => $request_id_encrypted ?? 'N/A',
                    'login_user_id' => $login_user_id ?? 'N/A'
                ],
                $ex->getTraceAsString(),
                'exception',
                'delete_request'
            );
            error_log("[" . date('c') . "] action_deleteuserrequest exception: " . $ex->getMessage());
            echo json_encode(-2);
        }
    }

    public function action_requests()
    {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);

            $name = $this->request->param('id');

            $name = Helpers_Utilities::remove_injection($name);
            $sdate = !empty($_GET['fr']) ? $_GET['fr'] : '';
            $edate = !empty($_GET['to']) ? $_GET['to'] : '';
            $u_req_id = !empty($_GET['req']) ? $_GET['req'] : '';
//            $user_req_id= $rest = substr($edate, -1);
            $name = Helpers_Utilities::encrypted_key($name, "decrypt");

            //if (!empty($name)) {
            $data['name'] = $name;
            $data['to'] = $edate;
            $data['fr'] = $sdate;

            $this->template->content = View::factory('templates/user/requests')
                ->bind('data', $_GET)
                ->bind('name', $name)
                ->bind('reqtype', $u_req_id);
            /*} else {
                $this->template->content = View::factory('templates/user/access_denied');
            }*/
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_requests',
                $ex->getMessage(),
                [
                    'name' => $name ?? 'N/A',
                    'request_type' => $u_req_id ?? 'N/A'
                ],
                $ex->getTraceAsString(),
                'exception',
                'page_load'
            );
            error_log("[" . date('c') . "] action_requests exception: " . $ex->getMessage());
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    public function action_u_requests()
    {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);

            $name = $this->request->param('id');
            $name = Helpers_Utilities::remove_injection($name);
            $sdate = !empty($_GET['fr']) ? $_GET['fr'] : '';
            $edate = !empty($_GET['to']) ? $_GET['to'] : '';
            $u_req_id = !empty($_GET['req']) ? $_GET['req'] : '';
//            $user_req_id= $rest = substr($edate, -1);
            $name = Helpers_Utilities::encrypted_key($name, "decrypt");

            //if (!empty($name)) {
            $data['name'] = $name;
            $data['to'] = $edate;
            $data['fr'] = $sdate;

            $this->template->content = View::factory('templates/user/u_requests')
                ->bind('data', $_GET)
                ->bind('id', $name)
                ->bind('reqtype', $u_req_id);
            /*} else {
                $this->template->content = View::factory('templates/user/access_denied');
            }*/
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_u_requests',
                $ex->getMessage(),
                [
                    'user_id' => $name ?? 'N/A',
                    'request_type' => $u_req_id ?? 'N/A'
                ],
                $ex->getTraceAsString(),
                'exception',
                'page_load'
            );
            error_log("[" . date('c') . "] action_u_requests exception: " . $ex->getMessage());
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    //ajax call for nadra request data
    public function action_ajax_nadra_request_status()
    {
        try {
            $this->auto_rednder = false;
            $reply_flag = '';
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('request_status_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_AdminRequest();
                $rows_count = $data->admin_nadra_request_sent_status($post, 'true');
                $profiles = $data->admin_nadra_request_sent_status($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $user_id = (isset($item['user_id'])) ? $item['user_id'] : 0;
                        $user_name = (isset($item['user_id'])) ? Helpers_Utilities::get_user_name($user_id) : 'NA';
                        $requested_by = (isset($item['rqtbyname'])) ? $item['rqtbyname'] : 'NA';
                        $region_id = (isset($item['region_id'])) ? $item['region_id'] : 0;
                        $region = !empty($region_id) ? Helpers_Utilities::get_region($region_id) : 'N/A';

                        $cnic = (isset($item['cnic'])) ? $item['cnic'] : 'NA';
                        $reason = (isset($item['reason'])) ? $item['reason'] : 'NA';
                        $date = (isset($item['request_date'])) ? $item['request_date'] : 'NA';
                        $status = (isset($item['status'])) ? $item['status'] : 0;
                        if ($status == 1) {
                            $status = '<span class="badge badge-pill badge-success">Processed </span>';
                        } else {
                            $status = '<span class="badge badge-pill badge-primary">Pending </span>';
                        }

                        $member_name_link = '<a class="btn btn-block btn-primary btn-xs"  href="#" onclick="UpdateRequestStatus(' . $item['request_id'] . ',' . $item['status'] . ')"> Update Status  </a>';
                        // $member_name_link = '<a class="btn btn-block btn-primary btn-xs"  href="#" onclick="UpdateRequestStatus('. $item['request_id'] .',' . $cnic . ',' . $status . ')"> Update Status  </a>';

                        $row = array(
                            $user_name,
                            $requested_by,
                            $region,
                            $cnic,
                            $reason,
                            $date,
                            $status,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_ajax_nadra_request_status',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'ajax_request'
            );
            error_log("[" . date('c') . "] action_ajax_nadra_request_status exception: " . $ex->getMessage());
        }
    }

    public function action_ajax_familytree_request_status()
    {
        try {
            $this->auto_rednder = false;
            $reply_flag = '';
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('request_status_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_AdminRequest();
                $rows_count = $data->admin_familtytree_request_sent_status($post, 'true');
                $profiles = $data->admin_familtytree_request_sent_status($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $user_id = (isset($item['user_id'])) ? $item['user_id'] : 0;
                        $user_name = (isset($item['user_id'])) ? Helpers_Utilities::get_user_name($user_id) : 'NA';
                        $requested_by = (isset($item['rqtbyname'])) ? $item['rqtbyname'] : 'NA';
                        $region_id = (isset($item['region_id'])) ? $item['region_id'] : 0;
                        $region = !empty($region_id) ? Helpers_Utilities::get_region($region_id) : 'N/A';

                        $cnic = (isset($item['cnic'])) ? $item['cnic'] : 'NA';
                        $reason = (isset($item['reason'])) ? $item['reason'] : 'NA';
                        $date = (isset($item['request_date'])) ? $item['request_date'] : 'NA';
                        $status = (isset($item['status'])) ? $item['status'] : 0;
                        if ($status == 1) {
                            $status = '<span class="badge badge-pill badge-success">Processed </span>';
                        } else {
                            $status = '<span class="badge badge-pill badge-primary">Pending </span>';
                        }

                        $member_name_link = '<a class="btn btn-block btn-primary btn-xs"  href="#" onclick="UpdateRequestStatus(' . $item['request_id'] . ',' . $item['status'] . ')"> Update Status  </a>';
                        // $member_name_link = '<a class="btn btn-block btn-primary btn-xs"  href="#" onclick="UpdateRequestStatus('. $item['request_id'] .',' . $cnic . ',' . $status . ')"> Update Status  </a>';

                        $row = array(
                            $user_name,
                            $requested_by,
                            $region,
                            $cnic,
                            $reason,
                            $date,
                            $status,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_ajax_familytree_request_status',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'ajax_request'
            );
            error_log("[" . date('c') . "] action_ajax_familytree_request_status exception: " . $ex->getMessage());
        }
    }

    //ajax call for nadra request data
    public function action_ajax_travel_request_status()
    {
        try {
            $this->auto_rednder = false;
            $reply_flag = '';
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('request_status_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_AdminRequest();
                $rows_count = $data->admin_travel_request_sent_status($post, 'true');
                $profiles = $data->admin_travel_request_sent_status($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $user_id = (isset($item['user_id'])) ? $item['user_id'] : 0;
                        $user_name = (isset($item['user_id'])) ? Helpers_Utilities::get_user_name($user_id) : 'NA';
                        $requested_by = (isset($item['rqtbyname'])) ? $item['rqtbyname'] : 'NA';
                        $region_id = (isset($item['region_id'])) ? $item['region_id'] : 0;
                        $region = !empty($region_id) ? Helpers_Utilities::get_region($region_id) : 'N/A';

                        $cnic = (isset($item['cnic'])) ? $item['cnic'] : 'NA';
                        $passport = (isset($item['passport'])) ? $item['passport'] : 'NA';
                        $reason = (isset($item['reason'])) ? $item['reason'] : 'NA';
                        $date = (isset($item['request_date'])) ? $item['request_date'] : 'NA';
                        $status = (isset($item['status'])) ? $item['status'] : 0;
                        if ($status == 1) {
                            $status = '<span class="badge badge-pill badge-success">Processed </span>';
                        } else {
                            $status = '<span class="badge badge-pill badge-primary">Pending </span>';
                        }

                        $member_name_link = '<a class="btn btn-block btn-primary btn-xs"  href="#" onclick="UpdateRequestStatus(' . $item['request_id'] . ',' . $item['status'] . ')"> Update Status  </a>';
                        // $member_name_link = '<a class="btn btn-block btn-primary btn-xs"  href="#" onclick="UpdateRequestStatus('. $item['request_id'] .',' . $cnic . ',' . $status . ')"> Update Status  </a>';
                        $file_name = (!empty($item['file_name'])) ?
                            '<a target="_blank" style="margin-top: 25px" target="" href=" ' . URL::base() . '/' . $item['file_name'] . '" class="btn btn-danger pull-right" >Download File</a>'
                            : '';
                        $row = array(
                            $user_name,
                            $requested_by,
                            $region,
                            $cnic,
                            $passport,
                            $reason,
                            $date,
                            $file_name,
                            $status,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_ajax_travel_request_status',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'ajax_request'
            );
            error_log("[" . date('c') . "] action_ajax_travel_request_status exception: " . $ex->getMessage());
        }
    }

    //update user request status
    public function action_update_nadra_request_status()
    {
        //
//        try {
        $post = $this->request->post();
        $post = Helpers_Utilities::remove_injection($post);
        $this->auto_render = FALSE;
        $user_id = Auth::instance()->get_user();
        $permission = Helpers_Utilities::get_user_permission($user_id->id);
        if ($permission == 1 || $permission == 5 || $permission == 2) {
            $request_id = $post["request_id"];
            //$request_name = $post["request_name"];
            $processing_index = $post["processing_index"];
            //print_r($post); exit;
            $update = 0;
            if (!empty($request_id)) {

                $update = Model_AdminRequest::update_nadra_request_status($request_id, $processing_index);
            }
            return $update;
        } else {
            $this->redirect();
        }
//        } catch (Exception $ex) {
//            echo json_encode(-2);
//        }
    }

    public function action_update_familytree_request_status()
    {
        //
//        try {
        $post = $this->request->post();
        $post = Helpers_Utilities::remove_injection($post);
        $this->auto_render = FALSE;
        $user_id = Auth::instance()->get_user();
        $permission = Helpers_Utilities::get_user_permission($user_id->id);
        if ($permission == 1 || $permission == 5 || $permission == 2) {
            $request_id = $post["request_id"];
            //$request_name = $post["request_name"];
            $processing_index = $post["processing_index"];
            //print_r($post); exit;
            $update = 0;
            if (!empty($request_id)) {

                $update = Model_AdminRequest::update_familytree_request_status($request_id, $processing_index);
            }
            return $update;
        } else {
            $this->redirect();
        }
//        } catch (Exception $ex) {
//            echo json_encode(-2);
//        }
    }

    //update user request status
    public function action_update_travel_request_status()
    {
        //
//        try {
        $post = $this->request->post();
        $post = Helpers_Utilities::remove_injection($post);
        $this->auto_render = FALSE;
        $user_id = Auth::instance()->get_user();
        $permission = Helpers_Utilities::get_user_permission($user_id->id);
        if ($permission == 1 || $permission == 5 || $permission == 2) {
            $request_id = $post["request_id"];
            //$request_name = $post["request_name"];
            $processing_index = $post["processing_index"];
            //print_r($post); exit;
            $update = 0;
            if (!empty($request_id)) {

                $update = Model_AdminRequest::update_travel_request_status($request_id, $processing_index);
            }
            return $update;
        } else {
            $this->redirect();
        }
//        } catch (Exception $ex) {
//            echo json_encode(-2);
//        }
    }


    // Autocomplete: suggests existing rqtbyname values across all admin
    // request tables (admin_request, admin_nadra_request,
    // admin_familytree_request, admin_travel_request).  The previous
    // implementation only queried admin_request, which is why the dropdown
    // was returning at most ~2 distinct names — names entered through the
    // NADRA / Family Tree / Travel forms were never offered as suggestions.
    public function action_autocomplete()
    {
        if (!Auth::instance()->logged_in()) {
            return;
        }
        if (!isset($_POST['rqtbyname'])) {
            return;
        }

        $raw = trim((string) $_POST['rqtbyname']);
        if ($raw === '') {
            echo '<ul class="list-unstyled"></ul>';
            return;
        }

        $DB = Database::instance();

        // Sanitize: remove injection markers, then escape LIKE wildcards
        // ('%' and '_') so a user typing them gets a literal-character match.
        // Using '|' as the LIKE escape character (instead of the default
        // backslash) avoids the multi-layer escaping headache where PHP
        // collapses \\\\ to \\ and MySQL collapses \\ to \ — different
        // tools / logs render those differently and ESCAPE only accepts
        // a single byte. '|' has no special meaning in PHP or MySQL strings,
        // so what we write is what MySQL gets.
        $clean = Helpers_Utilities::remove_injection($raw);
        $like  = str_replace(array('|', '%', '_'), array('||', '|%', '|_'), $clean);
        $like_q = $DB->escape('%' . $like . '%');

        // Pull suggestions from every admin request table that carries
        // rqtbyname, dedupe across them, sort, and cap to 20 rows so the
        // dropdown stays usable.
        $sql = "
            SELECT name FROM (
                SELECT rqtbyname AS name FROM admin_request            WHERE rqtbyname LIKE {$like_q} ESCAPE '|'
                UNION
                SELECT rqtbyname AS name FROM admin_nadra_request      WHERE rqtbyname LIKE {$like_q} ESCAPE '|'
                UNION
                SELECT rqtbyname AS name FROM admin_familytree_request WHERE rqtbyname LIKE {$like_q} ESCAPE '|'
                UNION
                SELECT rqtbyname AS name FROM admin_travel_request     WHERE rqtbyname LIKE {$like_q} ESCAPE '|'
            ) AS combined
            WHERE name IS NOT NULL AND TRIM(name) != ''
            GROUP BY name
            ORDER BY name ASC
            LIMIT 20
        ";

        $result = DB::query(Database::SELECT, $sql)->as_object()->execute();

        $output = '<ul class="list-unstyled">';
        if ($result->count() > 0) {
            foreach ($result as $rs) {
                $output .= '<li>' . htmlspecialchars(ucwords($rs->name), ENT_QUOTES, 'UTF-8') . '</li>';
            }
        } else {
            $output .= '<li> Requested By not Found</li>';
        }
        $output .= '</ul>';
        echo $output;
    }

    // admin nadra request send
    public function action_autocomplete_nadra()
    {
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();

            if (isset($_POST['rqtbyname'])) {

                $output = "";
                $city = $_POST['rqtbyname'];
                $sql = "SELECT rqtbyname 
                         from admin_nadra_request where rqtbyname like '%$city%' group by rqtbyname";
                $result = DB::query(Database::SELECT, $sql)->as_object()->execute();
                $output = '<ul class="list-unstyled">';
                if ($result->count() > 0) {
                    foreach ($result as $rs) {
                        $output .= '<li>' . ucwords($rs->rqtbyname) . '</li>';
                    }
                } else {
                    $output .= '<li> Requested By not Found</li>';
                }

                $output .= '</ul>';
                echo $output;
            }

        }

    }

    /**
     * AJAX endpoint that returns the email template subject + body and the
     * default company email for a (request_type, company_name) pair.
     *
     * Used by the admin custom request form to auto-populate the readonly
     * "Email Subject" and "Custom Email Address" fields when either
     * Request Type or Company Name changes — mirrors what the single-request
     * server-side flow already does at submit time.
     *
     * Request: POST {request_type, company_name}
     * Response: {subject, body, email} (each field may be '' when no match).
     */

    /**
     * Stream a tiny sample CSV the admin can fill in their spreadsheet
     * tool and re-upload via the Bulk Upload control on the custom form.
     *
     * GET ?type=mobile|cnic|imei
     */
    public function action_sample_csv()
    {
        $this->auto_render = false;

        $type = isset($_GET['type']) ? strtolower((string) $_GET['type']) : '';
        $samples = array(
            'mobile' => array(
                'header' => 'Mobile Number',
                'rows'   => array('3001234567', '3007654321', '3211234567'),
                'file'   => 'mobile_numbers_sample.csv',
            ),
            'cnic' => array(
                'header' => 'CNIC Number',
                'rows'   => array('1234512345671', '1410123456789', '1740112345671'),
                'file'   => 'cnic_numbers_sample.csv',
            ),
            'imei' => array(
                'header' => 'IMEI Number',
                'rows'   => array('123456789012345', '987654321098765', '356938035643809'),
                'file'   => 'imei_numbers_sample.csv',
            ),
        );

        if (!isset($samples[$type])) {
            header('HTTP/1.1 400 Bad Request');
            header('Content-Type: text/plain');
            echo 'Unknown sample type. Use type=mobile|cnic|imei.';
            return;
        }

        $sample = $samples[$type];
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $sample['file'] . '"');
        header('Cache-Control: no-store');

        $fh = fopen('php://output', 'w');
        // BOM so Excel auto-detects UTF-8.
        fwrite($fh, "\xEF\xBB\xBF");
        fputcsv($fh, array($sample['header']));
        foreach ($sample['rows'] as $row) {
            fputcsv($fh, array($row));
        }
        fclose($fh);
    }

    /**
     * Parse a CSV / XLSX / TXT file uploaded from the custom form's Bulk
     * Upload control. Reads the first column, normalises Pakistani MSISDN
     * variants, drops invalid rows, dedupes, and returns JSON the client
     * can pour straight into the corresponding select2-tags input.
     *
     * Request:  POST {field_type: 'mobile'|'cnic'|'imei'} + multipart bulk_file
     * Response: {values: string[], invalid_count: int, invalid_samples: string[]}
     */
    public function action_parse_bulk_upload()
    {
        $this->auto_render = false;
        header('Content-Type: application/json');

        $response = array(
            'values'          => array(),
            'invalid_count'   => 0,
            'invalid_samples' => array(),
        );

        if (!Auth::instance()->logged_in()) {
            $response['error'] = 'auth';
            echo json_encode($response);
            return;
        }

        $type = isset($_POST['field_type']) ? strtolower((string) $_POST['field_type']) : '';
        if (!in_array($type, array('mobile', 'cnic', 'imei'), true)) {
            $response['error'] = 'invalid_type';
            echo json_encode($response);
            return;
        }

        if (empty($_FILES['bulk_file']['tmp_name']) || !is_uploaded_file($_FILES['bulk_file']['tmp_name'])) {
            $response['error'] = 'no_file';
            echo json_encode($response);
            return;
        }

        // Cap upload size at 2 MB — we only need a list of strings, not a
        // workbook full of formulas.
        if (!empty($_FILES['bulk_file']['size']) && $_FILES['bulk_file']['size'] > 2 * 1024 * 1024) {
            $response['error'] = 'file_too_large';
            echo json_encode($response);
            return;
        }

        $tmp  = $_FILES['bulk_file']['tmp_name'];
        $name = isset($_FILES['bulk_file']['name']) ? $_FILES['bulk_file']['name'] : '';
        $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        try {
            $raw_values = $this->_read_first_column($tmp, $ext);
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'action_parse_bulk_upload',
                'File parse failed: ' . $e->getMessage(),
                array('field_type' => $type, 'ext' => $ext, 'name' => $name),
                $e->getTraceAsString(),
                'parse_failure',
                'admin_custom_form'
            );
            $response['error'] = 'parse_failed';
            echo json_encode($response);
            return;
        }

        // Validate + normalise per type.
        $cleaned = array();
        $invalid = array();
        foreach ($raw_values as $val) {
            $val = trim((string) $val);
            if ($val === '') continue;
            $digits = preg_replace('/\D/', '', $val);

            if ($type === 'mobile') {
                // Normalise common Pakistani MSISDN forms → 10-digit 3xxxxxxxxx.
                if (strlen($digits) === 11 && substr($digits, 0, 1) === '0') {
                    $digits = substr($digits, 1);
                } elseif (strlen($digits) === 12 && substr($digits, 0, 2) === '92') {
                    $digits = substr($digits, 2);
                } elseif (strlen($digits) === 13 && substr($digits, 0, 4) === '0092') {
                    $digits = substr($digits, 4);
                }
                if (strlen($digits) === 10 && substr($digits, 0, 1) === '3') {
                    $cleaned[] = $digits;
                } else {
                    $invalid[] = $val;
                }
            } elseif ($type === 'cnic') {
                if (strlen($digits) === 13) {
                    $cleaned[] = $digits;
                } else {
                    $invalid[] = $val;
                }
            } elseif ($type === 'imei') {
                if (strlen($digits) === 14 || strlen($digits) === 15 || strlen($digits) === 16) {
                    $cleaned[] = $digits;
                } else {
                    $invalid[] = $val;
                }
            }
        }

        $response['values']          = array_values(array_unique($cleaned));
        $response['invalid_count']   = count($invalid);
        $response['invalid_samples'] = array_slice($invalid, 0, 5);

        echo json_encode($response);
    }

    /**
     * Internal helper used by action_parse_bulk_upload(). Reads the first
     * column of an uploaded file, regardless of whether it's CSV/TSV/TXT
     * (native PHP) or XLSX/XLS (PHPExcel via the project's existing
     * Spreadsheet wrapper). Skips a header row when the first cell looks
     * like a label (non-numeric).
     *
     * @return string[] raw cell values from the first column.
     */
    private function _read_first_column($path, $ext)
    {
        $values = array();

        if ($ext === 'csv' || $ext === 'tsv' || $ext === 'txt' || $ext === '') {
            $delim = ($ext === 'tsv') ? "\t" : ',';
            $fh    = @fopen($path, 'r');
            if ($fh === false) {
                throw new Exception('Cannot open file');
            }
            // Strip optional UTF-8 BOM.
            $bom = fread($fh, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($fh);
            }

            $row_index = 0;
            while (($row = fgetcsv($fh, 0, $delim)) !== false) {
                $first = isset($row[0]) ? trim((string) $row[0]) : '';
                if ($first === '') {
                    $row_index++;
                    continue;
                }
                // Skip a header row only on the first non-empty line, and
                // only when it looks like a label (no digits at all).
                if ($row_index === 0 && !preg_match('/\d/', $first)) {
                    $row_index++;
                    continue;
                }
                $values[] = $first;
                $row_index++;
            }
            fclose($fh);
            return $values;
        }

        if ($ext === 'xlsx' || $ext === 'xls') {
            // Lean on the project's Kohana phpexcel module — instantiating
            // Spreadsheet pulls in PHPExcel's autoloader as a side effect,
            // so PHPExcel_IOFactory becomes resolvable below.
            new Spreadsheet();
            $reader = PHPExcel_IOFactory::createReaderForFile($path);
            $reader->setReadDataOnly(true);
            $excel = $reader->load($path);
            $sheet = $excel->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            $first_row_seen = false;
            for ($r = 1; $r <= $highestRow; $r++) {
                $cell = $sheet->getCellByColumnAndRow(0, $r);
                $val = $cell->getValue();
                $val = is_null($val) ? '' : trim((string) $val);
                if ($val === '') continue;

                // Skip header cell once.
                if (!$first_row_seen && !preg_match('/\d/', $val)) {
                    $first_row_seen = true;
                    continue;
                }
                $first_row_seen = true;
                $values[] = $val;
            }
            return $values;
        }

        throw new Exception('Unsupported file format: ' . $ext);
    }

    /**
     * Build the bulk-request email body server-side. The custom request
     * form calls this via AJAX whenever (request_type, company, mobiles,
     * cnics, imeis, dates) changes — server is the source of truth so
     * the format the LEA team's parser sees matches exactly what the
     * admin previewed.
     *
     * Request:  POST {request_type, company_name, mobiles[], cnics[],
     *                  imeis[], start_date, end_date}
     * Response: {subject, body, email, errors[], has_bulk_format}
     *   - subject / email come from email_templates + Helpers_CompanyEmail
     *     (same source the single-request flow uses).
     *   - body is either the bulk-format string from Helpers_BulkRequest
     *     (when the (type, company) combo has one defined) OR the
     *     standard template body_txt with all single-request placeholders
     *     substituted.
     *   - errors lists every validation issue surfaced by
     *     Helpers_BulkRequest::validate(). Empty array == ready to send.
     *   - has_bulk_format flags whether the body came from a bulk builder.
     */
    public function action_build_bulk_body()
    {
        $this->auto_render = false;
        header('Content-Type: application/json');

        $response = array(
            'subject'         => '',
            'body'            => '',
            'email'           => '',
            'errors'          => array(),
            'has_bulk_format' => false,
        );

        if (!Auth::instance()->logged_in()) {
            $response['errors'][] = 'Session expired. Please reload.';
            echo json_encode($response);
            return;
        }

        try {
            $post = Helpers_Utilities::remove_injection($_POST);

            // Validate first — we still return subject/email/body even if
            // there are errors so the admin sees a useful preview while
            // they fix things, but the submit path will refuse to send.
            $response['errors'] = Helpers_BulkRequest::validate($post);

            $request_type = isset($post['request_type']) ? (int) $post['request_type'] : 0;
            $company_name = isset($post['company_name']) ? (int) $post['company_name'] : 0;

            // Subject + recipient: always come from the standard sources
            // (email_templates row + company_emails config) so admins
            // can't accidentally override them via the form.
            if ($request_type > 0 && $company_name > 0) {
                $template = Model_Email::get_email_tempalte($request_type, $company_name);
                if (!empty($template) && isset($template['subject'])) {
                    $response['subject'] = htmlspecialchars_decode($template['subject']);
                }
                $email_config = Helpers_CompanyEmail::get_email($company_name, $request_type);
                if (!empty($email_config['email'])) {
                    $response['email'] = $email_config['email'];
                }

                // Bulk body if (type, company) has a defined format,
                // otherwise fall back to standard placeholder substitution
                // against the template body_txt.
                $bulk_body = Helpers_BulkRequest::build($post);
                if ($bulk_body !== null) {
                    $response['body']            = $bulk_body;
                    $response['has_bulk_format'] = true;
                } elseif (!empty($template) && isset($template['body_txt'])) {
                    $response['body'] = $this->_apply_template_placeholders(
                        (string) $template['body_txt'], $post
                    );
                }
            }
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'action_build_bulk_body',
                $e->getMessage(),
                array('post' => $_POST),
                $e->getTraceAsString(),
                'ajax_error',
                'admin_custom_form'
            );
            $response['errors'][] = 'Internal server error.';
        }

        echo json_encode($response);
    }

    /**
     * Apply the same single-request placeholder set the single-request
     * controller does — used for non-bulk types (3, 4) where we still
     * want a useful body preview built from email_templates.body_txt.
     */
    private function _apply_template_placeholders($text, array $post)
    {
        if ($text === '') return '';
        $mobiles = isset($post['mobiles']) ? (array) $post['mobiles'] : array();
        $cnics   = isset($post['cnics'])   ? (array) $post['cnics']   : array();
        $imeis   = isset($post['imeis'])   ? (array) $post['imeis']   : array();
        $sd_raw  = isset($post['start_date']) ? (string) $post['start_date'] : '';
        $ed_raw  = isset($post['end_date'])   ? (string) $post['end_date']   : '';

        if (count($mobiles) > 0) $text = str_replace('[mobile_number]', implode(', ', $mobiles), $text);
        if (count($cnics)   > 0) $text = str_replace('[cnic_number]',   implode(', ', $cnics),   $text);
        if (count($imeis)   > 0) $text = str_replace('[imei_number]',   implode(', ', $imeis),   $text);

        // Date placeholders (only substitute when valid mm/dd/yyyy).
        if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $sd_raw, $m) && checkdate((int)$m[1], (int)$m[2], (int)$m[3])) {
            $sd_dt = DateTime::createFromFormat('!m/d/Y', sprintf('%d/%d/%d', (int)$m[1], (int)$m[2], (int)$m[3]));
            if ($sd_dt) {
                $text = str_replace('[start_date_dot]',       $sd_dt->format('d.m.Y'), $text);
                $text = str_replace('[start_date_slash]',     $sd_dt->format('d/m/Y'), $text);
                $text = str_replace('[start_date_hyphen]',    $sd_dt->format('d-m-Y'), $text);
                $text = str_replace('[start_date_slash_mdy]', $sd_dt->format('m/d/Y'), $text);
            }
        }
        if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $ed_raw, $m) && checkdate((int)$m[1], (int)$m[2], (int)$m[3])) {
            $ed_dt = DateTime::createFromFormat('!m/d/Y', sprintf('%d/%d/%d', (int)$m[1], (int)$m[2], (int)$m[3]));
            if ($ed_dt) {
                $text = str_replace('[end_date_dot]',       $ed_dt->format('d.m.Y'), $text);
                $text = str_replace('[end_date_slash]',     $ed_dt->format('d/m/Y'), $text);
                $text = str_replace('[end_date_hyphen]',    $ed_dt->format('d-m-Y'), $text);
                $text = str_replace('[end_date_slash_mdy]', $ed_dt->format('m/d/Y'), $text);
            }
        }

        $text = str_replace('[current_date]', date('d/m/Y'), $text);

        // Preserve the [case_number] placeholder for the server-side
        // ADM-<reference_id> substitution in action_admincustomsend.
        $text = preg_replace('/(?:ADM-)?\[case_number\]/', 'ADM-[case_number]', $text);

        return $text;
    }

    public function action_get_template_data()
    {
        $this->auto_render = false;
        header('Content-Type: application/json');

        $response = array('subject' => '', 'body' => '', 'email' => '');

        if (!Auth::instance()->logged_in()) {
            echo json_encode($response);
            return;
        }

        try {
            $post = Helpers_Utilities::remove_injection($_POST);
            $request_type = !empty($post['request_type']) ? (int) $post['request_type'] : 0;
            $company_name = !empty($post['company_name']) ? (int) $post['company_name'] : 0;

            if ($request_type > 0 && $company_name > 0) {
                // Email template (subject + body) for this (type, company) pair.
                $template = Model_Email::get_email_tempalte($request_type, $company_name);
                if (!empty($template)) {
                    $response['subject'] = isset($template['subject'])
                        ? htmlspecialchars_decode($template['subject'])
                        : '';
                    $response['body'] = isset($template['body_txt'])
                        ? $template['body_txt']
                        : '';
                }

                // Default recipient email for the chosen company / request type.
                $email_config = Helpers_CompanyEmail::get_email($company_name, $request_type);
                $response['email'] = isset($email_config['email'])
                    ? $email_config['email']
                    : '';
            }
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'action_get_template_data',
                $e->getMessage(),
                array('request_type' => $request_type ?? null, 'company_name' => $company_name ?? null),
                $e->getTraceAsString(),
                'ajax_error',
                'admin_custom_form'
            );
            // Fall through with the empty $response so the form doesn't break.
        }

        echo json_encode($response);
    }

    // admin custom email send
    public function action_admincustomsend()
    {
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();
            $email_file_name = $file_name = '';

            if (!empty($_FILES['emailfile'])) {
                $directory = 'dist/uploads/user/request_approve/';
                $file_name = "emailfile";
                $date = date("YmdHis", time());
                $ext = pathinfo($_FILES['emailfile']['name'], PATHINFO_EXTENSION);
                $filename = $file_name . $date . "." . $ext;
                $file = Upload::save($_FILES['emailfile'], $filename, $directory);
                $email_file_name = getcwd() . DIRECTORY_SEPARATOR . $directory . $filename;
            }

            if (!empty($_FILES['file'])) {
                $directory = 'dist/uploads/user/request_approve/';
                $file_name = "rqtaprvd";
                $date = date("YmdHis", time());
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                $filename = $file_name . $date . "." . $ext;
                $file = Upload::save($_FILES['file'], $filename, $directory);
                $file_name = $directory . $filename;
            }

            if ((isset($_POST)) && ($_POST != '')) {
//                echo '<pre>';
//                print_r($_POST);
//                exit;
                $body_r = $_POST['body'];
                $cust_email = $_POST['emiladdress'];
                $_POST = Helpers_Utilities::remove_injection($_POST);
                $request_type = $_POST['ChooseTemplate'];

                $user_id = $user_obj->id;
                $status = 1;
                $processing = 0;
                $reason = !empty($_POST['inputreason']) ? $_POST['inputreason'] : '';
                $company_name = (!empty($_POST['company_name_get']) ? $_POST['company_name_get'] : '');
                $date_current = date('d/M/Y'); //$date;
                $date_current_dot = date('d.m.Y'); //$date;
                $concerned_person_id = (!empty($_POST['person_id']) ? $_POST['person_id'] : '');


                try {
                    //// limit check start
                    $query = "SELECT sum(total)as total FROM request_send_today where request_priority = 1";
                    $sql = DB::query(Database::SELECT, $query);
                    $total_count = $sql->execute()->current();

// previous low request 1000
                    if ($total_count['total'] <= 1450) {
                        //// limit check end
                        // Use centralized email helper
                        $email_config = Helpers_CompanyEmail::get_email($company_name, $request_type);
                        $to = $email_config['email'] ?? '';
                        $to_name = $email_config['name'] ?? '';
                        
                        // Validate email configuration
                        if (empty($to)) {
                            throw new Exception("Email configuration not found for company_name: {$company_name}, request_type: {$request_type}");
                        }
                        do {
                            if ($GLOBALS['id_generator'] == 0) {
                                $GLOBALS['id_generator'] = 1;
                                //Reference id to be sent to company
                                $reference_id = Helpers_Utilities::id_generator("admin_reference_id");
                                $GLOBALS['id_generator'] = 0;
                            }
                        } while ($GLOBALS['id_generator'] == 1);
                        $requested_value = $startDate = $endDate = '';


                        $rqtby = !empty($_POST['rqtbyname']) ? $_POST['rqtbyname'] : '';


                        /*if ($request_type != 18) {*/
                        $reference_number = Model_AdminRequest::admin_request($reference_id, $user_id, $request_type, $company_name, $status, $requested_value, $startDate, $endDate, $reason, $file_name, $rqtby);
                        if (empty($_POST['esubject'])) {
                            $template_data = Model_Email::get_email_tempalte($request_type, $company_name);
                            $template = !empty($template_data) ? $template_data['subject'] : '';
                        } else {
                            $template = $_POST['esubject'];
                        }
                        //    echo $template_data['subject'];    exit;
                        // Handle BOTH legacy '[case_number]' and the new
                        // form-display 'ADM-[case_number]' in one substitution
                        // so we never end up with a double 'ADM-ADM-' prefix.
                        // The view's auto-fill renders '[case_number]' as
                        // 'ADM-[case_number]'; if the admin manually edits
                        // the subject they may keep either form.
                        $subject = preg_replace(
                            '/(?:ADM-)?\[case_number\]/',
                            'ADM-' . $reference_id,
                            htmlspecialchars_decode($template)
                        );

                        //    echo $subject;    exit;
                        /*} else {
                            $reference_number = Model_AdminRequest::admin_request($reference_id, $user_id, 4, $company_name, $status, $requested_value, $startDate, $endDate, $reason, $file_name, $rqtby);
                            $subject = 'loc&Subs';
                        }*/


                        //$body = !empty($_POST['body'])?$_POST['body']:'';;
                        $body = !empty($body_r) ? $body_r : '';
                        //$body = preg_replace("/\\r|\\n/", "", $body);
                        // echo $body; exit;
                        /* change in 10 23 2017 */

                        // Guarantee 'ADM-<reference_id>' is at least in the body
                        // (and in the subject too, except for Ufone which enforces
                        // a strict standard subject format we mustn't pollute).
                        // The cron receive flow scans subject + body, so the body
                        // footer alone is enough to route Ufone replies back here.
                        $inject_subject = ((int) $company_name !== 3); // 3 = Ufone
                        list($subject, $body) = Helpers_Email::ensure_admin_reference_token(
                            $subject, $body, $reference_id, $inject_subject
                        );

                        $email_staus = Helpers_Email::send_email($cust_email, $to_name, $subject, $body, $email_file_name);
                        /*
                          if ($email_staus == 1) {
                          $reference_number = Model_Email::email_sended($to, $subject, $body, $reference_number, 0, 1);
                          } else { */
                        if (file_exists($email_file_name)) {
                            unlink($email_file_name);
                        }
                        $reference_number = Model_AdminRequest::admin_email_sended($cust_email, $subject, $body, $reference_number, 7, 1, $startDate, $endDate);
                        //
                        //}
                        // print_r($request_type);
                        // print_r($reference_number); exit;
                        /* $login_user = Auth::instance()->get_user();
                         $uid = $login_user->id;
                         Helpers_Profile::user_activity_log($uid, 10, $request_type, $requested_value, $concerned_person_id, $company_name);
                         */

                        /*if ($request_type == 4) {
                            Helpers_Person::fire_current_location();
                        }*/

                        ///limit start
                        $query = DB::update('request_send_today')->set(array('total' => DB::expr('total + 1')))
                            ->where('request_priority', '=', 1)
                            ->and_where('company_name', '=', $company_name)
                            ->execute();
                    } else {
                        echo json_encode(5);
                        exit;
                    }
                    //limit end

                } catch (Exception $e) {
                    Model_ErrorLog::log(
                        'action_admincustomsend',
                        $e->getMessage(),
                        [
                            'request_type' => $request_type ?? 'N/A',
                            'company_name' => $company_name ?? 'N/A',
                            'cust_email' => $cust_email ?? 'N/A'
                        ],
                        $e->getTraceAsString(),
                        'email_send_failure',
                        'admin_custom_send'
                    );
                    error_log("[" . date('c') . "] action_admincustomsend exception: " . $e->getMessage());
                    echo json_encode(2);
                    exit;
                }

                echo json_encode(1);
                //$this->redirect($redirect_url);
            }
        }
    }

    // admin email send
    public function action_adminsend()
    {
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();
            $file_name = '';
            if (!empty($_FILES['file'])) {
                $directory = 'dist/uploads/user/request_approve/';
                $file_name = "rqtaprvd";
                $date = date("YmdHis", time());
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                $filename = $file_name . $date . "." . $ext;
                $file = Upload::save($_FILES['file'], $filename, $directory);
                $file_name = $directory . $filename;
            }

            if ((isset($_POST)) && ($_POST != '')) {

                $_POST = Helpers_Utilities::remove_injection($_POST);
                $request_type = $_POST['ChooseTemplate'];

                $user_id = $user_obj->id;
                $status = 1;
                $processing = 0;
                $reason = !empty($_POST['inputreason']) ? $_POST['inputreason'] : '';
                $company_names = (!empty($_POST['company_name_get']) ? $_POST['company_name_get'] : '');

                $date_current = date('d/M/Y'); //$date;
                $date_current_dot = date('d.m.Y'); //$date;


                $concerned_person_id = (!empty($_POST['person_id']) ? $_POST['person_id'] : '');
                $endDate = (!empty($_POST['endDate']) ? $_POST['endDate'] : '');
                $startDate = (!empty($_POST['startDate']) ? $_POST['startDate'] : '');
                $to_name = '';
                foreach ($company_names as $company_name) {
                    try {

                        $requested_value = (isset($_POST['inputSubNO']) && !empty($_POST['inputSubNO']) ? $_POST['inputSubNO'] :
                            ((isset($_POST['inputCNIC']) && !empty($_POST['inputCNIC'])) ? $_POST['inputCNIC'] :
                                (isset($_POST['inputPTCLNO']) && !empty($_POST['inputPTCLNO']) ? $_POST['inputPTCLNO'] :
                                    (isset($_POST['inputInternationalNo']) && !empty($_POST['inputInternationalNo']) ? $_POST['inputInternationalNo'] :
                                        (isset($_POST['inputIMEI']) && !empty($_POST['inputIMEI']) ? $_POST['inputIMEI'] : '')))));

                        //// limit check start
                        $query = "SELECT sum(total)as total FROM request_send_today where request_priority = 1";
                        $sql = DB::query(Database::SELECT, $query);
                        $total_count = $sql->execute()->current();

                        if ($total_count['total'] <= 1350) {
                            //// limit check end

                            // Use centralized email helper
                            $email_config = Helpers_CompanyEmail::get_email($company_name, $request_type);
                            $to = $email_config['email'] ?? '';
                            $to_name = $email_config['name'] ?? '';
                            
                            // Validate email configuration
                            if (empty($to)) {
                                throw new Exception("Email configuration not found for company_name: {$company_name}, request_type: {$request_type}");
                            }
                            
                            // Format dates for different companies
                            $start_date_dot = date('d.m.Y', strtotime($startDate));
                            $end_date_dot = date('d.m.Y', strtotime($endDate));

                            $start_date_slash = date('d/m/Y', strtotime($startDate));
                            $end_date_slash = date('d/m/Y', strtotime($endDate));

                            $start_date_hyphen = date('d-m-Y', strtotime($startDate));
                            $end_date_hyphen = date('d-m-Y', strtotime($endDate));

                            $start_date_slash_mdy = date('m/d/Y', strtotime($startDate));
                            $end_date_slash_mdy = date('m/d/Y', strtotime($endDate));
                            
                            // Special handling for specific companies
                            if ($company_name == 3 && strlen($requested_value) == 15) {
                                // Ufone specific: truncate IMEI
                                $requested_value = substr($requested_value, 0, 14) . '0';
                            }
                            
                            if ($company_name == 6 && isset($_POST['inputIMEI']) && !empty($_POST['inputIMEI'])) {
                                // Telenor specific: trim last character from IMEI
                                $_POST['inputIMEI'] = mb_substr($_POST['inputIMEI'], 0, -1);
                            }


                            do {
                                if ($GLOBALS['id_generator'] == 0) {
                                    $GLOBALS['id_generator'] = 1;
                                    //Reference id to be sent to company
                                    $reference_id = Helpers_Utilities::id_generator("admin_reference_id");
                                    $GLOBALS['id_generator'] = 0;
                                }
                            } while ($GLOBALS['id_generator'] == 1);

                            $rqtby = !empty($_POST['rqtbyname']) ? $_POST['rqtbyname'] : '';
                            $reference_number = Model_AdminRequest::admin_request($reference_id, $user_id, $request_type, $company_name, $status, $requested_value, $startDate, $endDate, $reason, $file_name, $rqtby);

                            $template_data = Model_Email::get_email_tempalte($request_type, $company_name);
                            if (!$template_data) {
                                throw new Exception("Email template not found for request_type {$request_type} and company_name {$company_name}");
                            }

                            // Same regex as action_admincustomsend: handles
                            // both '[case_number]' and 'ADM-[case_number]' so
                            // a future template that includes the prefix
                            // doesn't double-stamp the final subject.
                            $subject = preg_replace(
                                '/(?:ADM-)?\[case_number\]/',
                                'ADM-' . $reference_id,
                                htmlspecialchars_decode($template_data['subject'])
                            );

                            if (!empty($_POST['inputSubNO'])) {
                                $subject = str_replace("[mobile_number]", $_POST['inputSubNO'], $subject);
                            }

                            $body = isset($_POST['inputSubNO']) ? str_replace("[mobile_number]", $_POST['inputSubNO'], $template_data['body_txt']) : $template_data['body_txt'];
                            $body = isset($_POST['inputCNIC']) ? str_replace("[cnic_number]", $_POST['inputCNIC'], $body) : $body;
                            $body = str_replace("[ptcl_number]", $requested_value, $body);
                            // Match the subject regex so a body template that
                            // already has 'ADM-[case_number]' doesn't double-prefix.
                            $body = preg_replace('/(?:ADM-)?\[case_number\]/', 'ADM-' . $reference_id, $body);

                            $body = str_replace("[start_date_dot]", $start_date_dot, $body);
                            $body = str_replace("[end_date_dot]", $end_date_dot, $body);
                            $body = str_replace("[start_date_slash]", $start_date_slash, $body);
                            $body = str_replace("[end_date_slash]", $end_date_slash, $body);
                            $body = str_replace("[start_date_slash_mdy]", $start_date_slash_mdy, $body);
                            $body = str_replace("[end_date_slash_mdy]", $end_date_slash_mdy, $body);
                            $body = str_replace("[start_date_hyphen]", $start_date_hyphen, $body);
                            $body = str_replace("[end_date_hyphen]", $end_date_hyphen, $body);

                            $body = isset($_POST['inputIMEI']) ? str_replace("[imei_number]", $_POST['inputIMEI'], $body) : $body;
                            if ($request_type == 10) {
                                $body = str_replace("[current_date]", $date_current_dot, $body);
                            } else {
                                $body = str_replace("[current_date]", $date_current, $body);
                            }

                            /* change in 10 23 2017 */

                            // Guarantee 'ADM-<reference_id>' lands in the body
                            // before the Ufone-vs-default branching below.
                            // For Ufone (company_id=3) the subject must stay in
                            // its strict standard format, so we pass false for
                            // $inject_subject — the body footer is the only
                            // marker, and it travels into the attached .txt.
                            // For everyone else, both subject and body get the
                            // marker as a belt-and-braces guarantee.
                            $inject_subject = ((int) $company_name !== 3);
                            list($subject, $body) = Helpers_Email::ensure_admin_reference_token(
                                $subject, $body, $reference_id, $inject_subject
                            );

                            if ($company_name == 3 && in_array($request_type, [1, 2, 6])) {  // Ufone specific handling

                                // Clean body text (strip HTML tags properly)
                                $body = strip_tags($body);
                                $body = str_ireplace(['&lt;p&gt;', '&lt;/p&gt;'], '', $body);  // remove leftover <p> entities

                                // Optional: uncomment if you still need this for request_type 2
                                // if ($request_type == 2) {
                                //     $body = rtrim($body, "\r\n");  // safer than substr_replace
                                // }

                                // Create temporary text file as attachment
                                $filename   = $reference_id . ".txt";
                                $target_dir = UFONE_FILES;  // already ends with \ from bootstrap
                                $full_path  = $target_dir . $filename;

                                // Ensure the directory exists (critical fix!)
                                if (!is_dir($target_dir)) {
                                    if (!mkdir($target_dir, 0755, true)) {
                                        // Log error instead of crashing
                                        error_log("Failed to create directory: $target_dir");
                                        // You can set a fallback or return error here
                                        $email_status = false;  // or handle gracefully
                                        // continue to next block or return early
                                    }
                                }

                                // Write the file
                                $myfile = fopen($full_path, "w");
                                if ($myfile === false) {
                                    error_log("Failed to create temp file: $full_path");
                                    $email_status = false;
                                } else {
                                    fwrite($myfile, $body);
                                    fclose($myfile);

                                    // Send email with attachment
                                    $email_status = Helpers_Email::send_email($to, $to_name, $subject, $body, $full_path);

                                    // Clean up temp file
                                    if (file_exists($full_path)) {
                                        unlink($full_path);
                                    }
                                }
                            } else {
                                // Normal email without attachment
                                $email_status = Helpers_Email::send_email($to, $to_name, $subject, $body);
                            }

                            //$email_staus = Helpers_Email::send_email($to, $to_name, $subject, $body);
                            /*
                              if ($email_staus == 1) {
                              $reference_number = Model_Email::email_sended($to, $subject, $body, $reference_number, 0, 1);
                              } else { */

                            $reference_number = Model_AdminRequest::admin_email_sended($to, $subject, $body, $reference_number, 7, 1, $startDate, $endDate);
                            //
                            //}
                            // print_r($request_type);
                            // print_r($reference_number); exit;
                            /* $login_user = Auth::instance()->get_user();
                             $uid = $login_user->id;
                             Helpers_Profile::user_activity_log($uid, 10, $request_type, $requested_value, $concerned_person_id, $company_name);
                             */

                            /*if ($request_type == 4) {
                                Helpers_Person::fire_current_location();
                            }*/

                            ///limit start
                            $query = DB::update('request_send_today')->set(array('total' => DB::expr('total + 1')))
                                ->where('request_priority', '=', 1)
                                ->and_where('company_name', '=', $company_name)
                                ->execute();
                        } else {
                            echo json_encode(5);
                            exit;
                        }
                        //limit end

                    } catch (Exception $e) {
                        Model_ErrorLog::log(
                            'action_adminsend',
                            $e->getMessage(),
                            [
                                'request_type' => $request_type ?? 'N/A',
                                'company_name' => $company_name ?? 'N/A',
                                'requested_value' => $requested_value ?? 'N/A'
                            ],
                            $e->getTraceAsString(),
                            'email_send_failure',
                            'admin_send'
                        );
                        error_log("[" . date('c') . "] action_adminsend exception: " . $e->getMessage());
                        echo json_encode([
                            'status' => 0,
                            'message' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine()
                        ]);
                        exit;
                    }
                }
                echo json_encode(1);
                //$this->redirect($redirect_url);
            }
        }
    } // admin nadra request send

    public function action_admin_nadra_send()
    {
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();
            $file_name = '';

            if (!empty($_FILES['file'])) {
                $directory = 'dist/uploads/user/request_approve/';
                $file_name = "rqtaprvd";
                $date = date("YmdHis", time());
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                $filename = $file_name . $date . "." . $ext;
                $file = Upload::save($_FILES['file'], $filename, $directory);
                $file_name = $directory . $filename;
            }

            if ((isset($_POST)) && ($_POST != '')) {
                $_POST = Helpers_Utilities::remove_injection($_POST);

                $user_id = $user_obj->id;
                $cnic = !empty($_POST['cnic']) ? $_POST['cnic'] : '';
                $date = !empty($_POST['date']) ? $_POST['date'] : '';

                $rqtby = !empty($_POST['rqtbyname']) ? $_POST['rqtbyname'] : '';
                $rqtby_region_id = !empty($_POST['reqbyregion']) ? $_POST['reqbyregion'] : '';
                $rqtby_district_id = !empty($_POST['reqbydistrict']) ? $_POST['reqbydistrict'] : '';
                // $region= Helpers_Utilities::get_region($rqtby_region_id);
                $reason = !empty($_POST['inputreason']) ? $_POST['inputreason'] : '';
                $status = !empty($_POST['status']) ? $_POST['status'] : 0;
                $update = Model_AdminRequest::admin_nadra_request($user_id, $cnic, $date, $status, $rqtby, $rqtby_region_id, $rqtby_district_id, $reason, $file_name);
                echo json_encode(1);
                exit;
            }
            echo json_encode(-2);
        }
    }

    public function action_admin_familytree_send()
    {
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();
            $file_name = '';

            if (!empty($_FILES['file'])) {
                $directory = 'dist/uploads/user/family_tree/';
                $file_name = "rqtaprvd";
                $date = date("YmdHis", time());
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                $filename = $file_name . $date . "." . $ext;
                $file = Upload::save($_FILES['file'], $filename, $directory);
                $file_name = $directory . $filename;
            }

            if ((isset($_POST)) && ($_POST != '')) {
                $_POST = Helpers_Utilities::remove_injection($_POST);

                $user_id = $user_obj->id;
                $cnic = !empty($_POST['cnic']) ? $_POST['cnic'] : '';
                $date = !empty($_POST['date']) ? $_POST['date'] : '';

                $rqtby = !empty($_POST['rqtbyname']) ? $_POST['rqtbyname'] : '';
                $rqtby_region_id = !empty($_POST['reqbyregion']) ? $_POST['reqbyregion'] : '';
                $rqtby_district_id = !empty($_POST['reqbydistrict']) ? $_POST['reqbydistrict'] : '';
                // $region= Helpers_Utilities::get_region($rqtby_region_id);
                $reason = !empty($_POST['inputreason']) ? $_POST['inputreason'] : '';
                $status = !empty($_POST['status']) ? $_POST['status'] : 0;
                $update = Model_AdminRequest::admin_family_tree_request($user_id, $cnic, $date, $status, $rqtby, $rqtby_region_id, $rqtby_district_id, $reason, $file_name);
                echo json_encode(1);
                exit;
            }
            echo json_encode(-2);
        }
    }

    public function action_admin_travel_send()
    {
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();
            $file_name = '';

            if (!empty($_FILES['file'])) {
                $directory = 'dist/uploads/user/travel_history/';
                $file_name = "rqtaprvd";
                $date = date("YmdHis", time());
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                $filename = $file_name . $date . "." . $ext;
                $file = Upload::save($_FILES['file'], $filename, $directory);
                $file_name = $directory . $filename;
            }

            if ((isset($_POST)) && ($_POST != '')) {
                $_POST = Helpers_Utilities::remove_injection($_POST);

                $user_id = $user_obj->id;
                $cnic = !empty($_POST['cnic']) ? $_POST['cnic'] : '';
                $passport = !empty($_POST['passport']) ? $_POST['passport'] : '';
                $date = !empty($_POST['date']) ? $_POST['date'] : '';

                $rqtby = !empty($_POST['rqtbyname']) ? $_POST['rqtbyname'] : '';
                $rqtby_region_id = !empty($_POST['reqbyregion']) ? $_POST['reqbyregion'] : '';
                $rqtby_district_id = !empty($_POST['reqbydistrict']) ? $_POST['reqbydistrict'] : '';
                // $region= Helpers_Utilities::get_region($rqtby_region_id);
                $reason = !empty($_POST['inputreason']) ? $_POST['inputreason'] : '';
                $status = !empty($_POST['status']) ? $_POST['status'] : 0;
                $update = Model_AdminRequest::admin_travel_request($user_id, $cnic, $passport, $date, $status, $rqtby, $rqtby_region_id, $rqtby_district_id, $reason, $file_name);
                echo json_encode(1);
                exit;
            }
            echo json_encode(-2);
        }
    }


    /* Admin Request Status (request_status) */

    public function action_admin_request_status()
    {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            if (Helpers_Utilities::chek_role_access($this->role_id, 14) == 1) {
                /* Posted Data */

                $post = $this->request->post(); //            echo '<pre>';
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['message']) && $post['message'] == 1) {
                    $message = 'Congratulation! Request Sent successfully';
                }
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('request_status_post', $post);
                /* File Included */
                include 'user_functions/request_status.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_admin_request_status',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'page_load'
            );
            error_log("[" . date('c') . "] action_admin_request_status exception: " . $ex->getMessage());
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /*     * Admin Request*/

    public function action_nadra_request_sent_form()
    {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 34) == 1 || $login_user->id == 719) {
                /* File Included */
                $this->template->content = View::factory('templates/user/nadra_request_sent_form');
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_nadra_request_sent_form',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'page_load'
            );
            error_log("[" . date('c') . "] action_nadra_request_sent_form exception: " . $ex->getMessage());
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    public function action_familytree_request_sent_form()
    {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 34) == 1 || $login_user->id == 719) {
                /* File Included */
                $this->template->content = View::factory('templates/user/familytree_request_sent_form');
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_familytree_request_sent_form',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'page_load'
            );
            error_log("[" . date('c') . "] action_familytree_request_sent_form exception: " . $ex->getMessage());
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /*     * Admin Request*/

    public function action_travel_request_sent_form()
    {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 34) == 1 || $login_user->id == 719) {
                /* File Included */
                $this->template->content = View::factory('templates/user/travel_request_sent_form');
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_travel_request_sent_form',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'page_load'
            );
            error_log("[" . date('c') . "] action_travel_request_sent_form exception: " . $ex->getMessage());
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    //bulk requests
    public function action_nadra_bulk_request_sent_form()
    {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 34) == 1 || $login_user->id == 719) {
                /* File Included */
                $this->template->content = View::factory('templates/user/nadra_bulk_request_sent_form');
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'action_nadra_bulk_request_sent_form',
                $ex->getMessage(),
                [],
                $ex->getTraceAsString(),
                'exception',
                'page_load'
            );
            error_log("[" . date('c') . "] action_nadra_bulk_request_sent_form exception: " . $ex->getMessage());
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    // admin nadra bulk request send

    public function action_admin_nadra_bulk_send()
    {
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();
            $file_name = '';

            if (!empty($_FILES['file'])) {
                $directory = 'dist/uploads/user/request_approve/';
                $file_name = "rqtaprvd";
                $date = date("YmdHis", time());
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                $filename = $file_name . $date . "." . $ext;
                $file = Upload::save($_FILES['file'], $filename, $directory);
                $file_name = $directory . $filename;
            }

            if ((isset($_POST)) && ($_POST != '')) {
                $_POST = Helpers_Utilities::remove_injection($_POST);

                $user_id = $user_obj->id;
                $cnic_group = !empty($_POST['cnic']) ? $_POST['cnic'] : '';
                $cnic_arr = (explode(",", $cnic_group));
//                echo '<pre>';
//                print_r($cnic_sep);
//                exit();
                foreach ($cnic_arr as $cnic_single) {
                    $cnic = $cnic_single;

                    $date = !empty($_POST['date']) ? $_POST['date'] : '';

                    $rqtby = !empty($_POST['rqtbyname']) ? $_POST['rqtbyname'] : '';
                    $rqtby_region_id = !empty($_POST['reqbyregion']) ? $_POST['reqbyregion'] : '';
                    $rqtby_district_id = !empty($_POST['reqbydistrict']) ? $_POST['reqbydistrict'] : '';
                    // $region= Helpers_Utilities::get_region($rqtby_region_id);
                    $reason = !empty($_POST['inputreason']) ? $_POST['inputreason'] : '';
                    $status = !empty($_POST['status']) ? $_POST['status'] : 0;
                    $update = Model_AdminRequest::admin_nadra_request($user_id, $cnic, $date, $status, $rqtby, $rqtby_region_id, $rqtby_district_id, $reason, $file_name);

                }
                echo json_encode(1);
                exit;
            }
            echo json_encode(-2);
        }
    }


}
