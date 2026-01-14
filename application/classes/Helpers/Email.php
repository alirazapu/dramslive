<?php

abstract class Helpers_Email {    
    
    public static function send_email($to, $to_name, $subject, $body, $attachment=NULL)
    {  
        $mail = new PHPMailer(); // create a new object
        
        $file_name = '';
        if(!empty($attachment))
        {
            if(!empty(strip_tags($body)))
            {
                $file_name = strip_tags($body) . '.txt';
            }else{
                $file_name = 'request.txt';
            }
            $body = '<p></p> ';
        }        
        include 'gmail/sending.inc';                        
        if(!empty($attachment))
        {
            //$mail->addAttachment($attachment,'application/octet-stream');
            $mail->addStringAttachment(file_get_contents($attachment), $file_name);         // Add attachments
        }
         if(!$mail->Send()) {
           //  echo '<pre>';
           // echo "Mailer Error: " . $mail->ErrorInfo;            
            return 2;
            //exit;
         } else {
            //echo "Message has been sent";
            //exit;
            return 1;
         }
         exit;
    }
    
    
    public static function receive_email($subject, $sender)
    {         
       $filename= '';
        /* Create gmail connection */
        //$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
        //$hostname = '{imap.gmail.com:995/imap/ssl}INBOX';
        $hostname = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
        if(!empty($sender) && $sender==2)
        {
            $result = Helpers_Inneruse::get_gmail_pw();
            $username = $result['send']['user'];
            $password = $result['send']['password'];
            
            $since = date("D, d M Y", strtotime("-15 days")); /* added range */
            $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());
           // $date = date ( "d M Y", strtotime ( "-45 days" ) );
            $search_criteria = 'UNSEEN SINCE "'.$since.' 00:00:00 -0700 (PDT)"';
           // echo $search_criteria; exit;
            //$emails = imap_search($inbox, 'UNSEEN SINCE "'.$since.' 00:00:00 -0700 (PDT)"');
         //$emails = imap_search($inbox, $search_criteria, SE_UID);
         $emails = imap_search($inbox, 'UNSEEN'); //updated 23 april 2025 against above line
         $emails = array_flip ($emails);
           // echo '<pre>';
           // print_r($emails); exit;

        }else{
            include 'gmail/receiving.inc';        
            //echo $username; exit;
            $since = date("D, d M Y", strtotime("-12 days")); /* added range */
            $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());
            $emails = imap_search($inbox, 'UNSEEN SINCE "'.$since.' 00:00:00 -0700 (PDT)"');
            
            //$inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());
            //$emails = imap_search($inbox, 'UNSEEN');
        }
        
        
        //$sender='test@gmail.com';
        /* Fetch emails */
        //echo $subject;
        //$emails = imap_search($inbox, "ALL");
        //$emails = imap_search($inbox, 'UNSEEN FROM "'.$sender.'"');

        //$emails = imap_search($inbox, 'SUBJECT "'.$subject.'" UNSEEN FROM "'.$sender.'"');
        
        
        
            //echo '<pre>';
           // print_r($emails);
                
        
        
        /* If emails are returned, cycle through each... */
        if ($emails) {            
            
            $output = '';

            /* Make the newest emails on top */
            rsort($emails);
/*
foreach ($emails as $mail) {
    //$status = imap_setflag_full($inbox, $mail, "\\Seen", ST_UID);
    //$status = imap_setflag_full($inbox, $mail, "\\Seen"); 
    $status = imap_setflag_full($inbox, $mail, "\Seen \Flagged"); 
    //$status = imap_setflag_full($inbox, $overview[0]->msgno, "\Seen \Flagged");
}*/
            
            /* For each email... */
            foreach ($emails as $email_number) {
                $is_file_exist=0;
              //  echo '<br>' . $email_number . '<br>';
                
                /*
                 * Email read or not */
               /* $e_status= Helpers_Email::emailreadstatuscheck($email_number);
                if(!empty($e_status))
                    continue;
               */ 
                /* */
                
                $headerInfo = imap_headerinfo($inbox, $email_number);
                $structure= '';
                $structure = imap_fetchstructure($inbox, $email_number);

                /* get information specific to this email */
                $overview = imap_fetch_overview($inbox, $email_number, 0);
                
                /* get mesage body */
              //  $message = imap_qprint(imap_fetchbody($inbox, $email_number, 0));
               // print_r($message); 
                    $message = imap_fetchbody($inbox,$email_number, 1.2);                                
                if(empty($message))
                    $message = quoted_printable_decode(imap_fetchbody($inbox,$email_number,1));    
                if(empty($message))
                    $message = imap_fetchbody($inbox,$email_number,2);
                
                /*$message_raw = quoted_printable_decode(imap_fetchbody($inbox,$email_number,1));    
                if(empty($message_raw))*/
                   /* 
               if (strpos($overview[0]->subject, 'Hi') !== false) {
                    echo $overview[0]->subject;
                    exit;
                }
                */
               $check_match = trim(str_replace("Re:", "", $overview[0]->subject));
               
                /* explode subject */
                $string_replace = str_replace("/", " /", $overview[0]->subject);
//                $string_replace = str_replace(" - Hi", "", $string_replace);
//                $string_replace = str_replace("- Hi", "", $string_replace);
//                $string_replace = str_replace("-", "", $string_replace);
//                $string_replace = str_replace("Hi", "", $string_replace);
                $string_replace = str_replace(",", " ,", $string_replace);
                $string_replace = str_replace(".", " . ", $string_replace);
                $query_subject = explode(' ', $string_replace);
                $query_subject_final = '';
                //echo '<pre>';
                /*$array_val= array_values(array_filter($query_subject));
                foreach ($array_val as $key => $value) {
                    if(is_numeric(trim($value)))
                    {
                        $query_subject_final = trim($array_val[$key]);
                    }
                }*/
                preg_match_all('/\b\d+\b/', $string_replace, $matches);
                $query_subject_final = $matches[0][0]?? '';

                if(empty($query_subject_final))
                    continue; 
                
                
                //echo '<br>' . $query_subject_final . '<br>';
               // echo $query_subject_final;
                
                //$query_subject_final = $query_subject[sizeof($query_subject)-2] . ' ' . $query_subject[sizeof($query_subject)-1];
                
                //echo $query_subject_final;
                /*new code with new logic start **/                
               // echo '<br>' . $query_subject_final . '<br>';
                
                if(!empty($query_subject_final))
                {    
                    //$e_status= Helpers_Email::emailreadstatuscheckUpdate($email_number, $query_subject_final);
                    $query_subject_final= Helpers_Email::emailreadstatuscheckUpdate($email_number, $query_subject_final);


                $sql = "SELECT request_id, reason, user_id, user_request_type_id, email_type_name, requested_value, concerned_person_id, t1.company_name,
                       created_at, status, processing_index, em.message_id, em.message_subject, em.sender_id FROM 
                       user_request as t1 
                       join email_templates_type as t2                                 
                       on t1.user_request_type_id = t2.id                       
                       join email_messages as em on em.message_id = t1.message_id   
                       and t1.user_request_type_id != 8
                       and t1.status = 1 and t1.processing_index = 0 
                       and t1.reference_id = {$query_subject_final};
                       ";                              //Where t1.user_id = {$user_id}  and em.message_subject = {$query_subject_final};
                       
                       $members = DB::query(Database::SELECT, $sql)->execute()->current();//->as_array();
                }else{
                    $members='';
                }
                
                      // print_r($members);
                       
                      // exit;
                       /*
                       if(!empty($members))
                       foreach ($members as $item) 
                       {
                             if(!empty($item['status']) && $item['status']==1 && !empty($item['message_subject']) && !empty($item['sender_id']))
                               {

                                   $email_sender = Helpers_Email::receive_email($item['message_subject'], $item['sender_id']);

                                   if($email_sender!=1)
                                   {   
                                       $file_name = !empty($email_sender['file'])?$email_sender['file']:'na';                         
                                       $body = !empty($email_sender['body'])?$email_sender['body']:'na';
                                       $body_raw = !empty($email_sender['body_raw'])?$email_sender['body_raw']:'na';                            
                                       Helpers_Email::change_status_raw($file_name, $body_raw, $body, $item['message_id'], $item['request_id']);
                                   }

                               }   
                       }*/
                
                /* new code with new logic end */
                
               // try {                
                //if((strpos($overview[0]->subject, $subject) !== false) && (strpos($overview[0]->from, $sender) !== false))
                //if((strpos($overview[0]->subject, $subject) !== false))
                /* if(strcmp($check_match,$subject) >= 0)
                 {  */
                                /*
                  // If attachment found use this one
                  // $message = imap_qprint(imap_fetchbody($inbox,$email_number,"1.2"));
                 */
//echo '<br>';
//print_r($members);   
if(!empty($members))
{   
                    $part = !empty($structure->parts[1])?$structure->parts[1]:'';                    
                     if($members['company_name']==13 || $members['company_name']==12 || $members['company_name']==11 || $members['company_name']==4 || $members['company_name']==3 )
                    {    
                        $message_raw = imap_fetchbody($inbox,$email_number,2);
                        if(!empty($part) && $part->encoding == 3) {
                            $message_raw = imap_base64($message_raw);
                        } else if(!empty($part) && $part->encoding == 1) {
                            $message_raw = imap_8bit($message_raw);
                        } else {
                            $message_raw = imap_qprint($message_raw);
                        }
                    }elseif($members['company_name']==6)
                        $message_raw = imap_base64(imap_fetchbody($inbox,$email_number,1));
                    else
                        $message_raw = imap_fetchbody($inbox,$email_number,1);
                    
               /*telco code for received email */
                $date_for_telco = date('Y-m-d'); //$date;
                $tel_report = "select * from telco_request_summary where date = '{$date_for_telco}'";
                $sql_telco = DB::query(Database::SELECT, $tel_report);
                $report_telco_result = $sql_telco->execute()->as_array();

                if(!empty($report_telco_result))
                {
                    $query = DB::update('telco_request_summary')->set(array('total_received'=>DB::expr('total_received + 1')))
                                    ->where('date', '=', $date_for_telco)   
                                    ->and_where('company_mnc', '=', $members['company_name'])
                                    ->execute();
                }else{
                    $tel_co_mnc = array(1,3,4,6,7,11,12,13);
                    $telco_array= array();
                    foreach($tel_co_mnc as $mnc)
                    {    
                        $company_mnc=$mnc;$send_high=0;$send_medium=0;$send_low=0;$total_send=0;$total_received=0; 
                        $t_date='"'. $date_for_telco . '"';                        
                        $telco_array[]='('. $t_date .', '. $company_mnc .', '. $send_high .', '. $send_medium .', '. $send_low .', '. $total_send. ')';

                    }
                    $query = 'INSERT INTO telco_request_summary (`date`, `company_mnc`, `send_high`, `send_medium`, `send_low`, `total_send`) VALUES '.implode(',', $telco_array);
                    $sql = DB::query(Database::INSERT, $query)->execute(); 
                }
            
                /*telco code for received email */
                //check issue not reach here 
    
    
                $output .= 'Subject: ' . $overview[0]->subject . '<br />';
                $output .= 'Body: ' . $message . '<br />';
                $output .= 'From: ' . $overview[0]->from . '<br />';
                $output .= 'Date: ' . $overview[0]->date . '<br />';
//$output .= 'CC: '.$headerInfo->ccaddress.'<br />';
//  Attachments
                $attachments = array();
                if (isset($structure->parts) && count($structure->parts)) {
                    for ($i = 0; $i < count($structure->parts); $i++) {
                        $attachments[$i] = array(
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => ''
                        );

                        if ($structure->parts[$i]->ifdparameters) {
                            foreach ($structure->parts[$i]->dparameters as $object) {
                                if (strtolower($object->attribute) == 'filename') {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['filename'] = $object->value;
                                }
                            }
                        }

                        if ($structure->parts[$i]->ifparameters) {
                            foreach ($structure->parts[$i]->parameters as $object) {
                                if (strtolower($object->attribute) == 'name') {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['name'] = $object->value;
                                }
                            }
                        }

                        if ($attachments[$i]['is_attachment']) {
                            $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i + 1);

                            /* 3 = BASE64 encoding */
                            if ($structure->parts[$i]->encoding == 3) {
                                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                            }
                            /* 4 = QUOTED-PRINTABLE encoding */ elseif ($structure->parts[$i]->encoding == 4) {
                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                            }
                        }
                    }
                }
                $filename='';
   
                foreach ($attachments as $attachment) {
                    if ($attachment['is_attachment'] == 1) {
                        $filename = !empty($attachment['name'])?$attachment['name']:$attachment['filename'];
                       // echo $filename;
                        /* if($members['company_name']==6)
                        {
                            if (empty($filename))
                            {   
                                    $filename = $attachment['name'];
                            }else{                            
                                    $filename = $query_subject_final. '_'. $attachment['name'];                            

                            }                               
                        }else{
                            */
                        //getting file_id 
                            $file_id = Helpers_Upload::get_fileid_with_requestid($members['request_id']);
                            if(!empty($file_id))
                            {
                                $is_file_exist =1;
                            }else{    
                                $is_file_exist =0;
                                $file_id = Helpers_Utilities::id_generator("file_id");    
                            }
                            $file_path = !empty($file_id) ? Helpers_Upload::get_request_data_path($file_id,'save') : '';
                             
                            $new_file_info=PATHINFO($attachment['filename']);
                           if (!empty($filename) && !empty($new_file_info) && !empty($new_file_info['extension'])) {  
                            if (!empty($filename)) {
                                $filename = 'rqt'.$query_subject_final . 'fid' . $file_id . '.' . $new_file_info['extension'];
                            } else {
                                $filename = 'rqt'.$query_subject_final . 'fid' . $file_id . '.' . $new_file_info['extension'];
                                //$filename = $query_subject_final . '_' . $attachment['filename'];
                           }}
                        //}
                            //echo $filename;                        
                        //$file_path = 'uploads' . DIRECTORY_SEPARATOR . 'cdr' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR; //  Upload folder
                        $fp = fopen($file_path . $filename, "w+");
                        /*if($members['company_name']==6)                        
                            fwrite($fp, base64_decode($attachment['attachment']));
                        else */
                            fwrite($fp, $attachment['attachment']);
                        
                        
                        fclose($fp);
                    }
                }
//  Attachments

                /* change the status */
                //$status = imap_setflag_full($inbox, $overview[0]->msgno, "\Seen \Flagged"); //i will use later
                $status = imap_setflag_full($inbox, $email_number, "\Seen \Flagged"); //i will use later
                //imap_clearflag_full($inbox,$overview[0]->msgno,"//Seen");  //Seen
                
                    $result = array();
                    $result['file'] = $filename; 
                    try{
                    $extrac= explode("On Sun,",$message);
                    $extrac= explode("On Mon,",$extrac[0]);
                    $extrac= explode("On Tue,",$extrac[0]);
                    $extrac= explode("On Wed,",$extrac[0]);
                    $extrac= explode("On Thu,",$extrac[0]);
                    $extrac= explode("On Fri,",$extrac[0]);
                    $extrac= explode("On Sat,",$extrac[0]);
                    $result['body'] = strip_tags($extrac[0]);
                    }catch (Exception $e)
                    {
                        $result['body'] = $message_raw;
                    }
                    $result['body_raw'] = $message_raw;
                    //imap_close($inbox);
                    
                    $file_name = !empty($result['file'])?$result['file']:'na';                         
                    $body = !empty($result['body'])?$result['body']:'na';
                    $body_raw = !empty($result['body_raw'])?$result['body_raw']:'na';                            
                    
                    if($members['company_name']>=11 && $members['company_name']<=13)
                    {
                        $process_index=7;
                    }else{
                        $process_index=4;
                    }
                    
                    //Helpers_Email::change_status_raw($file_name, $body_raw, $body, $members['message_id'], $members['request_id'], $process_index);                   
                   if(!empty($file_id) && $file_name!='na' && $is_file_exist==0)
                   {    
                        Helpers_Upload::insert_file_record($file_name, $members['user_id'], $members['user_request_type_id'], $members['company_name'], $members['requested_value'], $members['request_id'], $members['reason'], $file_id);  
                   }
                       Helpers_Email::change_status_raw($file_name, $body_raw, $body, $members['message_id'], $members['request_id'], $is_file_exist);
                   
                    
                    
                        
        
                    //return $result;
               // }
             /*   } catch (ORM_Validation_Exception $e) {
                    return 1;
                }*/
                  //  exit;
//imap_close($inbox);
//exit;                       
}
else{
    //imap_setflag_full($mbox, "2,5", "\\Seen \\Flagged");
    //imap_clearflag_full($inbox,$overview[0]->msgno,'\\Seen');  //Seen
    imap_clearflag_full($inbox,$email_number,'\\Seen');  //Seen
    //imap_setflag_full($inbox, $overview[0]->msgno, "\\Seen \\Flagged", ST_UID);
   // echo '<br>  N ' . $overview[0]->msgno;
    //$message = imap_fetchbody($inbox,$overview[0]->msgno,1, FT_PEEK);    
    //imap_fetchbody($inbox,$overview[0]->msgno,1, FT_PEEK);
    //$status = imap_setflag_full($inbox, $overview[0]->msgno, "\\Seen"); //i will use later
} 
            }            
            //echo $output;
        }

        /* close the connection */
        imap_close($inbox);
         return 1;
        
    }
    public static function receive_email_backup($subject, $sender)
    {         
       $filename= '';
        /* Create gmail connection */
        //$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
        //$hostname = '{imap.gmail.com:995/imap/ssl}INBOX';
        $hostname = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
            include 'gmail/receiving.inc';        
            //echo $username; exit;
            //mlrz rtyk vzhk ijbn
            $password = 'mlrz rtyk vzhk ijbn';
//            $since = date("D, d M Y", strtotime("-1 days")); /* added range */
//            $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());
//            $emails = imap_search($inbox, 'UNSEEN SINCE "'.$since.' 00:00:00 -0700 (PDT)"');
            //$emails = imap_search($inbox, 'UNSEEN SINCE "' . $since . '"');



$hostname = '{imap.gmail.com:993/imap/ssl/novalidate-cert}[Gmail]/All Mail';  // SSL + skip cert validation :contentReference[oaicite:9]{index=9}
$inbox = imap_open($hostname, $username, $password)
    or die('Cannot connect to Gmail: ' . imap_last_error());  // die on failure :contentReference[oaicite:10]{index=10}

$since  = date('d-M-Y', strtotime('-3 days'));  // e.g. "19-Apr-2025" :contentReference[oaicite:7]{index=7}
$before = date('d-M-Y', strtotime('today'));    // e.g. "21-Apr-2025"

// 3) Search unseen within that window
$criteria = sprintf(
    'UNSEEN SINCE "%s" BEFORE "%s"',
    $since,
    $before
);
//$emails = imap_search($inbox, $criteria);  // returns messages with internal date ≥ $since and < $before :contentReference[oaicite:8]{index=8}
$emails = imap_search($inbox, 'UNSEEN');

            /*
            echo '<pre>'; 
            print_r($emails);
imap_close($inbox);            
            echo 'test'; exit;
                */
        
        
        /* If emails are returned, cycle through each... */
        if ($emails) {            
            
            $output = '';

            /* Make the newest emails on top */
            rsort($emails);
/*
foreach ($emails as $mail) {
    //$status = imap_setflag_full($inbox, $mail, "\\Seen", ST_UID);
    //$status = imap_setflag_full($inbox, $mail, "\\Seen"); 
    $status = imap_setflag_full($inbox, $mail, "\Seen \Flagged"); 
    //$status = imap_setflag_full($inbox, $overview[0]->msgno, "\Seen \Flagged");
}*/
            
            /* For each email... */
            foreach ($emails as $email_number) {
                $is_file_exist=0;
              //  echo '<br>' . $email_number . '<br>';
                
                /*
                 * Email read or not */
               /* $e_status= Helpers_Email::emailreadstatuscheck($email_number);
                if(!empty($e_status))
                    continue;
               */ 
                /* */
                
                $headerInfo = imap_headerinfo($inbox, $email_number);
                $structure= '';
                $structure = imap_fetchstructure($inbox, $email_number);

                /* get information specific to this email */
                $overview = imap_fetch_overview($inbox, $email_number, 0);
                
                /* get mesage body */
              //  $message = imap_qprint(imap_fetchbody($inbox, $email_number, 0));
               // print_r($message); 
                    $message = imap_fetchbody($inbox,$email_number, 1.2);                                
                if(empty($message))
                    $message = quoted_printable_decode(imap_fetchbody($inbox,$email_number,1));    
                if(empty($message))
                    $message = imap_fetchbody($inbox,$email_number,2);
                
                /*$message_raw = quoted_printable_decode(imap_fetchbody($inbox,$email_number,1));    
                if(empty($message_raw))*/
                   /* 
               if (strpos($overview[0]->subject, 'Hi') !== false) {
                    echo $overview[0]->subject;
                    exit;
                }
                */
               $check_match = trim(str_replace("Re:", "", $overview[0]->subject));
              // echo '<br>';
                /* explode subject */
               $string_replace = str_replace("/", " /", $overview[0]->subject);
//                $string_replace = str_replace(" - Hi", "", $string_replace);
//                $string_replace = str_replace("- Hi", "", $string_replace);
//                $string_replace = str_replace("-", "", $string_replace);
//                $string_replace = str_replace("Hi", "", $string_replace);
                $string_replace = str_replace(",", " ,", $string_replace);
                $string_replace = str_replace(".", " . ", $string_replace);
                 $query_subject_final = '';                
               /* $query_subject = explode(' ', $string_replace);
              
                $array_val= array_values(array_filter($query_subject));
                foreach ($array_val as $key => $value) {
                    if(is_numeric(trim($value)))
                    {
                        $query_subject_final = trim($array_val[$key]);
                    }
                }*/
                 echo '<br>' . $string_replace;
                preg_match_all('/\b\d+\b/', $string_replace, $matches);
                $query_subject_final = $matches[0][0]?? '';
                $word = 'ADM-';
                $word_1 = 'QRM';
                if(empty($query_subject_final) || $query_subject_final < 1000 
                        || (strpos($string_replace, $word) !== false)
                        || (strpos($string_replace, $word_1) !== false))
                {    
                    $status = imap_setflag_full($inbox, $email_number, "\Seen \Flagged"); //i will use later
                    continue; 
                }
                echo '<br>' . $query_subject_final;
                
                                
//                echo '<br>' . $query_subject_final . '<br>';
//                exit;
                //echo $query_subject_final;
                
                //$query_subject_final = $query_subject[sizeof($query_subject)-2] . ' ' . $query_subject[sizeof($query_subject)-1];
                
                //echo $query_subject_final;
                /*new code with new logic start **/                
               // echo '<br>' . $query_subject_final . '<br>';
                
                if(!empty($query_subject_final) &&  $query_subject_final> 1000)
                {    
                    echo $query_subject_final . ' <br> '; 
                    //$e_status= Helpers_Email::emailreadstatuscheckUpdate($email_number, $query_subject_final);
                    $query_subject_final= Helpers_Email::emailreadstatuscheckUpdate($email_number, $query_subject_final);

                    
                $sql = "SELECT request_id, reason, user_id, user_request_type_id, email_type_name, requested_value, concerned_person_id, t1.company_name,
                       created_at, status, processing_index, em.message_id, em.message_subject, em.sender_id FROM 
                       user_request as t1 
                       join email_templates_type as t2                                 
                       on t1.user_request_type_id = t2.id                       
                       join email_messages as em on em.message_id = t1.message_id   
                       and t1.user_request_type_id != 8
                       and t1.status = 1 and t1.processing_index = 0 
                       and t1.reference_id = {$query_subject_final};
                       ";                              //Where t1.user_id = {$user_id}  and em.message_subject = {$query_subject_final};
                       //echo $sql; 
                       $members = DB::query(Database::SELECT, $sql)->execute()->current();//->as_array();
                }else{
                    $members='';
                }
                
                      // print_r($members);
                       
                      // exit;
                       /*
                       if(!empty($members))
                       foreach ($members as $item) 
                       {
                             if(!empty($item['status']) && $item['status']==1 && !empty($item['message_subject']) && !empty($item['sender_id']))
                               {

                                   $email_sender = Helpers_Email::receive_email($item['message_subject'], $item['sender_id']);

                                   if($email_sender!=1)
                                   {   
                                       $file_name = !empty($email_sender['file'])?$email_sender['file']:'na';                         
                                       $body = !empty($email_sender['body'])?$email_sender['body']:'na';
                                       $body_raw = !empty($email_sender['body_raw'])?$email_sender['body_raw']:'na';                            
                                       Helpers_Email::change_status_raw($file_name, $body_raw, $body, $item['message_id'], $item['request_id']);
                                   }

                               }   
                       }*/
                
                /* new code with new logic end */
                
               // try {                
                //if((strpos($overview[0]->subject, $subject) !== false) && (strpos($overview[0]->from, $sender) !== false))
                //if((strpos($overview[0]->subject, $subject) !== false))
                /* if(strcmp($check_match,$subject) >= 0)
                 {  */
                                /*
                  // If attachment found use this one
                  // $message = imap_qprint(imap_fetchbody($inbox,$email_number,"1.2"));
                 */
//echo '<br>';
//print_r($members); //  exit;
if(!empty($members))
{   
                    $part = !empty($structure->parts[1])?$structure->parts[1]:'';                    
                     if($members['company_name']==13 || $members['company_name']==12 || $members['company_name']==11 || $members['company_name']==4 || $members['company_name']==3 )
                    {    
                        $message_raw = imap_fetchbody($inbox,$email_number,2);
                        if(!empty($part) && $part->encoding == 3) {
                            $message_raw = imap_base64($message_raw);
                        } else if(!empty($part) && $part->encoding == 1) {
                            $message_raw = imap_8bit($message_raw);
                        } else {
                            $message_raw = imap_qprint($message_raw);
                        }
                    }elseif($members['company_name']==6)
                        $message_raw = imap_base64(imap_fetchbody($inbox,$email_number,1));
                    else
                        $message_raw = imap_fetchbody($inbox,$email_number,1);
                    
               /*telco code for received email */
                $date_for_telco = date('Y-m-d'); //$date;
                $tel_report = "select * from telco_request_summary where date = '{$date_for_telco}'";
                $sql_telco = DB::query(Database::SELECT, $tel_report);
                $report_telco_result = $sql_telco->execute()->as_array();

                if(!empty($report_telco_result))
                {
                    $query = DB::update('telco_request_summary')->set(array('total_received'=>DB::expr('total_received + 1')))
                                    ->where('date', '=', $date_for_telco)   
                                    ->and_where('company_mnc', '=', $members['company_name'])
                                    ->execute();
                }else{
                    $tel_co_mnc = array(1,3,4,6,7,11,12,13);
                    $telco_array= array();
                    foreach($tel_co_mnc as $mnc)
                    {    
                        $company_mnc=$mnc;$send_high=0;$send_medium=0;$send_low=0;$total_send=0;$total_received=0; 
                        $t_date='"'. $date_for_telco . '"';                        
                        $telco_array[]='('. $t_date .', '. $company_mnc .', '. $send_high .', '. $send_medium .', '. $send_low .', '. $total_send. ')';

                    }
                    $query = 'INSERT INTO telco_request_summary (`date`, `company_mnc`, `send_high`, `send_medium`, `send_low`, `total_send`) VALUES '.implode(',', $telco_array);
                    $sql = DB::query(Database::INSERT, $query)->execute(); 
                }
            
                /*telco code for received email */
                //check issue not reach here 
    
    
                $output .= 'Subject: ' . $overview[0]->subject . '<br />';
                $output .= 'Body: ' . $message . '<br />';
                $output .= 'From: ' . $overview[0]->from . '<br />';
                $output .= 'Date: ' . $overview[0]->date . '<br />';
//$output .= 'CC: '.$headerInfo->ccaddress.'<br />';
//  Attachments
                $attachments = array();
                if (isset($structure->parts) && count($structure->parts)) {
                    for ($i = 0; $i < count($structure->parts); $i++) {
                        $attachments[$i] = array(
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => ''
                        );

                        if ($structure->parts[$i]->ifdparameters) {
                            foreach ($structure->parts[$i]->dparameters as $object) {
                                if (strtolower($object->attribute) == 'filename') {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['filename'] = $object->value;
                                }
                            }
                        }

                        if ($structure->parts[$i]->ifparameters) {
                            foreach ($structure->parts[$i]->parameters as $object) {
                                if (strtolower($object->attribute) == 'name') {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['name'] = $object->value;
                                }
                            }
                        }

                        if ($attachments[$i]['is_attachment']) {
                            $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i + 1);

                            /* 3 = BASE64 encoding */
                            if ($structure->parts[$i]->encoding == 3) {
                                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                            }
                            /* 4 = QUOTED-PRINTABLE encoding */ elseif ($structure->parts[$i]->encoding == 4) {
                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                            }
                        }
                    }
                }
                $filename='';
   
                foreach ($attachments as $attachment) {
                    if ($attachment['is_attachment'] == 1) {
                        $filename = !empty($attachment['name'])?$attachment['name']:$attachment['filename'];
                       // echo $filename;
                        /* if($members['company_name']==6)
                        {
                            if (empty($filename))
                            {   
                                    $filename = $attachment['name'];
                            }else{                            
                                    $filename = $query_subject_final. '_'. $attachment['name'];                            

                            }                               
                        }else{
                            */
                        //getting file_id 
                            $file_id = Helpers_Upload::get_fileid_with_requestid($members['request_id']);
                            if(!empty($file_id))
                            {
                                $is_file_exist =1;
                            }else{    
                                $is_file_exist =0;
                                $file_id = Helpers_Utilities::id_generator("file_id");    
                            }
                            $file_path = !empty($file_id) ? Helpers_Upload::get_request_data_path($file_id,'save') : '';
                             
                            $new_file_info=PATHINFO($attachment['filename']);
                           if (!empty($filename) && !empty($new_file_info) && !empty($new_file_info['extension'])) {  
                            if (!empty($filename)) {
                                $filename = 'rqt'.$query_subject_final . 'fid' . $file_id . '.' . $new_file_info['extension'];
                            } else {
                                $filename = 'rqt'.$query_subject_final . 'fid' . $file_id . '.' . $new_file_info['extension'];
                                //$filename = $query_subject_final . '_' . $attachment['filename'];
                           }}
                        //}
                            //echo $filename;                        
                        //$file_path = 'uploads' . DIRECTORY_SEPARATOR . 'cdr' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR; //  Upload folder
                        $fp = fopen($file_path . $filename, "w+");
                        /*if($members['company_name']==6)                        
                            fwrite($fp, base64_decode($attachment['attachment']));
                        else */
                            fwrite($fp, $attachment['attachment']);
                        
                        
                        fclose($fp);
                    }
                }
//  Attachments

                /* change the status */
                //$status = imap_setflag_full($inbox, $overview[0]->msgno, "\Seen \Flagged"); //i will use later
                $status = imap_setflag_full($inbox, $email_number, "\Seen \Flagged"); //i will use later
                //imap_clearflag_full($inbox,$overview[0]->msgno,"//Seen");  //Seen
                
                    $result = array();
                    $result['file'] = $filename; 
                    try{
                    $extrac= explode("On Sun,",$message);
                    $extrac= explode("On Mon,",$extrac[0]);
                    $extrac= explode("On Tue,",$extrac[0]);
                    $extrac= explode("On Wed,",$extrac[0]);
                    $extrac= explode("On Thu,",$extrac[0]);
                    $extrac= explode("On Fri,",$extrac[0]);
                    $extrac= explode("On Sat,",$extrac[0]);
                    $result['body'] = strip_tags($extrac[0]);
                    }catch (Exception $e)
                    {
                        $result['body'] = $message_raw;
                    }
                    $result['body_raw'] = $message_raw;
                    //imap_close($inbox);
                    
                    $file_name = !empty($result['file'])?$result['file']:'na';                         
                    $body = !empty($result['body'])?$result['body']:'na';
                    $body_raw = !empty($result['body_raw'])?$result['body_raw']:'na';                            
                    
                    if($members['company_name']>=11 && $members['company_name']<=13)
                    {
                        $process_index=7;
                    }else{
                        $process_index=4;
                    }
                    
                    //Helpers_Email::change_status_raw($file_name, $body_raw, $body, $members['message_id'], $members['request_id'], $process_index);                   
                   if(!empty($file_id) && $file_name!='na' && $is_file_exist==0)
                   {    
                        Helpers_Upload::insert_file_record($file_name, $members['user_id'], $members['user_request_type_id'], $members['company_name'], $members['requested_value'], $members['request_id'], $members['reason'], $file_id);  
                   }
                       Helpers_Email::change_status_raw($file_name, $body_raw, $body, $members['message_id'], $members['request_id'], $is_file_exist);
                   
                    
                    
                        
        
                    //return $result;
               // }
             /*   } catch (ORM_Validation_Exception $e) {
                    return 1;
                }*/
                  //  exit;
//imap_close($inbox);
//exit;                       
}
else{
    //imap_setflag_full($mbox, "2,5", "\\Seen \\Flagged");
    //imap_clearflag_full($inbox,$overview[0]->msgno,'\\Seen');  //Seen
    imap_clearflag_full($inbox,$email_number,'\\Seen');  //Seen
    //$status = imap_setflag_full($inbox, $email_number, "\Seen \Flagged"); //i will use later
    
    //imap_setflag_full($inbox, $overview[0]->msgno, "\\Seen \\Flagged", ST_UID);
   // echo '<br>  N ' . $overview[0]->msgno;
    //$message = imap_fetchbody($inbox,$overview[0]->msgno,1, FT_PEEK);    
    //imap_fetchbody($inbox,$overview[0]->msgno,1, FT_PEEK);
    //$status = imap_setflag_full($inbox, $overview[0]->msgno, "\\Seen"); //i will use later
} 
            }            
            //echo $output;
        }

        /* close the connection */
        imap_close($inbox);
         return 1;
        
    }
    /*
     *  
     */
    public static function get_email_status()
    {
        
        $email_sender = Helpers_Email::receive_email_backup('', '');
        /*
     $sql = "SELECT request_id, user_id, email_type_name, requested_value, concerned_person_id,
            created_at, status, processing_index, em.message_id, em.message_subject, em.sender_id FROM 
            user_request as t1 
            join email_templates_type as t2                                 
            on t1.user_request_type_id = t2.id                       
            join email_messages as em on em.message_id = t1.message_id   
            and t1.user_request_type_id != 8
            ";                              //Where t1.user_id = {$user_id}  
    
            $members = DB::query(Database::SELECT, $sql)->execute()->as_array();
            
            if(!empty($members))
            foreach ($members as $item) 
            {
                  if(!empty($item['status']) && $item['status']==1 && !empty($item['message_subject']) && !empty($item['sender_id']))
                    {
                                          
                        $email_sender = Helpers_Email::receive_email($item['message_subject'], $item['sender_id']);
                        
                        if($email_sender!=1)
                        {   
                            $file_name = !empty($email_sender['file'])?$email_sender['file']:'na';                         
                            $body = !empty($email_sender['body'])?$email_sender['body']:'na';
                            $body_raw = !empty($email_sender['body_raw'])?$email_sender['body_raw']:'na';                            
                            Helpers_Email::change_status_raw($file_name, $body_raw, $body, $item['message_id'], $item['request_id']);
                        }
                        
                    }   
            }*/
    }
    public static function change_status_raw($file_name, $body_raw, $body, $messge_id, $request_id, $is_file_exist=NULL, $process_index=Null)
    {
        if(empty($process_index))
            $process_index=4;
        
        $DB = Database::instance();
        $date = date('Y-m-d H:i:s');
        DB::update("user_request")->set(array('status' =>2, 'processing_index'=>$process_index))->where('request_id', '=', $request_id)->execute();
        DB::update("email_messages")->set(array('received_date'=>$date, 'received_file_path' =>$file_name, 'received_body_raw'=>$body_raw, 'received_body'=>$body))->where('message_id', '=', $messge_id)->execute();
        
        If($is_file_exist==1 && !empty($file_name) && $file_name!='na')
            DB::update("files")->set(array('file'=>$file_name))->where('request_id', '=', $request_id)->execute();        
        
    }
    public static function change_status($file_name, $body, $messge_id, $request_id)
    {
        $DB = Database::instance();
        $date = date('Y-m-d H:i:s');
        DB::update("user_request")->set(array('status' =>2, 'processing_index'=>4))->where('request_id', '=', $request_id)->execute();
        DB::update("email_messages")->set(array('received_date'=>$date, 'received_file_path' =>$file_name, 'received_body'=>$body))->where('message_id', '=', $messge_id)->execute();
        
    }
    
    //check in blocked number list
    public static function check_in_blocked_number_list($request) {  
       $DB = Database::instance();
        $sql = "SELECT * 
                FROM blocked_numbers 
                WHERE blocked_value = '{$request}' ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
        
    }    
    public static function check_old_familytree_request($cnic) {  
       $DB = Database::instance();
        $sql = "SELECT request_id "
                . "FROM user_request as t1 "
                . "WHERE (t1.user_request_type_id = 10 AND t1.status = 2 AND t1.requested_value = '{$cnic}') ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $request_id = isset($results->request_id) && !empty($results->request_id) ? $results->request_id : 0;        
        return $request_id;
        
    }
    public static function check_old_travelhistory_request($cnic) {  
       $DB = Database::instance();
        $sql = "SELECT count(request_id) as cnt FROM user_request as t1
                WHERE (t1.user_request_type_id = 12 AND t1.requested_value = '{$cnic}') ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $request_count = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;        
        return $request_count;
        
    }
    //get request send permission for sub
    public static function get_sub_request_permission($requesttype,$mnc,$msisdn,$date) {  
       $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype} AND requested_value='{$msisdn}' AND company_name = {$mnc} AND created_at > '{$date}'";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $per = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if($per>0){
            $per=1;
        }
        return $per;        
    }
    //get request send permission for location
    public static function get_location_request_permission($requesttype,$msisdn,$date) {  
       $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype} AND requested_value='{$msisdn}'
                AND created_at > '{$date}' AND (status=1 OR status=0 OR processing_index=4) ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $per = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if($per>0){
            $per=1;
        }
        return $per;
        
    }   
    //get request send permission for international number
    public static function get_cdrint_request_permission($requesttype,$msisdn) {  
       $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype} AND requested_value='{$msisdn}'
                AND (status=1 OR status=0 OR processing_index=4) ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $per = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if($per>0){
            $per=1;
        }
        return $per;      
    }
    //get request send permission for ptcl sub
    public static function get_subptcl_request_permission($requesttype,$msisdn,$date) {  
       $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype} AND requested_value='{$msisdn}'
                AND created_at > '{$date}' AND (status=1 OR status=0 OR processing_index=4) ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $per = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if($per>0){
            $per=1;
        }
        return $per;      
    }    
    //get request send permission for cdr against imei
    public static function get_cdr_against_imei_request_permission($requesttype,$imei,$date) {  
       $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype} AND requested_value='{$imei}'AND created_at > '{$date}' AND (status=1 OR status=0 OR processing_index=4) ";               
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $per = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if($per>0){
            $per=1;
        }
        return $per;
        
    }
    //get request send permission for cdr against imei and cnic
    public static function get_mnc_of_request_in_queue($requesttype,$requested_value, $date) {  
       $DB = Database::instance();
        $sql = "SELECT distinct company_name as mnc
                FROM user_request 
                WHERE user_request_type_id = {$requesttype}
                AND requested_value='{$requested_value}'
                AND ( created_at > '{$date}' AND (status=1 OR status=0 OR processing_index=4))";         
                //or replace with and updated after date 24 may 2022
                /*if(Auth::instance()->get_user()->id ==719)
                {
                    echo $sql; exit;
                }*/   
         $results = DB::query(Database::SELECT, $sql)->execute()->as_array('mnc');
         $results = implode(',', array_keys($results));
         return ($results);        
    }
    //get request send permission for location
    public static function get_sims_against_cnic_request_permission($requesttype,$mnc,$cnic,$date) {  
       $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype} AND requested_value='{$cnic}' AND company_name IN ({$mnc}) AND created_at > '{$date}'";
     
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $per = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if($per>0){
            $per=1;
        }
        return $per;
        
    }
        //get request send permission for verisysrequest
    public static function get_verisys_request_permission($requesttype,$cnic) {  
       $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype} AND requested_value='{$cnic}'
                AND (status=1 OR status=0 OR processing_index=4) ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $per = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if($per>0){
            
            
            $per=1;
        }
        return $per;      
    }    
    
    //check any type of request in queue by request type main requested value
    public static function check_request_in_queue_status($requesttype,$requested_value) {  
       $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype}
                AND requested_value='{$requested_value}'
                AND (status=1 OR status=0 OR processing_index=4) ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $request_count = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if($request_count>0){
            $request_count =1;
        }
        return $request_count;      
    }
    //check branchless banking request in queue
    public static function request_in_queue_branchlessbanking($requested_value) {  
       $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM ctfu_user_request 
                WHERE requested_value = {$requested_value}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $request_count = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if($request_count>0){
            $request_count =1;
        }
        return $request_count;      
    }
    //check any type of request in last days (date) with requested value and type
    public static function check_old_request_with_date($requesttype,$requested_value, $date) {  
       $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype}
                AND requested_value='{$requested_value}'
                AND (status != 4)
                AND (processing_index != 7)
                AND created_at > '{$date}'";  //if request parsing error is resolved with marked complete then it can be requested again.
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $request_count = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if($request_count>0){
            $request_count =1;
        }
        return $request_count;      
    }
    
    //get telco emails
    public static function get_telco_emails() {  
       $DB = Database::instance();
        $sql = "SELECT *
                FROM telco_emails 
                WHERE 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();              
            return $results; 
        
    } 
    
    /* Update email read status table */    
    public static function emailreadstatus($request_id) {
        $DB = Database::instance();
        $sql = "SELECT id FROM email_read_status WHERE request_id = {$request_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        
        if(empty($results->id))
        {
            $query = DB::insert('email_read_status', array('request_id', 'is_read'))
                ->values(array($request_id, 0))
                ->execute();
            
        }else{
            $query = DB::update('email_read_status')->set(array('is_read' => 0))
                ->where('id', '=', $results->id)
                ->execute();
        }            
    }
    
    public static function emailreadstatuscheckUpdate($email_id, $request_id) {
        
        if(substr($request_id, 0, 2 ) === "92" && strlen($request_id)==12)
        {
            $DB = Database::instance();
            $str2 = substr($request_id, 2);
            $sql = "select reference_id from user_request ur 
                    where requested_value = {$str2} and status = 1 and ur.user_request_type_id IN (3,4)";
                    // and request_type in (1,2,3);
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            
            if(!empty($results->reference_id))   
            $request_id = $results->reference_id;
         
        } 
        
        $query = DB::update('email_read_status')->set(array('is_read' => 1, 'gmail_id' => $email_id))
                ->where('request_id', '=', $request_id)
                ->execute();
        
        return $request_id;
    }
    public static function emailreadstatuscheck($email_id) {
        $DB = Database::instance();
        $sql = "SELECT id FROM email_read_status WHERE gmail_id = {$email_id} and is_read = 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        
        if(empty($results->id))
        {
            return ''; 
        }else{
            return $results->id;
        }
    }
    
    public static function receive_single_email($request_id)
    {    
        $is_file_exist ='';
        $DB = Database::instance(); 
        $sql = "SELECT gmail_id FROM email_read_status WHERE request_id = {$request_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        
        $file_id = Helpers_Upload::get_fileid_with_requestid($request_id);
        
        if(!empty($results->gmail_id))
        {
            
            $email_number = $results->gmail_id;
            $filename = '';            
            $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
            include 'gmail/receiving.inc';
            $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());
            $output = '';
            $headerInfo = imap_headerinfo($inbox, $email_number);
            $structure = '';
            $structure = imap_fetchstructure($inbox, $email_number);
            $overview = imap_fetch_overview($inbox, $email_number, 0);
            $message = imap_fetchbody($inbox, $email_number, 1.2);
            if (empty($message))
                 $message = quoted_printable_decode(imap_fetchbody($inbox, $email_number, 1));
            if (empty($message))
                 $message = imap_fetchbody($inbox, $email_number, 2);                                 
                    $sql = "SELECT request_id, user_id, email_type_name, requested_value, concerned_person_id, t1.company_name,
                       created_at, status, processing_index, em.message_id, em.message_subject, em.sender_id FROM 
                       user_request as t1 
                       join email_templates_type as t2                                 
                       on t1.user_request_type_id = t2.id                       
                       join email_messages as em on em.message_id = t1.message_id   
                       and t1.user_request_type_id != 8                       
                       and t1.request_id = {$request_id};
                       ";
                    $members = DB::query(Database::SELECT, $sql)->execute()->current(); //->as_array();
                if (!empty($members)) {
                    
                    $part = !empty($structure->parts[1])?$structure->parts[1]:'';                    
                    if($members['company_name']==13 || $members['company_name']==12 || $members['company_name']==11 || $members['company_name']==4 || $members['company_name']==3 )
                    {    
                        $message_raw = imap_fetchbody($inbox,$email_number,2);
                        if(!empty($part) && $part->encoding == 3) {
                            $message_raw = imap_base64($message_raw);
                        } else if(!empty($part) && $part->encoding == 1) {
                            $message_raw = imap_8bit($message_raw);
                        } else {
                            $message_raw = imap_qprint($message_raw);
                        }
                    }elseif($members['company_name']==6)
                        $message_raw = imap_base64(imap_fetchbody($inbox,$email_number,1));
                    else
                        $message_raw = imap_fetchbody($inbox,$email_number,1);
                    
                    
                    //htmlentities(base64_decode($message))
//                    if ($members['company_name'] == 6)
//                        $message_raw = imap_base64(imap_fetchbody($inbox, $email_number, 1));
//                    else
//                        $message_raw = imap_fetchbody($inbox, $email_number, 1);
                    
                    $output .= 'Subject: ' . $overview[0]->subject . '<br />';
                    $output .= 'Body: ' . $message . '<br />';
                    $output .= 'From: ' . $overview[0]->from . '<br />';
                    $output .= 'Date: ' . $overview[0]->date . '<br />';
                    $attachments = array();
                    if (isset($structure->parts) && count($structure->parts)) {
                        for ($i = 0; $i < count($structure->parts); $i++) {
                            $attachments[$i] = array(
                                'is_attachment' => false,
                                'filename' => '',
                                'name' => '',
                                'attachment' => ''
                            );
                            if ($structure->parts[$i]->ifdparameters) {
                                foreach ($structure->parts[$i]->dparameters as $object) {
                                    if (strtolower($object->attribute) == 'filename') {
                                        $attachments[$i]['is_attachment'] = true;
                                        $attachments[$i]['filename'] = $object->value;
                                    }
                                }
                            }

                            if ($structure->parts[$i]->ifparameters) {
                                foreach ($structure->parts[$i]->parameters as $object) {
                                    if (strtolower($object->attribute) == 'name') {
                                        $attachments[$i]['is_attachment'] = true;
                                        $attachments[$i]['name'] = $object->value;
                                    }
                                }
                            }

                            if ($attachments[$i]['is_attachment']) {
                                $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i + 1);

                                /* 3 = BASE64 encoding */
                                if ($structure->parts[$i]->encoding == 3) {
                                    $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                                }
                                /* 4 = QUOTED-PRINTABLE encoding */ elseif ($structure->parts[$i]->encoding == 4) {
                                    $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                                }
                            }
                        }
                    }
                    $filename = '';

                    foreach ($attachments as $attachment) {
                        if(!empty($file_id))                                
                            {
                                $is_file_exist =1;
                                $file_path = !empty($file_id) ? Helpers_Upload::get_request_data_path($file_id,'save') : '';                                
                            }
                            else
                            {   
                                $is_file_exist =0;
                                $file_id = Helpers_Utilities::id_generator("file_id");    
                                $file_path = !empty($file_id) ? Helpers_Upload::get_request_data_path($file_id,'save') : '';
                            }    
                        if ($attachment['is_attachment'] == 1) {
                            $filename = $attachment['name'];                            
                            $new_file_info=PATHINFO($attachment['filename']);
                            if (!empty($filename)) {
                                $filename = 'rqt'.$members['request_id'] . 'fid' . $file_id . '.' . $new_file_info['extension'];
                            } else {
                                $filename = 'rqt'.$members['request_id'] . 'fid' . $file_id . '.' . $new_file_info['extension'];
                                //$filename = $query_subject_final . '_' . $attachment['filename'];
                            }
                            
                            $fp = fopen($file_path . $filename, "w+");
                            fwrite($fp, $attachment['attachment']);
                            fclose($fp);
                        }
                    }
                    $status = imap_setflag_full($inbox, $email_number, "\Seen \Flagged"); //i will use later
                    $result = array();
                    $result['file'] = $filename;
                    $extrac = explode("On Sun,", $message);
                    $extrac = explode("On Mon,", $extrac[0]);
                    $extrac = explode("On Tue,", $extrac[0]);
                    $extrac = explode("On Wed,", $extrac[0]);
                    $extrac = explode("On Thu,", $extrac[0]);
                    $extrac = explode("On Fri,", $extrac[0]);
                    $extrac = explode("On Sat,", $extrac[0]);
                    $result['body'] = strip_tags($extrac[0]);
                    $result['body_raw'] = $message_raw;
                    $file_name = !empty($result['file']) ? $result['file'] : 'na';
                    $body = !empty($result['body']) ? $result['body'] : 'na';
                    $body_raw = !empty($result['body_raw']) ? $result['body_raw'] : 'na';
                  
                    
                    if(!empty($file_id) && $file_name!='na' && !empty($is_file_exist) && $is_file_exist==0)
                        Helpers_Upload::insert_file_record($file_name, $members['user_id'], $members['user_request_type_id'], $members['company_name'], $members['requested_value'], $members['request_id'], $members['reason'], $file_id);
                    else
                        Helpers_Email::change_status_raw($file_name, $body_raw, $body, $members['message_id'], $members['request_id'], $is_file_exist);
                } 
            imap_close($inbox);
        }
        }
        
        public static function email_status($reference_number, $status, $process_status){
            $query = DB::update('user_request')->set(array('status'=> $status, 'processing_index' => $process_status))
                    ->where('request_id', '=', $reference_number)
                    ->execute();
            return $query;

        }
    }

?>
