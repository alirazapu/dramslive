<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Admindatabank extends Controller_Working
{

    public function __Construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->request = $request;
        $this->response = $response;
    }


    //ajax call for data
    public function action_ajaxusernadrarequests_databank() {
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

    //bulk verisys respond
    public function action_bulk_nadra_requests_databank() {
         try {
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
         $uid=$login_user->id;
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('bulkresponse_post', $post);
            $this->template->content = View::factory('templates/user/nadra_requests_bulkresponse_databank')
                ->bind('user_id', $uid)
                ->set('search_post', $post);
        } else {
            $this->redirect('user/access_denied');
        }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/some_thing_went_wrong');
        }
    }

    //bulk verisys REports
    public function action_nadra_requests_reports_databank() {
         try {
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

         $uid=$login_user->id;

            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('bulkresponse_post', $post);
            $this->template->content = View::factory('templates/user/nadra_requests_bulkreports_databank')
                ->bind('user_id', $uid)
                ->set('search_post', $post);
        } else {
            $this->redirect('user/access_denied');
        }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/some_thing_went_wrong');
        }
    }
    //bulk msisdn REports
    public function action_msisdn_requests_reports_databank() {
         try {
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

         $uid=$login_user->id;

            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('msisdn_bulkresponse_post', $post);
            $this->template->content = View::factory('templates/user/msisdn_requests_bulkreports_databank')
                ->bind('user_id', $uid)
                ->set('search_post', $post);
        } else {
            $this->redirect('user/access_denied');
        }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/some_thing_went_wrong');
        }
    }

    /* Update Person Verysis temp bulk images */

    public function action_nadra_requests_temp_upload_databank() {
        try {

            $this->auto_render = FALSE;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;

            $person_id= Helpers_Utilities::get_person_id_with_cnic($_POST['cnic_number']);
            $create_flag=0;
                if(empty($person_id)){
                    $mg = new Model_Generic();
                    $chk=2;
                    $person_id = $mg->update_cnic_number($_POST,$chk);
                    $create_flag=1;

                }

                $is_forigner=Helpers_Utilities::get_person_nationality_with_pid($person_id);


            $is_versis = Helpers_Profile::check_image_url_exist($person_id);

            if(empty($is_versis))
            {

                $result = array();
                if (!empty($_FILES['personverysis'])) {
                    $result = Helpers_Profile::_save_image($_FILES['personverysis'], "person_verysis", $person_id);

                    $mg = new Model_Generic();
                    $mg->nadra_verisys_file_insertion($result,$is_forigner,$person_id);
                    if($create_flag==0) {
                        echo json_encode(3);
                    }else{
                        echo json_encode(4);
                    }
                } else {
                    echo json_encode(5);
                }
            }
            else{
                echo json_encode(2);
            }

        } catch (Exception $ex) {
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex);
                exit;
            } else {
                echo json_encode(5);
            }
        }
    }

    //ajax call for data
    public function action_ajaxdatabanknadrarequestsbulk() {
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
                $rows_count = $data->nadra_verisys_databank_bulk($post, 'true');
                $profiles = $data->nadra_verisys_databank_bulk($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $image_name=Helpers_Profile::check_image_url_exist($item['person_id']);

                        $cnic_number = ( isset($item['cnic_number']) ) ? $item['cnic_number'] : '0';
                        if($cnic_number==0)
                        {
                            $cnic_number = ( isset($item['cnic_number_foreigner']) ) ? $item['cnic_number_foreigner'] : '0';
                        }

                     //   $image_name = ( isset($item['cnic_image_url']) ) ? $item['cnic_image_url'] : '0';

                        $upload_by = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $upload_date = ( isset($item['created_at']) ) ? $item['created_at'] : 0;



                        $row = array(
                            $image_name,
                            $cnic_number,
                            $upload_by,
                            $upload_date,

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

    //ajax call for nadra reports
    public function action_ajaxdatabanknadrarequestsreports() {
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
                $post = Session::instance()->get('bulkresponse_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;
//            }

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);


                $data = new Model_Userrequest;
                $rows_count = $data->nadra_verisys_databank_reports($post, 'true');
                $profiles = $data->nadra_verisys_databank_reports($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $image_name=Helpers_Profile::check_image_url_exist($item['person_id']);

                        $cnic_number = ( isset($item['cnic_number']) ) ? $item['cnic_number'] : '0';
                        if($cnic_number==0)
                        {
                            $cnic_number = ( isset($item['cnic_number_foreigner']) ) ? $item['cnic_number_foreigner'] : '0';
                        }

                     //   $image_name = ( isset($item['cnic_image_url']) ) ? $item['cnic_image_url'] : '0';

                        $upload_by = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        $upload_date = ( isset($item['created_at']) ) ? $item['created_at'] : 0;



                        $row = array(
                            $image_name,
                            $cnic_number,
                            $upload_by,
                            $upload_date,

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
    //ajax call for msisdn reports
    public function action_ajaxdatabank_msisdn_requests_reports() {
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
                $post = Session::instance()->get('msisdn_bulkresponse_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;
//            }

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

//                echo '<pre>';
//                print_r($post);
//                exit();
                $data = new Model_Userreport();
                $rows_count = $data->msisdn_reports($post, 'true');
                $profiles = $data->msisdn_reports($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {


//                        $image_name=Helpers_Profile::check_image_url_exist($item['person_id']);

                        $country = ( isset($item['country']) ) ? $item['country'] : '0';
                        if($country==0)
                        {
                            $country="Pakistan";
                        }
                        else{
                            $country="Foriegn";
                        }
                        $project_id = ( isset($item['project_id']) ) ? $item['project_id'] : 1;
                        $project_name=  Helpers_Utilities::get_project_names($project_id);

//                        $project_info= Helpers_Utilities::get_projects_list($project_id);
//                        echo '<pre>';
//                        print_r($project_info);
//                        exit();
//                        $project_name= $project_info->project_name;
//                        $pro_reg_id= $project_info->region_id;
//                        $pro_region= Helpers_Utilities::get_region($pro_reg_id);
//                        $pro_dis_id= $project_info->district_id;
//                        $pro_district= Helpers_Utilities::get_district($pro_dis_id);

                        $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : '0';

                        $activation_date = ( isset($item['activation_date']) ) ? $item['activation_date'] : '0';
                        $imsi_number = ( isset($item['imsi_number']) ) ? $item['imsi_number'] : '0';
                        $imei_number = ( isset($item['imei_number']) ) ? $item['imei_number'] : '0';
                        $first_name = ( isset($item['first_name']) ) ? $item['first_name'] : 'NA';
                        $last_name = ( isset($item['last_name']) ) ? $item['last_name'] : 'NA';
                        $cnic_number = ( isset($item['cnic_number']) ) ? $item['cnic_number'] : '0';
                        $address = ( isset($item['address']) ) ? $item['address'] : 'NA';
                        $con_type = ( isset($item['con_type']) ) ? $item['con_type'] : '0';
                        if($con_type==0)
                        {
                            $con_type="Pre Paid";
                        }else{
                            $con_type="Post Paid";
                        }
                        $status = ( isset($item['status']) ) ? $item['status'] : '0';
                        if($status==0)
                        {
                            $status="Active";
                        }else{
                            $status="In Active ";
                        }

                        $file = ( isset($item['file']) ) ? $item['file'] : '';
                        if (!empty($file)) {
                            $member_name_link = '<form role="form" id="download" name="download" class="ipf-form" action="' . URL::site('admindatabank/download/') . '" method="POST">'
                                . '<input type="hidden" readonly="readonly" class="form-control" id="userid" value="' . $file . '" name="file">'
                                . '<button type="submit" class="btn btn-primary"> Download </button>'
                                . '</form>';
                        }else
                        {
                            $member_name_link= "No File";
                        }



                        $upload_by = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        // $upload_date = ( isset($item['created_at']) ) ? $item['created_at'] : 0;



                        $row = array(

                            '<b>NAME: </b>'. $first_name.' '.$last_name."<br>".'<b> CNIC No.:</b>'.$cnic_number."<br>".'<b> Address : </b>'.$address."<br>".'<b> Country : </b>'.$country,
                            '<b>Phone No. : </b>'. $phone_number."<br>".'<b> IMSI No.:</b>'.$imsi_number."<br>".'<b> IMEI No. : </b>'.$imei_number."<br>".'<b> Connection Type : </b>'.$con_type."<br>".'<b> Status : </b>'.$status."<br>".'<b> Activation Date : </b>'.$activation_date,
//                             $project_name.' (Region-'.$pro_region.') '.'(District-'.$pro_district.')',
                            $project_name,
                            $upload_by,
                            $member_name_link,

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

    //audit report basic
    public function action_breakup_report() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 34) == 1) {

                /* Posted Data */
                $search_post = $this->request->post();
                $search_post = Helpers_Utilities::remove_injection($search_post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('breakup_report_post', $search_post);
                /* Excel Export File Included */
                include 'excel/breakup_report_basic.inc';
                /* File Included */
                include 'user_functions/breakup_report_basic.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }
    //msisdn report breakup
    public function action_msisdn_breakup_report() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 34) == 1) {

                /* Posted Data */
                $search_post = $this->request->post();
                $search_post = Helpers_Utilities::remove_injection($search_post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('msisdn_breakup_report_post', $search_post);
                /* Excel Export File Included */
                include 'excel/msisdn_breakup_report_basic.inc';
                /* File Included */
                include 'user_functions/msisdn_breakup_report_basic.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxbreakupreportbasic() {
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
                $post = Session::instance()->get('breakup_report_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;
//            }

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->breakup_report_basic($post, 'true');
                $profiles = $data->breakup_report_basic($post, 'false');

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
                        $member_name_link = '<form role="form" name="advance_search_form" id="advance_search_form" class="ipf-form" action="' . URL::site('admindatabank/breakup_report_individual/') . '" method="POST">'
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
    //ajax call for data
    public function action_ajaxmsisdnbreakupreportbasic() {
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
                $post = Session::instance()->get('msisdn_breakup_report_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;
//            }

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userreport;
                $rows_count = $data->msisdn_breakup_report_basic($post, 'true');
                $profiles = $data->msisdn_breakup_report_basic($post, 'false');

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
//                        echo '<pre>';
//                        print_r($item);
//                        exit();


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
                        $member_name_link = '<form role="form" name="advance_search_form" id="advance_search_form" class="ipf-form" action="' . URL::site('admindatabank/msisdn_breakup_report_individual/') . '" method="POST">'
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
    public function action_breakup_report_individual() {
        try {
            /* Posted Data */
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $_POST = Helpers_Utilities::remove_injection($_POST);
            if (!empty($_POST)) {
                $post = array_merge($post, $_POST);
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('breakup_report_individual_post', $post);
                /* Excel Export File Included */
                include 'excel/breakup_report.inc';
                /* File Included */
                include 'user_functions/breakup_report.inc';
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
    public function action_msisdn_breakup_report_individual() {
        try {
            /* Posted Data */
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $_POST = Helpers_Utilities::remove_injection($_POST);
            if (!empty($_POST)) {
                $post = array_merge($post, $_POST);
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('msisdn_breakup_report_individual_post', $post);
                /* Excel Export File Included */
                include 'excel/breakup_report.inc';
                /* File Included */
                include 'user_functions/msisdn_breakup_report.inc';
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
    public function action_ajaxbreakupreport() {
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
                $post = Session::instance()->get('breakup_report_individual_post', array());

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
                $rows_count = $data->breakup_report($post, 'true', $posted);
                $profiles = $data->breakup_report($post, 'false', $posted);

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
                        $member_name_link = '<form role="form" name="advance_search_form" id="advance_search_form" class="ipf-form" action="' . URL::site('admindatabank/no_send_type_reports/') . '" method="POST">'
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
    //ajax call for data
    public function action_ajaxmsisdnbreakupreport() {
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
                $post = Session::instance()->get('msisdn_breakup_report_individual_post', array());

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
                $rows_count = $data->msisdn_breakup_report($post, 'true', $posted);
                $profiles = $data->msisdn_breakup_report($post, 'false', $posted);

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
                        $member_name_link = '<form role="form" name="advance_search_form" id="advance_search_form" class="ipf-form" action="' . URL::site('admindatabank/msisdn_no_request_send_reports_detail/') . '" method="POST">'
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


    public function action_no_send_type_reports() {

        try {
            /* Posted Data */
            $post = $this->request->post();
            $_POST = Helpers_Utilities::remove_injection($_POST);
            if (!empty($_POST)) {
                $post = array_merge($post, $_POST);
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('no_send_type_reports_post', $post);
                /* Excel Export File Included */
                include 'excel/no_send_reports.inc';
                /* File Included */
                include 'user_functions/no_send_reports.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }



    //ajax call for data
    public function action_ajaxuserrequestsendreports() {
    //    try {

            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('no_send_type_reports_post', array());
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
                $rows_count = $data->no_request_send_type_reports($post, 'true', $userid);
                $profiles = $data->no_request_send_type_reports($post, 'false', $userid);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $user_id = ( isset($item['id']) ) ? $item['id'] : 'NA';
                        $user_name = ( isset($item['id']) ) ? Helpers_Utilities::get_user_name($item['id']) : 'NA';
                        $user_designation = ( isset($item['id']) ) ? Helpers_Utilities::get_user_job_title($item['id']) : 'NA';
                        $user_region = (!empty($item['region_id']) ) ? Helpers_Utilities::get_region($item['region_id']) : 'Head Quarters';
                        $user_posting = ( isset($item['posted']) ) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';
                        $request_type = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type_name($item['user_request_type_id']) : 'NA';
                        $count = ( isset($item['count']) ) ? $item['count'] : 0;
                        $user_id_en = Helpers_Utilities::encrypted_key($item['id'], 'encrypt');
                        $request_type="Nadra Verisys";
                        $request_type_en = Helpers_Utilities::encrypted_key($request_type, 'encrypt');

                        $member_name_link = '<form role="form" id="view_form" name="view_form" class="ipf-form" action="' . URL::site('admindatabank/no_request_send_reports_detail/') . '" method="POST">'
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
//        } catch (Exception $ex) {
//            echo '<pre>';
//            print_r($ex);
//            exit;
//        }
    }


    public function action_no_request_send_reports_detail() {
        try {
            /* Posted Data */
            $post = $this->request->post();
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $post = array_merge($post, $_POST);
            $post = Helpers_Utilities::remove_injection($post);
            if (!empty($_POST)) {
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('no_reports_send_detail_post', $post);
                /* Excel Export File Included */
                include 'excel/no_send_reports_detail.inc';
                /* File Included */
                include 'user_functions/no_send_reports_detail.inc';
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
    public function action_msisdn_no_request_send_reports_detail() {
        try {
            /* Posted Data */
            $post = $this->request->post();
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $post = array_merge($post, $_POST);
            $post = Helpers_Utilities::remove_injection($post);
            if (!empty($_POST)) {
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('msisdn_no_reports_send_detail_post', $post);
                /* Excel Export File Included */
                include 'excel/no_send_reports_detail.inc';
                /* File Included */
                include 'user_functions/msisdn_no_send_reports_detail.inc';
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
    public function action_ajaxuserrequestsendreportsdetail() {
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
                $post = Session::instance()->get('no_reports_send_detail_post', array());
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
                $rows_count = $data->no_request_send_detail_reports($post, 'true', $userid, $request_type);
                $profiles = $data->no_request_send_detail_reports($post, 'false', $userid, $request_type);

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

                        $requested_value = ( isset($item['cnic_number']) ) ? $item['cnic_number'] : 0;
                        if(empty($requested_value))
                        {
                            $requested_value = ( isset($item['cnic_number_foreigner']) ) ? $item['cnic_number_foreigner'] : 0;
                        }

                       // $requested_value .= ( isset($item['request_id']) ) ? '<br/><b>' . $item['request_id'] . '<b>' : '';
                        $reason = ( isset($item['reason']) ) ? $item['reason'] : 'NA';
                        $concerned_person_id = ( isset($item['person_id']) ) ? $item['person_id'] : 'NA';
                        if ($concerned_person_id > 0) {
                            $perons_name = ( isset($item['person_id']) ) ? Helpers_Person::get_person_name($item['person_id']) : 'NA';
                            $perons_name .= '[';
                            $perons_name .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';
                            $perons_name .= ']';
                        } else {
                            $perons_name = " ";
                        }
                        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : 'NA';
                       // $view_request_status = '<a href="' . URL::site('userrequest/request_status_detail/' . Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt')) . '" > View Detail </a>';

                        $row = array(
                            $user_name,
                            // $request_type,
                            $requested_value,
                            $reason,
                            $perons_name,
                            $created_at
                         //   $view_request_status,
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
    //ajax call for data
    public function action_ajaxmsisdnuserrequestsendreportsdetail() {
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
                $post = Session::instance()->get('msisdn_no_reports_send_detail_post', array());
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;
//            }
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
//echo '<pre>';
//print_r($post);
//exit();
                if (!empty($post['userid']) ) {
                    $userid = Helpers_Utilities::encrypted_key($post['userid'], 'decrypt');
                  //  $request_type = Helpers_Utilities::encrypted_key($post['request_type'], 'decrypt');
                } else {
                    $userid = NULL;
                    //$request_type = NULL;
                }
                $data = new Model_Userreport;
                $rows_count = $data->msisdn_no_request_send_detail_reports($post, 'true', $userid);
                $profiles = $data->msisdn_no_request_send_detail_reports($post, 'false', $userid);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {


//                        $image_name=Helpers_Profile::check_image_url_exist($item['person_id']);

                        $country = ( isset($item['country']) ) ? $item['country'] : '0';
                        if($country==0)
                        {
                            $country="Pakistan";
                        }
                        else{
                            $country="Foriegn";
                        }
                        $project_id = ( isset($item['project_id']) ) ? $item['project_id'] : 1;
                        $project_name=  Helpers_Utilities::get_project_names($project_id);

//                        $project_info= Helpers_Utilities::get_projects_list($project_id);
//                        echo '<pre>';
//                        print_r($project_info);
//                        exit();
//                        $project_name= $project_info->project_name;
//                        $pro_reg_id= $project_info->region_id;
//                        $pro_region= Helpers_Utilities::get_region($pro_reg_id);
//                        $pro_dis_id= $project_info->district_id;
//                        $pro_district= Helpers_Utilities::get_district($pro_dis_id);

                        $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : '0';

                        $activation_date = ( isset($item['activation_date']) ) ? $item['activation_date'] : '0';
                        $imsi_number = ( isset($item['imsi_number']) ) ? $item['imsi_number'] : '0';
                        $imei_number = ( isset($item['imei_number']) ) ? $item['imei_number'] : '0';
                        $first_name = ( isset($item['first_name']) ) ? $item['first_name'] : 'NA';
                        $last_name = ( isset($item['last_name']) ) ? $item['last_name'] : 'NA';
                        $cnic_number = ( isset($item['cnic_number']) ) ? $item['cnic_number'] : '0';
                        $address = ( isset($item['address']) ) ? $item['address'] : 'NA';
                        $con_type = ( isset($item['con_type']) ) ? $item['con_type'] : '0';
                        if($con_type==0)
                        {
                            $con_type="Pre Paid";
                        }else{
                            $con_type="Post Paid";
                        }
                        $status = ( isset($item['status']) ) ? $item['status'] : '0';
                        if($status==0)
                        {
                            $status="Active";
                        }else{
                            $status="In Active ";
                        }

                        $file = ( isset($item['file']) ) ? $item['file'] : '';
                        if (!empty($file)) {
                            $member_name_link = '<form role="form" id="download" name="download" class="ipf-form" action="' . URL::site('admindatabank/download/') . '" method="POST">'
                                . '<input type="hidden" readonly="readonly" class="form-control" id="userid" value="' . $file . '" name="file">'
                                . '<button type="submit" class="btn btn-primary"> Download </button>'
                                . '</form>';
                        }else
                        {
                            $member_name_link= "No File";
                        }



                        $upload_by = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                        // $upload_date = ( isset($item['created_at']) ) ? $item['created_at'] : 0;



                        $row = array(
                           // $upload_by,
                            '<b>NAME: </b>'. $first_name.' '.$last_name."<br>".'<b> CNIC No.:</b>'.$cnic_number."<br>".'<b> Address : </b>'.$address."<br>".'<b> Country : </b>'.$country,
                            '<b>Phone No. : </b>'. $phone_number."<br>".'<b> IMSI No.:</b>'.$imsi_number."<br>".'<b> IMEI No. : </b>'.$imei_number."<br>".'<b> Connection Type : </b>'.$con_type."<br>".'<b> Status : </b>'.$status."<br>".'<b> Activation Date : </b>'.$activation_date,
//                             $project_name.' (Region-'.$pro_region.') '.'(District-'.$pro_district.')',
                            $project_name,
                            $member_name_link,

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
     *  User Manual Data Upload (data_upload)
     */


    public function action_data_upload_against_msisdn() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);

            if ($permission == 1 || $permission == 2 || ($permission == 4 )) {
                /* Posted Data */
                $post = $this->request->post();

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $uid=$login_user->id;

                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('old_data_post', $post);
                $this->template->content = View::factory('templates/user/data_up_against_msisdn')
                    ->bind('user_id', $uid)
                    ->set('search_post', $post);
            } else {
                $this->redirect('user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/some_thing_went_wrong');
        }
    }

    public function action_data_upload_against_msisdn_feed() {
        try {

            $this->auto_render = FALSE;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;


            $ph_no= $_POST['mobile_number'];
            $directory = '/home/aiesfiles/old-data/';
            $date = date("YmdHis", time());

            if(!empty($_FILES['cdr_file']['name'])) {
                $filename = $ph_no .  $date . ".jpg";
            }
            else{
                $filename='';
            }
            if ($file = Upload::save($_FILES['cdr_file'], NULL, $directory)) {
                $img = Image::factory($file);
                $img->save($directory . $filename);
                Helpers_Profile::_resize_images($filename);
                unlink($file);
            }
        $msisdn = new Model_Generic();
                $result = $msisdn->data_up_against_msisdn($_POST, $user_id,$filename);
                if ($result == 1) {
                    echo json_encode(1);
                } else {
                    echo json_encode(2);
                }
        }catch (Exception $ex) {
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex);
                exit;
            } else {
                echo json_encode(2);
            }
        }
    }
    //ajax call for data
    public function action_ajaxmsisdn_old_data() {
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
                $post = Session::instance()->get('old_data_post', array());

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);


                $data = new Model_Userrequest;
                $rows_count = $data->msisdn_old_data_upload($post, 'true');
                $profiles = $data->msisdn_old_data_upload($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {


//                        $image_name=Helpers_Profile::check_image_url_exist($item['person_id']);

                        $country = ( isset($item['country']) ) ? $item['country'] : '0';
                        if($country==0)
                        {
                            $country="Pakistan";
                        }
                        else{
                            $country="Foriegn";
                        }
                        $project_id = ( isset($item['project_id']) ) ? $item['project_id'] : 1;
                      $project_name=  Helpers_Utilities::get_project_names($project_id);

//                        $project_info= Helpers_Utilities::get_projects_list($project_id);
//                        echo '<pre>';
//                        print_r($project_info);
//                        exit();
//                        $project_name= $project_info->project_name;
//                        $pro_reg_id= $project_info->region_id;
//                        $pro_region= Helpers_Utilities::get_region($pro_reg_id);
//                        $pro_dis_id= $project_info->district_id;
//                        $pro_district= Helpers_Utilities::get_district($pro_dis_id);

                        $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : '0';

                        $activation_date = ( isset($item['activation_date']) ) ? $item['activation_date'] : '0';
                        $imsi_number = ( isset($item['imsi_number']) ) ? $item['imsi_number'] : '0';
                        $imei_number = ( isset($item['imei_number']) ) ? $item['imei_number'] : '0';
                        $first_name = ( isset($item['first_name']) ) ? $item['first_name'] : 'NA';
                        $last_name = ( isset($item['last_name']) ) ? $item['last_name'] : 'NA';
                        $cnic_number = ( isset($item['cnic_number']) ) ? $item['cnic_number'] : '0';
                        $address = ( isset($item['address']) ) ? $item['address'] : 'NA';
                        $con_type = ( isset($item['con_type']) ) ? $item['con_type'] : '0';
                        if($con_type==0)
                        {
                            $con_type="Pre Paid";
                        }else{
                            $con_type="Post Paid";
                        }
                        $status = ( isset($item['status']) ) ? $item['status'] : '0';
                        if($status==0)
                        {
                            $status="Active";
                        }else{
                            $status="In Active ";
                        }

                        $file = ( isset($item['file']) ) ? $item['file'] : '';
                        if (!empty($file)) {
                            $member_name_link = '<form role="form" id="download" name="download" class="ipf-form" action="' . URL::site('admindatabank/download/') . '" method="POST">'
                                . '<input type="hidden" readonly="readonly" class="form-control" id="userid" value="' . $file . '" name="file">'
                                . '<button type="submit" class="btn btn-primary"> Download </button>'
                                . '</form>';
                        }else
                        {
                            $member_name_link= "No File";
                        }



                        $upload_by = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
                       // $upload_date = ( isset($item['created_at']) ) ? $item['created_at'] : 0;



                        $row = array(

                            '<b>NAME: </b>'. $first_name.' '.$last_name."<br>".'<b> CNIC No.:</b>'.$cnic_number."<br>".'<b> Address : </b>'.$address."<br>".'<b> Country : </b>'.$country,
                            '<b>Phone No. : </b>'. $phone_number."<br>".'<b> IMSI No.:</b>'.$imsi_number."<br>".'<b> IMEI No. : </b>'.$imei_number."<br>".'<b> Connection Type : </b>'.$con_type."<br>".'<b> Status : </b>'.$status."<br>".'<b> Activation Date : </b>'.$activation_date,
//                             $project_name.' (Region-'.$pro_region.') '.'(District-'.$pro_district.')',
                            $project_name,
                            $upload_by,
                            $member_name_link,

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

    public function action_download() {
        $login_user = Auth::instance()->get_user();
        $user_id = !empty($login_user->id) ? $login_user->id : 0;
        ob_clean();
        try {
            $_GET = Helpers_Utilities::remove_injection($_GET);
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $target='/home/aiesfiles/old-data/';
            $_GET['file'] = $file = !empty($_POST['file']) ? $_POST['file'] : '';
            $file = $target . $file;
            if (!$file) { // file does not exist
                die('file not found');
            } else {
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename={$_GET['file']}");
                header("Content-Type: application/zip");
                header("Content-Transfer-Encoding: binary");
                header("Content-type:application/pdf");
                // read the file from disk
                readfile($file);
            }
        } catch (Exception $ex) {
            echo 'Error in file download';
        }
    }





}
