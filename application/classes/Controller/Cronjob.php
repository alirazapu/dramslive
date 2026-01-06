<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Controller_Cronjob extends Controller {    
    /* test function */
    public function action_test() {
        exit;            
        $send_key= Helpers_Utilities::encrypted_key('test', "encrypt");
        $send_key = str_replace("axHmBf8ri9x","",$send_key);
        $send_key= Helpers_Utilities::encrypted_key($send_key, "decrypt");        
    }    
    public function action_email_send_ufone() {
        /*  High prority  for location */
        include 'cron_job/send_other/low_ufone.inc';
    }
    public function action_email_send_nadira() {
        /*  High prority  for location */
        include 'cron_job/send_nadira/heigh.inc';
    }
    /* ptcl */
    public function action_email_send_ptcl() {
        /*  High prority  for location */
        include 'cron_job/send_ptcl/heigh.inc';
    }
    /* Current Location */
    public function action_email_send_loc() {
        /* Telco Report */
        include 'cron_job/send_other/telco_rep.inc';
        /*  High prority  for location */
        include 'cron_job/send_location/heigh.inc';
    }

    public function action_email_send() {
        $data = Model_Generic::resend_error_in_queue();
        /* Telco Report */
        include 'cron_job/send_other/telco_rep.inc';
        /*  High prority */
        include 'cron_job/send_other/heigh.inc';
        /*  Medium prority */
        include 'cron_job/send_other/medium.inc';
        /*  Low prority */
        include 'cron_job/send_other/low.inc';
    }

    /* email receive */
    public function action_email_receive() {        
        Helpers_Email::get_email_status();
    }

    /* email receive */
    public function action_email_receive2() {
        $email_sender = Helpers_Email::receive_email('', 2);
    }

    public function action_email_parse_sub() {
         //echo 'Current Location';        
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
               and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and ur.user_request_type_id = 3               
                ORDER BY ur.request_id  ASC
            ";                              //Where t1.user_id = {$user_id}  
            // and ur.request_id=459688
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

                $login_user = Auth::instance()->get_user();
   if(!empty($login_user->id) && $login_user->id==138){
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 3
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and ur.user_request_type_id = 3
               and company_name=1
                ORDER BY ur.request_id  DESC
            ";                              //Where t1.user_id = {$user_id}  
// and ur.request_id = 1140862
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

//        echo '<pre>';
//        print_r(count($parse_data));
//        exit;
          }
        foreach ($parse_data as $data) {
            try {
                $mobile_number = '';
                $name = '';
                $cnic = '';
                $cnic_original = '';
                $is_foreigner = 0;
                $address = '';
                $active = '';
                $date = '';
                $status = '';
                $not_fount = 0;
                 $reference_number = $data['request_id'];
//echo '<br>';
                $data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);

                //print_r($data);
$login_user = Auth::instance()->get_user();
//   if($login_user->id==138){
       
   //echo "<pre>"; print_r($data); exit;
//   }
   echo 'Company: '.$data['company_name'].'<br>';
   echo 'Request ID: '.$data['request_id'].'<br><br>';
   
                switch ($data['company_name']) {
                    case 1: // mobilink  
                    case 7: // mobilink  
                        // echo '<br>' . 'Mobilink' .'<br>';     
                        include 'cron_job/parse_sub/mobilink.inc';

                        break;
                    //case 7: // warid
                    //echo '<br>' . 'Warid' .'<br>';                        
                    //print_r($data['received_file_path']);
                    /* include 'cron_job/parse_sub/warid.inc';

                      break; */
                    case 3: // Ufone
                        // echo '<br>' . 'Ufone' .'<br>';                                                
                        include 'cron_job/parse_sub/ufone.inc';

                        break;
                    case 6: // Telenor
                        //echo '<br>' . 'Telenor' .'<br>';                        
                        include 'cron_job/parse_sub/telenor.inc';

                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';
                        include 'cron_job/parse_sub/zong.inc';

                        break;
                }

                //echo $mobile_number;
                if ($not_fount == 0) {
                    /* Insertion Code */

                   $mobile_number = trim($mobile_number);
                 
                    $name = trim($name);
                 
                    $cnic = trim($cnic);
                 
                    $address = trim($address);
                 
                    $active = trim($active);

                    if ((strlen($mobile_number) == 10 && ctype_digit($mobile_number) && ctype_digit($cnic)) || ( strlen($mobile_number) == 10 && strlen($cnic) == 13 && ctype_digit($mobile_number))) {

                        $name = $parse_val = array_filter(explode(' ', trim(strip_tags($name))));
                        if (empty($active) || $active == 'Active')
                            $active = 1;
                        else
                            $active = 0;
                        if (!empty($date))
                            $date = date("Y-m-d H:i:s", strtotime($date));
                        else
                            $date = '';
                        if (!empty($status) && $status == 'Postpaid') {
                            $status = '0';
                        } else {
                            $status = '1';
                        }
/*
                        if (!empty($is_foreigner) && $is_foreigner == 1) {
                            $reference_number_1 = Model_Email::email_status($reference_number, 2, 3);

                            break;
                            exit;
                        }*/

                        $sub_data = array();
                        $sub_data['act_date'] = $date;
                        $sub_data['mobile_number'] = trim($mobile_number);
                        $sub_data['cnic_number'] = trim($cnic);
                        $sub_data['cnic_number_original'] = trim($cnic_original);
                        $sub_data['is_foreigner'] = trim($is_foreigner);
                        if (sizeof($name) == 3) {
                            $name = array_values($name);
                            $sub_data['person_name'] = trim($name[0]) . ' ' . trim($name[1]);
                            $sub_data['person_name1'] = trim($name[2]);
                        } else {
                            $sub_data['person_name'] = !empty($name[0]) ? trim($name[0]) : '';
                            $sub_data['person_name1'] = !empty($name[1]) ? trim($name[1]) : '';
                        }

                        $sub_data['address'] = trim($address);
                        $sub_data['user_id'] = $data['user_id'];
                        $sub_data['imsi'] = '';
                        $sub_data['StatusRadios'] = $active;
                        $sub_data['ConnectionTypeRadios'] = $status;
                        $sub_data['company_name_get'] = $data['company_name'];
                        $sub_data['imei'] = '';
                        $sub_data['phone_name'] = '';
                        $sub_data['requestid'] = $reference_number;
                        
                        //extra check
                        $phone_helper = $sub_data['mobile_number'];
                        if(strlen($phone_helper)===10 && $phone_helper[0]==='3')
                        {
                            $sub_model = new Model_Generic();                        
                            $sub_model_result = $sub_model->ManualSubInfoinsert($sub_data);
                            $reference_number_1 = Model_Email::email_status($reference_number, 2, 5);
                        }else{
                          $reference_number_1 = Model_Email::email_status($reference_number, 2, 3);
                        } 
                    } else {
                        $reference_number_1 = Model_Email::email_status($reference_number, 2, 3);
                        //break;
                       // exit;
                    }
                }
            } catch (Exception $e) {
                //re-throw exception
                //throw new customException($email);
                //echo $reference_number; 
              // echo '<pre>';
              // print_r($e);
                //      echo 'error exception';
               
                $reference_number = $data['request_id'];
                $reference_number_1 = Model_Email::email_status($reference_number, 2, 3);
                //break;
               // exit;
            }
        }
        //exit;
    }

    /* Cron Job for Current Location */

    public function action_email_parse_loc() {
        //echo 'Current Location';
        //load page after every 5 seconds
         // echo '<script>setTimeout(function(){   window.location.reload(1); }, 5000)</script>';
        /*         * ** */
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and ur.user_request_type_id = 4
              
                ORDER BY ur.request_id  ASC
            ";                              //Where t1.user_id = {$user_id}  
// and ur.request_id = 1140862
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

         $login_user = Auth::instance()->get_user();
   if(!empty($login_user->id) && $login_user->id==1385){
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 3
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and ur.user_request_type_id = 4
               and company_name=8
                ORDER BY ur.request_id  DESC
            ";                              //Where t1.user_id = {$user_id}  
// and ur.request_id = 1140862
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

//         
          } 
        foreach ($parse_data as $data) {
            
//   if(!empty($login_user->id) && $login_user->id==138){
//         echo '<pre>'; print_r(count($parse_data));         
//          exit;
//         
//          } 

            try {

                $loc_data = array();
                $loc_data['locdate'] = date("Y-m-d H:i:s");
                $loc_data['person_id'] = $data['concerned_person_id'];
               echo '<br>'.  $loc_data['requestid'] = $data['request_id'];
                $loc_data['loccompany'] = $data['company_name'];
                $loc_data['user_id'] = $data['user_id'];
                $loc_data['locimsi'] = '';
                $loc_data['locationmsisdn'] = '';
                $loc_data['locimei'] = '';
                $loc_data['locphonename'] = '';
                $loc_data['locnetwork'] = 0;
                $loc_data['loclac'] = '';
                $loc_data['loccellid'] = '';
                $loc_data['loclat'] = '';
                $loc_data['loclong'] = '';
                $loc_data['locaddress'] = '';
                $loc_data['locstatus'] = 0;
                $not_fount = 0;

                $data_body = array_filter(explode('From:', strip_tags($data['received_body'])));
                $data['received_body'] = $data_body[0];

  
//   $login_user = Auth::instance()->get_user();
//          if(!empty($login_user->id) && $login_user->id==138){
//         echo '<pre>'; print_r($data);         
//          exit;
//         
//          } 
             
                switch ($data['company_name']) {
                    case 1: // mobilink                      
                        // echo '<br>' . 'Mobilink' .'<br>';                                                                        
                        include 'cron_job/parse_location/mobilink.inc';

                        break;
                    case 7: // warid
                        //echo '<br>' . 'Warid' .'<br>';
                        include 'cron_job/parse_location/warid.inc';

                        break;
                    case 3: // Ufone
                        //echo '<br>' . 'Ufone' .'<br>';  
                        
                        include 'cron_job/parse_location/ufone.inc';

                        break;
                    case 6: // Telenor
                        //echo '<br>' . 'Telenor' .'<br>';                        
                        include 'cron_job/parse_location/telenor.inc';

                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                        
                        include 'cron_job/parse_location/zong.inc';

                        break;
                    
                         case 8: // scom
                        //echo '<br>' . 'Scom' .'<br>';                                               
                        include 'cron_job/parse_location/scom.inc';

                        break;
                }

                if ($not_fount == 0) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    if (strlen($loc_data['locationmsisdn']) == 10 && ctype_digit($loc_data['locationmsisdn'])) {
                        $sub_model = new Model_Generic();
                        $sub_model_result = $sub_model->ManualLocationinsert($loc_data);
                    } else {
                        $reference_number = Model_Email::email_status($reference_number, 2, 3);
                      //  break;
                       // exit;
                    }
                }
            } catch (Exception $e) {
                
//                if(!empty($login_user->id) && $login_user->id==138){
//         echo '<pre>'; print_r($e);         
//          exit;
//         
//          }
                //re-throw exception
                //throw new customException($email);
                //echo $loc_data['requestid']; 
               // echo $e;
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);

                //break;
                //exit();
            }
        }
        
      
    }

    /* Cron Job for CNIC # */

    public function action_email_parse_nic() {
        //echo 'nic #';

        /*         * ** */
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and ur.user_request_type_id = 5
                ORDER BY ur.request_id  ASC
            ";                              //Where t1.user_id = {$user_id}  
        
           $login_user = Auth::instance()->get_user();
          if(!empty($login_user->id) && $login_user->id==138){
           $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and ur.user_request_type_id = 5
                and ur.company_name=4
                ORDER BY ur.request_id DESC
            ";
         
          } 

        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($parse_data as $data) {
            try {

                $loc_data = array();
                $loc_data['user_id'] = $data['user_id'];
                $loc_data['requestid'] = $data['request_id'];
                $not_fount = 0;
                $name_flag = 0;

                $data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);

//                  echo '<pre>tst';
//                         print_r($data['company_name']); 
//                exit;
                
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        // echo '<br>' . 'Mobilink' .'<br>';
                        include 'cron_job/parse_nic/mobilink.inc';

                        break;
                    case 7: // warid
                        //echo '<br>' . 'Warid' .'<br>';
                        include 'cron_job/parse_nic/warid.inc';

                        break;
                    case 3: // Ufone
                        //echo '<br>' . 'Ufone' .'<br>';                                    
                        include 'cron_job/parse_nic/ufone.inc';

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' .'<br>';                        
                        include 'cron_job/parse_nic/telenor.inc';


                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                                               
                        include 'cron_job/parse_nic/zong.inc';

                        break;
                }

                if ($not_fount == 0) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    if (strlen($loc_data['cnicsims']) == 13 && ctype_digit($loc_data['cnicsims'])) {
                        $sub_model = new Model_Generic();
                        $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);
                    } else {
                        $reference_number = Model_Email::email_status($reference_number, 2, 3);
                        break;
                        exit;
                    }
                }
            } catch (Exception $e) {
                //re-throw exception
                //echo $loc_data['requestid'];         //throw new customException($email);
                /*echo '<pre>';
                echo $e;
                exit;*/
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
                break;
                exit;
            }
        }
    }
// check prorities high 
    public function action_email_parse_phone_high() {

        $request_id = $this->request->param('id');
        $sub_query = ' and ur.company_name not in (1,7,3,6,4) ';
        if(!empty($request_id)){
            $sub_query = " and ur.request_id =  {$request_id}";
        }else{
            exit;
        }
        
       
        $not_fount = 0;
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and (ur.user_request_type_id = 1 or ur.user_request_type_id = 6)
                {$sub_query}
                ORDER BY ur.request_id  ASC";                              //Where t1.user_id = {$user_id}
//                if(Auth::instance()->get_user()->id==419){
//                    print_r($sql);  exit;
//                }
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($parse_data as $data) {
            try {
                $phone_data['company_name'] = $data['company_name'];
                $phone_data['phone_number'] = $data['requested_value'];
                $phone_data['userrequestid'] = $data['request_id'];
                 $phone_data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $data['id'] = $data['file_id'] = $phone_data['file_id'];
                $cdrfile_name = Helpers_Upload::get_file_info_with_request_id($data['request_id']);


                if(empty($cdrfile_name['file']))
                {
                    $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }
                    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));                                 
                    include '/var/www/html/aies/application/classes/Controller/cron_job/parse_sub/notfound.inc';
                    exit;
                }    
                $data['received_file_path'] = !empty($cdrfile_name['file'])?$cdrfile_name['file']:'';

                
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job/parse_phone/mobilink.inc';

                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        include 'cron_job/parse_phone/warid.inc';


                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        include 'cron_job/parse_phone/ufone.inc';
                        // echo '<pre>';
                        // print_r($data['received_file_path']);                      

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' . '<br>';
                        //print_r($data['received_file_path']);
                        include 'cron_job/parse_phone/telenor.inc';



                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                        
                        //print_r($data['received_file_path']);
                        include 'cron_job/parse_phone/zong.inc';

                        break;
                }

                if ($not_fount != 1) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    $reference_number = Model_Email::email_status($reference_number, 2, 5);
                    /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                      { */
                    $sub_model = new Model_Generic();
                    //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);
                }
                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                //re-throw exception
                //throw new customException($email);
//                echo $e;
//                exit;
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
            }
        }
    }
// mobilink 
    public function action_email_parse_phone() {

        $request_id = $this->request->param('id');
        $sub_query = ' and ur.company_name not in (1,7,3,6,4) ';
        if(!empty($request_id)){
            $sub_query = " and ur.request_id =  {$request_id}";
        }
        $not_fount = 0;
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and (ur.user_request_type_id = 1 or ur.user_request_type_id = 6)
                {$sub_query}
                ORDER BY ur.request_id  ASC";                              //Where t1.user_id = {$user_id}
//                if(Auth::instance()->get_user()->id==419){
//                    print_r($sql);  exit;
//                }
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($parse_data as $data) {
            try {
                $phone_data['company_name'] = $data['company_name'];
                $phone_data['phone_number'] = $data['requested_value'];
                $phone_data['userrequestid'] = $data['request_id'];
                 $phone_data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $data['id'] = $data['file_id'] = $phone_data['file_id'];
                $cdrfile_name = Helpers_Upload::get_file_info_with_request_id($data['request_id']);


                if(empty($cdrfile_name['file']))
                {
                    $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }
                    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));                                 
                    include '/var/www/html/aies/application/classes/Controller/cron_job/parse_sub/notfound.inc';
                    exit;
                }    
                $data['received_file_path'] = !empty($cdrfile_name['file'])?$cdrfile_name['file']:'';

                
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job/parse_phone/mobilink.inc';

                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        include 'cron_job/parse_phone/warid.inc';


                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        include 'cron_job/parse_phone/ufone.inc';
                        // echo '<pre>';
                        // print_r($data['received_file_path']);                      

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' . '<br>';
                        //print_r($data['received_file_path']);
                        include 'cron_job/parse_phone/telenor.inc';



                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                        
                        //print_r($data['received_file_path']);
                        include 'cron_job/parse_phone/zong.inc';

                        break;
                }

                if ($not_fount != 1) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    $reference_number = Model_Email::email_status($reference_number, 2, 5);
                    /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                      { */
                    $sub_model = new Model_Generic();
                    //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);
                }
                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                //re-throw exception
                //throw new customException($email);
//                echo $e;
//                exit;
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
            }
        }
    }
// mobilink 
    public function action_email_parse_phone_1() {
//       phpinfo();
//       exit;
        // echo 'cdr';
        
        $not_fount = 0;
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and (ur.user_request_type_id = 1 or ur.user_request_type_id = 6)
                and ur.company_name = 1
                ORDER BY ur.request_id  ASC
            ";                              //Where t1.user_id = {$user_id}  

        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($parse_data as $data) {
            try {
                $phone_data['company_name'] = $data['company_name'];
                $phone_data['phone_number'] = $data['requested_value'];
             echo    $phone_data['userrequestid'] = $data['request_id'];
             echo '<br>';  
                $phone_data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $data['id'] = $data['file_id'] = $phone_data['file_id'];
                $cdrfile_name = Helpers_Upload::get_file_info_with_request_id($data['request_id']);
                       
                  
                
                if(empty($cdrfile_name['file']))
                {
                    $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII' || $encode_str=='UTF-8'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }

                    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));                                 
                    include '/var/www/html/aies/application/classes/Controller/cron_job/parse_sub/notfound.inc';
                    exit;
                }    
                $data['received_file_path'] = !empty($cdrfile_name['file'])?$cdrfile_name['file']:'';

                
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job/parse_phone/mobilink.inc';

                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        include 'cron_job/parse_phone/warid.inc';


                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        include 'cron_job/parse_phone/ufone.inc';
                        // echo '<pre>';
                        // print_r($data['received_file_path']);                      

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' . '<br>';
                        //print_r($data['received_file_path']);
                        include 'cron_job/parse_phone/telenor.inc';



                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                        
                        //print_r($data['received_file_path']);
                        include 'cron_job/parse_phone/zong.inc';

                        break;
                }

                if ($not_fount != 1) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    $reference_number = Model_Email::email_status($reference_number, 2, 5);
                    /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                      { */
                    $sub_model = new Model_Generic();
                    //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);
                }
                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                //re-throw exception
                //throw new customException($email);
              //  echo $e;
              //  exit;
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
            }
        }
    }
// Warid 
    public function action_email_parse_phone_7() {
//       phpinfo();
//       exit;
        // echo 'cdr';
        
        $not_fount = 0;
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and (ur.user_request_type_id = 1 or ur.user_request_type_id = 6)
                and ur.company_name = 7
                ORDER BY ur.request_id  ASC
            ";                              //Where t1.user_id = {$user_id}  

        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($parse_data as $data) {
            try {
                $phone_data['company_name'] = $data['company_name'];
                $phone_data['phone_number'] = $data['requested_value'];
             echo    $phone_data['userrequestid'] = $data['request_id'];
             echo '<br>';
                $phone_data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $data['id'] = $data['file_id'] = $phone_data['file_id'];
                $cdrfile_name = Helpers_Upload::get_file_info_with_request_id($data['request_id']);
                       
                if(empty($cdrfile_name['file']))
                {
                    $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII' || $encode_str=='UTF-8'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }
                    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));                                 
                    include '/var/www/html/aies/application/classes/Controller/cron_job/parse_sub/notfound.inc';
                    exit;
                }    
                $data['received_file_path'] = !empty($cdrfile_name['file'])?$cdrfile_name['file']:'';

                
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job/parse_phone/mobilink.inc';

                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        //include 'cron_job/parse_phone/warid.inc';
                        include 'cron_job/parse_phone/mobilink.inc';    

                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        include 'cron_job/parse_phone/ufone.inc';
                        // echo '<pre>';
                        // print_r($data['received_file_path']);                      

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' . '<br>';
                        //print_r($data['received_file_path']);
                        include 'cron_job/parse_phone/telenor.inc';



                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                        
                        //print_r($data['received_file_path']);
                        include 'cron_job/parse_phone/zong.inc';

                        break;
                }

                if ($not_fount != 1) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    $reference_number = Model_Email::email_status($reference_number, 2, 5);
                    /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                      { */
                    $sub_model = new Model_Generic();
                    //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);
                }
                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                //re-throw exception
                //throw new customException($email);
//                echo $e;
//                exit;
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
            }
        }
    }
// ufone 
    public function action_email_parse_phone_3() {
//       phpinfo();
//       exit;
        // echo 'cdr';
        
        $not_fount = 0;
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and (ur.user_request_type_id = 1 or ur.user_request_type_id = 6)
                and ur.company_name = 3
                ORDER BY ur.request_id  ASC
            ";                              //Where t1.user_id = {$user_id}  

        //print_r($sql); exit;
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($parse_data as $data) {
            try {
                $phone_data['company_name'] = $data['company_name'];
                $phone_data['phone_number'] = $data['requested_value'];
             echo    $phone_data['userrequestid'] = $data['request_id'];
                $phone_data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $data['id'] = $data['file_id'] = $phone_data['file_id'];
                $cdrfile_name = Helpers_Upload::get_file_info_with_request_id($data['request_id']);
                       
              
                
                if(empty($cdrfile_name['file']))
                {
                    $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII' || $encode_str=='UTF-8'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }
                    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));                                 
                    include '/var/www/html/aies/application/classes/Controller/cron_job/parse_sub/notfound.inc';
                    if($not_fount != 1)
                    {    
                        $reference_number = $data['request_id'];
                        $reference_number = Model_Email::email_status($reference_number, 2, 5);                        
                    }
                     
                    exit;
                }
                
                $data['received_file_path'] = !empty($cdrfile_name['file'])?$cdrfile_name['file']:'';

               
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job/parse_phone/mobilink.inc';

                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        include 'cron_job/parse_phone/warid.inc';


                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        include 'cron_job/parse_phone/ufone.inc';
                        // echo '<pre>';
                        // print_r($data['received_file_path']);                      

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' . '<br>';
                        //print_r($data['received_file_path']);
                        include 'cron_job/parse_phone/telenor.inc';



                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                        
                        //print_r($data['received_file_path']);
                        include 'cron_job/parse_phone/zong.inc';

                        break;
                }

              
               
                if ($not_fount != 1) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    $reference_number = Model_Email::email_status($reference_number, 2, 5);
                    /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                      { */
                    $sub_model = new Model_Generic();
                    //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);
                }
                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                //re-throw exception
                //throw new customException($email);
                echo $e;
//                exit;
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
            }
        }
    }
// telenor 
    public function action_email_parse_phone_6() {
//       phpinfo();
//       exit;
        // echo 'cdr';
        
        $not_fount = 0;
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and (ur.user_request_type_id = 1 or ur.user_request_type_id = 6)
                and ur.company_name = 6
                ORDER BY ur.request_id  ASC
            ";                              //Where t1.user_id = {$user_id}  

        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($parse_data as $data) {
            try {
                $phone_data['company_name'] = $data['company_name'];
                $phone_data['phone_number'] = $data['requested_value'];
             echo    $phone_data['userrequestid'] = $data['request_id'];
             echo '<br>';
                $phone_data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $data['id'] = $data['file_id'] = $phone_data['file_id'];
                $cdrfile_name = Helpers_Upload::get_file_info_with_request_id($data['request_id']);
                       
                if(empty($cdrfile_name['file']))
                {
                    $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII' || $encode_str=='UTF-8'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }
                    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));                                 
                    include '/var/www/html/aies/application/classes/Controller/cron_job/parse_sub/notfound.inc';
                    exit;
                }    
                $data['received_file_path'] = !empty($cdrfile_name['file'])?$cdrfile_name['file']:'';

                
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job/parse_phone/mobilink.inc';

                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        include 'cron_job/parse_phone/warid.inc';


                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        include 'cron_job/parse_phone/ufone.inc';
                        // echo '<pre>';
                        // print_r($data['received_file_path']);                      

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' . '<br>';
                        //print_r($data['received_file_path']);
                        include 'cron_job/parse_phone/telenor.inc';



                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                        
                        //print_r($data['received_file_path']);
                        include 'cron_job/parse_phone/zong.inc';

                        break;
                }

                if ($not_fount != 1) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    $reference_number = Model_Email::email_status($reference_number, 2, 5);
                    /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                      { */
                    $sub_model = new Model_Generic();
                    //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);
                }
                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                //re-throw exception
                //throw new customException($email);
                echo $e;
                exit;
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
            }
        }
    }
// zong 
    public function action_email_parse_phone_4() {
//       phpinfo();
//       exit;
        // echo 'cdr';
        
        $not_fount = 0;
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and (ur.user_request_type_id = 1 or ur.user_request_type_id = 6)
                and ur.company_name = 4
                ORDER BY ur.request_id  ASC
            ";                              //Where t1.user_id = {$user_id}  

        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($parse_data as $data) {
            try {
                $phone_data['company_name'] = $data['company_name'];
                $phone_data['phone_number'] = $data['requested_value'];
                 $phone_data['userrequestid'] = $data['request_id'];
             //echo '<br>';
                $phone_data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $data['id'] = $data['file_id'] = $phone_data['file_id'];
                $cdrfile_name = Helpers_Upload::get_file_info_with_request_id($data['request_id']);
                       
                if(empty($cdrfile_name['file']))
                {
                    $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII' || $encode_str=='UTF-8'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }
                    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));                                 
                    
                    include '/var/www/html/aies/application/classes/Controller/cron_job/parse_sub/notfound.inc';
                    exit;
                }    
                $data['received_file_path'] = !empty($cdrfile_name['file'])?$cdrfile_name['file']:'';

                
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job/parse_phone/mobilink.inc';

                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        include 'cron_job/parse_phone/warid.inc';


                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        include 'cron_job/parse_phone/ufone.inc';
                        // echo '<pre>';
                        // print_r($data['received_file_path']);                      

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' . '<br>';
                        //print_r($data['received_file_path']);
                        include 'cron_job/parse_phone/telenor.inc';



                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                        
                        //print_r($data['received_file_path']);
                        include 'cron_job/parse_phone/zong.inc';

                        break;
                }

                if ($not_fount != 1) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    $reference_number = Model_Email::email_status($reference_number, 2, 5);
                    /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                      { */
                    $sub_model = new Model_Generic();
                    //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);
                }
                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                //re-throw exception
                //throw new customException($email);
               // echo $e;
               // exit;
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
            }
        }
    }

    public function action_email_parse_imei() {

        echo 'IMEI';

        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and ur.user_request_type_id = 2 
                ORDER BY ur.request_id  DESC
            ";                              //Where t1.user_id = {$user_id}  

              //   and ur.request_id=661186
         
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();
        $not_fount = 0;
        foreach ($parse_data as $data) {
            try {
                $phone_data['company_name'] = $data['company_name'];
                $phone_data['phone_number'] = $data['requested_value'];
             echo   $phone_data['userrequestid'] = $data['request_id'];
             echo '<br>';
                $phone_data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $file_id = $phone_data['file_id'];
                $data['file_id'] = $phone_data['file_id'];
                $cdrfile_name = Helpers_Upload::get_file_info_with_request_id($data['request_id']);
                $data['received_file_path'] = !empty($cdrfile_name['file'])?$cdrfile_name['file']:'';
            
            
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job/parse_imei/mobilink.inc';
                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        //print_r($data['received_file_path']);      
                        include 'cron_job/parse_imei/warid.inc';
                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        // print_r($data['received_file_path']);
                        include 'cron_job/parse_imei/ufone.inc';
                        break;
                    case 6: // Telenor
                        // echo '<br>' . 'Telenor' .'<br>';                        
                        //print_r($data['received_file_path']);
                        include 'cron_job/parse_imei/telenor.inc';

                        break;
                    case 4: // Zong
                        // echo '<br>' . 'Zong' .'<br>';                        
                        include 'cron_job/parse_imei/zong.inc';
                        break;
                }

                /* Insertion Code */
                $reference_number = $data['request_id'];
                /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                  { */
                $sub_model = new Model_Generic();
                //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);

                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                //re-throw exception
                //throw new customException($email);
               // echo $e;
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
            }
        }
    }

    public function action_bparty_table() {
        try {
            $data = Model_Generic::get_bparty_data();
        } catch (Exception $e) {
            
        }
    }

    public function action_family_tree_complete() {
        try {
            $data = Model_Generic::family_tree_complete();
        } catch (Exception $e) {
            
        }
    }

    public function action_resend_in_parse_queue() {
        //$current_ip= $_SERVER['REMOTE_ADDR'];     
        //if($current_ip=='202.125.145.104'){
        try {
            $data = Model_Generic::resend_parse_queue();
        } catch (Exception $e) {
            
        }
    }

    public function action_resend_error_in_queue() {
        try {
            $data = Model_Generic::resend_error_in_queue();
        } catch (Exception $e) {
            
        }
    }
    
    
}
