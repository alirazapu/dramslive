<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Personprofile extends Controller_Working {
    /* Person Dashboard (person_profile) */

    public function action_person_profile() {
        try {
            $post = $this->request->post();
            $post_data = array_merge($post, $_GET);
            $post_data = Helpers_Utilities::remove_injection($post_data);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('person_mobiles_post', $post_data);
            Session::instance()->set('person_data_post', $post_data);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                //$pid = Session::instance()->get('personid');
                $data = Helpers_Person::get_person_perofile($pid);
                include 'persons_functions/person_profile.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /* Person Dashboard / Person Profile / Person Verisys */

    public function action_person_verisys() {
        try {
            $post = $this->request->post();
            $post_data = array_merge($post, $_GET);
            $post_data = Helpers_Utilities::remove_injection($post_data);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                $this->template->content = View::factory('templates/persons/Person_profile/person_verisys')
                        ->bind('person_id', $pid);
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    public function action_person_ftree_update() {
        try {
            $post = $this->request->post();
            $post_data = array_merge($post, $_GET);
            $post_data = Helpers_Utilities::remove_injection($post_data);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                $this->template->content = View::factory('templates/persons/Person_profile/person_f_tree_update')
                        ->bind('person_id', $pid);
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /* Update baisc info  */

    public function action_update_basic_info() {
        try {
            $this->auto_render = FALSE;
            // print_r($_POST); exit;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $pid = (int) Helpers_Utilities::encrypted_key($_POST['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $update = Model_Personprofile::update_basic_info($_POST, $user_id, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

//get district list
    public function action_get_district() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $helper_district = !empty($_POST['region']) ? Helpers_Utilities::get_region_district($_POST['region']) : "";
            $html = '';
            if (!empty($helper_district)) {
                foreach ($helper_district as $list) {
                    if (!empty($_POST['district_id']) && ($_POST['district_id'] == $list['district_id'])) {
                        $selected = "selected";
                    } else {
                        $selected = '';
                    }
                    $html .= '<option value="' . $list['district_id'] . '" ' . $selected . '>' . $list['name'] . '</option>';
                }
            } else {
                $html .= '<option value="">Please Select Region First</option>';
            }
            echo $html;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

//get district list
    public function action_get_police_station() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $helper_ps = !empty($_POST['district_id']) ? Helpers_Utilities::get_district_police_station($_POST['district_id']) : "";
            $html = '';
            if (!empty($helper_ps)) {
                foreach ($helper_ps as $list) {
                    if (!empty($_POST['police_station_id']) && ($_POST['police_station_id'] == $list->ps_id)) {
                        $selected = "selected";
                    } else {
                        $selected = '';
                    }
                    $html .= '<option value="' . $list->ps_id . '" ' . $selected . '>' . $list->ps_name . '</option>';
                }
            } else {
                $html .= '<option value="">Please Select District First</option>';
            }
            echo $html;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

//get district list
    public function action_get_sect() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $helper_religion = !empty($_POST['religion']) ? Helpers_Utilities::get_sect(0, $_POST['religion']) : "";
            $html = '';
            if (!empty($helper_religion)) {
                foreach ($helper_religion as $list) {
                    if (!empty($_POST['sect']) && ($_POST['sect'] == $list->id)) {
                        $selected = "selected";
                    } else {
                        $selected = '';
                    }
                    $html .= '<option value="' . $list->id . '" ' . $selected . '>' . $list->sect . '</option>';
                }
            } else {
                $html .= '<option value="">Please Select Religion First</option>';
            }

            echo $html;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Update detail info  */

    public function action_update_detail_info() {
        try {
            $this->auto_render = FALSE;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $pid = (int) Helpers_Utilities::encrypted_key($_POST['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $update = Model_Personprofile::update_detail_info($_POST, $user_id, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //person mobile numbers list
    public function action_ajaxmobilenumbers() {
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
                $post = Session::instance()->get('person_mobiles_post', array());

                $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Personprofile;
                $rows_count = $data->get_mobiles($post, 'true', $pid);
                $profiles = $data->get_mobiles($post, 'false', $pid);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $ownerid = ( isset($item['sim_owner']) ) ? $item['sim_owner'] : '';
                        $userid = ( isset($item['person_id']) ) ? $item['person_id'] : '';
                        // owner info
                        if ($ownerid == $pid && $ownerid != '') {
                            $ownername = "Self";
                        } else {
                            $ownername = (!empty($ownerid) ) ? Helpers_Person::get_person_name($ownerid) : 'NA';
                            if ($ownername != "NA") {
                                $ownername = '<a  href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($ownerid, "encrypt")) . '" > ' . $ownername . ' </a>';
                            }
                        }
                        //user info
                        if ($pid == $userid && $userid != '') {
                            $username = "Self";
                        } else {
                            $username = (!empty($userid) ) ? Helpers_Person::get_person_name($userid) : 'NA';
                            if ($username != "NA") {
                                $username = '<a  href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($userid, "encrypt")) . '" ) > ' . $username . ' </a>';
                            }
                        }
                        $number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 'NA';
                        // $last = ( isset($item['sim_last_used_at']) ) ? $item['sim_last_used_at'] : 'NA';
                        $company1 = ( isset($item['mnc']) ) ? Helpers_Utilities::get_companies_data($item['mnc']) : 'NA';
                        $company = isset($company1->company_name) ? $company1->company_name : "Unknown";
                        $status = ( isset($item['status']) ) ? $item['status'] : 'NA';
                        $type = ( isset($item['connection_type']) ) ? $item['connection_type'] : 'NA';
                        $act = ( isset($item['sim_activated_at']) ) ? $item['sim_activated_at'] : 'NA';
                        $contact_type = (!empty($item['contact_type']) ) ? $item['contact_type'] : '';
                        // print_r($contact_type); exit;
                        $action = '<a href="#" onclick="editmsisdn(' . $number . ',' . $contact_type . ')"><span class="fa fa-edit"> Edit</span></a>';
                        $contact_type = !empty($contact_type) ? Helpers_Utilities::get_contact_type($contact_type) : "NA";
                        if ($status == 1) {
                            $status = "Active";
                        } elseif ($status == 0) {
                            $status = "InActive";
                        }
                        if ($type == 1) {
                            $type = "Prepaid";
                        } elseif ($type == 0) {
                            $type = "Postpaid";
                        }

                        $row = array(
                            $number,
                            $contact_type,
                            $ownername,
                            $username,
                            $company,
                            $status,
                            $type,
                            $act,
                                // $action
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

    //person mobile numbers list
    public function action_ajaxlinkedprojects() {
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
                $post = Session::instance()->get('person_linked_projects', array());

                //get person id from session
                $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                // $pid = Session::instance()->get('personid');
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }

                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_Personprofile;
                $rows_count = $data->get_linked_projects($post, 'true', $pid);
                $profiles = $data->get_linked_projects($post, 'false', $pid);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $p_user_id = (!empty($item['user_id']) ) ? $item['user_id'] : -1;
                        if ($p_user_id != -1) {
                            $postingplace = Helpers_Profile::get_user_region_district($p_user_id);
                        } else {
                            $postingplace = 'NA';
                        }
                        $emailtypename = (!empty($item['email_type_name']) ) ? $item['email_type_name'] : 'NA';
                        $projects_ids = (!empty($item['project_id']) ) ? $item['project_id'] : 0;
                        $requested_value = (!empty($item['requested_value']) ) ? $item['requested_value'] : 0;

                        $project_name = !empty($projects_ids) ? Helpers_Utilities::get_project_names($projects_ids) : "Unknown";
                        $project_region = !empty($projects_ids) ? Helpers_Utilities::get_project_region_name($projects_ids) : "Unknown";

                        $project_name .= ' [';
                        $project_name .= $project_region;
                        $project_name .= '] ';

                        $requesttime = (!empty($item['request_time']) ) ? $item['request_time'] : 'NA';


                        $row = array(
                            $postingplace,
                            $emailtypename,
                            $requested_value,
                            $project_name,
                            $requesttime
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

    //person category change history 
    public function action_ajaxcategoryhistory() {
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
                $post = Session::instance()->get('person_linked_projects', array());

                //get person id from session
                $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                // $pid = Session::instance()->get('personid');
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Personprofile;
                $rows_count = $data->get_category_history($post, 'true', $pid);
                $profiles = $data->get_category_history($post, 'false', $pid);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $category_id_old = (!empty($item['old_category_id']) ) ? $item['old_category_id'] : 0;
                        $category_name_old = '';
                        switch ($category_id_old) {
                            case 0:
                                $category_name_old = 'White';
                                break;
                            case 1:
                                $category_name_old = 'Grey';
                                break;
                            case 2:
                                $category_name_old = 'Black';
                                break;
                        }
                        $category_id_new = (!empty($item['new_category_id']) ) ? $item['new_category_id'] : 0;
                        $category_name_new = '';
                        switch ($category_id_new) {
                            case 0:
                                $category_name_new = 'White';
                                break;
                            case 1:
                                $category_name_new = 'Grey';
                                break;
                            case 2:
                                $category_name_new = 'Black';
                                break;
                        }
                        $user_id = (!empty($item['user_id']) ) ? $item['user_id'] : 0;
                        $user_name = 'Hidden';
                        $change_date = 'Hidden';
                        $change_reason = 'Hidden';

                        $login_user = Auth::instance()->get_user();
                        $permission = Helpers_Utilities::get_user_permission($login_user->id);
                        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
                        $posting = $login_user_profile->posted;
                        $result = explode('-', $posting);
                        if (($permission == 1) || ($login_user->id == $user_id) || ($permission == 3 && $result[0] == 'h')) {
                            $user_name = Helpers_Utilities::get_user_name($user_id);
                            $change_date = (!empty($item['added_on']) ) ? $item['added_on'] : '---';
                            $change_reason = (!empty($item['reason']) ) ? $item['reason'] : '---';
                        }


                        $row = array(
                            $category_name_old,
                            $category_name_new,
                            $user_name,
                            $change_date,
                            $change_reason,
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

    /* Update mobile info  */

    public function action_update_mobiles() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");

            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $update = Model_Personprofile::update_mobiles_info($_POST, $user_id, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //person relations list
    public function action_ajaxpersonrelations() {
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
                $post = Session::instance()->get('person_data_post', array());

                //get person id from session
                // $pid = Session::instance()->get('personid');
                $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Personprofile;
                $rows_count = $data->get_person_relations($post, 'true', $pid);
                $profiles = $data->get_person_relations($post, 'false', $pid);
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $cnicnumber = ( isset($item['cnic_number']) ) ? $item['cnic_number'] : 0;
                        $under_custodian = ( isset($item['under_custodian']) ) ? $item['under_custodian'] : 0;
                        $cnicnumberforigner = ( isset($item['cnic_number_foreigner']) ) ? $item['cnic_number_foreigner'] : 0;
                        $is_foreigner = ( isset($item['is_foreigner']) ) ? $item['is_foreigner'] : 0;
                        if ($item['rel_f_id'] == $pid) {
                            $relfromlink = "Self";
                        } else {
                            $relfrom = ( isset($item['rel_f_id']) ) ? Helpers_Person::get_person_name($item['rel_f_id']) : 'NA';

                            if ($relfrom == "" OR $relfrom == " " OR $relfrom == "Null") {
                                $relfrom = "Profile";
                            }
                            $relfromlink = $cnicnumber . '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['rel_f_id'], "encrypt")) . '" > (' . $relfrom . ') </a>';
                        }
                        if ($item['rel_t_id'] == $pid) {
                            $reltolink = "Self";
                        } else {
                            $relto = ( isset($item['rel_t_id']) ) ? Helpers_Person::get_person_name($item['rel_t_id']) : 'NA';
                            $relto = trim($relto);
                            if (empty($relto) || $relto == "NA") {
                                $relto = "Profile";
                            }
                            // print_r($relto); exit;
                            $reltolink = $cnicnumber . '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['rel_t_id'], "encrypt")) . '" > (' . $relto . ') </a>';
                        }
                        $relationtypeid = ( isset($item['rel_id']) ) ? $item['rel_id'] : 0;
                        $relationtype = (!empty($item['rel_id']) ) ? Helpers_Person::get_person_relation_type($item['rel_id']) : 'No Relation';

                        if (empty($cnicnumber) && !empty($cnicnumberforigner1)) {
                            $cnicnumber = $cnicnumberforigner1;
                        }
                        $cnicnumber1 = "'" . trim($cnicnumber) . "'";
                        $action = '<a href="#" onclick="editrelation(' . $cnicnumber1 . ',' . $item['rel_id'] . ',' . $is_foreigner . ',' . $under_custodian . ')"><span class="fa fa-edit"> Edit</span></a>';
                        if (empty($under_custodian)) {
                            $under_custodian = 'No';
                        } else {
                            $under_custodian = 'Yes';
                        }
                        $row = array(
                            $relfromlink,
                            $relationtype,
                            $reltolink,
                            $under_custodian,
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

    /* Update relations info  */

    public function action_update_relations() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //print_r($pid); exit;
            //$pid = Session::instance()->get('personid');
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $update = Model_Personprofile::update_relations($_POST, $user_id, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //person Identities List
    public function action_ajaxpersonidentity() {
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
                $post = Session::instance()->get('person_data_post', array());
                //get person id from url
                //print_r($_GET['id']); exit;
                $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");

                //get person id from session
                //sajid            
                //  $pid = Session::instance()->get('personid');
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Personprofile;
                $rows_count = $data->get_person_identity($post, 'true', $pid);
                $profiles = $data->get_person_identity($post, 'false', $pid);
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $identityid = ( isset($item['identity_id']) ) ? $item['identity_id'] : '';
                        $recordid = ( isset($item['id']) ) ? $item['id'] : 0;
                        $identityno = ( isset($item['identity_no']) ) ? $item['identity_no'] : 0;
                        $identity_no = "'" . trim($item['identity_no']) . "'";
                        if ($identityid == 4 OR $identityid == 5) {
                            $action = '';
                        } else {
                            $action = '<a href="#" onclick="editidentity(' . $identityid . ',' . $identity_no . ',' . $recordid . ')"><span class="fa fa-edit"> Edit</span></a>' . " " . '<a href="#" onclick="deleteidentity(' . $recordid . ',' . $identity_no . ')"><span class="fa fa-remove warning"> Delete</span></a>';
                        }
                        $identity_name = Helpers_Person::get_person_identity_type($identityid);
                        $row = array(
                            $identity_name,
                            $identityno,
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

    /* Update Identity  */

    public function action_update_identity() {
        try {
            $this->auto_render = FALSE;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $pid = (int) Helpers_Utilities::encrypted_key($_POST['id'], "decrypt");
            //  print_r($pid); exit;
            //$pid = Session::instance()->get('personid');
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $update = Model_Personprofile::update_identity($_POST, $user_id, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Update Identity  */

    public function action_delete_identity() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            ;
            //$pid = Session::instance()->get('personid');
            $recordid = (int) $_GET['recordid'];
            //print_r($pid); exit;
            $update = Model_Personprofile::delete_identity($recordid, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //person Identities List
    public function action_ajaxpersonedu() {
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
                $post = Session::instance()->get('person_data_post', array());
                $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                //get person id from session
                // $pid = Session::instance()->get('personid');
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Personprofile;
                $rows_count = $data->get_person_education($post, 'true', $pid);
                $profiles = $data->get_person_education($post, 'false', $pid);
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $edutype = ( isset($item['edu_type']) ) ? $item['edu_type'] : '';
                        $edulevel = ( isset($item['education_level']) ) ? $item['education_level'] : 0;
                        $degname = ( isset($item['degree_name']) ) ? $item['degree_name'] : 0;
                        $degid = ( isset($item['id']) ) ? $item['id'] : 0;
                        $comyear = (!empty($item['complete_year']) ) ? $item['complete_year'] : 0;
                        $instname = ( isset($item['institute_name']) ) ? $item['institute_name'] : 0;
                        $degname1 = "'" . trim($item['degree_name']) . "'";
                        $instname1 = "'" . trim($item['institute_name']) . "'";
                        $action = '<a href="#" onclick="editedu(' . $edutype . ',' . $degname1 . ',' . $comyear . ',' . $instname1 . ',' . $degid . ',' . $edulevel . ')"><span class="fa fa-edit"> Edit</span></a>' . " " . '<a href="#" onclick="deleteedu(' . $degname1 . ',' . $degid . ')"><span class="fa fa-remove warning"> Delete</span></a>';
                        if ($edutype == 0) {
                            $edutype = "Religious";
                        } else {
                            $edutype = "Non-Religious";
                        }
                        $edulevelname = !empty($edulevel) ? Helpers_Utilities::get_education_level($edulevel) : "Unknown";
                        $row = array(
                            $edutype,
                            $edulevelname,
                            $degname,
                            $comyear,
                            $instname,
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

    /* Update education  */

    public function action_update_education() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $update = Model_Personprofile::update_education($_POST, $user_id, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Update Education  */

    public function action_delete_education() {
        try {
            $this->auto_render = FALSE;
            //person id from url
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //education id from url        
            $education_id = (int) $_GET['education_id'];

            $update = Model_Personprofile::delete_education($education_id, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //person Identities List
    public function action_ajaxpersonbanks() {
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
                $post = Session::instance()->get('person_data_post', array());
                $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                //get person id from session
                // $pid = Session::instance()->get('personid');

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Personprofile;
                $rows_count = $data->get_person_banks($post, 'true', $pid);
                $profiles = $data->get_person_banks($post, 'false', $pid);
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $account = ( isset($item['account_number']) ) ? $item['account_number'] : 'NA';
                        $atm = ( isset($item['atm_number']) ) ? $item['atm_number'] : 0;
                        $bankrecid = ( isset($item['id']) ) ? $item['id'] : 0;
                        $bankid = ( isset($item['bank_name']) ) ? $item['bank_name'] : 0;
                        $bankname = !empty($bankid) ? Helpers_Utilities::get_bank_list($bankid) : 'NA';
                        $bankbranch = ( isset($item['branch_name']) ) ? $item['branch_name'] : "NA";
                        $is_internet_b = ( isset($item['is_internet_banking']) ) ? $item['is_internet_banking'] : 0;
                        $account1 = "'" . trim($item['account_number']) . "'";
                        $bankbranch1 = "'" . trim($item['branch_name']) . "'";
                        $action = '<a href="#" onclick="editbanks(' . $account1 . ',' . $atm . ',' . $bankid . ',' . $bankbranch1 . ',' . $bankrecid . ',' . $is_internet_b . ')"><span class="fa fa-edit"> Edit</span></a>' . " " . '<a href="#" onclick="deletebank(' . $bankrecid . ', ' . $account1 . ',' . $atm . ')"><span class="fa fa-remove warning"> Delete</span></a>';
                        if (empty($is_internet_b)) {
                            $is_internet_b = "No";
                        } else {
                            $is_internet_b = "Yes";
                        }

                        $row = array(
                            $account,
                            $atm,
                            $bankname,
                            $bankbranch,
                            $is_internet_b,
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

    /* Update ebanks */

    public function action_update_banks() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['pid'], "decrypt");

            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $update = Model_Personprofile::update_banks($_POST, $user_id, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Update Delete  */

    public function action_delete_bank() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $bankrecid = (int) $_GET['bankrecid'];

            $update = Model_Personprofile::delete_bank($bankrecid, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //person Identities List
    public function action_ajaxpersoncrimes() {
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
                $post = Session::instance()->get('person_data_post', array());
                $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                //get person id from session
                //$pid = Session::instance()->get('personid');
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Personprofile;
                $rows_count = $data->get_person_crrecord($post, 'true', $pid);
                $profiles = $data->get_person_crrecord($post, 'false', $pid);
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $firno = ( isset($item['fir_number']) ) ? $item['fir_number'] : 'NA';
                        $firdate = ( isset($item['fir_date']) ) ? $item['fir_date'] : "";
                        $ps_id = ( isset($item['police_station_id']) ) ? $item['police_station_id'] : "NA";
                        if ($ps_id < 900) {
                            $ps_name = Helpers_Utilities::get_punjab_police_station($ps_id);
                        } else {
                            $ps_name = 'CTD ';
                            $ctd_ps_name = isset(Helpers_Utilities::get_ps_name($ps_id)->name) ? Helpers_Utilities::get_ps_name($ps_id)->name : 'Unknown';
                            $ps_name .= $ctd_ps_name;
                        }
                        $section = ( isset($item['sections_applied']) ) ? $item['sections_applied'] : "NA";
                        $cposition = ( isset($item['case_position']) ) ? $item['case_position'] : "NA";
                        $acposition = ( isset($item['accused_position']) ) ? $item['accused_position'] : "NA";
                        $firno1 = "'" . trim($item['fir_number']) . "'";
                        $firdate1 = "'" . trim($item['fir_date']) . "'";
                        $section1 = "'" . trim($item['sections_applied']) . "'";
                        //replace
                        $class = str_replace('/', '_', $item['fir_number']);
                        $fir_val = "'" . trim($class) . "'";
                        //$psid= "'" . trim($item['police_station_id']) . "'";
                        $action = '<a href="#" onclick="editcrrecord(' . $firno1 . ',' . $firdate1 . ',' . $item['police_station_id'] . ',' . $section1 . ',' . $item['case_position'] . ',' . $item['accused_position'] . ')"><span class="fa fa-edit"> Edit</span></a>' . " " . '<a class="item-' . $class . '" href="#" onclick="deletecriminalrec(' . $firno1 . ',' . $firdate1 . ',' . $item['police_station_id'] . ',' . $pid . ')"><span class="fa fa-remove warning"> Delete</span></a>';
                        $cposition1 = Helpers_Person::get_case_accused_position($cposition);
                        $acposition1 = Helpers_Person::get_case_accused_position($acposition);


                        $row = array(
                            $firno,
                            $firdate,
                            $ps_name,
                            $section,
                            $cposition1,
                            $acposition1,
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

    /* Update Criminal Record */

    public function action_update_criminalr() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $update = Model_Personprofile::update_criminalr($_POST, $user_id, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Update Delete  */

    public function action_deletecriminalrecord() {
        try {
            $this->auto_render = FALSE;
            //person id 
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //fir id 
            $post = $_POST;
            $update = Model_Personprofile::delete_criminalr($post, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //person Affiliations List
    public function action_ajaxpersonaffiliations() {
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
                $post = Session::instance()->get('person_data_post', array());

                //get person id from session
                //$pid = Session::instance()->get('personid');
                $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Personprofile;
                $rows_count = $data->get_person_affiliations($post, 'true', $pid);
                $profiles = $data->get_person_affiliations($post, 'false', $pid);
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $stance = ( isset($item['ideological_stance']) ) ? $item['ideological_stance'] : 0;
                        $recruited = ( isset($item['self_recruitment_details']) ) ? $item['self_recruitment_details'] : 'N/A';
                        $orgstance = !empty($stance) ? Helpers_Utilities::get_organizations_stance($stance) : 'N/A';
                        $org_id = ( isset($item['organization_id']) ) ? $item['organization_id'] : 1;
                        $orgname = Helpers_Utilities::get_banned_organizations_name($org_id);
                        // $desig = ( isset($item['designation']) ) ? $item['designation'] : "N/A";
                        $desig = !empty($item['designation']) ? Helpers_Utilities::get_organization_designation($item['designation']) : "Unknown";


                        if ($desig == 'Unknown')
                            $desig = 'N/A';
                        else
                            $desig = !empty($desig->organization_designation) ? $desig->organization_designation : 'N/A';
                        $details = ( isset($item['details']) ) ? $item['details'] : " N/A ";
                        $is_trained = ( isset($item['is_trained']) ) ? $item['is_trained'] : 0;
                        $details1 = "'" . trim($item['details']) . "'";
                        $orgstance1 = "'" . trim($item['self_recruitment_details']) . "'";
                        $project_name1 = 0;
                        if (!empty($is_trained)) {
                            $training = "Yes";
                        } else {
                            $training = "No";
                        }
                        $action = '<a href="#" onclick="editaffiliations(' . $item['id'] . ',' . $project_name1 . ',' . $item['organization_id'] . ',' . trim($item['designation']) . ',' . $details1 . ',' . $stance . ',' . $orgstance1 . ' )"><span class="fa fa-edit"> Edit</span></a>';


                        $row = array(
                            $orgname,
                            $orgstance,
                            $desig,
                            $details,
                            $recruited,
                            $training,
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

    //person Affiliations List
    public function action_ajaxpersontrainings() {
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
                $post = Session::instance()->get('person_data_post', array());

                //get person id from session
                //$pid = Session::instance()->get('personid');
                $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                //print_r($post); exit;
                $data = new Model_Personprofile;
                $rows_count = $data->get_person_trainings($post, 'true', $pid);
                $profiles = $data->get_person_trainings($post, 'false', $pid);
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
                        $organization_id = ( isset($item['organization_id']) ) ? $item['organization_id'] : 0;
                        $pid = ( isset($item['person_id']) ) ? $item['person_id'] : 0;
                        $trainig_camp = ( isset($item['training_camp']) ) ? $item['training_camp'] : '';
                        $traing_site = ( isset($item['training_site']) ) ? $item['training_site'] : '';
                        $training_type = ( isset($item['training_type_id']) ) ? $item['training_type_id'] : 0;
                        $training_duration = ( isset($item['training_duration']) ) ? $item['training_duration'] : 0;
                        $training_year = ( isset($item['training_year']) ) ? $item['training_year'] : 0;
                        $training_purpose = ( isset($item['training_purpose']) ) ? $item['training_purpose'] : '';
                        $material_taught = ( isset($item['material_taught']) ) ? $item['material_taught'] : '';
                        $other_details = ( isset($item['other_details']) ) ? $item['other_details'] : '';


                        $traing_site1 = "'" . trim($item['training_site']) . "'";
                        $training_purpose1 = "'" . trim($item['training_purpose']) . "'";
                        $material_taught1 = "'" . trim($item['material_taught']) . "'";
                        $other_details1 = "'" . trim($item['other_details']) . "'";
                        $action = '<a href="#training_org" onclick="edittraining(' . $id . ',' . $organization_id . ',' . $pid . ',' . $trainig_camp . ',' . $traing_site1 . ',' . $training_purpose1 . ',' . $material_taught1 . ',' . $other_details1 . ' ,' . $training_type . ',' . $training_year . ',' . $training_duration . ')"><span class="fa fa-edit"> Edit</span></a>';


                        $organization_name = !empty($organization_id) ? Helpers_Utilities::get_banned_organizations_name($organization_id) : '';
                        $typ = !empty($training_type) ? Helpers_Utilities::get_organization_training_type($training_type)->training_type : '';
                        $camp_name = !empty($trainig_camp) ? Helpers_Utilities::get_organization_training_camp($trainig_camp)->training_camp : '';

                        $row = array(
                            $organization_name,
                            $camp_name,
                            $traing_site,
                            $typ,
                            $training_duration,
                            $training_year,
                            $training_purpose,
                            $material_taught,
                            $other_details,
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

    public function action_ajaxgettrainingorg() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $pid = (int) Helpers_Utilities::encrypted_key($_POST['id'], "decrypt");
            $aff_data = !empty($pid) ? Model_Personprofile::get_person_affiliated_org($pid) : '';
            $html = '<option value="">Please select organization</option>';
            if (!empty($aff_data)) {
                foreach ($aff_data as $aff_data1) {
                    $html .= '<option value="' . $aff_data1['organization_id'] . '">' . Helpers_Utilities::get_banned_organizations_name($aff_data1['organization_id']) . '</option>';
                }
            } else {
                $html = '<option value="">Please affiliate person first</option>';
            }
            echo $html;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Update Criminal Record */

    public function action_update_affiliations() {
        try {
            $this->auto_render = FALSE;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $pid = (int) Helpers_Utilities::encrypted_key($_POST['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $update = Model_Personprofile::update_affiliations($_POST, $user_id, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Update Criminal Record */

    public function action_update_trainings() {
        try {
            $this->auto_render = FALSE;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $pid = (int) Helpers_Utilities::encrypted_key($_POST['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $update = Model_Personprofile::update_trainings($_POST, $user_id, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* get project name */

    public function action_get_project_id() {
        try {
            $this->auto_render = FALSE;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            if (!empty($_POST['org_id'])) {
                $org_id = $_POST['org_id'];
                $results = Helpers_Utilities::get_project_id($org_id);
            } else {
                $results = 1;
            }
            echo json_encode($results);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*  Delete affiliations  */

    public function action_deleteaffiliations() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $recid = $_GET['affiliationid'];
            $update = Model_Personprofile::delete_affiliations($recid, $pid);
            echo json_encode($update);
        } catch (Exception $e) {
            
        }
    }

    /* Update Person Picture */

    public function action_update_personpic() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //print_r($pid); exit;
            //$pid = Session::instance()->get('personid');
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            if (isset($_FILES['personpic']) and $_FILES['personpic'] != "") {
                $personpic = Helpers_Profile::_save_image($_FILES['personpic'], "person", $pid);
            } else {
                $personpic = "";
            }
            $_POST['person_pic'] = $personpic;
            $update = Model_Personprofile::update_personpic($_POST, $user_id, $pid);
            //dowload link
            $person_download_data_path = !empty($pid) ? Helpers_Person::get_person_download_data_path($pid) : '';
            if (!empty($person_download_data_path) && $personpic) {
                $piclink = $person_download_data_path . $personpic;
                echo HTML::image("{$piclink}", array("height" => "450px", "width" => "450px"));
                //echo json_encode($update);
            }
        } catch (Exception $e) {
            
        }
    }

    /* Update Person Verysis */
    public function action_update_personverysis() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //print_r($pid); exit;
            //$pid = Session::instance()->get('personid');
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            if (!empty($_POST['userid']))
                $user_id = $_POST['userid'];

            if (isset($_FILES['personverysis']) and $_FILES['personverysis'] != "") {
                Helpers_Utilities::check_file_from_blacklist($_FILES['personverysis']);
                $personverysis = Helpers_Profile::_save_image($_FILES['personverysis'], "person_verysis", $pid);
            } else {
                $personverysis = "";
            }
            $_POST['person_verysis'] = $personverysis;
            $update = Model_Personprofile::update_personverisis($_POST, $user_id, $pid);
            //get person assets dowload path
            $person_download_data_path = !empty($pid) ? Helpers_Person::get_person_download_data_path($pid) : '';
            if (!empty($person_download_data_path) && $personverysis) {
                $piclink = $person_download_data_path . $personverysis;
                echo HTML::image("{$piclink}", array("height" => "450px", "width" => "450px"));
                //echo json_encode($update);
            }
        } catch (Exception $ex) {
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex->getMessage());
                exit;
            } else {
                echo 2;
                exit;
            }
        }
    }
    /* Update Person fam tree */
    public function action_update_personftreepic() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //print_r($pid); exit;
            //$pid = Session::instance()->get('personid');
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            if (!empty($_POST['userid']))
                $user_id = $_POST['userid'];

            if (isset($_FILES['personftreepic']) and $_FILES['personftreepic'] != "") {
                Helpers_Utilities::check_file_from_blacklist($_FILES['personftreepic']);
                $personverysis = Helpers_Profile::_save_image($_FILES['personftreepic'], "person_familytree", $pid);
            } else {
                $personverysis = "";
            }
            $_POST['person_familytree'] = $personverysis;
            $update = Model_Personprofile::update_personftreepic($_POST, $user_id, $pid);
            //get person assets dowload path
            $person_download_data_path = !empty($pid) ? Helpers_Person::get_person_download_data_path($pid) : '';
            if (!empty($person_download_data_path) && $personverysis) {
                $piclink = $person_download_data_path . $personverysis;
                echo HTML::image("{$piclink}", array("height" => "450px", "width" => "450px"));
                //echo json_encode($update);
            }
        } catch (Exception $ex) {
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex->getMessage());
                exit;
            } else {
                echo 2;
                exit;
            }
        }
    }
    /* Update Person update_verisys_info_form */
    public function action_update_verisys_info() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;            
            $update_data = Model_Personprofile::update_verisys_info($_POST, $user_id, $pid); 
            echo json_encode($update_data);
            exit;
        } catch (Exception $ex) {
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;  
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex->getMessage());
                exit;
            } else {
                echo 2;
                exit;
            }
        }
    }

    /* Update Person Verysis */

    public function action_update_personverysisrequest() {
//        try {
//            echo '<pre>'; print_r($_FILES['personverysis']); exit;
            $this->auto_render = FALSE;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $requesting_user = $_POST['userid'];
            //get person id by updating table
            $array_person['cnic_number'] = $_POST['cnic_number'];
            $array_person['user_id'] = $requesting_user;
            $content = new Model_Generic();
            $pid = $content->update_cnic_number($array_person);

            if (isset($_FILES['personverysis']) and ( $_FILES['personverysis'] != "") && (!empty($pid))) {

                Helpers_Utilities::check_file_from_blacklist($_FILES['personverysis']);

                $personverysis = Helpers_Profile::_save_image($_FILES['personverysis'], "person_verysis", $pid);

                $_POST['person_verysis'] = $personverysis;
                $update = Model_Personprofile::update_personverisisrequest($_POST, $requesting_user, $pid);
                echo json_encode($update);
            } else {
                $personverysis = "";
                echo json_encode(3);
            }
//        } catch (Exception $ex) {
//            if (Helpers_Utilities::check_user_id_developers($user_id)) {
//                echo '<pre>';
//                print_r($ex);
//                exit;
//            } else {
//                echo 2;
//                exit;
//            }
//        }
    }
    /* Update Person Verysis */

    public function action_update_personfamilytreerequest() {
//        try {
//            echo '<pre>'; print_r($_FILES['personverysis']); exit;
            $this->auto_render = FALSE;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $requesting_user = $_POST['userid'];
            //get person id by updating table
            $array_person['cnic_number'] = $_POST['cnic_number'];
            $array_person['user_id'] = $requesting_user;
            $content = new Model_Generic();
            $pid = $content->update_cnic_number($array_person);

            if (isset($_FILES['personfamilytree']) and ( $_FILES['personfamilytree'] != "") && (!empty($pid))) {

                Helpers_Utilities::check_file_from_blacklist($_FILES['personfamilytree']);

                $personverysis = Helpers_Profile::_save_image($_FILES['personfamilytree'], "person_familytree", $pid);

                $_POST['personfamilytree'] = $personverysis;
                $update = Model_Personprofile::update_personfamilytreerequest($_POST, $requesting_user, $pid);
                echo json_encode($update);
            } else {
                $personverysis = "";
                echo json_encode(3);
            }
//        } catch (Exception $ex) {
//            if (Helpers_Utilities::check_user_id_developers($user_id)) {
//                echo '<pre>';
//                print_r($ex);
//                exit;
//            } else {
//                echo 2;
//                exit;
//            }
//        }
    }

    /* Admin Update Person Verysis */

    public function action_admin_update_personverysisrequest() {
        try {
            $this->auto_render = FALSE;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $requesting_user = $_POST['userid'];
            //get person id by updating table
            $array_person['cnic_number'] = $_POST['cnic_number'];
            $array_person['user_id'] = $requesting_user;
            $content = new Model_Generic();
            $pid = $content->update_cnic_number($array_person);

            if (isset($_FILES['personverysis']) and ( $_FILES['personverysis'] != "") && (!empty($pid))) {
                Helpers_Utilities::check_file_from_blacklist($_FILES['personverysis']);
                $personverysis = Helpers_Profile::_save_image($_FILES['personverysis'], "person_verysis", $pid);
                $_POST['person_verysis'] = $personverysis;
                $update = Model_Personprofile::update_personverisisrequest($_POST, $requesting_user, $pid);
                echo json_encode($update);
            } else {
                $personverysis = "";
                echo json_encode(3);
            }
        } catch (Exception $ex) {
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex);
                exit;
            } else {
                echo 2;
                exit;
            }
        }
    }

    /* Update Person Verysis temp bulk images */

    public function action_nadra_requests_temp_upload() {
        try {
            $this->auto_render = FALSE;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $result = array();
            if (!empty($_FILES['personverysis'])) {
                $result = Helpers_Profile::_save_verisys_temp_image($_FILES['personverysis'], $user_id);
                echo json_encode($result);
            } else {
                echo json_encode(2);
            }
        } catch (Exception $ex) {
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex);
                exit;
            } else {
                echo json_encode(2);
            }
        }
    }
    /* Update Person familytree temp bulk images */

    public function action_familytree_requests_temp_upload() {
        try {
            $this->auto_render = FALSE;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $result = array();
            if (!empty($_FILES['personfamilytree'])) {
                $result = Helpers_Profile::_save_familytree_temp_image($_FILES['personfamilytree'], $user_id);
                echo json_encode($result);
            } else {
                echo json_encode(2);
            }
        } catch (Exception $ex) {
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex);
                exit;
            } else {
                echo json_encode(2);
            }
        }
    }




    /* Update Person Verysis temp bulk images */

    public function action_nadra_requests_temp_upload_databank() {
        try {
            $this->auto_render = FALSE;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $cnic1 = !empty($_POST['cnic']) ? $_POST['cnic'] : '';
            $cnic= explode(',',$cnic1);

            $result = array();
            if (!empty($_FILES['personverysis'])) {
                $result = Helpers_Profile::_save_verisys_temp_image($_FILES['personverysis'], $user_id);
                echo json_encode($result);
            } else {
                echo json_encode(2);
            }
        } catch (Exception $ex) {
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex);
                exit;
            } else {
                echo json_encode(2);
            }
        }
    }
    /* Update travelhistory  bulk images */

    public function action_travelhistory_requests_temp_upload() {
        try {
            $this->auto_render = FALSE;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $result = array();
            if (!empty($_FILES['travelhistoryfiles'])) {                
                $result = Helpers_Profile::_save_travelhistory_temp_image($_FILES['travelhistoryfiles'], $user_id);
                echo json_encode($result);
            } else {
                echo json_encode(2);
            }
        } catch (Exception $ex) {
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex);
                exit;
            } else {
                echo json_encode(2);
            }
        }
    }
    
        /* Upload Travel History File */

    public function action_update_persontravelhistory() {
        try {
            $this->auto_render = FALSE;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $requesting_user = $_POST['userid'];
            //get person id by updating table
            $array_person['cnic_number'] = $_POST['cnic_number'];
            $array_person['user_id'] = $requesting_user;
            $content = new Model_Generic();
            //$pid = $content->update_cnic_number($array_person);
            if (isset($_FILES['travelhistoryfile']) and ( $_FILES['travelhistoryfile'] != "")) {
                $update = Model_Personprofile::update_travelhistory_request($_FILES['travelhistoryfile'],$_POST, $requesting_user);
                echo json_encode($update);
            } else {
                $personverysis = "";
                echo json_encode(3);
            }
        } catch (Exception $ex) {
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex);
                exit;
            } else {
                echo 2;
                exit;
            }
        }
    }

    //person Affiliations List
    public function action_ajaxpersonreports() {
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
                $post = Session::instance()->get('person_data_post', array());
                $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                //get person id from session
                //  $pid = Session::instance()->get('personid');
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_Personprofile;
                $rows_count = $data->get_person_reports($post, 'true', $pid);
                $profiles = $data->get_person_reports($post, 'false', $pid);
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $reporttype = ( isset($item['report_type']) ) ? $item['report_type'] : 'NA';
                        if ($reporttype == 1) {
                            $reporttype1 = "Interrogation Report";
                        } elseif ($reporttype == 2) {
                            $reporttype1 = "Investigation Report";
                        } elseif ($reporttype == 3) {
                            $reporttype1 = "Special Report";
                        } elseif ($reporttype == 4) {
                            $reporttype1 = "Intelligance Report";
                        } elseif ($reporttype == 5) {
                            $reporttype1 = "Ground Check Report";
                        } elseif ($reporttype == 6) {
                            $reporttype1 = "FIR Report";
                        } elseif ($reporttype == 7) {
                            $reporttype1 = "Recommendations/Remarks";
                        } elseif ($reporttype == 8) {
                            $reporttype1 = "Other";
                        }
                        $reportref = ( isset($item['report_reference_no']) ) ? $item['report_reference_no'] : "";
                        $reportdate = ( isset($item['report_date']) ) ? $item['report_date'] : "NA";
                        $reportdetails = ( isset($item['report_details']) ) ? $item['report_details'] : "NA";
                        $reportlink = (!empty($item['file_link']) ) ? $item['file_link'] : "";
                        //get person assets dowload path
                        $person_download_data_path = !empty($pid) ? Helpers_Person::get_person_download_data_path($pid) : '';

                        if ($reportlink != '' && !empty($person_download_data_path)) {
                            $filelink = $person_download_data_path . $reportlink;
                            $reportdwd = '<a target="_blank" href="' . $filelink . '" >' . $reportlink . '</a>';
                        } else {
                            $reportdwd = '';
                        }
                        $reportref1 = "'" . trim($item['report_reference_no']) . "'";
                        $reportdate1 = "'" . trim($item['report_date']) . "'";
                        $reportdetails1 = "'" . trim($item['report_details']) . "'";
                        $action = '<a href="#" onclick="editreports(' . $reporttype . ',' . $reportref1 . ',' . $reportdate1 . ',' . $reportdetails1 . ')"><span class="fa fa-edit"> Edit</span></a>' . " " . '<a href="#" onclick="deletereport(' . $reporttype . ',' . $reportref1 . ')"><span class="fa fa-remove warning"> Delete</span></a>';


                        $row = array(
                            $reporttype1,
                            $reportref,
                            $reportdate,
                            $reportdetails,
                            $reportdwd,
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

    /* Update Person Reports */

    public function action_update_personreports() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $_POST = Helpers_Utilities::remove_injection($_POST);

            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $reportname = Helpers_Utilities::get_person_report_types($_POST['reporttype']);
            $reportname = str_replace("Report", "-Report", $reportname);
            $reportname = str_replace("Ground Check", "Groung-Check=Report", $reportname);
            $reportname = str_replace("Recommendations/Remarks", "recommendations-remarks-Report", $reportname);
            if (!empty($_FILES)) {
                Helpers_Utilities::check_file_from_blacklist($_FILES['personfile']);
                $reportfile1 = Helpers_Upload::upload_person_documents($_FILES, "person_report", $pid, $reportname);
            } else {
                $reportfile1 = "";
            }
            $_POST['file_link'] = $reportfile1;
            //print_r($reportfile1); exit;
            $update = Model_Personprofile::update_personreports($_POST, $user_id, $pid);
            // echo HTML::image("dist/uploads/person/verysis_images/{$personverysis}", array("height" => "50%", "width" => "50%"));
            echo json_encode($update);
        } catch (Exception $ex) {
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex);
                exit;
            } else {
                echo json_encode(2);
                exit;
            }
        }
    }

    /*  Delete report  */

    public function action_deletereport() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $reportid = $_GET['reportid'];
            $refrenceno = $_GET['refrenceid'];
            $update = Model_Personprofile::delete_personreport($reportid, $refrenceno, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //Person recommendations/remarks
    public function action_ajaxpersonsources() {
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
                $post = Session::instance()->get('person_data_post', array());
                $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                //get person id from session
                // $pid = Session::instance()->get('personid');
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Personprofile;
                $rows_count = $data->get_person_income_sources($post, 'true', $pid);
                $profiles = $data->get_person_income_sources($post, 'false', $pid);
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $sname = ( isset($item['income_source_name']) ) ? $item['income_source_name'] : "";
                        $sdatails = ( isset($item['details']) ) ? $item['details'] : "NA";
                        $sourceid = ( isset($item['id']) ) ? $item['id'] : 0;
                        $reportlink = ( isset($item['file_link']) ) ? $item['file_link'] : "";
                        //get person assets dowload path
                        $person_download_data_path = !empty($pid) ? Helpers_Person::get_person_download_data_path($pid) : '';

                        if (!empty($reportlink) && !empty($person_download_data_path)) {
                            $filelink = $person_download_data_path . $reportlink;
                            $reportdwd = '<a target="_blank" href="' . $filelink . '" >' . $reportlink . '</a>';
                        } else {
                            $reportdwd = '';
                        }
                        $sname1 = "'" . trim($item['income_source_name']) . "'";
                        $sdatails1 = "'" . trim($item['details']) . "'";
                        $action = '<a href="#" onclick="editsource(' . $sname1 . ',' . $sdatails1 . ',' . $sourceid . ')"><span  class="fa fa-edit"> Edit</span></a>' . " " . '<a href="#" onclick="deletesource(' . $sname1 . ',' . $sourceid . ')"><span class="fa  fa-remove warning"> Delete</span></a>';



                        $row = array(
                            $sname,
                            $sdatails,
                            $reportdwd,
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

    /*  Delete income source  */

    public function action_deletesource() {
        try {
            $this->auto_render = FALSE;
            //person id from url
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //Income source id  from url
            $sourceid = (int) $_GET['source_income'];
            $update = Model_Personprofile::delete_income_source($sourceid, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Update Person Reports */

    public function action_update_personincomesource() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");

            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            if (!empty($_FILES)) {
                Helpers_Utilities::check_file_from_blacklist($_FILES['personfile']);
                $reportfile1 = Helpers_Upload::upload_person_documents($_FILES, "person_income_source", $pid, 'income-source');
            } else {
                $reportfile1 = "";
            }
            $_POST['file_link'] = $reportfile1;
            // print_r($reportfile1); exit;
            $update = Model_Personprofile::update_personincomesource($_POST, $user_id, $pid);
            // echo HTML::image("dist/uploads/person/verysis_images/{$personverysis}", array("height" => "50%", "width" => "50%"));
            echo json_encode($update);
        } catch (Exception $ex) {
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex);
                exit;
            } else {
                echo json_encode(2);
                exit;
            }
        }
    }

    /* Update Person Reports */

    public function action_update_personassets() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $_POST = Helpers_Utilities::remove_injection($_POST);

            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            if (!empty($_FILES)) {
                Helpers_Utilities::check_file_from_blacklist($_FILES['personfile']);
                $reportfile1 = Helpers_Upload::upload_person_documents($_FILES, "person_assets", $pid, 'asset');
            } else {
                $reportfile1 = "";
            }
            // print_r($reportfile1); exit;
            $_POST['file_link'] = $reportfile1;
            //print_r($reportfile1); exit;
            $update = Model_Personprofile::update_personassets($_POST, $user_id, $pid);
            // echo HTML::image("dist/uploads/person/verysis_images/{$personverysis}", array("height" => "50%", "width" => "50%"));
            echo json_encode($update);
        } catch (Exception $ex) {
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex);
                exit;
            } else {
                echo json_encode(2);
                exit;
            }
        }
    }

    //Person recommendations/remarks
    public function action_ajaxpersonassets() {
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
                $post = Session::instance()->get('person_data_post', array());
                $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                //get person id from session
                // $pid = Session::instance()->get('personid');
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_Personprofile;
                $rows_count = $data->get_person_assets($post, 'true', $pid);
                $profiles = $data->get_person_assets($post, 'false', $pid);
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $sname = ( isset($item['asset_name']) ) ? $item['asset_name'] : "";
                        $sdatails = ( isset($item['details']) ) ? $item['details'] : "NA";
                        $assetid = ( isset($item['id']) ) ? $item['id'] : 0;
                        $reportlink = ( isset($item['file_link']) ) ? $item['file_link'] : "";
                        //get person assets dowload path
                        $person_download_data_path = !empty($pid) ? Helpers_Person::get_person_download_data_path($pid) : '';

                        if (!empty($reportlink) && !empty($person_download_data_path)) {
                            $reportdwd = '<a target="_blank" href="' . $person_download_data_path . $reportlink . '" >' . $reportlink . '</a>';
                        } else {
                            $reportdwd = '';
                        }
                        // $reportdwd = '<a target="_blank" href="' . URL::base() . '/personprofile/download?fid=2&pid='.$pid.'&file=' . $reportlink . '" >' . $reportlink . '</a>';
                        $sname1 = "'" . trim($item['asset_name']) . "'";
                        $sdatails1 = "'" . trim($item['details']) . "'";
                        $action = '<a href="#" onclick="editasset(' . $sname1 . ',' . $sdatails1 . ',' . $assetid . ')"><span class="fa fa-edit"> Edit</span></a>' . " " . '<a href="#" onclick="deleteasset(' . $assetid . ',' . $sname1 . ')"><span class="fa fa-remove warning"> Delete</span></a>';


                        $row = array(
                            $sname,
                            $sdatails,
                            $reportdwd,
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

    /*  Delete assets  */

    public function action_deleteasset() {
        try {
            $this->auto_render = FALSE;
            //person id 
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //Asset id 
            $recid = (int) $_GET['assetid'];

            $update = Model_Personprofile::delete_asset($recid, $pid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    public function action_download() {
        $login_user = Auth::instance()->get_user();
        $user_id    = !empty($login_user->id) ? $login_user->id : 0;

        // Clean output buffers early
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        try {
            $_GET  = Helpers_Utilities::remove_injection($_GET);
            $_POST = Helpers_Utilities::remove_injection($_POST);

            $target = '';
            if (!empty($_GET['pid'])) {
                $decrypted_pid = Helpers_Utilities::encrypted_key($_GET['pid'], "decrypt");
                $target = Helpers_Person::get_person_download_data_path($decrypted_pid);
            }
            if (!empty($_POST['fid'])) {
                $decrypted_fid = Helpers_Utilities::encrypted_key($_POST['fid'], 'decrypt');
                $target = Helpers_Upload::get_request_data_path($decrypted_fid);
            }

            $requested_file = !empty($_POST['file']) ? $_POST['file'] : (!empty($_GET['file']) ? $_GET['file'] : '');
            $file = rtrim($target, '/\\') . '/' . ltrim($requested_file, '/\\');

            if (!$file ) {
                http_response_code(404);
                die('File not found');
            }

            // Get extension (lowercase, without dot)
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            // MIME type mapping
            $mimeTypes = [
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'xls'  => 'application/vnd.ms-excel',
                'csv'  => 'text/csv',
                'zip'  => 'application/zip',
                'rar'  => 'application/x-rar-compressed',
                // Add more if needed: pdf, docx, etc.
            ];

            $contentType = $mimeTypes[$ext] ?? 'application/octet-stream';

            // Optional: improve filename (use original requested name if available)
            $downloadName = !empty($_GET['filename']) ? $_GET['filename'] : basename($file);
            // Make sure it has extension
            if (strpos($downloadName, '.') === false) {
                $downloadName .= '.' . $ext;
            }

            // Headers
            header('Cache-Control: public, must-revalidate, max-age=0');
            header('Pragma: public');
            header('Expires: 0');
            header('Content-Description: File Transfer');
            header('Content-Transfer-Encoding: binary');
            header('Content-Type: ' . $contentType);
            header('Content-Length: ' . filesize($file));
            header('Content-Disposition: attachment; filename="' . $downloadName . '"');

            // For large files: increase timeout & flush
            set_time_limit(0);
            readfile($file);
            exit;

        } catch (Exception $ex) {
            http_response_code(500);
            echo 'Error in file download: ' . htmlspecialchars($ex->getMessage());
        }
    }

    /* Update Person Picture */
    public function action_upload_person_pictures() {
        try {
            $this->auto_render = FALSE;
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //print_r($_POST); exit;
            //$pid = Session::instance()->get('personid');
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            if (isset($_FILES['personpic']) and $_FILES['personpic'] != "") {
                Helpers_Utilities::check_file_from_blacklist($_FILES['personpic']);
                $personpic = Helpers_Profile::_save_image($_FILES['personpic'], "person_pictures", $pid);
            } else {
                $personpic = "";
            }
            //print_r($pid); exit;
            $_POST['person_pic'] = $personpic;
            $update = Model_Personprofile::upload_person_pictures($_POST, $user_id, $pid);
            //get person assets dowload path
            $person_download_data_path = !empty($pid) ? Helpers_Person::get_person_download_data_path($pid) : '';
            if (!empty($person_download_data_path) && $personpic) {
                $piclink = $person_download_data_path . $personpic;
                echo HTML::image("{$piclink}", array("height" => "200px", "width" => "200px"));
                //echo json_encode($update);
            }
        } catch (Exception $ex) {            
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex->getMessage());
                exit;
            } else {
                echo 2;
                exit;
            }
        }
    }

    public function action_person_info_update() {
        try {
            $post = $this->request->post();
            $post_data = array_merge($post, $_GET);
            $post_data = Helpers_Utilities::remove_injection($post_data);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                //$pid = Session::instance()->get('personid');
                $person_table_data = Helpers_Person::get_person_table_data($pid);
                $person_initiate_table_data = Helpers_Person::get_person_initiate_table_data($pid);
                $person_nadra_profile_table_data = Helpers_Person::get_person_nadra_profile_table_data($pid);
                $person_foreigner_profile_table_data = Helpers_Person::get_person_foreigner_profile_table_data($pid);
                $this->template->content = View::factory('templates/persons/person_info_update')
                        ->bind('person_table_data', $person_table_data)
                        ->bind('person_initiate', $person_initiate_table_data)
                        ->bind('person_nadra_profile', $person_nadra_profile_table_data)
                        ->bind('person_foreigner', $person_foreigner_profile_table_data)
                        ->bind('person_id', $pid);
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    public function action_person_info_update_post() {
        try {
            //Update Basic Info
            $post = $this->request->post();
            $post_data = array_merge($post, $_GET);
            $post_data = Helpers_Utilities::remove_injection($post_data);

            $result = Helpers_Person::update_person_information($post_data);
            echo json_encode($result);
            //Update Person Initiate Table
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
            echo json_encode(2);
        }
    }

    //update person date of birth
    public function action_person_dob_update() {
        try {
            //Update Basic Info
            $post = $this->request->post();
            $post_data = array_merge($post, $_GET);
            $post_data = Helpers_Utilities::remove_injection($post_data);

            $result = Helpers_Person::update_person_dob($post_data);
            echo json_encode($result);
            //Update Person Initiate Table
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }
    
        /* Person Dashboard / Person Profile / Person Verisys */

    public function action_person_pictures() {
        try {
            $post = $this->request->post();
            $post_data = array_merge($post, $_GET);
            $post_data = Helpers_Utilities::remove_injection($post_data);
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                $this->template->content = View::factory('templates/persons/Person_profile/person_pictures')
                        ->bind('person_id', $pid);
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

}

// End Persons Profile Class

