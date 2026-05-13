<?php

try {
    $query = "SELECT ur.*, em.* 
              FROM `user_request` AS ur
              JOIN email_messages AS em ON ur.message_id = em.message_id
              WHERE ur.status = 0 
                AND ur.processing_index = 0
                AND ur.request_priority = 3
                AND ur.user_request_type_id != 8
              ORDER BY ur.request_id ASC";
    
    $result = DB::query(Database::SELECT, $query)->execute()->as_array();
    $count=0;
    foreach ($result as $res) {
        if ($count > 7) {
            break;
        }
        try {
            // Check daily limit for this priority
            $total_query = "SELECT SUM(total) AS total 
                           FROM request_send_today 
                           WHERE request_priority = :priority";
            $total_count = DB::query(Database::SELECT, $total_query)
                ->param(':priority', $res['request_priority'])
                ->execute()
                ->current();

            if ($total_count['total'] > 100 || $res['request_priority'] != 3) {
                continue;
            }
            
            $company_name = $res['company_name'];
            // Include email routing logic
            include 'telco_email.inc';
            
            $subject = $res['message_subject'];
            $body = $res['message_body'];
            $reference_number = $res['request_id'];

            // SCOM rows store FIR + Cover Letter paths pipe-separated
            // in user_request.file_name (see Controller_Email::action_send).
            // Attach both files when emailing SCOM (company_name = 8).
            $scom_files = null;
            if ($company_name == 8 && !empty($res['file_name'])) {
                $scom_files = array_filter(array_map('trim', explode('|', $res['file_name'])));
            }

            // Send email
            $email_staus = Helpers_Email::send_email($to, $to_name, $subject, $body, null, $scom_files);
            
            // Update telco count
            include 'telco_count.inc';
            
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'send_high_priority',
                $e->getMessage(),
                [
                    'request_id' => $res['request_id'] ?? 'unknown',
                    'company_name' => $company_name ?? 'unknown',
                    'request_type_id' => $res['user_request_type_id'] ?? null,
                    'request_priority' => $res['request_priority'] ?? null
                ],
                $e->getTraceAsString(),
                'email_send_failure',
                'high_priority_send'
            );
            error_log("[" . date('c') . "] heigh.inc send failed for request " . ($res['request_id'] ?? 'unknown'));
        }
    }
    
} catch (Exception $e) {
    Model_ErrorLog::log(
        'send_high_priority_query',
        $e->getMessage(),
        [],
        $e->getTraceAsString(),
        'query_failure',
        'high_priority_fetch'
    );
    error_log("[" . date('c') . "] heigh.inc query failed: " . $e->getMessage());
}

?>