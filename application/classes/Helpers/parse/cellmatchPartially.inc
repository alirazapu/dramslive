<?php
switch ($company) {
    case 6:        
        /*  Telenor */
        
        $compare_telenor_cdr = array_diff($telenor_cdr, array_map('trim', $data[$row]));
        $compare_telenor_cdr2 = array_diff($telenor_cdr_2, array_map('trim', $data[$row]));
		$compare_telenor_cdr3 = array_diff($telenor_cdr_3, array_map('trim', $data[$row]));
        if ((sizeof($compare_telenor_cdr) == sizeof($telenor_cdr)) && ($objPHPExcel->getSheetCount() > 1) || 
            (sizeof($compare_telenor_cdr2) == sizeof($telenor_cdr_2)) && ($objPHPExcel->getSheetCount() > 1) || 
            (sizeof($compare_telenor_cdr3) == sizeof($telenor_cdr_3)) && ($objPHPExcel->getSheetCount() > 1)) {
            $continue = 'continue';
        } else {
          
            //if (empty($compare_telenor_cdr) || empty($compare_telenor_cdr2)) {
            if (empty($compare_telenor_cdr) || empty($compare_telenor_cdr3)) {
                $flag = 'telnor_cdr';
                /* Index Setting */
                $data[$row] = array_map("strtoupper", $data[$row]);
                $call_type_index = array_search('CALL_TYPE', array_map('trim', $data[$row]));
                $call_type_dir_index = array_search('INBOUND_OUTBOUND_IND', array_map('trim', $data[$row]));
                $party_aindex = array_search('MSISDN', array_map('trim', $data[$row]));
                $party_bindex = array_search('CALL_ORG_NUM', array_map('trim', $data[$row]));
                $party_cindex = array_search('CALL_DIALED_NUM', array_map('trim', $data[$row]));
                $date_time_index = array_search('CALL_START_DT_TM', array_map('trim', $data[$row]));
                $duration_index = array_search('CALL_END_DT_TM', array_map('trim', $data[$row]));
                $imei_index = array_search('IMEI', array_map('trim', $data[$row]));
                $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
                $site_index = array_search('LOCATION', array_map('trim', $data[$row]));
                $cel_lac_index = array_search('LAC_ID', array_map('trim', $data[$row]));
                $cell_site_index = array_search('CELL_SITE_ID', array_map('trim', $data[$row]));
                $lng = array_search('LONGITUDE', array_map('trim', $data[$row]));   
                $lat = array_search('LAT', array_map('trim', $data[$row]));  
            }elseif (empty($compare_telenor_cdr2)) {
                $flag = 'telnor_cdr';
                /* Index Setting */
                $data[$row] = array_map("strtoupper", $data[$row]);
                $call_type_index = array_search('CALL_TYPE', array_map('trim', $data[$row]));
                $call_type_dir_index = array_search('INBOUND_OUTBOUND_IND', array_map('trim', $data[$row]));
                $party_aindex = array_search('MSISDN', array_map('trim', $data[$row]));
                $party_bindex = array_search('CALL_ORG_NUM', array_map('trim', $data[$row]));
                $party_cindex = array_search('CALL_DIALED_NUM', array_map('trim', $data[$row]));
                $date_time_index = array_search('CALL_START_DT_TM', array_map('trim', $data[$row]));
                $duration_index = array_search('CALL_END_DT_TM', array_map('trim', $data[$row]));
                $imei_index = array_search('IMEI', array_map('trim', $data[$row]));
                $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
                $site_index = array_search('LOCATION', array_map('trim', $data[$row]));
                $cel_lac_index = array_search('LAC_ID', array_map('trim', $data[$row]));
                $cell_site_index = array_search('SITE_ID', array_map('trim', $data[$row]));
                $lng = array_search('LONGITUDE', array_map('trim', $data[$row]));   
                $lat = array_search('LAT', array_map('trim', $data[$row]));  
            } else {
                if (!empty($file_id))
                    $error_number = Model_Email::file_status($file_id, 1, 3);                
                if(!empty($userrequestid)){
                    Model_ErrorLog::log(
                        'parse_cellmatch',
                        'Invalid Telenor CDR format - header validation failed',
                        array(
                            'request_id' => $userrequestid,
                            'processing_index' => 3,
                            'file_id' => !empty($file_id) ? $file_id : null,
                            'company' => 'Telenor',
                            'row' => !empty($row) ? $row : null
                        ),
                        null,
                        'validation_error',
                        'cellmatch_parsing'
                    );
                    $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                }
                exit;
            }
            
        }
        break;
    case 1:
    case 7:
        /* Jazz CDR */
        $compare_jazz_cdr = array_diff($jazz_cdr, array_map('trim', $data[$row]));
        $compare_jazz_cdr_alt1 = array_diff($jazz_cdr_alt1, array_map('trim', $data[$row]));
        $compare_jazz_cdr_alt2 = array_diff($jazz_cdr_alt2, array_map('trim', $data[$row]));
        $compare_jazz_cdr_alt3 = array_diff($jazz_cdr_alt3, array_map('trim', $data[$row]));
        

        if ((sizeof($compare_jazz_cdr) == sizeof($jazz_cdr)) && 
            (sizeof($compare_jazz_cdr_alt1) == sizeof($jazz_cdr_alt1)) && 
            (sizeof($compare_jazz_cdr_alt2) == sizeof($jazz_cdr_alt2)) && 
            (sizeof($compare_jazz_cdr_alt3) == sizeof($jazz_cdr_alt3)) && 
            ($objPHPExcel->getSheetCount() > 1)) {            
            $continue = 'continue';
        } else {
            if (empty($compare_jazz_cdr) || empty($compare_jazz_cdr_alt1) || empty($compare_jazz_cdr_alt2) || empty($compare_jazz_cdr_alt3)) {
                $flag = 'jazz_cdr';
                $data[$row] = array_map("strtoupper", $data[$row]);
                /* Index Setting */
               /* $call_type_index = array_search('CALLTYPE', array_map('trim', $data[$row]));
                $party_aindex = array_search('APARTY', array_map('trim', $data[$row]));
                $party_bindex = array_search('BPARTY', array_map('trim', $data[$row]));
                $date_time_index = array_search('DATETIME', array_map('trim', $data[$row]));
                $duration_index = array_search('DURATION', array_map('trim', $data[$row]));
                $imei_index = array_search('IMEI', array_map('trim', $data[$row]));
                $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
                $site_index = array_search('SITELOCATION', array_map('trim', $data[$row]));
                $cel_lac_index = array_search('LAC ID', array_map('trim', $data[$row]));
                $cell_site_index = array_search('CELLID', array_map('trim', $data[$row]));
                * 
                */
                
                
                $call_type_index = array_search('CALL TYPE', array_map('trim', $data[$row]));
                $party_aindex = array_search('A-PARTY', array_map('trim', $data[$row]));
                // Try alternative column name if not found
                if ($party_aindex === false) {
                    $party_aindex = array_search('APARTY', array_map('trim', $data[$row]));
                }
                $party_bindex = array_search('B-PARTY', array_map('trim', $data[$row]));
                $date_time_index = array_search('DATE & TIME', array_map('trim', $data[$row]));
                // Try alternative column names if not found
                if ($date_time_index === false) {
                    $date_time_index = array_search('DATETIME', array_map('trim', $data[$row]));
                }
                $duration_index = array_search('DURATION', array_map('trim', $data[$row]));
                $imei_index = array_search('IMEI', array_map('trim', $data[$row]));
                $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
                $site_index = array_search('SITE', array_map('trim', $data[$row]));
                $cel_lac_index = array_search('LAC ID', array_map('trim', $data[$row]));
                $cell_site_index = array_search('CELL ID', array_map('trim', $data[$row]));
                
                /*
                $call_type_index = array_search('CALL TYPE', array_map('trim', $data[$row]));
                $party_aindex = array_search('A PARTY', array_map('trim', $data[$row]));
                $party_bindex = array_search('B-PARTY', array_map('trim', $data[$row]));
                $date_time_index = array_search('DATE AND TIME', array_map('trim', $data[$row]));
                $duration_index = array_search('DURATION', array_map('trim', $data[$row]));
                $imei_index = array_search('IMEI', array_map('trim', $data[$row]));
                $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
                $site_index = array_search('SITE LOCATION', array_map('trim', $data[$row]));
                $cel_lac_index = array_search('LAC ID', array_map('trim', $data[$row]));
                $cell_site_index = array_search('CELL ID', array_map('trim', $data[$row]));
                 * */
                 
            } else {
                if (!empty($file_id))
                    $error_number = Model_Email::file_status($file_id, 1, 3);
                if(!empty($userrequestid)){
                    // Enhanced logging to show actual headers found
                    $actual_headers = isset($data[$row]) ? implode(', ', array_map('trim', $data[$row])) : 'No headers found';
                    $expected_headers_list = [
                        'Format 1: ' . implode(', ', $jazz_cdr),
                        'Format 2: ' . implode(', ', $jazz_cdr_alt1),
                        'Format 3: ' . implode(', ', $jazz_cdr_alt2),
                        'Format 4: ' . implode(', ', $jazz_cdr_alt3)
                    ];
                    $expected_headers = implode(' | ', $expected_headers_list);
                    $missing_from_formats = [];
                    if (!empty($compare_jazz_cdr)) $missing_from_formats[] = 'Format 1: ' . implode(', ', $compare_jazz_cdr);
                    if (!empty($compare_jazz_cdr_alt1)) $missing_from_formats[] = 'Format 2: ' . implode(', ', $compare_jazz_cdr_alt1);
                    if (!empty($compare_jazz_cdr_alt2)) $missing_from_formats[] = 'Format 3: ' . implode(', ', $compare_jazz_cdr_alt2);
                    if (!empty($compare_jazz_cdr_alt3)) $missing_from_formats[] = 'Format 4: ' . implode(', ', $compare_jazz_cdr_alt3);
                    $missing_headers = !empty($missing_from_formats) ? implode(' | ', $missing_from_formats) : 'None of the formats matched';
                    
                    Model_ErrorLog::log(
                        'parse_cellmatch',
                        'Invalid Jazz CDR format - header validation failed',
                        array(
                            'request_id' => $userrequestid,
                            'processing_index' => 3,
                            'file_id' => !empty($file_id) ? $file_id : null,
                            'company' => 'Jazz',
                            'row' => !empty($row) ? $row : null,
                            'expected_headers' => $expected_headers,
                            'actual_headers' => $actual_headers,
                            'missing_headers' => $missing_headers
                        ),
                        null,
                        'validation_error',
                        'cellmatch_parsing'
                    );
                    $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                }
                exit;
            }
        }
        break;
    case 3:        
        /* Ufone CDR */
        $compare_ufone_cdr = array_diff($ufone_cdr, array_map('trim', $data[$row]));
        
            
               
        if ((sizeof($compare_ufone_cdr) == sizeof($ufone_cdr)) && ($objPHPExcel->getSheetCount() > 1)) {
            $continue = 'continue';
            
            
        } else {
          
               
            if (empty($compare_ufone_cdr)) {
                $flag = 'ufone_cdr';
                /* Index Setting */
                $data[$row] = array_map("strtoupper", $data[$row]);

                $call_type_index = array_search('DIRECTION', array_map('trim', $data[$row]));
                $call_type_dir_index = array_search('TYPE', array_map('trim', $data[$row]));

                $party_aindex = array_search('A NUMBER', array_map('trim', $data[$row]));
                $party_bindex = array_search('B NUMBER', array_map('trim', $data[$row]));
                $date_times_index = array_search('START TIME', array_map('trim', $data[$row]));
                $date_timee_index = array_search('END TIME', array_map('trim', $data[$row]));                
                $duration_index = array_search('DURATION', array_map('trim', $data[$row]));
                $imei_index = array_search('IMEI', array_map('trim', $data[$row]));
                $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
                $site_index = array_search('LOCATION', array_map('trim', $data[$row]));
                $cell_site_index = array_search('CELL ID', array_map('trim', $data[$row]));
                $cel_lac_index = array_search('LAC ID -A', array_map('trim', $data[$row]));
                $lng = array_search('LONGITUDE', array_map('trim', $data[$row]));   
                $lat = array_search('LATITUDE', array_map('trim', $data[$row]));  
            } else {
                if (!empty($file_id))
                    $error_number = Model_Email::file_status($file_id, 1, 3);
                if(!empty($userrequestid)){
                    Model_ErrorLog::log(
                        'parse_cellmatch',
                        'Invalid Ufone CDR format - header validation failed',
                        array(
                            'request_id' => $userrequestid,
                            'processing_index' => 3,
                            'file_id' => !empty($file_id) ? $file_id : null,
                            'company' => 'Ufone',
                            'row' => !empty($row) ? $row : null
                        ),
                        null,
                        'validation_error',
                        'cellmatch_parsing'
                    );
                    $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                }
                exit;
            }
        }
        break;
    case 4:
        /* Zong Partial CDR - expects only 3 columns: Mobile No, IMEI, LAST_ACTIVITY_DATE */

        // Normalize both arrays to uppercase for comparison
        $compare_zong_cdr = array_diff(
            array_map('strtoupper', $zong_cdr),
            array_map('strtoupper', array_map('trim', $data[$row]))
        );

        if ((sizeof($compare_zong_cdr) == sizeof($zong_cdr)) && ($objPHPExcel->getSheetCount() > 1)) {
            $continue = 'continue';
        } else {
            if (empty($compare_zong_cdr)) {
                $flag = 'zong_cdr';

                // Convert headers to uppercase for consistent matching
                $data[$row] = array_map("strtoupper", array_map('trim', $data[$row]));

                // For partial parsing, we only need these 3 indices
                $party_aindex = array_search('MOBILE NO', $data[$row]);
                if ($party_aindex === false) {
                    $party_aindex = array_search('MSISDN', $data[$row]); // Fallback
                }

                $imei_index = array_search('IMEI', $data[$row]);

                $date_index = array_search('LAST_ACTIVITY_DATE', $data[$row]);
                if ($date_index === false) {
                    $date_index = array_search('STRT_TM', $data[$row]); // Fallback
                }

                // Set other indices to null/false as they're not used in partial parsing
                $call_type_index = false;
                $party_bindex = false;
                $time_index = false;
                $duration_index = false;
                $imsi_index = false;
                $site_index = false;
                $lng = false;
                $lat = false;
                $cel_lac_index = false;
                $cell_site_index = false;

                // Debug: Log what we found (remove after testing)
                if (!empty($file_id)) {
                    Model_ErrorLog::log(
                        'parse_cellmatch_debug',
                        'Zong partial CDR headers matched',
                        array(
                            'file_id' => $file_id,
                            'party_aindex' => $party_aindex,
                            'imei_index' => $imei_index,
                            'date_index' => $date_index,
                            'headers_found' => implode(', ', $data[$row])
                        ),
                        null,
                        'info',
                        'cellmatch_parsing'
                    );
                }

            } else {
                // Log the mismatch details
                $actual_headers = isset($data[$row]) ? implode(', ', array_map('trim', $data[$row])) : 'No headers found';
                $expected_headers = implode(', ', $zong_cdr);
                $missing_headers = implode(', ', $compare_zong_cdr);

                if (!empty($file_id)) {
                    Model_ErrorLog::log(
                        'parse_cellmatch',
                        'Invalid Zong partial CDR format - header validation failed',
                        array(
                            'request_id' => !empty($userrequestid) ? $userrequestid : null,
                            'processing_index' => 3,
                            'file_id' => $file_id,
                            'company' => 'Zong',
                            'row' => !empty($row) ? $row : null,
                            'expected_headers' => $expected_headers,
                            'actual_headers' => $actual_headers,
                            'missing_headers' => $missing_headers
                        ),
                        null,
                        'validation_error',
                        'cellmatch_parsing'
                    );
                    $error_number = Model_Email::file_status($file_id, 1, 3);
                }

                if (!empty($userrequestid)) {
                    $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                }
                exit;
            }
        }
        break;
        //break;

    /* Warid Subscription */
    /* if($company==7)
      $compare_warid_sub = array_diff($warid_sub, $data[$row]); */
}