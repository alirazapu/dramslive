<?php
try {
    $query = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 0 and ur.processing_index = 0
                and ur.user_request_type_id = 4                
                ORDER BY ur.request_id  ASC";
            //and ur.company_name not in (1,3)
    $sql = DB::query(Database::SELECT, $query);
    $result = $sql->execute()->as_array();       
       
    
    foreach($result as $res)
    {    
        try {
            $query = "SELECT sum(total)as total FROM request_send_today where request_priority = " . $res['request_priority'];
            $sql = DB::query(Database::SELECT, $query);
            $total_count = $sql->execute()->current();         
            /*if($total_count['total']<= 370 && $res['request_priority']==3)
            {*/                       
                $company_name = $res['company_name'];
                /* telco email */
                //include '../send_other/telco_email.inc';
                include APPPATH . 'classes' . DS . 'Controller' . DS . 'cron_job' . DS . 'send_other' . DS . 'telco_email.inc';
                
                $subject = $res['message_subject'];
                $body = $res['message_body'];
                $reference_number = $res['request_id'];
                /* email send */
                $email_staus = Helpers_Email::send_email($to, $to_name, $subject, $body);
                
                /*email count flag */
                //include __DIR__ . DS . '..' . DS . 'send_other' . DS . 'telco_count.inc';
                include APPPATH . 'classes' . DS . 'Controller' . DS . 'cron_job' . DS . 'send_other' . DS . 'telco_count.inc';
                
           // }
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'send_location_heigh_loop',
                $e->getMessage(),
                [
                    'request_id' => $res['request_id'] ?? 'unknown',
                    'company_name' => $res['company_name'] ?? 'unknown',
                    'request_type_id' => $res['user_request_type_id'] ?? null,
                ],
                $e->getTraceAsString(),
                'email_send_failure',
                'location_send'
            );
            error_log("[" . date('c') . "] send_location/heigh.inc failed for request " . ($res['request_id'] ?? 'unknown'));
        }
    }     
} catch (Exception $e) {
    Model_ErrorLog::log(
        'send_location_heigh_query',
        $e->getMessage(),
        [],
        $e->getTraceAsString(),
        'query_failure',
        'location_fetch'
    );
    error_log("[" . date('c') . "] send_location/heigh.inc query failed: " . $e->getMessage());
}

?>