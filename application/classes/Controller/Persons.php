<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_Persons extends Controller_Working
{

    public function action_test()
    {
        try {

            for ($i = 1; $i <= 1000; $i++) {

                $mail = new PHPMailer(); // create a new object
                $mail->IsSMTP(); // enable SMTP
                //   $mail->SMTPDebug = 2; // debugging: 1 = errors and messages, 2 = messages only
                $mail->SMTPAuth = true; // authentication enabled
                $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
                $mail->Host = "smtp.gmail.com";
                $mail->Port = 465; // or 587
                $mail->IsHTML(true);
                $mail->Username = "bandook57@gmail.com";
                $mail->Password = "password12345@";
                //$mail->SetFrom ("test@gmail.com");
                // $mail->From = "from@example.com";
                $mail->FromName = "Sajid Tiger";
                //$mail->setFrom('test@gmail.com', 'CTD Punjab');
                //$mail->Subject = "Test 2";
                //$mail->Body = "hello test";
                //$mail->AddAddress("test@gmail.com ");
                $mail->Subject = "testing total number of email " . $i;
                $mail->Body = "testing total number of email";
                $mail->AddAddress("bali26339@gmail.com");

                if (!$mail->Send()) {
                    //echo "Mailer Error: " . $mail->ErrorInfo;            
                    //return 2;
                    echo $i;
                } else {
                    //echo "Message has been sent";
                    echo $i;
                }
            }
        } catch (Exception $e) {

        }
    }

    public function __Construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->request = $request;
        $this->response = $response;
    }

    public function action_index()
    {
        $this->redirect('errors?_e=page_not_exist');
    }

    /*
     *  Person Dashboard (person_dashboard)
     */

    public function action_dashboard()
    {
        try {
            $_GET = Helpers_Utilities::remove_injection($_GET);

            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if (empty($pid) || $pid == 0) {
                $this->redirect(url::base() . 'errors?_e=wrong_parameters');
            }
            $login_user = Auth::instance()->get_user();

            /* check if id is related with sensitive user not open  */
            //$pid = Session::instance()->get('personid');
            // echo $pid; exit;
            if (!empty($pid)) {
                Session::instance()->set('personid', $pid);
            } else {
                $pid = Session::instance()->get('personid');
            }
            //  $access = Helpers_Person::sensitive_person_acl($login_user->id, $pid);

            $access = Helpers_Person::sensitive_person_acl($login_user->id, $pid);
            $access_view_person = Helpers_Profile::get_user_access_permission($login_user->id, 7);
            $access_message = '';

            if ($access == FALSE) {
                $access_message = 'Your Access To This Person Is Denied,Contact Administrator.';
            }
            if ($access_view_person == 0) {
                $access_message = 'Access denied, Contact your technical support team';
            }
        } catch (Exception $e) {
            $this->redirect(url::base() . 'errors?_e=wrong_parameters');
        }
        if (($access == TRUE) && ($access_view_person == 1)) {
            //to increase person search count/view count
            Helpers_Person::increament_person_view_count($pid);
            $login_user = Auth::instance()->get_user();
            $uid = $login_user->id;
            Helpers_Profile::user_activity_log($uid, 9, NULL, NULL, $pid);
            include 'persons_functions/dashboard.inc';
        } else {
            $user_obj = Auth::instance()->get_user();
            $userid = $user_obj->id;
            $persmission = Helpers_Utilities::get_user_permission($userid);
            if ($persmission == 2) {
                $this->redirect('user/access_denied');
                //$this->redirect('user/data_upload/?accessmessage=' . $access_message);
            } else {
                $this->redirect('user/access_denied');
                //$this->redirect('Userdashboard/dashboard/?accessmessage=' . $access_message);
            }
        }
    }

    /*
     *  Person Dashboard/Calls & SMS Log
     */

    public function action_callandsmslog()
    {
        try {
            //set Session for person id 
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");

            //$pid = Session::instance()->get('personid');
            $user_obj = Auth::instance()->get_user();
            $data = new Model_Persons;
            $rows = $data->get_person_calls_sms_summary($pid);
            $data = array();
            foreach ($rows as $row) {
                $data['month'][] = $row->month;
                $data['calls'][] = $row->calls;
                $data['sms'][] = $row->sms;
            }
            if (!empty($data)) {
                echo json_encode($data);
            } else {
                echo -1;
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*
     *  Person Last Location 
     */

    public function action_personlastlocation()
    {
        try {
            //set Session for person id 
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $data = Helpers_Person::get_person_last_location($pid);
            if (!empty($data)) {
                echo json_encode($data);
            } else {
                echo -1;
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Person Nadra Profile */

    public function action_nadra_profile()
    {
        try {
            //$post = $this->request->post();
            // $person_id = 265;
            // $this->auto_render = FALSE;
            if (Auth::instance()->logged_in()) {
                $_GET = Helpers_Utilities::remove_injection($_GET);
                $cnic = $_GET['cnic'];
                //$person_id = $_GET['pid'];
                $person_id = (int)Helpers_Utilities::encrypted_key($_GET['pid'], "decrypt");


                $user_obj = Auth::instance()->get_user();
                $uid = $user_obj->id;
                $region_id = Helpers_Profile::get_user_perofile($user_obj->id);
                $is_foreigner = !empty($person_id) ? Helpers_Utilities::check_is_foreigner($person_id) : -7;
                if (empty($is_foreigner)) {
                    $profnadra = Helpers_Person::get_person_nadra_perofile($person_id);

                    $nadra_status = isset($profnadra->person_nadra_status) ? $profnadra->person_nadra_status : 0;
                    if ($nadra_status == 0) {
                        try {


                            $request_rgid = $region_id->region_id;
                            $date = date('Y-m-01');
                            Helpers_Person::update_nadra_api_count($request_rgid, $date);

                            $arrContextOptions = array(
                                "ssl" => array(
                                    "verify_peer" => false,
                                    "verify_peer_name" => false,
                                ),
                            );

                            include 'user_functions/nadira_key.inc';
                            if (!empty($test_array['photograph'])) {
                                $imageData = base64_decode($test_array['photograph']);
                                //get person save data path
                                $person_save_data_path = !empty($person_id) ? Helpers_Person:: get_person_save_data_path($person_id) : '';

                                file_put_contents($person_save_data_path . 'nadra-image' . $test_array['citizen_number'] . '.gif', $imageData);
                            } else {
                                $imageData = '';
                            }
                        } catch (Exception $e) {
                            $test_array['message'] = 'NADRA Not Responding';
                            $this->redirect('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($person_id, "encrypt") . '&nadraerror=' . $test_array['message']);
                        }

                        if (!empty($test_array['code'])) {
                            if ($test_array['code'] == 100) {


                                $data = new Model_Personprofile;
                                $rows = $data->insert_nadra_info($test_array, $user_obj->id, $person_id);
                                $test_array['message'] = "Nadra Profile Updated";
                                $this->redirect('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($person_id, "encrypt") . '&nadraerror=' . $test_array['message']);
                            } else {
                                $this->redirect('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($person_id, "encrypt") . '&nadraerror=' . $test_array['message']);
                            }
                        } else {
                            $test_array['message'] = 'NADRA Not Responding';
                            $this->redirect('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($person_id, "encrypt") . '&nadraerror=' . $test_array['message']);
                        }
                    }
                } elseif (!empty($is_foreigner) && $is_foreigner != -7) {
                    //parameters
                    $search_type = 'foreigner_profile';
                    $search_value = $cnic;

                    //api call
                    if (!empty($search_type) && !empty($search_value)) {
                        if (empty($uid)) {
                            $uid = 9991;
                        }

                        //user activity type
                        Helpers_Profile::user_activity_log($uid, 82, $search_type, $search_value);

                        include 'user_functions/subscriber_api_key.inc';
                        $post = $test_array;
                        // print_r($test_array); exit;
                    }
                    //  $test_array= Helpers_Person::search_foreigner_details_with_cnic($cnic);
                    foreach ($post['data'] as $result) {
                        $foreigner_cnic = $result['cnic_number'];
                    }
                    if (!empty($foreigner_cnic)) {
                        foreach ($post['data'] as $result) {
                            $data = new Model_Personprofile;
                            $rows = $data->update_foreigner_profile($result, $uid, $person_id);
                        }
                        $test_array['message'] = "Foreigner Profile Updated";
                        $this->redirect('persons/dashboard?id=' . Helpers_Utilities::encrypted_key($person_id, "encrypt") . '&nadraerror="' . $test_array['message'] . '"');
                    } else {
                        $test_array['message'] = "No data found";
                        $this->redirect('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($person_id, "encrypt") . '&nadraerror=' . $test_array['message']);
                    }
                }
            } else {
                $this->redirect();
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*  Last 5 calls  */

    public function action_last_five_calls()
    {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);

            $pid = (int)Helpers_Utilities::encrypted_key($_POST['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $user_obj = Auth::instance()->get_user();
            $data = new Model_Persons;
//            echo '<pre>';
//            print_r($_POST);
//            exit();
            $type = $_POST['type'];
            if (!empty($type) || $type != NULL) {
                $map_data = $data->get_person_call_sms_location($_POST, $pid);
            } else {
                $map_data = $data->get_person_last_five_calls($pid);
            }
            // echo '<pre>';        print_r($map_data); exit;
            if (!empty($map_data)) {
                $arr_val = array();
                for ($i = 0; $i < sizeof($map_data['location']); $i++) {
                    $arr_val[] = [$map_data['location'][$i], $map_data['latitude'][$i], $map_data['longitude'][$i], $i];
                }
                echo json_encode($arr_val);
            } else {
                echo '-1';
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Graphical View Recent 5 calls */

    public function action_recent_five_calls()
    {
        try {
            //set Session for person id 
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $pid = (int)Helpers_Utilities::encrypted_key($_POST['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $user_obj = Auth::instance()->get_user();
            $data = new Model_Persons;
            $type = $_POST['type'];
            // print_r($type); exit;
            if (!empty($type) || $type != NULL) {
                $graphic_data = $data->get_person_cdr_graphic_calls($_POST, $pid);
                if (!empty($graphic_data)) {
                    $data = array();
                    $data['personnumber'] = $graphic_data['phone'][0];
                    for ($i = 0; $i < sizeof($graphic_data['phone']); $i++) {
                        switch ($type) {
                            case 'call':
                                //echo '<pre>';
                                //print_r($graphic_data);
                                $data['node'][]['data'] = array('id' => $graphic_data['phone'][$i], 'name' => $graphic_data['phone'][$i], 'weight' => 65, 'faveColor' => '#ff0000', 'faveShape' => 'octagon');
                                if ($graphic_data['calls_made'][$i] > 0 || $graphic_data['calls_received'][$i])
                                    $data['node'][]['data'] = array('id' => $graphic_data['ophone'][$i], 'name' => $graphic_data['ophone'][$i], 'weight' => 65, 'faveColor' => Helpers_Utilities::rand_color(), 'faveShape' => Helpers_Utilities::rand_shape());
                                //outgoing Calls edges
                                if ($graphic_data['calls_made'][$i] > 0)
                                    $data['edge'][]['data'] = array('source' => $graphic_data['phone'][$i], 'target' => $graphic_data['ophone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 100, 'name' => 'Calls:' . $graphic_data['calls_made'][$i]);
                                //incomming calls edges
                                if ($graphic_data['calls_received'][$i])
                                    $data['edge'][]['data'] = array('source' => $graphic_data['ophone'][$i], 'target' => $graphic_data['phone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 100, 'name' => 'Calls:' . $graphic_data['calls_received'][$i]);
                                break;
                            case 'sms':
                                $data['node'][]['data'] = array('id' => $graphic_data['phone'][$i], 'name' => $graphic_data['phone'][$i], 'weight' => 65, 'faveColor' => '#ff0000', 'faveShape' => 'octagon');
                                if ($graphic_data['sms_sent'][$i] > 0 || $graphic_data['sms_received'][$i] > 0)
                                    $data['node'][]['data'] = array('id' => $graphic_data['ophone'][$i], 'name' => $graphic_data['ophone'][$i], 'weight' => 65, 'faveColor' => Helpers_Utilities::rand_color(), 'faveShape' => Helpers_Utilities::rand_shape());
                                //outgoing sms edges
                                if ($graphic_data['sms_sent'][$i] > 0)
                                    $data['edge'][] = array('data' => array('source' => $graphic_data['phone'][$i], 'target' => $graphic_data['ophone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 60, 'name' => 'SMS:' . $graphic_data['sms_sent'][$i]), 'classes' => 'questionable');
                                //Incomming Sms edges
                                if ($graphic_data['sms_received'][$i] > 0)
                                    $data['edge'][] = array('data' => array('source' => $graphic_data['ophone'][$i], 'target' => $graphic_data['phone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 60, 'name' => 'SMS:' . $graphic_data['sms_received'][$i]), 'classes' => 'questionable');
                                break;
                            case 'callsms':
                                $data['node'][]['data'] = array('id' => $graphic_data['phone'][$i], 'name' => $graphic_data['phone'][$i], 'weight' => 65, 'faveColor' => '#ff0000', 'faveShape' => 'octagon');
                                if ($graphic_data['calls_made'][$i] > 0 || $graphic_data['calls_received'][$i] || $graphic_data['sms_sent'][$i] > 0 || $graphic_data['sms_received'][$i] > 0)
                                    $data['node'][]['data'] = array('id' => $graphic_data['ophone'][$i], 'name' => $graphic_data['ophone'][$i], 'weight' => 65, 'faveColor' => Helpers_Utilities::rand_color(), 'faveShape' => Helpers_Utilities::rand_shape());
                                //outgoing Calls edges
                                if ($graphic_data['calls_made'][$i] > 0)
                                    $data['edge'][]['data'] = array('source' => $graphic_data['phone'][$i], 'target' => $graphic_data['ophone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 100, 'name' => 'Calls:' . $graphic_data['calls_made'][$i]);
                                //incomming calls edges
                                if ($graphic_data['calls_received'][$i])
                                    $data['edge'][]['data'] = array('source' => $graphic_data['ophone'][$i], 'target' => $graphic_data['phone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 100, 'name' => 'Calls:' . $graphic_data['calls_received'][$i]);
                                //outgoing sms edges
                                if ($graphic_data['sms_sent'][$i] > 0)
                                    $data['edge'][] = array('data' => array('source' => $graphic_data['phone'][$i], 'target' => $graphic_data['ophone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 60, 'name' => 'SMS:' . $graphic_data['sms_sent'][$i]), 'classes' => 'questionable');
                                //Incomming Sms edges
                                if ($graphic_data['sms_received'][$i] > 0)
                                    $data['edge'][] = array('data' => array('source' => $graphic_data['ophone'][$i], 'target' => $graphic_data['phone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 60, 'name' => 'SMS:' . $graphic_data['sms_received'][$i]), 'classes' => 'questionable');
                                break;
                            case 'favfive':
                                $data['node'][]['data'] = array('id' => $graphic_data['phone'][$i], 'name' => $graphic_data['phone'][$i], 'weight' => 65, 'faveColor' => '#ff0000', 'faveShape' => 'octagon');
                                $data['node'][]['data'] = array('id' => $graphic_data['ophone'][$i], 'name' => $graphic_data['ophone'][$i], 'weight' => 65, 'faveColor' => Helpers_Utilities::rand_color(), 'faveShape' => Helpers_Utilities::rand_shape());
                                //outgoing Calls edges
                                if ($graphic_data['calls_made'][$i] > 0)
                                    $data['edge'][]['data'] = array('source' => $graphic_data['phone'][$i], 'target' => $graphic_data['ophone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 100, 'name' => 'Calls:' . $graphic_data['calls_made'][$i]);
                                //incomming calls edges
                                if ($graphic_data['calls_received'][$i])
                                    $data['edge'][]['data'] = array('source' => $graphic_data['ophone'][$i], 'target' => $graphic_data['phone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 100, 'name' => 'Calls:' . $graphic_data['calls_received'][$i]);
                                //outgoing sms edges
                                if ($graphic_data['sms_sent'][$i] > 0)
                                    $data['edge'][] = array('data' => array('source' => $graphic_data['phone'][$i], 'target' => $graphic_data['ophone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 60, 'name' => 'SMS:' . $graphic_data['sms_sent'][$i]), 'classes' => 'questionable');
                                //Incomming Sms edges
                                if ($graphic_data['sms_received'][$i] > 0)
                                    $data['edge'][] = array('data' => array('source' => $graphic_data['ophone'][$i], 'target' => $graphic_data['phone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 60, 'name' => 'SMS:' . $graphic_data['sms_received'][$i]), 'classes' => 'questionable');
                                break;
                            case 'linked':

                                $data['node'][]['data'] = array('id' => $graphic_data['phone'][$i], 'name' => $graphic_data['phone'][$i], 'weight' => 65, 'faveColor' => '#ff0000', 'faveShape' => 'octagon');
                                $data['node'][]['data'] = array('id' => $graphic_data['ophone'][$i], 'name' => $graphic_data['ophone'][$i], 'weight' => 65, 'faveColor' => Helpers_Utilities::rand_color(), 'faveShape' => Helpers_Utilities::rand_shape());
                                //outgoing Calls edges
                                if ($graphic_data['calls_made'][$i] > 0)
                                    $data['edge'][]['data'] = array('source' => $graphic_data['phone'][$i], 'target' => $graphic_data['ophone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 100, 'name' => 'Calls:' . $graphic_data['calls_made'][$i]);
                                //incomming calls edges
                                if ($graphic_data['calls_received'][$i] > 0)
                                    $data['edge'][]['data'] = array('source' => $graphic_data['ophone'][$i], 'target' => $graphic_data['phone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 100, 'name' => 'Calls:' . $graphic_data['calls_received'][$i]);
                                //outgoing sms edges
                                if ($graphic_data['sms_sent'][$i] > 0)
                                    $data['edge'][] = array('data' => array('source' => $graphic_data['phone'][$i], 'target' => $graphic_data['ophone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 60, 'name' => 'SMS:' . $graphic_data['sms_sent'][$i]), 'classes' => 'questionable');
                                //Incomming Sms edges
                                if ($graphic_data['sms_received'][$i] > 0)
                                    $data['edge'][] = array('data' => array('source' => $graphic_data['ophone'][$i], 'target' => $graphic_data['phone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 60, 'name' => 'SMS:' . $graphic_data['sms_received'][$i]), 'classes' => 'questionable');
                                break;
                        }
                    }
                    echo json_encode($data);
                } else {
                    echo '-1';
                }
            } else {
                $graphic_data = $data->get_person_recent_five_calls($pid);
                if (!empty($graphic_data)) {
                    $data = array();
                    for ($i = 0; $i < sizeof($graphic_data['phone']); $i++) {
                        $data['node'][]['data'] = array('id' => $graphic_data['phone'][$i], 'name' => $graphic_data['phone'][$i], 'weight' => 65, 'faveColor' => '#ff0000', 'faveShape' => 'octagon');
                        $data['node'][]['data'] = array('id' => $graphic_data['ophone'][$i], 'name' => $graphic_data['ophone'][$i], 'weight' => 65, 'faveColor' => Helpers_Utilities::rand_color(), 'faveShape' => Helpers_Utilities::rand_shape());
                        //outgoing Calls edges  
                        if ($graphic_data['calls_made'][$i] > 0)
                            $data['edge'][]['data'] = array('source' => $graphic_data['phone'][$i], 'target' => $graphic_data['ophone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 100, 'name' => 'Calls:' . $graphic_data['calls_made'][$i]);
                        //outgoing sms edges                    
                        if ($graphic_data['sms_sent'][$i] > 0)
                            $data['edge'][] = array('data' => array('source' => $graphic_data['phone'][$i], 'target' => $graphic_data['ophone'][$i], 'faveColor' => Helpers_Utilities::rand_color(), 'strength' => 60, 'name' => 'SMS:' . $graphic_data['sms_sent'][$i]), 'classes' => 'questionable');
                        $data['personnumber'] = $graphic_data['phone'][$i];
                    }
                    echo json_encode($data);
                } else {
                    echo '-1';
                }
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* TO get bparty of give person phone number */

    public function action_other_person_phone_number()
    {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            $phone_number = $_POST['phone'];
            if (!empty($_POST['otherphonenumbers'])) {
                $other_phone_number = explode(",", $_POST['otherphonenumbers']);
            } else {
                $other_phone_number = '';
            }
            $post = Session::instance()->get('cell_log_summary_post', array());
            $bparty_data = Helpers_Person::get_person_total_bparty($pid, $phone_number);
            $data = '';
            foreach ($bparty_data as $party) {
                $data .= '<option ' . ((!empty($other_phone_number) && in_array($party->ophone, $other_phone_number)) ? "selected" : '') . '  value="' . $party->ophone . '">' . $party->ophone . '</option>';
            }
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*
    *  Person Dashboard (call_summary)
    */

    public function action_call_summary()
    {
        try {
            $post = $this->request->post();
            if (isset($_GET)) {
                $post_data = array_merge($post, $_GET);
            }
            $post_data = Helpers_Utilities::remove_injection($post_data);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('call_summary_post', $post_data);
            //get Person id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                /* Export to excel file include */
                include 'excel/persons/call_summary.inc';
                //$pid = Session::instance()->get('personid');
                include 'persons_functions/call_summary.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /*
     *  Person Dashboard (call_summary)
     */

    public function action_ajaxcallsummary()
    {
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
                $post = Session::instance()->get('call_summary_post', array());
                //get person id from session
                // $pid = Session::instance()->get('personid');


                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['partya']) && !empty($post['partyb'])) {
                    $phone = $post['partya'];
                    $phone2 = $post['partyb'];
                } else {
                    $phone = NULL;
                    $phone2 = NULL;
                }

                $data = new Model_Persons;
                $rows_count = $data->call_summary($post, 'true', $pid, $phone, $phone2);

                $profiles = $data->call_summary($post, 'false', $pid, $phone, $phone2);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $phone_number = (isset($item['phone_number'])) ? $item['phone_number'] : 'NA';
                        $other_phone = (isset($item['other_person_phone_number'])) ? $item['other_person_phone_number'] : 'NA';

                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($other_phone);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $other_phone . ')">' . $other_phone . '</a>' : $other_phone;


                        $incomingcall = (isset($item['icalls'])) ? $item['icalls'] : 0;
                        $outgoingcall = (isset($item['ocalls'])) ? $item['ocalls'] : 0;
                        $totalcalls = $incomingcall + $outgoingcall;
                        $member_name_link = '<a href="' . URL::site('persons/call_summary_detail/?id=' . $_GET['id'] . '&partya=' . $item['phone_number'] . '&partyb=' . $item['other_person_phone_number']) . '" > View Detail </a>';


                        $row = array(
                            $phone_number,
                            $other_phone1.' '.$person_profile_link,
                            $incomingcall,
                            $outgoingcall,
                            $totalcalls,
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

    /* Person Dashboard (call_summary_detail) */

    public function action_call_summary_detail()
    {
        try {
            $post = $this->request->post();
//        if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//            $_GET['iDisplayStart']=0;                
//
//        }     
            if (isset($_GET)) {
                $post = array_merge($post, $_GET);
            }
            $post = Helpers_Utilities::remove_injection($post);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('call_summary_detail_post', $post);
            //get person id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            include 'persons_functions/call_summary_detail.inc';
        } catch (Exception $e) {

        }
    }

    /* Person Dashboard (call_summary_detail) */

    public function action_ajaxcallsummarydetail()
    {
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
                $post = Session::instance()->get('call_summary_detail_post', array());
                //irfan..
                // $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $phone = $post['partya'];
                $phone2 = $post['partyb'];
                //get person id from session
                // $pid = Session::instance()->get('personid');
                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                $data = new Model_Persons;
                $rows_count = $data->call_summary_detail($post, 'true', $pid, $phone, $phone2);

                $profiles = $data->call_summary_detail($post, 'false', $pid, $phone, $phone2);

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
                        $phone_number = (isset($item['phone_number'])) ? $item['phone_number'] : 'NA';
                        $other_phone = (isset($item['other_person_phone_number'])) ? $item['other_person_phone_number'] : 'NA';
                        $type = ($item['is_outgoing'] == 1) ? "outgoing" : 'incoming';
                        $duration = (isset($item['duration_in_seconds'])) ? $item['duration_in_seconds'] : 0;
                        $duration .= " Seconds ";
                        $datetime = (isset($item['call_at'])) ? $item['call_at'] : 'NA';
                        $location = (isset($item['address'])) ? $item['address'] : 'NA';

                        $row = array(
                            $phone_number,
                            $other_phone,
                            $type,
                            $duration,
                            $datetime,
                            $location,
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

    /* Person Dashboard (call_summary_detail) */

    public function action_sms_summary_detail()
    {
        try {
            $post = $this->request->post();
//        if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;
//            }
            if (isset($_GET)) {
                $post = array_merge($post, $_GET);
            }
            $post = Helpers_Utilities::remove_injection($post);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('sms_summary_detail_post', $post);
            //get person id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            include 'persons_functions/sms_summary_detail.inc';
        } catch (Exception $ex) {

        }
    }

    /* Person Dashboard (call_summary_detail) */

    public function action_ajaxsmssummarydetail()
    {
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
                $post = Session::instance()->get('sms_summary_detail_post', array());
                //irfan..
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $phone = $post['partya'];
                $phone2 = $post['partyb'];
                //get person id from session
                //   $pid = Session::instance()->get('personid');
                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                $data = new Model_Persons;
                $rows_count = $data->sms_summary_detail($post, 'true', $pid, $phone, $phone2);

                $profiles = $data->sms_summary_detail($post, 'false', $pid, $phone, $phone2);

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
                        $phone_number = (isset($item['phone_number'])) ? $item['phone_number'] : 'NA';
                        $other_phone = (isset($item['other_person_phone_number'])) ? $item['other_person_phone_number'] : 'NA';
                        $type = ($item['is_outgoing'] == 1) ? "outgoing" : 'incoming';
                        $datetime = (isset($item['sms_at'])) ? $item['sms_at'] : 'NA';
                        $location = (isset($item['address'])) ? $item['address'] : 'NA';

                        $row = array(
                            $phone_number,
                            $other_phone,
                            $type,
                            $datetime,
                            $location,
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

    //sms summary
    public function action_sms_summary()
    {
        try {
            $post = $this->request->post();
            if (isset($_GET)) {
                $post_data = array_merge($post, $_GET);
            }
            $post_data = Helpers_Utilities::remove_injection($post_data);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('sms_summary_post', $post_data);
            //print_r($post); exit;
            //get Person id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                /* Export to excel file include */
                include 'excel/persons/sms_summary.inc';
                include 'persons_functions/sms_summary.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /*
     *  Person Dashboard (call_summary)
     */

    //irfan
    public function action_ajaxsmssummary()
    {
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
                $post = Session::instance()->get('sms_summary_post', array());
                //get person id from session
                //  $pid = Session::instance()->get('personid');
                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                //refre
                if (!empty($post['partya']) && !empty($post['partyb'])) {
                    $phone = $post['partya'];
                    $phone2 = $post['partyb'];
                } else {
                    $phone = NULL;
                    $phone2 = NULL;
                }
                $data = new Model_Persons;
                $rows_count = $data->sms_summary($post, 'true', $pid, $phone, $phone2);
                $profiles = $data->sms_summary($post, 'false', $pid, $phone, $phone2);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $phone_number = (isset($item['phone_number'])) ? $item['phone_number'] : 'NA';
                        $other_phone = (isset($item['other_person_phone_number'])) ? $item['other_person_phone_number'] : 'NA';

                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($other_phone);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $other_phone . ')">' . $other_phone . '</a>' : $other_phone;


                        $incomingsms = (isset($item['sms_received_count'])) ? $item['sms_received_count'] : 'NA';
                        $outgoingsms = (isset($item['sms_sent_count'])) ? $item['sms_sent_count'] : 'NA';
                        $totalSMS = $incomingsms + $outgoingsms;
                        $member_name_link = '<a href="' . URL::site('persons/sms_summary_detail/?id=' . Helpers_Utilities::encrypted_key($pid, "encrypt") . '&partya=' . $item['phone_number'] . '&partyb=' . $item['other_person_phone_number']) . '" > View Detail </a>';

                        $row = array(
                            $phone_number,
                            $other_phone1.' '.$person_profile_link,
                            $incomingsms,
                            $outgoingsms,
                            $totalSMS,
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

    /*
     *  Person Dashboard (cdr_graphic)
     */

    public function action_cdr_graphic()
    {
        try {
            //get Person id from session
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('location_call_log_post', $post);
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
            include 'persons_functions/cdr_graphic.inc';
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /*
     *  Person Dashboard (cdr_report)
     */

    public function action_cdr_report()
    {
        try {
            include 'persons_functions/cdr_report.inc';
        } catch (Exception $e) {

        }
    }

    /*
     *  Person Dashboard (cdr_report_Detail)
     */

    public function action_cdr_report_Detail()
    {
        try {
            include 'persons_functions/cdr_report_Detail.inc';
        } catch (Exception $e) {

        }
    }

    /* Person Dashboard (cdr_summary) */

    public function action_cdr_summary()
    {
        try {
            $post = $this->request->post();
//        if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 
            if (isset($_GET)) {
                $post = array_merge($post, $_GET);
            }
            $post = Helpers_Utilities::remove_injection($post);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('cdr_summary_post', $post);
            //get Person id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                /* Export to excel file include */
                include 'excel/persons/cdr_summary.inc';
                include 'persons_functions/cdr_summary.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }
    /* Person Dashboard (bparty subscriber) */

    public function action_bparty_subscriber()
    {
        try {
            $post = $this->request->post();
//        if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;
//
//            }
            if (isset($_GET)) {
                $post = array_merge($post, $_GET);
            }
            $post = Helpers_Utilities::remove_injection($post);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('bparty_subscriber_post', $post);
            //get Person id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                /* Export to excel file include */
               // include 'excel/persons/cdr_summary.inc';
                include 'persons_functions/bparty_subscriber.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }
    /* Person Dashboard (short code analysis) */

    public function action_shortcode_analysis()
    {
        try {
            $post = $this->request->post();
            if (isset($_GET)) {
                $post = array_merge($post, $_GET);
            }
            $post = Helpers_Utilities::remove_injection($post);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('shortcode_analysis_post', $post);
            //get Person id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                /* Export to excel file include */
                include 'excel/persons/shortcode_analysis.inc';
                include 'persons_functions/shortcode_analysis.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit();
            $this->template->content = View::factory('templates/user/exception_error_page')

                ->bind('exception', $ex);
        }
    }   /* Person Dashboard (b_party) */

    public function action_b_party()
    {
        try {
            $post = $this->request->post();
//        if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;
//
//            }
            if (isset($_GET)) {
                $post = array_merge($post, $_GET);
            }
            $post = Helpers_Utilities::remove_injection($post);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('cdr_summary_post', $post);
            //get Person id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                /* Export to excel file include */
                include 'excel/persons/cdr_summary.inc';
                include 'persons_functions/b_party.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    //irfan cdr
    /* ajax call to cdr summary page */
    public function action_ajaxcdrsummary()
    {
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
                $post = Session::instance()->get('cdr_summary_post', array());
                //get person id from url
                // $pid = Session::instance()->get('personid');
                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                //rola
//            $phone_number = "";
//            if (!empty($post['phone_number'])) {
//                $phone_number = $post['phone_number']; 
//            }
//            print_r($phone_number);
//            exit;
                $data = new Model_Persons;
                $rows_count = $data->cdr_summary($post, 'true', $pid);
                $profiles = $data->cdr_summary($post, 'false', $pid);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                // edit done here
                if (isset($profiles) && sizeof($profiles) > 0) {
                    // $i = 1 ;
                    foreach ($profiles as $item) {
                        $phone_number = (isset($item['phone_number'])) ? $item['phone_number'] : 'NA';
                        $other_phone = (isset($item['other_person_phone_number'])) ? $item['other_person_phone_number'] : 'NA';

                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($other_phone);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $other_phone . ')">' . $other_phone . '</a>' : $other_phone;

                        $incomingsms = (isset($item['sms_received_count'])) ? $item['sms_received_count'] : 0;
                        $outgoingsms = (isset($item['sms_sent_count'])) ? $item['sms_sent_count'] : 0;
                        $totalSMS = $incomingsms + $outgoingsms;
                        $totalSMS .= " <div style='float:right'>[";
                        $totalSMS .= '<a href="' . URL::site('persons/sms_summary/?id=' . $_GET['id'] . '&partya=' . $item['phone_number'] . '&partyb=' . $item['other_person_phone_number']) . '" > View Detail </a>';
                        $totalSMS .= "]</div>   ";

                        $incomingcall = (isset($item['calls_received_count'])) ? $item['calls_received_count'] : 0;
                        $outgoingcall = (isset($item['calls_made_count'])) ? $item['calls_made_count'] : 0;

                        $totalcalls = $incomingcall + $outgoingcall;
                        $totalcalls .= " <div style='float:right'>[";
                        $totalcalls .= '<a href="' . URL::site('persons/call_summary/?id=' . $_GET['id'] . '&partya=' . $item['phone_number'] . '&partyb=' . $item['other_person_phone_number']) . '" > View Detail </a>';
                        $totalcalls .= "]</div>   ";


                        //ali

                        $row = array(
                            $phone_number,
                            $other_phone1.' '.$person_profile_link,
                            $totalSMS,
                            $totalcalls,
                        );

                        $output['aaData'][] = $row;
                        //$i++;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {

        }
    }
    /* ajax call to bparty subscriber page */
    public function action_ajaxbpartysubscriber()
    {
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
                $post = Session::instance()->get('bparty_subscriber_post', array());
                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                $uid = Auth::instance()->get_user()->id;
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Persons;
                $rows_count = $data->cdr_summary($post, 'true', $pid);
                $profiles = $data->cdr_summary($post, 'false', $pid);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                // edit done here
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $phone_number = (isset($item['phone_number'])) ? $item['phone_number'] : 'NA';
                        $other_phone = (isset($item['other_person_phone_number'])) ? $item['other_person_phone_number'] : 'NA';

                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($other_phone);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"> [View Profile]</a>' : ' ';
                       // $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $other_phone . ')">' . $other_phone . '</a>' : $other_phone;
                        $rst_resp=$other_phone;
                        
                        if(empty($person_profile_id)) {
                            
                            $search_type = 'msisdn';
                            $search_value = $other_phone;
                            include 'user_functions/subscriber_api_key.inc';
                            
                            if(!empty($test_array['data'])) {
                                //$rst_resp = '';
                                foreach ($test_array['data'] as $result) {
                                    if (!empty($result)) {
                                        if (!empty($result['ADDRESS1']) && $result['BVS']=='VERIFIED') {
                                            $rst_resp .= !empty($result['CNIC']) ? '<br><b> CNIC: '.$result['CNIC'] : 'NA';
                                            $rst_resp .= !empty($result['FIRSTNAME']) ? '<br><b> Name: '.$result['FIRSTNAME'] : 'NA';
                                            $address1 = !empty($result['ADDRESS1']) ? $result['ADDRESS1'] : '';
                                            $address2 = !empty($result['ADDRESS2']) ? $result['ADDRESS2'] : '';
                                            $address3 = !empty($result['ADDRESS3']) ? $result['ADDRESS3'] : '';
                                            $address4 = !empty($result['ADDRESS4']) ? $result['ADDRESS4'] : '';
                                            $resident_contact = !empty($result['RESCONTACT']) ? $result['RESCONTACT'] : '';
                                            $phone_office = !empty($result['PHONE_OFFICE']) ? $result['PHONE_OFFICE'] : '';
                                            $rst_resp .='<br><b>Address: </b>'. $address1 . " " . $address2 . " " . $address3 . " " . $address4 . ", Home#" . $resident_contact . ", Office#" . $phone_office;
                                            break;
                                        }
                                    }
                                }

                            }
                            else
                                $rst_resp=$other_phone.'<p style="color: red"><b>(Not Found)</b></p>';
                        }

                        $incomingsms = (isset($item['sms_received_count'])) ? $item['sms_received_count'] : 0;
                        $outgoingsms = (isset($item['sms_sent_count'])) ? $item['sms_sent_count'] : 0;
                        $totalSMS = $incomingsms + $outgoingsms;
                        $totalSMS .= " <div style='float:right'>[";
                        $totalSMS .= '<a href="' . URL::site('persons/sms_summary/?id=' . $_GET['id'] . '&partya=' . $item['phone_number'] . '&partyb=' . $item['other_person_phone_number']) . '" > View Detail </a>';
                        $totalSMS .= "]</div>   ";

                        $incomingcall = (isset($item['calls_received_count'])) ? $item['calls_received_count'] : 0;
                        $outgoingcall = (isset($item['calls_made_count'])) ? $item['calls_made_count'] : 0;

                        $totalcalls = $incomingcall + $outgoingcall;
                        $totalcalls .= " <div style='float:right'>[";
                        $totalcalls .= '<a href="' . URL::site('persons/call_summary/?id=' . $_GET['id'] . '&partya=' . $item['phone_number'] . '&partyb=' . $item['other_person_phone_number']) . '" > View Detail </a>';
                        $totalcalls .= "]</div>   ";


                        //ali

                        $row = array(
                            $phone_number,
                            $rst_resp.' '.$person_profile_link,
                            $totalSMS,
                            $totalcalls,
                        );

                        $output['aaData'][] = $row;
                        //$i++;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
           //echo '<pre>';print_r($ex); exit;
        }
    }
    /* ajax call to short code analysis page */
    public function action_ajax_shortcode_analysis()
    {

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
                $post = Session::instance()->get('shortcode_analysis_post', array());

                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_Persons;
                $rows_count = $data->short_code_analysis($post, 'true', $pid);
                $profiles = $data->short_code_analysis($post, 'false', $pid);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                // edit done here
                if (isset($profiles) && sizeof($profiles) > 0) {
                    // $i = 1 ;
                    foreach ($profiles as $result) {
//                        echo '<pre>';
//                        print_r($item);
//                        exit();
                        $company_name = (isset($result['company_name'])) ? $result['company_name'] : 'NA';
                     //   $code = (isset($item['code'])) ? $item['code'] : 'NA';
                        // helper \
                     //   $result = Helpers_Person::shortcode_count($code,$pid);
//                        SELECT * , (sms_received_count + sms_sent_count) as tsms,
//                                                     (calls_received_count + calls_made_count) as tcalls
//                                            FROM person_summary
//                                            WHERE person_id=61518
//                                            and other_person_phone_number = 552

//                        echo '<pre>';
//                        print_r($result);
//                        exit();
                        $phone_number = (isset($result['phone_number'])) ? $result['phone_number'] : 0;
                        $other_person_phone_number = (isset($result['other_person_phone_number'])) ? $result['other_person_phone_number'] : 0;
                        $code=$other_person_phone_number;
                        $incomingsms = (isset($result['sms_received_count'])) ? $result['sms_received_count'] : 0;
                        $outgoingsms = (isset($result['sms_sent_count'])) ? $result['sms_sent_count'] : 0;
                        $totalSMS = $incomingsms + $outgoingsms;
                        if(!empty($totalSMS)) {
                            $totalSMS .= " <div style='float:right'>[";
                            $totalSMS .= '<a href="' . URL::site('persons/sms_summary/?id=' . $_GET['id'] . '&partya=' . $phone_number . '&partyb=' . $other_person_phone_number) . '" > View Detail </a>';
                            $totalSMS .= "]</div>   ";
                        }else{
                            $totalSMS=0;
                        }

                        $incomingcall = (isset($result['calls_received_count'])) ? $result['calls_received_count'] : 0;
                        $outgoingcall = (isset($result['calls_made_count'])) ? $result['calls_made_count'] : 0;

                        $totalcalls = $incomingcall + $outgoingcall;
                        if(!empty($totalcalls)) {
                            $totalcalls .= " <div style='float:right'>[";
                            $totalcalls .= '<a href="' . URL::site('persons/call_summary/?id=' . $_GET['id'] . '&partya=' . $phone_number . '&partyb=' . $other_person_phone_number) . '" > View Detail </a>';
                            $totalcalls .= "]</div>   ";
                        }else{
                            $totalcalls=0;
                        }

                        $row = array(
                            $company_name,
                            $code,
//                            $phone_number,
//                            $other_phone1.' '.$person_profile_link,
                            $totalSMS,
                            $totalcalls,
                        );

                        $output['aaData'][] = $row;
                        //$i++;
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

    /* ajax call to b party page */
    public function action_ajax_b_party()
    {
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
                $post = Session::instance()->get('cdr_summary_post', array());
                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Persons;
                $rows_count = $data->b_party($post, 'true', $pid);
                $profiles = $data->b_party($post, 'false', $pid);
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                // edit done here
                if (isset($profiles) && sizeof($profiles) > 0) {
                    // $i = 1 ;
                    foreach ($profiles as $item) {

                        //   $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 'NA';
                        $other_phone = (isset($item['other_person_phone_number'])) ? $item['other_person_phone_number'] : 'NA';
                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($other_phone);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $other_phone . ')">' . $other_phone . '</a>' : $other_phone;
                        $incomingsms = (isset($item['sms_received_count'])) ? $item['sms_received_count'] : 0;
                        $outgoingsms = (isset($item['sms_sent_count'])) ? $item['sms_sent_count'] : 0;
                        $totalSMS = $incomingsms + $outgoingsms;
                        $totalSMS .= " <div style='float:right'>[";
                        $totalSMS .= '<a href="' . URL::site('persons/sms_summary/?id=' . $_GET['id'] . '&partya=' . $item['phone_number'] . '&partyb=' . $item['other_person_phone_number']) . '" > View Detail </a>';
                        $totalSMS .= "]</div>   ";

                        $incomingcall = (isset($item['calls_received_count'])) ? $item['calls_received_count'] : 0;
                        $outgoingcall = (isset($item['calls_made_count'])) ? $item['calls_made_count'] : 0;

                        $totalcalls = $incomingcall + $outgoingcall;
                        $totalcalls .= " <div style='float:right'>[";
                        $totalcalls .= '<a href="' . URL::site('persons/call_summary/?id=' . $_GET['id'] . '&partya=' . $item['phone_number'] . '&partyb=' . $item['other_person_phone_number']) . '" > View Detail </a>';
                        $totalcalls .= "]</div>   ";
                        //ali
                        $row = array(
                            //  $phone_number,
                            $other_phone1 . '' . $person_profile_link,
                            $totalSMS,
                            $totalcalls,
                        );
                        $output['aaData'][] = $row;
                        //$i++;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {

        }
    }

    /*
     *  Person Dashboard (cell_log_summary)
     */

    public function action_cell_log_summary()
    {
        try {
            $post = $this->request->post();

            //get id from session
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $post = Helpers_Utilities::remove_injection($post);
            $post = array_merge($post, $_GET);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('cell_log_summary_post', $post);


            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {

                /* Export to excel file include */
                include 'excel/persons/cell_log_summary.inc';
                //$pid = Session::instance()->get('personid');
                include 'persons_functions/cell_log_summary.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /*
     *  Person Dashboard (cell_log_summary: ajax call for data)
     */

    public function action_ajaxcelllogsummary()
    {
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
                $post = Session::instance()->get('cell_log_summary_post', array());
                //get id from session
                // $pid = Session::instance()->get('personid');
                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                //this now
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                //print_r($post); exit;
                $data = new Model_Persons;
                $rows_count = $data->cell_log_summary($post, 'true', $pid);
                $profiles = $data->cell_log_summary($post, 'false', $pid);

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
                        $phone_number = (isset($item['phone_number'])) ? $item['phone_number'] : 'NA';
                        $other_phone = (isset($item['other_person_phone_number'])) ? $item['other_person_phone_number'] : 'NA';


                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($other_phone);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $other_phone . ')">' . $other_phone . '</a>' : $other_phone;


                        $type = ($item['is_outgoing'] == 1) ? "outgoing" : 'incoming';
                        $duration = (isset($item['duration_in_seconds'])) ? $item['duration_in_seconds'] : 'NA';
                        $duration .= "  Seconds";
                        $datetime = (isset($item['call_at'])) ? $item['call_at'] : 'NA';
                        $longitude = (isset($item['longitude'])) ? $item['longitude'] : 'NA';
                        $latitude = (isset($item['latitude'])) ? $item['latitude'] : 'NA';
                        $location = (isset($item['address'])) ? $item['address'] : 'NA';

                        $row = array(
                            $phone_number,
                            $other_phone1.' '.$person_profile_link,
                            $type,
                            $duration,
                            $datetime,
                            '<b>Longitude: </b>'.$longitude.'<br><b>Latitude: </b>'.$latitude,
                            $location,
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*
     *  Person Favourite Person
     */

    public function action_person_favourite_person()
    {
        try {
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $_GET = Helpers_Utilities::remove_injection($_GET);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('person_favourite_person_post', $post);
            //get id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                //excel export
                include 'excel/persons/person_favourite_person.inc';
                //$pid = Session::instance()->get('personid');
                include 'persons_functions/person_favourite_person.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /* Person Dashboard (Person Favourite Persons: ajax call for data) */

    public function action_ajaxpersonfavouriteperson()
    {
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
                $post = Session::instance()->get('person_favourite_person_post', array());
                //get id from session
                // $pid = Session::instance()->get('personid');
                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Persons;
                $rows_count = $data->person_favourite_person($post, 'true', $pid);
                $profiles = $data->person_favourite_person($post, 'false', $pid);

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
                        $other_phone = (isset($item['other_person_phone_number'])) ? $item['other_person_phone_number'] : 'NA';
                        $phone_person_id = (isset($item['other_id'])) ? $item['other_id'] : NULL;

                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($other_phone);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $other_phone . ')">' . $other_phone . '</a>' : $other_phone;


                        if (!empty($item['other_id'])) {
                            $other_name = Helpers_Person::get_person_name($phone_person_id);
                            $other_name .= ' [';
                            $other_name .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['other_id'], "encrypt")) . '" > View Detail </a>';
                            $other_name .= ' ]';
                            $other_father = Helpers_Person::get_person_father_name($phone_person_id);
                            $other_cnic = Helpers_Person::get_person_cnic($phone_person_id);
                            $viewlink = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['other_id'], "encrypt")) . '" > Update Subscriber</a>';
                        } else {
                            // $sub_data=Helpers_Person::search_subscriber_detail($other_phone_number);
                            //  print_r($sub_data); exit;
                            $other_name = !empty($sub_data['name']) ? $sub_data['name'] : '';
                            $other_father = '';
                            $other_cnic = !empty($sub_data['cnic']) ? $sub_data['cnic'] : '';
                            $viewlink = '<a title="Click To Search Subscriber"  href="#" onclick="external_search_model(' . $other_phone . ')"> Request Subscriber </a>';
                            //' . URL::site('userrequest/request/' . $item['other_person_phone_number']) . '
                        }
                        $totalcalls = (isset($item['calls'])) ? $item['calls'] : 0;
                        $totalsms = (isset($item['sms'])) ? $item['sms'] : 0;
                        $row = array(
                            $other_phone1.' '.$person_profile_link ,
                            $other_name,
                            $other_father,
                            $other_cnic,
                            $totalcalls,
                            $totalsms,
                            $viewlink,
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

    /*   Person Favourite Person */

    public function action_person_db_match()
    {
        try {
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $_GET = Helpers_Utilities::remove_injection($_GET);
            
            if(empty($post))
                $post['category']= 3;
            elseif(isset($post['category']) && !empty($post['category'])){
                $post['category'] = $post['category'];
            }elseif(!isset($post['category'])){
                $post['category']= 3;
            }
            
            
            
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('person_db_match_post', $post);
            //get id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            
            if ($pid != 0) {
                //excel export
                include 'excel/persons/person_db_match.inc';
                //$pid = Session::instance()->get('personid');
//                include 'persons_functions/person_favourite_person.inc';
                $this->template->content = View::factory('templates/persons/person_db_match')
                    ->set('search_post', $post)
                    ->bind('person_id', $pid);
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
//            echo '<pre>';
//            print_r($ex);
//            exit; 
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /* Person Dashboard (Person Favourite Persons: ajax call for data) */

    public function action_ajaxpersondbmatch()
    {
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
                $post = Session::instance()->get('person_db_match_post', array());
                //get id from session
                // $pid = Session::instance()->get('personid');
                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                
                if(!empty($post['phone_number']) && $post['phone_number']==3)
                {unset ($post['phone_number']);}
                $data = new Model_Persons;
                //$rows_count = $data->person_db_match($post, 'true', $pid);
                $result = $data->person_db_match($post, 'false', $pid);
                
                $rows_count = $result['count'];
                $profiles = $result['result'];
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
                        $phone_number = (isset($item['phone'])) ? $item['phone'] : ' ';
                        
//                        echo '<pre>';
//                        print_r($item);
//                        echo '<br>';
                        $other_phone = (isset($item['bparty'])) ? $item['bparty'] : '';
                        $phone_person_id = (isset($item['other_id'])) ? $item['other_id'] : NULL;
                        $other_name = '';
                        $other_father = '';
                        $other_cnic = '';
                        if (!empty($item['other_id'])) {
                            $other_name = Helpers_Person::get_person_name($phone_person_id);
                            $other_name .= ' [';
                            $other_name .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['other_id'], "encrypt")) . '" > View Profile </a>';
                            $other_name .= ' ]';
                            $other_father = Helpers_Person::get_person_father_name($phone_person_id);
                            $other_cnic = Helpers_Person::get_person_cnic($phone_person_id);
                        }
                        if ($other_father == 'Unknown') {
                            $other_father = '';
                        }
                        $address = (isset($item['address'])) ? '</br><b>Address:</b>' . $item['address'] : '';

                        $outgoing_calls = (isset($item['outgoing_calls'])) ? $item['outgoing_calls'] : 'N/A';
                        $incomming_calls = (isset($item['incoming_calls'])) ? $item['incoming_calls'] : 'N/A';
                        $outgoing_sms = (isset($item['outgoing_sms'])) ? $item['outgoing_sms'] : 'N/A';
                        $incomming_sms = (isset($item['incoming_sms'])) ? $item['incoming_sms'] : 'N/A';
                        $last_activities = Helpers_Person::get_last_activity($phone_number, $other_phone, $pid);
                        $last_2_activity = '';
                        if(!empty($last_activities))
                        foreach($last_activities as $key => $activity)
                        {
                            $last_2_activity .= '<b> Last Call </b> </br>';
                            $last_2_activity .= '<b> Duration: </b> '. $activity->duration_in_seconds;
                            $last_2_activity .= ' </br><b> Lat: </b> '. $activity->latitude;
                            $last_2_activity .= ' </br><b> Long: </b> '. $activity->longitude;
                            $last_2_activity .= ' </br><b> Address: </b> '. $activity->address;
                            $last_2_activity .= ' </br><b> Call Timing: </b> '. $activity->call_at . ' to ' . $activity->call_end_at;
                            
                        }    
                        $last_activities = Helpers_Person::get_last_activity_sms($phone_number, $other_phone, $pid);                        
                        if(!empty($last_activities))
                        foreach($last_activities as $key => $activity)
                        {
                            $last_2_activity .= '</br><b> Last SMS </b>';                            
                            $last_2_activity .= ' </br><b> Lat: </b> '. $activity->latitude;
                            $last_2_activity .= ' </br><b> Long: </b> '. $activity->longitude;
                            $last_2_activity .= ' </br><b> Address: </b> '. $activity->address;
                            $last_2_activity .= ' </br><b> Call Timing: </b> '. $activity->sms_at;
                            
                        }
                        
                         $url_path = "http://www.ims.ctdpunjab.com/frontcat/pid?cnic=" . (int)trim($other_cnic);                                            
                                            $url = file_get_contents($url_path);
                                            $rst = !empty($url)? $url:'Not Found';
                         $wms = ' <b>WMS PID: </b>'.$rst;                   
                        $row = array(
                            $phone_number,
                            $other_phone,
                            '<b>Name: </b>'.$other_name.'<br>'.'<b>Father/Husband Name: </b>'
                            .$other_father.'<br>'.'<b>CNIC: </b>'.$other_cnic .$wms .$address,

                            $incomming_calls,
                            $outgoing_calls,
                            $incomming_sms,
                            $outgoing_sms,
                            $last_2_activity,
                        );

                        $output['aaData'][] = $row;
                    }
//                    exit;
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
     *  Person Favourite Person
     */

    public function action_person_affiliation()
    {
        try {
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $_GET = Helpers_Utilities::remove_injection($_GET);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('person_affiliation_post', $post);
            //get id from url
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                //$pid = Session::instance()->get('personid');
                include 'persons_functions/person_affiliation.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /*
     *  Person Dashboard (Person Favourite Persons: ajax call for data)
     */

    public function action_ajaxpersonaffiliation()
    {
        try {
            //echo 'hello'; exit;
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('person_affiliation_post', array());
//            echo '<pre>';
//            print_r($post); exit;
                //get id from URL
                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Persons;
                $rows_count = $data->person_affiliation($post, 'true', $pid);
                $profiles = $data->person_affiliation($post, 'false', $pid);

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
                        $pid = (isset($item['pid'])) ? $item['pid'] : 0;
                        $phone_number = (isset($item['phone_number'])) ? $item['phone_number'] : 0;
                        $other_phone = (isset($item['other_person_phone_number'])) ? $item['other_person_phone_number'] : 'NA';
                        $phone_person_id = (isset($item['other_id'])) ? $item['other_id'] : NULL;
                        $project = '';
                        if (!empty($item['other_id'])) {
                            $other_name = Helpers_Person::get_person_name($phone_person_id);
                            $other_name .= ' [';
                            $other_name .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['other_id'], "encrypt")) . '" > View Detail </a>';
                            $other_name .= ' ]';
                            $other_cnic = Helpers_Person::get_person_cnic($phone_person_id);
                            $project_id = Helpers_Utilities::get_person_project_from_category($item['other_id']);
                            $project = Helpers_Utilities::get_project_names($project_id);
                        } else {
                            $other_name = 'N/A';
                            $other_cnic = 'N/A';
                        }
                        $totalcalls = Helpers_Person::get_person_total_calls_with_number($pid, $other_phone); //( isset($item['calls']) ) ? $item['calls'] : 0;
                        $totalsms = Helpers_Person::get_person_total_sms_with_number($pid, $other_phone); //( isset($item['calls']) ) ? $item['calls'] : 0;
                        $organization = (isset($item['org_id']) && $item['org_id'] != 0) ? Helpers_Utilities::get_banned_organizations_name($item['org_id']) : '-';
                        $row = array(
                            $phone_number,
                            $other_phone,
                            $other_name,
                            $other_cnic,
                            $totalcalls,
                            $totalsms,
                            $project,
                            $organization,
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
     *  Person Dashboard (family_tree)
     */

    public function action_family_tree()
    {
        try {
            include 'persons_functions/family_tree.inc';
        } catch (Exception $e) {

        }
    }

    /*
     *  Person Dashboard (location_call_log)
     */

    public function action_location_call_log()
    {
        try {
            //get Person id from session
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('location_call_log_post', $post);
                /* Export to excel file include */
                include 'excel/persons/location_call_log.inc';
                /* File Included */
                include 'persons_functions/location_call_log.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /* Person Dashboard physical location summary */

    public function action_physical_location_summary()
    {
        try {
            //get Person id from session
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('physical_location_summary_post', $post);
                /* Export to excel file include */
                include 'excel/persons/physical_location_summary.inc';
                /* File Included */
                $this->template->content = View::factory('templates/persons/physical_location_summary')
                    ->set('search_post', $post)
                    ->bind('person_id', $pid);
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /*
     *  Person Dashboard (cell_log_summary: ajax call for data)
     */

    public function action_ajaxphysicallocationsummary()
    {
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
                $post = Session::instance()->get('physical_location_summary_post', array());
                //get id from session
                // $pid = Session::instance()->get('personid');
                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                //this now
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                //print_r($post); exit;
                $data = new Model_Persons;
                $rows_count = $data->person_physical_location($post, 'true', $pid);
                $profiles = $data->person_physical_location($post, 'false', $pid);

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
                        $person_id = (isset($item['person_id'])) ? $item['person_id'] : 0;
                        $party_a = (isset($item['phone'])) ? $item['phone'] : 0;
                        $address = (isset($item['address'])) ? $item['address'] : '--';
                        $longitude = (isset($item['longitude'])) ? $item['longitude'] : 'NA';
                        $latitude = (isset($item['latitude'])) ? $item['latitude'] : '--';
                        $address_count = (isset($item['loc_count'])) ? $item['loc_count'] : 0;

                        $row = array(
                            $party_a,
                            $address,
                            $latitude,
                            $longitude,
                            $address_count,
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            echo json_encode('Exception');
        }
    }

    /*
     *  Person Dashboard (location_datetime)
     */

    public function action_location_datetime()
    {
        try {
            include 'persons_functions/location_datetime.inc';
        } catch (Exception $e) {

        }
    }

    /*
     *  Person Dashboard (location_sms_log)
     */

    public function action_location_sms_log()
    {
        try {
            include 'persons_functions/location_sms_log.inc';
        } catch (Exception $e) {

        }
    }

    /*
     *  Person Dashboard (location_log_chart)
     */

    public function action_location_log_chart()
    {
        try {
            //$id = (int) $this->request->param('id');
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $_GET = Helpers_Utilities::remove_injection($_GET);

            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('location_call_log_post', $post);
            //get Person id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                /* Export to excel file include */
                // include 'excel/persons/sms_log_summary.inc';

                include 'persons_functions/location_log_chart.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    public function action_ajax_loc_chartt()
    {
        try {

            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");

            $row = Helpers_Person::location_chartt($pid);
            $compare = array();
            foreach ($row as $r) {
                $compare[] = $r;
            }
            echo json_encode($compare);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    public function action_ajax_loc_chartt2()
    {
        try {

            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");

            $row = Helpers_Person::location_chartt2($pid);
            $compare = array();
            foreach ($row as $r) {
                $compare[] = $r;
            }
            echo json_encode($compare);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    public function action_ajax_loc_chartt3()
    {
        try {

            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");

            $row = Helpers_Person::location_chartt3($pid);
            $compare = array();
            foreach ($row as $r) {
                $compare[] = $r;
            }
            echo json_encode($compare);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    public function action_ajax_loc_chartt1()
    {
        try {

            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");

            $row = Helpers_Person::location_chartt1($pid);
            $compare = array();
            foreach ($row as $r) {
                $compare[] = $r;
            }
            echo json_encode($compare);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*
     *  Person Dashboard (call_log_chart)
     */

    public function action_call_log_chart()
    {
        try {
            //$id = (int) $this->request->param('id');
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $_GET = Helpers_Utilities::remove_injection($_GET);

            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('cell_log_summary_post', $post);
            //get Person id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                /* Export to excel file include */
                // include 'excel/persons/sms_log_summary.inc';

                include 'persons_functions/call_log_chart.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    public function action_ajax_call_chart()
    {

        try {
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            $data = Helpers_Person::call_log_chart($pid);

            echo json_encode($data);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*
     *  Person Dashboard (sms_log_chart)
     */

    public function action_sms_log_chart()
    {
        try {
            //$id = (int) $this->request->param('id');
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $_GET = Helpers_Utilities::remove_injection($_GET);

            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('sms_log_summary_post', $post);
            //get Person id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                /* Export to excel file include */
                // include 'excel/persons/sms_log_summary.inc';

                include 'persons_functions/sms_log_chart.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }


    public function action_ajax_sms_chart()
    {

        try {
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            $data = Helpers_Person::sms_log_chart($pid);
            echo json_encode($data);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*
     *  Person Dashboard (sms_log_summary)
     */

    public function action_sms_log_summary()
    {
        try {
            //$id = (int) $this->request->param('id');
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $post = array_merge($post, $_GET);

            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('sms_log_summary_post', $post);
            //get Person id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                /* Export to excel file include */
                include 'excel/persons/sms_log_summary.inc';

                include 'persons_functions/sms_log_summary.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }
    /*
     *  Person Dashboard (location_log_summary)
     */

    public function action_location_log_summary()
    {
        try {
            //$id = (int) $this->request->param('id');
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $post = array_merge($post, $_GET);
//            echo '<pre>';
//            print_r($post);
//            exit();

            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('location_log_summary_post', $post);
            //get Person id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                /* Export to excel file include */
                include 'excel/persons/location_log_summary.inc';

                include 'persons_functions/location_log_summary.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /*
     *  Person Dashboard (sms_log_summary ajax function)
     */

    public function action_ajaxsmslogsummary()
    {
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
                $post = Session::instance()->get('sms_log_summary_post', array());
                //get id from url
                // $pid = Session::instance()->get('personid');
                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_Persons;
                $rows_count = $data->sms_log_summary($post, 'true', $pid);

                $profiles = $data->sms_log_summary($post, 'false', $pid);

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

                        // edit done here.... !!! 
                        $phone_number = (isset($item['phone_number'])) ? $item['phone_number'] : 'NA';
                        $other_phone = (isset($item['other_person_phone_number'])) ? $item['other_person_phone_number'] : 'NA';

                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($other_phone);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $other_phone . ')">' . $other_phone . '</a>' : $other_phone;


                        $type = ($item['is_outgoing'] == 1) ? "outgoing" : 'incoming';
                        //$duration= ( isset($item['duration_in_seconds']) ) ?  $item['duration_in_seconds'] : 'NA';
                        $datetime = (isset($item['sms_at'])) ? $item['sms_at'] : 'NA';
                        $location = (isset($item['address'])) ? $item['address'] : 'NA';

                        $row = array(
                            $phone_number,
                            $other_phone1.' '.$person_profile_link,
                            $type,
                            $datetime,
                            $location,
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
     *  Person Dashboard (location_log_summary ajax function)
     */

    public function action_ajaxlocation_log_summary()
    {
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
                $post = Session::instance()->get('location_log_summary_post', array());

                //get id from url
                // $pid = Session::instance()->get('personid');
                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;
//
//            }
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
//                echo '<pre>';
//                print_r($post);
//                exit();

                $data = new Model_Persons;
                $rows_count = $data->location_log_summary($post, 'true', $pid);

                $profiles = $data->location_log_summary($post, 'false', $pid);

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

                        // edit done here.... !!!
                        $phone_number = (isset($item['phone_number'])) ? $item['phone_number'] : 'NA';
                        $other_phone = (isset($item['other_person_phone_number'])) ? $item['other_person_phone_number'] : 'NA';

                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($other_phone);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $other_phone . ')">' . $other_phone . '</a>' : $other_phone;


                        $type = ($item['call'] ) ? $item['call'] : '';
                        //$duration= ( isset($item['duration_in_seconds']) ) ?  $item['duration_in_seconds'] : 'NA';
                        $datetime = (isset($item['time_t'])) ? $item['time_t'] : 'NA';
                        $location = (isset($item['address'])) ? $item['address'] : 'NA';

                        $row = array(
                            $phone_number,
                            $other_phone1.' '.$person_profile_link,
                            $type,
                            $datetime,
                            $location,
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

    /*   Person Dashboard (users_feedback) */

    public function action_users_feedback()
    {
        try {
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $_GET = Helpers_Utilities::remove_injection($_GET);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('users_feedback_post', $post);
            //get Person id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                //LOGIN user id 
                $login_user = Auth::instance()->get_user()->id;

                $data = new Model_Persons;
                $rows_count = $data->users_feedback('true', $pid);
                include 'persons_functions/users_feedback.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /*
     *  Person Dashboard (sms_log_summary ajax function)
     */

    public function action_ajaxusersfeedback()
    {
        try {
            $this->auto_rednder = false;
            if (Auth::instance()->logged_in()) {
                //LOGIN user id 
                $login_user = Auth::instance()->get_user()->id;
                //get id from url
                //$pid = Session::instance()->get('personid');
                $_GET = Helpers_Utilities::remove_injection($_GET);

                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                $data = new Model_Persons;
                $rows_count = $data->users_feedback('true', $pid);

                $profiles = $data->users_feedback('false', $pid);
                $html = '';
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        /* Concate name full name */

                        // edit done here.... !!! 
                        $userid = (isset($item['user_id'])) ? $item['user_id'] : 'NA';
                        //print_r($userid); exit;

                        $username = 'avatar';
                        $userimg = 'avatar5.png';
                        // $username = (isset(Helpers_Profile::get_user_perofile($userid)->first_name)) ? Helpers_Profile::get_user_perofile($userid)->first_name : "Unknown";
                        //$username .= " ";
                        // $username .= (isset(Helpers_Profile::get_user_perofile($userid)->last_name)) ? Helpers_Profile::get_user_perofile($userid)->last_name : "Name";
                        //print_r($username); exit;                    
                        $person_id = (isset($item['person_id'])) ? $item['person_id'] : 'NA';
                        $addeddate = (isset($item['added_on'])) ? $item['added_on'] : 'NA';
                        $feedback = (isset($item['feedback'])) ? $item['feedback'] : 'NA';
                        $user_first_letter = Helpers_Profile::get_first_letters($userid);


                        $login_user = Auth::instance()->get_user();
                        $permission = Helpers_Utilities::get_user_permission($login_user->id);
                        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
                        $posting = $login_user_profile->posted;
                        $result = explode('-', $posting);
                        if (($permission == 1) || ($login_user->id == $userid) || ($permission == 3 && $result[0] == 'h')) {
                            $username = Helpers_Utilities::get_user_name($userid);
                            $userimg = Helpers_Profile::get_user_image_by_id($userid);
                        }
                        if ($login_user == $userid) {
                            $html .= '  <div class="direct-chat-msg right">
                                            <div class="direct-chat-info clearfix">
                                                <span class="direct-chat-name pull-left">' . $username . '</span>
                                                <span class="direct-chat-timestamp pull-right">' . $addeddate . '</span>
                                            </div>     
                                            <img class="direct-chat-img" src="' . URL::base() . 'dist/uploads/user/profile_images/' . $userimg . '" alt="IMG">
                                            <div class="direct-chat-text">
                                                ' . $feedback . '
                                            </div>                                            
                                        </div>';
                        } else {
                            $html .= '<div class="direct-chat-msg">
                            <div class="direct-chat-info clearfix">                          
                                <span class="direct-chat-name pull-left">' . $username . '</span>
                                <span class="direct-chat-timestamp pull-right">' . $addeddate . '</span>
                            </div>                            
                            <img class="direct-chat-img" src="' . URL::base() . 'dist/uploads/user/profile_images/' . $userimg . '" alt="IMG">
                            <div class="direct-chat-text">
                                ' . $feedback . '
                            </div>                          
                        </div>';
                        }
                    }
                }
            }

//        echo json_encode($output);
            print $html;
            exit();
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*     *  Person Dashboard (one_page_performa)   */

    public function action_one_page_performa()
    {
        try {
            $person_data = array();
            $post = $this->request->post();
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $post = Helpers_Utilities::remove_injection($post);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('one_page_performa_post', $post);
            //get Person id from session
            $person_id = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");

            if ($person_id != 0) {
                //$pid = Session::instance()->get('personid');
                //LOGIN user id         
                $login_user = Auth::instance()->get_user()->id;
                $person_data['basicinfo'] = Helpers_Person::get_person_perofile($person_id);
                //sims data
                $mobile_number = isset($post['phone_number']) ? $post['phone_number'] : 0;
                $start_date = isset($post['startdate']) ? date("Y-m-d", strtotime($post['startdate'])) : '';
                $end_date = isset($post['enddate']) ? date("Y-m-d", strtotime($post['enddate'])) : '';
                $start_date_with_time = $start_date . ' 00:00:00';
                $end_date_with_time = $end_date . ' 23:59:59';
                if (!empty($mobile_number)) {
                    $person_data['siminfo'] = $mobile_number;
                    //devices data
                    $device_info = Helpers_Person::get_person_devices_one_pager($person_id, $mobile_number, $start_date_with_time, $end_date_with_time);
                    $person_data['deviceinfo'] = $device_info->as_array();
                    //person favourite persons
                    //ff
                    $favourite_person_data = Helpers_Person::person_fav_callers_one_pager($person_id, $mobile_number, $start_date_with_time, $end_date_with_time);
                    $person_data['favouriteperson'] = $favourite_person_data->as_array();
                    //Person DB Match data
                    $dbmatch_data = Helpers_Person::person_db_match_one_pager($person_id, $mobile_number, $start_date_with_time, $end_date_with_time);
                    $person_data['dbmatch'] = $dbmatch_data->as_array();
                    //person location history
                    $location_data = Helpers_Person::person_location_one_pager($person_id, $mobile_number, $start_date_with_time, $end_date_with_time);
                    $person_data['person_locations'] = $location_data->as_array();

                    //person Current location history
                    $current_location_data = Helpers_Person::person_current_location_one_pager($person_id, $mobile_number, $start_date_with_time, $end_date_with_time);
                    $person_data['person_current_location'] = $current_location_data->as_array();
                }
                include 'persons_functions/one_page_performa.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
            }
        } catch (Exception $ex) {
            if ($login_user == 136) {
                echo '<pre>';
                print_r($ex);
                exit;
            }
            echo json_encode(6);
        }
    }

    /* Change Person Status */

    public function action_change()
    {
        $e_person_id = '';
        try {
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $this->auto_render = FALSE;
            $user_id = Auth::instance()->get_user();
            $post['user_id'] = $user_id->id;
            $update = Model_Personsreports::update_person_status($post);
            $person_id = $post['person_id'];
            $e_person_id = Helpers_Utilities::encrypted_key($person_id, "encrypt");
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/some_thing_went_wrong');
        }
        $this->redirect('persons/dashboard/?id=' . $e_person_id);
    }

    /* Change Person Status */

    public function action_feedback()
    {
        try {
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $this->auto_render = FALSE;
            $update = Model_Personsreports::insert_users_feedback($post["uid"], $post["pid"], $post["messg"]);
            echo $update[0];
            exit;
        } catch (Exception $ex) {

        }
    }

    /* add user Favourite Person */

    public function action_addfavouriteperson()
    {
        try {
            if (Auth::instance()->logged_in()) {
                $user_obj = Auth::instance()->get_user();
                $login_user_id = $user_obj->id;
                $person_id = (int)$this->request->param('id');
                $person_id = Helpers_Utilities::remove_injection($person_id);
                $per = Helpers_Profile::get_user_access_permission($login_user_id, 4);
                if ($per == 0) {
                    echo -2;
                } else {
                    $addfav = New Model_Persons;
                    $result = $addfav->add_favouriteperson($person_id, $login_user_id);

                    return 0;
                }
            } else {
                return 0;
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Delete user Favourite Person */

    public function action_deletefavouriteperson()
    {
        try {
            if (Auth::instance()->logged_in()) {
                $user_obj = Auth::instance()->get_user();
                $login_user_id = $user_obj->id;
                $person_id = (int)$this->request->param('id');
                $person_id = Helpers_Utilities::remove_injection($person_id);
                $per = Helpers_Profile::get_user_access_permission($login_user_id, 5);
                if ($per == 0) {
                    echo -2;
                } else {
                    $addfav = New Model_Persons;
                    $result = $addfav->delete_favouriteperson($person_id, $login_user_id);
                    echo $result;
                }
            } else {
                return 0;
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* add user sensitive Person */

    public function action_addsensitveperson()
    {
        try {
            if (Auth::instance()->logged_in()) {
                $user_obj = Auth::instance()->get_user();
                $login_user_id = $user_obj->id;
                $person_id = (int)$this->request->param('id');
                $person_id = Helpers_Utilities::remove_injection($person_id);
                $addfav = New Model_Persons;
                $result = $addfav->add_sensitiveperson($person_id, $login_user_id);

                return 0;
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Delete user Sensitive Person */

    public function action_deletesensitiveperson()
    {
        try {
            if (Auth::instance()->logged_in()) {
                $user_obj = Auth::instance()->get_user();
                $login_user_id = $user_obj->id;
                $person_id = (int)$this->request->param('id');
                $person_id = Helpers_Utilities::remove_injection($person_id);
                $addfav = New Model_Persons;
                $result = $addfav->delete_sensitiveperson($person_id, $login_user_id);
                echo $result;
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*
     *  Sensitive Person ACL Data 
     */

    public function action_sensitiveperson_acl_data()
    {
        //$_POST['id']
        try {
            $model = New Model_Persons;
            $result = $model->get_person_acl_users();
            $login_user = Auth::instance()->get_user();
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $person_id = $_POST['id'];
            $data = "";
            $i = 0;
            $data .= '<input type="hidden" name="person-acl" value="' . $person_id . '">';
            if (!empty($result)) {

                foreach ($result as $item) {
                    /* Concate name full name */
                    $user_id = $item->id;
                    $full_name = $item->first_name;
                    $full_name .= ' ';
                    $full_name .= $item->last_name;
                    $designation = $item->job_title;
                    $posted = $item->posted;
                    $posting = Helpers_Profile::get_user_posting($posted);

                    $access_status = Helpers_Person::get_person_user_access($user_id, $_POST['id']);

                    $data .= '<tr>'
                        . '<input type="hidden" name="user-acl[' . $i . ']" value="' . $user_id . '">';
                    $data .= '<td>' . $full_name . '</td>';
                    $data .= '<td>' . $designation . '</td>';
                    $data .= '<td>' . $posting . '</td>';
                    $data .= '<td>';
                    $data .= '<div class="checkbox" style="margin-top:0px;margin-bottom: 0px;">';
                    $data .= '<label>';
                    $data .= '<input  ' . $access_status . ' name="user-acl-val[' . $i . ']" type="checkbox" data-toggle="toggle">';
                    $data .= '</label>';
                    $data .= '</div>';
                    $data .= '</td>';
                    $data .= '</tr>';
                    $i++;
                }
            }
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    public function action_sensitiveperson_acl_form()
    {
        try {
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
        } catch (Exception $e) {

        }
        if (!empty($post['person-acl'])) {
            try {
                $person_id = Helpers_Utilities::encrypted_key($post['person-acl'], "encrypt");
                $data = new Model_Persons;
                $rows_count = $data->sensitiveperson_acl_save($post);
            } catch (Exception $e) {

            }
            $this->redirect('persons/dashboard?id=' . $person_id);
        } else {
            $this->redirect();
        }
    }

    //person person_total_devices_details ajax    
    public function action_person_total_devices_details()
    {
        try {
            //set Session for person id 
            $_GET = Helpers_Utilities::remove_injection($_GET);

            $person_id = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            $dcount = 1;
            $totaldevices = Helpers_Person::get_person_devices($person_id);
            ?>
            <ul class="todo-list">
                <?php
                foreach ($totaldevices as $totaldevice) {
                    if ($dcount < 6) {
                        ?>
                        <li class="dashboard-sticky-danger">
                            <span class="text-black"> <b><?php echo $dcount . ":"; ?></b> <?php echo $totaldevice->phone_name; ?> </span><span
                                    class="text-black">(<?php echo $totaldevice->imei_number; ?>)<a href="#"
                                                                                                    onclick="requestimeicdr(<?php echo $totaldevice->imei_number . ',' . $person_id; ?>)"> <span
                                            class="label label-primary pull-right">Request CDR</span> </a></span>
                        </li>
                        <?php
                        $dcount++;
                    }
                }
                for ($i = 0; $i < 6 - $dcount; $i++) {
                    ?>
                    <li class="dashboard-sticky-danger">
                    </li>
                <?php }
                ?>
                <li class="dashboard-sticky-danger pull-right">
                    <a href="<?php echo URL::site('personsreports/person_devices/?id=' . $_GET['id']); ?>"> <span
                                class="text-orange hov"> <i class="fa fa-mobile-phone"></i> View Details</span> </a>
                </li>
            </ul>
            <?php
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //person person_link_with_projects ajax  last 5 records
    public function action_person_link_with_projects()
    {
        try {
            //set Session for person id 
            $_GET = Helpers_Utilities::remove_injection($_GET);

            $person_id = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");

            $last_five_link_with_projects = Helpers_Person::get_link_with_project_last_five($person_id);
            ?>
            <ul class="todo-list">
                <?php
                foreach ($last_five_link_with_projects as $item) {
                    $p_user_id = (!empty($item->user_id)) ? $item->user_id : -1;
                    if ($p_user_id != -1) {
                        $postingplace = Helpers_Profile::get_user_region_district($p_user_id);
                    } else {
                        $postingplace = 'NA';
                    }
                    $emailtypename = (!empty($item->email_type_name)) ? $item->email_type_name : 'NA';
                    $projects_ids = (!empty($item->project_id)) ? $item->project_id : 0;
                    $project_name = !empty($projects_ids) ? Helpers_Utilities::get_project_names($projects_ids) : "Unknown";
                    $project_region = !empty($projects_ids) ? Helpers_Utilities::get_project_region_name($projects_ids) : "Unknown";
                    $project_name .= ' [';
                    $project_name .= $project_region;
                    $project_name .= '] ';
                    ?>
                    <li class="dashboard-sticky-danger">
                        <span class="text-black"> <b><?php echo "*" . $postingplace; ?></b> </span>
                        <span class="text-black"> <b><?php echo "*" . $project_name; ?></b> </span>
                        <span class="text-black">    <?php echo $emailtypename; ?> </span>
                    </li>
                <?php } ?>
                <?php if ((($last_five_link_with_projects->count() != 0))) { ?>
                    <li class="dashboard-sticky-danger pull-right">
                        <span class="text-black"> <b><?php echo 'For More Details Visit CTD Suspect Profile'; ?></b> </span>
                    </li>
                <?php } ?>
            </ul>
            <?php
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //person person_device_count ajax    
    public function action_person_device_count()
    {
        try {
            //set Session for person id 
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $person_id = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            echo Helpers_Person::get_person_total_devices($person_id);
        } catch (Exception $ex) {
            //print_r($ex); exit;
            echo json_encode('-2');
        }
    }

    //person person_sims_count ajax    
    public function action_person_sims_count()
    {
        try {
            //set Session for person id 
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $person_id = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            echo Helpers_Person::get_person_SIMs($person_id);
        } catch (Exception $ex) {
            //print_r($ex); exit;
            echo json_encode('-2');
        }
    }

    //person person_total_sims_detail ajax    
    public function action_person_total_sims_detail()
    {
        $user_obj = Auth::instance()->get_user();
               $login_user_id = $user_obj->id;
        /*if($login_user_id != 137)        
        {   echo 'Team working on this section, please wait.'; exit;
        
        }*/
                
        try {
            //set Session for person id 
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $person_id = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            $totalsims = Helpers_Person::get_person_total_SIMs($person_id);
            $simscount = 1;
            ?>
            <ul class="todo-list">
                <?php
                foreach ($totalsims as $totalsim) {
                    if ($simscount < 6) {

                        $sim = $totalsim->phone_number;
                        $status = $totalsim->status;
                        $owner = "";
                        $simuser = "";
                        $sttitle = "";
                        $ownercolor = "";
                        if ($totalsim->person_id == $totalsim->sim_owner) {
                            $owner = $simscount . ": ";
                            $sttitle = "Own name sim is under personal use.";
                            $simuser = "";
                            $ownercolor = "green";
                        } elseif ($person_id == $totalsim->sim_owner && $person_id != $totalsim->person_id) {
                            $owner = $simscount . ": ";
                            $sttitle = "Own name sim is under another persons use click for details.";
                            $simuser = $totalsim->person_id;
                            $ownercolor = "warning";
                        } elseif ($person_id == $totalsim->person_id && $person_id != $totalsim->sim_owner) {
                            $owner = $simscount . ": ";
                            $sttitle = "Another person's sim is under personal use.";
                            $simuser = $totalsim->sim_owner;
                            $ownercolor = "danger";
                        }
                        ?>
                        <!--                        <span class="label label-<?php
                        if ($status == 1) {
                            $simstatuscolor = "success";
                        } elseif ($status == 0) {
                            $simstatuscolor = "danger";
                        } else {
                            $simstatuscolor = "warning";
                        }
                        ?> "><?php
                        if ($status == 1) {
                            $simstatus = "Active";
                        } elseif ($status == 0) {
                            $simstatus = "Inactive";
                        } else {
                            $simstatus = "NA";
                        }
                        ?></span>-->
                        <li class="dashboard-sticky-green">
                            <div style="padding-left: 2px !important; padding-right: 2px !important; "
                                 class="col-md-3 col-sm-12">
                                <?php if (!empty($simuser)) { ?>
                                    <span class="text-black"><b><?php echo $owner; ?></b><a
                                                class="text-<?php echo $ownercolor ?>"
                                                href="<?php echo URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($simuser, "encrypt")); ?>"
                                                title="<?php echo $sttitle . " and status of this sim is: " . $simstatus; ?>">  <?php echo $sim; ?> </a></span>
                                <?php } else { ?>
                                    <span title="<?php echo $sttitle . " and status of this sim is: " . $simstatus; ?>"
                                          class="text-black"><b><?php echo $owner; ?></b><a
                                                class="text-<?php echo $ownercolor ?> disabled" href="#"
                                                title="<?php echo $sttitle . " and status of this sim is: " . $simstatus; ?>">  <?php echo $sim; ?> </a></span>
                                <?php } ?>
                            </div>

                            <div style="padding-left: 2px !important; padding-right: 2px !important;"
                                 class="col-md-2 col-sm-12">
                                <?php /*
                                $data = [
                                           'mob' => $sim,
                                           'key_' => 'SZEhiAeCdhIJgQdcbqJc2td5tWZn4Xqu',

                                ];

                                $cURLConnection = curl_init('http://www.ctw.ctdpunjab.com/api/ctw_exist');
                                curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $data);
                                curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
                                $apiResponse = curl_exec($cURLConnection);
                                curl_close($cURLConnection);
                                $jsonArrayResponse = json_decode($apiResponse);
                                $ctw_res = $jsonArrayResponse ;

                                  if(!empty($ctw_res->url)){
                                       foreach ($ctw_res->url as $i=> $res) { ?>
                                <a href=" <?php echo $res ?>" target=”_blank”><span
                                            class="label label-warning">CTW</span></a>
                                            <?php }
                                   }else{
                                      echo ' ';
                                  }  */ ?>
                            </div>
                            <div style="padding-left: 2px !important; padding-right: 2px !important;"
                                 class="col-md-5 col-sm-12">
                                <span class="pull-right text-black" title="Click To Request Data From Company">
<!--                                    <a href="#" onclick="requestbranchlessbanking(<?php /* echo $sim . ',' . $person_id; */ ?>)"> <span class="label label-warning">Branchless Banking</span> </a>-->
                                    <a href="#" onclick="requestlocation(<?php echo $sim . ',' . $person_id; ?>)"> <span
                                                class="label label-primary">Location</span> </a>
                                    <a href="#" onclick="external_search_model(<?php echo $sim . ',0'; ?>)"><span
                                                class="label label-primary">Subscriber</span></a>
                                    <a href="#" onclick="requestcdr(<?php echo $sim . ',' . $person_id; ?>)"><span
                                                class="label label-primary">CDR</span></a><b></b>
                                </span>
                            </div>

                            <div style="padding-left: 2px !important; padding-right: 2px !important;"
                                 class="pull-right col-md-2 col-sm-12">
                                <?php
                                $cdr_status = Helpers_Person::get_person_cdr_status($person_id, $sim);
                                if ($cdr_status == 1) { ?>
                                    <a href="<?php echo URL::site('persons/cdr_summary/?phone_number=' . $sim . '&id=' . $_GET['id']); ?>">
                                        <span class="label label-success">View CDR</span> </a>
                                <?php } ?>
                            </div>
                        </li>

                        <?php
                        $simscount++;
                    }//end of if
                }

                for ($i = 0; $i < 6 - $simscount; $i++) {
                    ?>
                    <li class="dashboard-sticky-green">
                    </li>
                <?php }
                ?>
                <li class="dashboard-sticky-green pull-right">
                    <a href="<?php echo URL::site('personsreports/person_sims/?id=' . $_GET['id']); ?>"> <span
                                class="text-green hov"> <i class="fa fa-mobile-phone"></i> View Details</span> </a>
                </li>
            </ul>
            <?php
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //person current location history
    public function action_person_current_location_history()
    {
        try {
            //set Session for person id
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $person_id = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            $location = Helpers_Utilities::get_person_location_history($person_id);
            //print_r($location); exit;
            if (!empty($location)) {
                foreach ($location as $loc) {
                    ?>
                    <div class="col-md-12">
                        <span><strong>Mobile Number: </strong></span><span><?php echo $loc->phone_number; ?></span>
                    </div>
                    <div class="col-md-12">
                        <span><strong>Company Name: </strong></span><span><?php
                            if (!empty($loc->mnc)) {
                                $comname = Helpers_Utilities::get_companies_data($loc->mnc);
                                echo $comname->company_name;
                            } else {
                                echo "Unknown";
                            }
                            ?></span>
                        <span><strong>Network: </strong></span><span><?php echo Helpers_Utilities::get_network_status($loc->network); ?></span>
                    </div>
                    <div class="col-md-12">
                        <span><strong>LAC ID: </strong></span><span><?php echo !empty($loc->lac_id) ? (int)$loc->lac_id : 'N/A'; ?></span>

                        <span><strong>CELL ID: </strong></span><span><?php echo !empty($loc->cell_id) ? $loc->cell_id : 'N/A'; ?></span>
                    </div>
                    <div class="col-md-12">
                        <span><strong>LAT: </strong></span><span><?php echo $loc->latitude; ?></span>

                        <span><strong>LONG: </strong></span><span><?php echo $loc->longitude; ?></span>
                    </div>
                    <div class="col-md-12">
                        <span><strong>Address: </strong></span><span><?php echo $loc->address; ?></span>
                    </div>
                    <div class="col-md-12">
                        <span><strong>Location Time: </strong></span><span><?php echo $loc->moved_in_at; ?></span>
                    </div>
                    <div class="col-md-12">
                        <span><strong>Current Status: </strong></span><span><?php echo Helpers_Utilities::get_connection_status($loc->status); ?></span>
                    </div>
                    <div class="col-md-12">
                        <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
                    </div>
                    <?php
                }
            } else {
                ?>
                <img id="nodata" style="width: 534px; margin: auto" class="img-responsive"
                     src="<?php echo URL::base() . 'dist/img/noperson.png'; ?>" alt="No Data">
                <?php
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //person person_affiliations_and_social_links
    public function action_person_affiliations_and_social_links()
    {
        try {
            //set Session for person id 
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $person_id = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            ?>
            <div class="col-md-6">
                <div class="col-md-6">
                    <strong><span><i class="fa fa-list margin-r-5"></i>Affiliations</span></strong>
                </div>
                <div class="col-md-6 pull-right text-right">
                    <!--<a href="<?php //echo URL::site('personprofile/person_profile/?id=' . $_GET['id'] . '&tab=affiliations'); ?>"> <span class=""> <strong> View All</strong></span> </a>-->
                    <?php
                    $url = '<form id="id-aies" method="post" action="http://www.suspect.ctdpunjab.com/" target="_blank">
                            <input type="hidden" name="username" value="' . Auth::instance()->get_user()->username . '">
                            <input type="hidden" name="userid" value="' . Auth::instance()->get_user()->id . '">
                            <input type="hidden" name="password" value="' . Auth::instance()->get_user()->password . '">
                            <input type="hidden" name="personid" value="' . $_GET['id'] . '">
                            <input type="hidden" name="siteid" value="1">
                            <input type="hidden" name="smartuser" value="' . $_COOKIE['smartuser'] . '">
                            <button style="" class=" person-link ml-56  form-control btn btn-primary " type="submit">
                                                View Person</button>
                            </form>';
                    echo $url;
                    ?>
                </div>
                <div class="col-md-12 pull-right-2">
                    <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
                </div>
                <?php
                $countaffil = 0;
                $affiliation1 = Helpers_Person::get_person_affiliations($person_id);
                //print_r($affiliation1); exit;
                foreach ($affiliation1 as $affiliation) {
                    //print_r($affiliation1);                               exit();
                    $orgid = !empty($affiliation["org_id1"]) ? $affiliation["org_id1"] : 0;
                    if (!empty($orgid)) {
                        $orgname = Helpers_Utilities::get_banned_organizations_name($orgid);
                    } else {
                        $orgname = 'hidden name';
                    }
                    $countaffil = $countaffil + 1;
                    ?>
                    <div class="col-md-12" style="margin-bottom:  2px">
                        <span><i class="fa fa-check margin-r-2"></i><strong><?php echo $orgname; ?> </strong></span>
                    </div>
                    <?php
                }
                if ($countaffil == 0) {
                    ?>
                    <div class="col-md-12" style="margin-bottom:  2px">
                        <span><i class="fa fa-check margin-r-2"></i><strong> No Record Exist</strong></span>
                    </div>
                    <?php
                }
                ?>
                <div class="col-md-12 pull-right-2">
                    <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
                </div>
            </div>
            <div class=" col-md-6">
                <div class="col-md-6">
                    <strong><span><i class="fa fa-list margin-r-5"></i> Social Links</span></strong>
                </div>
                <div class="col-md-6 pull-right text-right">
                    <a href="<?php echo URL::site('socialanalysis/social_links/?id=' . $_GET['id']); ?>"> <span
                                class=""> <strong> View All</strong></span> </a>
                </div>
                <div class="col-md-12 pull-right-2">
                    <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
                </div>
                <?php
                $countsocial = 0;
                $sociallinks1 = Helpers_Person::get_person_social_links($person_id);
                foreach ($sociallinks1 as $sociallinks) {
                    // print_r($affiliation1);                               exit();
                    $websitename = !empty($sociallinks["website_name"]) ? $sociallinks["website_name"] : "";
                    $socialpersonid = !empty($sociallinks["person_sw_id"]) ? $sociallinks["person_sw_id"] : '';
                    $socialmobile = !empty($sociallinks["phone_number"]) ? $sociallinks["phone_number"] : "";
                    $countsocial = $countsocial + 1;
                    ?>
                    <div class="col-md-12" style="margin-bottom:  2px">
                        <span><i class="fa fa-check margin-r-2"></i><strong>  <?php echo $websitename; ?></strong>: <?php echo $socialpersonid . ", " . $socialmobile; ?> </span>
                    </div>
                    <?php
                }
                if ($countsocial == 0) {
                    ?>
                    <div class="col-md-12" style="margin-bottom:  2px">
                        <span><i class="fa fa-check margin-r-2"></i><strong> No Record Exist</strong></span>
                    </div>
                    <?php
                }
                ?>
                <div class="col-md-12 pull-right-2">
                    <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
                </div>
            </div>
            <?php
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //person person_other_numbers
    public function action_person_other_numbers()
    {
        try {
            //set Session for person id
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $person_id = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            ?>
            <div class="col-md-6">
                <div class="col-md-6">
                    <strong><span><i class="fa fa-list margin-r-5"></i>PTCL Numbers</span></strong>
                </div>
                <div class="col-md-12 pull-right-2">
                    <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
                </div>
                <?php
                $total_ptcl_numbers = Helpers_Person::get_person_total_ptcl_numbers($person_id);
                //
                //$total_ptcl_numbers_count = $total_ptcl_numbers->num_rows();
                $number_count = 1;
                // print_r($ptcl_number_count); exit;
                foreach ($total_ptcl_numbers as $ptcl_number) {
                    $ptcl_number = isset($ptcl_number->phone_number) ? $ptcl_number->phone_number : '';
                    ?>
                    <div class="col-md-12" style="margin-bottom:  2px">
                        <span><strong><?php echo $number_count . ': ' . $ptcl_number; ?> </strong></span>
                        <div class="pull-right">
                            <?php
                            $cdr_status = Helpers_Person::get_other_number_cdr_status($ptcl_number, 7);
                            if ($cdr_status == 1) {
                                ?>
                                <a href="<?php echo URL::site('Othernumbersearch/other_number_search/?ptclnumber=' . $ptcl_number . '&number_type=1'); ?>">
                                    <span class="label label-success">Old Requests Data</span> </a>
                                <?php
                            }
                            ?>
                            <span class="text-black" title="Click To Request Data From Company">                            
                                <a href="#"
                                   onclick="requestptclsubs(<?php echo $ptcl_number . ',' . $person_id; ?>)"><span
                                            class="label label-primary">Subscriber</span></a>
                                <a href="#"
                                   onclick="requestptclcdr(<?php echo $ptcl_number . ',' . $person_id; ?>)"><span
                                            class="label label-primary">CDR</span></a><b></b>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12 pull-right-2">
                        <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
                    </div>
                    <?php
                    $number_count++;
                }
                if ($number_count == 1) {
                    ?>
                    <div class="col-md-12" style="margin-bottom:  2px">
                        <span><i class="fa fa-check margin-r-2"></i><strong> No Record Exist</strong></span>
                    </div>
                    <div class="col-md-12 pull-right-2">
                        <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="col-md-6">
                <div class="col-md-8">
                    <strong><span><i class="fa fa-list margin-r-5"></i>International Numbers</span></strong>
                </div>
                <div class="col-md-12 pull-right-2">
                    <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
                </div>
                <?php
                try {
                    $total_international_numbers = Helpers_Person::get_person_total_international_numbers($person_id);
                } catch (Exception $ex) {

                }
                //
                //$total_ptcl_numbers_count = $total_ptcl_numbers->num_rows();
                $number_count = 1;
                // print_r($ptcl_number_count); exit;
                foreach ($total_international_numbers as $int_number) {
                    $number = isset($int_number->phone_number) ? $int_number->phone_number : '';
                    ?>
                    <div class="col-md-12" style="margin-bottom:  2px">
                        <span><strong><?php echo $number_count . ': ' . $number; ?> </strong></span>
                        <div class="pull-right">
                            <?php
                            $cdr_status = Helpers_Person::get_other_number_cdr_status($number, 9);
                            if ($cdr_status == 1) {
                                ?>
                                <a href="<?php echo URL::site('Othernumbersearch/other_number_search/?internationalnumber=' . $number . '&number_type=2'); ?>">
                                    <span class="label label-success">Old Requests Data</span> </a>
                                <?php
                            }
                            ?>
                            <span class="text-black" title="Click To Request Data From Company">                                                        
                                <a href="#" onclick="requestintercdr(<?php echo $number . ',' . $person_id; ?>)"><span
                                            class="label label-primary">Request CDR</span></a><b></b>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12 pull-right-2">
                        <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
                    </div>
                    <?php
                    $number_count++;
                }
                if ($number_count == 1) {
                    ?>
                    <div class="col-md-12" style="margin-bottom:  2px">
                        <span><i class="fa fa-check margin-r-2"></i><strong> No Record Exist</strong></span>
                    </div>
                    <div class="col-md-12 pull-right-2">
                        <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
                    </div>
                    <?php
                }
                ?>
            </div>

            <?php
        } catch (Exception $ex) {
            echo json_encode(-2);
        }
    }

    public function action_person_favourite_person_list()
    {
        try {
            //set Session for person id 
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $person_id = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            $favpersons = Helpers_Person::get_person_favourite_person($person_id);
            if (!empty($favpersons)) {
                foreach ($favpersons as $fav) {
                    $favno = $fav->other_person_phone_number;
                    $sms = $fav->sms;
                    $calls = $fav->calls;
                    $favpersonid = $fav->other_id;
                    ?>
                    <div class="col-md-2">
                        <strong><i class="fa fa-mobile margin-r-5"></i></strong>
                    </div>
                    <div class="col-md-4">
                        <strong> Contact On:</strong>
                    </div>
                    <div class="col-md-6 pull-right-3">
                        <strong> <?php echo $favno; ?> </strong>
                    </div>
                    <div class="col-md-2">
                        <strong><i class="fa fa-phone margin-r-5"></i> </strong>
                    </div>
                    <div class="col-md-4">
                        <strong> Total Calls:</strong>
                    </div>
                    <div class="col-md-6 pull-right-3">
                        <strong> <?php echo $calls; ?> </strong>
                    </div>
                    <div class="col-md-2">
                        <strong><i class="fa fa-envelope margin-r-5"></i> </strong>
                    </div>
                    <div class="col-md-4">
                        <strong> Total SMS:</strong>
                    </div>
                    <div class="col-md-6 pull-right-3">
                        <strong> <?php echo $sms; ?> </strong>
                    </div>

                    <div class="col-md-12 pull-right-5">
                        <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
                    </div>
                <?php }
                ?>
                <div class="col-md-12 pull-right-4">
                    <strong><a href="<?php echo URL::site('persons/person_favourite_person/?id=' . $_GET['id']); ?>"
                               class="center-text">View All</a></strong>
                </div>
            <?php } else { ?>
                <img id="nofavouriteperson" style="height: 187px" class="img-responsive"
                     src="<?php echo URL::base() . 'dist/img/noperson.png'; ?>" alt="No Data">
                <?php
            }
        } catch (Exception $ex) {
//             $login_user = Auth::instance()->get_user();
//            if($login_user->id == 136)                
//            {
//                                print_r($ex); exit;
//                                
//            }
            echo json_encode(-2);
        }
    }

    //person last call and sms    
    public function action_person_last_call_sms()
    {
        try {
            //set Session for person id 
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $person_id = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");

            $lcall = Helpers_Person::get_person_last_call($person_id);
            $last = isset($lcall->phone1) ? $lcall->phone1 : 0;
            $other = isset($lcall->other_person_phone_number) ? $lcall->other_person_phone_number : 0;
            $addre = isset($lcall->address) ? $lcall->address : "NA";
            $callat = isset($lcall->call_at) ? date("Y-m-d H:i:s", strtotime($lcall->call_at)) : 0;

            // $callat = date("Y-m-d H:i:s", strtotime($lcall->call_at ));

            $lsms = Helpers_Person::get_person_last_sms($person_id);
            $last1 = isset($lsms->phone1) ? $lsms->phone1 : 0;
            $other1 = isset($lsms->other_person_phone_number) ? $lsms->other_person_phone_number : 0;
            $addre1 = isset($lsms->address) ? $lsms->address : "NA";
            $smsat = isset($lsms->sms_at) ? $lsms->sms_at : 0;
            ?>
            <div class="col-md-1">
                <strong><i class="fa fa-phone margin-r-5"></i></strong>
            </div>
            <div class="col-md-4">
                <strong> Call From:</strong>
            </div>
            <div class="col-md-6 pull-right">
                <strong> <?php echo $last; ?> </strong>
            </div>
            <div class="col-md-12 pull-right">
                <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
            </div>
            <div class="col-md-1">
                <strong><i class="fa fa-mobile margin-r-5"></i> </strong>
            </div>
            <div class="col-md-4">
                <strong> Call To:</strong>
            </div>
            <div class="col-md-6 pull-right">
                <strong> <?php echo $other; ?> </strong>
            </div>
            <div class="col-md-12 pull-right-2">
                <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
            </div>
            <div class="col-md-1">
                <strong><i class="fa fa-clock-o margin-r-5"></i> </strong>
            </div>
            <div class="col-md-4">
                <strong> Call at:</strong>
            </div>
            <div class="col-md-6 pull-right">
                <strong> <?php echo $callat; ?> </strong>
            </div>
            <div class="col-md-12 pull-right">
                <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
            </div>
            <div class="col-md-1">
                <strong><i class="fa fa-envelope margin-r-5"></i></strong>
            </div>
            <div class="col-md-4">
                <strong> SMS From:</strong>
            </div>
            <div class="col-md-6 pull-right">
                <strong> <?php echo $last1; ?> </strong>
            </div>
            <div class="col-md-12 pull-right">
                <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
            </div>
            <div class="col-md-1">
                <strong><i class="fa  fa-envelope-o margin-r-5"></i> </strong>
            </div>
            <div class="col-md-4">
                <strong> SMS To:</strong>
            </div>
            <div class="col-md-6 pull-right">
                <strong> <?php echo $other1; ?> </strong>
            </div>
            <div class="col-md-12 pull-right-2">
                <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
            </div>
            <div class="col-md-1">
                <strong><i class="fa fa-clock-o margin-r-5"></i> </strong>
            </div>
            <div class="col-md-4">
                <strong> SMS at:</strong>
            </div>
            <div class="col-md-6 pull-right">
                <strong> <?php echo $smsat; ?> </strong>
            </div>
            <div class="col-md-12 pull-right">
                <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
            </div>
            <?php
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //person categories data
    public function action_person_tags()
    {
        // print_r($_POST); exit;
        if (Auth::instance()->logged_in()) {
            try {
                $_POST = Helpers_Utilities::remove_injection($_POST);
                $person_id = $_POST['person_id'];
                $add_tag = New Model_Persons;
                $result = $add_tag->add_person_tags($_POST);
                echo json_encode(1);
                //$this->redirect('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($person_id, "encrypt") . '&tag_message=1');
            } catch (Exception $ex) {
                echo json_encode(6);
                //exit;
            }
        } else {
            $this->redirect();
        }
    }

    /*
     *  Person Dashboard (sms_log_summary)
     */

    public function action_user_activity_log()
    {
        try {
            //$id = (int) $this->request->param('id');
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            //get Person id from url
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                $post['person_id'] = $pid;
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('user_activity_log_post', $post);
                include 'persons_functions/user_activity_log.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /*
     *  Person Dashboard (sms_log_summary ajax function)
     */

    public function action_ajaxuseractivitylog()
    {
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
                $post = Session::instance()->get('user_activity_log_post', array());
                //get id from url
                // $pid = Session::instance()->get('personid');
                // print_r($_GET); exit;
                // $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                //print_r($post['person_id']); exit;
                $data = new Model_Persons;
                $rows_count = $data->user_activity_log($post, 'true', $post['person_id']);

                $profiles = $data->user_activity_log($post, 'false', $post['person_id']);

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
                        $user_name = (isset($item['user_id'])) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                         $id_encrypted = "'" . Helpers_Utilities::encrypted_key($item['user_id'], "encrypt") . "'";
                        
                          $user_name = '<a target="_blank" class="btn btn-small action" href="' . URL::site('user/user_profile/' . $id_encrypted) . '"><i class="fa fa-folder-open-o"></i> '.$user_name.'</a>';
                        
                          
                        $designation = (isset($item['job_title'])) ? $item['job_title'] : 'NA';
                        $timeline_id = (isset($item['timeline_id'])) ? $item['timeline_id'] : 'NA';
                        $user_role_name = (isset($item['user_id'])) ? Helpers_Utilities::get_user_role_name($item['user_id']) : 'N/A';
                        $district = (isset($item['posted'])) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';
                        // $timeline_enc = "'" . Helpers_Utilities::encrypted_key($item['timeline_id'], 'encrypt') . "'";
                        if ($item['region_id'] == 0) {
                            $region = "Head Quarters";
                        } else {
                            $region = (isset($item['region_id'])) ? Helpers_Utilities::get_region($item['region_id']) : 'NA';
                        }
                        $activity_id = (isset($item['user_activity_type_id'])) ? $item['user_activity_type_id'] : 0;
                        switch ($activity_id) {
                            case 8:
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:searchdetail(' . $timeline_id . ')" > View Detail </a>';
                                $activity .= "]";
                                break;
                            case 66:
                            case 67:
                            case 68:
                            case 61:
                            case 60:
                            case 58:
                            case 57:
                            case 55:
                            case 53:
                            case 52:
                            case 50:
                            case 51:
                            case 64:
                            case 65:
                          
                            case 46:
                            case 45:
                            case 43:
                            case 42:
                            case 40:
                            case 39:
                            case 37:
                            case 36:
                            case 33:
                            case 34:
                            case 32:
                            case 31:
                            case 30:
                            case 12:
                            case 9 :
                            case 22:
                            case 23:
                            case 24:
                            case 25:
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';

                                break;
                            case 27:
                            case 28:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';

                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:project_details(' . $timeline_id . ')" > View Data </a>';
                                $activity .= "] ";
                                break;
                            case 10:
                          
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:requestdetail(' . $timeline_id . ')" > View Request Data </a>';
                                $activity .= "] ";
                                break;
                            case 11:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:categorydetail(' . $timeline_id . ')" > View Details </a>';
                                $activity .= "] ";
                                break;
                            case 26:
                            case 48:
                            case 77:
                            case 78:
                            case 79:
                            case 80:
                                  case 48:
                            case 49:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:activitydetail(' . $timeline_id . ')" > View Details </a>';
                                $activity .= "] ";
                                break;
                            case 35:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:identitydeletedetail(' . $timeline_id . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 38:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:educationdeletedetail(' . $timeline_id . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 41:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:incomesourcedeletedetail(' . $timeline_id . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 44:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:bankdetailsdeletedetail(' . $timeline_id . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 47:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:assetdeletedetail(' . $timeline_id . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 54:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:criminalrecorddeletedetail(' . $timeline_id . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 59:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:reportdeletedetail(' . $timeline_id . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 62:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:affiliationdeletedetail(' . $timeline_id . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 75:
                                //$member_name_link = '<a class="chagne-st-btn" href="javascript:acluser(' . $item['user_id'] . ')" > View Detail </a>';
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a class="chagne-st-btn" href="javascript:tagupdationdetail(' . $timeline_id . ')" > View Details </a>';
                                $activity .= "] ";
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 29:
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('user/user_profile/' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            case 19:
                            case 20:
                            case 21:
                            case 4:
                            case 5:
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                                $details_data = Helpers_Utilities::search_person_details($item['timeline_id']);
                                $new_user = !empty($details_data->user_id) ? $details_data->user_id : 0;
                                $activity .= " [";
                                $activity .= '<a href="' . URL::site('user/user_profile/' . Helpers_Utilities::encrypted_key($new_user, "encrypt")) . '" > View Profile </a>';
                                $activity .= "] ";
                                break;
                            default:
                                $activity = (isset($item['user_activity_type_id'])) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id']) : 'NA';
                        }
                        $datetime = (isset($item['activity_time'])) ? $item['activity_time'] : 'NA';
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


    /*
 *  Person Dashboard (branchless_transactions)
 */

    public function action_branchless_transactions()
    {
        try {
            //$id = (int) $this->request->param('id');
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $_GET = Helpers_Utilities::remove_injection($_GET);

            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('branchless_transactions_post', $post);
            //get Person id from session
            $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                /* Export to excel file include */
                include 'excel/persons/branchless_transactions.inc';

                $this->template->content = View::factory('templates/persons/branchless_transactions')
                    ->set('search_post', $post)
                    ->bind('person_id', $pid);
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    /*
     *  Person Dashboard (sms_log_summary ajax function)
     */

    public function action_ajaxblesstrans()
    {
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
                $post = Session::instance()->get('branchless_transactions_post', array());
                //get id from url
                // $pid = Session::instance()->get('personid');
                $pid = (int)Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_Persons;
                $rows_count = $data->branchless_transactions($post, 'true', $pid);

                $profiles = $data->branchless_transactions($post, 'false', $pid);

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

                        // edit done here.... !!! 
                        $phone_number = (isset($item['phone_number'])) ? $item['phone_number'] : 'NA';
                        $other_phone = (isset($item['other_person_phone_number'])) ? $item['other_person_phone_number'] : 0;
                        $data = Helpers_Utilities::get_company_branchless_transactions($other_phone);

                        $value = ' ';
                        $value .= ' [';
                        $value .= !empty($data['name_branchless_transation']) ? $data['name_branchless_transation'] : '-';
                        $value .= '] ';
                        $value .= '[';
                        $value .= !empty($data['bank_company_name']) ? $data['bank_company_name'] : '-';
                        $value .= ']';
                        $value = $other_phone . $value;

                        $type = ($item['is_outgoing'] == 1) ? "outgoing" : 'incoming';
                        //$duration= ( isset($item['duration_in_seconds']) ) ?  $item['duration_in_seconds'] : 'NA';
                        $datetime = (isset($item['sms_at'])) ? $item['sms_at'] : 'NA';
                        $location = (isset($item['address'])) ? $item['address'] : 'NA';

                        $row = array(
                            $phone_number,
                            $value,
                            $type,
                            $datetime,
                            $location,
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

// End Persons Class

