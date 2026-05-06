<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Email extends Controller_Working
{

    public function __Construct(Request $request, Response $response)
    {

        parent::__construct($request, $response);
        $this->request = $request;
        $this->response = $response;
    }

    public function action_send()
    {
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();
            if ((isset($_POST)) && ($_POST != '')) {
                $_POST = Helpers_Utilities::remove_injection($_POST);
                $request_type = $_POST['ChooseTemplate'];

                $user_id = $user_obj->id;

                $status = 1;
                $processing = 0;
                $reason = !empty($_POST['inputreason']) ? $_POST['inputreason'] : '';
                $company_names = (!empty($_POST['company_name_get']) ? $_POST['company_name_get'] : '');
                $projectsids = (!empty($_POST['inputproject']) ? $_POST['inputproject'] : '');

                $date_current = date('d/M/Y'); //$date; 
                $date_current_dot = date('d.m.Y'); //$date; 

                $projectids = '';
                foreach ($projectsids as $projectid) {
                    if (sizeof($projectsids) <= 1) {
                        $projectids = $projectid;
                    } else {
                        $projectids = $projectids . "," . $projectid;
                    }
                }
                $requested_value = (isset($_POST['inputSubNO']) && !empty($_POST['inputSubNO']) ? $_POST['inputSubNO'] :
                    ((isset($_POST['inputCNIC']) && !empty($_POST['inputCNIC'])) ? $_POST['inputCNIC'] :
                        (isset($_POST['inputPTCLNO']) && !empty($_POST['inputPTCLNO']) ? $_POST['inputPTCLNO'] :
                            (isset($_POST['inputInternationalNo']) && !empty($_POST['inputInternationalNo']) ? $_POST['inputInternationalNo'] :
                                (isset($_POST['inputIMEI']) && !empty($_POST['inputIMEI']) ? $_POST['inputIMEI'] : '')))));

                $concerned_person_id = (!empty($_POST['person_id']) ? $_POST['person_id'] : '');
                $endDate = (!empty($_POST['endDate']) ? $_POST['endDate'] : '');
                $startDate = (!empty($_POST['startDate']) ? $_POST['startDate'] : '');
                $post_force_imei_last_digit_zero = (!empty($_POST['force_imei_last_digit_zero']) && $_POST['force_imei_last_digit_zero'] == 1) ? 1 : 0;

                // Optional "Requested Attachment" file the analyst picked
                // on the user-side request forms (CDR / Subscriber /
                // Location / CNIC-SIMs). Mirrors the admin attachment
                // pattern at Adminrequest::action_adminsend so we use the
                // same Upload::save call shape; only the destination
                // directory differs (REQUESTED_ATTACHMENTS, declared in
                // application/bootstrap.php). The path lands in
                // user_request.file_name via Model_Email::user_request.
                // Loop selects multiple companies — we save the file ONCE
                // and reference the same path on every per-company row so
                // analysts don't end up with duplicate copies on disk.
                $req_attachment = '';
                if (!empty($_FILES['rqtfile']) && !empty($_FILES['rqtfile']['name'])) {
                    if (!is_dir(REQUESTED_ATTACHMENTS)) {
                        @mkdir(REQUESTED_ATTACHMENTS, 0755, true);
                    }
                    $stamp    = date('YmdHis');
                    $ext      = pathinfo($_FILES['rqtfile']['name'], PATHINFO_EXTENSION);
                    $filename = 'rqtuser' . $stamp . '.' . $ext;
                    Upload::save($_FILES['rqtfile'], $filename, REQUESTED_ATTACHMENTS);
                    $req_attachment = REQUESTED_ATTACHMENTS . $filename;
                }

                foreach ($company_names as $company_name) {

                    try {
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

                                $to = Helpers_CompanyEmail::get_email_address(1, $request_type);
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

                                $to = Helpers_CompanyEmail::get_email_address(3, $request_type);
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

                                $to = Helpers_CompanyEmail::get_email_address(4, $request_type);
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

                                $to = Helpers_CompanyEmail::get_email_address(6, $request_type);
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

                                $to = Helpers_CompanyEmail::get_email_address(7, $request_type);
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

                                $to = Helpers_CompanyEmail::get_email_address(8, $request_type);
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
                                if ($request_type != 11) {
                                    //model call to update data in table 'Other numbers'
                                    $model = Model_Othernumber::update_other_numbers($_POST);
                                }

                                $to = Helpers_CompanyEmail::get_email_address(11, $request_type);
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
                                $model = Model_Othernumber::update_other_numbers($_POST);

                                $to = Helpers_CompanyEmail::get_email_address(12, $request_type);
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

                                $to = Helpers_CompanyEmail::get_email_address(13, $request_type);
                                break;
                        }


                        do {
                            if ($GLOBALS['id_generator'] == 0) {
                                $GLOBALS['id_generator'] = 1;
                                //Reference id to be sent to company
                                $reference_id = Helpers_Utilities::id_generator("reference_id");
                                $GLOBALS['id_generator'] = 0;
                            }
                        } while ($GLOBALS['id_generator'] == 1);

                        $reference_number = Model_Email::user_request($reference_id, $user_id, $request_type, $company_name, $status, $requested_value, $concerned_person_id, $projectids, $startDate, $endDate, $reason, ($company_name == 3 && $request_type == 2 && $post_force_imei_last_digit_zero == 1) ? 1 : 0, $req_attachment);

                        $template_data = Model_Email::get_email_tempalte($request_type, $company_name);

                        $subject = str_replace("[case_number]", $reference_id, $template_data['subject']);
                        if (!empty($_POST['inputSubNO'])) {
                            $subject = str_replace("[mobile_number]", $_POST['inputSubNO'], $subject);
                        }

                        $body = isset($_POST['inputSubNO']) ? str_replace("[mobile_number]", $_POST['inputSubNO'], $template_data['body_txt']) : $template_data['body_txt'];
                        $body = isset($_POST['inputCNIC']) ? str_replace("[cnic_number]", $_POST['inputCNIC'], $body) : $body;
                        $body = str_replace("[ptcl_number]", $requested_value, $body);
                        $body = str_replace("[case_number]", $reference_id, $body);

                        $body = str_replace("[start_date_dot]", $start_date_dot, $body);
                        $body = str_replace("[end_date_dot]", $end_date_dot, $body);
                        $body = str_replace("[start_date_slash]", $start_date_slash, $body);
                        $body = str_replace("[end_date_slash]", $end_date_slash, $body);
                        $body = str_replace("[start_date_slash_mdy]", $start_date_slash_mdy, $body);
                        $body = str_replace("[end_date_slash_mdy]", $end_date_slash_mdy, $body);
                        $body = str_replace("[start_date_hyphen]", $start_date_hyphen, $body);
                        $body = str_replace("[end_date_hyphen]", $end_date_hyphen, $body);

                        
						if ($company_name == 3 && $request_type == 2 && $post_force_imei_last_digit_zero == 1) {
							// Replace the [imei_number] token with version having 15th digit as 0
							if (isset($_POST['inputIMEI'])  && !empty($_POST['inputIMEI'])) {
								$imei_normalized = substr($_POST['inputIMEI'], 0, 14) . '0';
								$body = str_replace("[imei_number]", $imei_normalized, $body);
							}
						}else{
							$body = isset($_POST['inputIMEI']) ? str_replace("[imei_number]", $_POST['inputIMEI'], $body) : $body;
						}

                        if ($request_type == 10) {
                            $body = str_replace("[current_date]", $date_current_dot, $body);
                        } else {
                            $body = str_replace("[current_date]", $date_current, $body);
                        }

                        /* change in 10 23 2017 */
                        // $email_staus = Helpers_Email::send_email($to, $subject, $body);
                        /*
                          if ($email_staus == 1) {
                          $reference_number = Model_Email::email_sended($to, $subject, $body, $reference_number, 0, 1);
                          } else { */
                        $reference_number = Model_Email::email_sended($to, $subject, $body, $reference_number, 0, 0, $startDate, $endDate);
                        //}
                        // print_r($request_type);
                        // print_r($reference_number); exit;
                        $login_user = Auth::instance()->get_user();
                        $uid = $login_user->id;
                        Helpers_Profile::user_activity_log($uid, 10, $request_type, $requested_value, $concerned_person_id, $company_name);
                        if ($request_type == 4) {
                            Helpers_Person::fire_current_location();
                        }
                    } catch (Exception $e) {
                        echo json_encode(2);
                        exit;
                    }
                }
                echo json_encode(1);
                //$this->redirect($redirect_url);
            }
        }
    }


    // request nadra verisis
    public function action_requestnadraverisis()
    {
        //In case of nadra verisys no need for refence number.....
        $reference_id = 0;
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();
            if ((isset($_POST)) && ($_POST != '')) {
                $_POST = Helpers_Utilities::remove_injection($_POST);
                $request_type = 8;
                $user_id = $user_obj->id;
                $status = 1;
                $reason = !empty($_POST['inputreason']) ? $_POST['inputreason'] : '';
                $projectsids = (!empty($_POST['inputproject']) ? $_POST['inputproject'] : '');
                $projectids = '';
                foreach ($projectsids as $projectid) {
                    if (sizeof($projectsids) <= 1) {
                        $projectids = $projectid;
                    } else {
                        $projectids = $projectids . "," . $projectid;
                    }
                }
                try {
                    $requested_value = (!empty($_POST['cnic_number']) ? $_POST['cnic_number'] : '');
                    $company_name = 13;
                    $concerned_person_id = (!empty($_POST['person_id']) ? $_POST['person_id'] : '');
                    $endDate = 0;
                    $startDate = 0;
                    $reference_number = Model_Email::user_request($reference_id, $user_id, $request_type, $company_name, $status, $requested_value, $concerned_person_id, $projectids, $startDate, $endDate, $reason, 0, $req_attachment);
                    $to = 'Technical Support Team';
                    $subject = 'Nadra Verysis';
                    $body = 'This is local request for nadra verysis';
                    $reference_number = Model_Email::email_sended($to, $subject, $body, $reference_number, 0, 1);
                    $login_user = Auth::instance()->get_user();
                    $uid = $login_user->id;
                    Helpers_Profile::user_activity_log($uid, 10, $request_type, $requested_value, $concerned_person_id, $company_name);
                } catch (Exception $e) {
                    echo '<pre>';
                    print_r($e);
                    exit;
                    echo json_encode(2);
                    exit;
                    //$this->redirect('user/some_thing_went_wrong');
                }
                echo json_encode(1);
                exit;
                //$this->redirect('/userrequest/request_status/?message=1');
            }
        }
    }

    // request family tree
    public function action_requestfamilytree()
    {
        //In case of nadra verisys no need for refence number.....
        $reference_id = 0;
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();
            if ((isset($_POST)) && ($_POST != '')) {
                $_POST = Helpers_Utilities::remove_injection($_POST);
                $request_type = 10;
                $user_id = $user_obj->id;
                $status = 1;
                $reason = !empty($_POST['inputreason']) ? $_POST['inputreason'] : '';
                $projectsids = (!empty($_POST['inputproject']) ? $_POST['inputproject'] : '');
                $projectids = '';
                foreach ($projectsids as $projectid) {
                    if (sizeof($projectsids) <= 1) {
                        $projectids = $projectid;
                    } else {
                        $projectids = $projectids . "," . $projectid;
                    }
                }
                try {
                    $requested_value = (!empty($_POST['cnic_number']) ? $_POST['cnic_number'] : '');
                    $company_name = 13;
                    $concerned_person_id = (!empty($_POST['person_id']) ? $_POST['person_id'] : '');
                    $endDate = '0000-00-00';
                    $startDate = '0000-00-00';
                    $reference_number = Model_Email::user_request($reference_id, $user_id, $request_type, $company_name, $status, $requested_value, $concerned_person_id, $projectids, $startDate, $endDate, $reason, 0, $req_attachment);
                    $to = 'Technical Support Team';
                    $subject = 'Family Tree';
                    $body = 'This is local request for family tree';
                    $reference_number = Model_Email::email_sended($to, $subject, $body, $reference_number, 0, 1);
                    $login_user = Auth::instance()->get_user();
                    $uid = $login_user->id;
                    Helpers_Profile::user_activity_log($uid, 10, $request_type, $requested_value, $concerned_person_id, $company_name);
                } catch (Exception $e) {
                    echo json_encode(2);
                    exit;
                    //$this->redirect('user/some_thing_went_wrong');
                }
                echo json_encode(1);
                exit;
                //$this->redirect('/userrequest/request_status/?message=1');
            }
        }
    }

    // request nadra Travel History
    public function action_requesttravelhistory()
    {
        //In case of nadra verisys no need for refence number.....
        $reference_id = 0;
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();
            if ((isset($_POST)) && ($_POST != '')) {
                $_POST = Helpers_Utilities::remove_injection($_POST);
                $request_type = 12;
                $user_id = $user_obj->id;
                $status = 1;
                $reason = !empty($_POST['inputreason']) ? $_POST['inputreason'] : '';
                $projectsids = (!empty($_POST['inputproject']) ? $_POST['inputproject'] : '');
                $projectids = '';
                foreach ($projectsids as $projectid) {
                    if (sizeof($projectsids) <= 1) {
                        $projectids = $projectid;
                    } else {
                        $projectids = $projectids . "," . $projectid;
                    }
                }
                try {
                    $requested_value = (!empty($_POST['cnic_number']) ? $_POST['cnic_number'] : '');
                    $company_name = 14;
                    $concerned_person_id = (!empty($_POST['person_id']) ? $_POST['person_id'] : '');
                    $endDate = '';
                    $startDate = '';
                    $reference_number = Model_Email::user_request($reference_id, $user_id, $request_type, $company_name, $status, $requested_value, $concerned_person_id, $projectids, $startDate, $endDate, $reason, 0, $req_attachment);
                    $to = 'Technical Support Team';
                    $subject = 'Travel History';
                    $body = 'This is local request for Travel History';
                    $reference_number = Model_Email::email_sended($to, $subject, $body, $reference_number, 0, 1);
                    $login_user = Auth::instance()->get_user();
                    $uid = $login_user->id;
                    Helpers_Profile::user_activity_log($uid, 10, $request_type, $requested_value, $concerned_person_id, $company_name);
                } catch (Exception $e) {
                    echo json_encode(2);
                    exit;
                    //$this->redirect('user/some_thing_went_wrong');
                }
                echo json_encode(1);
                exit;
                //$this->redirect('/userrequest/request_status/?message=1');
            }
        }
    }

    // request nadra verisis
    public function action_requestbranchlessbanking()
    {
        //In case of nadra verisys no need for refence number.....
        $reference_id = 0;
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();
            if ((isset($_POST)) && ($_POST != '')) {
                $_POST = Helpers_Utilities::remove_injection($_POST);
                $request_type = 12;
                $user_id = !empty($user_obj->id) ? $user_obj->id : 0;
                $status = 1;
                $reason = !empty($_POST['inputreason']) ? $_POST['inputreason'] : '';
                $project_id = (!empty($_POST['inputproject']) ? $_POST['inputproject'] : '');
                $requested_value = (isset($_POST['inputSubNO']) && !empty($_POST['inputSubNO']) ? $_POST['inputSubNO'] :
                    ((isset($_POST['inputCNIC']) && !empty($_POST['inputCNIC'])) ? $_POST['inputCNIC'] : ''));
                $concerned_person_id = (!empty($_POST['person_id']) ? $_POST['person_id'] : '');
                $bank_ids = (!empty($_POST['banks_names']) ? $_POST['banks_names'] : '');
                do {
                    if ($GLOBALS['id_generator'] == 0) {
                        $GLOBALS['id_generator'] = 1;
                        //Reference id to be sent to company
                        $reference_id = Helpers_Utilities::id_generator("ctfu_reference_id");
                        $GLOBALS['id_generator'] = 0;
                    }
                } while ($GLOBALS['id_generator'] == 1);
                foreach ($bank_ids as $bank_id) {
                    $user_request_type_id = 12;
                    $reference_number = Model_Email::insert_request_ctfu($reference_id, $user_id, $request_type, $bank_id, $status, $requested_value, $concerned_person_id, $project_id, $reason);
                    //CTFU Request LOG
                    //Helpers_Profile::user_activity_log($uid, 10, $request_type, $requested_value, $concerned_person_id, $company_name);
                }
                echo json_encode(1);
                exit;
                //$this->redirect('/userrequest/request_status/?message=1');
            }
        }
    }

    public function action_index1()
    {
        try {
            /* Create gmail connection */
            $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
            $username = 'reg745964@gmail.com';
            $password = 'Pakistan92';
            $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());

            /* Fetch emails */
            $emails = imap_search($inbox, "ALL");

            /* If emails are returned, cycle through each... */
            if ($emails) {
                $output = '';

                /* Make the newest emails on top */
                rsort($emails);

                /* For each email... */
                foreach ($emails as $email_number) {

                    $headerInfo = imap_headerinfo($inbox, $email_number);
                    $structure = imap_fetchstructure($inbox, $email_number);

                    /* get information specific to this email */
                    $overview = imap_fetch_overview($inbox, $email_number, 0);

                    /* get mesage body */
                    //  $message = imap_qprint(imap_fetchbody($inbox, $email_number, 0));
                    // print_r($message);
                    $message = imap_fetchbody($inbox, $email_number, 2);
                    $message = quoted_printable_decode(imap_fetchbody($inbox, $email_number, 1));
                    $message = imap_fetchbody($inbox, $email_number, 1.2);

                    /*
                      // If attachment found use this one
                      // $message = imap_qprint(imap_fetchbody($inbox,$email_number,"1.2"));
                     */

                    $output .= 'Subject: ' . $overview[0]->subject . '<br />';
                    $output .= 'Body: ' . $message . '<br />';
                    $output .= 'From: ' . $overview[0]->from . '<br />';
                    $output .= 'Date: ' . $overview[0]->date . '<br />';
//$output .= 'CC: '.$headerInfo->ccaddress.'<br />';
//  Attachments
                    $attachments = array();
                    if (isset($structure->parts) && count($structure->parts)) {
                        for ($i = 0; $i < count($structure->parts); $i++) {
                            $attachments[$i] = array(
                                'is_attachment' => false,
                                'filename' => '',
                                'name' => '',
                                'attachment' => ''
                            );

                            if ($structure->parts[$i]->ifdparameters) {
                                foreach ($structure->parts[$i]->dparameters as $object) {
                                    if (strtolower($object->attribute) == 'filename') {
                                        $attachments[$i]['is_attachment'] = true;
                                        $attachments[$i]['filename'] = $object->value;
                                    }
                                }
                            }

                            if ($structure->parts[$i]->ifparameters) {
                                foreach ($structure->parts[$i]->parameters as $object) {
                                    if (strtolower($object->attribute) == 'name') {
                                        $attachments[$i]['is_attachment'] = true;
                                        $attachments[$i]['name'] = $object->value;
                                    }
                                }
                            }

                            if ($attachments[$i]['is_attachment']) {
                                $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i + 1);

                                /* 3 = BASE64 encoding */
                                if ($structure->parts[$i]->encoding == 3) {
                                    $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                                } /* 4 = QUOTED-PRINTABLE encoding */ elseif ($structure->parts[$i]->encoding == 4) {
                                    $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                                }
                            }
                        }
                    }

                    foreach ($attachments as $attachment) {
                        if ($attachment['is_attachment'] == 1) {
                            $filename = $attachment['name'];
                            if (empty($filename))
                                $filename = $attachment['filename'];
                            $file_path = 'uploads/'; //  Upload folder
                            $fp = fopen($file_path . $filename, "w+");
                            fwrite($fp, $attachment['attachment']);
                            fclose($fp);
                        }
                    }
//  Attachments

                    /* change the status */
                    $status = imap_setflag_full($inbox, $overview[0]->msgno, "\Seen \Flagged");

                    if ($overview[0]->subject == 'test mail attachment') {
                        echo 'match';
                        echo $filename;
                    }
                    exit;
                }

                echo $output;
            }

            /* close the connection */
            imap_close($inbox);
        } catch (Exception $e) {

        }
    }

    public function action_index()
    {
        try {
            // Use All Mail for comprehensive view (change back to INBOX if preferred)
            $hostname = '{imap.gmail.com:993/imap/ssl/novalidate-cert}[Gmail]/INBOX';
            $username = 'reg745964@gmail.com';
            $password = 'ftoqbqythasdpwqz';//'bfcihehizxazlphk';  // Confirm NO spaces!

            $inbox = imap_open($hostname, $username, $password);
            if (!$inbox) {
                throw new Exception('Connection failed: ' . imap_last_error());
            }

            // Smarter search: unseen + recent (change to 'ALL' only after testing)
            $criteria = 'UNSEEN SINCE "01-Jan-2026"';  // Or 'ALL' if you want everything
            $emails = imap_search($inbox, $criteria);

            $output = '<h2>Gmail Emails</h2>';

            if ($emails && is_array($emails)) {
                rsort($emails);  // Newest first
                $emails = array_slice($emails, 0, 10);  // Limit to 10 to avoid overload

                foreach ($emails as $email_number) {
                    $overview = imap_fetch_overview($inbox, $email_number, 0);
                    $structure = imap_fetchstructure($inbox, $email_number);

                    // Fetch body (prefer HTML if available, fallback plain)
                    $body_part = (isset($structure->parts) && !empty($structure->parts)) ? 2 : 1;
                    $body = imap_fetchbody($inbox, $email_number, $body_part);
                    $body = quoted_printable_decode($body);  // Common decoding

                    $output .= '<div style="border:1px solid #ccc; margin:10px; padding:10px;">';
                    $output .= '<strong>Subject:</strong> ' . htmlspecialchars($overview[0]->subject ?? '(No Subject)') . '<br>';
                    $output .= '<strong>From:</strong> ' . htmlspecialchars($overview[0]->from ?? '(Unknown)') . '<br>';
                    $output .= '<strong>Date:</strong> ' . htmlspecialchars($overview[0]->date ?? '(No Date)') . '<br>';
                    $output .= '<strong>Status:</strong> ' . ($overview[0]->seen ? 'Read' : 'Unread') . '<br><hr>';
                    $output .= '<div style="white-space: pre-wrap;">' . nl2br(htmlspecialchars(substr($body, 0, 500))) . '...</div>';
                    $output .= '</div>';
                }
            } else {
                $output .= '<p>No emails match the criteria (try changing search to "ALL" or check Gmail web for messages in this folder).</p>';
            }

            echo $output;
            imap_close($inbox);
        } catch (Exception $e) {
            echo '<div style="color:red; font-weight:bold;">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }

    /* telco email configuration */

    public function action_update_telcoemail_config()
    {
        if (Auth::instance()->logged_in()) {
            try {
                $user_obj = Auth::instance()->get_user();
                if ((isset($_POST)) && ($_POST != '')) {
                    $_POST = Helpers_Utilities::remove_injection($_POST);
                    $telcoemails = $_POST;
                    $telco = Model_Email::update_telco_emails($telcoemails);
                }
            } catch (Exception $e) {

            }
            $this->redirect('userrequest/request_schedular/');
        }
    }

}

// End Users Class
