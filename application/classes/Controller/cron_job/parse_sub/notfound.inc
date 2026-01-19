<?php

try {
    // Ensure received_body is an array
    if (empty($data['received_body'])) {
        $data['received_body'] = array_filter(explode('From:', $data['received_body_raw'] ?? ''));
    }

    $mark_complete = 0;
    
    // Patterns that indicate no data found
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
        'no number defined',
        'no data is found',
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
        'is no number defined',
        'networkerror',
        '***************************************=',
        'nter a valid value',
        'invalid input',
        'is no number defined against provided',
        'voice mail services',
    ];
    
    // Exact body matches
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
    
    // Check for patterns in body
    $body_text = isset($data['received_body'][0]) ? 
                 strtolower(trim($data['received_body'][0])) : '';
    
    foreach ($check_array as $str_check) {
        if (strpos($body_text, $str_check) !== false) {
            $mark_complete = 1;
            break;
        }
    }
    
    // Check for exact matches
    if (!$mark_complete) {
        $tempBodyCheck = $data['received_body'][0] ?? '';
        $tempBodyCheck = str_replace('&nbsp;', '', $tempBodyCheck);
        $tempBodyCheckClean = strtolower(trim(strip_tags($tempBodyCheck)));
        
        foreach ($check_body as $str_check) {
            if ($tempBodyCheckClean === $str_check) {
                $mark_complete = 1;
                break;
            }
        }
    }

    // Handle different "not found" scenarios
    if (!empty($mark_complete) || empty($data['received_body'][0]) || 
        $data['received_body'][0] == null || $data['received_body'][0] == ' ') {
        
        $reference_number = $data['request_id'];
        $reference_number = Model_Email::email_status($reference_number, 2, 5);
        $not_fount = 1;
        
    } elseif (strtolower(trim($data['received_body'][0])) == 'Data can\'t be fetched for more than 180 days') {
        
        $reference_number = $data['request_id'];
        $reference_number = Model_Email::email_status($reference_number, 0, 0);
        $not_fount = 1;
        
    } elseif ($data['company_name'] != 3 && 
              $data['user_request_type_id'] != 3 && 
              $data['user_request_type_id'] != 5) {
        
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
    
} catch (Exception $e) {
    Model_ErrorLog::log(
        'parse_sub_notfound',
        $e->getMessage(),
        [
            'request_id' => $data['request_id'] ?? 'unknown',
            'company_name' => $data['company_name'] ?? 'unknown',
            'user_request_type_id' => $data['user_request_type_id'] ?? null,
            'body_sample' => isset($data['received_body'][0]) ? substr($data['received_body'][0], 0, 200) : ''
        ],
        $e->getTraceAsString(),
        'validation_failure',
        'notfound_check'
    );
    error_log("[" . date('c') . "] notfound.inc check failed: " . $e->getMessage());
    $not_fount = 1; // Mark as not found to prevent further processing
}

?>