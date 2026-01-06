<?php
$query = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 0 and ur.processing_index = 0                
                and ur.user_request_type_id in (1,2,6)
                and ur.company_name = 3
                ORDER BY ur.request_priority  DESC, 
                ur.reference_id asc limit 7";
        $sql = DB::query(Database::SELECT, $query);
        $result = $sql->execute()->as_array();
        $count = 0;
        foreach($result as $res)
        {
            $query = "SELECT sum(total)as total FROM request_send_today where request_priority = " . $res['request_priority'];
                $sql = DB::query(Database::SELECT, $query);
                $total_count = $sql->execute()->current();         
                //echo $total_count['total'];                
                if($total_count['total']<= 600)
                {             
                    
                    $company_name = $res['company_name'];                  
                    /* telco email */
                    include 'telco_email.inc';                    
                    $subject = $res['message_subject'];                    
                    $body = strip_tags($res['message_body']);
                    $text=str_ireplace('&lt;p&gt;','',$body);                
                     $body=str_ireplace('&lt;/p&gt;','',$text); 
                     if($res['user_request_type_id']==2)
                     {
                         $body= substr_replace( $body, 0, strlen($body)-1);
                     }    
                    $reference_number = $res['request_id'];
                    $reference_idd = $res['reference_id'];
                    //create attachment start
                    $file_name = "/root/serverfiles/aies-home/aiesfiles/ufone_tem_files/". $reference_idd .".txt";
                    $myfile = fopen($file_name, "w") or die("Unable to open file!");
                    fwrite($myfile, $body);
                    fclose($myfile);                    
                    //create attachment end
                    //echo $file_name;                    
                    //echo $to='test@gmail.com';
                    $body = $reference_idd .".txt";
                    $query = "select *
                        FROM `user_request` as ur
                        where ur.status = 0 and ur.processing_index = 0                      
                        and ur.user_request_type_id != 8
                        and ur.user_request_type_id != 10
                        and ur. request_id = {$reference_number}
                        ORDER BY ur.request_id  ASC LIMIT 1";
                        $sql = DB::query(Database::SELECT, $query);
                        $re_result = $sql->execute()->current();                        
                    /* email send */
                    $email_staus = '';                    
                        if(!empty($re_result) && !empty($re_result['request_id']))
                            $email_staus = Helpers_Email::send_email($to, $to_name, $subject, $body, $file_name);
                                        
                    /*telco count */
                    include 'telco_count.inc'; 
                    
                    //if exist then delete
                    if (file_exists($file_name)) {
                        unlink($file_name);
                    }                    
                }else{
                    
                    $company_name = $res['company_name'];                  
                    /* telco email */
                    include 'telco_email.inc';                    
                    $subject = $res['message_subject'];                    
                    $body = strip_tags($res['message_body']);
                    $text=str_ireplace('&lt;p&gt;','',$body);                
                     $body=str_ireplace('&lt;/p&gt;','',$text); 
                     if($res['user_request_type_id']==2)
                     {
                         $body= substr_replace( $body, 0, strlen($body)-1);
                     }    
                    $reference_number = $res['request_id'];
                    $reference_idd = $res['reference_id'];
                    //create attachment start
                    $file_name = "/root/serverfiles/aies-home/aiesfiles/ufone_tem_files/". $reference_idd .".txt";
                    $myfile = fopen($file_name, "w") or die("Unable to open file!");
                    fwrite($myfile, $body);
                    fclose($myfile);                    
                    //create attachment end
                    //echo $file_name;                    
                    //echo $to='test@gmail.com';
                    $body = $reference_idd .".txt";
                    $query = "select *
                        FROM `user_request` as ur
                        where ur.status = 0 and ur.processing_index = 0                      
                        and ur.request_priority = 3
                        and ur.user_request_type_id != 8
                        and ur.user_request_type_id != 10
                        and ur. request_id = {$reference_number}
                        ORDER BY ur.request_id  ASC LIMIT 1";
                        $sql = DB::query(Database::SELECT, $query);
                        $re_result = $sql->execute()->current();                        
                    /* email send */
                    $email_staus = '';                    
                        if(!empty($re_result) && !empty($re_result['request_id']))
                            $email_staus = Helpers_Email::send_email($to, $to_name, $subject, $body, $file_name);
                                        
                    /*telco count */
                    include 'telco_count.inc'; 
                    
                    //if exist then delete
                    if (file_exists($file_name)) {
                        unlink($file_name);
                    }   
                    
                }
                
            
        }     
        
        
?>        