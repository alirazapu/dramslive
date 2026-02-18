<?php
switch ($company) {
    case 6:        
        /*  Telenor */
        
        $compare_telenor_cdr = array_diff($telenor_cdr, array_map('trim', $data[$row]));
        $compare_telenor_cdr2 = array_diff($telenor_cdr_2, array_map('trim', $data[$row]));
     
        if ((sizeof($compare_telenor_cdr) == sizeof($telenor_cdr)) && ($objPHPExcel->getSheetCount() > 1) || 
            (sizeof($compare_telenor_cdr2) == sizeof($telenor_cdr_2)) && ($objPHPExcel->getSheetCount() > 1)) {
            $continue = 'continue';
        } else {
          
            //if (empty($compare_telenor_cdr) || empty($compare_telenor_cdr2)) {
            if (empty($compare_telenor_cdr)) {
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
        $compare_jazz_cdr_2 = array_diff($jazz_cdr_2, array_map('trim', $data[$row]));
        $compare_jazz_cdr_3 = array_diff($jazz_cdr_3, array_map('trim', $data[$row]));

        if ((sizeof($compare_jazz_cdr) == sizeof($jazz_cdr)) && ($objPHPExcel->getSheetCount() > 1)) {            
            $continue = 'continue';
        } else {
            if (empty($compare_jazz_cdr)) {
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
                
                /*
                $call_type_index = array_search('CALL TYPE', array_map('trim', $data[$row]));
                $party_aindex = array_search('A-PARTY', array_map('trim', $data[$row]));
                $party_bindex = array_search('B-PARTY', array_map('trim', $data[$row]));
                $date_time_index = array_search('DATE & TIME', array_map('trim', $data[$row]));
                $duration_index = array_search('DURATION', array_map('trim', $data[$row]));
                $imei_index = array_search('IMEI', array_map('trim', $data[$row]));
                $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
                $site_index = array_search('SITE', array_map('trim', $data[$row]));
                $cel_lac_index = array_search('LAC ID', array_map('trim', $data[$row]));
                $cell_site_index = array_search('CELL ID', array_map('trim', $data[$row]));
                */
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
                $call_type_index = array_search('CALL TYPE', array_map('trim', $data[$row]));
                $party_aindex = array_search('A-PARTY', array_map('trim', $data[$row]));
                $party_bindex = array_search('B-PARTY', array_map('trim', $data[$row]));
                $date_time_index = array_search('DATE AND TIME', array_map('trim', $data[$row]));
                $duration_index = array_search('DURATION', array_map('trim', $data[$row]));
                $imei_index = array_search('IMEI', array_map('trim', $data[$row]));
                $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
                $site_index = array_search('SITELOCATION', array_map('trim', $data[$row]));
                $cel_lac_index = array_search('LAC ID', array_map('trim', $data[$row]));
                $cell_site_index = array_search('CELL ID', array_map('trim', $data[$row]));
                
                 
            }elseif(empty($compare_jazz_cdr_2)) {
                $flag = 'jazz_cdr';
                $data[$row] = array_map("strtoupper", $data[$row]);
                $call_type_index = array_search('CALL TYPE', array_map('trim', $data[$row]));
                $party_aindex = array_search('A-PARTY', array_map('trim', $data[$row]));
                $party_bindex = array_search('B-PARTY', array_map('trim', $data[$row]));
                $date_time_index = array_search('DATE & TIME', array_map('trim', $data[$row]));
                $duration_index = array_search('DURATION', array_map('trim', $data[$row]));
                $imei_index = array_search('IMEI', array_map('trim', $data[$row]));
                $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
                $site_index = array_search('SITE', array_map('trim', $data[$row]));
                $cel_lac_index = array_search('LAC ID', array_map('trim', $data[$row]));
                $cell_site_index = array_search('CELL ID', array_map('trim', $data[$row]));
                
                 
            }elseif(empty($compare_jazz_cdr_3)) {
                $flag = 'jazz_cdr';
                $data[$row] = array_map("strtoupper", $data[$row]);
                $call_type_index = array_search('CALLTYPE', array_map('trim', $data[$row]));
                $party_aindex = array_search('APARTY', array_map('trim', $data[$row]));
                $party_bindex = array_search('BPARTY', array_map('trim', $data[$row]));
                $date_time_index = array_search('DATETIME', array_map('trim', $data[$row]));
                $duration_index = array_search('DURATION', array_map('trim', $data[$row]));
                $imei_index = array_search('IMEI', array_map('trim', $data[$row]));
                $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
                $site_index = array_search('SITELOCATION', array_map('trim', $data[$row]));
                $cel_lac_index = array_search('IP', array_map('trim', $data[$row]));
                $cell_site_index = array_search('CELLID', array_map('trim', $data[$row]));
                
                 
            } else {
                if (!empty($file_id))
                    $error_number = Model_Email::file_status($file_id, 1, 3);
                if(!empty($userrequestid)){
                    Model_ErrorLog::log(
                        'parse_cellmatch',
                        'Invalid Jazz CDR format - header validation failed',
                        array(
                            'request_id' => $userrequestid,
                            'processing_index' => 3,
                            'file_id' => !empty($file_id) ? $file_id : null,
                            'company' => 'Jazz',
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
  /*  case 7:        
        /// Warid CDR 
        $compare_warid_cdr = array_diff($warid_cdr, array_map('trim', $data[$row]));
        if ((sizeof($compare_warid_cdr) == sizeof($warid_cdr)) && ($objPHPExcel->getSheetCount() > 1)) {
            $continue = 'continue';
        } else {
            if (empty($compare_warid_cdr)) {
                $flag = 'warid_cdr';
                // Index Setting 
                $data[$row] = array_map("strtoupper", $data[$row]);
                $call_type_index = array_search('OPER', array_map('trim', $data[$row]));  //OPER column in Warid
                $party_aindex = array_search('SUBNO', array_map('trim', $data[$row]));  //SUBNO in warid
                $party_bindex = array_search('B_SUBNO', array_map('trim', $data[$row])); //B_SUBNO in warid
                $date_index = array_search('A_TRANSDATE', array_map('trim', $data[$row]));
                $time_index = array_search('TRANSTIME', array_map('trim', $data[$row]));
                $duration_index = array_search('DURATION', array_map('trim', $data[$row]));
                $imei_index = array_search('IMEI_NUMBER', array_map('trim', $data[$row]));
                $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));   // Yet not available 
                $site_index = array_search('DESCRIPTION', array_map('trim', $data[$row]));
                $cell_site_index = array_search('CELL_ID', array_map('trim', $data[$row]));
                $cel_lac_index = array_search('LAC ID -A', array_map('trim', $data[$row]));
            } else {
                if (!empty($file_id))
                    $error_number = Model_Email::file_status($file_id, 1, 3);
                if(!empty($userrequestid)){
                    Model_ErrorLog::log(
                        'parse_cellmatch',
                        'Invalid Warid CDR format - header validation failed',
                        array(
                            'request_id' => $userrequestid,
                            'processing_index' => 3,
                            'file_id' => !empty($file_id) ? $file_id : null,
                            'company' => 'Warid',
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

        break;  */
    case 4:        
        /* Zong CDR */
        // Check for CDR Against Mobile Number format (Customer Msisdn based)
        $compare_zong_cdr_call = ('Customer Msisdn' == $data[$row][0] && empty($data[$row][2]) && empty($data[$row][3])) ? 1 : 0;
        
        // Check for CDR Against IMEI Number format (Mobile No, IMEI, LAST_ACTIVITY_DATE)
        $zong_cdr_imei = ['Mobile No', 'IMEI', 'LAST_ACTIVITY_DATE'];
        $compare_zong_cdr_imei = array_diff(
            array_map('strtoupper', $zong_cdr_imei),
            array_map('strtoupper', array_map('trim', $data[$row]))
        );
        
        // Check if neither format matches and there are multiple sheets
        if (($compare_zong_cdr_call == 0) && (!empty($compare_zong_cdr_imei)) && ($total_Sheet > 1)) {            
            $continue = 'continue';
        } else {            
            // Process CDR Against Mobile Number format
            if (!empty($compare_zong_cdr_call) && $compare_zong_cdr_call == 1) {                
                $flag = 'zong_cdr';
                $index_no = 3;
                    $data[$index_no] = array_map("strtoupper", $data[$index_no]);
                    $call_type_index = array_search('CALL_TYPE', array_map('trim', $data[$index_no]));  //OPER column in Warid
                    
                    if(isset($call_type_index) && $call_type_index!=0)
                    {
                        $index_no = 5;
                        $data[$index_no] = array_map("strtoupper", $data[$index_no]);
                        $call_type_index = array_search('CALL_TYPE', array_map('trim', $data[$index_no]));  //OPER column in Warid
                    } 
                    $party_aindex = array_search('MSISDN', array_map('trim', $data[$index_no]));  //SUBNO in warid
                    $party_bindex = array_search('BNUMBER', array_map('trim', $data[$index_no])); //B_SUBNO in warid
                    $date_index = array_search('STRT_TM', array_map('trim', $data[$index_no]));
                    $time_index = array_search('MINS', array_map('trim', $data[$index_no]));
                    $duration_index = array_search('SECS', array_map('trim', $data[$index_no]));
                    $imei_index = array_search('IMEI', array_map('trim', $data[$index_no]));
                    $imsi_index = array_search('IMSI', array_map('trim', $data[$index_no]));   // Yet not available 
                    $site_index = array_search('SITE_ADDRESS', array_map('trim', $data[$index_no]));   
                    $lng = array_search('LNG', array_map('trim', $data[$index_no]));   
                    $lat = array_search('LAT', array_map('trim', $data[$index_no]));  
                    $cel_lac_index = array_search('LAC_ID', array_map('trim', $data[$index_no]));                    
                    $cell_site_index = array_search('CELL_ID', array_map('trim', $data[$index_no]));                     
            
            // Process CDR Against IMEI Number format
            } elseif (empty($compare_zong_cdr_imei)) {
                $flag = 'zong_cdr_partial';
                $index_no = 0;
                $data[$index_no] = array_map("strtoupper", $data[$index_no]);
                $party_aindex = array_search('MOBILE NO', array_map('trim', $data[$index_no]));
                $imei_index = array_search('IMEI', array_map('trim', $data[$index_no]));
                $date_index = array_search('LAST_ACTIVITY_DATE', array_map('trim', $data[$index_no]));
                // Initialize indices not used in IMEI format to avoid undefined variable errors
                $party_bindex = false;
                $time_index = false;
                $duration_index = false;
                $imsi_index = false;
                $site_index = false;
                $lng = false;
                $lat = false;
                $cel_lac_index = false;
                $cell_site_index = false;
                
            } else {
                if (!empty($file_id))
                    $error_number = Model_Email::file_status($file_id, 1, 3);
                if(!empty($userrequestid)){
                    Model_ErrorLog::log(
                        'parse_cellmatch',
                        'Invalid Zong CDR format - header validation failed',
                        array(
                            'request_id' => $userrequestid,
                            'processing_index' => 3,
                            'file_id' => !empty($file_id) ? $file_id : null,
                            'company' => 'Zong',
                            'row' => !empty($row) ? $row : null,
                            'raw_header' => isset($data[$row]) ? $data[$row] : null
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
        //break;

    /* Warid Subscription */
    /* if($company==7)
      $compare_warid_sub = array_diff($warid_sub, $data[$row]); */
}