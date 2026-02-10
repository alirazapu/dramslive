<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * module related with User Requests   
 */
class Controller_Userrequest extends Controller_Working {
    /*
     *  User Request (request)
     */

    public function action_request() {
        try {
            $this->template->content = View::factory('templates/user/some_thing_went_wrong');

//            $id = $this->request->param('id');
//            $id = Helpers_Utilities::remove_injection($id);
//            /* Posted Data */
//            $post = $this->request->post();
//
//            $_GET = Helpers_Utilities::remove_injection($_GET);
//            $_POST = Helpers_Utilities::remove_injection($_POST);
//            if (!empty($_GET['id']))
//                $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
//            else
//                $pid = '';
//            if (!empty($_POST['simownerid'])) {
//                $pid = $_POST['simownerid'];
//                //   print_r($pid); exit;
//            }
//            /* Set Session for post data carrying for the  ajax call */
//            Session::instance()->set('request_post', $post);
//            /* File Included */
//            include 'user_functions/request.inc';
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /* Admin Request Status (request_status) */

    public function action_request_status() {
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

    //ajax call for data
    public function action_ajaxuserrequeststatus() {
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

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->user_request_status($post, 'true');
                $profiles = $data->user_request_status($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $request_type_id = ( isset($item['user_request_type_id']) ) ? $item['user_request_type_id'] : 0;
                        $request_id = ( isset($item['request_id']) ) ? $item['request_id'] : 'NA';
                        $request_reference_id = ( isset($item['reference_id']) ) ? $item['reference_id'] : 0;
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 0;
                        $user_role_name = (isset($user_id) ) ? Helpers_Utilities::get_user_role_name($user_id) : 'N/A';
                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $username = ( isset($item['user_id']) ) ? '<br><span><b>'.Helpers_Utilities::get_username($item['user_id']). '</b></span>' : '--';
                        $user_request = ( isset($item['email_type_name']) ) ? $item['email_type_name'] : 'NA';
                        $user_request .= '<span><br><b>ID:';
                        $user_request .= ( isset($item['request_id']) ) ? $item['request_id'] : 'NA';
                        $user_request .= ' Ref#' . $request_reference_id;
                        $user_request .= '</b></span>';
                        $company_name = '<span><b>';
                        $company_name .= (isset($item['company_name']) && !empty($item['company_name']) ) ? Helpers_Utilities::get_companies_data($item['company_name'])->company_name : '--';
                        $company_name .= '</b></span><br><span>';
                        $company_name .= (isset($item['requested_value']) && !empty($item['requested_value']) ) ? $item['requested_value'] : '--';
                        $company_name .= '</span>';
                        $cnic_no='';
                        if($item['user_request_type_id']==5){
                           // $cnic= implode("-", str_split($item['requested_value'], 5));
                            $cnic_1=  substr_replace($item['requested_value'],'-', -8,0);
                            $cnic_2=  substr_replace($cnic_1,'-', -1,0);
                            $cnic_no= '<b> CNIC '. $cnic_2;
                        }
                        $requested_value = ( isset($item['requested_value']) ) ? $item['requested_value'] : 'NA';
                        $reply = ( isset($item['reply']) ) ? $item['reply'] : 0;
                        $concerned_person_id = ( isset($item['concerned_person_id']) ) ? $item['concerned_person_id'] : 'NA';
                        $enc_request_id = trim(Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt'));
                        if ($concerned_person_id > 0) {
                            $perons_name = ( isset($item['concerned_person_id']) ) ? Helpers_Person::get_person_name($item['concerned_person_id']) : 'NA';
                            $perons_name .= '</br>[';
                            $perons_name .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['concerned_person_id'], "encrypt")) . '" > View Profile </a>';
                            $perons_name .= ']';
                        } else {
                            $perons_name = " ";
                        }
                        //  $user_request = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type($item['user_request_type_id']) : 'NA';
                        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : 'NA';
                        $status = ( isset($item['status']) ) ? $item['status'] : 0;
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
                                $system_status = ( isset($item['processing_index']) ) ? $item['processing_index'] : 0;
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
                                            $imei_link = (isset($item['requested_value']) && !empty($item['requested_value']) ) ? $item['requested_value'] : '--';
                                            $system_status_flag .= '<a href="'.URL::site('User/upload_against_imei').'?imei=' . $imei_link . '" <span class="badge badge-pill badge-success">Check IMEI</span></a>';
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
                        $member_name_link = '<a class="btn btn-block btn-info btn-xs" href="' . URL::site('userrequest/request_status_detail/' . $enc_request_id) . ' " > View Detail </a> ';
                        $login_user = Auth::instance()->get_user();
                        $permission = Helpers_Utilities::get_user_permission($login_user->id);
                        /* if (($permission == 1 || $permission == 2) && ($request_type_id != 8) && ($status == 2)) {
                          $member_name_link .= '<a class="btn btn-block btn-warning btn-xs" style="background-color:#ff82b6" href="' . URL::site('userrequest/request_reread_status_detail/' . $enc_request_id) . '" > Req.Reread </a>';
                          } */
                        //Add user ID of Iqra and sana. manan
                        $userslist = [842, 137, 2031,1761, 2603];
                        if (($permission == 1 || $permission == 5) && (in_array($login_user->id, $userslist))) {
                            $member_name_link .= '<a class="btn btn-block btn-primary btn-xs"  href="#" onclick="UpdateRequestStatus(' . $item['request_id'] . ',' . $request_reference_id . ',' . $item['status'] . ',' . $item['processing_index'] . ')"> Update Status  </a>';
                            $member_name_link .= '<a  style="display: none;" class="btn btn-block btn-danger btn-xs"  href="#" onclick="deleteuserrequest(' . $enc_request_id . ',' . $enc_request_id . ')"> Delete Request  </a>';
                        }
                        $row = array(
                            $user_name . $username,
//                      $user_role_name,                        
                            $user_request,
                            $company_name.'<br>'.$cnic_no,
                            $perons_name,
                            $created_at,
                            $status_flag,
                            $reply_flag,
                            $system_status_flag,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
//            echo '<pre>';
//            print_r($ex);
//            exit;
        }
    }

    //update user request status 
    public function action_update_user_request_status() {
        //  
        try {
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $this->auto_render = FALSE;
            $user_id = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($user_id->id);
            if ($permission == 1  || $permission == 5 || $permission == 2) {
                $request_id = $post["request_id"];
                $request_status = $post["request_status"];
                $processing_index = $post["processing_index"];
                //print_r($post); exit;
                $update = 0;
                if (!empty($request_id)) {

                    $update = Model_Userrequest::update_user_request_status($request_id, $request_status, $processing_index);
                }
                return $update;
            } else {
                $this->redirect();
            }
        } catch (Exception $ex) {
            echo json_encode(-2);
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
                    $user = New Model_Userrequest();

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

    // Delete Rejeted user request
    public function action_deleterejecteduserrequest() {
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
                    $user = New Model_Userrequest();

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

    /* User Request Status Telenor (request_status_telenor) */

    public function action_request_status_telenor() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 24) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                /* Posted Data */
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['message']) && $post['message'] == 1) {
                    $message = 'Congratulation! Request Sent successfully';
                }
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('request_status_telenor_post', $post);
                /* File Included */
                include 'user_functions/request_status_telenor.inc';
            } else {

                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
    public function action_request_status_ufone() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 24) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                /* Posted Data */
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['message']) && $post['message'] == 1) {
                    $message = 'Congratulation! Request Sent successfully';
                }
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('request_status_ufone_post', $post);
                /* File Included */
                $this->template->content = View::factory('templates/user/request_status_ufone')
                ->bind('message' , $message)
                ->set('search_post', $post);
            } else {

                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxuserrequeststatustelenor() {
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
                $post = Session::instance()->get('request_status_telenor_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->user_request_status_telenor($post, 'true');
                $profiles = $data->user_request_status_telenor($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $request_type_id = ( isset($item['user_request_type_id']) ) ? $item['user_request_type_id'] : 0;

                        $request_id = ( isset($item['request_id']) ) ? $item['request_id'] : 0;
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 0;
                        $user_role_name = (isset($user_id) ) ? Helpers_Utilities::get_user_role_name($user_id) : 'N/A';

                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $user_request = ( isset($item['email_type_name']) ) ? $item['email_type_name'] : 'NA';
                        $user_request .= '<span> #<b>';
                        $user_request .= ( isset($item['request_id']) ) ? $item['request_id'] : 'NA';
                        $user_request .= '</b></span>';
                        $company_name = '<span><b>';
                        $company_name .= (isset($item['company_name']) && !empty($item['company_name']) ) ? Helpers_Utilities::get_companies_data($item['company_name'])->company_name : '--';
                        $company_name .= '</b></span><br><span>';
                        $company_name .= (isset($item['requested_value']) && !empty($item['requested_value']) ) ? $item['requested_value'] : '--';
                        $company_name .= '</span>';
                        $requested_value = ( isset($item['requested_value']) ) ? $item['requested_value'] : 'NA';
                        $reply = ( isset($item['reply']) ) ? $item['reply'] : 0;
                        $email_subject = ( isset($item['message_subject']) ) ? $item['message_subject'] : 'NA';
                        $email_body = ( isset($item['message_body']) ) ? $item['message_body'] : 'NA';
                        $days = ( isset($item['days']) ) ? $item['days'] : 'NA';
                        //  $user_request = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type($item['user_request_type_id']) : 'NA';
                        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : 'NA';
                        $status = ( isset($item['status']) ) ? $item['status'] : 0;
                        $system_status_flag = '';
                        switch ($status) {
                            case 0:
                                $status_flag = '<span class="label label-info">Request in Queue</span>';
                                break;
//                            case 1:
//                                $status_flag = '<span class="label label-info">Request Sent</span>';
//                                break;
                        }
                        $member_name_link = '<a style="cursor: pointer" onclick="requestmanualsent(' . $request_id . ')"><span class="fa fa-send"> Request Sent</span></a>';

                        $row = array(
                            $user_name,
//                      $user_role_name,                        
                            $user_request,
                            $company_name,
                            $days,
                            $email_subject,
                            $email_body,
                            $created_at,
                            $status_flag,
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
    public function action_ajaxuserrequeststatusufone() {
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
                $post = Session::instance()->get('request_status_ufone_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->user_request_status_ufone($post, 'true');
                $profiles = $data->user_request_status_ufone($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $request_type_id = ( isset($item['user_request_type_id']) ) ? $item['user_request_type_id'] : 0;

                        $request_id = ( isset($item['request_id']) ) ? $item['request_id'] : 0;
                        $enc_request_id = trim(Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt'));
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 0;
                        $user_role_name = (isset($user_id) ) ? Helpers_Utilities::get_user_role_name($user_id) : 'N/A';

                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $user_request = ( isset($item['email_type_name']) ) ? $item['email_type_name'] : 'NA';
                        $user_request .= '<span> #<b>';
                        $user_request .= ( isset($item['request_id']) ) ? $item['request_id'] : 'NA';
                        $user_request .= '</b></span>';
                        $company_name = '<span><b>';
                        $company_name .= (isset($item['company_name']) && !empty($item['company_name']) ) ? Helpers_Utilities::get_companies_data($item['company_name'])->company_name : '--';
                        $company_name .= '</b></span><br><span>';
                        $company_name .= (isset($item['requested_value']) && !empty($item['requested_value']) ) ? $item['requested_value'] : '--';
                        $company_name .= '</span>';
                        $requested_value = ( isset($item['requested_value']) ) ? $item['requested_value'] : 'NA';
                        $reply = ( isset($item['reply']) ) ? $item['reply'] : 0;
                        $email_subject = ( isset($item['message_subject']) ) ? $item['message_subject'] : 'NA';
                        $email_body = ( isset($item['message_body']) ) ? $item['message_body'] : 'NA';                        
                        //  $user_request = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type($item['user_request_type_id']) : 'NA';
                        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : 'NA';
                        $status = ( isset($item['status']) ) ? $item['status'] : 0;
                        $system_status_flag = '';
                        switch ($status) {
                            case 0:
                                $status_flag = '<span class="label label-info">Request in Queue</span>';
                                break;
//                            case 1:
//                                $status_flag = '<span class="label label-info">Request Sent</span>';
//                                break;
                        }
                        $member_name_link = '<a style="cursor: pointer" onclick="requestmanualsent(' . $request_id . ', \''. $enc_request_id.'\')"><span class="fa fa-send"> Request Sent</span></a>';

                        $row = array(
                            $user_name,
//                      $user_role_name,                        
                            $user_request,
                            $company_name,                            
                            $email_subject,
                            $email_body,
                            $created_at,
                            $status_flag,
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

    /* User Request Status Telenor More than 6 Months (request_status_telenor) */

    public function action_request_status_telenor_sixmonths() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 24) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                /* Posted Data */
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['message']) && $post['message'] == 1) {
                    $message = 'Congratulation! Request Sent successfully';
                }
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('request_status_telenor_post', $post);
                /* File Included */
                include 'user_functions/request_status_telenor_sixmonths.inc';
            } else {

                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxuserrequeststatustelenorsixmonths() {
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
                $post = Session::instance()->get('request_status_telenor_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;
//            }

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);


                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->user_request_status_telenor_sixmonths($post, 'true');
                $profiles = $data->user_request_status_telenor_sixmonths($post, 'false');


                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

//                         $days = ( isset($item['concerned_person_id']) ) ? $item['concerned_person_id'] : 'NA';
//                        echo '<pre>';
//                        print_r($days); exit;
                        $request_type_id = ( isset($item['user_request_type_id']) ) ? $item['user_request_type_id'] : 0;

                        $request_id = ( isset($item['request_id']) ) ? $item['request_id'] : 0;
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 0;
                        $user_role_name = (isset($user_id) ) ? Helpers_Utilities::get_user_role_name($user_id) : 'N/A';

                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $user_request = ( isset($item['email_type_name']) ) ? $item['email_type_name'] : 'NA';
                        $user_request .= '<span> #<b>';
                        $user_request .= ( isset($item['request_id']) ) ? $item['request_id'] : 'NA';
                        $user_request .= '</b></span>';
                        $company_name = '<span><b>';
                        $company_name .= (isset($item['company_name']) && !empty($item['company_name']) ) ? Helpers_Utilities::get_companies_data($item['company_name'])->company_name : '--';
                        $company_name .= '</b></span><br><span>';
                        $company_name .= (isset($item['requested_value']) && !empty($item['requested_value']) ) ? $item['requested_value'] : '--';
                        $company_name .= '</span>';
                        $requested_value = ( isset($item['requested_value']) ) ? $item['requested_value'] : 'NA';
                        $reply = ( isset($item['reply']) ) ? $item['reply'] : 0;
                        $email_subject = ( isset($item['message_subject']) ) ? $item['message_subject'] : 'NA';
                        $email_body = ( isset($item['message_body']) ) ? $item['message_body'] : 'NA';
                        $days = ( isset($item['days']) ) ? $item['days'] : 'NA';
                        //  $user_request = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type($item['user_request_type_id']) : 'NA';
                        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : 'NA';
                        $status = ( isset($item['status']) ) ? $item['status'] : 0;
                        $system_status_flag = '';
                        switch ($status) {
                            case 0:
                                $status_flag = '<span class="label label-info">Request in Queue</span>';
                                break;
                        }
                        $member_name_link = '<a style="cursor: pointer" onclick="requestmanualsent(' . $request_id . ')"><span class="fa fa-send"> Request Sent</span></a>';

                        $row = array(
                            $user_name,
//                      $user_role_name,
                            $user_request,
                            $company_name,
                            $days,
                            $email_subject,
                            $email_body,
                            $created_at,
                            $status_flag,
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


    /* User Request Status Telenor (request_status_familytree) */

    public function action_request_status_familytree() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 18) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                /* Posted Data */
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['message']) && $post['message'] == 1) {
                    $message = 'Congratulation! Request Sent successfully';
                }
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('request_status_familytree_post', $post);
                /* File Included */
                $this->template->content = View::factory('templates/user/request_status_familytree')
                        ->bind('message', $message)
                        ->set('search_post', $post);
            } else {

                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxuserrequeststatusfamilytree() {
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
                $post = Session::instance()->get('request_status_familytree_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->user_request_status_familytree($post, 'true');
                $profiles = $data->user_request_status_familytree($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $request_type_id = ( isset($item['user_request_type_id']) ) ? $item['user_request_type_id'] : 0;

                        $request_id = ( isset($item['request_id']) ) ? $item['request_id'] : 0;
                        $reference_id = ( isset($item['reference_id']) ) ? $item['reference_id'] : 0;
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 0;
                        $user_role_name = (isset($user_id) ) ? Helpers_Utilities::get_user_role_name($user_id) : 'N/A';

                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $user_request = ( isset($item['email_type_name']) ) ? $item['email_type_name'] : 'NA';
                        $user_request .= '<span><b> ID:' . $request_id . '</b></span>';
                        $user_request .= '<span><b> Ref#' . $reference_id . '</b></span>';
                        $company_name = '<span><b>';
                        $company_name .= (isset($item['company_name']) && !empty($item['company_name']) ) ? Helpers_Utilities::get_companies_data($item['company_name'])->company_name : '--';
                        $company_name .= '</b></span><br><span>';
                        $company_name .= (isset($item['requested_value']) && !empty($item['requested_value']) ) ? $item['requested_value'] : '--';
                        $company_name .= '</span>';
                        $requested_value = ( isset($item['requested_value']) ) ? $item['requested_value'] : 'NA';
                        $reply = ( isset($item['reply']) ) ? $item['reply'] : 0;
                        $email_subject = ( isset($item['message_subject']) ) ? $item['message_subject'] : 'NA';
                        // $email_body = ( isset($item['message_body']) ) ? $item['message_body'] : 'NA';
                        //  $user_request = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type($item['user_request_type_id']) : 'NA';
                        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : 'NA';
                        $status = ( isset($item['status']) ) ? $item['status'] : 0;
                        $system_status_flag = '';
                        switch ($status) {
                            case 0:
                                $status_flag = '<span class="label label-info">Request in Queue</span>';
                                break;
                        }
                        $member_name_link = '<a style="cursor: pointer" onclick="requestmanualsent(' . $request_id . ')"><span class="fa fa-send"> Send Request</span></a>';

                        $row = array(
                            $user_name,
//                      $user_role_name,                        
                            $user_request,
                            $company_name,
                            $email_subject,
                            $created_at,
                            $status_flag,
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

    /* User Request Status Telenor (request_status_telenor) */

    public function action_blocked_numbers() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 19) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                include 'user_functions/blocked_numbers.inc';
            } else {

                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $e) {
            
        }
    }

    //ajax call for data
    public function action_ajaxblockednumbers() {
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
                $post = Session::instance()->get('request_status_telenor_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->blocked_number($post, 'true');
                $profiles = $data->blocked_number($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $blocked_id = ( isset($item['blocked_id']) ) ? $item['blocked_id'] : 0;
                        $blocked_number_type = ( isset($item['blocked_number_type']) ) ? $item['blocked_number_type'] : 0;
                        $blocked_number_type_name = '';
                        switch ($blocked_number_type) {
                            case 1:
                                $blocked_number_type_name = 'Mobile Number';
                                break;
                            case 2:
                                $blocked_number_type_name = 'CNIC Number';
                                break;
                            case 3:
                                $blocked_number_type_name = 'IMEI Number';
                                break;
                        }
                        $blocked_value = ( isset($item['blocked_value']) ) ? $item['blocked_value'] : '';
                        $blocked_reason = ( isset($item['blocked_reason']) ) ? $item['blocked_reason'] : '';
                        $blocked_details = ( isset($item['blocked_details']) ) ? $item['blocked_details'] : '';
                        $blocked_by = ( isset($item['blocked_by']) ) ? Helpers_Utilities::get_user_name($item['blocked_by']) : 'Un-Known';
                        $blocked_time = ( isset($item['time_stamp']) ) ? $item['time_stamp'] : '';

                        $action = '<a href="#" onclick="DeleteBlockedNumber(' . $blocked_id . ')"><span class="fa fa-remove warning"> Delete</span></a>';
                        //$member_name_link = '<a style="cursor: pointer" onclick="requestmanualsent(' . $request_id . ')"><span class="fa fa-send"> Request Sent</span></a>';

                        $row = array(
                            $blocked_number_type_name,
                            $blocked_value,
                            $blocked_reason,
                            $blocked_details,
                            $blocked_by,
                            $blocked_time,
                            $action
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

    public function action_deleteblockednumber() {
        try {
            if (Auth::instance()->logged_in()) {
                $user_obj = Auth::instance()->get_user();
                $login_user_id = $user_obj->id;
                $blocked_id = (int) $this->request->param('id');
                $blocked_id = Helpers_Utilities::remove_injection($blocked_id);
                // print_r($blocked_id); exit;
                $user = New Model_Userrequest();
                $result = $user->delete_blocked_number($blocked_id, $login_user_id);
                //print_r($result); exit;
                echo 1;
            } else {
                return 0;
            }
        } catch (Exception $e) {
            
        }
    }

    //add new number in block list
    public function action_add_blocked_number() {
        if (Auth::instance()->logged_in()) {
            try {
                $_POST = Helpers_Utilities::remove_injection($_POST);
                $number_value = $_POST['numbervalue'];
                $count = Helpers_Utilities::check_number_exit_in_block_list($number_value);
            } catch (Exception $e) {
                
            }
            if (!empty($count)) {
                $this->redirect('userrequest/blocked_numbers/?message=2');
            }
            try {
                $this->auto_render = FALSE;
                $user_obj = Auth::instance()->get_user();
                $login_user_id = $user_obj->id;
                $user = New Model_Userrequest();
                $result = $user->add_blocked_number($_POST, $login_user_id);
            } catch (Exception $e) {
                
            }
            $this->redirect('userrequest/blocked_numbers/?message=1');
            echo 1;
        } else {
            return 0;
        }
    }

    /* Telenor Manual request send */

    public function action_manualrequestsend() {
        try {
            if (Auth::instance()->logged_in()) {
                $request_id = (int) $this->request->param('id');
                $request_id = Helpers_Utilities::remove_injection($request_id);

                $data = new Model_Email;
                $result = $data->email_sending_status($request_id, 1, 0);

                $query = DB::update('request_send_today')->set(array('total' => DB::expr('total + 1')))
                        ->where('request_priority', '=', 1)
                        ->and_where('company_name', '=', 13)
                        ->execute();

                $date_for_telco = date('Y-m-d'); //$date;
                $tel_report = "select * from telco_request_summary where date = '{$date_for_telco}'";
                $sql_telco = DB::query(Database::SELECT, $tel_report);
                $report_telco_result = $sql_telco->execute()->as_array();
//                echo '<pre>';
//                print_r($query); exit;

                if (!empty($report_telco_result)) {
                    $tel_co_mnc = array(13);
                    $telco_array = array();
                    foreach ($tel_co_mnc as $mnc) {
                        $company_mnc = '';
                        $send_high = 0;
                        $send_medium = 0;
                        $send_low = 0;
                        $total_send = 0;
                        $total_received = 0;
                        $tel_query = "SELECT * FROM request_send_today where company_name = " . $mnc;
                        $sql = DB::query(Database::SELECT, $tel_query);
                        $tel_result = $sql->execute()->as_array();

                        $company_mnc = $mnc;
                        foreach ($tel_result as $tel) {
                            switch ($tel['request_priority']) {
                                case 1:
                                    $send_low += $tel['total'];
                                    break;
                                case 2:
                                    $send_medium += $tel['total'];
                                    break;
                                case 3:
                                    $send_high += $tel['total'];
                                    break;
                            }
                        }
                        $t_date = '"' . $date_for_telco . '"';
                        $total_send = $send_high + $send_medium + $send_low;
                        //$telco_array[]='('. $t_date .', '. $company_mnc .', '. $send_high .', '. $send_medium .', '. $send_low .', '. $total_send. ')';
                        $query = DB::update('telco_request_summary')->set(array('send_high' => $send_high,
                                    'send_medium' => $send_medium, 'send_low' => $send_low, 'total_send' => $total_send))
                                ->where('date', '=', $date_for_telco)
                                ->and_where('company_mnc', '=', $mnc)
                                ->execute();
                    }
                    //$query = 'INSERT INTO telco_request_summary (`date`, `company_mnc`, `send_high`, `send_medium`, `send_low`, `total_send`) VALUES '.implode(',', $telco_array);
                    //sql = DB::query(Database::INSERT, $query)->execute(); 
                }
            }
        } catch (Exception $e) {
            
        }
    }

    /* auto Manual request send for family tree */

    public function action_automanualrequestsend() {

        if (Auth::instance()->logged_in()) {
            //company name = 13;
            // request type = 10;
            try {
                $request_id = (int) $this->request->param('id');
                $request_id = Helpers_Utilities::remove_injection($request_id);
                //print_r($request_id); exit;
                //$data = new Model_Email;
                //$result = $data->email_sending_status($request_id, 1, 0);

                Helpers_Person::fire_family_tree($request_id);
            } catch (Exception $e) {
                
            }
            /*
              $query = DB::update('request_send_today')->set(array('total' => DB::expr('total + 1')))
              ->where('request_priority', '=', 1)
              ->and_where('company_name', '=', 13)
              ->execute();

              $date_for_telco = date('Y-m-d'); //$date;
              $tel_report = "select * from telco_request_summary where date = '{$date_for_telco}'";
              $sql_telco = DB::query(Database::SELECT, $tel_report);
              $report_telco_result = $sql_telco->execute()->as_array();

              if (!empty($report_telco_result)) {
              $tel_co_mnc = array(13);
              $telco_array = array();
              foreach ($tel_co_mnc as $mnc) {
              $company_mnc = '';
              $send_high = 0;
              $send_medium = 0;
              $send_low = 0;
              $total_send = 0;
              $total_received = 0;
              $tel_query = "SELECT * FROM request_send_today where company_name = " . $mnc;
              $sql = DB::query(Database::SELECT, $tel_query);
              $tel_result = $sql->execute()->as_array();

              $company_mnc = $mnc;
              foreach ($tel_result as $tel) {
              switch ($tel['request_priority']) {
              case 1:
              $send_low += $tel['total'];
              break;
              case 2:
              $send_medium += $tel['total'];
              break;
              case 3:
              $send_high += $tel['total'];
              break;
              }
              }
              $t_date = '"' . $date_for_telco . '"';
              $total_send = $send_high + $send_medium + $send_low;
              //$telco_array[]='('. $t_date .', '. $company_mnc .', '. $send_high .', '. $send_medium .', '. $send_low .', '. $total_send. ')';
              $query = DB::update('telco_request_summary')->set(array('send_high' => $send_high,
              'send_medium' => $send_medium, 'send_low' => $send_low, 'total_send' => $total_send))
              ->where('date', '=', $date_for_telco)
              ->and_where('company_mnc', '=', $mnc)
              ->execute();
              }
              //$query = 'INSERT INTO telco_request_summary (`date`, `company_mnc`, `send_high`, `send_medium`, `send_low`, `total_send`) VALUES '.implode(',', $telco_array);
              //sql = DB::query(Database::INSERT, $query)->execute();
              }

             */
        }
    }

    /*
     *  User nadra requests (request_status)
     */

    public function action_nadra_requests() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);

            $access_nadra_request = Helpers_Profile::get_user_access_permission($login_user->id, 12);
            if ((Helpers_Utilities::chek_role_access($this->role_id, 16) == 1) && $access_nadra_request == 1) {
                /* Posted Data */
                $post = $this->request->post();
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            }             
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['message']) && $post['message'] == 1) {
                    $message = 'Congratulation! Request Sent successfully';
                }
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('request_status_post', $post);
                /* Excel export file  */
                include 'excel/nadra_request_cnic.inc';
                /* File Included */
                include 'user_functions/nadra_requests.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/some_thing_went_wrong');
        }
    }
    /*
     *  User family tree requests (request_status)
     */

    public function action_familytree_requests() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);

            $access_nadra_request = Helpers_Profile::get_user_access_permission($login_user->id, 12);
            if ((Helpers_Utilities::chek_role_access($this->role_id, 16) == 1) && $access_nadra_request == 1) {
                /* Posted Data */
                $post = $this->request->post();
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;
//            }
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['message']) && $post['message'] == 1) {
                    $message = 'Congratulation! Request Sent successfully';
                }
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('request_status_post', $post);

                /* Excel export file  */
                include 'excel/familytree_request_cnic.inc';
                /* File Included */
                include 'user_functions/familytree_requests.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/some_thing_went_wrong');
        }
    }

    //ajax call for data
    public function action_ajaxusernadrarequests() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('request_status_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->user_nadra_requests($post, 'true');
                $profiles = $data->user_nadra_requests($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $request_id = ( isset($item['request_id']) ) ? $item['request_id'] : 0;
                        $request_id_en = "'" . Helpers_Utilities::encrypted_key($request_id, 'encrypt') . "'";
                        $project_id = ( isset($item['project_id']) ) ? $item['project_id'] : 1;
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 0;

                        $user_role_name = (isset($user_id) ) ? Helpers_Utilities::get_user_role_name($user_id) : 'N/A';

                        $user_name = ( isset($item['name']) ) ? $item['name'] : 'NA';
                        $region_name = (!empty($item['region_id']) ) ? Helpers_Utilities::get_region($item['region_id']) : 'HQ';
                        $user_request = ( isset($item['email_type_name']) ) ? $item['email_type_name'] : 'NA';
                        $user_request .= '<span> #<b>';
                        $user_request .= ( isset($item['request_id']) ) ? $item['request_id'] : 'NA';
                        $user_request .= '</b></span>';
                        $requested_value = ( isset($item['requested_value']) ) ? $item['requested_value'] : 'NA';
                        //$reason = ( isset($item['reason']) ) ? $item['reason'] : 'NA';
                        $concerned_person_id = ( isset($item['concerned_person_id']) ) ? $item['concerned_person_id'] : 'NA';
                        if ($concerned_person_id > 0) {
                            $perons_name = ( isset($item['concerned_person_id']) ) ? Helpers_Person::get_person_name($item['concerned_person_id']) : 'NA';
                            $perons_name .= '</br>[';
                            $perons_name .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['concerned_person_id'], "encrypt")) . '" > View Profile </a>';
                            $perons_name .= ']';
                        } else {
                            $perons_name = "NA";
                        }
                        //  $user_request = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type($item['user_request_type_id']) : 'NA';
                        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : 'NA';
                        $status = ( isset($item['status']) ) ? $item['status'] : 0;

                        // print_r($item); exit;
                        switch ($status) {
                            case 1:
                                $status_flag = '<span class="label label-info">Request Pending</span>';
                                break;
                            case 2:
                                $status_flag = '<span class="label label-success">Request Completed</span>';
                                break;
                            default :
                                $status_flag = '';
                        }

                        if ($status != 2) {
                            $searchString = ',';
                            if (strpos($project_id, $searchString) !== false) {
                                $myArray = explode(',', $project_id);
                                $project_id = $myArray[1];
                            }
                            $member_name_link1 = '<a href="#" onclick="findphonenumber(' . $requested_value . ',' . $request_id . ',' . $project_id . ',' . $user_id . ')">Proceed </a>';
                            $member_name_link2 = '<a href="' . URL::site('userrequest/request_status_detail/' . Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt')) . '" > View Detail </a>';
                            $member_name_link = $member_name_link1 . ", " . $member_name_link2;
                        } else {
                            $member_name_link = '<a href="' . URL::site('userrequest/request_status_detail/' . Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt')) . '" > View Detail </a>';
                            $member_name_link .= ' , ';
                            $member_name_link .= '<a href="#" onclick="requeueverisys(' . $request_id_en . ')">ReQueue </a>';
                        }

                        $row = array(
                            $user_name,
                            $region_name,
//                      $user_role_name,                        
                            $user_request,
                            $requested_value,
                            $perons_name,
                            $created_at,
                            $status_flag,
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
    public function action_ajaxuserfamilytreerequests() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('request_status_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;
//            }

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_Userrequest;
                $rows_count = $data->user_familytree_requests($post, 'true');
                $profiles = $data->user_familytree_requests($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $request_id = ( isset($item['request_id']) ) ? $item['request_id'] : 0;
                        $request_id_en = "'" . Helpers_Utilities::encrypted_key($request_id, 'encrypt') . "'";
                        $project_id = ( isset($item['project_id']) ) ? $item['project_id'] : 1;
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 0;

                        $user_role_name = (isset($user_id) ) ? Helpers_Utilities::get_user_role_name($user_id) : 'N/A';

                        $user_name = ( isset($item['name']) ) ? $item['name'] : 'NA';
                        $region_name = (!empty($item['region_id']) ) ? Helpers_Utilities::get_region($item['region_id']) : 'HQ';
                        $user_request = ( isset($item['email_type_name']) ) ? $item['email_type_name'] : 'NA';
                        $user_request .= '<span> #<b>';
                        $user_request .= ( isset($item['request_id']) ) ? $item['request_id'] : 'NA';
                        $user_request .= '</b></span>';
                        $requested_value = ( isset($item['requested_value']) ) ? $item['requested_value'] : 'NA';
                        //$reason = ( isset($item['reason']) ) ? $item['reason'] : 'NA';
                        $concerned_person_id = ( isset($item['concerned_person_id']) ) ? $item['concerned_person_id'] : 'NA';
                        if ($concerned_person_id > 0) {
                            $perons_name = ( isset($item['concerned_person_id']) ) ? Helpers_Person::get_person_name($item['concerned_person_id']) : 'NA';
                            $perons_name .= '</br>[';
                            $perons_name .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['concerned_person_id'], "encrypt")) . '" > View Profile </a>';
                            $perons_name .= ']';
                        } else {
                            $perons_name = "NA";
                        }
                        //  $user_request = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type($item['user_request_type_id']) : 'NA';
                        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : 'NA';
                        $status = ( isset($item['status']) ) ? $item['status'] : 0;

                        // print_r($item); exit;
                        switch ($status) {
                            case 1:
                                $status_flag = '<span class="label label-info">Request Pending</span>';
                                break;
                            case 2:
                                $status_flag = '<span class="label label-success">Request Completed</span>';
                                break;
                            default :
                                $status_flag = '';
                        }

                        if ($status != 2) {
                            $searchString = ',';
                            if (strpos($project_id, $searchString) !== false) {
                                $myArray = explode(',', $project_id);
                                $project_id = $myArray[1];
                            }
                            $member_name_link1 = '<a href="#" onclick="findphonenumber(' . $requested_value . ',' . $request_id . ',' . $project_id . ',' . $user_id . ')">Proceed </a>';
                            $member_name_link2 = '<a href="' . URL::site('userrequest/request_status_detail/' . Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt')) . '" > View Detail </a>';
                            $member_name_link = $member_name_link1 . ", " . $member_name_link2;
                        } else {
                            $member_name_link = '<a href="' . URL::site('userrequest/request_status_detail/' . Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt')) . '" > View Detail </a>';
                            $member_name_link .= ' , ';
                            $member_name_link .= '<a href="#" onclick="requeueverisys(' . $request_id_en . ')">ReQueue </a>';
                        }

                        $row = array(
                            $user_name,
                            $region_name,
//                      $user_role_name,
                            $user_request,
                            $requested_value,
                            $perons_name,
                            $created_at,
                            $status_flag,
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

    //bulk verisys respond
    public function action_bulk_nadra_requests() {
        // try {
        $DB = Database::instance();
        $login_user = Auth::instance()->get_user();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);

        $access_nadra_request = Helpers_Profile::get_user_access_permission($login_user->id, 12);
        if ($permission == 1 || $permission == 2 || ($permission == 4 && $access_nadra_request == 1)) {
            /* Posted Data */
            $post = $this->request->post();

            if (isset($_GET)) {
                $post = array_merge($post, $_GET);
            }
            $post = Helpers_Utilities::remove_injection($post);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('bulkresponse_post', $post);
            $this->template->content = View::factory('templates/user/nadra_requests_bulkresponse')
                    ->set('search_post', $post);
        } else {
            $this->redirect('user/access_denied');
        }
//        } catch (Exception $ex) {
//            $this->template->content = View::factory('templates/user/some_thing_went_wrong');
//        }
    }
    //bulk verisys respond
    public function action_bulk_familytree_requests() {
        // try {
        $DB = Database::instance();
        $login_user = Auth::instance()->get_user();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);

        $access_nadra_request = Helpers_Profile::get_user_access_permission($login_user->id, 12);
        if ($permission == 1 || $permission == 2 || ($permission == 4 && $access_nadra_request == 1)) {
            /* Posted Data */
            $post = $this->request->post();

            if (isset($_GET)) {
                $post = array_merge($post, $_GET);
            }
            $post = Helpers_Utilities::remove_injection($post);
            /* Set Session for post data carrying for the  ajax call */

            Session::instance()->set('bulkresponse_post', $post);
            $this->template->content = View::factory('templates/user/familytree_requests_bulkresponse')
                    ->set('search_post', $post);
        } else {
            $this->redirect('user/access_denied');
        }
//        } catch (Exception $ex) {
//            $this->template->content = View::factory('templates/user/some_thing_went_wrong');
//        }
    }

    //ajax call for data
    public function action_ajaxusernadrarequestsbulk() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('request_status_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->nadra_verisys_bulk($post, 'true');
                $profiles = $data->nadra_verisys_bulk($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $row_id = ( isset($item['row_id']) ) ? $item['row_id'] : '0';
                        $image_name = ( isset($item['image_name']) ) ? $item['image_name'] : '0';
                        $cnic_number = ( isset($item['vcnic']) ) ? $item['vcnic'] : '';
                        //$upload_by = ( isset($item['uploaded_by_user']) ) ? $item['uploaded_by_user'] : 0;
                        $upload_by = ( isset($item['uploaded_by_user']) ) ? Helpers_Utilities::get_user_name($item['uploaded_by_user']) : 'NA';
                        $upload_date = ( isset($item['upload_date']) ) ? $item['upload_date'] : 0;
                        $attachment_status = ( isset($item['attachment_status']) ) ? $item['attachment_status'] : 0;
                        $status_flag = '';
                        switch ($attachment_status) {
                            case 0:
                                $status_flag = '<span class="label label-primary">Waiting Upload</span>';
                                break;
                            case 1:
                                $status_flag = '<span class="label label-success">Upload Success</span>';
                                break;
                            case 2:
                                $status_flag = '<span class="label label-danger">Upload Fail</span>';
                                break;
                            case 3:
                                $status_flag = '<span class="label label-warning">Request Not Found</span>';
                                break;
                            default :
                                $status_flag = '<span class="label label-default">Un-Know</span>';
                                break;
                        }
                        $member_name_link = 'NO Action';
                        if ($attachment_status == 3) {
                            $member_name_link = '<a class="btn btn-block btn-danger btn-xs" onclick="javascript:delete_record(' . $row_id . ')">Delete Record</a>';
                        }

                        $row = array(
                            $image_name,
                            $cnic_number,
                            $upload_by,
                            $upload_date,
                            $status_flag,
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
    public function action_ajaxuserfamilytreerequestsbulk() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('request_status_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;
//            }

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->nadra_familytree_bulk($post, 'true');
                $profiles = $data->nadra_familytree_bulk($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $row_id = ( isset($item['row_id']) ) ? $item['row_id'] : '0';
                        $image_name = ( isset($item['image_name']) ) ? $item['image_name'] : '0';
                        $cnic_number = ( isset($item['vcnic']) ) ? $item['vcnic'] : '';
                        //$upload_by = ( isset($item['uploaded_by_user']) ) ? $item['uploaded_by_user'] : 0;
                        $upload_by = ( isset($item['uploaded_by_user']) ) ? Helpers_Utilities::get_user_name($item['uploaded_by_user']) : 'NA';
                        $upload_date = ( isset($item['upload_date']) ) ? $item['upload_date'] : 0;
                        $attachment_status = ( isset($item['attachment_status']) ) ? $item['attachment_status'] : 0;
                        $status_flag = '';
                        switch ($attachment_status) {
                            case 0:
                                $status_flag = '<span class="label label-primary">Waiting Upload</span>';
                                break;
                            case 1:
                                $status_flag = '<span class="label label-success">Upload Success</span>';
                                break;
                            case 2:
                                $status_flag = '<span class="label label-danger">Upload Fail</span>';
                                break;
                            case 3:
                                $status_flag = '<span class="label label-warning">Request Not Found</span>';
                                break;
                            default :
                                $status_flag = '<span class="label label-default">Un-Know</span>';
                                break;
                        }
                        $member_name_link = 'NO Action';
                        if ($attachment_status == 3) {
                            $member_name_link = '<a class="btn btn-block btn-danger btn-xs" onclick="javascript:delete_record(' . $row_id . ')">Delete Record</a>';
                        }

                        $row = array(
                            $image_name,
                            $cnic_number,
                            $upload_by,
                            $upload_date,
                            $status_flag,
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
    
        /*  Requests Response Travel History */

    public function action_travelhistory_requests() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);

            $access_nadra_request = Helpers_Profile::get_user_access_permission($login_user->id, 12);
            if ((Helpers_Utilities::chek_role_access($this->role_id, 16) == 1) && $access_nadra_request == 1) {
                /* Posted Data */
                $post = $this->request->post();
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            }             
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['message']) && $post['message'] == 1) {
                    $message = 'Congratulation! Request Sent successfully';
                }
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('request_status_post', $post);
                /* Excel export file  */
                include 'excel/travelhistory_request_cnic.inc';
                $this->template->content = View::factory('templates/user/travelhistory')
                                            ->bind('message' , $message)
                                            ->set('search_post', $post);
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/some_thing_went_wrong');
        }
    }

    //ajax call for data
    public function action_ajaxusertravelhistory() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('request_status_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->user_travelhistory_requests($post, 'true');
                $profiles = $data->user_travelhistory_requests($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $request_id = ( isset($item['request_id']) ) ? $item['request_id'] : 0;
                        $request_id_en = "'" . Helpers_Utilities::encrypted_key($request_id, 'encrypt') . "'";
                        $project_id = ( isset($item['project_id']) ) ? $item['project_id'] : 1;
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 0;

                        $user_role_name = (isset($user_id) ) ? Helpers_Utilities::get_user_role_name($user_id) : 'N/A';

                        $user_name = ( isset($item['name']) ) ? $item['name'] : 'NA';
                        $region_name = (!empty($item['region_id']) ) ? Helpers_Utilities::get_region($item['region_id']) : 'HQ';
                        $user_request = ( isset($item['email_type_name']) ) ? $item['email_type_name'] : 'NA';
                        $user_request .= '<span> #<b>';
                        $user_request .= ( isset($item['request_id']) ) ? $item['request_id'] : 'NA';
                        $user_request .= '</b></span>';
                        $requested_value = ( isset($item['requested_value']) ) ? $item['requested_value'] : 'NA';
                        //$reason = ( isset($item['reason']) ) ? $item['reason'] : 'NA';
                        $concerned_person_id = ( isset($item['concerned_person_id']) ) ? $item['concerned_person_id'] : 'NA';
                        if ($concerned_person_id > 0) {
                            $perons_name = ( isset($item['concerned_person_id']) ) ? Helpers_Person::get_person_name($item['concerned_person_id']) : 'NA';
                            $perons_name .= '</br>[';
                            $perons_name .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['concerned_person_id'], "encrypt")) . '" > View Profile </a>';
                            $perons_name .= ']';
                        } else {
                            $perons_name = "NA";
                        }
                        //  $user_request = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type($item['user_request_type_id']) : 'NA';
                        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : 'NA';
                        $status = ( isset($item['status']) ) ? $item['status'] : 0;

                        // print_r($item); exit;
                        switch ($status) {
                            case 1:
                                $status_flag = '<span class="label label-info">Request Pending</span>';
                                break;
                            case 2:
                                $status_flag = '<span class="label label-success">Request Completed</span>';
                                break;
                            default :
                                $status_flag = '';
                        }

                        if ($status != 2) {
                            $searchString = ',';
                            if (strpos($project_id, $searchString) !== false) {
                                $myArray = explode(',', $project_id);
                                $project_id = $myArray[1];
                            }
                            $member_name_link1 = '<a href="#" onclick="findphonenumber(' . $requested_value . ',' . $request_id . ',' . $project_id . ',' . $user_id . ')">Proceed </a>';
                            $member_name_link2 = '<a href="' . URL::site('userrequest/request_status_detail/' . Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt')) . '" > View Detail </a>';
                            $member_name_link = $member_name_link1 . ", " . $member_name_link2;
                        } else {
                            $member_name_link = '<a href="' . URL::site('userrequest/request_status_detail/' . Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt')) . '" > View Detail </a>';
                            $member_name_link .= ' , ';
                            $member_name_link .= '<a href="#" onclick="requeueverisys(' . $request_id_en . ')">ReQueue </a>';
                        }

                        $row = array(
                            $user_name,
                            $region_name,
//                      $user_role_name,                        
                            $user_request,
                            $requested_value,
                            $perons_name,
                            $created_at,
                            $status_flag,
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
    
        //bulk verisys respond
    public function action_bulk_travelhistory() {
        // try {
        $DB = Database::instance();
        $login_user = Auth::instance()->get_user();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);

        $access_nadra_request = Helpers_Profile::get_user_access_permission($login_user->id, 12);
        if ($permission == 1 || $permission == 2 || ($permission == 4 && $access_nadra_request == 1)) {
            /* Posted Data */
            $post = $this->request->post();

            if (isset($_GET)) {
                $post = array_merge($post, $_GET);
            }
            $post = Helpers_Utilities::remove_injection($post);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('bulkresponse_post', $post);
            $this->template->content = View::factory('templates/user/travelhistory_bulkresponse')
                    ->set('search_post', $post);
        } else {
            $this->redirect('user/access_denied');
        }
//        } catch (Exception $ex) {
//            $this->template->content = View::factory('templates/user/some_thing_went_wrong');
//        }
    }

    //ajax call for data
    public function action_ajaxbulktravelhistory() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('request_status_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->travelhistory_bulk_upload($post, 'true');
                $profiles = $data->travelhistory_bulk_upload($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $row_id = ( isset($item['row_id']) ) ? $item['row_id'] : '0';
                        $image_name = ( isset($item['image_name']) ) ? $item['image_name'] : '0';
                        $cnic_number = ( isset($item['vcnic']) ) ? $item['vcnic'] : '';
                        //$upload_by = ( isset($item['uploaded_by_user']) ) ? $item['uploaded_by_user'] : 0;
                        $upload_by = ( isset($item['uploaded_by_user']) ) ? Helpers_Utilities::get_user_name($item['uploaded_by_user']) : 'NA';
                        $upload_date = ( isset($item['upload_date']) ) ? $item['upload_date'] : 0;
                        $attachment_status = ( isset($item['attachment_status']) ) ? $item['attachment_status'] : 0;
                        $status_flag = '';
                        switch ($attachment_status) {
                            case 0:
                                $status_flag = '<span class="label label-primary">Waiting Upload</span>';
                                break;
                            case 1:
                                $status_flag = '<span class="label label-success">Upload Success</span>';
                                break;
                            case 2:
                                $status_flag = '<span class="label label-danger">Upload Fail</span>';
                                break;
                            case 3:
                                $status_flag = '<span class="label label-warning">Request Not Found</span>';
                                break;
                            default :
                                $status_flag = '<span class="label label-default">Un-Know</span>';
                                break;
                        }
                        $member_name_link = 'NO Action';
                        if ($attachment_status == 3) {
                            $member_name_link = '<a class="btn btn-block btn-danger btn-xs" onclick="javascript:delete_record(' . $row_id . ')">Delete Record</a>';
                        }

                        $row = array(
                            $image_name,
                            $cnic_number,
                            $upload_by,
                            $upload_date,
                            $status_flag,
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

    /*
     *  User rejected requests (request_status)
     */

    public function action_rejected_requests() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            if (Helpers_Utilities::chek_role_access($this->role_id, 17) == 1) {
                /* Posted Data */
                $post = $this->request->post();
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

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
                include 'user_functions/rejected_requests.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxuserrejectedrequests() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('user_request_status_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->user_rejected_requests($post, 'true');
                $profiles = $data->user_rejected_requests($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $request_id = ( isset($item['request_id']) ) ? $item['request_id'] : 'NA';
                        $reference_id = ( isset($item['reference_id']) ) ? $item['reference_id'] : 'NA';
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 0;

                        $user_role_name = (isset($user_id) ) ? Helpers_Utilities::get_user_role_name($user_id) : 'N/A';

                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $user_request = ( isset($item['email_type_name']) ) ? $item['email_type_name'] : 'NA';
                        //$user_request = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type_name($item['user_request_type_id']) : 'NA';
                        $user_request .= '<span><b> ID:';
                        $user_request .= $request_id;
                        $user_request .= ' Ref#' . $reference_id;
                        $user_request .= '</b></span>';
                        $requested_value = ( isset($item['requested_value']) ) ? $item['requested_value'] : 'NA';
                        //$reason = ( isset($item['reason']) ) ? $item['reason'] : 'NA';
                        $concerned_person_id = ( isset($item['concerned_person_id']) ) ? $item['concerned_person_id'] : 'NA';
                        $enc_request_id1 = Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt');
                        if ($concerned_person_id > 0) {
                            $perons_name = ( isset($item['concerned_person_id']) ) ? Helpers_Person::get_person_name($item['concerned_person_id']) : 'NA';
                            $perons_name .= '</br>[';
                            $perons_name .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['concerned_person_id'], "encrypt")) . '" > View Profile </a>';
                            $perons_name .= ']';
                        } else {
                            $perons_name = "NA";
                        }
                        //  $user_request = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type($item['user_request_type_id']) : 'NA';
                        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : 'NA';
                        $status = ( isset($item['status']) ) ? $item['status'] : 4;

                        // print_r($item); exit;
                        switch ($status) {
                            case 4:
                                $status_flag = '<span class="label label-danger">Request Rejected</span>';
                                break;
                        }

                        $member_name_link1 = '<a href="#" onclick="resendrequest(' . $request_id . ')">Resend </a>';
                        $member_name_link2 = '<a href="' . URL::site('userrequest/request_status_detail/' . Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt')) . '" > View Detail </a>';
                        //    $member_delete_link = '<a href="' . URL::site('userrequest/delete_request/?request_id=' . Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt')) . '&page=rejected" > Delete Request </a>';
                        $member_delete_link = '<a  href="#" onclick="deleterejecteduserrequest(\'' . $enc_request_id1 . '\',\'' . $enc_request_id1 . '\')"> Delete Request  </a>';
                        $member_name_link = $member_name_link1 . ", " . $member_name_link2 . "," . $member_delete_link;


                        $row = array(
                            $user_name,
//                      $user_role_name,                        
                            $user_request,
                            $requested_value,
                            $perons_name,
                            $created_at,
                            $status_flag,
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

    /*
     *  User Request Status Detail (request_status_detail)
     */

    public function action_request_status_detail() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($login_user->id);
            if (Helpers_Utilities::chek_role_access($this->role_id, 14) == 1) {
                if (Auth::instance()->logged_in()) {
                    $encrypted_id = $this->request->param('id');
                    $encrypted_id = Helpers_Utilities::remove_injection($encrypted_id);
                    $id = Helpers_Utilities::encrypted_key($encrypted_id, 'decrypt');                    
                    if (isset($id) && ($id != NULL)) {
                        $user_obj = Auth::instance()->get_user();
                        $data = new Model_Userrequest;
                        
                        if(!empty($this->request->param('id2')) && $this->request->param('id2')=='ad')
                            $data1 = $data->viewad($id);
                        else
                            $data1 = $data->view($id);
                        
                        if (isset($data1) && ($data1 != NULL)) {
                            $view = View::factory('templates/user/request_status_detail')
                                    ->set('results', $data1)
                                    ->set('user_id', $user_obj->id);
                            $this->template->content = $view;
                        } else {
                            
                            $this->template->content = View::factory('templates/user/access_denied');
                        }
                    } else {
                        
                        $this->template->content = View::factory('templates/user/access_denied');
                    }
                } else {
                    header("Location:" . URL::base());
                    exit;
                }
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //Branchless Banking Request Status Detail
    public function action_request_status_detail_banking() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            if (Auth::instance()->logged_in()) {
                $encrypted_id = $this->request->param('id');
                $encrypted_id = Helpers_Utilities::remove_injection($encrypted_id);
                $id = Helpers_Utilities::encrypted_key($encrypted_id, 'decrypt');
                if (isset($id) && ($id != NULL)) {
                    $model_reference = new Model_Userrequest;
                    $request_data = $model_reference->request_detail_ctfu($id);
                    $view = View::factory('templates/user/request_status_detail_banking')
                            ->set('request_data', $request_data)
                            ->set('user_id', $login_user->id);
                    $this->template->content = $view;
                } else {
                    $this->template->content = View::factory('templates/user/access_denied');
                }
            } else {
                header("Location:" . URL::base());
                exit;
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    public function action_request_reread_status_detail() {

        $DB = Database::instance();
        $login_user = Auth::instance()->get_user();


        try {
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
        } catch (Exception $e) {
            $this->redirect();
        }
        if ($permission == 1 || $permission == 2 || $permission == 3 || $permission == 4) {
            if (Auth::instance()->logged_in()) {
                try {
                    $id_encrypted = $this->request->param('id');
                    $id_encrypted = Helpers_Utilities::remove_injection($id_encrypted);
                    $id = Helpers_Utilities::encrypted_key($id_encrypted, 'decrypt');
                } catch (Exception $e) {
                    
                }
                if (isset($id) && ($id != NULL)) {

                    /* email read */
                    Helpers_Email::receive_single_email($id);
                    /* email read */

                    $user_obj = Auth::instance()->get_user();
                    $data = new Model_Userrequest;
                    $data1 = $data->view($id);
                    if (isset($data1) && ($data1 != NULL)) {
                        $view = View::factory('templates/user/request_status_detail')
                                ->set('results', $data1)
                                ->set('user_id', $user_obj->id);
                        $this->template->content = $view;
                    } else {
                        $this->redirect();
                    }
                } else {

                    $this->redirect();
                }
            } else {
                $this->redirect();
            }
        } else {
            $this->redirect('user/access_denied');
        }
    }

    /*
     * Request Data for grey user
     */

    public function action_request_data() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
        } catch (Exception $e) {
            
        }
        if ($permission == 1 || $login_user->id == 183) {
            /* Posted Data */
            $post = $this->request->post();
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('search_post', $post);
            /* File Included */
            include 'user_functions/request_data.inc';
        } else {
            $this->redirect('user/access_denied');
            //$this->redirect('userdashboard/dashboard/' . $login_user->id .'?accessmessage=' .$access_message);
        }
    }

    /*
     * Request Schedular
     */

    public function action_request_schedular() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 20) == 1) {
                /* Posted Data */
                $post = $this->request->post();
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('search_post', $post);

                /* File Included */
                include 'user_functions/request_schedular.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data of request Schedular
    public function action_ajaxrequestschedular() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('user_request_status_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->request_status($post, 'true');
                $profiles = $data->request_status($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $request_id = ( isset($item['request_id']) ) ? $item['request_id'] : 0;
                        $reference_id = ( isset($item['reference_id']) ) ? $item['reference_id'] : '--';
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 0;
                        //$user_role_name=(isset($user_id) ) ? Helpers_Utilities::get_user_role_name($user_id) : 'N/A';
                        $user_posting = ( isset($item['user_id']) ) ? Helpers_Profile::get_user_region_district($item['user_id']) : 'NA';

                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $user_request = ( isset($item['email_type_name']) ) ? $item['email_type_name'] : 'NA';
                        $user_request .= '<span><b> ID:' . $request_id . '</b></span>';
                        $user_request .= '<span><b> Ref#' . $reference_id . '</b></span>';
                        $company_name = (isset($item['company_name']) && !empty($item['company_name']) ) ? Helpers_Utilities::get_companies_data($item['company_name'])->company_name : '--';
                        $requested_value = ( isset($item['requested_value']) ) ? $item['requested_value'] : 'NA';
                        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : 'NA';

                        $requested_priority = '<select class="form-control" name="priority" id="priority-' . $item['request_id'] . '" onchange="javascript:ChangePriority(' . $item['request_id'] . ')">                                             
                                            <option value="1" ' . ((isset($item['request_priority']) && $item['request_priority'] == 1 ) ? "selected" : "") . '> Normal</option>
                                            <option value="2" ' . ((isset($item['request_priority']) && $item['request_priority'] == 2 ) ? "selected" : "") . '> Medium</option>
                                            <option value="3" ' . ((isset($item['request_priority']) && $item['request_priority'] == 3 ) ? "selected" : "") . '> High</option>
                                            <option value="4" ' . ((isset($item['request_priority']) && $item['request_priority'] == 4 ) ? "selected" : "") . '> Reject</option>
                                         </select>';
                        //print_r($requested_priority); exit;
                        //$requested_priority = ( isset($item['request_priority']) ) ? $item['request_priority'] : 'NA';                               

                        $row = array(
                            $user_name,
                            $user_posting,
                            $user_request,
                            $company_name,
                            $requested_value,
                            $created_at,
                            $requested_priority
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            //exit();
        } catch (Exception $ex) {
            
        }
    }

    /*
     * Data parsing queue
     */

    public function action_parsing_queue() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 21) == 1) {
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('search_post', $post);

                /* File Included */
                include 'user_functions/parsing_queue.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data of request Queue
    public function action_ajaxrequestqueue() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('user_request_status_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->request_parsing_status($post, 'true');
                $profiles = $data->request_parsing_status($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $request_id = ( isset($item['request_id']) ) ? $item['request_id'] : 0;
                        $reference_id = ( isset($item['reference_id']) ) ? $item['reference_id'] : 0;
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 0;
                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $user_posting = ( isset($item['user_id']) ) ? Helpers_Profile::get_user_region_district($item['user_id']) : 'NA';
                        $user_request = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type_name($item['user_request_type_id']) : 'NA';
                        $user_request .= '<span><b>ID:';
                        $user_request .= $request_id;
                        $user_request .= ' Ref#' . $reference_id;
                        $user_request .= '</b></span>';
                        $sending_date = ( isset($item['sending_date']) ) ? $item['sending_date'] : 'NA';
                        $company_name = '<span><b>';
                        $company_name .= (isset($item['company_name']) && !empty($item['company_name']) ) ? Helpers_Utilities::get_companies_data($item['company_name'])->company_name : '--';
                        $company_name .= '</b></span><br><span>';
                        $company_name .= (isset($item['requested_value']) && !empty($item['requested_value']) ) ? $item['requested_value'] : 0;
                        $company_name .= '</span>';
                        $receiving_date = ( isset($item['message_date']) ) ? $item['message_date'] : 'NA';
                        $processing_index = ( isset($item['processing_index']) ) ? $item['processing_index'] : 'NA';
                        switch ($processing_index) {
                            case 1:
                                $system_status_flag = '<span class="badge badge-pill badge-info">Format Error</span>';
                                break;
                            case 2:
                                $system_status_flag = '<span class="badge badge-pill badge-warning">No Data</span>';
                                break;
                            case 3:
                                $system_status_flag = '<span class="badge badge-pill badge-danger">Parsing Error</span>';
                                break;
                            case 4:
                                $system_status_flag = '<span class="badge badge-pill badge-success">Waiting Parsing</span>';
                                break;
                            case 5:
                                $system_status_flag = '<span class="badge badge-pill badge-success">Parsing completed</span>';
                                break;
                        }

                        $row = array(
                            $user_name,
                            $user_posting,
                            $user_request,
                            $sending_date,
                            $company_name,
                            $receiving_date,
                            $system_status_flag
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            //exit();
        } catch (Exception $ex) {
            
        }
    }

    public function action_ChangePriority() {
        try {
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $this->auto_render = FALSE;
            $user_id = Auth::instance()->get_user();
            $request_id = $post["requestid"];
            $request_priority = $post["requestp"];
            //print_r($post); exit;
            if ($request_priority == 4) {

                // to reject user request
                $update = Model_Email::email_status($request_id, 4, 0);
            } else {
                $update = Model_Userrequest::update_request_priority($request_id, $request_priority);
            }
            return $update;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //resend request
    public function action_resend_request() {
        try {
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $this->auto_render = FALSE;
            $user_id = Auth::instance()->get_user();
            $request_id = $post["process_request_id"];
            $update = Model_Email::email_status($request_id, 0, 0);
            return $update;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    public function action_dailyrequestcomparison() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_role = Helpers_Utilities::get_user_role_id($user_obj->id);
            $row = Helpers_Utilities::get_request_comparison();
            echo json_encode($row);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    public function action_totalrequestcomparison() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_role = Helpers_Utilities::get_user_role_id($user_obj->id);

            $row = Helpers_Utilities::get_total_request_comparison();
            echo json_encode($row);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    public function action_totalreceivedcomparison() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_role = Helpers_Utilities::get_user_role_id($user_obj->id);

            $row = Helpers_Utilities::get_total_received_comparison();
            echo json_encode($row);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Parsing status daily 
    public function action_dailyparsingcomparison() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_role = Helpers_Utilities::get_user_role_id($user_obj->id);

            $row = Helpers_Utilities::get_parsing_comparison();
            echo json_encode($row);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    public function action_totalparsingcomparison() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_role = Helpers_Utilities::get_user_role_id($user_obj->id);

            $row = Helpers_Utilities::get_total_parsing_comparison();
            echo json_encode($row);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Request send daily log
    public function action_dailyrequestcount() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_role = Helpers_Utilities::get_user_role_id($user_obj->id);

            $row = Helpers_Utilities::get_request_send_today();
            echo json_encode($row);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Request send daily log
    public function action_request_resend() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_role = Helpers_Utilities::get_user_role_id($user_obj->id);
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $request_id = $_GET['request_id'];
            $request_id = Helpers_Utilities::encrypted_key($request_id, 'decrypt');
            $row = Helpers_Utilities::request_resend($request_id);
        } catch (Exception $e) {
            
        }
        $request_id = Helpers_Utilities::encrypted_key($request_id, 'encrypt');
        if ($row == 1) {
            $this->redirect('userrequest/request_status_detail/' . $request_id . '?resend=1');
        }
    }

    //requeueverisys
    public function action_request_requeue_verisys() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_role = Helpers_Utilities::get_user_role_id($user_obj->id);
            $user_id = $user_obj->id;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $request_id = $_POST['request_id'];
            $request_id = Helpers_Utilities::encrypted_key($request_id, 'decrypt');
            $row = Helpers_Utilities::request_requeue_verisys($request_id);
            echo json_encode($row);
        } catch (Exception $e) {
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex->getMessage());
                exit;
            } else {
                echo json_encode(2);
                exit;
            }
        }
    }
    //requeueverisys
    public function action_request_requeue_familytree() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_role = Helpers_Utilities::get_user_role_id($user_obj->id);
            $user_id = $user_obj->id;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $request_id = $_POST['request_id'];
            $request_id = Helpers_Utilities::encrypted_key($request_id, 'decrypt');
            $row = Helpers_Utilities::request_requeue_verisys($request_id);
            echo json_encode($row);
        } catch (Exception $e) {
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex->getMessage());
                exit;
            } else {
                echo json_encode(2);
                exit;
            }
        }
    }

    //Request send daily log
    public function action_reply_sent() {
        try {
            $user_obj = Auth::instance()->get_user();
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $user_role = Helpers_Utilities::get_user_role_id($user_obj->id);
            $request_id = (int) Helpers_Utilities::encrypted_key($_GET['request_id'], 'decrypt');
            //print_r($request_id); exit;
            $row = Helpers_Utilities::request_reply_sent($request_id);
            echo json_encode(1);
            exit;
        } catch (Exception $ex) {
            echo json_encode(2);
            exit;
        }
    }

    //delete request
    public function action_delete_request() {
        try {
            $user_obj = Auth::instance()->get_user();
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $user_role = Helpers_Utilities::get_user_role_id($user_obj->id);
            $request_id = Helpers_Utilities::encrypted_key($_GET['request_id'], 'decrypt');
            $page = $_GET['page'];
            $row = Helpers_Utilities::request_delete($request_id);
        } catch (Exception $ex) {

            exit;
        }
        if ($page == 'rejected') {
            $this->redirect('Userrequest/rejected_requests');
        } else {
            $this->redirect('Userdashboard/dashboard');
        }
    }

    //Request send permissionz
    public function action_sendrequestpermission() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_permission = Helpers_Profile::get_user_access_permission($user_obj->id, 8);
            //setting json objects default value               
            $message = '';
            $permission = '';
            $startdate = '';
            $enddate = '';
            $mnc = '';
            $_POST = Helpers_Utilities::remove_injection($_POST);
            //checking user permission
            if (!empty($user_permission)) {
                // $date = date('y-m-d h:i:s');
                //getting companies mnc value
                $size_of_mnc = sizeof($_POST['mnc']);
                if ($size_of_mnc == 1) {
                    if (empty($_POST['mnc'][0])) {
                        $mnc = 0;
                    } else {
                        $mnc = $_POST['mnc'][0];
                    }
                } else {
                    $mnc = implode(',', $_POST['mnc']);
                }
                //to get requested value            
                $requested_value = (!empty($_POST['msisdn']) ? $_POST['msisdn'] :
                        (!empty($_POST['imei']) ? $_POST['imei'] :
                        (!empty($_POST['ptclnumber']) ? $_POST['ptclnumber'] :
                        (!empty($_POST['cnic']) ? $_POST['cnic'] : ''))));

                if (!empty($requested_value)) {
                    $check_blocked = Helpers_Email::check_in_blocked_number_list($requested_value);
                }
                if (empty($check_blocked)) {
                    switch ($_POST['requesttype']) {
                        case 3:  ////subscriber request
                            $permissionperiod = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' -1 month')); //one month permission denied period, user can not request for subscriber within one month               
                            $permission = Helpers_Email::get_sub_request_permission(3, $mnc, $_POST['msisdn'], $permissionperiod);
                            if ($permission == 1) {
                                $message = "Not Permitted: Previous request is initiated within last 30 days, check in request status";
                            } else {
                                $message = "Permitted: Permission granted to request data from company";
                            }
                            break;
                        case 4: // current location
                            $permissionperiod = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' -1 days')); //1 days permission denied period, user can not request if current location request is pending within 1 day             
                            $permission = Helpers_Email::get_location_request_permission(4, $mnc, $_POST['msisdn'], $permissionperiod);
                            if ($permission == 1) {
                                $message = "Not Permitted: Previous request is initiated within last 24 hours, check in request status";
                            } else {
                                $message = "Permitted: Permission granted to request data from company";
                            }
                            break;
                        case 5:  //sims against cnic
                            $permissionperiod = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' -1 month')); //one month permission denied period, user can not request for sims against cnic within one month               
                            $permission = Helpers_Email::get_sims_against_cnic_request_permission(5, $mnc, $_POST['cnic'], $permissionperiod);
                            if ($permission == 1) {
                                $message = "Not Permitted: Previous request is initiated within last 30 days, check in request status";
                            } else {
                                $message = "Permitted: Permission granted to request data from company";
                            }
                            break;
                        case 1:  //cdr against mobile number
                            //check if request is penidn within last 15 days
                            $permissionperiod = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' -15 days')); //15 days permission denied period, user can not not request if cdr request is pending within 15 days             
                            $permission = Helpers_Email::get_cdr_against_msisdn_request_permission(1, $mnc, $_POST['msisdn'], $permissionperiod);
                            //check if duration is not prohibited
                            if (empty($permission)) {
                                $startdate = Helpers_Utilities::get_cdr_first_date_with_msisdn($_POST['msisdn']);
                                $enddate = Helpers_Utilities::get_cdr_last_date_with_msisdn($_POST['msisdn']);
                                if (!empty($startdate) && !empty($enddate)) {
                                    if (strtotime($_POST['startdate']) > strtotime($enddate) && strtotime($_POST['enddate'] > strtotime($enddate))) {
                                        $permission = 0;
                                        $message = "Prohibited Duration (MM/DD/YY): " . $startdate . " To " . $enddate;
                                    } elseif (strtotime($_POST['startdate']) < strtotime($startdate) && strtotime($_POST['enddate'] < strtotime($startdate))) {
                                        $permission = 0;
                                        $message = "Prohibited Duration (MM/DD/YY): " . $startdate . " To " . $enddate;
                                    } else {
                                        $permission = 2;
                                        $message = "Prohibited Duration (MM/DD/YY): " . $startdate . " To " . $enddate;
                                    }
                                } else {
                                    $permission = 0;
                                    $message = "Permitted: Permission granted to request data from company";
                                }
                            } else {
                                $message = "Not Permitted: Previous request is initiated within last 15 days, check in request status";
                            }
                            break;
                        case 6:  //cdr against mobile number for sms
                            //check either request is pending
                            $permissionperiod = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' -15 days'));
                            //15 days permission denied period, user can not not request if cdr request is pending within 5 days             
                            $permission = Helpers_Email::get_cdr_against_msisdn_request_permission(6, $mnc, $_POST['msisdn'], $permissionperiod);
                            //check allowed time duration
                            if (empty($permission)) {
                                $startdate = Helpers_Utilities::get_sms_details_first_date_with_msisdn($_POST['msisdn']);
                                $enddate = Helpers_Utilities::get_sms_details_last_date_with_msisdn($_POST['msisdn']);
                                if (!empty($startdate) && !empty($enddate)) {
                                    if (strtotime($_POST['startdate']) > strtotime($enddate) && strtotime($_POST['enddate'] > strtotime($enddate))) {
                                        $permission = 0;
                                        $message = "Prohibited Duration (MM/DD/YY): " . $startdate . " To " . $enddate;
                                    } elseif (strtotime($_POST['startdate']) < strtotime($startdate) && strtotime($_POST['enddate'] < strtotime($startdate))) {
                                        $permission = 0;
                                        $message = "Prohibited Duration (MM/DD/YY): " . $startdate . " To " . $enddate;
                                    } else {
                                        $permission = 2;
                                        $message = "Prohibited Duration (MM/DD/YY): " . $startdate . " To " . $enddate;
                                    }
                                } else {
                                    $permission = 0;
                                    $message = "Permitted: Permission granted to request data from company";
                                }
                            } else {
                                $message = "Not Permitted: Previous request is initiated within last 15 days, check in request status";
                            }
                            break;
                        case 2: //cdr against imei number
                            //check either request is pending
                            $permissionperiod = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' -5 days')); //5 days permission denied period, user can not not request if cdr request is pending within 5 days             
                            $permission = Helpers_Email::get_cdr_against_imei_request_permission(2, $mnc, $_POST['imei'], $permissionperiod);
                            //check in prohibited period
                            if (empty($permission)) {
                                $startdate = Helpers_Utilities::get_cdr_first_date_with_imei($_POST['imei']);
                                $enddate = Helpers_Utilities::get_cdr_last_date_with_imei($_POST['imei']);
                                if (!empty($startdate) && !empty($enddate)) {
                                    if (strtotime($_POST['startdate']) > strtotime($enddate) && strtotime($_POST['enddate'] > strtotime($enddate))) {
                                        $permission = 0;
                                        $message = "Prohibited Duration (MM/DD/YY): " . $startdate . " To " . $enddate;
                                    } elseif (strtotime($_POST['startdate']) < strtotime($startdate) && strtotime($_POST['enddate'] < strtotime($startdate))) {
                                        $permission = 0;
                                        $message = "Prohibited Duration (MM/DD/YY): " . $startdate . " To " . $enddate;
                                    } else {
                                        $permission = 0;
                                        $message = "CDR Existed For " . $_POST['imei'] . " From (MM/DD/YY): " . $startdate . " To " . $enddate;
                                    }
                                } else {
                                    $permission = 0;
                                    $message = "Permitted: Permission granted to request data from company";
                                }
                            } else {
                                $message = "Not Permitted: Previous request is initiated within last 15 days, check in request status";
                            }
                            break;
                        case 7: //PTCL Number 
                            //Permission for PTCL Number 
                            //Comming soon
                            $permission = 0;
                            $message = "Permitted: Permission granted to request data from company";
                            break;
                        case 9: //International Number
                            //Permission for International Number 
                            //Comming soon
                            $permission = 0;
                            $message = "Permitted: Permission granted to request data from company";
                            break;
                        case 10: //International Number
                            //Permission for Family Tree
                            $check_old_request = Helpers_Email::check_old_familytree_request($requested_value);
                            $en_check_old_request = trim(Helpers_Utilities::encrypted_key($check_old_request, 'encrypt'));
                            if (!empty($check_old_request)) {
                                $permission = -1;
                                $view_detail_link = '<a href="' . URL::site('userrequest/request_status_detail/' . $en_check_old_request) . '" > View Detail </a>';
                                $message = 'Request againt this CNIC already exist, click here to ' . $view_detail_link;
                            } else {
                                //Comming soon
                                $permission = 0;
                                $message = "Permitted: Permission granted to request Family Tree";
                            }
                            break;
                        case 11: //PTCL Subscriber persmission                        
                            $permissionperiod = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' -1 month')); //one month permission denied period, user can not request for subscriber within one month               
                            $permission = Helpers_Email::get_sub_request_permission(11, 11, $requested_value, $permissionperiod);
                            if ($permission == 1) {
                                $message = "Not Permitted: Previous request is initiated within last 30 days, check in request status";
                            } else {
                                $message = "Permitted: Permission granted to request data from company";
                            }
                            break;
                    }
                } else {
                    $blocked_reason = !empty($check_blocked->blocked_reason) ? $check_blocked->blocked_reason : 'NA';
                    $blocked_details = !empty($check_blocked->blocked_details) ? $check_blocked->blocked_details : 'NA';
                    $blocked_time = !empty($check_blocked->time_stamp) ? $check_blocked->time_stamp : '';
                    $message = "Prohibited Reason: " . $blocked_reason . ". Details: " . $blocked_details . ". Blocked Time: " . $blocked_time;
                    $permission = -1;
                }
                $json_ob['permission'] = $permission;
                $json_ob['message'] = $message;
                $json_ob['startdate'] = $startdate;
                $json_ob['enddate'] = $enddate;
                $json_ob['mnc'] = $mnc;
                $json_ob['requesttype'] = $_POST['requesttype'];
                echo json_encode($json_ob);
            } else {
                $json_ob['permission'] = -1;
                $json_ob['startdate'] = $startdate;
                $json_ob['enddate'] = $enddate;
                $json_ob['mnc'] = $mnc;
                $json_ob['message'] = 'Not permitted to send request';
                $json_ob['requesttype'] = $_POST['requesttype'];
                echo json_encode($json_ob);
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* CDR against Mobile number permissions */
    /* CDR against Mobile number with sms permissions */

    public function action_cdrrequestpermission() {
        try {
            $requesttype = !empty($_POST['requesttype']) ? $_POST['requesttype'] : 0;
            $msisdn = !empty($_POST['msisdn']) ? $_POST['msisdn'] : 0;
            $c_startdate = !empty($_POST['startdate']) ? $_POST['startdate'] : null;
            $c_enddate = !empty($_POST['enddate']) ? $_POST['enddate'] : null;
                
            $startdate = NULL;
            $enddate = NULL;
            //check either request is pending if request is in queue, new request can not be initiated            
            $permission = Helpers_Email::check_request_in_queue_status($requesttype, $msisdn);
            //check if duration is not prohibited
            if (empty($permission)) {
                if(!empty($c_startdate) && !empty($c_enddate)){
                    $check_duration = Helpers_Utilities::get_cdr_duration_with_msisdn($_POST['msisdn'],$c_startdate,$c_enddate);
                
                  if (!empty($check_duration)) {
                      $startdate=$c_startdate;
                      $enddate=$c_enddate;
                    $permission = 2;
                    $message = "Prohibited Duration (MM/DD/YY): " . $startdate . " To " . $enddate;
                } else {
                    $permission = 0;
                    $message = "Permitted: Permission granted to request data from company";
                }
                    
                }else{
                
                $startdate = Helpers_Utilities::get_cdr_first_date_with_msisdn($_POST['msisdn']);
                $enddate = Helpers_Utilities::get_cdr_last_date_with_msisdn($_POST['msisdn']);
                if (!empty($startdate) && !empty($enddate)) {
                    $permission = 2;
                    $message = "Prohibited Duration (MM/DD/YY): " . $startdate . " To " . $enddate;
                } else {
                    $permission = 0;
                    $message = "Permitted: Permission granted to request data from company";
                }
                }
            } else {
                $message = "Not Permitted: Previous request is in queue, check in request status or contact Admin";
            }
            $json_ob['permission'] = $permission;
            $json_ob['message'] = $message;
            $json_ob['startdate'] = $startdate;
            $json_ob['enddate'] = $enddate;
            echo json_encode($json_ob);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* SIM's Against CNIC permissions */

    public function action_cnicsimspermission() {
        try {
            $cnic = !empty($_POST['cnic']) ? $_POST['cnic'] : 0;
            $permissionperiod = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' -15 days')); //1 days permission denied period, user can not request if current location request is pending within 1 day 
            //check if request is pending then from how many companies its pending, mnc names in an array            
            $mnc = Helpers_Email::get_mnc_of_request_in_queue(5, $cnic, $permissionperiod);
            //check in prohibited period                                    
            $permission = 2;
            $message = "Permission granted to request data from company";
            $json_ob['permission'] = $permission;
            $json_ob['message'] = $message;
            $json_ob['mnc'] = $mnc;
            echo json_encode($json_ob);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* CDR against IMEI number permissions */

    public function action_imeirequestpermission() {
        try {
            $requesttype = !empty($_POST['requesttype']) ? $_POST['requesttype'] : 0;
            $imei = !empty($_POST['imei']) ? $_POST['imei'] : 0;
            $startdate = NULL;
            $enddate = NULL;
            //check either request is pending
            $permissionperiod = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' -5 days')); //5 days permission denied period, user can not not request if cdr request is pending within 5 days
            //check if request is pending then from how many companies its pending, mnc names in an array            
            $mnc = Helpers_Email::get_mnc_of_request_in_queue(2, $imei, $permissionperiod);
            $permission = 2;
            $message = "Permission granted to request data from company";
            //check in prohibited period            
            //$startdate = Helpers_Utilities::get_cdr_first_date_with_imei($imei);
            // $enddate = Helpers_Utilities::get_cdr_last_date_with_imei($imei);
//            if (!empty($startdate) && !empty($enddate)) {
//                    $permission = 2;
//                    $message = "Prohibited Duration (MM/DD/YY): " . $startdate . " To " . $enddate;
//            } else {
//                $permission = 2;
//                $message = "Permission granted to request data from company";
//            }
            $json_ob['permission'] = $permission;
            $json_ob['message'] = $message;
            $json_ob['startdate'] = $startdate;
            $json_ob['enddate'] = $enddate;
            $json_ob['mnc'] = $mnc;
            echo json_encode($json_ob);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Current loaction permission */

    public function action_locrequestpermission() {
        try {
            $requesttype = !empty($_POST['requesttype']) ? $_POST['requesttype'] : 0;
            $msisdn = !empty($_POST['msisdn']) ? $_POST['msisdn'] : 0;
            $permissionperiod = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' -1 days')); //1 days permission denied period, user can not request if current location request is pending within 1 day             
            $permission = Helpers_Email::get_location_request_permission(4, $msisdn, $permissionperiod);
            if ($permission == 1) {
                $message = "Not Permitted: Previous request is initiated within last 24 hours, check in request status";
            } else {
                $message = "Permitted: Permission granted to request data from company";
            }
            $json_ob['permission'] = $permission;
            $json_ob['message'] = $message;
            echo json_encode($json_ob);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* CDR PTCL permission */

    public function action_cdrptclpermission() {
        try {
            $requesttype = !empty($_POST['requesttype']) ? $_POST['requesttype'] : 0;
            $ptclno = !empty($_POST['ptclno']) ? $_POST['ptclno'] : 0;
            $permission = Helpers_Email::check_request_in_queue_status(7, $ptclno);
            if ($permission == 1) {
                $message = "Not Permitted: Previous request is in queue check request status or contact Admin.";
            } else {
                $message = "Permitted: Permission granted to request data from company";
            }
            $json_ob['permission'] = $permission;
            $json_ob['message'] = $message;
            echo json_encode($json_ob);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Sub PTCL permission llll */

    public function action_subptclpermission() {
        try {
            $requesttype = !empty($_POST['requesttype']) ? $_POST['requesttype'] : 0;
            $ptclno = !empty($_POST['ptclno']) ? $_POST['ptclno'] : 0;
            $permissionperiod = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' -15 days')); //one month permission denied period, user can not request for subscriber within one month
            $permission = Helpers_Email::get_subptcl_request_permission(11, $ptclno, $permissionperiod);
            if ($permission == 1) {
                $message = "Not Permitted: Previous request is in queue or initiated within last 15 days.";
            } else {
                $message = "Permitted: Permission granted to request data from company";
            }
            $json_ob['permission'] = $permission;
            $json_ob['message'] = $message;
            echo json_encode($json_ob);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* CDR international permission */

    public function action_cdrintpermission() {
        try {
            $requesttype = !empty($_POST['requesttype']) ? $_POST['requesttype'] : 0;
            $intnumber = !empty($_POST['inputINTNO']) ? $_POST['inputINTNO'] : 0;
            $permission = Helpers_Email::get_cdrint_request_permission(9, $intnumber);
            if ($permission == 1) {
                $message = "Not Permitted: Previous request is in queue check request status or contact Admin.";
            } else {
                $message = "Permitted: Permission granted to request data from company";
            }
            $json_ob['permission'] = $permission;
            $json_ob['message'] = $message;
            echo json_encode($json_ob);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* verisys permission */

    public function action_verisyspermission() {
        try {
            $cnic_number = !empty($_POST['cnic_number']) ? $_POST['cnic_number'] : 0;
            $permission = Helpers_Email::get_verisys_request_permission(8, $cnic_number);
            if ($permission == 1) {
                $message = "Not Permitted: Previous request is in queue check request status or contact Admin.";
            } else {
                $message = "Permitted: Permission granted to request data from company";
            }
            $json_ob['permission'] = $permission;
            $json_ob['message'] = $message;
            echo json_encode($json_ob);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }
    /* verisys permission */

    public function action_travelhistorypermission() {
        try {
            $cnic_number = !empty($_POST['cnic_number']) ? $_POST['cnic_number'] : 0;
            $permission = Helpers_Email::get_verisys_request_permission(12, $cnic_number);
            if ($permission == 1) {
                $message = "Not Permitted: Previous request is in queue check request status or contact Admin.";
            } else {
                $message = "Permitted: Permission granted to request data from company";
            }
            $json_ob['permission'] = $permission;
            $json_ob['message'] = $message;
            echo json_encode($json_ob);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Family tree permission */

    public function action_familytreepermission() {
        try {
            $cnic_number = !empty($_POST['cnic_number']) ? $_POST['cnic_number'] : 0;
            $permission = Helpers_Email::check_request_in_queue_status(10, $cnic_number);
            if ($permission == 1) {
                $message = "Not Permitted: Previous request is in queue check request status or contact Admin.";
            } else {
                $message = "Permitted: Permission granted to request data from company";
            }
            $json_ob['permission'] = $permission;
            $json_ob['message'] = $message;
            echo json_encode($json_ob);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Subscriber Against Mobile Number permission lolll */

    public function action_subrequestpermission() {
        try {
            //check if previous request is in queue
            $inputSubNO = !empty($_POST['inputSubNO']) ? $_POST['inputSubNO'] : 0;
            $permissionperiod = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' -15 days')); //1 days permission denied period, user can not request if current location request is pending within 1 day             
            //check request in queue lol manan
            $permission = Helpers_Email::check_request_in_queue_status(3, $inputSubNO);
            if ($permission == 1) {
                $message = "Not Permitted: Previous request is in queue, check in request status or contact Admin";
            } else {
                //check if request was generated in last 15 days
                //Ali Raza remove The Check For 15 days for BVS
                /*$date_permission = Helpers_Email::check_old_request_with_date(3, $inputSubNO, $permissionperiod);
                if ($date_permission == 1) {
                    $permission = 2;
                    $message = "Not Permitted: Previous request is initiated within last 15 days, check in request status or contact Admin";
                }
                else*/
                {
                    $message = "Permitted: Permission granted to request data from company";
                }
            }
            $json_ob['permission'] = $permission;
            $json_ob['message'] = $message;
            echo json_encode($json_ob);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*  Branchless Banking Details permission */

    public function action_branchlessbankingpermission() {
        try {
            //check if previous request is in queue
            $requested_value = (isset($_POST['inputSubNO']) && !empty($_POST['inputSubNO']) ? $_POST['inputSubNO'] :
                    ((isset($_POST['inputCNIC']) && !empty($_POST['inputCNIC'])) ? $_POST['inputCNIC'] : ''));
            $permissionperiod = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' -15 days')); //1 days permission denied period, user can not request if current location request is pending within 1 day             
            //check request in queue lol manan
            $permission = Helpers_Email::request_in_queue_branchlessbanking($requested_value);
            if ($permission == 1) {
                $message = "Not Permitted: Request already generated, check in request status or contact Admin";
            } else {
                $message = "Permitted: Permission granted to request data from company";
            }
            $json_ob['permission'] = $permission;
            $json_ob['message'] = $message;
            echo json_encode($json_ob);
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
            echo json_encode(2);
        }
    }

    /* User Request Status (request_status) */

    public function action_request_status_resend() {
        try {

            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
            $posting = $login_user_profile->posted;
            $result = explode('-', $posting);
            if (Helpers_Utilities::chek_role_access($this->role_id, 23) == 1) {
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
                /* View Generate */
                $this->template->content = View::factory('templates/user/request_status_resend')
                        ->bind('message', $message)
                        ->set('search_post', $post);
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

//ajax call for data
    public function action_ajaxuserrequeststatusresend() {
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

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->user_request_status_resend($post, 'true');
                $profiles = $data->user_request_status_resend($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $request_type_id = ( isset($item['user_request_type_id']) ) ? $item['user_request_type_id'] : 0;
                        $request_reference_id = ( isset($item['reference_id']) ) ? $item['reference_id'] : 0;
                        $request_id = ( isset($item['request_id']) ) ? $item['request_id'] : 0;
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 0;
                        $user_role_name = (isset($user_id) ) ? Helpers_Utilities::get_user_role_name($user_id) : 'N/A';
                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $user_request = ( isset($item['email_type_name']) ) ? $item['email_type_name'] : 'NA';
                        $user_request .= '<span><b>ID:';
                        $user_request .= ( isset($item['request_id']) ) ? $item['request_id'] : 'NA';
                        $user_request .= ' Ref#' . $request_reference_id;
                        $user_request .= '</b></span>';
                        $company_name = '<span><b>';
                        $company_name .= (isset($item['company_name']) && !empty($item['company_name']) ) ? Helpers_Utilities::get_companies_data($item['company_name'])->company_name : '--';
                        $company_name .= '</b></span><br><span>';
                        $company_name .= (isset($item['requested_value']) && !empty($item['requested_value']) ) ? $item['requested_value'] : '--';
                        $company_name .= '</span>';
                        $requested_value = ( isset($item['requested_value']) ) ? $item['requested_value'] : 'NA';
                        $reply = ( isset($item['reply']) ) ? $item['reply'] : 0;
                        $concerned_person_id = ( isset($item['concerned_person_id']) ) ? $item['concerned_person_id'] : 'NA';
                        if ($concerned_person_id > 0) {
                            $perons_name = ( isset($item['concerned_person_id']) ) ? Helpers_Person::get_person_name($item['concerned_person_id']) : 'NA';
                            $perons_name .= '</br>[';
                            $perons_name .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['concerned_person_id'], "encrypt")) . '" > View Profile </a>';
                            $perons_name .= ']';
                        } else {
                            $perons_name = " ";
                        }
                        //  $user_request = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type($item['user_request_type_id']) : 'NA';
                        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : 'NA';
                        $email_send_date = ( isset($item['sending_date']) ) ? $item['sending_date'] : 'Un-Known';
                        $email_send_count = ( isset($item['request_send_count']) ) ? $item['request_send_count'] : 0;
                        $status = ( isset($item['status']) ) ? $item['status'] : 0;
                        $system_status_flag = '';

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
                        $member_name_link = '<a class="btn btn-block btn-warning btn-xs" onclick="request_resend(' . $request_id . ')" > Resend </a> ';
                        $member_name_link .= ' ' . '<a class="btn btn-block btn-danger btn-xs" onclick="request_reject(' . $request_id . ')" > Reject </a> ';
                        $row = array(
                            $user_name,
//                      $user_role_name,                        
                            $user_request,
                            $company_name,
                            // $perons_name,
                            $created_at,
                            $email_send_date,
                            $email_send_count,
                            $status_flag,
                            //$reply_flag,
                            //$system_status_flag,
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

    public function action_request_resend_tech() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $request_id = $_POST['request_id'];
            $row = Helpers_Utilities::request_resend($request_id);
            echo json_encode($row);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Request Reject option for tech section
    public function action_request_reject_tech() {
        try {
            $login_user = Auth::instance()->get_user();
            $user_id = $login_user->id;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $request_id = $_POST['request_id'];
            // to reject user request
            $update = Model_Email::email_status($request_id, 4, 0);
            // print_r($update); exit;
            //Reject Request activitys
            echo json_encode(1);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*
     * Cron job manual run
     */

    public function action_cronjobmanual() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 22) == 1) {
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                // Session::instance()->set('search_post', $post);

                $this->template->content = View::factory('templates/user/cronjobmanual')
                        ->set('search_post', $post);
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    public function action_allow_me() {

        $login_user = Auth::instance()->get_user();
        if ($login_user_id == 2603 || $login_user_id == 842 || $login_user_id == 137 || $login_user_id == 2031) {
            try {
                $id = $this->request->param('id');
                $id = Helpers_Utilities::remove_injection($id);
                $id = str_replace('a', '.', $id);

                $query = DB::delete('LoginAttempts')
                        ->where('IP', '=', $id)
                        ->execute();
                echo 'Done';
            } catch (Exception $e) {
                echo 'Error';
            }
        }
    }

    /* Delete not Found temp verisys upload record */

    public function action_delete_temp_verisys_record() {
        try {
            if (Auth::instance()->logged_in()) {
                $row_id = $this->request->param('id');
                $row_id = Helpers_Utilities::remove_injection($row_id);
                // print_r($blocked_id); exit;
                $user = New Model_Userrequest();
                $result = $user->delete_temp_verisys_record($row_id);
                echo 1;
            } else {
                echo json_encode(-2);
            }
        } catch (Exception $ex) {
            echo json_encode(-2);
        }
    }

    /* Delete not Found temp verisys upload record */

    public function action_delete_temp_familytree_record() {
        try {
            if (Auth::instance()->logged_in()) {
                $row_id = $this->request->param('id');
                $row_id = Helpers_Utilities::remove_injection($row_id);
                // print_r($blocked_id); exit;
                $user = New Model_Userrequest();
                $result = $user->delete_temp_familytree_record($row_id);
                echo 1;
            } else {
                echo json_encode(-2);
            }
        } catch (Exception $ex) {
            echo json_encode(-2);
        }
    }
    public function action_delete_temp_travelhistory_record() {
        try {
            if (Auth::instance()->logged_in()) {
                $row_id = $this->request->param('id');
                $row_id = Helpers_Utilities::remove_injection($row_id);
                // print_r($blocked_id); exit;
                $user = New Model_Userrequest();
                $result = $user->delete_temp_travelhistory_record($row_id);
                echo 1;
            } else {
                echo json_encode(-2);
            }
        } catch (Exception $ex) {
            echo json_encode(-2);
        }
    }

    /*  CDR against Mobile number */

    public function action_requestcdr() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_permission = Helpers_Profile::get_user_access_permission($user_obj->id, 8);
            if (!empty($user_permission)) {
                /* Posted Data */
                $post = $this->request->post();
                $_GET = Helpers_Utilities::remove_injection($_GET);
                $_POST = Helpers_Utilities::remove_injection($_POST);
                if (!empty($_GET['id']))
                    $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                else
                    $pid = '';
                
                //$this->template->content = View::factory('templates/user/access_denied');
                
                 $this->template->content = View::factory('templates/requests/cdrmsisdn')
                        ->bind('pid', $pid)
                        ->bind('post', $post);
            }else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*  CDR against Mobile number with sms details */

    public function action_requestcdrsms() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_permission = Helpers_Profile::get_user_access_permission($user_obj->id, 8);
            if (!empty($user_permission)) {
                /* Posted Data */
                $post = $this->request->post();
                $_GET = Helpers_Utilities::remove_injection($_GET);
                $_POST = Helpers_Utilities::remove_injection($_POST);
                if (!empty($_GET['id']))
                    $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                else
                    $pid = '';
                $this->template->content = View::factory('templates/requests/cdrmsisdnsms')
                        ->bind('pid', $pid)
                        ->bind('post', $post);
            }else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*  CDR against IMEI number */

    public function action_requestcdrimei() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_permission = Helpers_Profile::get_user_access_permission($user_obj->id, 8);
            if (!empty($user_permission)) {
                /* Posted Data */
                $post = $this->request->post();
                $_GET = Helpers_Utilities::remove_injection($_GET);
                $_POST = Helpers_Utilities::remove_injection($_POST);
                if (!empty($_GET['id']))
                    $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                else
                    $pid = '';
                $this->template->content = View::factory('templates/requests/cdrimei')
                        ->bind('pid', $pid)
                        ->bind('post', $post);
            }else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*  Current loaction */

    public function action_requestlocation() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_permission = Helpers_Profile::get_user_access_permission($user_obj->id, 8);
            if (!empty($user_permission)) {
                /* Posted Data */
                $post = $this->request->post();
                $_GET = Helpers_Utilities::remove_injection($_GET);
                $_POST = Helpers_Utilities::remove_injection($_POST);
                if (!empty($_GET['id']))
                    $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                else
                    $pid = '';
                $this->template->content = View::factory('templates/requests/location')
                        ->bind('pid', $pid)
                        ->bind('post', $post);
            }else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*  CDR Against PTCL Number */

    public function action_requestcdrptcl() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_permission = Helpers_Profile::get_user_access_permission($user_obj->id, 8);
            if (!empty($user_permission)) {
                /* Posted Data */
                $post = $this->request->post();
                $_GET = Helpers_Utilities::remove_injection($_GET);
                $_POST = Helpers_Utilities::remove_injection($_POST);
                if (!empty($_GET['id']))
                    $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                else
                    $pid = '';
                $this->template->content = View::factory('templates/requests/cdrptcl')
                        ->bind('pid', $pid)
                        ->bind('post', $post);
            }else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*  Subscriber Against PTCL Number */

    public function action_requestsubptcl() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_permission = Helpers_Profile::get_user_access_permission($user_obj->id, 8);
            if (!empty($user_permission)) {
                /* Posted Data */
                $post = $this->request->post();
                $_GET = Helpers_Utilities::remove_injection($_GET);
                $_POST = Helpers_Utilities::remove_injection($_POST);
                if (!empty($_GET['id']))
                    $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                else
                    $pid = '';
                $this->template->content = View::factory('templates/requests/subptcl')
                        ->bind('pid', $pid)
                        ->bind('post', $post);
            }else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*  CDR Against International Number */

    public function action_requestcdrinternational() {
        
        //temporary disabled
        $this->template->content = View::factory('templates/user/access_denied');
       
        /*
        try {
            $user_obj = Auth::instance()->get_user();
            $user_permission = Helpers_Profile::get_user_access_permission($user_obj->id, 8);
            if (!empty($user_permission)) {
                // Posted Data 
                $post = $this->request->post();
                $_GET = Helpers_Utilities::remove_injection($_GET);
                $_POST = Helpers_Utilities::remove_injection($_POST);
                if (!empty($_GET['id']))
                    $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                else
                    $pid = '';
                $this->template->content = View::factory('templates/requests/cdrinternational')
                        ->bind('pid', $pid)
                        ->bind('post', $post);
            }else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
        */
    }

    /*  verisy request */

    public function action_requestverisys() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_permission = Helpers_Profile::get_user_access_permission($user_obj->id, 8);
            if (!empty($user_permission)) {
                /* Posted Data */
                $post = $this->request->post();
                $_GET = Helpers_Utilities::remove_injection($_GET);
                $_POST = Helpers_Utilities::remove_injection($_POST);
                if (!empty($_GET['id']))
                    $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                else
                    $pid = '';
                $this->template->content = View::factory('templates/requests/verisys')
                        ->bind('pid', $pid)
                        ->bind('post', $post);
            }else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
    /*  family tree request */

    public function action_requestfamtree() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_permission = Helpers_Profile::get_user_access_permission($user_obj->id, 8);
            if (!empty($user_permission)) {
                /* Posted Data */
                $post = $this->request->post();
                $_GET = Helpers_Utilities::remove_injection($_GET);
                $_POST = Helpers_Utilities::remove_injection($_POST);
                if (!empty($_GET['id']))
                    $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                else
                    $pid = '';
                $this->template->content = View::factory('templates/requests/famtree')
                        ->bind('pid', $pid)
                        ->bind('post', $post);
            }else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
    /*  verisy request */

    public function action_requesttravelhistory() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_permission = Helpers_Profile::get_user_access_permission($user_obj->id, 8);
            if (!empty($user_permission)) {
                /* Posted Data */
                $post = $this->request->post();
                $_GET = Helpers_Utilities::remove_injection($_GET);
                $_POST = Helpers_Utilities::remove_injection($_POST);
                if (!empty($_GET['id']))
                    $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                else
                    $pid = '';
                $this->template->content = View::factory('templates/requests/travelhistory')
                        ->bind('pid', $pid)
                        ->bind('post', $post);
            }else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*  family tree request */

    public function action_requestfamilytree() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_permission = Helpers_Profile::get_user_access_permission($user_obj->id, 8);
            if (!empty($user_permission)) {
                /* Posted Data */
                $post = $this->request->post();
                $_GET = Helpers_Utilities::remove_injection($_GET);
                $_POST = Helpers_Utilities::remove_injection($_POST);
                if (!empty($_GET['id']))
                    $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                else
                    $pid = '';
                $this->template->content = View::factory('templates/requests/familytree503')
                        ->bind('pid', $pid)
                        ->bind('post', $post);
            }else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*  Subscriber request */

    public function action_requestsubscriber() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_permission = Helpers_Profile::get_user_access_permission($user_obj->id, 8);
            if (!empty($user_permission)) {
                /* Posted Data */
                $post = $this->request->post();
                $_GET = Helpers_Utilities::remove_injection($_GET);
                $_POST = Helpers_Utilities::remove_injection($_POST);
                if (!empty($_GET['id']))
                    $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                else
                    $pid = '';
                $this->template->content = View::factory('templates/requests/subsciber')
                        ->bind('pid', $pid)
                        ->bind('post', $post);
            }else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*  SIM's Against CNIC */

    public function action_requestcnicsims() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_permission = Helpers_Profile::get_user_access_permission($user_obj->id, 8);
            if (!empty($user_permission)) {
                /* Posted Data */
                $post = $this->request->post();
                $_GET = Helpers_Utilities::remove_injection($_GET);
                $_POST = Helpers_Utilities::remove_injection($_POST);
                if (!empty($_GET['id']))
                    $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                else
                    $pid = '';
                $this->template->content = View::factory('templates/requests/cnicsims')
                        ->bind('pid', $pid)
                        ->bind('post', $post);
            }else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //Family tree request status detail
    public function action_familytree_detail() {
        try {
            $user_obj = Auth::instance()->get_user();
            /* Posted Data */
            $post = $this->request->post();
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $request_id = !empty($_POST['request_id']) ? $_POST['request_id'] : 0;
            $model_object = new Model_Userrequest;
            $request_data = $model_object->view($request_id);
            $user_obj = Auth::instance()->get_user();
            $this->template->content = View::factory('templates/user/request_familytree_detail')
                    ->set('results', $request_data)
                    ->set('user_id', $user_obj->id);
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/some_thing_went_wrong');
        }
    }

    /*  Request Branch Less Banking Data */

    public function action_requestbranchlessbanking() {
        try {
            $user_obj = Auth::instance()->get_user();
            $user_permission = Helpers_Profile::get_user_access_permission($user_obj->id, 8);
            if (!empty($user_permission)) {
                /* Posted Data */
                $post = $this->request->post();
                $_GET = Helpers_Utilities::remove_injection($_GET);
                $_POST = Helpers_Utilities::remove_injection($_POST);
                if (!empty($_GET['id']))
                    $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                else
                    $pid = '';
                $this->template->content = View::factory('templates/user/comingsoon');
//                $this->template->content = View::factory('templates/requests/branchlessbanking')
//                        ->bind('pid', $pid)
//                        ->bind('post', $post);
            }else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /* Branchless Banking Request Status */

    public function action_request_status_branclessbanking() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            /* Posted Data */
            $post = $this->request->post();
            if (Helpers_Utilities::chek_role_access($this->role_id, 15) == 1) {
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('r_s_branchlessbanking_post', $post);
                $this->template->content = View::factory('templates/user/request_status_branchlessbanking')
                        ->set('search_post', $post);
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxstatusbranclessbanking() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('r_s_branchlessbanking_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->branchlessbanking_request_data($post, 'true');
                $profiles = $data->branchlessbanking_request_data($post, 'false');
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $request_type_id = ( isset($item['user_request_type_id']) ) ? $item['user_request_type_id'] : 0;
                        $request_id = ( isset($item['request_id']) ) ? $item['request_id'] : 'NA';
                        $request_reference_id = ( isset($item['reference_id']) ) ? $item['reference_id'] : 0;
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 0;
                        $userposting = ( isset($user_id) ) ? Helpers_Profile::get_user_region_district($user_id) : 'NA';
                        $user_role_name = (isset($user_id) ) ? Helpers_Utilities::get_user_role_name($user_id) : 'N/A';
                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $user_name .= '<span></br><b>';
                        $user_name .= $userposting;
                        $user_name .= '</b></span>';
                        $user_request = ( isset($item['email_type_name']) ) ? $item['email_type_name'] : 'NA';
                        $user_request .= '<span> <b></br>ID:';
                        $user_request .= ( isset($item['request_id']) ) ? $item['request_id'] : 'NA';
                        $user_request .= ' Ref#' . $request_reference_id;
                        $user_request .= '</b></span>';
                        //Request Dispatch ID
                        $dispatch_id = ( isset($item['dispatch_id']) ) ? $item['dispatch_id'] : 0;
                        //
                        $bank_name_requested_value = '<span><b>';
                        $bank_name_requested_value .= (isset($item['bank_id']) && !empty($item['bank_id']) ) ? Helpers_Utilities::get_banks_list($item['bank_id'])->name : '--';
                        $bank_name_requested_value .= '</b></span><br><span>';
                        $bank_name_requested_value .= (isset($item['requested_value']) && !empty($item['requested_value']) ) ? $item['requested_value'] : '--';
                        $bank_name_requested_value .= '</span>';
                        //person profile and link
                        $concerned_person_id = ( isset($item['concerned_person_id']) ) ? $item['concerned_person_id'] : 'NA';
                        $enc_request_id = trim(Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt'));
                        if ($concerned_person_id > 0) {
                            $perons_name = ( isset($item['concerned_person_id']) ) ? Helpers_Person::get_person_name($item['concerned_person_id']) : 'NA';
                            $perons_name .= '</br>[';
                            $perons_name .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['concerned_person_id'], "encrypt")) . '" > View Profile </a>';
                            $perons_name .= ']';
                        } else {
                            $perons_name = " ";
                        }
                        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : 'NA';
                        $dispatch_flag = '</br><span class="label label-info"><b>Dispatch ID: ';
                        $dispatch_flag .= $dispatch_id;
                        $dispatch_flag .= '</b></span>';
                        //request status flag
                        $request_status = ( isset($item['request_status']) ) ? $item['request_status'] : 1;
                        switch ($request_status) {
                            case 1:
                                $status_flag = '<span class="label label-warning">Request Send</span>';
                                break;
                            case 2:
                                $status_flag = '     <span class="label label-primary">Request Dispatched</span>';
                                break;
                            case 3:
                                $status_flag = '<span class="label label-success">Request Received</span>';
                                break;
                        }

                        $member_name_link = '<a class="btn btn-block btn-info btn-xs" href="' . URL::site('userrequest/request_status_detail_banking/' . $enc_request_id) . ' " > View Detail </a> ';
                        $row = array(
                            $user_name,
//                      $user_role_name,                        
                            $user_request,
                            $bank_name_requested_value,
                            $perons_name,
                            $created_at,
                            $status_flag . $dispatch_flag,
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

    public function action_download_request_file() {
        ob_clean();
        $_GET = Helpers_Utilities::remove_injection($_GET);
        $record_id = !empty($_GET['record_id']) ? Helpers_Utilities::encrypted_key($_GET['record_id'], "decrypt") : '';
        $file_details = Helpers_Utilities::get_ctfu_user_request_files($record_id);
        $file_name = !empty($file_details['received_file_path']) ? $file_details['received_file_path'] : '';
        if (!empty($_GET['record_id'])) {
            $file_path = Helpers_Utilities::ctfu_requests_file_path();
            $file = $file_path . $file_name;
        }
        if (!$file) { // file does not exist
            die('file not found');
        } else {
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename={$file_name}");
            header("Content-Type: application/zip");
            header("Content-Transfer-Encoding: binary");
            header("Content-type:application/pdf");
            // read the file from disk
            readfile($file);
        }
    }

}
