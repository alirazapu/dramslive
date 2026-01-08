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
                                            $system_status_flag .= '<a href="http://ctd.aiesplus.kpk/User/upload_against_imei?imei=' . $imei_link . '" <span class="badge badge-pill badge-success">Check IMEI</span></a>';
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
                            $enc_request_id="'".$enc_request_id."'";
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
                                            $system_status_flag .= '<a href="http://ctd.aiesplus.kpk/User/upload_against_imei?imei=' . $imei_link . '" <span class="badge badge-pill badge-success">Check IMEI</span></a>';
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
                                            $system_status_flag .= '<a href="http://ctd.aiesplus.kpk/User/upload_against_imei?imei=' . $imei_link . '" <span class="badge badge-pill badge-success">Check IMEI</span></a>';
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
                        $userslist = [842, 137, 2031,1761, 2603];
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
                        if(!empty($user_request_type_id)) {
                            $dquery .= '/&req=' . $user_request_type_id;
                        }

                        $html = '<a class="btn btn-small action" href="' . URL::site('adminrequest/requests/' . $name_encrypted  . $dquery  ) . '"><i class="fa fa-folder-open-o"></i> View Requests</a>';


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
                        $user_id= (isset($item['user_id'])) ? $item['user_id'] : 0;

                        $u_name= Helpers_Utilities::get_user_name($user_id);
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
                        if(!empty($user_request_type_id)) {
                            $dquery .= '/&req=' . $user_request_type_id;
                        }

                        $html = '<a class="btn btn-small action" href="' . URL::site('adminrequest/u_requests/' . $uid_encrypted  . $dquery  ) . '"><i class="fa fa-folder-open-o"></i> View Requests</a>';


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
        }
    }
/* Delete record form blocked number */

    public function action_deleteuserrequest() {
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
                    $user = New Model_AdminRequest();

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
                                '<a target="_blank" style="margin-top: 25px" target="" href=" ' . URL::base() .  '/' . $item['file_name'] . '" class="btn btn-danger pull-right" >Download File</a>'
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


    // admin email send
    public function action_autocomplete()
    {
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();

            if (isset($_POST['rqtbyname'])) {

                $output = "";
                $city = $_POST['rqtbyname'];
                $sql = "SELECT rqtbyname 
                         from admin_request where rqtbyname like '%$city%' group by rqtbyname";
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

    // admin custom email send
    public function action_admincustomsend()
    {
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();
           $email_file_name= $file_name = '';
            
            if (!empty($_FILES['emailfile'])) {
                $directory = 'dist/uploads/user/request_approve/';
                $file_name = "emailfile";
                $date = date("YmdHis", time());
                $ext = pathinfo($_FILES['emailfile']['name'], PATHINFO_EXTENSION);
                $filename = $file_name . $date . "." . $ext;
                $file = Upload::save($_FILES['emailfile'], $filename, $directory);
                $email_file_name = getcwd() .DIRECTORY_SEPARATOR . $directory . $filename;
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
                        switch ($company_name) {
                            case 1: // Mobilink                                
                                $to = 'leasupportteam@jazz.com.pk';
                                $to_name = '';
                                break;
                            case 3: // Ufone                                
                                //$to = 'racentral@ufone.com';
                                //$to_name = '';
                                if (($request_type == 6 || $request_type == 1 || $request_type == 2)) {
                                        $to = 'cdr.requests@ptclgroup.com';
//                                        $to = 'cdr.requests@ufone.com';
                                        $to_name = ''; //ufone auto for cdr msisdn/imei
                                    } else {
                                        //$to = 'racentral@ufone.com';
                                        //$to = 'ufone.location@ufone.com';
                                        $to = 'ufone.location@ptclgroup.com';
                                        $to_name = '';
                                    }
                                break;
                            case 4: // Zong                                
                                $to = 'reg@zong.com.pk';
                                $to_name = '';
                                break;
                            case 6: // Telenor   $request_type
                                if ($request_type == 1 || $request_type == 2)
                                    //$to = 'lea2@newsystem123.com';
                                    $to = 'lea.2@telenor.com.pk';
                                else
                                    $to = 'lea@newsystem123.com';
                                $to_name = '';
                                break;
                            case 7: // Warid                                
                                $to = 'waridlea@jazz.com.pk';
                                $to_name = '';
                                break;
                            //added by shoaib
                            case 8: // SCOM                                
                                $to = 'reg@zong.com.pk';
                                $to_name = 'scom';
                                break;
                            //shoaib changes ended
                            case 11: // PTCL                                                                
                                $to = 'mega.radata@ptcl.net.pk';
                                $to_name = 'MegaRAdata/PTCL';
                                break;
                            case 12: // International                                
                                $to = 'mega.radata@ptcl.net.pk';
                                $to_name = 'MegaRAdata/PTCL';
                                break;
                            case 13: // family request                                
                                $to = 'naumana.manzoor@nadra.gov.pk';
                                $to_name = 'Nadra';
                                break;
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
                            if(empty($_POST['esubject']))
                            {    
                                $template_data = Model_Email::get_email_tempalte($request_type, $company_name);
                                $template = !empty($template_data) ? $template_data['subject']: '';
                            }else{
                               $template=  $_POST['esubject'];
                            }    
                            //    echo $template_data['subject'];    exit;
                            $subject = str_replace("[case_number]", 'ADM-' . $reference_id, htmlspecialchars_decode($template));

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

                            switch ($company_name) {
                                case 1: // Mobilink
                                    $start_date_dot = date('d.m.Y', strtotime($startDate));
                                    $end_date_dot = date('d.m.Y', strtotime($endDate));

                                    $start_date_slash = date('d/m/Y', strtotime($startDate));
                                    $end_date_slash = date('d/m/Y', strtotime($endDate));

                                    $start_date_hyphen = date('d-m-Y', strtotime($startDate));
                                    $end_date_hyphen = date('d-m-Y', strtotime($endDate));

                                    $start_date_slash_mdy = date('m/d/Y', strtotime($startDate));
                                    $end_date_slash_mdy = date('m/d/Y', strtotime($endDate));

                                    $to = 'leasupportteam@jazz.com.pk';
                                    $to_name = '';
                                    break;
                                case 3: // Ufone
                                    $start_date_dot = date('d.m.Y', strtotime($startDate));
                                    $end_date_dot = date('d.m.Y', strtotime($endDate));
                                    $start_date_slash = date('d/m/Y', strtotime($startDate));
                                    $end_date_slash = date('d/m/Y', strtotime($endDate));
                                    $start_date_slash_mdy = date('m/d/Y', strtotime($startDate));
                                    $end_date_slash_mdy = date('m/d/Y', strtotime($endDate));
                                    $start_date_hyphen = date('d-m-Y', strtotime($startDate));
                                    $end_date_hyphen = date('d-m-Y', strtotime($endDate));

                                    if(strlen($requested_value)==15){
                                        $requested_value= substr($requested_value,0,14).'0';
                                    }
                                    if (($request_type == 6 || $request_type == 1 || $request_type == 2)) {
                                        $to = 'cdr.requests@ptclgroup.com';
//                                        $to = 'cdr.requests@ufone.com';
                                        $to_name = ''; //ufone auto for cdr msisdn/imei
                                    } else {
                                        //$to = 'racentral@ufone.com';
                                       // $to = 'ufone.location@ufone.com';
                                        $to = 'ufone.location@ptclgroup.com';
                                        $to_name = '';
                                    }
                                    break;
                                case 4: // Zong
                                    $start_date_dot = date('d.m.Y', strtotime($startDate));
                                    $end_date_dot = date('d.m.Y', strtotime($endDate));
                                    $start_date_slash = date('d/m/Y', strtotime($startDate));
                                    $end_date_slash = date('d/m/Y', strtotime($endDate));
                                    $start_date_hyphen = date('d-m-Y', strtotime($startDate));
                                    $end_date_hyphen = date('d-m-Y', strtotime($endDate));

                                    $start_date_slash_mdy = date('m/d/Y', strtotime($startDate));
                                    $end_date_slash_mdy = date('m/d/Y', strtotime($endDate));

                                    $to = 'reg@zong.com.pk';
                                    $to_name = '';
                                    break;


                                case 6: // Telenor   $request_type
                                    //print_r($_POST['inputIMEI']);
                                    //echo '<br>' . substr($_POST['inputIMEI'], 0, -1);
                                    $_POST['inputIMEI'] = isset($_POST['inputIMEI']) && !empty($_POST['inputIMEI']) ? mb_substr($_POST['inputIMEI'], 0, -1) : '';
                                    //$_POST['inputIMEI'] = mb_substr($_POST['inputIMEI'], 0, -1);

                                    $start_date_dot = date('d.m.Y', strtotime($startDate));
                                    $end_date_dot = date('d.m.Y', strtotime($endDate));
                                    $start_date_slash = date('d/m/Y', strtotime($startDate));
                                    $end_date_slash = date('d/m/Y', strtotime($endDate));
                                    $start_date_hyphen = date('d-m-Y', strtotime($startDate));
                                    $end_date_hyphen = date('d-m-Y', strtotime($endDate));

                                    $start_date_slash_mdy = date('m/d/Y', strtotime($startDate));
                                    $end_date_slash_mdy = date('m/d/Y', strtotime($endDate));


                                    if (($request_type == 1 || $request_type == 2 || $request_type == 3 || $request_type == 5)) {
                                        //$to = 'lea2@newsystem123.com';
                                         $to = 'lea.2@telenor.com.pk';
                                        $to_name = ''; //Law Enforcement Agency
                                    } elseif ($request_type == 4) {
                                      //  $to = 'lea1@newsystem123.com';
                                        $to = 'lea.1@telenor.com.pk';
                                        $to_name = '';
                                    } else {
                                        $to = 'lea@newsystem123.com';
                                        $to_name = '';
                                    }

                                    break;
                                case 7: // Warid
                                    $start_date_dot = date('d.m.Y', strtotime($startDate));
                                    $end_date_dot = date('d.m.Y', strtotime($endDate));
                                    $start_date_slash = date('d/m/Y', strtotime($startDate));
                                    $end_date_slash = date('d/m/Y', strtotime($endDate));
                                    $start_date_hyphen = date('d-m-Y', strtotime($startDate));
                                    $end_date_hyphen = date('d-m-Y', strtotime($endDate));
                                    $start_date_slash_mdy = date('m/d/Y', strtotime($startDate));
                                    $end_date_slash_mdy = date('m/d/Y', strtotime($endDate));
                                    if (($request_type == 5 || $request_type == 3 || $request_type == 4)) {
                                        $to = 'leasupportteam@jazz.com.pk';
                                        $to_name = '';
                                    } else {
                                        // $to = 'waridlea@jazz.com.pk';
                                        $to = 'leasupportteam@jazz.com.pk';
                                        $to_name = '';
                                    }
                                    break;
                                //added by shoaib
                                case 8: // SCOM
                                    $start_date_dot = date('d.m.Y', strtotime($startDate));
                                    $end_date_dot = date('d.m.Y', strtotime($endDate));
                                    $start_date_slash = date('d/m/Y', strtotime($startDate));
                                    $end_date_slash = date('d/m/Y', strtotime($endDate));
                                    $start_date_hyphen = date('d-m-Y', strtotime($startDate));
                                    $end_date_hyphen = date('d-m-Y', strtotime($endDate));

                                    $start_date_slash_mdy = date('m/d/Y', strtotime($startDate));
                                    $end_date_slash_mdy = date('m/d/Y', strtotime($endDate));
                                    $to = 'info.lea@sco.gov.pk';
                                    $to_name = 'scom';
                                    break;
                                //shoaib changes ended
                                case 11: // PTCL
                                    $start_date_dot = date('d.m.Y', strtotime($startDate));
                                    $end_date_dot = date('d.m.Y', strtotime($endDate));
                                    $start_date_slash = date('d/m/Y', strtotime($startDate));
                                    $end_date_slash = date('d/m/Y', strtotime($endDate));
                                    $start_date_hyphen = date('d-m-Y', strtotime($startDate));
                                    $end_date_hyphen = date('d-m-Y', strtotime($endDate));
                                    $start_date_slash_mdy = date('m/d/Y', strtotime($startDate));
                                    $end_date_slash_mdy = date('m/d/Y', strtotime($endDate));
//                                if ($request_type != 11) {
//                                    //model call to update data in table 'Other numbers'
//                                    $model = Model_Othernumber::update_other_numbers($_POST);
//                                }
                                    $to = 'mega.radata@ptcl.net.pk';
                                    $to_name = 'MegaRAdata/PTCL';
                                    break;
                                case 12: // International
                                    $start_date_dot = date('d.m.Y', strtotime($startDate));
                                    $end_date_dot = date('d.m.Y', strtotime($endDate));
                                    $start_date_slash = date('d/m/Y', strtotime($startDate));
                                    $end_date_slash = date('d/m/Y', strtotime($endDate));
                                    $start_date_hyphen = date('d-m-Y', strtotime($startDate));
                                    $end_date_hyphen = date('d-m-Y', strtotime($endDate));
                                    $start_date_slash_mdy = date('m/d/Y', strtotime($startDate));
                                    $end_date_slash_mdy = date('m/d/Y', strtotime($endDate));
                                    //model call to update data in table 'Other numbers'
//                                $model = Model_Othernumber::update_other_numbers($_POST);
                                    $to = 'mega.radata@ptcl.net.pk';
                                    $to_name = 'MegaRAdata/PTCL';
                                    break;
                                case 13: // family request
                                    $start_date_dot = date('d.m.Y', strtotime($startDate));
                                    $end_date_dot = date('d.m.Y', strtotime($endDate));
                                    $start_date_slash = date('d/m/Y', strtotime($startDate));
                                    $end_date_slash = date('d/m/Y', strtotime($endDate));
                                    $start_date_hyphen = date('d-m-Y', strtotime($startDate));
                                    $end_date_hyphen = date('d-m-Y', strtotime($endDate));
                                    $start_date_slash_mdy = date('m/d/Y', strtotime($startDate));
                                    $end_date_slash_mdy = date('m/d/Y', strtotime($endDate));
                                    //model call to update data in table 'Other numbers'
                                    $to = 'naumana.manzoor@nadra.gov.pk';
                                    $to_name = 'Nadra';
                                    break;
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

                            $subject = str_replace("[case_number]", 'ADM-' . $reference_id, htmlspecialchars_decode($template_data['subject']));
                            if(!empty($_POST['inputSubNO']))
                            {
                                $subject = str_replace("[mobile_number]", $_POST['inputSubNO'], $subject);
                            }

                            $body = isset($_POST['inputSubNO']) ? str_replace("[mobile_number]", $_POST['inputSubNO'], $template_data['body_txt']) : $template_data['body_txt'];
                            $body = isset($_POST['inputCNIC']) ? str_replace("[cnic_number]", $_POST['inputCNIC'], $body) : $body;
                            $body = str_replace("[ptcl_number]", $requested_value, $body);
                            $body = str_replace("[case_number]", 'ADM-' . $reference_id, $body);

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

                            if ($company_name == 3 && ($request_type == 1 || $request_type == 2 || $request_type == 6)) {  //ufone

                                $body = strip_tags($body);
                                $text = str_ireplace('&lt;p&gt;', '', $body);
                                $body = str_ireplace('&lt;/p&gt;', '', $text);
                                /*if($request_type==2)
                                {
                                    $body= substr_replace( $body, 0, strlen($body)-1);
                                }*/
                                //create attachment start
                                $file_name = "/root/serverfiles/aies-home/aiesfiles/ufone_tem_files/" . $reference_id . ".txt";
                                $myfile = fopen($file_name, "w") or die("Unable to open file!");
                                fwrite($myfile, $body);
                                fclose($myfile);
                                $body = $reference_id . ".txt";
                                /* email send */
                                $email_staus = Helpers_Email::send_email($to, $to_name, $subject, $body, $file_name);
                                if (file_exists($file_name)) {
                                    unlink($file_name);
                                }
                            } else {
                                $email_staus = Helpers_Email::send_email($to, $to_name, $subject, $body);
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

                        echo json_encode(2);
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
            if (Helpers_Utilities::chek_role_access($this->role_id, 34) == 1 ||  $login_user->id ==719) {
                /* File Included */
                $this->template->content = View::factory('templates/user/nadra_request_sent_form');
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
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
            if (Helpers_Utilities::chek_role_access($this->role_id, 34) == 1 ||  $login_user->id ==719) {
                /* File Included */
                $this->template->content = View::factory('templates/user/familytree_request_sent_form');
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
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
            if (Helpers_Utilities::chek_role_access($this->role_id, 34) == 1 ||  $login_user->id ==719) {
                /* File Included */
                $this->template->content = View::factory('templates/user/travel_request_sent_form');
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
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
            if (Helpers_Utilities::chek_role_access($this->role_id, 34) == 1 ||  $login_user->id ==719) {
                /* File Included */
                $this->template->content = View::factory('templates/user/nadra_bulk_request_sent_form');
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
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
