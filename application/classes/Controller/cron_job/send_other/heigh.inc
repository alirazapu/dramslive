<?php

$query = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 0 and ur.processing_index = 0
                and ur.request_priority = 3
                and ur.user_request_type_id != 8
                ORDER BY ur.request_id  ASC";
        $sql = DB::query(Database::SELECT, $query);
        $result = $sql->execute()->as_array();       

        $count = 0;
        foreach($result as $res)
        {

            //if($count<=7)
            if($count<=7)
            {
            $query = "SELECT sum(total)as total FROM request_send_today where request_priority = " . $res['request_priority'];
                $sql = DB::query(Database::SELECT, $query);
                $total_count = $sql->execute()->current();         
                if($total_count['total']<= 100 && $res['request_priority']==3)
                {
                    $company_name = $res['company_name'];

                    //tempararey line added for telnor case 
                   if(($company_name == 3 || $company_name == 6) && ($res['user_request_type_id']==1 || $res['user_request_type_id']==2))
                    { 
                        
                    }
                    else{    
                        /* telco email */
                        include 'telco_email.inc';

                        $subject = $res['message_subject'];
                        $body = $res['message_body'];
                        $reference_number = $res['request_id'];
                        /* email send */
                        $email_staus = Helpers_Email::send_email($to, $to_name, $subject, $body);
                        /*telco count */
                        include 'telco_count.inc'; 
                    }
                }                
            }   
        }     


?>