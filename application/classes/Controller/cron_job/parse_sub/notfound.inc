<?php
if (empty($data['received_body'])) {
    $data['received_body'] = array_filter(explode('From:', $data['received_body_raw']));
}

$mark_complete = 0;
$check_array = [
    'not found',
    'no data',
    'no record found',
    'no data against',
    'network error',
    'service unavailable',
    'nodata',
    'not bvs verified',
    'is ported out',
    'no data found',
    'no record found',
    'no number defined',
    'no data is found',
    'not found',
    'disowned',
    'no record',
    'doest not exist',
    'port out',
    'ported out',
    'please always provide fir/case number',
    'no msisdn found',
    'no details',
    'extraction problem',
    'date error',
    'more than a year',
    'number is purged on',
    'no record found',
    'is no number defined',
    'networkerror',
    '***************************************=',
    'nter a valid value',
    'invalid input',
    'no number defined',
    'is no number defined against provided',
    'voice mail services',
//    'nil',
    
];
if(isset($tempExclude) && !empty($tempExclude)){
   // array_push($check_array, $tempExclude);
}
$check_body = [
    'na',
    'n',
    'no data',
    'nil',
    'doest not exist',
    'no record',
    '',
    'Nil',
    ' ',
];
foreach ($check_array as $str_check) {
    if (strpos(strtolower(trim(($data['received_body'][0]))), $str_check) !== false) {
        $mark_complete = 1;
    }
}
foreach ($check_body as $str_check) {
    $tempBodyCheck=$data['received_body'][0];
    $tempBodyCheck = str_replace('&nbsp;','',$tempBodyCheck);              
    if (strtolower(trim(strip_tags($tempBodyCheck))) == $str_check) {
        $mark_complete = 1;
    }
}

if (!empty($mark_complete) || empty($data['received_body'][0]) || $data['received_body'][0] == null || $data['received_body'][0] == ' ') {

    $reference_number = $data['request_id'];
    $reference_number = Model_Email::email_status($reference_number, 2, 5);
    $not_fount = 1;
} elseif (strtolower(trim($data['received_body'][0])) == 'Data can\'t be fetched for more than 180 days') {
    $reference_number = $data['request_id'];
    $reference_number = Model_Email::email_status($reference_number, 0, 0);
    $not_fount = 1;
} elseif ($data['company_name'] != 3 && $data['user_request_type_id'] != 3 && $data['user_request_type_id'] != 5) {
    if ($data['company_name'] == 4) {
        $reference_number = $data['request_id'];
        $reference_number = Model_Email::email_status($reference_number, 2, 5);
        $not_fount = 1;
    } else {
        $reference_number = $data['request_id'];
        $reference_number = Model_Email::email_status($reference_number, 2, 3);
        $not_fount = 1;
    }
}
?>