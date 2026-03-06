<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Userreports extends Controller_Working {

    public function __Construct(Request $request, Response $response) {
        parent::__construct($request, $response);
        $this->request = $request;
        $this->response = $response;
    }

    /*
     * User Listing
     */

    public function action_users_list() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 48) == 1) {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';            
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('users_list_post', $post);
                /* Get parameter */
                //$type = $this->request->param('id');
                include 'excel/users_list.inc';
                /* File Included */
                include 'user_functions/users_list.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     *  User Ajax Call data
     */

    public function action_ajaxuserslist() {
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
                $post = Session::instance()->get('users_list_post', array());
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->user_list($post, 'true');
                $profiles = $data->user_list($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    $user_obj = Auth::instance()->get_user();
                    $loginuser = $user_obj->id;
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 'NA';
                        $full_name = ( isset($item['first_name']) ) ? $item['first_name'] : 'NA';
                        $mobile_number = ( isset($item['mobile_number']) ) ? $item['mobile_number'] : 'NA';
                        $full_name .= ' ';
                        $full_name .= ( isset($item['last_name']) ) ? $item['last_name'] : 'NA';
                        $username = ( isset($item['username']) ) ? $item['username'] : '';
                        $username_data = explode('::', $username);
                        $trasfered_flag = (isset($username_data[1]) && $username_data[1] == 'transferred') ? 1 : 0;
                        $user_role_name = (isset($item['user_id']) ) ? Helpers_Utilities::get_user_role_name($item['user_id']) : 'N/A';

                        $designation = ' </br> <span> <b>';
                        $designation .= ( isset($item['job_title']) ) ? $item['job_title'] : 'NA';
                        $designation .= '</b></span>';
                        $cnic_number = ( isset($item['cnic_number']) ) ? $item['cnic_number'] : 'NA';
                        $posting = ( isset($item['posted']) ) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';
                        $posting_cnic = $posting . '</br><b> ' . $cnic_number . '</b>';
                        $countfavt = Helpers_Profile::is_favourite_user($loginuser, $item['user_id']);
                        $id_encrypted = "'" . Helpers_Utilities::encrypted_key($item['id'], "encrypt") . "'";
                        $login_user = Auth::instance()->get_user();
                        $permission = Helpers_Utilities::get_user_permission($login_user->id);
                        $html = '<a class="btn btn-small action" href="' . URL::site('user/user_profile/' . $id_encrypted) . '"><i class="fa fa-folder-open-o"></i> View Profile</a>';
                        if ($trasfered_flag == 0) {
                            if ($countfavt != 0 || $permission == 2) {
                                $html .= '<a class="btn btn-small action item-' . $id_encrypted . '" href="javascript:ConfirmChoiceBlock(' . $id_encrypted . ')"><i class="fa fa-ban"></i> Block User</a>';
                            } else {
                                $html .= '<a class="btn btn-small action item-' . $id_encrypted . '" href="javascript:ConfirmChoiceBlock(' . $id_encrypted . ')"><i class="fa fa-ban"></i> Block User</a> <a class="btn btn-small action item-' . $id_encrypted . '" href="javascript:ConfirmChoice(' . $id_encrypted . ')"><i class="fa fa-plus"></i> Add Favourite</a>';
                            }
                            if ($permission == 1) {
                                if (isset($item['user_password_backup'])) {
                                    if (empty($item['user_password_backup']) || ($item['user_password_backup'] == "NULL")) {
                                        $html .= '<a  class="btn btn-small action"  title="Change password into 12345678" href="javascript:convertpassword(' . $id_encrypted . ')"><i class="fa fa-hand-scissors-o"></i> Change password </a>';
                                    } else {
                                        $html .= '<a  class="btn btn-small action"  title="Revert Password" href="javascript:revert(' . $id_encrypted . ')"><i class="fa fa-exchange"></i> Revert password </a>';
                                    }
                                } else {
                                    $html .= '<a  class="btn btn-small action"  title="Revert Password" href="javascript:revert(' . $id_encrypted . ')"><i class="fa fa-hand-scissors-o"></i> change password </a>';
                                }
                            }
                        }
                        $row = array(
                            // $user_id,
                            $full_name,
                            $username,
                            $user_role_name . $designation,
                            $posting_cnic,
                            $mobile_number,
                            $html
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
        }
    }
    /* Transferred User's Listing */
    public function action_users_transferred_list() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 48) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($login_user->id);
                $access_message = 'Access denied, Contact your technical support team';
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('users_list_post', $post);
                /* Get parameter */
                //$type = $this->request->param('id');
                include 'excel/users_transferred_list.inc';
                /* File Included */
                include 'user_functions/users_transferred_list.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }
    /*
     *  User Transferred Ajax Call data
     */

    public function action_ajaxuserstransferredlist() {
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
                $post = Session::instance()->get('users_list_post', array());
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;
//
//            }

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->users_transferred_list($post, 'true');
                $profiles = $data->users_transferred_list($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    $user_obj = Auth::instance()->get_user();
                    $loginuser = $user_obj->id;
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 'NA';
                        $full_name = ( isset($item['first_name']) ) ? $item['first_name'] : 'NA';
                        $mobile_number = ( isset($item['mobile_number']) ) ? $item['mobile_number'] : 'NA';
                        $full_name .= ' ';
                        $full_name .= ( isset($item['last_name']) ) ? $item['last_name'] : 'NA';
                        $username = ( isset($item['username']) ) ? $item['username'] : '';
                        $username_data = explode('::', $username);
                        $trasfered_flag = (isset($username_data[1]) && $username_data[1] == 'transferred') ? 1 : 0;
                        $user_role_name = (isset($item['user_id']) ) ? Helpers_Utilities::get_user_role_name($item['user_id']) : 'N/A';

                        $designation = ' </br> <span> <b>';
                        $designation .= ( isset($item['job_title']) ) ? $item['job_title'] : 'NA';
                        $designation .= '</b></span>';
                        $cnic_number = ( isset($item['cnic_number']) ) ? $item['cnic_number'] : 'NA';
                        $posting = ( isset($item['posted']) ) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';
                        $posting_cnic = $posting . '</br><b> ' . $cnic_number . '</b>';
                        $countfavt = Helpers_Profile::is_favourite_user($loginuser, $item['user_id']);
                        $id_encrypted = "'" . Helpers_Utilities::encrypted_key($item['id'], "encrypt") . "'";
                        $login_user = Auth::instance()->get_user();
                        $permission = Helpers_Utilities::get_user_permission($login_user->id);
                        $html = '<a class="btn btn-small action" href="' . URL::site('user/user_profile/' . $id_encrypted) . '"><i class="fa fa-folder-open-o"></i> View Profile</a>';
                        if ($trasfered_flag == 0) {
                            if ($countfavt != 0 || $permission == 2) {
                                $html .= '<a class="btn btn-small action item-' . $id_encrypted . '" href="javascript:ConfirmChoiceBlock(' . $id_encrypted . ')"><i class="fa fa-ban"></i> Block User</a>';
                            } else {
                                $html .= '<a class="btn btn-small action item-' . $id_encrypted . '" href="javascript:ConfirmChoiceBlock(' . $id_encrypted . ')"><i class="fa fa-ban"></i> Block User</a> <a class="btn btn-small action item-' . $id_encrypted . '" href="javascript:ConfirmChoice(' . $id_encrypted . ')"><i class="fa fa-plus"></i> Add Favourite</a>';
                            }
                            if ($permission == 1) {
                                if (isset($item['user_password_backup'])) {
                                    if (empty($item['user_password_backup']) || ($item['user_password_backup'] == "NULL")) {
                                        $html .= '<a  class="btn btn-small action"  title="Change password into 12345678" href="javascript:convertpassword(' . $id_encrypted . ')"><i class="fa fa-hand-scissors-o"></i> Change password </a>';
                                    } else {
                                        $html .= '<a  class="btn btn-small action"  title="Revert Password" href="javascript:revert(' . $id_encrypted . ')"><i class="fa fa-exchange"></i> Revert password </a>';
                                    }
                                } else {
                                    $html .= '<a  class="btn btn-small action"  title="Revert Password" href="javascript:revert(' . $id_encrypted . ')"><i class="fa fa-hand-scissors-o"></i> change password </a>';
                                }
                            }
                        }
                        $row = array(
                            // $user_id,
                            $full_name,
                            $username,
                            $user_role_name . $designation,
                            $posting_cnic,
                            $mobile_number,
                            $html
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
        }
    }

    /* Blocked User's Listing */

    public function action_users_list_blocked() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 49) == 1) {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';            
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('users_list_post', $post);
                //$type = $this->request->param('id');
                include 'excel/users_list_blocked.inc';
                /* File Included */
                include 'user_functions/users_list_blocked.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data of blocked users
    public function action_ajaxuserslistblocked() {
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
                $post = Session::instance()->get('users_list_post', array());
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $login_user = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($login_user->id);

                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->user_list_blocked($post, 'true');
                $profiles = $data->user_list_blocked($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    $user_obj = Auth::instance()->get_user();
                    $loginuser = $user_obj->id;
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $full_name = ( isset($item['first_name']) ) ? $item['first_name'] : 'NA';
                        $full_name .= ' ';
                        $full_name .= ( isset($item['last_name']) ) ? $item['last_name'] : 'NA';
                        $designation = ( isset($item['job_title']) ) ? $item['job_title'] : 'NA';
                        $user_role_name = (isset($item['user_id']) ) ? Helpers_Utilities::get_user_role_name($item['user_id']) : 'N/A';
                        $mobile_number = ( isset($item['mobile_number']) ) ? $item['mobile_number'] : 'NA';
                        $district = ( isset($item['posted']) ) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';
                        $id_encrypted = "'" . Helpers_Utilities::encrypted_key($item['id'], "encrypt") . "'";
                        $countfavt = Helpers_Profile::is_favourite_user($loginuser, $item['user_id']);
                        $blockreason_data = Helpers_Profile::get_user_block_reasons($item['user_id']);
                        $blockreasons = '';
                        $countblock = 1;
                        foreach ($blockreason_data as $brd) {
                            $blockreasons .= $countblock . '# ' . $brd->block_reason . ' (' . $brd->timestamp . ')';
                            $blockreasons .= '</br>';
                            $countblock ++;
                        }

                        if ($permission == 1) {
                            $html = '<a class="btn btn-small action" href="' . URL::site('user/user_profile/' . Helpers_Utilities::encrypted_key($item['id'], "encrypt")) . '"><i class="fa fa-folder-open-o"></i> View Profile</a><a class="btn btn-small action item-' . $id_encrypted . '" href="javascript:ConfirmChoiceUnBlock(' . $id_encrypted . ')"><i class="fa fa-ban"></i>  Un-Block User</a>';
                        } else {
                            $html = '<a class="btn btn-small action" href="' . URL::site('user/user_profile/' . Helpers_Utilities::encrypted_key($item['id'], "encrypt")) . '"><i class="fa fa-folder-open-o"></i> View Profile</a>';
                        }


                        $row = array(
                            $full_name,
                            $user_role_name,
                            $designation,
                            $district,
                            $mobile_number,
                            $blockreasons,
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

    /* New User's Listing */

    public function action_users_list_new() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 50) == 1) {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            //$access_user_approval = Helpers_Profile::get_user_access_permission($login_user->id, 19);
            $access_message = 'Access denied, Contact your technical support team';
            //&& $access_user_approval == 1            
                /* File Included */
                include 'user_functions/users_list_new.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data of blocked users
    public function action_ajaxuserslistnew() {
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
                $post = Session::instance()->get('users_list_post', array());
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->user_list_new($post, 'true');
                $profiles = $data->user_list_new($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    $user_obj = Auth::instance()->get_user();
                    $loginuser = $user_obj->id;
                    foreach ($profiles as $item) {
                        /* Concate name full name */

                        $user_id = ( isset($item['id']) ) ? $item['id'] : 0;
                        $full_name = ( isset($item['first_name']) ) ? $item['first_name'] : 'NA';
                        $full_name .= ' ';
                        $full_name .= ( isset($item['last_name']) ) ? $item['last_name'] : 'NA';
                        $designation = ( isset($item['job_title']) ) ? $item['job_title'] : 'NA';
                        $user_role_name = (isset($item['user_id']) ) ? Helpers_Utilities::get_user_role_name($item['user_id']) : 'N/A';

                        $district = ( isset($item['posted']) ) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';

                        $created_by = ( isset($item['created_by']) ) ? $item['created_by'] : 'NA';
                        /*$created_by_name = ( isset($item['created_by']) ) ? Helpers_Profile::get_user_perofile($created_by)->first_name : ' ';
                        $created_by_name .= ' ';
                        $created_by_name .= ( isset($item['created_by']) ) ? Helpers_Profile::get_user_perofile($created_by)->last_name : ' ';*/
                        //$designation1 = ( isset($item['created_by']) ) ? Helpers_Profile::get_user_perofile($created_by)->job_title : ' ';
                        $order_no = ( isset($item['order_no']) ) ? $item['order_no'] : 'NA';
                        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : ' ';
                        $id_encrypted = "'" . Helpers_Utilities::encrypted_key($item['id'], "encrypt") . "'";
                        $html = '<a class="btn btn-small action item-' . $item['id'] . '" href="javascript:ConfirmChoiceBlock(' . $id_encrypted . ')"><i class="fa fa-ban"></i> Block</a><a class="btn btn-small action item-' . $id_encrypted . '" href="javascript:ConfirmChoiceApprove(' . $id_encrypted . ')"><i class="fa fa-check"></i> Approve</a>';

                        $row = array(
                            $user_id,
                            $full_name,
                            $designation,
                            $user_role_name,
                            $district,
                          //  $created_by_name,
                          //  $designation1,
                            $order_no . '- Smart id: ' . $created_by,
                            $created_at,
                            $html
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex); exit;
        }
    }

    /* User's Favourite user Listing */

    public function action_users_favourite_user() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 41) == 1) {
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('users_favourite_user_post', $post);
                /* Export to excel file include */
                include 'excel/users_favourite_user.inc';
                /* File Included */
                include 'user_functions/users_favourite_user.inc';
            } elseif ($permission == 4) {
                $this->redirect('userreports/users_favourite_agent/' . $login_user->id);
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     *  User Ajax Call data
     */

    //ajax call for data
    public function action_ajaxusersfavouriteuser() {
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
                $post = Session::instance()->get('users_favourite_user_post', array());
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->user_favourite_user($post, 'true');

                $profiles = $data->user_favourite_user($post, 'false');



                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {


                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 'NA';

                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $user_role_name = (isset($item['user_id']) ) ? Helpers_Utilities::get_user_role_name($item['user_id']) : 'N/A';
                        $user_job_title = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_job_title($item['user_id']) : 'NA';

                        $district = ( isset($item['user_id']) ) ? Helpers_Profile::get_user_region_district($item['user_id']) : 'NA';

                        $count = ( isset($item['count']) ) ? $item['count'] : 'NA';
                        $member_name_link = '<a href="' . URL::site('userreports/users_favourite_agent/' . Helpers_Utilities::encrypted_key($item['user_id'], 'encrypt')) . '" > View Detail </a>';


                        $row = array(
                            $user_name,
                            $user_role_name,
                            $user_job_title,
                            $district,
                            $count,
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
     * User's Favourite person Listing
     */

    public function action_user_favourite_person() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 42) == 1) {
            $user_obj = Auth::instance()->get_user();
            $role = Helpers_Utilities::get_user_role_id($user_obj->id);
            $permission = Helpers_Utilities::get_user_permission($user_obj->id);
            $user_id = $user_obj->id;
            if ($permission == 1 || $permission == 3) {
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('user_favourite_person_post', $post);
                /* File Included */
                include 'excel/user_favourite_person.inc';
                include 'user_functions/user_favourite_person.inc';
            } else {
                $this->redirect('userreports/user_favourite_person_list/' . Helpers_Utilities::encrypted_key($user_id, "encrypt"));
            }
            }else{
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->redirect('Userdashboard/dashboard');
        }
    }

    //ajax call for data
    public function action_ajaxuserfavouriteperson() {
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
                $post = Session::instance()->get('user_favourite_person_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->user_favourite_person($post, 'true');
                $profiles = $data->user_favourite_person($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        /* Concate name full name */

                        $userid = ( isset($item['user_id']) ) ? $item['user_id'] : 'NA';

                        $username = ( isset($item['fname']) ) ? $item['fname'] : 'NA';
                        $username .= ' ';
                        $username .= ( isset($item['lname']) ) ? $item['lname'] : 'NA';

                        $user_role_name = (isset($item['user_id']) ) ? Helpers_Utilities::get_user_role_name($item['user_id']) : 'N/A';

                        $user_designation = ( isset($item['designaion']) ) ? $item['designaion'] : 'NA';
                        $user_district = ( isset($item['user_id']) ) ? Helpers_Profile::get_user_region_district($item['user_id']) : 'NA';
                        $total = ( isset($item['count']) ) ? $item['count'] : 'NA';
                        $member_name_link = '<a href="' . URL::site('userreports/user_favourite_person_list/' . Helpers_Utilities::encrypted_key($item['user_id'], "encrypt")) . '" > View Detail </a>';

                        $row = array(
                            $username,
                            $user_role_name,
                            $user_designation,
                            $user_district,
                            $total,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }
            echo json_encode($output);
            exit();
        } catch (Exception $e) {
            
        }
    }

    public function action_no_of_login() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 45) == 1) {
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('no_of_login_post', $post);
                /* Excel Export File Included */
                include 'excel/no_of_login.inc';
                /* File Included */
                include 'user_functions/no_of_login.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxnooflogin() {
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
                $post = Session::instance()->get('no_of_login_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_Userreport;
                $rows_count = $data->no_of_login($post, 'true');
                $profiles = $data->no_of_login($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $id = ( isset($item['id']) ) ? $item['id'] : 0;
                        $name = ( isset($item['id']) ) ? Helpers_Utilities::get_user_name($item['id']) : 'NA';
                        $name .= '[';
                        $name .= '<a class="btn btn-small action" href="' . URL::site('user/user_profile/' . Helpers_Utilities::encrypted_key($id, "encrypt")) . '"> View Profile</a>';
                        $name .= ']';
                        $user_role_name = (isset($item['id']) ) ? Helpers_Utilities::get_user_role_name($item['id']) : 'N/A';
                        $designation = ( isset($item['job_title']) ) ? $item['job_title'] : 'NA';
                        $posting = ( isset($item['posted']) ) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';
                        $nooflogin = ( isset($item['logins']) ) ? $item['logins'] : 'NA';
                        $lastLogin = ( isset($item['last_login']) ) ? date('m/d/Y H:i:s', $item['last_login']) : 'NA';

                        $row = array(
                            $id,
                            $name,
                            $user_role_name,
                            $designation,
                            $posting,
                            $lastLogin,
                            $nooflogin
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

    public function action_no_request_send() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 43) == 1) {
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                $post = array_merge($post, $_POST);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('no_request_send_post', $post);
                /* Excel Export File Included */
                include 'excel/no_request_send.inc';
                /* File Included */
                include 'user_functions/no_request_send.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxuserrequestsend() {
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
                $post = Session::instance()->get('no_request_send_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $sdate = !empty($post['sdate']) ? $post['sdate'] : '';
                $edate = !empty($post['edate']) ? $post['edate'] : '';
                $data = new Model_Userreport;
                $rows_count = $data->no_request_send($post, 'true');
                $profiles = $data->no_request_send($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 'NA';

                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $user_role_name = (isset($item['user_id']) ) ? Helpers_Utilities::get_user_role_name($item['user_id']) : 'N/A';
                        $user_designation = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_job_title($item['user_id']) : 'NA';
                        $user_region = (!empty($item['region_id']) ) ? Helpers_Utilities::get_region($item['region_id']) : 'Head Quarters';
                        $user_posting = ( isset($item['posted']) ) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';
                        $count = ( isset($item['count']) ) ? $item['count'] : 0;
                        //this
                        $member_name_link = '<form role="form" id="view_form" name="view_form" class="ipf-form" action="' . URL::site('userreports/no_request_send_type/') . '" method="POST">'
                                . '<input type="hidden" readonly="readonly" class="form-control" id="userid" value="' . Helpers_Utilities::encrypted_key($item['user_id'], 'encrypt') . '" name="userid">'
                                . '<input type="hidden" readonly="readonly" class="form-control" id="sdate" value="' . $sdate . '" name="sdate">'
                                . '<input type="hidden" readonly="readonly" class="form-control" id="edate" value="' . $edate . '" name="edate">'
                                . '<button type="submit" class="btn btn-primary"> View Detail </button>'
                                . '</form>';
                        $row = array(
                            //$user_id,
                            $user_name,
                            $user_role_name,
                            $user_designation,
                            $user_region,
                            $user_posting,
                            $count,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }
            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
        }
    }

    public function action_no_request_send_detail() {
        try {
            /* Posted Data */
            $post = $this->request->post();
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $post = array_merge($post, $_POST);
            $post = Helpers_Utilities::remove_injection($post);
            if (!empty($_POST)) {
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('no_request_send_detail_post', $post);
                /* Excel Export File Included */
                include 'excel/no_request_send_detail.inc';
                /* File Included */
                include 'user_functions/no_request_send_detail.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxuserrequestsenddetail() {
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
                $post = Session::instance()->get('no_request_send_detail_post', array());
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['userid']) && !empty($post['request_type'])) {
                    $userid = Helpers_Utilities::encrypted_key($post['userid'], 'decrypt');
                    $request_type = Helpers_Utilities::encrypted_key($post['request_type'], 'decrypt');
                } else {
                    $userid = NULL;
                    $request_type = NULL;
                }
                $data = new Model_Userreport;
                $rows_count = $data->no_request_send_detail($post, 'true', $userid, $request_type);
                $profiles = $data->no_request_send_detail($post, 'false', $userid, $request_type);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';

                        // $request_type = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type($item['user_request_type_id']) : 'NA';

                        $requested_value = ( isset($item['requested_value']) ) ? $item['requested_value'] : 'NA';

                        $requested_value .= ( isset($item['request_id']) ) ? '<br/><b>' . $item['request_id'] . '<b>' : '';
                        $reason = ( isset($item['reason']) ) ? $item['reason'] : 'NA';
                        $concerned_person_id = ( isset($item['concerned_person_id']) ) ? $item['concerned_person_id'] : 'NA';
                        if ($concerned_person_id > 0) {
                            $perons_name = ( isset($item['concerned_person_id']) ) ? Helpers_Person::get_person_name($item['concerned_person_id']) : 'NA';
                            $perons_name .= '[';
                            $perons_name .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['concerned_person_id'], "encrypt")) . '" > View Profile </a>';
                            $perons_name .= ']';
                        } else {
                            $perons_name = " ";
                        }
                        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : 'NA';
                        $view_request_status = '<a href="' . URL::site('userrequest/request_status_detail/' . Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt')) . '" > View Detail </a>';

                        $row = array(
                            $user_name,
                            // $request_type,
                            $requested_value,
                            $reason,
                            $perons_name,
                            $created_at,
                            $view_request_status,
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }
            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
        }
    }

    public function action_no_request_send_type() {

        try {
            /* Posted Data */
            $post = $this->request->post();
            $_POST = Helpers_Utilities::remove_injection($_POST);
            if (!empty($_POST)) {
                $post = array_merge($post, $_POST);
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('no_request_send_type_post', $post);
                /* Excel Export File Included */
                include 'excel/no_request_send_type.inc';
                /* File Included */
                include 'user_functions/no_request_send_type.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxuserrequestsendtype() {
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
                $post = Session::instance()->get('no_request_send_type_post', array());
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $sdate = !empty($post['sdate']) ? $post['sdate'] : '';
                $edate = !empty($post['edate']) ? $post['edate'] : '';
                //echo $post; exit;
                if (!empty($post['userid'])) {
                    $userid = (int) Helpers_Utilities::encrypted_key($post['userid'], 'decrypt');
                } else {
                    $userid = 0;
                }
                $data = new Model_Userreport;
                $rows_count = $data->no_request_send_type($post, 'true', $userid);
                $profiles = $data->no_request_send_type($post, 'false', $userid);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 'NA';
                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $user_designation = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_job_title($item['user_id']) : 'NA';
                        $user_region = (!empty($item['region_id']) ) ? Helpers_Utilities::get_region($item['region_id']) : 'Head Quarters';
                        $user_posting = ( isset($item['posted']) ) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';
                        $request_type = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type_name($item['user_request_type_id']) : 'NA';
                        $count = ( isset($item['count']) ) ? $item['count'] : 0;
                        $user_id_en = Helpers_Utilities::encrypted_key($item['user_id'], 'encrypt');
                        $request_type_en = Helpers_Utilities::encrypted_key($item['user_request_type_id'], 'encrypt');

                        $member_name_link = '<form role="form" id="view_form" name="view_form" class="ipf-form" action="' . URL::site('userreports/no_request_send_detail/') . '" method="POST">'
                                . '<input type="hidden" readonly="readonly" class="form-control" id="userid" value="' . $user_id_en . '" name="userid">'
                                . '<input type="hidden" readonly="readonly" class="form-control" id="request_type" value="' . $request_type_en . '" name="request_type">'
                                . '<input type="hidden" readonly="readonly" class="form-control" id="sdate" value="' . $sdate . '" name="sdate">'
                                . '<input type="hidden" readonly="readonly" class="form-control" id="edate" value="' . $edate . '" name="edate">'
                                . '<button type="submit" class="btn btn-primary"> View Detail </button>'
                                . '</form>';

                        // $member_name_link = '<a href="' . URL::site('userreports/no_request_send_detail/?userid=' . $user_id_en . '&request_type=' . $request_type_en) . '" > View Detail </a>';
                        $row = array(
                            //$user_id,
                            $user_name,
                            $user_designation,
                            $user_region,
                            $user_posting,
                            $request_type,
                            $count,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }
            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
        }
    }

    //audit report basic
    public function action_audit_report_basic() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 46) == 1) {

                /* Posted Data */
                $search_post = $this->request->post();
                $search_post = Helpers_Utilities::remove_injection($search_post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('audit_report_basic_post', $search_post);                    
                /* Excel Export File Included */
                include 'excel/audit_report_basic.inc';
                /* File Included */
                include 'user_functions/audit_report_basic.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

//ajax call for data
    public function action_ajaxauditreportbasic() {
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
                $post = Session::instance()->get('audit_report_basic_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->audit_report_basic($post, 'true');
                $profiles = $data->audit_report_basic($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                $sdate = !empty($post['sdate']) ? $post['sdate'] : '';
                $edate = !empty($post['edate']) ? $post['edate'] : '';
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $posting_place = '';
                        $posting = ( isset($item['posted']) ) ? $item['posted'] : 'NA';
                        $result = explode('-', $posting);
                        switch ($result[0]) {
                            case 'h':
                                $posting_place = 'Head Quarters ';
                                $posting_place .= Helpers_Utilities::get_headquarter($result[1]);
                                break;
                            case 'r':
                                $posting_place = 'Region ';
                                $posting_place .= Helpers_Utilities::get_region($result[1]);
                                break;
                            case 'd':
                                $posting_place = 'District ';
                                $posting_place .= Helpers_Utilities::get_district($result[1]);
                                break;
                            case 'p':
                                $posting_place = 'Police Station ';
                                $data = Helpers_Utilities::get_ps_name($result[1]);
                                $posting_place .= isset($data->name) ? $data->name : 'Unknown';
                                break;
                        }
                        $count = ( isset($item['count']) ) ? $item['count'] : 'NA';
                        $member_name_link = '<form role="form" name="advance_search_form" id="advance_search_form" class="ipf-form" action="' . URL::site('userreports/audit_report/') . '" method="POST">'
                                . '<input type="hidden" readonly="readonly" class="form-control" id="posted" value="' . Helpers_Utilities::encrypted_key($posting, 'encrypt') . '" name="posted">'
                                . '<input type="hidden" readonly="readonly" class="form-control" id="sdate" value="' . $sdate . '" name="sdate">'
                                . '<input type="hidden" readonly="readonly" class="form-control" id="edate" value="' . $edate . '" name="edate">'
                                . '<button type="submit" class="btn btn-primary"> View Detail </button>'
                                . '</form>';

                        $row = array(
                            $posting_place,
                            $count,
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

    public function action_audit_report() {
        try {
            /* Posted Data */
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $_POST = Helpers_Utilities::remove_injection($_POST);
            if (!empty($_POST)) {
                $post = array_merge($post, $_POST);
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('audit_report_post', $post);
                /* Excel Export File Included */
                include 'excel/audit_report.inc';
                /* File Included */
                include 'user_functions/audit_report.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxauditreport() {
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
                $post = Session::instance()->get('audit_report_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['posted'])) {
                    $posted_encrypted = $post['posted'];
                    $posted = Helpers_Utilities::encrypted_key($posted_encrypted, 'decrypt');
                } else {
                    $posted = NULL;
                }
                $sdate = !empty($post['sdate']) ? $post['sdate'] : '';
                $edate = !empty($post['edate']) ? $post['edate'] : '';
                $data = new Model_Userreport;
                $rows_count = $data->audit_report($post, 'true', $posted);
                $profiles = $data->audit_report($post, 'false', $posted);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 'NA';
                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        if (!empty($item['user_id']))
                            $blocked = Helpers_Profile::get_user_blocked($item['user_id']);
                        $user_name .= ( isset($blocked) && $blocked != 0 ) ? '   <span class="badge badge-pill badge-danger">Blocked</span>' : '';

                        $designation = ( isset($item['job_title']) ) ? $item['job_title'] : 'NA';

                        $posting = ( isset($item['posted']) ) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';
                        // $district = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_district_name($item['user_id']) : 'NA';
                        $reg_id = $item['region_id'];
                        if ($reg_id == 0) {
                            $region = "Head Quaters";
                        } else {
                            $region = ( isset($item['region_id']) ) ? Helpers_Utilities::get_region($item['region_id']) : 'NA';
                        }
                        $requestcount = ( isset($item['count']) ) ? $item['count'] : 0;
                        $member_name_link = '<form role="form" name="advance_search_form" id="advance_search_form" class="ipf-form" action="' . URL::site('userreports/no_request_send_type/') . '" method="POST">'
                                . '<input type="hidden" readonly="readonly" class="form-control" id="userid" value="' . Helpers_Utilities::encrypted_key($item['user_id'], 'encrypt') . '" name="userid">'
                                . '<input type="hidden" readonly="readonly" class="form-control" id="sdate" value="' . $sdate . '" name="sdate">'
                                . '<input type="hidden" readonly="readonly" class="form-control" id="edate" value="' . $edate . '" name="edate">'
                                . '<button type="submit" class="btn btn-primary"> View Detail </button>'
                                . '</form>';

                        $row = array(
                            $user_name,
                            $designation,
                            $posting,
                            $region,
                            $requestcount,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }
            echo json_encode($output);
            exit();
        } catch (Exception $e) {
            echo '<pre>';
            print_r($e);
            exit;
        }
    }

    public function action_performance_report() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 47) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($login_user->id);
                $access_message = 'Access denied, Contact your technical support team';
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('performance_report_post', $post);

                /* Excel Export File Included */
                include 'excel/performance_report.inc';
                /* File Included */
                include 'user_functions/performance_report.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxperformancereport() {
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
                $post = Session::instance()->get('performance_report_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_Userreport;
                $rows_count = $data->performance_report($post, 'true');
                $profiles = $data->performance_report($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $totalwhite = $totalgray = $totalblack = 0;
                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 'NA';
                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $user_name .= '</br>[';
                        $user_name .= '<a href="' . URL::site('user/user_profile/' . Helpers_Utilities::encrypted_key($item['user_id'], "encrypt")) . '" > View Profile </a>';
                        $user_name .= ']';

                        $user_role_name = (isset($item['user_id']) ) ? Helpers_Utilities::get_user_role_name($item['user_id']) : 'N/A';
                        $designation = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_job_title($item['user_id']) : 'NA';
                        $posting = ( isset($item['user_id']) ) ? Helpers_Profile::get_user_region_district($item['user_id']) : 'NA';
                        $totalrequests = ( isset($item['user_id']) ) ? Helpers_Utilities::get_request_count($item['user_id']) : 'NA';
                        $totalfavperson = ( isset($item['user_id']) ) ? Helpers_Utilities::get_favourite_person_count($item['user_id']) : 'NA';
                        $totalfavuser = ( isset($item['user_id']) ) ? Helpers_Utilities::get_favourite_user_count($item['user_id']) : 'NA';
                        $totalwhite = ( isset($item['user_id']) ) ? Helpers_Utilities::get_users_white_person($item['user_id']) : 'NA';
                        $totalgray = ( isset($item['user_id']) ) ? Helpers_Utilities::get_users_grey_person($item['user_id']) : 'NA';
                        $totalblack = ( isset($item['user_id']) ) ? Helpers_Utilities::get_users_black_person($item['user_id']) : 'NA';
                        $totallogin = ( isset($item['user_id']) ) ? Helpers_Utilities::get_login_count($item['user_id']) : 'NA';

                        $row = array(
                            //$user_id,
                            $user_name,
                            $user_role_name,
                            $designation,
                            $posting,
                            $totalrequests,
                            $totalfavperson,
                            $totalfavuser,
                            $totalwhite,
                            $totalgray,
                            $totalblack,
                            $totallogin,
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
     * User Panel Log
     */

    public function action_panel_log() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 57) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $access_message = 'Access denied, Contact your technical support team';
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('panel_log', $post);
                /* Excel Export File Included */
                include 'excel/panel_log.inc';
                /* File Included */
                include 'user_functions/panel_log.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
    
     public function action_panel_log_officewise() {
        
        try {
            
            if (Helpers_Utilities::chek_role_access($this->role_id, 57) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $access_message = 'Access denied, Contact your technical support team';
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('panel_log', $post);
                /* Excel Export File Included */
             
                include 'excel/panel_log_officewise.inc';
                /* File Included */
                
                include 'user_functions/panel_log_officewise.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxpanellog() {
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
                $post = Session::instance()->get('panel_log', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->user_panel_log($post, 'true');

                $profiles = $data->user_panel_log($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $accessed_url= explode("?",$_SERVER['REQUEST_URI']);
                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $designation = ( isset($item['job_title']) ) ? $item['job_title'] : 'NA';
                        $user_role_name = (isset($item['user_id']) ) ? Helpers_Utilities::get_user_role_name($item['user_id']) : 'N/A';
                        $district = ( isset($item['posted']) ) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';
                        if ($item['region_id'] == 0) {
                            $region = "Head Quarters";
                        } else {
                            $region = ( isset($item['region_id']) ) ? Helpers_Utilities::get_region($item['region_id']) : 'NA';
                        }
                        $activity_id = ( isset($item['user_activity_type_id']) ) ? $item['user_activity_type_id'] : 0;
                        switch ($activity_id) {
                            case 8:
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:searchdetail(' . $item['timeline_id'] . ')" > View Detail </a>';
                                $activity .= "] ";
                                break;
                            case 66: case 67: case 68: case 72: case 73: case 74: case 76: case 84: case 85:
                            case 61: case 60: case 58: case 57: case 55: case 53: case 52:
                            case 65: case 49: case 46:
                            case 45: case 43: case 42: case 40: case 39: case 37: case 36:
                            case 33: case 34: case 32: case 31: case 30: case 12: case 9 :
                            case 22: case 23: case 24: case 25: case 90:
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 27: case 28:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:project_details(' . $item['timeline_id'] . ')" > View Data </a>';
                                $activity .= "] ";
                                break;
                            case 10:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:requestdetail(' . $item['timeline_id'] . ')" > View Request Data </a>';
                                $activity .= "] ";
                                break;
                            case 11:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:categorydetail(' . $item['timeline_id'] . ')" > View Details </a>';
                                $activity .= "] ";
                                break;
                            case 26: case 48: case 77: case 78: case 79: case 80: case 50: case 51: case 64:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:activitydetail(' . $item['timeline_id'] . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 81: case 82: case 83:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:activitydetail(' . $item['timeline_id'] . ')" > View Details </a>';
                                $activity .= "] ";
                                break;
                            case 35:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:identitydeletedetail(' . $item['timeline_id'] . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 38:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:educationdeletedetail(' . $item['timeline_id'] . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 41:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:incomesourcedeletedetail(' . $item['timeline_id'] . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 44:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:bankdetailsdeletedetail(' . $item['timeline_id'] . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 47:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:assetdeletedetail(' . $item['timeline_id'] . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 54:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:criminalrecorddeletedetail(' . $item['timeline_id'] . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 59:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:reportdeletedetail(' . $item['timeline_id'] . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 62:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:affiliationdeletedetail(' . $item['timeline_id'] . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 75:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:tagupdationdetail(' . $item['timeline_id'] . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 29:
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('user/user_profile/' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 19: case 20: case 21: case 4: case 5:
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $details_data = Helpers_Utilities::search_person_details($item['timeline_id']);
                                $new_user = !empty($details_data->user_id) ? $details_data->user_id : 0;
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('user/user_profile/' . Helpers_Utilities::encrypted_key($new_user, "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            default:
                                $activity = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                        }
                        $datetime = ( isset($item['activity_time']) ) ? $item['activity_time'] : 'NA';
                        $row = array(
                            $user_name,
                            $designation,
                            $user_role_name,
                            $district,
                            $region,
                            $activity,
                            $datetime
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
    public function action_ajaxpanellogpostingwise() {
//        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('panel_log', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->user_panel_log_officewise($post, 'true');

                $profiles = $data->user_panel_log_officewise($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                   
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                      $district = ( isset($item['posted']) ) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';
                      $total = !empty($item['total']) ? $item['total'] :0;
                      $row = array(
                            $district,
                            $total,
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
//        } catch (Exception $ex) {
//            
//        }
    }
    /*
     * User url hits log
     */

    public function action_url_hits_log() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 58) == 1) {
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('url_hits_log', $post);
                /* Excel Export File Included */
                include 'excel/panel_log.inc';
                /* File Included */
                include 'user_functions/url_hits_log.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxurlhitslog() {
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
                $post = Session::instance()->get('url_hits_log', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->user_url_hits_log($post, 'true');

                $profiles = $data->user_url_hits_log($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $designation = ( isset($item['job_title']) ) ? $item['job_title'] : 'NA';
                        $user_role_name = (isset($item['user_id']) ) ? Helpers_Utilities::get_user_role_name($item['user_id']) : 'N/A';
                        $district = ( isset($item['posted']) ) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';
                        if ($item['region_id'] == 0) {
                            $region = "Head Quarters";
                        } else {
                            $region = ( isset($item['region_id']) ) ? Helpers_Utilities::get_region($item['region_id']) : 'NA';
                        }
                        $user_data = $user_name . ' (' . $designation . ')' . '</br><b>Role:</b> ' . $user_role_name . '</br><b>District:</b> ' . $district . '</br><b>Region:</b> ' . $region;
                        $datetime = ( isset($item['timestamp']) ) ? $item['timestamp'] : 'NA';
                        $user_agent = ( isset($item['user_agent']) ) ? $item['user_agent'] : 'NA';
                        $accessed_url = ( isset($item['accessed_url']) ) ? $item['accessed_url'] : 'NA';
                        $user_ip = ( isset($item['user_ip']) ) ? $item['user_ip'] : 'NA';
                        $accessed_url_status_code = ( isset($item['accessed_url_status_code']) ) ? $item['accessed_url_status_code'] : 'NA';
                        $row = array(
                            $user_data,
                            $user_ip,
                            $user_agent,
                            $accessed_url,
                            $accessed_url_status_code,
                            $datetime
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

    //there1
    public function action_searchpersondetails() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $time_line_id = $_POST['id'];
            $data = "";
            $persons = Helpers_Utilities::search_person_details($time_line_id);
            $data .= '<tr>';
            $data .= '<td>' . str_replace('-', '<br>', $persons->key_name) . '</td>';
            $data .= '<td>' . str_replace('-', '<br>', $persons->key_value) . '</td>';
            $data .= '</tr>';
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(66);
        }
    }

    //there1
    public function action_projectdetails() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $time_line_id = !empty($_POST['id']) ? $_POST['id'] : 0;
            $data = "";
            $project_data = Helpers_Utilities::search_person_details($time_line_id);
            $region_name = Helpers_Utilities::get_region($project_data->key_value);
            $data .= '<tr>';
            $data .= '<td>' . str_replace('-', '<br>', $project_data->key_name) . '</td>';
            $data .= '<td>' . $region_name . '</td>';
            $data .= '</tr>';
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Activity log request Details
    public function action_requestdetails() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $time_line_id = !empty($_POST['id']) ? $_POST['id'] : 0;
            $data = "";
            $persons = Helpers_Utilities::search_person_details($time_line_id);
            $data .= '<tr>';
            $data .= '<td>' . Helpers_Utilities::get_request_type($persons->key_name) . '</td>';
            if ($persons->key_name != 8) {
                $data .= '<td>' . Helpers_Utilities::get_companies_data($persons->request_company)->company_name . '</td>';
            } else {
                $data .= '<td>' . 'Regional Office/Head Quarters' . '</td>';
            }

            $data .= '<td>' . $persons->key_value . '</td>';
            $data .= '</tr>';
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Activity log Category Change Details
    public function action_categorychangedetails() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $time_line_id = !empty($_POST['id']) ? $_POST['id'] : 0;
            $data = "";
            $activity_data = Helpers_Utilities::timeline_details($time_line_id);
            $activity = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($activity_data->person_id, "encrypt")) . '" > [View Profile] </a>';
            $detaildata = Helpers_Utilities::search_person_details($time_line_id);
            $data .= '<tr>';
            $data .= '<td>' . Helpers_Person::get_person_name($activity_data->person_id) . $activity . '</td>';
            $data .= '<td>' . Helpers_Utilities::get_category_name($detaildata->key_name) . '</td>';
            $data .= '<td>' . Helpers_Utilities::get_category_name($detaildata->key_value) . '</td>';
            $data .= '</tr>';
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Activity log Category Change Details
    public function action_identitydeletedetail() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $time_line_id = !empty($_POST['id']) ? $_POST['id'] : 0;
            $data = "";
            $detaildata = Helpers_Utilities::search_person_details($time_line_id);
            $data .= '<tr>';
            $va = Helpers_Person::get_person_identity_type($detaildata->key_name);
            $identity_name = isset($va) ? $va : 'Un-Known';
            $data .= '<td>' . $identity_name . '</td>';
            $data .= '<td>' . $detaildata->key_value . '</td>';
            $data .= '</tr>';
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Activity log Activity Details
    public function action_activitydetails() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $time_line_id = !empty($_POST['id']) ? $_POST['id'] : 0;
            $data = "";
            $detaildata = Helpers_Utilities::search_person_details($time_line_id);
            $data .= '<tr>';
            $data .= '<td>' . $detaildata->key_name . '</td>';
            $data .= '<td>' . $detaildata->key_value . '</td>';
            $data .= '</tr>';
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Activity log Education delete 
    public function action_educationdeletedetail() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $time_line_id = !empty($_POST['id']) ? $_POST['id'] : 0;
            $data = "";
            $detaildata = Helpers_Utilities::search_person_details($time_line_id);
            $var1 = explode('-', (isset($detaildata->key_name) ? $detaildata->key_name : 'Unknown-Unknown'));
            if ($var1[0] == 0) {
                $var1[0] = 'Religious';
            } else {
                $var1[0] = 'Non Religious';
            }
            $data .= '<td>' . (isset($var1[0]) ? $var1[0] : 'Unknown') . '</td>';
            $data .= '<td>' . (isset($var1[1]) ? $var1[1] : 'Unknown') . '</td>';
            $var2 = explode('-', (isset($detaildata->key_value) ? $detaildata->key_value : 'Unknown-Unknown'));
            $data .= '<td>' . (isset($var2[0]) ? $var2[0] : 'Unknown') . '</td>';
            $data .= '<td>' . (isset($var2[1]) ? $var2[1] : 'Unknown') . '</td>';
            $data .= '</tr>';
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Activity log Income source delete 
    public function action_incomesourcedeletedetail() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $time_line_id = !empty($_POST['id']) ? $_POST['id'] : 0;
            $data = "";
            $detaildata = Helpers_Utilities::search_person_details($time_line_id);
            $data .= '<tr>';
            $data .= '<td>' . $detaildata->key_name . '</td>';
            $var2 = explode('<>', $detaildata->key_value);
            $data .= '<td>' . $var2[0] . '</td>';
            $data .= '<td>' . $var2[1] . '</td>';
            $data .= '</tr>';
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Activity log Bank Details delete 
    public function action_bankdetailsdeletedetail() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $time_line_id = !empty($_POST['id']) ? $_POST['id'] : 0;
            $data = "";
            $detaildata = Helpers_Utilities::search_person_details($time_line_id);
            $data .= '<tr>';
            $var1 = explode('-', $detaildata->key_name);
            $data .= '<td>' . (isset($var1[0]) ? $var1[0] : 'Unknown') . '</td>';
            $data .= '<td>' . (isset($var1[1]) ? $var1[1] : 'Unknown') . '</td>';
            $var2 = explode('-', $detaildata->key_value);
            $data .= '<td>' . (isset($var2[0]) ? $var2[0] : 'Unknown') . '</td>';
            $data .= '<td>' . (isset($var2[1]) ? $var2[1] : 'Unknown') . '</td>';
            $data .= '</tr>';
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Activity log Assets delete 
    public function action_assetdeletedetail() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $time_line_id = !empty($_POST['id']) ? $_POST['id'] : 0;
            $data = "";
            $detaildata = Helpers_Utilities::search_person_details($time_line_id);
            $data .= '<tr>';
            $var1 = explode('<>', $detaildata->key_name);
            $data .= '<td>' . (isset($var1[0]) ? $var1[0] : 'Unknown') . '</td>';
            $data .= '<td>' . (isset($var1[1]) ? $var1[1] : 'Unknown') . '</td>';
            $data .= '<td>' . (isset($detaildata->key_value) ? $detaildata->key_value : 'Unknown') . '</td>';
            $data .= '</tr>';
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Activity log Criminal Record Delete
    public function action_criminalrecorddeletedetail() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $time_line_id = !empty($_POST['id']) ? $_POST['id'] : 0;
            $data = "";
            $detaildata = Helpers_Utilities::search_person_details($time_line_id);
            $data .= '<tr>';
            $var1 = explode('<>', $detaildata->key_name);
            $data .= '<td>' . (isset($var1[0]) ? $var1[0] : 'Unknown') . '</td>';
            $data .= '<td>' . (isset($var1[1]) ? $var1[1] : 'Unknown') . '</td>';
            $data .= '<td>' . (isset($var1[2]) ? Helpers_Utilities::get_punjab_police_station($var1[2]) : 'Unknown') . '</td>';
            $var2 = explode('<>', $detaildata->key_value);
            $data .= '<td>' . (isset($var2[0]) ? $var2[0] : 'Unknown') . '</td>';
            $data .= '<td>' . (isset($var2[1]) ? Helpers_Utilities::get_case_position_name($var2[1]) : 'Unknown') . '</td>';
            $data .= '<td>' . (isset($var2[2]) ? Helpers_Utilities::get_accused_position_name($var2[2]) : 'Unknown') . '</td>';
            $data .= '</tr>';
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Activity log Criminal Record Delete
    public function action_reportdeletedetail() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $time_line_id = !empty($_POST['id']) ? $_POST['id'] : 0;
            $data = "";
            $detaildata = Helpers_Utilities::search_person_details($time_line_id);
            $data .= '<tr>';
            $var1 = explode('<>', $detaildata->key_name);
            $data .= '<td>' . (isset($var1[0]) ? Helpers_Utilities::get_report_type_name($var1[0]) : 'Unknown') . '</td>';
            $data .= '<td>' . (isset($var1[1]) ? $var1[1] : 'Unknown') . '</td>';
            $data .= '<td>' . (isset($var1[2]) ? $var1[2] : 'Unknown') . '</td>';
            $var2 = explode('<>', $detaildata->key_value);
            $data .= '<td>' . (isset($var2[0]) ? $var2[0] : 'Unknown') . '</td>';
            $data .= '<td>' . (isset($var2[1]) ? $var2[1] : 'Unknown') . '</td>';
            $data .= '</tr>';
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Activity log Criminal Record Delete
    public function action_affiliationdeletedetail() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $time_line_id = $_POST['id'];
            $data = "";
            $detaildata = Helpers_Utilities::search_person_details($time_line_id);
            $data .= '<tr>';
            $var1 = explode('<>', $detaildata->key_name);
            $details = Helpers_Utilities::get_projects_list($var1[0]);
            $org_details = Helpers_Utilities::get_banned_organizations($var1[1]);
            $data .= '<td>' . (isset($var1[0]) && ($var1[1] != 0) ? $details->project_name : '-') . '</td>';
            $data .= '<td>' . (isset($var1[1]) && ($var1[1] != 0) ? $org_details->org_name : '-') . '</td>';
            $data .= '<td>' . (isset($var1[2]) ? $var1[2] : 'Unknown') . '</td>';
            $data .= '<td>' . (isset($var1[3]) ? $var1[3] : 'Unknown') . '</td>';
            $var2 = explode('<>', $detaildata->key_value);
            $data .= '<td>' . (isset($var2[0]) && ($var2[0] == 1) ? 'YES' : 'NO') . '</td>';
            $data .= '<td>' . (isset($var2[1]) && ($var2[0] == 1) ? $var2[1] : '-') . '</td>';
            $data .= '<td>' . (isset($var2[2]) && ($var2[0] == 1) ? $var2[2] : '-') . '</td>';
            $data .= '<td>' . (isset($var2[3]) && ($var2[0] == 1) ? $var2[3] : '-') . '</td>';
            $data .= '</tr>';
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Activity log Person Tags Update
    public function action_tagupdationdetail() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $time_line_id = $_POST['id'];
            $data = "";
            $detaildata = Helpers_Utilities::search_person_details($time_line_id);
            $old_tags = !empty($detaildata->key_name) ? $detaildata->key_name : 0;
            $old_tag_values = Helpers_Watchlist::get_tag_name_all($old_tags);

            $new_tags = !empty($detaildata->key_value) ? $detaildata->key_value : 0;
            $new_tag_values = Helpers_Watchlist::get_tag_name_all($new_tags);

            $data .= '<tr>';
            $data .= '<td>' . ($old_tag_values) . '</td>';
            $data .= '<td>' . ($new_tag_values) . '</td>';
            $data .= '</tr>';
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Activity log request Details
    public function action_userdetails() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $time_line_id = !empty($_POST['id']) ? $_POST['id'] : 0;
            $data = "";
            $details_data = Helpers_Utilities::search_person_details($time_line_id);
            $data .= '<tr>';
            $data .= '<td>' . Helpers_Utilities::get_request_type($persons->key_name) . '</td>';
            $data .= '<td>' . Helpers_Utilities::get_companies_data($persons->request_company)->company_name . '</td>';
            $data .= '<td>' . $persons->key_value . '</td>';
            $data .= '</tr>';
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    public function action_no_record_search() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 44) == 1) {
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('no_record_search_post', $post);
                /* Excel export File Included */
                include 'excel/no_record_search.inc';
                /* File Included */
                include 'user_functions/no_record_search.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxnorecordsearch() {
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
                $post = Session::instance()->get('no_record_search_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->no_record_searched($post, 'true');
                $profiles = $data->no_record_searched($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $user_id = ( isset($item['tuser']) ) ? $item['tuser'] : 'NA';
                        $user_name = ( isset($item['tuser']) ) ? Helpers_Utilities::get_user_name($item['tuser']) : 'NA';
                        $user_name .= '[';
                        $user_name .= '<a class="btn btn-small action" href="' . URL::site('user/user_profile/' . Helpers_Utilities::encrypted_key($user_id, "encrypt")) . '"> View Profile</a>';
                        $user_name .= ']';
                        $user_role_name = (isset($item['tuser']) ) ? Helpers_Utilities::get_user_role_name($item['tuser']) : 'N/A';
                        $searchname = ( isset($item['key_name']) ) ? $item['key_name'] : 'NA';
                        $searchname1 = str_replace('-', '<br>', $searchname);
                        $searchkey = ( isset($item['key_value']) ) ? $item['key_value'] : 'NA';
                        $searchkey1 = str_replace('-', '<br>', $searchkey);
                        $time = ( isset($item['activity_time']) ) ? $item['activity_time'] : 'NA';
                        $row = array(
                            //  $user_id,
                            $user_name,
                            $user_role_name,
                            $searchname1,
                            $searchkey1,
                            $time,
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

    public function action_users_favourite_agent() {
        try {
            /* Posted Data */
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('users_favourite_agent_post', $post);
            //get id from url
            $id_encrypted = $this->request->param('id');
            $id_encrypted = Helpers_Utilities::remove_injection($id_encrypted);
            $id = Helpers_Utilities::encrypted_key($id_encrypted, 'decrypt');
            if (!empty($id)) {
                Session::instance()->set('userid', $id);
                /* Include file for excel export */
                include 'excel/users_favourite_agent.inc';
                /* File Included */
                include 'user_functions/users_favourite_agent.inc';
            } else {
//            header("Location:" . url::base() . "errors?_e=wrong_parameters");
//            exit;
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxusersfavouriteagent() {
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
                $post = Session::instance()->get('users_favourite_agent_post', array());
                //get user id from url
                $id = Session::instance()->get('userid');

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->users_favourite_agent($post, 'true', $id);
                $profiles = $data->users_favourite_agent($post, 'false', $id);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $user_id = ( isset($item['userid']) ) ? $item['userid'] : 'NA';
                        $user_name = ( isset($item['userid']) ) ? Helpers_Utilities::get_user_name($item['userid']) : 'NA';

                        $user_role_name = (isset($item['userid']) ) ? Helpers_Utilities::get_user_role_name($item['userid']) : 'N/A';

                        $user_designation = ( isset($item['userid']) ) ? Helpers_Utilities::get_user_job_title($item['userid']) : 'NA';
                        $user_district = ( isset($item['userid']) ) ? Helpers_Profile::get_user_region_district($item['userid']) : 'NA';
                        //$member_name_link = '<a href="' . URL::site('persons/dashboard/' . $item['person_id']) . '" > View Detail </a>';
                        $login_user = Auth::instance()->get_user();
                        $login_id = $login_user->id;
                        $check_owner = ( isset($item['userid']) ) ? Helpers_Profile::is_favourite_user($login_id, $item['userid']) : 'NA';
                        if ($check_owner == 1) {
                            $userid_encrypted = "'" . Helpers_Utilities::encrypted_key($item['userid'], "encrypt") . "'";
                            $html = '<a class="btn btn-small action" href="' . URL::site('user/user_profile/' . Helpers_Utilities::encrypted_key($item['userid'], "encrypt")) . '"><i class="fa fa-folder-open-o"></i> View Profile</a> '
                                    . '<a class="btn btn-small action item-' . $item['userid'] . '" href="javascript:ConfirmChoice(' . $userid_encrypted . ')"><i class="fa fa-trash"></i> Delete Favourite</a>';
                        } else {
                            $html = '<a class="btn btn-small action" href="' . URL::site('user/user_profile/' . Helpers_Utilities::encrypted_key($item['userid'], "encrypt")) . '"><i class="fa fa-folder-open-o"></i> View Profile</a> ';
                        }
                        $row = array(
                            //  $user_id,
                            $user_name,
                            $user_role_name,
                            $user_designation,
                            $user_district,
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

    /* delete user Favourite user */

    public function action_deletefavouriteuser() {
        try {
            if (Auth::instance()->logged_in()) {
                $user_obj = Auth::instance()->get_user();
                $login_user_id = $user_obj->id;
                $user_id_enc = $this->request->param('id');
                $user_id_enc = Helpers_Utilities::remove_injection($user_id_enc);
                $user_id = Helpers_Utilities::encrypted_key($user_id_enc, 'decrypt');
                $per = Helpers_Profile::get_user_access_permission($login_user_id, 3);
                if ($per == 0) {
                    echo -2;
                } else {
                    $del = New Model_Userreport;
                    $result = $del->delete_favouriteuser($user_id, $login_user_id);
                    echo $result;
                }
            } else {
                return 0;
            }
        } catch (Exception $ex) {
            
        }
    }

    /* add user Favourite user */

    public function action_addfavouriteuser() {
        try {
            if (Auth::instance()->logged_in()) {
                $user_obj = Auth::instance()->get_user();
                $login_user_id = $user_obj->id;
                $user_id_encrypt = $this->request->param('id');
                $user_id_encrypt = Helpers_Utilities::remove_injection($user_id_encrypt);
                $user_id = Helpers_Utilities::encrypted_key($user_id_encrypt, 'decrypt');
                $per = Helpers_Profile::get_user_access_permission($login_user_id, 2);
                if ($per == 0) {
                    echo -2;
                } else {
                    try {
                        $user = New Model_Userreport;
                        $result = $user->add_favouriteuser($user_id, $login_user_id);
                        echo 1;
                    } catch (Exception $ex) {
                        echo json_encode(-3);
                    }
                }
            } else {
                return 0;
            }
        } catch (Exception $e) {
            
        }
    }

    public function action_user_favourite_person_list() {
        try {
            /* Posted Data */
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('user_favourite_person_list_post', $post);
            //get id from url
            $id_encoded = $this->request->param('id');
            $id_encoded = Helpers_Utilities::remove_injection($id_encoded);
            $id = (int) Helpers_Utilities::encrypted_key($id_encoded, "decrypt");
            Session::instance()->set('userid', $id);
            /* excel export file Included */
            include 'excel/user_favourite_person_list.inc';
            /* File Included */
            include 'user_functions/user_favourite_person_list.inc';
        } catch (Exception $ex) {
            $this->redirect('Userdashboard/dashboard');
        }
    }

    public function action_my_favourite_persons() {
        try {
            /* Posted Data */
            if ((Helpers_Utilities::chek_role_access($this->role_id, 8) == 1)) {
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('user_favourite_person_list_post', $post);
                //get id from url
                $id_encoded = $this->request->param('id');
                $id_encoded = Helpers_Utilities::remove_injection($id_encoded);
                //
                $id = (int) Helpers_Utilities::encrypted_key($id_encoded, "decrypt");
                Session::instance()->set('userid', $id);
                /* excel export file Included */
                include 'excel/user_favourite_person_list.inc';
                /* File Included */
                include 'user_functions/user_favourite_person_list.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
    public function action_my_persons_analysis() {
        try {
            /* Posted Data */
            if ((Helpers_Utilities::chek_role_access($this->role_id, 8) == 1)) {
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('user_person_list_post', $post);
                //get id from url
                $id_encoded = $this->request->param('id');
                $id_encoded = Helpers_Utilities::remove_injection($id_encoded);
                //
                $id = (int) Helpers_Utilities::encrypted_key($id_encoded, "decrypt");
                Session::instance()->set('userid', $id);
                /* excel export file Included */
              //  include 'excel/user_favourite_person_list.inc';
                /* File Included */
                include 'user_functions/user_person_list.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxusersfavouritepersonlist() {
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
                //get user id from session
                $id = Session::instance()->get('userid');
                $post = Session::instance()->get('panel_log', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->user_favourite_person_list($post, 'true', $id);
                $profiles = $data->user_favourite_person_list($post, 'false', $id);



                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        //here, ,t2.father_name,t2.,t2.,                     
                        $personname = ( isset($item['first_name']) ) ? $item['first_name'] : 'NA';
                        $personname .= " ";
                        $personname .= ( isset($item['last_name']) ) ? $item['last_name'] : 'NA';
                        $fathername = ( isset($item['father_name']) ) ? $item['father_name'] : 'NA';
                        $foreigner_status = (isset($item['is_foreigner'])) ? $item['is_foreigner'] : 0;
                        if ($foreigner_status == 0) {
                            $cnic = (isset($item['cnic_number'])) ? $item['cnic_number'] : 0;
                        } else {
                            $cnic = (isset($item['cnic_number_foreigner'])) ? $item['cnic_number_foreigner'] : 0;
                        }
                        $address = ( isset($item['address']) ) ? $item['address'] : 'NA';
                        $member_name_link = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Detail </a>';
                        $row = array(
                            $personname,
                            $fathername,
                            $cnic,
                            $address,
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
    public function action_ajaxuserspersonlist() {
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
                //get user id from session
                $id = Session::instance()->get('userid');
                $post = Session::instance()->get('panel_log', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;
//            }

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->user_person_list($post, 'true', $id);
                $profiles = $data->user_person_list($post, 'false', $id);



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
                        /* Concate name full name */
                        //here, ,t2.father_name,t2.,t2.,
                        $personname = ( !empty($item['first_name']) ) ? $item['first_name'] : 'NA';
                        $personname .= " ";
                        $personname .= ( !empty($item['last_name']) ) ? $item['last_name'] : '';
                        $fathername = ( !empty($item['father_name']) ) ? $item['father_name'] : 'NA';
                        $category = ( isset($item['category_id']) ) ? $item['category_id'] : '';
                        if($category==1)
                        {
                            $category='Gray';
                        }
                        elseif($category==2)
                        {
                            $category='Black';
                        }else{
                            $category='White';
                        }

                        $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : '';
                        $address = ( !empty($item['address']) ) ? $item['address'] : 'NA';
                        $action = '<input class="" value=' . $phone_number . ' name="phonenumber[]"    type="checkbox" onclick="bindPhoneNo(' . $phone_number . ')"/>';

                       // $action = '<a href="#" class="btn btn-danger btn-xs" onclick="bindPhoneNo(' . $phone_number . ')">Add Phone in Search</a>';

                        $member_name_link = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Detail </a>';
                        $row = array(
                            $personname,
                            $fathername,
                            $category,
                            $phone_number,
                            $address,
                            $action,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
echo '<pre>';
print_r($ex);
exit();
        }

    }

    /*
     *  User Access Control List (access_control_list)
     */

    public function action_access_control_form() {
        try {
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $data = new Model_Userreport;
            $rows_count = $data->access_control_save($post);
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/some_thing_went_wrong');
        }
        $this->redirect('userreports/access_control_list/?message=1');
    }

    /*
     *  User Access Control List (access_control_list)
     */

    public function action_access_control_list() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();

            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 33) == 1) {
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('access_control_list_post', $post);
                /* File Included */
                include 'user_functions/access_control_list.inc';
            } else {
               $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     *  User Ajax Call data
     */

    //ajax call for data
    public function action_ajaxaccesscontrollist() {
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
                $post = Session::instance()->get('access_control_list_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->access_control_list($post, 'true');

                $profiles = $data->access_control_list($post, 'false');



                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $user_name = ( isset($item['user_id']) ) ? $item['first_name'] . " " . $item['last_name'] : 'NA';
                        $user_name .= '<a class="btn btn-small action" href="' . URL::site('user/user_profile/' . Helpers_Utilities::encrypted_key($item['user_id'], "encrypt")) . '"> [View Profile]</a>';
                        $username = isset($item['username']) ? $item['username'] : 'NA';
                        $username_dp = '<b>' . $username . '</b> </br>';
                        $username_data = explode('::', $username);

                        $trasfered_flag = (isset($username_data[1]) && $username_data[1] == 'transferred') ? 1 : 0;
                        $designation = ( isset($item['job_title']) ) ? $item['job_title'] : 'NA';
                        $user_role_name = (isset($item['user_id']) ) ? Helpers_Utilities::get_user_role_name($item['user_id']) : 'N/A';
                        $district = ( isset($item['user_id']) ) ? Helpers_Profile::get_user_region_district($item['user_id']) : 'NA';
                        $reg_id = $item['region_id'];
                        //    $user_id_enc=  "'".Helpers_Utilities::encrypted_key($item['user_id'], 'encrypt')."'";
                        if ($reg_id == 0) {
                            $region = "Head Quaters";
                        } else {
                            $region = ( isset($item['region_id']) ) ? Helpers_Utilities::get_region($item['region_id']) : 'NA';
                        }
                        // $region = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_region_name($item['user_id']) : 'NA';
                        $right_level = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_right_level($item['user_id']) . "%" : 'NA';
                        if ($trasfered_flag == 0) {
                            $member_name_link = '<a class="chagne-st-btn  " href="javascript:acluser(' . $item['user_id'] . ')" > Manage Rights </a>';
                        } else {
                            $member_name_link = '';
                        }
                        //there
                        $row = array(
                            $user_name,
                            $username_dp . $user_role_name,
                            $designation,
                            $district,
                            $region,
                            $right_level,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
        }
    }

    /*
     *  User Ajax Call for acl 
     */

    public function action_acl_data() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $data = "";
            $data .= '<input type="hidden" name="user-id" value="' . $_POST['id'] . '">';
            $rights = Helpers_Utilities::get_user_access_name();
            $i = 1;
            foreach ($rights as $right) {
                $permission = Helpers_Utilities::get_user_permission($_POST['id']);

                $value_status = Helpers_Utilities::get_user_activity_value($_POST['id'], $right->id);
                // echo $value_status; exit;
                $data .= '<tr>'
                        . '<input type="hidden" name="user-acl[' . $i . ']" value="' . $right->id . '">';
                $data .= '<td>' . $right->label . '</td>';
                $data .= '<td>';
                $data .= '<div class="checkbox" style="margin-top:0px;margin-bottom: 0px;">';
                $data .= '<label>';
                $data .= '<input  ' . $value_status . ' name="user-acl-val[' . $i . ']" type="checkbox" data-toggle="toggle">';
                $data .= '</label>';
                $data .= '</div>';
                $data .= '</td>';
                $data .= '</tr>';
                $i++;
            }
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*
     * User Panel Log
     */

    public function action_user_manual() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 59) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('panel_log', $post);
                /* File Included */
                include 'user_functions/user_manual.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /* telco Report */
    /*
     * Telco Reports
     */

    public function action_telco_reports() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact Your Administrator';
            if (Helpers_Utilities::chek_role_access($this->role_id, 37) == 1) {
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
               // $post['total'] =;

                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('telco_reports_post', $post);
                /* Get parameter */
                //$type = $this->request->param('id');
                include 'excel/telco_reports.inc';
                /* File Included */
                include 'user_functions/telco_reports.inc';

            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }

        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     *  Telco Reports Ajax Call data
     */

    public function action_ajaxtelcoreports() {
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
                $post = Session::instance()->get('telco_reports_post', array());
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->telco_request_summary($post, 'true');
                $profiles = $data->telco_request_summary($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    $user_obj = Auth::instance()->get_user();
                    $loginuser = $user_obj->id;
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $date = ( isset($item['date']) ) ? $item['date'] : 'NA';
                        $company = ( isset($item['company_name']) ) ? $item['company_name'] : 'Unknown';
                        $company = ($company == 'NADRA') ? 'Family Tree' : $company;
                        $sendhigh = ( isset($item['send_high']) ) ? $item['send_high'] : 0;
                        $sendmedium = ( isset($item['send_medium']) ) ? $item['send_medium'] : 0;
                        $sendlow = ( isset($item['send_low']) ) ? $item['send_low'] : 0;
                        $sendtotal = ( isset($item['total_send']) ) ? $item['total_send'] : 0;
                        $rectotal = ( isset($item['total_received']) ) ? $item['total_received'] : 0;
                        $high = '<b >' . $sendhigh . ' </b>';
                        $medium = '<b >' . $sendmedium . ' </b>';
                        $low = '<b >' . $sendlow . ' </b>';
                        $total = '<span class="badge badge-pill badge-primary">Send: ' . $sendtotal . ' </span>' . '<br><span class="badge badge-pill badge-success">Received: ' . $rectotal . '</span>';
                        $row = array(
                            $date,
                            $company,
                            $high,
                            $medium,
                            $low,
                            $total
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
           /* echo'<pre>';
            print_r($ex);
            exit;*/
        }
    }
    public function action_ajaxtelcoreportstotal() {
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
                $post = Session::instance()->get('telco_reports_post', array());
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;
//
//            }

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->telco_request_total_summary($post, 'true');
                $profiles = $data->telco_request_total_summary($post, 'false');

/*
                $rows_count1 = $data->telco_request_summary1($post, 'true');
                $profiles1 = $data->telco_request_summary1($post, 'false');*/

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    $user_obj = Auth::instance()->get_user();
                    $loginuser = $user_obj->id;
                    foreach ($profiles as $item) {
                        //muzammal
                        /* Concate name full name */
//                        $date = ( isset($item['date']) ) ? $item['date']: 'NA';
                        if (!empty($post['startdate']) && !empty($post['enddate'])) {
                            $start_date =$post['startdate'];
                            $end_date =$post['enddate'];
                            $date = '<span ><b>Start Date: </b>'.$start_date.'</span>'.' '. '<span class="text_success"></br><b>End Date:</b> '.$end_date.'</span>';
                        } else {
                            $date ='<span><b>Till Date: </b></span>'. date("Y-m-d");
                        }
                      /*  $date = ( isset($item['date']) ) ? $item['date'] : 'NA';*/
                        $company = ( isset($item['company_name']) ) ? $item['company_name'] : 'Unknown';
                        $company = ($company == 'NADRA') ? 'Family Tree' : $company;
                       /* $sendhigh = ( isset($item['send_high']) ) ? $item['send_high'] : 0;
                        $sendmedium = ( isset($item['send_medium']) ) ? $item['send_medium'] : 0;
                        $sendlow = ( isset($item['send_low']) ) ? $item['send_low'] : 0;*/
                        $sendtotal = ( isset($item['total_send']) ) ? $item['total_send'] : 0;
                        $rectotal = ( isset($item['total_received']) ) ? $item['total_received'] : 0;
                        /*$high = '<b >' . $sendhigh . ' </b>';
                        $medium = '<b >' . $sendmedium . ' </b>';
                        $low = '<b >' . $sendlow . ' </b>';*/
                        $total = '<span class="badge badge-pill badge-primary">Send: ' . $sendtotal . ' </span>' . '<br><span class="badge badge-pill badge-success">Received: ' . $rectotal . '</span>';
                        $row = array(
                            $date,
                            $company,
                         /*   $high,
                            $medium,
                            $low,*/
                            $total
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

    /* Admin Reports  */

    public function action_admin_reports() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact Your Administrator';
            if (Helpers_Utilities::chek_role_access($this->role_id, 36) == 1) {
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('admin_reports_post', $post);
                /* Get parameter */
                //$type = $this->request->param('id');
                //include 'excel/telco_reports.inc';
                /* File Included */
                include 'user_functions/admin_reports.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     *  Telco Reports Ajax Call data
     */

    public function action_ajaxadminreports() {
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
                $post = Session::instance()->get('telco_reports_post', array());
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->telco_request_summary($post, 'true');
                $profiles = $data->telco_request_summary($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    $user_obj = Auth::instance()->get_user();
                    $loginuser = $user_obj->id;
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $date = ( isset($item['date']) ) ? $item['date'] : 'NA';
                        $company = ( isset($item['company_name']) ) ? $item['company_name'] : 'Unknown';
                        $sendhigh = ( isset($item['send_high']) ) ? $item['send_high'] : 0;
                        $sendmedium = ( isset($item['send_medium']) ) ? $item['send_medium'] : 0;
                        $sendlow = ( isset($item['send_low']) ) ? $item['send_low'] : 0;
                        $sendtotal = ( isset($item['total_send']) ) ? $item['total_send'] : 0;
                        $rectotal = ( isset($item['total_received']) ) ? $item['total_received'] : 0;
                        $high = '<b >' . $sendhigh . ' </b>';
                        $medium = '<b >' . $sendmedium . ' </b>';
                        $low = '<b >' . $sendlow . ' </b>';
                        $total = '<span class="badge badge-pill badge-primary">Send: ' . $sendtotal . ' </span>' . '<br><span class="badge badge-pill badge-success">Received: ' . $rectotal . '</span>';
                        $row = array(
                            $date,
                            $company,
                            $high,
                            $medium,
                            $low,
                            $total
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

    /* Person Last Location  */

    public function action_nadra_profile_data() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            if (!empty($_POST)) {
                $date_current = date('Y-m-d', strtotime($_POST['yearapi'] . '-' . $_POST['monthapi'] . '-01'));
            } else {
                $date_current = date('Y-m-01'); //$date;   
            }

            $data = array();
            $nadra_data = Helpers_Utilities::get_nadra_profile_stat($date_current);
            foreach ($nadra_data as $row) {
                $data['region'][] = $row['name'];
                $data['requests'][] = $row['total_request'];
            }
            if (!empty($nadra_data)) {
                echo json_encode($data);
                exit;
            } else {
                echo -1;
            }
        } catch (Exception $e) {
            
        }
    }

    /* Request To search subscriber in local databases */

    public function action_msisdn_data_search() {
        try {
            //parameters
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $search_type = !empty($_POST['search_type']) ? $_POST['search_type'] : 0;
            $search_value = !empty($_POST['search_value']) ? trim($_POST['search_value']) : 0;
            //request functions
            if ($search_type == 'cnic') {
                $request_function = 'requestcnicsims(' . $search_value . ',-1)';
                $request_function_name = 'Request From Company';
            } elseif ($search_type == 'msisdn') {
                $request_function = 'requestsub(' . $search_value . ')';
                $request_function_name = 'Request From Company';
            } elseif ($search_type == 'imsi') {
                $request_function = '#';
                $request_function_name = '';
            } else {
                $request_function = '#';
                $request_function_name = '';
            }

            //api call
            if (!empty($search_type) && !empty($search_value)) {

                //getting login user credentials
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $uid = $login_user->id;
                if (empty($uid)) {
                    $uid = 9991;
                }

                //user activity type
                Helpers_Profile::user_activity_log($uid, 81, $search_type, $search_value);
                $result = Helpers_Subscriber::search($search_type, $search_value);


               // include 'user_functions/subscriber_api_key.inc';
                $post = $result;

            if (!empty($post['data'])) {
                // Access the CNIC directly
                $cnic = $post['data']['cnic'] ?? null;

                if (!empty($cnic)) {
                    $data = new Model_Userrequest();
                    $results = $data->subscriber_external_search_results($post, $uid, 'true');
                    echo $results;
                    exit;
                }
            }

                echo '<p style="color: red;font-weight: bold">NO RECORD FOUND</p><a class="btn btn-primary pull-right" style="margin-top:-37px;" href="#" onclick="' . $request_function . '">' . $request_function_name . '</a>';

                echo '<hr class="style14 ">';
            } else {
                echo '<p style="color: red;font-weight: bold">PARAMETER ERROR</p><a class="btn btn-primary pull-right" style="margin-top:-37px;" href="#" onclick="' . $request_function . '">' . $request_function_name . '</a>';
                echo '<hr class="style14 ">';
            }
        } catch (Exception $ex) {
            echo '<p style="color: red;font-weight: bold">NO RECORD FOUND</p><a class="btn btn-primary pull-right" style="margin-top:-37px;" href="#" onclick="' . $request_function . '">' . $request_function_name . '</a>';

            echo '<hr class="style14 ">';
            //echo json_encode(2);
        }
    }

    /* Request To search subscriber in local databases */

    public function action_search_foreinger_detail() {
        try {
            //parameters
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $search_type = !empty($_POST['search_type']) ? $_POST['search_type'] : 0;
            $search_value = !empty($_POST['search_value']) ? trim($_POST['search_value']) : 0;

            //api call
            if (!empty($search_type) && !empty($search_value)) {

                //getting login user credentials
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $uid = $login_user->id;
                if (empty($uid)) {
                    $uid = 9991;
                }

                //user activity type
                Helpers_Profile::user_activity_log($uid, 82, $search_type, $search_value);
                $test_array = Helpers_Subscriber::searchForeignerAccount($search_type, $search_value);
                //include 'user_functions/subscriber_api_key.inc';
                $post = $test_array;

                if (!empty($post['data'])) {
                    // Access the CNIC directly
                    $cnic = $post['data']['master_acc_number'] ?? null;

                    if (!empty($cnic)) {
                        $data = new Model_Userrequest();
                        $results = $data->foreigner_external_search_results($post, $uid, 'true');
                        echo $results;
                        exit;
                    }
                }

                echo '<p style="color: red;font-weight: bold">NO RECORD FOUND</p>';

                echo '<hr class="style14 ">';
            } else {
                echo '<p style="color: red;font-weight: bold">PARAMETER ERROR</p>';
                echo '<hr class="style14 ">';
            }
        } catch (Exception $ex) {
            
        }
    }

    //create foreginer person
    public function action_create_foreigner_person() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
        } catch (Exception $e) {
            echo 'Access denied, Contact your technical support team';
            exit;
        }
        if ($permission == 1 || $permission == 2 || $permission == 3 || $permission == 4) {
            //get person id by updating table
            $content = new Model_Generic();
            $pid = $content->update_cnic_number($_POST);
            //print_r($pid);die();
            try {                                
                $_POST = Helpers_Utilities::remove_injection($_POST);
                $array_person['cnic_number'] = $_POST['cnic_number'];
                $array_person['first_name'] = $_POST['person_name'];
                $array_person['is_foreigner'] = 1;
                $content = new Model_Generic();

                if(!empty($array_person['cnic_number']))
                    $pid = $content->update_cnic_number($array_person);
                else 
                    $access_message = 'Error, CNIC Missing, Contact your technical support team';
            } catch (Exception $e) {
                echo 'Access denied, Contact your technical support team';
             exit;   
            }
            $this->redirect('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($pid, "encrypt"));
        } else {
            $this->redirect('user/access_denied');
        }
    }

    /*     * Region wise break up report */

    public function action_request_breakup_report() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 52) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($login_user->id);
                $access_message = 'Access denied, Contact your technical support team';
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if ($permission == 3) {
                    try {
                        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
                        $region_id = $login_user_profile->region_id;
                        $region_id_encrypted = !empty($region_id) ? Helpers_Utilities::encrypted_key($region_id, "encrypt") : '';
                    } catch (Exception $ex) {
                        $this->template->content = View::factory('templates/user/exception_error_page')
                                ->bind('exception', $ex);
                    }
                    $this->redirect('userreports/request_breakup_district/?id=' . $region_id_encrypted);
                } else {
                    Session::instance()->set('request_log_region_post', $post);
                    include 'excel/request_breakup_region.inc';
                    $this->template->content = View::factory('templates/user/adminreports/request_breakup_region')
                            ->set('search_post', $post);
                }
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     *  Region wise break up report Ajax Call data
     */

    public function action_ajaxrequestbreakupreport() {
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
                $post = Session::instance()->get('request_log_region_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['startdate']) && !empty($post['enddate'])) {
                    $date1 = strtotime($post['startdate']);
                    $date2 = strtotime($post['enddate']);
                }
                $data = new Model_Userreport();
                $rows_count = $data->request_breakup_region($post, 'true');
                $profiles = $data->request_breakup_region($post, 'false');


                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    $user_obj = Auth::instance()->get_user();
                    $loginuser = $user_obj->id;
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $region = ( isset($item['region_id']) ) ? $item['region_id'] : 0;
                        $region_id_encrypted = !empty($item['region_id']) ? Helpers_Utilities::encrypted_key($item['region_id'], "encrypt") : '';
                        $region_name = Helpers_Utilities::get_region($region);
                        $total_request = ( isset($item['total_request']) ) ? $item['total_request'] : 0;
//                    
//                    $request_date_new = strtotime(date('Y-m-d', strtotime($request_date)));                    
//                    $request_count = ( isset($item['request_count']) ) ? $item['request_count'] : 0;
//                    $response_count = ( isset($item['pending']) ) ? $item['pending'] : 0;
                        //  $member_name_link =  '<a class="chagne-st-btn" href="" > View Detail </a>';
                        if (isset($date1) && isset($date2)) {
                            $member_name_link = '<a class="btn btn-small action" href="' . URL::site('userreports/request_breakup_district/?id=' . $region_id_encrypted) . '&startdate=' . $date1 . '&enddate=' . $date2 . '"><i class="fa fa-folder-open-o"></i> View Detail</a>';
                        } else {
                            $member_name_link = '<a class="btn btn-small action" href="' . URL::site('userreports/request_breakup_district/?id=' . $region_id_encrypted) . '"><i class="fa fa-folder-open-o"></i> View Detail</a>';
                        }
                        $row = array(
                            $region_name,
                            $total_request,
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

    /*     * District wise break up report */
    public function action_request_breakup_district() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if ($permission == 1 || $permission == 2 || $permission == 3) {
                $post = $this->request->post();
                if (!empty($_GET['startdate']) && !empty($_GET['enddate'])) {
                    $_GET['startdate'] = date('m/d/Y', ($_GET['startdate']));
                    $_GET['enddate'] = date('m/d/Y', ($_GET['enddate']));
                }
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                Session::instance()->set('request_breakup_district_post', $post);
                $this->template->content = View::factory('templates/user/adminreports/request_breakup_district')
                        ->set('search_post', $post);
            } else {
                $this->redirect('user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     *  Region wise break up report Ajax Call data
     */

    public function action_ajaxrequestbreakupreportdist() {
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
                $post = Session::instance()->get('request_breakup_district_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['startdate']) && !empty($post['enddate'])) {

                    $date1 = strtotime($post['startdate']);
                    $date2 = strtotime($post['enddate']);
                }
                try {
                    $post['region_id'] = !empty($post['id']) ? (int) Helpers_Utilities::encrypted_key($post['id'], "decrypt") : 0;
                } catch (Exception $e) {
                    $post['region_id'] = 0;
                }

                $data = new Model_Userreport();
                $rows_count = $data->request_breakup_district($post, 'true');
                $profiles = $data->request_breakup_district($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    $user_obj = Auth::instance()->get_user();
                    $loginuser = $user_obj->id;
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $region_id = ( isset($item['region_id']) ) ? $item['region_id'] : 0;
                        $region_name = Helpers_Utilities::get_region($region_id);
                        $posted = ( isset($item['posted']) ) ? $item['posted'] : 0;
                        $posted_encrypted = !empty($item['posted']) ? Helpers_Utilities::encrypted_key($item['posted'], "encrypt") : '';
                        $office = Helpers_Profile::get_user_posting($posted);
                        $total_request = ( isset($item['total_request']) ) ? $item['total_request'] : 0;
                        if (isset($date1) && isset($date2)) {
                            $member_name_link = '<a class="btn btn-small action" href="' . URL::site('userreports/request_type_breakup_district/?id=' . $posted_encrypted) . '&startdate=' . $date1 . '&enddate=' . $date2 . '"><i class="fa fa-folder-open-o"></i> View Detail</a>';
                        } else {
                            $member_name_link = '<a class="btn btn-small action" href="' . URL::site('userreports/request_type_breakup_district/?id=' . $posted_encrypted) . '"><i class="fa fa-folder-open-o"></i> View Detail</a>';
                        }
                        $row = array(
                            $region_name,
                            $office,
                            $total_request,
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

    /*     * District wise break up report with request type */

    public function action_request_type_breakup_district() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if ($permission == 1 || $permission == 2 || $permission == 3) {
                $post = $this->request->post();
                if (!empty($_GET['startdate']) && !empty($_GET['enddate'])) {
                    $_GET['startdate'] = date('m/d/Y', ($_GET['startdate']));
                    $_GET['enddate'] = date('m/d/Y', ($_GET['enddate']));
                }
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                Session::instance()->set('request_type_breakup_district_post', $post);
                include 'excel/request_breakup_region.inc';
                $this->template->content = View::factory('templates/user/adminreports/request_type_breakup_district')
                        ->set('search_post', $post);
            } else {
                $this->redirect('user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     *  Region wise break up report Ajax Call data
     */

    public function action_ajaxrequesttypebreakupreportdist() {
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
                $post = Session::instance()->get('request_type_breakup_district_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                try {
                    $post['posted'] = !empty($post['id']) ? Helpers_Utilities::encrypted_key($post['id'], "decrypt") : 0;
                } catch (Exception $e) {
                    $post['posted'] = 0;
                }
                $data = new Model_Userreport();
                $rows_count = $data->request_type_breakup_district($post, 'true');
                $profiles = $data->request_type_breakup_district($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    $user_obj = Auth::instance()->get_user();
                    $loginuser = $user_obj->id;
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $region_id = ( isset($item['region_id']) ) ? $item['region_id'] : 0;
                        $region_name = Helpers_Utilities::get_region($region_id);
                        $posted = ( isset($item['posted']) ) ? $item['posted'] : 0;
                        $posted_encrypted = !empty($item['posted']) ? Helpers_Utilities::encrypted_key($item['posted'], "encrypt") : '';
                        $office = Helpers_Profile::get_user_posting($posted);
                        $total_request = ( isset($item['total_request']) ) ? $item['total_request'] : 0;
                        $request_type = !empty($item['user_request_type_id']) ? Helpers_Utilities::emailtemplatetype($item['user_request_type_id']) : '';
                        $request_type = !empty($request_type['email_type_name']) ? $request_type['email_type_name'] : 'NA';
                        $row = array(
                            $region_name,
                            $office,
                            $request_type,
                            $total_request
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

    //project request details
    public function action_project_request_type() {
        try {
            /* Posted Data */
            $post = $this->request->post();
            if (isset($_GET)) {
                $post = array_merge($post, $_GET);
            }
            $post = Helpers_Utilities::remove_injection($post);

            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('project_request_type_post', $post);
            /* Excel Export File Included */
            include 'excel/project_request_type.inc';
            //Call to view
            $this->template->content = View::factory('templates/user/project_request_type')
                    ->set('search_post', $post);
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxprojectrequesttype() {
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
                $post = Session::instance()->get('project_request_type_post', array());
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
//                echo '<pre>';
//                print_r($post);
//                exit;

                if (!empty($post['project_id'])) {
                    $project_id = (int) Helpers_Utilities::encrypted_key($post['project_id'], 'decrypt');
                } else {
                    $project_id = 0;
                }

                $data = new Model_Userreport;
                $rows_count = $data->project_request_type($post, 'true', $project_id);
                $profiles = $data->project_request_type($post, 'false', $project_id);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 'NA';
                        $project_id = ( isset($item['project_id']) ) ? $item['project_id'] : 0;
                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $user_designation = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_job_title($item['user_id']) : 'NA';
                        $user_region = (!empty($item['region_id']) ) ? Helpers_Utilities::get_region($item['region_id']) : 'Head Quarters';
                        $user_posting = ( isset($item['posted']) ) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';
                        $request_type = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type_name($item['user_request_type_id']) : 'NA';
                        $count = ( isset($item['count']) ) ? $item['count'] : 0;
                        $user_id_en = Helpers_Utilities::encrypted_key($item['user_id'], 'encrypt');
                        $request_type_en = Helpers_Utilities::encrypted_key($item['user_request_type_id'], 'encrypt');
                        $project_id_en = Helpers_Utilities::encrypted_key($project_id, 'encrypt');

                        $member_name_link = '<a href="' . URL::site('userreports/project_request_send_detail/?userid=' . $user_id_en . '&request_type=' . $request_type_en . '&project_id=' . $project_id_en.'&sdate='.$post['sdate'].'&edate='.$post['edate']) . '" > View Detail </a>';
                        $row = array(
                            //$user_id,
                            $user_name,
                            $user_designation,
                            $user_region,
                            $user_posting,
                            $request_type,
                            $count,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }
            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
        }
    }

    public function action_project_request_send_detail() {
        try {
            /* Posted Data */
            $post = $this->request->post();
            if (isset($_GET)) {
                $post = array_merge($post, $_GET);
            }

            $post = Helpers_Utilities::remove_injection($post);
//            echo '<pre>';
//            print_r($post);
//            exit;
            $post['userid'] = isset($post['userid']) ? $post['userid'] : 0;
            $post['request_type'] = isset($post['request_type']) ? $post['request_type'] : 0;
            $post['project_id'] = isset($post['project_id']) ? $post['project_id'] : 0;

            $userid = (int) Helpers_Utilities::encrypted_key($post['userid'], 'decrypt');
            $request_type = (int) Helpers_Utilities::encrypted_key($post['request_type'], 'decrypt');
            $project_id = (int) Helpers_Utilities::encrypted_key($post['project_id'], 'decrypt');
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('project_request_send_detail_post', $post);
            /* Excel Export File Included */
            include 'excel/project_request_send_detail.inc';
            /* Project Request Send Detail */
            $this->template->content = View::factory('templates/user/project_request_send_detail')
                    ->set('search_post', $post);
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxprojectrequestsenddetail() {
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
                $post = Session::instance()->get('project_request_send_detail_post', array());
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
//                echo '<pre>';
//                print_r($post);
//                exit;
                if (!empty($post['userid']) && !empty($post['request_type'])) {
                    $userid = Helpers_Utilities::encrypted_key($post['userid'], 'decrypt');
                    $request_type = Helpers_Utilities::encrypted_key($post['request_type'], 'decrypt');
                    $project_id = Helpers_Utilities::encrypted_key($post['project_id'], 'decrypt');
                } else {
                    $userid = NULL;
                    $request_type = NULL;
                    $project_id = NULL;
                }
                $data = new Model_Userreport;
                $rows_count = $data->project_request_send_detail($post, 'true', $userid, $request_type, $project_id);
                $profiles = $data->project_request_send_detail($post, 'false', $userid, $request_type, $project_id);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';

                        // $request_type = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type($item['user_request_type_id']) : 'NA';

                        $requested_value = ( isset($item['requested_value']) ) ? $item['requested_value'] : 'NA';

                        $requested_value .= ( isset($item['request_id']) ) ? '<br/><b>' . $item['request_id'] . '<b>' : '';
                        $reason = ( isset($item['reason']) ) ? $item['reason'] : 'NA';
                        $concerned_person_id = ( isset($item['concerned_person_id']) ) ? $item['concerned_person_id'] : 'NA';
                        if ($concerned_person_id > 0) {
                            $perons_name = ( isset($item['concerned_person_id']) ) ? Helpers_Person::get_person_name($item['concerned_person_id']) : 'NA';
                            $perons_name .= '[';
                            $perons_name .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['concerned_person_id'], "encrypt")) . '" > View Profile </a>';
                            $perons_name .= ']';
                        } else {
                            $perons_name = " ";
                        }
                        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : 'NA';
                        $view_request_status = '<a href="' . URL::site('userrequest/request_status_detail/' . Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt')) . '" > View Detail </a>';

                        $row = array(
                            $user_name,
                            // $request_type,
                            $requested_value,
                            $reason,
                            $perons_name,
                            $created_at,
                            $view_request_status,
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }
            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
        }
    }

}

// End Users Class
