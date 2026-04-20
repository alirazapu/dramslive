<?php

$from = '';
$to = '';
$to_old = '';
                
$call_execute = 0;
$sms_execute = 0;
ini_set('memory_limit', '19999999990024M');
ini_set('wait_timeout', '19999999990024');
$imei = $data[sizeof($data) - 1][$imei_index];
$total_record = sizeof($data)-1;
/*date */
/* First date and time */
if(!empty($date_times_index))
{   
    $to='';
    $from = '';
    if(!empty($data[2][$date_times_index]))
    {    
    if(ctype_digit(trim($data[2][$date_times_index]))==1) 
        $from = !empty($data[2][$date_times_index]) ? date('Y-m-d H:i:s', ($data[2][$date_times_index])) : '';
    else
        $from = !empty($data[2][$date_times_index]) ? date('Y-m-d H:i:s', strtotime($data[2][$date_times_index])) : '';
    }
    /* Last date and time */
    if(!empty($data[2][$date_timee_index]))
    {    
    if(ctype_digit(trim($data[2][$date_timee_index]))==1) 
        $to = !empty($data[2][$date_timee_index]) ? date('Y-m-d H:i:s', $data[2][$date_timee_index]) : '';
    else
        $to = !empty($data[2][$date_timee_index]) ? date('Y-m-d H:i:s', strtotime($data[2][$date_timee_index])) : '';
    }
}else{
    $from = '';
    $to = '';
}
//$from = !empty($data[2][$date_times_index]) ? date('Y-m-d H:i:s', ($data[2][$date_times_index])) : '';
/* Last date and time */
//$to = !empty($data[2][$date_timee_index]) ? date('Y-m-d H:i:s', $data[2][$date_timee_index]) : '';

/*company name*/
$company_mnc = 3;

$upload_status = 1;
$request_type = 2;
$party_a = substr($data[2][$party_aindex], -10);
$user_obj = Auth::instance()->get_user();
$imsi_number = !empty($data[2][$imsi_index]) ? $data[2][$imsi_index] : '';
$status = 1;
$imei = Helpers_Utilities::remvoe_science($data[2][$imei_index]);
$imei = Model_Generic::imei_finder(substr($imei, 0, 14));
$imei = str_pad($imei, 15, '0', STR_PAD_RIGHT);
$imei = Helpers_Utilities::find_imei_last_digit($imei);

if(!empty(Auth::instance()->get_user()))
{
    $user_id = Auth::instance()->get_user()->id;
}else{
    $user_id = 9999;
}

/*
   1.	Get IMEI from input box
   2.	Get IME FROM CDR
   3.	Cross check if match then move for step 4 otherwise return error message  
 */

$field_imei_no = Model_Generic::imei_finder(substr($field_imei_no, 0, 14));

// Fetch force_imei_last_digit_zero flag for this request
$force_imei_last_digit_zero = 0;
if (!empty($userrequestid)) {
    $flag_query = DB::query(Database::SELECT, "SELECT force_imei_last_digit_zero FROM user_request WHERE request_id = " . (int)$userrequestid);
    $flag_result = $flag_query->execute()->current();
    if (!empty($flag_result) && !empty($flag_result['force_imei_last_digit_zero'])) {
        $force_imei_last_digit_zero = 1;
    }
}

// When force_imei_last_digit_zero is set, compare only first 14 digits to accept Ufone CDR that has 15th digit=0
if ($force_imei_last_digit_zero == 1) {
    $imei_match = (substr($field_imei_no, 0, 14) === substr($imei, 0, 14));
} else {
    $imei_match = ($field_imei_no == $imei);
}

if ($imei_match) {
    /* IMEI file */
    Model_Parse::update_file_against_phone($file_id, $imei, $total_record, $from, $to, $upload_status, $request_type);
    /* 4.	Get device id from Person_phone_device */
    $device_id = Model_Parse::check_imei_number($imei);
    if (empty($device_id)) {
        /* If not exist new entry without person_id  */
        $device_id = Model_Shortparse::insert_person_phone_device($imei,$from, $user_id);
    }
    /* 5.	Get distinct phone number from CDR   */
    if(!empty($party_a))
    {    
        /* 6.	Change flag is_active to 0 in person_device_number against mobile number  */
        Model_Shortparse::update_person_device_number_change_status($party_a);
        /*
            7.	If mobile number exist against device_id in person_device_number then change flag is_active to 1 and update sim_last_use date & user_id
            8.	If mobile number not exist against device_id in person_device_number then new entry.
         */
        Model_Shortparse::updateorinsert_person_device_number_withfirstuse($device_id, $party_a, $from, $to);
        /*
         * 9.	Check mobile number exist in person_phone_number if exist update sim_last_use, mnc. status,imsi,user_id
         *      if not exist then enter without sim_owner,person_id
         */
        Model_Shortparse::updateorinsert_person_phone_number_withfirstuse($party_a, $imsi_number, $from, $company_mnc, 1, $user_id, $to);
    }
    
    if(sizeof($data)>2)
    {
        for ($row = 3; $row <= sizeof($data); $row++) {
            
            $imei_new = Helpers_Utilities::remvoe_science($data[$row][$imei_index]);
            $imei_new = Model_Generic::imei_finder(substr($imei_new, 0, 14));
            $imei_new = str_pad($imei_new, 15, '0', STR_PAD_RIGHT);
            $imei_new = Helpers_Utilities::find_imei_last_digit($imei_new);
            
            if($force_imei_last_digit_zero == 1) {
                $row_imei_mismatch = (substr($imei, 0, 14) !== substr($imei_new, 0, 14));
            } else {
                $row_imei_mismatch = ($imei != $imei_new);
            }
            if($row_imei_mismatch)
            {
                if(!empty($userrequestid)){
                    Model_ErrorLog::log(
                        'parse_partially_ufone',
                        'IMEI mismatch detected during CDR parsing - expected IMEI does not match IMEI in row ' . $row,
                        array(
                            'request_id' => $userrequestid,
                            'processing_index' => 3,
                            'file_id' => $file_id,
                            'expected_imei' => $imei,
                            'found_imei' => $imei_new,
                            'row_number' => $row
                        ),
                        null,
                        'validation_error',
                        'cdr_partial_parsing'
                    );
                    $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                }
                if(!empty($file_id))
                  $error_number = Model_Email::file_status($file_id, 6, 3);
                echo '4';
                exit;
            }    
            
            $past_row = $row - 1;
            /* First date and time */
            
            if(!empty($date_times_index))
            {   
                $to='';
                $from = '';
                if(!empty($data[$row][$date_times_index]))
                {    
                    if(ctype_digit(trim($data[$row][$date_times_index]))==1) 
                        $from = !empty($data[$row][$date_times_index]) ? date('Y-m-d H:i:s', ($data[$row][$date_times_index])) : '';
                    else
                        $from = !empty($data[$row][$date_times_index]) ? date('Y-m-d H:i:s', strtotime($data[$row][$date_times_index])) : '';
                }
                /* Last date and time */
                if(!empty($data[$row][$date_timee_index]))
                {    
                    if(ctype_digit(trim($data[$row][$date_timee_index]))==1) 
                        $to = !empty($data[$row][$date_timee_index]) ? date('Y-m-d H:i:s', $data[$row][$date_timee_index]) : '';
                    else
                        $to = !empty($data[$row][$date_timee_index]) ? date('Y-m-d H:i:s', strtotime($data[$row][$date_timee_index])) : '';
                }
                if(!empty($data[$past_row][$date_timee_index]))
                {    
                if(ctype_digit(trim($data[$past_row][$date_timee_index]))==1) 
                        $to_old = !empty($data[$past_row][$date_timee_index]) ? date('Y-m-d H:i:s', $data[$past_row][$date_timee_index]) : '';
                    else
                        $to_old = !empty($data[$past_row][$date_timee_index]) ? date('Y-m-d H:i:s', strtotime($data[$past_row][$date_timee_index])) : '';
                }
            }else{
                $from = '';
                $to = '';
                $to_old = '';
            }
            
            
            $imsi_number = !empty($data[$row][$imsi_index]) ? $data[$row][$imsi_index] : '';
            if ($party_a != substr($data[$row][$party_aindex], -10)) {
                /* 9 and 7 update sim_last_use */
                Model_Shortparse::updateorinsert_person_device_number_withlastuse($device_id, $party_a, $to_old);
                Model_Shortparse::updateorinsert_person_phone_number_withfirstuse($party_a, $imsi_number, $to_old, 1, 1, $user_id);
                $party_a = substr($data[$row][$party_aindex], -10);
                if(!empty($party_a))
                {    
                    /* 6.	Change flag is_active to 0 in person_device_number against mobile number  */
                    Model_Shortparse::update_person_device_number_change_status($party_a);
                    /*
                        7.	If mobile number exist against device_id in person_device_number then change flag is_active to 1 and update sim_last_use date & user_id
                        8.	If mobile number not exist against device_id in person_device_number then new entry.
                     */
                    Model_Shortparse::updateorinsert_person_device_number_withfirstuse($device_id, $party_a, $from, $to);
                    /*
                     * 9.	Check mobile number exist in person_phone_number if exist update sim_last_use, mnc. status,imsi,user_id
                     *      if not exist then enter without sim_owner,person_id
                     */
                    Model_Shortparse::updateorinsert_person_phone_number_withfirstuse($party_a, $imsi_number, $from, $company_mnc, 1, $user_id, $to);
                }                
            }
        }        
    }
     if(!empty($userrequestid)){
        Model_ErrorLog::log(
            'parse_partially_ufone',
            'CDR parsing completed successfully but no matching records found for processing',
            array(
                'request_id' => $userrequestid,
                'processing_index' => 5,
                'file_id' => $file_id,
                'total_records' => $total_record,
                'imei' => $imei
            ),
            null,
            'not_found',
            'cdr_partial_parsing'
        );
        $reference_number = Model_Email::email_status($userrequestid, 2, 5);
    }
    if(!empty($file_id))
           $error_number = Model_Email::file_status($file_id, 0, 1);
    
    echo '1';
    exit;
} else {
     if(!empty($userrequestid)){
        Model_ErrorLog::log(
            'parse_partially_ufone',
            'Initial IMEI validation failed - input IMEI does not match first CDR record IMEI',
            array(
                'request_id' => $userrequestid,
                'processing_index' => 3,
                'file_id' => $file_id,
                'input_imei' => $field_imei_no,
                'cdr_imei' => $imei
            ),
            null,
            'validation_error',
            'cdr_partial_parsing'
        );
        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
    }
    if(!empty($file_id))
           $error_number = Model_Email::file_status($file_id, 4, 3);
    echo '2';
    exit;
}

