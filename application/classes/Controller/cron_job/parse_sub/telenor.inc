<?php

// Check if file exists, parse file first before falling back to email body
if(!empty($data['received_file_path']) && $data['received_file_path'] != 'na') {
    // File exists, parse it to extract subscriber information
    $ext = pathinfo($data['received_file_path'], PATHINFO_EXTENSION);
    $base_path = !empty($data['file_id']) ? Helpers_Upload::get_request_data_path($data['file_id'], 'save') : '';
    
    $file_to_parse = '';
    $should_delete = false;
    
    if($ext == 'zip') {
        $file = $base_path . $data['received_file_path'];
        $file_name = Helpers_Upload::unzip_file_multiple($file, $data['requested_value']);
        $file_to_parse = $base_path . $file_name;
        $should_delete = true;
    } elseif($ext == 'rar') {
        $file = $base_path . $data['received_file_path'];
        $file_name = Helpers_Upload::unziprar_file($file, $data['request_id']);
        $file_to_parse = $base_path . $file_name;
        $should_delete = true;
    } else {
        $file_to_parse = $base_path . $data['received_file_path'];
    }
    
    // Parse the file using telenor_cnic_sub helper
    $reference_number = $data['request_id'];
    $cnic_data = Helpers_Upload::telenor_cnic_sub($file_to_parse, $reference_number);
    
    // Clean up extracted file if needed
    if($should_delete && file_exists($file_to_parse)) {
        unlink($file_to_parse);
    }
    
    // Extract subscriber information from parsed data
    // Expected format: Row 0 = headers, Row 1+ = data
    // Columns: CNIC, MSISDN, FULL_NAME, ADDRESS, ACTIVATION_DATE
    if(!empty($cnic_data) && isset($cnic_data[1])) {
        $mobile_number = isset($cnic_data[1][1]) ? substr(trim($cnic_data[1][1]), -10) : '';
        $cnic = isset($cnic_data[1][0]) ? trim($cnic_data[1][0]) : '';
        $name = isset($cnic_data[1][2]) ? trim($cnic_data[1][2]) : '';
        $address = isset($cnic_data[1][3]) ? trim($cnic_data[1][3]) : '';
        $date = isset($cnic_data[1][4]) ? $cnic_data[1][4] : '';
        $active = 'Active'; // Default to active for file-based data
        $status = ''; // Default status
        
        // Remove dashes from CNIC if present
        $cnic = str_replace('-', '', $cnic);
        
        // Check if CNIC is foreigner CNIC
        if(!ctype_digit(trim($cnic)) && strlen(trim($cnic))==13) {
            $cnic_original = trim($cnic);
            $is_foreigner = 1;
        }
        
        // Set not_fount = 0 so that Cronjob.php will process and insert the data
        $not_fount = 0;
    } else {
        // No valid data in file, mark as not found
        $not_fount = 1;
    }
    $is_file=true;
} else {
    $is_file=false;
    // No file, parse from email body (original logic)
    $encode_str= mb_detect_encoding(base64_decode($data['received_body']));
    if($encode_str=='ASCII')
        $data['received_body'] = base64_decode($data['received_body']);    


    $data['received_body'] = array_filter(explode('From:', strip_tags($data['received_body'])));
    $data['received_body'] = array_filter(explode('- Sent', strip_tags($data['received_body'][0])));
    //$data['received_body'] = str_replace(" -","",$data['received_body']);
    //$data['received_body'] = str_replace("-","",$data['received_body']);
    //$data['received_body'] = str_replace(PHP_EOL . PHP_EOL . PHP_EOL,PHP_EOL,$data['received_body']);                        
    $total_sub = substr_count($data['received_body'][0], 'MSISDN:');

    /* Not Found */
    include 'notfound.inc';
}
 
if ($not_fount!=1 && !$is_file){
    if ($total_sub == 1) {

        $data['received_body'] = $data['received_body'][0];
        /* Replace String */
        include 'replace.inc';
        $data['received_body'] = str_replace('.','',$data['received_body']);
        $data['received_body'] = str_replace(PHP_EOL,'',$data['received_body']);
        $second_array = array_filter(explode('Name:', strip_tags($data['received_body'])));

        if(sizeof($second_array)==1 && (trim($second_array[0]) == 'MSISDN:0' . $data['requested_value']))
        {
            //$data = Model_Generic::resend_error_id_queue($data['request_id']);

            Model_ErrorLog::log(
                'parse_sub_telenor',
                'Incomplete response - only MSISDN present without subscriber details - marking as status 3 (Error)',
                [
                    'request_id' => $data['request_id'],
                    'company_name' => $data['company_name'] ?? 'telenor',
                    'user_request_type_id' => $data['user_request_type_id'] ?? null,
                    'processing_index' => 3,
                    'requested_value' => $data['requested_value'] ?? '',
                    'body_sample' => isset($second_array[0]) ? substr($second_array[0], 0, 200) : '',
                    'reason' => 'Response contains only MSISDN without Name/Address/CNIC fields'
                ],
                null,
                'parsing_error',
                'subscriber_parsing'
            );

            $data = Model_Email::email_status($data['request_id'], 2, 3);
            exit;
        }


        $mobile_number = str_replace('MSISDN:', '', $second_array[0]);
        $mobile_number=substr(trim($mobile_number), -10);
        $second_array = array_filter(explode('Address:', strip_tags($second_array[1])));
        $name = str_replace('Name:', '', $second_array[0]);
        $second_array = array_filter(explode('CNIC:', strip_tags($second_array[1])));
        $address = str_replace('Address:', '', $second_array[0]);
        $cnic = str_replace('CNIC:', '', $second_array[1]);
        $cnic = str_replace('-', '', $cnic);


    } else {
            $data['received_body'] = $data['received_body'][0];
            $data['received_body'] = str_replace('Sold on CNIC #', '', $data['received_body']);
            $data['received_body'] = str_replace('Msisdn', '', $data['received_body']);
            $data['received_body'] = str_replace('Cnic', '', $data['received_body']);
            $data['received_body'] = str_replace('Ni l', '', $data['received_body']);
            $data['received_body'] = str_replace('Customer', '', $data['received_body']);
            $data['received_body'] = str_replace('Father', '', $data['received_body']);
            $data['received_body'] = str_replace('*Msisdn*', '', $data['received_body']);
            $data['received_body'] = str_replace('*Cnic*', '', $data['received_body']);
            $data['received_body'] = str_replace('*Customer Name*', '', $data['received_body']);
            $data['received_body'] = str_replace('*Father Name*', '', $data['received_body']);
            $data['received_body'] = str_replace('*Address*', '', $data['received_body']);
            $data['received_body'] = str_replace('/', '', $data['received_body']);
            $data['received_body'] = str_replace('-', '', $data['received_body']);
            $data['received_body'] = str_replace('MSISDN', '', $data['received_body']);
            $data['received_body'] = str_replace('Name', '', $data['received_body']);
            $data['received_body'] = str_replace('Address', '', $data['received_body']);
            $data['received_body'] = str_replace('CNIC', '', $data['received_body']);
            $data['received_body'] = str_replace(':', '', $data['received_body']);
            $data['received_body'] = str_replace('Sold on', '', $data['received_body']);
            $data['received_body'] = str_replace('Afghan', '', $data['received_body']);
            $data['received_body'] = str_replace('card', '', $data['received_body']);
            $data['received_body'] = str_replace('Card', '', $data['received_body']);
            $data['received_body'] = str_replace('#', '', $data['received_body']);

            $parse_val = array_filter(explode(PHP_EOL, strip_tags($data['received_body'])));


            $array_parse = array();
            foreach ($parse_val as $parse) {
                if (strlen(trim($parse)) >= 2)
                    $array_parse[] = trim($parse);
            }
            $parse_val = $array_parse;

            if (sizeof($parse_val) > 2) {
                $count = 0;
                $helper_array = array();
                foreach ($parse_val as $parse) {

                    if (strlen(trim($parse)) == 12 && $count != 0 && $count != 4 && is_numeric(trim($parse))) {
                        for ($i = $count; $i < 4; $i++) {
                            $helper_array[] = 'na';
                        }
                        $count = 0;
                    } else {
                        $count = $count + 1;
                    }

                    if (strlen(trim($parse)) == 12 && $count == 4)
                        $count = 0;

                    $helper_array[] = $parse;
                }

                if ($count != 0 && $count < 4) {
                    for ($i = $count; $i < 4; $i++) {
                        $helper_array[] = 'na';
                    }
                    $count = 0;
                }


                $count = 0;
                $mob_array = array();
                $cnic_array = array();
                $name_array = array();
                $add_array = array();
                $parse_val = $helper_array;



                foreach ($parse_val as $parse) {
                    if ($count == 0)
                        $mob_array[] = substr(trim($parse), -10);
                    elseif ($count == 1)
                        $cnic_array[] = $parse;
                    elseif ($count == 2)
                        $name_array[] = $parse;
                    elseif ($count == 4)
                        $add_array[] = $parse;

                    if ($count == 4) {
                        $count = 0;
                    } else {
                        $count = $count + 1;
                    }
                }


                for ($i = 0; $i < sizeof($mob_array); $i++) {
                    if (!empty($mob_array[$i])) {
                        /* new code for multiple enteries */
                        $mobile_number = trim($mob_array[$i]);
                        $name = trim($name_array[$i]);
                        $cnic = trim($cnic_array[$i]);
                        $address = trim($add_array[$i]);
                        $active = 'Active';
                        if (strlen($mobile_number) == 10 && ctype_digit($mobile_number) && ctype_digit($cnic)) {
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
                            $sub_data = array();
                            $sub_data['act_date'] = $date;
                            $sub_data['mobile_number'] = trim($mobile_number);
                            $sub_data['cnic_number'] = trim($cnic);
                            if (sizeof($name) == 3) {
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
                            $sub_model = new Model_Generic();
                            $sub_model_result = $sub_model->ManualSubInfoinsert($sub_data);
                        }
                    }
                    $not_fount = 1;
                    break;
                    /* New code for multiple end */
                }
            } else {
                $mobile_number = substr(trim($data['requested_value']), -10);
                $cnic = $parse_val[0];
                $name = '';
                $address = '';
            }
        }


        if(!ctype_digit(trim($cnic)) && strlen(trim($cnic))==13)
        {
            $cnic_original = trim($cnic);
            //$cnic = Helpers_Utilities::check_digit(trim($cnic));
            $is_foreigner= 1;
        }

}
?>