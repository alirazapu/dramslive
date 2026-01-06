<?php
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
            $query = "SELECT sum(total)as total FROM request_send_today where request_priority = " . $res['request_priority'];
                $sql = DB::query(Database::SELECT, $query);
                $total_count = $sql->execute()->current();         
                /*if($total_count['total']<= 370 && $res['request_priority']==3)
                {*/                       
                    $company_name = $res['company_name'];
                    /* telco email */
                    //include '../send_other/telco_email.inc';
                    include '/var/www/html/aies/application/classes/Controller/cron_job/send_other/telco_email.inc';
                    
                    $subject = $res['message_subject'];
                    $body = $res['message_body'];
                    $reference_number = $res['request_id'];
                    /* email send */
                    $email_staus = Helpers_Email::send_email($to, $to_name, $subject, $body);
                    
                    /*email count flag */
                    //include '../send_other/telco_count.inc';
                    include '/var/www/html/aies/application/classes/Controller/cron_job/send_other/telco_count.inc';
                    
               // }                
        }     
        
        


?>