<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Adminreports extends Controller_Working {

    public function __Construct(Request $request, Response $response) {
        parent::__construct($request, $response);
        $this->request = $request;
        $this->response = $response;
    }

    /*     * Admin Reports Indetity breakup */

    public function action_identity_breakup_report() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 34) == 1) {
                /* File Included */
                $this->template->content = View::factory('templates/user/identity_breakup');
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*     * Admin Reports Indetity breakup */

    public function action_persons_with_passpoertnumbers() {
        try {
            $pessport_count = Helpers_Adminreports::get_identity_count(6);
            $total_persons = Helpers_Adminreports::get_total_person_count();
            $percentage = (($pessport_count / $total_persons) * 100);

            $html = '<span class="info-box-text">Total Number of "<u>Passport No</u>"</span>
                                    <span class="info-box-number"> ' . $pessport_count . '</span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: ' . floor($percentage) . '%"></div>
                                    </div>
                                    <span class="progress-description">
                                        ' . round($percentage, 2) . ' % out of <u>' . $total_persons . '</u> person
                                    </span>';
            echo $html;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*     * Admin Reports Indetity breakup */

    public function action_persons_with_armedlicence() {
        try {
            $armedlicence_count = Helpers_Adminreports::get_identity_count(1);
            $total_persons = Helpers_Adminreports::get_total_person_count();
            $percentage = (($armedlicence_count / $total_persons) * 100);
            $html = '<span class="info-box-text">Total Number of "<u>Armed License No</u>"</span>
                                    <span class="info-box-number">' . $armedlicence_count . '</span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: ' . floor($percentage) . '%"></div>
                                    </div>
                                    <span class="progress-description">
                                        ' . round($percentage, 2) . ' % out of <u>' . $total_persons . '</u> person
                                    </span>';
            echo $html;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*     * Admin Reports Indetity breakup */

    public function action_persons_with_drivinglicence() {
        try {
            $drivinglicence_count = Helpers_Adminreports::get_identity_count(2);
            $total_persons = Helpers_Adminreports::get_total_person_count();
            $percentage = (($drivinglicence_count / $total_persons) * 100);
            $html = '<span class="info-box-text">Total Number of "<u>Driving License No</u>"</span>
                                    <span class="info-box-number">' . $drivinglicence_count . '</span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: ' . floor($percentage) . '%"></div>
                                    </div>
                                    <span class="progress-description">
                                        ' . round($percentage, 2) . ' % out of <u>' . $total_persons . '</u> person
                                    </span>';
            echo $html;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*     * Admin Reports Indetity breakup */

    public function action_persons_with_ntnnumber() {
        try {
            $ntnnumber_count = Helpers_Adminreports::get_identity_count(3);
            $total_persons = Helpers_Adminreports::get_total_person_count();
            $percentage = (($ntnnumber_count / $total_persons) * 100);
            $html = '<span class="info-box-text">Total Number of "<u>NTN No</u>"</span>
                                    <span class="info-box-number">' . $ntnnumber_count . '</span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: ' . floor($percentage) . '%"></div>
                                    </div>
                                    <span class="progress-description">
                                        ' . round($percentage, 2) . ' % out of <u>' . $total_persons . '</u> person
                                    </span>';
            echo $html;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*     * Admin Reports Indetity breakup */

    public function action_persons_with_foreignercnic() {
        try {
            $foreigner_cnic_count = Helpers_Adminreports::get_foreigner_cnic_count();
            $total_persons = Helpers_Adminreports::get_total_person_count();
            $percentage = (($foreigner_cnic_count / $total_persons) * 100);
            $html = '<span class="info-box-text">Total Number of "<u>Foreigner NIC</u>"</span>
                                    <span class="info-box-number">' . $foreigner_cnic_count . '</span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: ' . floor($percentage) . '%"></div>
                                    </div>
                                    <span class="progress-description">
                                        ' . round($percentage, 2) . ' % out of <u>' . $total_persons . '</u> person
                                    </span>';
            echo $html;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*     * Admin Reports Verisys Pending */

    public function action_verisys_pending_report() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 35) == 1) {
                /* File Included */
                $this->template->content = View::factory('templates/user/adminreports/verisys_pending');
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*     * Admin Reports users_breakup */

    public function action_users_breakup_report() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 38) == 1) {
                /* File Included */
                $this->template->content = View::factory('templates/user/adminreports/users_breakup_report');
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /* Function to get data of Pending Nadra verisys requests  */

    public function action_pending_nadravarisys_data() {
        try {
            $data = array();
            $nadra_data = Helpers_Adminreports::get_pending_nadravarisys_data();
            foreach ($nadra_data as $row) {
                if ($row['region_id'] == 0) {
                    $row['region_id'] = 11;
                }
                $data['region'][] = Helpers_Utilities::get_region($row['region_id']);
                $data['requests'][] = $row['requests'];
            }
            if (!empty($nadra_data)) {
                echo json_encode($data);
                exit;
            } else {
                echo -1;
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*     * Admin Reports Verisys response report */

    public function action_verisys_response_report() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 51) == 1) {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';            
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                Session::instance()->set('verisys_response_post', $post);
                include 'excel/verisysdailyreport.inc';
                $this->template->content = View::factory('templates/user/adminreports/verisys_response')
                        ->set('search_post', $post);
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     *  verisys response Reports Ajax Call data
     */

    public function action_ajaxverisysresponse() {
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
                $post = Session::instance()->get('verisys_response_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Adminreports;
                $rows_count = $data->verisys_response_summary($post, 'true');
                $profiles = $data->verisys_response_summary($post, 'false');

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
                        $region = ( isset($item['region_id']) ) ? $item['region_id'] : 'NA';
                        $region_name = Helpers_Utilities::get_region($region);
                        $request_date = ( isset($item['request_date']) ) ? $item['request_date'] : 0;
                        $request_date_new = strtotime(date('Y-m-d', strtotime($request_date)));
                        $request_count = ( isset($item['request_count']) ) ? $item['request_count'] : 0;
                        $response_count = ( isset($item['pending']) ) ? $item['pending'] : 0;
                        $region_id_encrypted = "'" . Helpers_Utilities::encrypted_key($region, "encrypt") . "'";
                        //   $req_encrypted="'".Helpers_Utilities::encrypted_key($request_date_new,"encrypt") ."'";
                        if ($region == 11) {
                            $member_name_link = "------";
                        } else {
                            $member_name_link = '<a class="chagne-st-btn" href="javascript:regionbreakup(' . $region_id_encrypted . ',' . $request_date_new . ')" > View Detail </a>';
                        }
                        $row = array(
                            $region_name,
                            $request_date,
                            $request_count,
                            $response_count,
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

    /*     * Admin Reports Verisys response report */

    public function action_verisys_response_details() {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $_POST['date'] = date('Y-m-d', $_POST['date']);
            $region_encrypted = isset($_POST['region']) ? $_POST['region'] : 0;
            $region = Helpers_Utilities::encrypted_key($region_encrypted, 'decrypt');
//        echo '<pre>';
//        print_r($_POST); exit;
            // $district_request_total = Helpers_Adminreports::get_verisys_response_breakup($district,$date);
            $html = '';
            $region_name = Helpers_Utilities::get_region($region);
            $district_data = Helpers_Utilities::get_region_district($region);
            $ps_data = Helpers_Utilities::get_region_police($region);
            $html .= '<tr>';
            $html .= '<td>';
            $html .= $region_name;
            $html .= '</td>';
            $html .= '<td>';
            $html .= "----";
            $html .= '</td>';
            $html .= '<td>';
            $html .= $_POST['date'];
            $html .= '</td>';
            $html .= '<td>';
            $html .= Helpers_Adminreports::get_verisys_response_breakup(0, $region, $_POST['date'], 0);
            // $html .= $district['district_id'];
            $html .= '</td>';
            $html .= '<td>';
            $html .= Helpers_Adminreports::get_verisys_response_breakup(0, $region, $_POST['date'], 1);
            $html .= '</td>';
            $html .= '</tr>';
            foreach ($district_data as $district) {
                $html .= '<tr>';
                $html .= '<td>';
                $html .= $region_name;
                $html .= '</td>';
                $html .= '<td>';
                $html .= $district['name'];
                $html .= '</td>';
                $html .= '<td>';
                $html .= $_POST['date'];
                $html .= '</td>';
                $html .= '<td>';
                $html .= Helpers_Adminreports::get_verisys_response_breakup(1, $district['district_id'], $_POST['date'], 0);
                // $html .= $district['district_id'];
                $html .= '</td>';
                $html .= '<td>';
                $html .= Helpers_Adminreports::get_verisys_response_breakup(1, $district['district_id'], $_POST['date'], 1);
                $html .= '</td>';
                $html .= '</tr>';
            }
            foreach ($ps_data as $ps) {
                $html .= '<tr>';
                $html .= '<td>';
                $html .= $region_name;
                $html .= '</td>';
                $html .= '<td>';
                $html .= 'Police station ' . $ps['name'];
                $html .= '</td>';
                $html .= '<td>';
                $html .= $_POST['date'];
                $html .= '</td>';
                $html .= '<td>';
                $html .= Helpers_Adminreports::get_verisys_response_breakup(2, $ps['id'], $_POST['date'], 0);
                $html .= '</td>';
                $html .= '<td>';
                $html .= Helpers_Adminreports::get_verisys_response_breakup(2, $ps['id'], $_POST['date'], 1);
                $html .= '</td>';
                $html .= '</tr>';
            }
            echo $html;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //blocked ip list
    public function action_password_reset() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            if (Helpers_Utilities::chek_role_access($this->role_id, 40) == 1) {
                if (Auth::instance()->logged_in()) {
                    $data = new Model_Adminreports();
                    $password_reset_requests = $data->password_reset_requests();
                    $view = View::factory('templates/user/adminreports/password_reset')
                            ->set('records', $password_reset_requests);
                    $this->template->content = $view;
                } else {
                    $this->template->content = View::factory('templates/user/access_denied');
                }
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $e) {
            $this->template->content = View::factory('templates/user/some_thing_went_wrong');
        }
    }

    //set temp password as users original password
    public function action_set_temp_password() {
        try {
            $id = (int) $this->request->param('id');
            $id = Helpers_Utilities::remove_injection($id);
            $model_reference = New Model_Adminreports();
            $query_response = $model_reference->set_temp_password($id);
            if ($query_response == 1) {
                echo 1;
            } else {
                echo '-2';
            }
        } catch (Exception $e) {
            echo '-2';
        }
    }

    //blocked ip list
    public function action_blocked_ip_list() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            if (Helpers_Utilities::chek_role_access($this->role_id, 39) == 1) {
                if (Auth::instance()->logged_in()) {
                    $data = new Model_Adminreports();
                    $blocked_ip_list = $data->blocked_ip_list();
                    $view = View::factory('templates/user/adminreports/blocked_ip_list')
                            ->set('records', $blocked_ip_list);
                    $this->template->content = $view;
                } else {
                    $this->template->content = View::factory('templates/user/access_denied');
                }
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $e) {
            $this->template->content = View::factory('templates/user/some_thing_went_wrong');
        }
    }

    //Menu Managment
    public function action_menu_managment() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            if ($permission == 1 || $permission == 5) {
                if (Auth::instance()->logged_in()) {
                    $data = new Model_Adminreports();
                    $blocked_ip_list = $data->blocked_ip_list();
                    $view = View::factory('templates/user/adminreports/menu_managment');
                    $this->template->content = $view;
                } else {
                    $this->template->content = View::factory('templates/user/access_denied');
                }
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $e) {
            $this->template->content = View::factory('templates/user/some_thing_went_wrong');
        }
    }

    //delete ip form blocked ip list
    public function action_delete_ip_from_blocked_ip_list() {
        try {
            $id = (int) $this->request->param('id');
            $id = Helpers_Utilities::remove_injection($id);
            $model_reference = New Model_Adminreports();
            $query_response = $model_reference->delete_ip_from_blocked_ip_list($id);
            if ($query_response == 1) {
                echo 1;
            } else {
                echo '-2';
            }
        } catch (Exception $e) {
            echo '-2';
        }
    }

    public function action_parse_text() {
        $insert = 0;
        $data = array();
        $txt_file = file_get_contents('slowlog/slow_log_last_50_lac.txt');
//        echo 'file red success';
//        exit;
        //query is on the lines after the timestamp
        //break up into lines and find the timestamp line
        $lines = array_filter(explode("# User", $txt_file));
        foreach ($lines as $k => $line) {
            try {
                if (preg_match("/SET timestamp=([0-9]*);\s(.*)\;/s", $line, $matches1)) {
                    $char_to_replace = array("`", "\n");
                    $data[$k]['query'] = str_replace($char_to_replace, '', $matches1[2]);
                } else {
                    $data[$k]['query'] = '';
                }
                $data[$k]['host'] = '';
                $data[$k]['time'] = '';
                $data[$k]['rows_sent'] = '';
                $data[$k]['rows_examined'] = '';
                $line_data = array_filter(explode("\n", $line));
                foreach ($line_data as $k2 => $value) {
                    //query time
                    preg_match("/Query_time: (([0-9]|\.)*)/", $value, $querytimematches); //final for query time
                    if (!empty($querytimematches)) {
                        $data[$k]['time'] = $querytimematches[1];
                    }
                    //host name
                    preg_match("/Host: .*@ (.*)/", $value, $hostmatch); //final for host name
                    if (!empty($hostmatch)) {
                        $chars = array("[", "]");
                        $data[$k]['host'] = str_replace($chars, "", $hostmatch[1]);
                    }
                    preg_match("/Rows_sent: (([0-9])*)/", $value, $Rows_sent_matches); //final for Rows sent
                    if (!empty($Rows_sent_matches)) {
                        $data[$k]['rows_sent'] = $Rows_sent_matches[1];
                    }
                    preg_match("/Rows_examined: (([0-9])*)/", $value, $Rows_examined_matches); //final for Rows sent
                    if (!empty($Rows_sent_matches)) {
                        $data[$k]['rows_examined'] = $Rows_examined_matches[1];
                    }
                }
            } catch (Exception $e) {
                echo '<pre>';
                print_r($e);
            }
        }
        foreach ($data as $value) {
            $insert = Helpers_Utilities::insert_slow_query_log_data($value);
            echo $insert . '</br>';
        }
    }

    public function action_menu_update() {
        try {
            if (Auth::instance()->logged_in()) {
                $_POST = Helpers_Utilities::remove_injection($_POST);
                $data = !empty($_POST['cb_id']) ? $_POST['cb_id'] : '';
                $data_array = explode(':', $data);
                $menu_id = !empty($data_array[0]) ? $data_array[0] : '';
                $role_id = !empty($data_array[1]) ? $data_array[1] : '';
                if (!empty($menu_id) && !empty($role_id)) {
                    $updae_menu = New Model_Adminreports();
                    $result = $updae_menu->menu_update($menu_id, $role_id);  
//                    echo '<pre>';
//                    print_r($result);
//                    exit;
                    echo 1;
                } else {
                    echo json_encode(2);
                }                
            }
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
            echo json_encode(9);
        }
    }

}
