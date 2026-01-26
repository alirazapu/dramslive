<?php

$call_execute = 0;
$sms_execute = 0;
ini_set('memory_limit', '19999999990024M');
ini_set('wait_timeout', '19999999990024');

$row = 2;
$global_imei=array();

/*
if(!empty($date_time_index))
{    
    $date = ($data[sizeof($data)][$date_time_index]);
    if(ctype_digit(trim($date))==1) 
        $end_date_right = date('Y-m-d H:i:s', ($date));
    else
        $end_date_right = date('Y-m-d H:i:s', strtotime($date));

    // Last date and time 
    if(ctype_digit(trim($date))==1) 
        $time = new DateTime(date('Y-m-d H:i:s', ($date)));
    else 
        $time = new DateTime(date('Y-m-d H:i:s', strtotime($date)));
    
    
    $second = !empty($data[sizeof($data)][$duration_index]) ? $data[sizeof($data)][$duration_index] : 00;
    $time->add(new DateInterval('PT' . $second . 'S'));
    $end_date_right_last = $time->format('Y-m-d H:i:s');
}else{
    $end_date_right = '';
    $end_date_right_last = '';
}

// First date and time 
if(!empty($date_time_index))
{    
    $date = ($data[$row][$date_time_index]);
    if(ctype_digit(trim($date))==1) 
        $date_right = date('Y-m-d H:i:s', ($date));
    else
        $date_right = date('Y-m-d H:i:s', strtotime($date));

    // Last date and time 
    if(ctype_digit(trim($date))==1) 
        $time = new DateTime(date('Y-m-d H:i:s', ($date)));
    else 
        $time = new DateTime(date('Y-m-d H:i:s', strtotime($date)));
    
    $second = !empty($data[$row][$duration_index]) ? $data[$row][$duration_index] : 00;
    $time->add(new DateInterval('PT' . $second . 'S'));
    $date_right_last = $time->format('Y-m-d H:i:s');
}else{
    $date_right = '';
    $date_right_last = '';
}
*/

// Last row
if (!empty($date_time_index)) {
    
     $input = $data[sizeof($data)][$date_time_index];
     $end_date_right_last = Helpers_Utilities::date_checking($input);
//    $date = $data[sizeof($data)][$date_time_index];
//    $end_date_right = Helpers_Utilities::convertExcelDate($date);
//    $time = new DateTime($end_date_right);
//    $second = !empty($data[sizeof($data)][$duration_index]) ? intval($data[sizeof($data)][$duration_index]) : 0;
//    $time->add(new DateInterval('PT' . $second . 'S'));
//    $end_date_right_last = $time->format('Y-m-d H:i:s');
   
} else {
    $end_date_right = '';
    $end_date_right_last = '';
}

// Current row
if (!empty($date_time_index)) {
     $input = $data[$row][$date_time_index];
     $date_right_last = Helpers_Utilities::date_checking($input);   
    
    /*$date = $data[$row][$date_time_index];
    $date_right = Helpers_Utilities::convertExcelDate($date);

    $time = new DateTime($date_right);
    $second = !empty($data[$row][$duration_index]) ? intval($data[$row][$duration_index]) : 0;
    $time->add(new DateInterval('PT' . $second . 'S'));
    $date_right_last = $time->format('Y-m-d H:i:s');*/
} else {
    $date_right = '';
    $date_right_last = '';
}


/*IMSI */
$imsi_number = !empty($data[2][$imsi_index]) ? $data[2][$imsi_index] : '';

/* uSER ID */
if(!empty(Auth::instance()->get_user()))
{
    $user_id = Auth::instance()->get_user()->id;
}else{
    $user_id = 9999;
}


/* temp person id */
$data[$row][$party_aindex] = str_replace("'","",$data[$row][$party_aindex]);
$temp_person_id = substr(trim($data[$row][$party_aindex]), -10);
/* IMEI # */
if(!empty($data[$row][$imei_index]))
{
    $data[$row][$imei_index] = str_replace("'","",$data[$row][$imei_index]);
    if(!is_numeric($data[$row][$imei_index]))
    {
        $temp_imei_number = Helpers_Utilities::remvoe_science($data[$row][$imei_index]);
    }else {
        $temp_imei_number = $data[$row][$imei_index];
    }
    $temp_imei_number = str_pad($temp_imei_number, 15, '0', STR_PAD_RIGHT);
    $temp_imei_number = Helpers_Utilities::find_imei_last_digit($temp_imei_number);
    $global_imei[]= $temp_imei_number;
    }else{
        $temp_imei_number = '';    
    }
/* Step 1 to Step 3 */

if ($temp_person_id == $phone_number) {
    if(empty($userrequestid))
        $userrequestid = '';
    if(empty($request_type))
        $request_type = 1;
        $total_record = sizeof($data)-1;
        $upload_status = 0;   
        Model_Parse::update_file_against_phone($file_id, $phone_number, $total_record, $date_right, $end_date_right_last, $upload_status, $userrequestid);
    /* Step 4 */
    $person_id = Model_Parse::get_person_id($temp_person_id);
    $party_a = $temp_person_id;
    
    if (!empty($temp_imei_number)) {
        $imei_exist_file = 1;
        $imei_number = $temp_imei_number;
        //check imei number in system 
        $imei_exist_db = Model_Parse::check_imei_number($temp_imei_number);
        $device_id= $imei_exist_db;
        /* Step 6 if not exist new entry in person_phone_device */
        if (empty($imei_exist_db)) {            
            $device_id = Model_Parse::inert_imei_number_first($person_id, $party_a, $temp_imei_number, $date_right);
        }
        /* Step 7 */
        $device_id_1 = Model_Parse::update_person_p_device($imei_number, $person_id, $party_a, $date_right, $date_right_last);
                
        /* Step 8 */
           Model_Shortparse::update_person_device_number_change_status($party_a);
         
        /* Step 9 & 10*/
          Model_Shortparse::updateorinsert_person_device_number_withfirstuse($device_id, $party_a, $date_right, $date_right_last);
          
         /* Step 11 */ 
          Model_Shortparse::updateorinsert_person_phone_number_withfirstuse($party_a, $imsi_number, $date_right, $mnc, 1, $user_id, $date_right_last);
          
          Model_Parse::last_interaction_at($person_id, $phone_number, $imei_number, $date_right_last, $device_id, $user_id);          
    } 
    
    /* else {
        $imei_exist_file = 0;       
    }*/
    $sms_table_record = DB::insert('person_sms_log', array('person_id', 'phone_number', 'other_person_phone_number', 'sms_at', 'cell_id', 'address', 'imei_number', 'is_outgoing', 'latitude', 'longitude', 'imsi_number', 'mnc', 'upload_at'));
    $call_table_record = DB::insert('person_call_log', array('person_id', 'phone_number', 'other_person_phone_number', 'call_at', 'call_end_at', 'duration_in_seconds', 'cell_id', 'address', 'imei_number', 'is_outgoing', 'latitude', 'longitude', 'imsi_number', 'mnc'));

    $date = date('Y-m-d H:i:s');
    $mid = 1;

    /* Check CDR Exist or not */
    $date_last_record = ($data[sizeof($data)][$date_time_index]);
    if(ctype_digit(trim($date_last_record))==1) 
        $date_last = date('Y-m-d H:i:s', ($date_last_record));
     else
        $date_last = date('Y-m-d H:i:s', strtotime($date_last_record));

    $cdr_check = new Model_parse;
    $check_cdr = $cdr_check->cdr_exist($person_id, $party_a, $date_right, $date_last);
    $check_cdr_range = $cdr_check->cdr_date_rang($person_id, $party_a, $date_right, $date_last);
    $last_lat ='';
    $last_long = '';
    $last_address = '';
    
    //echo 'ttt cdr exist ' . $check_cdr;    
    //if (empty($check_cdr)) {        
        for ($row = 2; $row <= sizeof($data); $row++) {
            
            if(!empty($data[$row][$date_time_index]) && !empty($data[$row][$party_bindex]) && !empty($data[$row][$party_aindex]))
            {
                
                
            /* First date and time */            
            $date_check = ($data[$row][$date_time_index]);
            /*if(ctype_digit(trim($date_check))==1) 
                $date_right = date('Y-m-d H:i:s', ($date_check));
            else
                $date_right = date('Y-m-d H:i:s', strtotime($date_check));
          // Last date and time 
            if(ctype_digit(trim($date_check))==1) 
                $time = new DateTime(date('Y-m-d H:i:s', ($date_check)));
            else
                $time = new DateTime(date('Y-m-d H:i:s', strtotime($date_check)));
            
            $second = !empty($data[$row][$duration_index]) ? (int) $data[$row][$duration_index] : 00;
            $time->add(new DateInterval('PT' . $second . 'S'));
            $date_right_last = $time->format('Y-m-d H:i:s');
            */
            
            
            $date = $data[$row][$date_time_index];
            $date_right = Helpers_Utilities::date_checking($date);   
            $second = !empty($data[$row][$duration_index]) ? intval($data[$row][$duration_index]) : 0;
            $date_right_last = Helpers_Utilities::date_extended_with_second($date_right, $second);   
            
            /*
            $date_right = Helpers_Utilities::convertExcelDate($date);
            $time = new DateTime($date_right);
            $second = !empty($data[$row][$duration_index]) ? intval($data[$row][$duration_index]) : 0;
            $time->add(new DateInterval('PT' . $second . 'S'));
            $date_right_last = $time->format('Y-m-d H:i:s');*/
            
            
/* var_dump($date_right);
var_dump($date_right_last);
exit;     */       

            /* check date exist in db */
            if ((strtotime($date_right) > strtotime($check_cdr_range['min'])) && (strtotime($date_right) < strtotime($check_cdr_range['max'])))
            {
                if ($mid == 5000 || $row == (sizeof($data))) {
                    if ($call_execute == 1)
                        $call_table_record->execute();

                    if ($sms_execute == 1)
                        $sms_table_record->execute();

                    $sms_table_record = DB::insert('person_sms_log', array('person_id', 'phone_number', 'other_person_phone_number', 'sms_at', 'cell_id', 'address', 'imei_number', 'is_outgoing', 'latitude', 'longitude', 'imsi_number', 'mnc', 'upload_at'));
                    $call_table_record = DB::insert('person_call_log', array('person_id', 'phone_number', 'other_person_phone_number', 'call_at', 'call_end_at', 'duration_in_seconds', 'cell_id', 'address', 'imei_number', 'is_outgoing', 'latitude', 'longitude', 'imsi_number', 'mnc'));

                    if(!empty($last_long) && !empty($last_lat))
                    Helpers_Upload::person_location_history($person_id, $party_a, "{$date_right}", "{$date_right_last}", "{$last_address}", $last_long, $last_lat, $mnc, $cel_lac, $cell_site);

                    $mid = 1;
                }                
                continue;
            }            
            //exit;
            $party_b = '';  
            // Clean the input: remove spaces, non-digits, and single quotes
            $data[$row][$party_bindex] = preg_replace('/[^\d]/u', '', $data[$row][$party_bindex]);

            // Proceed only if the cleaned data is not empty
            if (!empty($data[$row][$party_bindex])) {
                $cleaned_number = $data[$row][$party_bindex];
                $length = strlen($cleaned_number);
                $prefix = substr($cleaned_number, 0, 3);
                $first_two = substr($cleaned_number, 0, 2);

                // Conditions for handling based on length and prefix
                if ($length > 10 && ($prefix == '923' || $prefix == '383' || $first_two == '03')) {
                    // Handle cases where length is greater than 10
                    $party_b = $length <= 8 
                        ? trim($cleaned_number) 
                        : Helpers_Upload::number_clean(substr($cleaned_number, -10));
                } else {
                    // Handle other cases
                    $party_b = $length <= 8 
                        ? trim($cleaned_number) 
                        : Helpers_Upload::number_clean($cleaned_number);
                }
            }
//            $party_b = '';            
//            $data[$row][$party_bindex] = preg_replace('/[^\d]/u', '', preg_replace('/\s+/', '', $data[$row][$party_bindex]));
//            
//            if (!empty($data[$row][$party_bindex])) { 
//                $data[$row][$party_bindex] = str_replace("'","",$data[$row][$party_bindex]);
//                
//                if (strlen($data[$row][$party_bindex]) > 10 && (substr($data[$row][$party_bindex], 0, 3) == 923 || substr($data[$row][$party_bindex], 0, 3) == 383 || (substr($data[$row][$party_bindex], 0, 2) == 03))) {
//                    if (strlen($data[$row][$party_bindex]) <= 8)
//                        $party_b = trim($data[$row][$party_bindex]);
//                    else    
//                        $party_b = Helpers_Upload::number_clean(substr($data[$row][$party_bindex], -10));
//                } else {
//                    
//                    if (strlen($data[$row][$party_bindex]) <= 8)
//                        $party_b = trim($data[$row][$party_bindex]);
//                    else    
//                        $party_b = Helpers_Upload::number_clean($data[$row][$party_bindex]);
//                }
//            }
            $duration = !empty($data[$row][$duration_index]) ? $data[$row][$duration_index] : '';
            $op_type = !empty($data[$row][$call_type_index]) ? $data[$row][$call_type_index] : '';
             $cel_lac = '';
             $cell_site = !empty($data[$row][$cell_site_index]) ? $data[$row][$cell_site_index] : '';
            
            
            $imsi_number = !empty($data[$row][$imsi_index]) ? $data[$row][$imsi_index] : '';

            $address = !empty($data[$row][$site_index]) ? $data[$row][$site_index] : '';
            $temp_address = explode(' ', $address);
            
           /* if($party_a == '3032552287')
            {
                
            //    print_r($address);
                print_r($temp_address);
                echo '<br>';
                $address= str_replace('"','',$address);
                 $temp_address = explode('|', $address);
                 print_r(array_filter($temp_address));
                 //array_filter
                echo '<br>';
                echo strpos($temp_address[sizeof($temp_address) - 1], '/');
                
                exit;
            }*/    
            
            if (strpos($temp_address[sizeof($temp_address) - 1], '/') !== false) {
                $temp_address1 = explode('/', $temp_address[sizeof($temp_address) - 1]);                
                $lat = !empty($temp_address1[0]) ? $temp_address1[0] : '';
                $lng = !empty($temp_address1[1]) ? $temp_address1[1] : '';
            } else {
                $address= str_replace('"','',$address); 
                $temp_address = explode('|', $address); 
                $temp_address = array_filter($temp_address);
                $lat = !empty($temp_address[sizeof($temp_address) - 1]) ? $temp_address[sizeof($temp_address) - 1] : '';
                $lng = !empty($temp_address[sizeof($temp_address) - 2]) ? $temp_address[sizeof($temp_address) - 2] : '';
            }
            
            
            if ($lat > $lng) {
                $helper = $lat;
                $lat = $lng;
                $lng = $helper;
            }
            
            if(!empty($lat) && !empty($lng))
            {
                $last_lat =$lat;
                $last_long = $lng;
                $last_address = $address; 
            }    
          /*  if(!empty($data[$row][$imei_index]))
            { 
                $data[$row][$imei_index] = str_replace("'","",$data[$row][$imei_index]);
                if(!is_numeric($data[$row][$imei_index]))
                {
                    $imei_number = Helpers_Utilities::remvoe_science($data[$row][$imei_index]);
                }else {
                    $imei_number = $data[$row][$imei_index];
                }                
              $imei_number = str_pad($imei_number, 15, '0', STR_PAD_RIGHT);
              $imei_number = Helpers_Utilities::find_imei_last_digit($imei_number);
              if (!in_array($imei_number, $global_imei)) {
                        $global_imei[]= $imei_number;
              }          
            }else{
            $temp_imei_number = '';    
            } */
            $data[$row][$party_aindex] = str_replace("'","",$data[$row][$party_aindex]);
            $party_b_check = substr(trim($data[$row][$party_aindex]), -10);
            if($party_a!=$party_b_check)
            {
                if(!empty($userrequestid)){
                    Model_ErrorLog::log(
                        'parse_cdr_jazz',
                        'Party A mismatch detected - CDR party_a does not match expected phone number',
                        array(
                            'request_id' => $userrequestid,
                            'processing_index' => 3,
                            'file_id' => !empty($file_id) ? $file_id : null,
                            'expected_party_a' => $party_a,
                            'actual_party_a' => $party_b_check,
                            'row' => $row
                        ),
                        null,
                        'validation_error',
                        'cdr_parsing'
                    );
                    $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                }
                if(!empty($file_id))
                  $error_number = Model_Email::file_status($file_id, 3, 3);				  
				
                echo '3';
                exit;
            }    
            
            if ($row != 2){
                if(!empty($data[$row][$imei_index])) {
                    
                    $data[$row][$imei_index] = str_replace("'","",$data[$row][$imei_index]);
                    
                    if(!is_numeric($data[$row][$imei_index]))
                    {
                        $imei_number = Helpers_Utilities::remvoe_science($data[$row][$imei_index]);
                    }else {
                        $imei_number = $data[$row][$imei_index];
                    }
                    $imei_number = str_pad($imei_number, 15, '0', STR_PAD_RIGHT);
                    $imei_number = Helpers_Utilities::find_imei_last_digit($imei_number);
                    
                    
                    //$imei_number_c = Helpers_Utilities::remvoe_science($data[$row-1][$imei_index]);
                    //$imei_number_c = str_pad($imei_number_c, 15, '0', STR_PAD_RIGHT);
                    //$imei_number_c = Helpers_Utilities::find_imei_last_digit($imei_number_c);
                /*    
                if ($imei_number_c != $imei_number) {
                    $imei_exist_file = 1;
                    $temp_imei_number = $imei_number;
                    //check imei number in system 
                    $imei_exist_db = Model_Parse::check_imei_number($temp_imei_number);
                    $device_id= $imei_exist_db;
                                                           
                    /// Step 7 
                    $device_id_1 = Model_Parse::update_person_p_device($imei_number, $person_id, $party_a, $date_right, $date_right_last);

                    
                    // Step 9 & 10
                      Model_Shortparse::updateorinsert_person_device_number_withfirstuse($device_id, $party_a, $date_right, $date_right_last);

                     // Step 11  
                      Model_Shortparse::updateorinsert_person_phone_number_withfirstuse($party_a, $imsi_number, $date_right, $mnc, 1, $user_id, $date_right_last);
                      Model_Parse::last_interaction_at($person_id, $phone_number, $imei_number, $date_right_last, $device_id, $user_id);
                    
                      $imei_number = Helpers_Utilities::remvoe_science($data[$row][$imei_index]);
                      $imei_number = str_pad($imei_number, 15, '0', STR_PAD_RIGHT);
                      $imei_number = Helpers_Utilities::find_imei_last_digit($imei_number);
                }                    
                */
                //if ($imei_number_c != $imei_number) {
                 if (!in_array($imei_number, $global_imei)) {
                        $global_imei[]= $imei_number;
                    $imei_exist_file = 1;
                    $temp_imei_number = $imei_number;
                    
                    
                    //check imei number in system 
                    $imei_exist_db = Model_Parse::check_imei_number($temp_imei_number);
                    $device_id= $imei_exist_db;
                    /* Step 6 if not exist new entry in person_phone_device */
                    if (empty($imei_exist_db)) {                        
                        $device_id = Model_Parse::inert_imei_number_first($person_id, $party_a, $temp_imei_number, $date_right);                    
                    }                                        
                    /* Step 7 */
                    $device_id_1 = Model_Parse::update_person_p_device($imei_number, $person_id, $party_a, $date_right, $date_right_last);

                    /* Step 8 */
                       Model_Shortparse::update_person_device_number_change_status($party_a);

                    /* Step 9 & 10*/
                      Model_Shortparse::updateorinsert_person_device_number_withfirstuse($device_id, $party_a, $date_right, $date_right_last);

                     /* Step 11 */ 
                      Model_Shortparse::updateorinsert_person_phone_number_withfirstuse($party_a, $imsi_number, $date_right, $mnc, 1, $user_id, $date_right_last);
                      Model_Parse::last_interaction_at($person_id, $phone_number, $imei_number, $date_right_last, $device_id, $user_id);
                }
                }
            }

            //$lng = !empty($data[$row][10]) ? $data[$row][10] : '';
            //$lat = !empty($data[$row][11]) ? $data[$row][11] : '';                 
            switch (trim(strtolower($op_type))) {
                case 'outgoing sms':
                    $sms_execute = 1;
                    $sms_table_record->values(array($person_id, $party_a, $party_b, "{$date_right}", '', "{$address}", $imei_number, 1, $lat, $lng, $imsi_number, $mnc, "{$date}"));
                    //Model_Parse::person_sms_log($person_id, $party_a, $party_b, $date_right, '', $address, $imei_number, 1, $latitude='', $longitude='', $imsi_number='', $mnc);
                    break;
                case 'incoming sms':
                    $sms_execute = 1;
                    $sms_table_record->values(array($person_id, $party_a, $party_b, "{$date_right}", '', "{$address}", $imei_number, 0, $lat, $lng, $imsi_number, $mnc, "{$date}"));
                    //Model_Parse::person_sms_log($person_id, $party_a, $party_b, $date_right, '', $address, $imei_number, 0, $latitude='', $longitude='', $imsi_number='', $mnc);
                    break;
                case 'outgoing':
                    $call_execute = 1;
                    $call_table_record->values(array($person_id, $party_a, $party_b, "{$date_right}", "{$date_right_last}", $duration, '', "{$address}", $imei_number, 1, $lat, $lng, $imsi_number, $mnc));
                    //Model_Parse::person_call_log($person_id, $party_a, $party_b, $date_right, $date_right_last, $duration, '', $address, $imei_number, 1, $latitude='', $longitude='', $imsi_number='', $mnc);
                    break;
                case 'incoming':
                    $call_execute = 1;
                    $call_table_record->values(array($person_id, $party_a, $party_b, "{$date_right}", "{$date_right_last}", $duration, '', "{$address}", $imei_number, 0, $lat, $lng, $imsi_number, $mnc));
                    //Model_Parse::person_call_log($person_id, $party_a, $party_b, $date_right, $date_right_last, $duration, '', $address, $imei_number, 0, $latitude='', $longitude='', $imsi_number='', $mnc);
                    break;
            }
            if ($mid == 5000 || $row == (sizeof($data))) {
                if ($call_execute == 1)
                    $call_table_record->execute();

                if ($sms_execute == 1)
                    $sms_table_record->execute();

                $sms_table_record = DB::insert('person_sms_log', array('person_id', 'phone_number', 'other_person_phone_number', 'sms_at', 'cell_id', 'address', 'imei_number', 'is_outgoing', 'latitude', 'longitude', 'imsi_number', 'mnc', 'upload_at'));
                $call_table_record = DB::insert('person_call_log', array('person_id', 'phone_number', 'other_person_phone_number', 'call_at', 'call_end_at', 'duration_in_seconds', 'cell_id', 'address', 'imei_number', 'is_outgoing', 'latitude', 'longitude', 'imsi_number', 'mnc'));

                if(!empty($last_long) && !empty($last_lat))
                Helpers_Upload::person_location_history($person_id, $party_a, "{$date_right}", "{$date_right_last}", "{$last_address}", $last_long, $last_lat, $mnc, $cel_lac, $cell_site);
                
                $mid = 1;
            }
            $mid++;
            }
        }
//if ($row == $highestRow) {//Last entry in person phone device 
        /* last time insertion
         */
        //hide by me. if need then uncomment
        //$device_id = Model_Parse::inert_imei_number_last($person_id, $party_a, $imei_number, $date_right_last, $device_id);
        Model_Parse::last_interaction_at($person_id, $phone_number, $imei_number, $date_right_last, $device_id, $user_id);

        /*
         *  Person Summary Helper
         */

        $result = Helpers_Upload::person_summary($party_a, $person_id);
        /* Monthly Summary */
        $result = Helpers_Upload::monthly_summary($party_a, $person_id);
        /* Location History */
        //$result = Helpers_Upload::person_location_history($person_id, $party_a, "{$date_right}", "{$date_right_last}", "{$last_address}", $last_long, $last_lat, $mnc);

    
//   }
    /*}
    else{        
        if(!empty($userrequestid)){
            Model_ErrorLog::log(
                'parse_cdr_jazz',
                'CDR already exists in database - duplicate upload detected',
                array(
                    'request_id' => $userrequestid,
                    'processing_index' => 3,
                    'file_id' => !empty($file_id) ? $file_id : null,
                    'person_id' => !empty($person_id) ? $person_id : null,
                    'party_a' => !empty($party_a) ? $party_a : null
                ),
                null,
                'validation_error',
                'cdr_parsing'
            );
            $reference_number = Model_Email::email_status($userrequestid, 2, 3);
        }
    if(!empty($file_id))
        {   
            $error_number = Model_Email::file_status($file_id, 5, 3);
            exit;
        }
    }*/
}else {
    if(!empty($userrequestid)){
        Model_ErrorLog::log(
            'parse_cdr_jazz',
            'Phone number mismatch - temp_person_id does not match expected phone_number',
            array(
                'request_id' => $userrequestid,
                'processing_index' => 3,
                'file_id' => !empty($file_id) ? $file_id : null,
                'expected_phone_number' => !empty($phone_number) ? $phone_number : null,
                'temp_person_id' => !empty($temp_person_id) ? $temp_person_id : null
            ),
            null,
            'validation_error',
            'cdr_parsing'
        );
        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
    }
    if(!empty($file_id))
           $error_number = Model_Email::file_status($file_id, 2, 3);
    
    echo '2';
    exit;
}
unset($data);