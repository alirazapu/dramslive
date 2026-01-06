<?php

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
 
if ($not_fount!=1){    
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