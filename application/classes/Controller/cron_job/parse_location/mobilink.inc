<?php

try {
    // Clean and prepare received body
    $data['received_body'] = str_replace(['--', PHP_EOL, '"', 'N.A', 'NULL', '*'], '', $data['received_body']);
    $parse_val = array_map('trim', array_filter(explode('|', strip_tags($data['received_body']))));
    
    $mark_complete = 0;
    
    // Patterns indicating no location data
    $check_array = [
        'not found', 
        'no record found', 
        'number is purged', 
        'does not exist', 
        'network error', 
        'try again later', 
        'pakistan with', 
        'roaming to', 
        'doest not exist', 
        'assumed idle'
    ];
    
    $check_body = ['na', 'n', 'no data', 'nil', '', ' '];
    
    // Check for not-found patterns
    $body_lower = strtolower(trim($data['received_body']));
    foreach ($check_array as $str_check) {
        if (strpos($body_lower, $str_check) !== false) {
            $mark_complete = 1;
            break;
        }
    }
    
    // Check for exact matches
    if (!$mark_complete) {
        foreach ($check_body as $str_check) {
            if ($body_lower == $str_check) {
                $mark_complete = 1;
                break;
            }
        }
    }
    
    // Mark as complete if no data
    if (!empty($mark_complete) || empty($data['received_body']) || 
        $data['received_body'] == null || $data['received_body'] == ' ') {
        
        $reference_number = $data['request_id'];
        $reference_number = Model_Email::email_status($reference_number, 2, 5);
        $not_fount = 1;
        
    } else {
        // Filter parsed values - keep only meaningful data
        $array_parse = array();
        foreach ($parse_val as $parse) {                            
            if (strlen(trim($parse)) >= 2) {
                $array_parse[] = trim($parse);
            }
        }
        $parse_val = array_values($array_parse); 
        
        // Mark complete if insufficient data
        if (sizeof($parse_val) < 5) {   
            $reference_number = $data['request_id'];
            $reference_number = Model_Email::email_status($reference_number, 2, 5);
            $not_fount = 1;
        } else {
            // Parse location data
            $loc_data['locationmsisdn'] = substr(trim($parse_val[0]), -10);
            
            // Parse date and time
            $parse_val[3] = str_replace([' PM', ' AM'], '', $parse_val[3]);
            $date_h = $parse_val[2] . ' ' . $parse_val[3];  
            $loc_data['locdate'] = date("Y-m-d H:i:s", strtotime($date_h));
            
            // Parse LAC and Cell ID
            if (ctype_digit(trim($parse_val[4]))) {
                $loc_data['loclac'] = trim($parse_val[4]);
            }
            
            if (ctype_digit(trim($parse_val[5]))) {
                $loc_data['loccellid'] = trim($parse_val[5]);
            }
            
            // Determine location status
            $key = array_search('Attached', $parse_val);
            if (empty($key)) {
                $key = array_search('Deattach', $parse_val);    
            }
            if (empty($key)) {
                $key = array_search('Purged', $parse_val);    
                $parse = $parse_val[$key] ?? '';
            } else {                            
                $parse = $parse_val[6] ?? '';
            }
            
            $parse_lower = strtolower($parse);
            if (strpos($parse_lower, 'attached') !== false) {
                $loc_data['locstatus'] = 1;
            } elseif (strpos($parse_lower, 'deattach') !== false) {
                $loc_data['locstatus'] = 0;
            } elseif (strpos($parse_lower, 'purged') !== false) {
                $loc_data['locstatus'] = 2;
            } else {
                $loc_data['locstatus'] = 0;
            }
            
            // Parse IMSI
            if (!empty($parse_val[8])) {
                $loc_data['locimsi'] = trim($parse_val[8]);
            }
            
            // Parse address
            if (isset($parse_val[10])) {
                $loc_data['locaddress'] = $parse_val[10] . 
                                         ' MSC: ' . ($parse_val[1] ?? '') . 
                                         ' Last Activity: ' . ($parse_val[7] ?? '') . 
                                         ' Last Radio Action: ' . ($parse_val[9] ?? '');
            }
            
            // Parse latitude and longitude
            if (!empty($parse_val[11]) && !empty($parse_val[12])) {   
                $lat_long = [trim($parse_val[11]), trim($parse_val[12])];
                $loc_data['loclat'] = min($lat_long);
                $loc_data['loclong'] = max($lat_long);
            } else {
                $loc_data['loclat'] = '';
                $loc_data['loclong'] = '';
            }
            
            // Parse IMEI
            if (isset($parse_val[14]) && is_numeric($parse_val[14])) {    
                $loc_data['locimei'] = str_pad(trim($parse_val[14]), 15, '0', STR_PAD_RIGHT);
            }
        }
    }
    
} catch (Exception $e) {
    Model_ErrorLog::log(
        'parse_location_mobilink',
        $e->getMessage(),
        [
            'request_id' => $data['request_id'] ?? 'unknown',
            'company_name' => $data['company_name'] ?? 'unknown',
            'msisdn' => $data['requested_value'] ?? 'unknown',
            'body_sample' => isset($data['received_body']) ? substr($data['received_body'], 0, 200) : ''
        ],
        $e->getTraceAsString(),
        'parsing_failure',
        'location_mobilink_parse'
    );
    error_log("[" . date('c') . "] parse_location/mobilink.inc failed: " . $e->getMessage());
    
    // Mark as complete to prevent further processing
    $reference_number = $data['request_id'] ?? null;
    if ($reference_number) {
        Model_Email::email_status($reference_number, 2, 3);
    }
    $not_fount = 1;
}

?>