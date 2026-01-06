<?php

//and ur.company_name not in (1,3)
        
$query = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 0 and ur.processing_index = 0
                and ur.request_priority = 1
                and ur.user_request_type_id != 8
                and ur.user_request_type_id != 10                
                ORDER BY ur.request_id  ASC";
        $sql = DB::query(Database::SELECT, $query);
        $result = $sql->execute()->as_array();
        
        //$count = 0;
        foreach($result as $res)
        {
              
            //if($count<=7 && ($res['company_name']==3 || $res['company_name'] == 4))
            if($count<=7)
            {    
            $query = "SELECT sum(total)as total FROM request_send_today where request_priority = " . $res['request_priority'];
                $sql = DB::query(Database::SELECT, $query);
                $total_count = $sql->execute()->current();         
                
                // previous low request 650
                if($total_count['total']<= 1200 && $res['request_priority']==1)
                { 
                          
                    $company_name = $res['company_name'];
                  
                    //tempararey line added for telnor case 
                    //($company_name == 3 && ($res['user_request_type_id']==4 || $res['user_request_type_id']==3)) ||
                    if($company_name == 3 && ($res['user_request_type_id']==5))
                    {
                        $reference_number_result = Model_Email::email_sending_status($res['request_id'], 1, 0);
                        
                    }elseif(     ($company_name == 3 && ($res['user_request_type_id']==1 || $res['user_request_type_id']==2 || $res['user_request_type_id']==6 || $res['user_request_type_id']==5)) ||
                            ($company_name == 6 && ($res['user_request_type_id']==1 || $res['user_request_type_id']==2)))// || $res['user_request_type_id']==3 || $res['user_request_type_id']==5)))
                    { 
                        
                    }
                    else{
                    /* telco email */
                    include 'telco_email.inc';
                    
                    $subject = $res['message_subject'];
                    $body = $res['message_body'];
                    $reference_number = $res['request_id'];
                    
                    $query = "select *
                        FROM `user_request` as ur
                        where ur.status = 0 and ur.processing_index = 0
                        and ur.request_priority = 1
                        and ur.user_request_type_id != 8
                        and ur.user_request_type_id != 10
                        and ur. request_id = {$reference_number}
                        ORDER BY ur.request_id  ASC LIMIT 1";
                        $sql = DB::query(Database::SELECT, $query);
                        $re_result = $sql->execute()->current();
                        
                    /* email send */
                        if(!empty($re_result) && !empty($re_result['request_id']))
                            $email_staus = Helpers_Email::send_email($to, $to_name, $subject, $body);
                                        
                    /*telco count */
                    include 'telco_count.inc'; 
                    }
                }
                
            }   
        }     
        
        
?>        