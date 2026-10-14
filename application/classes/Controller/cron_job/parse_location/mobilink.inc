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

    /* Jazz also replies in a newer one-line layout, positions taken from the
       unfiltered split so an empty field cannot shift the ones after it:
         GSM/UMTS: MSISDN|2026-October-08 12:19:31|Attached|LAC|Cell|IMSI|GSM/UMTS|, Address|Lat|Long|IMEI
         LTE:      MSISDN|2026-October-08 12:35:14|Attached|TAC|eNodeB|Cell|IMSI|LTE|, Address|Lat|Long|IMEI
       The IMEI field is followed by the quoted request (date, FIR no., MSISDN).
       Jazz sometimes wraps a line inside the date ("2026-June-30" / "13:43:33"),
       so the space between date and time may be missing or be a line break.
       Any other reply falls through to the original layout parsing below. */
    $jazz_fields = array_map('trim', explode('|', strip_tags($data['received_body'])));
    $jazz_network_key = false;
    if (isset($jazz_fields[1])) {
        $jazz_fields[1] = preg_replace('/^(\d{4}-[A-Za-z]+-\d{2})\s*(\d{1,2}:\d{2}(:\d{2})?)$/', '$1 $2', $jazz_fields[1]);
    }
    if (count($jazz_fields) >= 11
            && preg_match('/^\d{4}-[A-Za-z]+-\d{2} \d{1,2}:\d{2}(:\d{2})?$/', $jazz_fields[1])
            && preg_match('/^(attached|detached|deattach(ed)?|purged)$/i', $jazz_fields[2])) {
        foreach (array(6, 7) as $key) {
            if (isset($jazz_fields[$key]) && preg_match('/^(gsm|umts|lte|nr|[2-5]g)(\/(gsm|umts|lte|nr|[2-5]g))*$/i', $jazz_fields[$key])) {
                $jazz_network_key = $key;
                break;
            }
        }
    }

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
        
        // Log status update to 5 (Not Found)
        Model_ErrorLog::log(
            'parse_location_mobilink',
            'No location data in response - marking as status 5 (Not Found)',
            [
                'request_id' => $reference_number,
                'company_name' => $data['company_name'] ?? 1,
                'user_request_type_id' => $data['user_request_type_id'] ?? null,
                'processing_index' => 5,
                'mark_complete' => $mark_complete,
                'body_empty' => empty($data['received_body'])
            ],
            null,
            'not_found',
            'location_parsing','success'
        );
        
        $reference_number = Model_Email::email_status($reference_number, 2, 5);
        $not_fount = 1;

    } elseif ($jazz_network_key !== false) {
        // Newer one-line layout (see $jazz_fields above)
        $n = $jazz_network_key;
        $loc_data['locationmsisdn'] = substr(preg_replace('/\D/', '', $jazz_fields[0]), -10);

        $jazz_date = DateTime::createFromFormat('Y-F-d H:i:s', $jazz_fields[1]);
        if (!$jazz_date) {
            $jazz_date = DateTime::createFromFormat('Y-F-d H:i', $jazz_fields[1]);
        }
        if ($jazz_date) {
            $loc_data['locdate'] = $jazz_date->format('Y-m-d H:i:s');
        }

        $jazz_status = strtolower($jazz_fields[2]);
        if ($jazz_status === 'attached') {
            $loc_data['locstatus'] = 1;
        } elseif ($jazz_status === 'purged') {
            $loc_data['locstatus'] = 2;
        } else {
            $loc_data['locstatus'] = 0;
        }

        // GSM/UMTS: LAC|Cell. LTE: TAC|eNodeB|Cell - TAC as LAC, eNodeB as cell, cell as sector.
        if (ctype_digit($jazz_fields[3])) {
            $loc_data['loclac'] = $jazz_fields[3];
        }
        if (ctype_digit($jazz_fields[4])) {
            $loc_data['loccellid'] = $jazz_fields[4];
        }
        if ($n == 7 && ctype_digit($jazz_fields[5])) {
            $loc_data['locsector'] = $jazz_fields[5];
        }

        if (ctype_digit($jazz_fields[$n - 1])) {
            $loc_data['locimsi'] = $jazz_fields[$n - 1];
        }

        $jazz_network = strtolower($jazz_fields[$n]);
        if (strpos($jazz_network, 'lte') !== false || strpos($jazz_network, '4g') !== false) {
            $loc_data['locnetwork'] = 3;
        } elseif ($jazz_network === 'umts' || $jazz_network === '3g') {
            $loc_data['locnetwork'] = 2;
        } elseif ($jazz_network === 'gsm' || $jazz_network === '2g') {
            $loc_data['locnetwork'] = 1;
        } else {
            $loc_data['locnetwork'] = 0; // e.g. GSM/UMTS: 2G or 3G, Jazz does not say which
        }

        $jazz_address = trim(trim($jazz_fields[$n + 1] ?? ''), ', ');
        $loc_data['locaddress'] = mb_substr($jazz_address . ' Network: ' . $jazz_fields[$n] . ' Last Activity: ' . $jazz_fields[1], 0, 500);

        $jazz_lat = $jazz_fields[$n + 2] ?? '';
        $jazz_long = $jazz_fields[$n + 3] ?? '';
        if (is_numeric($jazz_lat) && is_numeric($jazz_long)) {
            // Same rule as the original layout: in Pakistan latitude < longitude.
            $loc_data['loclat'] = min($jazz_lat, $jazz_long);
            $loc_data['loclong'] = max($jazz_lat, $jazz_long);
        }

        // 14 or 15 leading digits: the quoted request's date (mm/dd/yyyy) follows
        // the IMEI, with no separator once the line break has been removed.
        if (isset($jazz_fields[$n + 4]) && preg_match('/^\d{14,15}?(?=\d{1,2}\/\d{1,2}\/\d{4}|\D|$)/', $jazz_fields[$n + 4], $jazz_imei)) {
            $loc_data['locimei'] = str_pad($jazz_imei[0], 15, '0', STR_PAD_RIGHT);
        }

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
            
            // Log status update to 5 (Not Found)
            Model_ErrorLog::log(
                'parse_location_mobilink',
                'Insufficient location data fields - marking as status 5 (Not Found)',
                [
                    'request_id' => $reference_number,
                    'company_name' => $data['company_name'] ?? 1,
                    'user_request_type_id' => $data['user_request_type_id'] ?? null,
                    'processing_index' => 5,
                    'fields_count' => sizeof($parse_val),
                    'required_fields' => 5
                ],
                null,
                'not_found',
                'location_parsing',
				'success'
            );
            
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