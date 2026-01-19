<?php

try {
    if ($email_staus == 1) {
        // Update request send count
        $query = DB::update('request_send_today')
            ->set(array('total' => DB::expr('total + 1')))
            ->where('request_priority', '=', $res['request_priority'])
            ->and_where('company_name', '=', $res['company_name'])
            ->execute();  

        /* For Telco Report */
        $date_for_telco = date('Y-m-d');
        
        // Check if telco summary exists for today
        $tel_report = "SELECT * FROM telco_request_summary WHERE date = :date";
        $report_telco_result = DB::query(Database::SELECT, $tel_report)
            ->param(':date', $date_for_telco)
            ->execute()
            ->as_array();

        if (!empty($report_telco_result)) {    
            $tel_co_mnc = array(1, 3, 4, 6, 7, 11, 12, 13);
            
            foreach ($tel_co_mnc as $mnc) {
                $send_high = 0;
                $send_medium = 0;
                $send_low = 0;
                
                // Get request counts by priority for company
                $tel_query = "SELECT request_priority, total FROM request_send_today WHERE company_name = :mnc";
                $tel_result = DB::query(Database::SELECT, $tel_query)
                    ->param(':mnc', $mnc)
                    ->execute()
                    ->as_array();

                foreach ($tel_result as $tel) {
                    switch ($tel['request_priority']) {
                        case 1:
                            $send_low += $tel['total'];
                            break;
                        case 2:
                            $send_medium += $tel['total'];
                            break;
                        case 3:
                            $send_high += $tel['total'];
                            break;
                    }                         
                }
                
                $total_send = $send_high + $send_medium + $send_low;
                
                // Update telco summary
                DB::update('telco_request_summary')
                    ->set(array(
                        'send_high' => $send_high,
                        'send_medium' => $send_medium,
                        'send_low' => $send_low,
                        'total_send' => $total_send
                    ))
                    ->where('date', '=', $date_for_telco)   
                    ->and_where('company_mnc', '=', $mnc)
                    ->execute();     
            }
        }

        $reference_number_result = Model_Email::email_sending_status($reference_number, 1, 0);                        
    } else {
        $reference_number_result = Model_Email::email_status($reference_number, 3, 1);
    }
    
    if (isset($count)) {
        $count = $count + 1; 
    }

    if (!empty($reference_number)) {
        Helpers_Email::emailreadstatus($reference_number);
    }
    
} catch (Exception $e) {
    Model_ErrorLog::log(
        'telco_count',
        $e->getMessage(),
        [
            'reference_number' => $reference_number ?? 'unknown',
            'email_status' => $email_staus ?? null,
            'company_name' => $res['company_name'] ?? 'unknown',
            'request_priority' => $res['request_priority'] ?? null
        ],
        $e->getTraceAsString(),
        'processing_failure',
        'telco_count_update'
    );
    error_log("[" . date('c') . "] telco_count.inc failed: " . $e->getMessage());
}

?>