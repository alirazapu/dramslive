<?php
exit;

require APPPATH . DIRECTORY_SEPARATOR.'classes/Controller/gmail_api/vendor/autoload.php';
use Google\Client;
use Google\Service\Gmail;


Class Controller_Gmailapi extends Controller
{


    public function action_send_email()
    {
        $send_type = 1;  // 1 without file // 2 with file

// Get the API client and construct the service object.

        $client = new Client();
        $client->setApplicationName('Gmail API PHP Quickstart');
        $client->setScopes(['https://www.googleapis.com/auth/gmail.compose',
            'https://www.googleapis.com/auth/gmail.send',
            'https://www.googleapis.com/auth/gmail.insert',
            'https://www.googleapis.com/auth/gmail.readonly',
            'https://www.googleapis.com/auth/documents']);
        $client->setAuthConfig(APPPATH . DIRECTORY_SEPARATOR.'classes/Controller/gmail_api/credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

// Load previously authorized token from a file, if it exists.
// The file token.json stores the user's access and refresh tokens, and is
// created automatically when the authorization flow completes for the first
// time.
        $tokenPath = APPPATH . DIRECTORY_SEPARATOR .'classes/Controller/gmail_api/token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        $service = new Gmail($client);

        try {

// Print the labels in the user's account.
            /*
            $user = 'me';
            $results = $service->users_labels->listUsersLabels($user);

            if (count($results->getLabels()) == 0) {
                print "No labels found.\n";
            } else {
                print "Labels:\n";
                foreach ($results->getLabels() as $label) {
                    printf("- %s\n", $label->getName());
                }
            }  */
            //$message = new Google_Service_Gmail_Message();
            //to create new email
            $create_email=  self::createMessage('test@gmail.com', 'test@gmail.com', "separate action method", 'Google_Service_Gmail_Message',$send_type);
           // $create_email = createMessage('test@gmail.com', 'test@gmail.com', "love abcd kg", 'Google_Service_Gmail_Message');
            //to send msg/email
        $message = $service->users_messages->send('me', $create_email);
        } catch (Exception $e) {

// TODO(developer) - handle error appropriately
            echo 'Message: ' . $e->getMessage();
        }
    }

    public function action_read_email()
    {
        $receive_type = 2; // 1 without file, 2 with file
        $email_type = 2; // 1 with timestamp, 2 without time
        $email_status = 2;  // 1 unread, 2 all

        $client = new Client();
        $client->setApplicationName('Gmail API PHP Quickstart');
        $client->setScopes(['https://www.googleapis.com/auth/gmail.compose',
            'https://www.googleapis.com/auth/gmail.send',
            'https://www.googleapis.com/auth/gmail.insert',
            'https://www.googleapis.com/auth/gmail.readonly',
            'https://www.googleapis.com/auth/documents']);
        $client->setAuthConfig(APPPATH . DIRECTORY_SEPARATOR.'classes/Controller/gmail_api/credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $tokenPath = APPPATH . DIRECTORY_SEPARATOR .'classes/Controller/gmail_api/token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        $service = new Gmail($client);
        try{
            //to display inbox emails(nth/all)
            $show_emails = self::allInboxMessage($service, 15, 'INBOX');
            //to get first five  new emails
            $latest_mails =self::newemails($service, 'me', 5);
            //to get with in time limit new emails
            $time_limit_latest_mails =self::newEmailsWithTimeLimit($service, 'me', ' -15 minutes', 100);

            if($email_type==1) {
                $list = $time_limit_latest_mails->getMessages();
            }else {
                if ($email_status == 1) {
                    $list = $latest_mails->getMessages();
                } else {
                    $list = $show_emails->getMessages();
                }
            }
            foreach ($list as $mails) {
                $messageId = $mails->getId(); // Grab first Message
                $optParamsGet = [];
                $optParamsGet['format'] = 'full'; // Display message in payload
                //$message = $service->users_messages->get('me',$messageId,$optParamsGet);
                $message = $service->users_messages->get('me', $messageId);
                $messagePayload = $message->getPayload();
                $headers = $message->getPayload()->getHeaders();
                $subject = array_values(array_filter($headers, function ($k) {
                    return $k['name'] == 'Subject';
                }));

                $sub = ($subject[0]->getValue());
                print_r('subject : ');
                print_r($sub);
                print_r('<br>');


                $parts = $message->getPayload()->getParts();
                $body = $messagePayload->getBody();

                $FOUND_BODY = json_decode($body['data']);
                // If we didn't find a body, let's look for the parts
                if (!$FOUND_BODY) {
                    $parts = $messagePayload->getParts();
//        echo '<pre>';
//        print_r($parts[1]['filename']);
//        print_r($parts[1]['body']['attachmentId']);
//        exit;
                    if (count($parts) > 0) {
                        //$data = $msg->getPayload()->getParts()[0]->getBody()->getData();
                        $data = $parts[0]->getBody()->getData();
                    } else {
                        $data = $message->getPayload()->getBody()->getData();
                    }

                    $out = str_replace("-", "+", $data);
                    $out = str_replace("_", "/", $out);
                    $body = base64_decode($out);

                    print_r('body : ');
                    print_r($body);
                    print_r('<br> ');
//        exit;
                    /*   foreach ($parts  as $part) {
                           if($part['body']) {
                               $FOUND_BODY = json_decode($part['body']->data);
                               break;
                           }
                           // Last try: if we didn't find the body in the first parts,
                           // let's loop into the parts of the parts (as @Tholle suggested).
                           if($part['parts'] && !$FOUND_BODY) {
                               foreach ($part['parts'] as $p) {
                                   // replace 'text/html' by 'text/plain' if you prefer
                                   if($p['mimeType'] === 'text/html' && $p['body']) {
                                       $FOUND_BODY = json_decode($p['body']->data);
                                       break;
                                   }
                               }
                           }
                           if($FOUND_BODY) {
                               break;
                           }
                       }*/
                }


//show/download attachement from recieved emails
            if ($receive_type == 2) {
                $attachments = [];
//    if (!empty($parts[1]->body->attachmentId)) {
                if (!empty($parts[1]['body']['attachmentId'])) {

                    $attachment = $service->users_messages_attachments->get('me', $messageId, $parts[1]->body->attachmentId);
                    $attachments[] = [
                        'filename' => $parts[1]->filename,
                        'mimeType' => $parts[1]->mimeType,
                        'data' => strtr($attachment->data, '-_', '+/')
                    ];
                } else if (!empty($parts->parts)) {
                    $attachments = array_merge($attachments, $this->getAttachments($messageId, $parts->parts));
                }

                if (!empty($attachments)) {
                    $data = $attachment->getData();
                    $data = strtr($data, array('-' => '+', '_' => '/'));
                    $myfile = fopen("/var/www/html/aies/uploads/gmailapi_files/" . $attachments[0]['filename'], "w+");
                    fwrite($myfile, base64_decode($data));
                    fclose($myfile);
                    print_r('attachments: ');
                    foreach ($attachments as $key => $value) {
//                        echo '<pre>';
//                        print_r($messageId);
//                        print_r('<br>');
//                        print_r($value);
//                        print_r('<br>');
//                        print_r($value['filename']);
//                        exit;
                        echo '<a target="_blank" href="attachment.php?messageId=' . $messageId . '&part_id=' . $value . '"> ' . $value['filename'] . '</a><br/>';
                    }
                }
            }

//    $files[]='';
//    $messageDetails = $message->getPayload();
//    foreach ($messageDetails['parts'] as $key => $value) {
//        if (!isset($value['body']['data'])) {
//           $ft= array_push($files, $value['partId']);
//        }
//    }

//        $partId = $messageDetails['parts'][1]['partId'];
//        function getAttachment($messageId, $partId)
//        {
//            try {
//                $files = [];
//                $gmail = new Google_Service_Gmail($this->authenticate->getClient());
//                $attachmentDetails = $this->getAttachmentDetailsFromMessage($messageId, $partId);
//                $attachment = $gmail->users_messages_attachments->get($this->authenticate->getUserId(), $messageId, $attachmentDetails['attachmentId']);
//                if (!$attachmentDetails['status']) {
//                    return $attachmentDetails;
//                }
//                $attachmentDetails['data'] = $this->base64UrlDecode($attachment->data);
//                return ['status' => true, 'data' => $attachmentDetails];
//            } catch (\Google_Service_Exception $e) {
//                return ['status' => false, 'message' => $e->getMessage()];
//            }
//        }
//
//        $fcall = getAttachment($messageId, $partId);


// Finally, print the message ID and the body
//    print_r($messageId . " : " . $FOUND_BODY);
//print_r($FOUND_BODY); exit;
// $body = $parts[0]['body'];
//    $rawData = $body->data;
//    $sanitizedData = strtr($rawData,'-_', '+/');
//    $decodedMessage = base64_decode($sanitizedData);

//    var_dump($decodedMessage);


//print_r($test);
            }
        } catch (Exception $e) {

// TODO(developer) - handle error appropriately
            echo 'Message: ' . $e->getMessage();
        }
    }

    function createMessage($sender, $to, $subject, $messageText, $send_type) {

        $message = new Google_Service_Gmail_Message();

//without file send email
        if($send_type==1) {
            $rawMessageString = "From: <{$sender}>\r\n";
            $rawMessageString .= "To: <{$to}>\r\n";
            $rawMessageString .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "?=\r\n";
            $rawMessageString .= "MIME-Version: 1.0\r\n";
            $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n";
            $rawMessageString .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
            $rawMessageString .= "{$messageText}\r\n";

            $rawMessage = strtr(base64_encode($rawMessageString), array('+' => '-', '/' => '_'));
            $message->setRaw($rawMessage);
            return $message;
        }
//with file send email
        if($send_type==2) {
            $strRawMessage = "";
            $boundary = uniqid(rand(), true);
            $subjectCharset = $charset = 'utf-8';
            $strToMailName = 'NAME';

            $strSesFromName = 'GMAIL API';


            $strRawMessage .= 'To: ' . $strToMailName . " <" . $to . ">" . "\r\n";
            $strRawMessage .= 'From: ' . $strSesFromName . " <" . $sender . ">" . "\r\n";

            $strRawMessage .= 'Subject: =?' . $subjectCharset . '?B?' . base64_encode($subject) . "?=\r\n";
            $strRawMessage .= 'MIME-Version: 1.0' . "\r\n";
            $strRawMessage .= 'Content-type: Multipart/Mixed; boundary="' . $boundary . '"' . "\r\n";


            $filePath = '/root/Downloads/Accused File.xlsx';
            $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
            $mimeType = finfo_file($finfo, $filePath);
            $fileName = 'Accused File.xlsx';

            $fileData = base64_encode(file_get_contents($filePath));

            $strRawMessage .= "\r\n--{$boundary}\r\n";
            $strRawMessage .= 'Content-Type: ' . $mimeType . '; name="' . $fileName . '";' . "\r\n";
            $strRawMessage .= 'Content-ID: <' . $sender . '>' . "\r\n";
            $strRawMessage .= 'Content-Description: ' . $fileName . ';' . "\r\n";
            $strRawMessage .= 'Content-Disposition: attachment; filename="' . $fileName . '"; size=' . filesize($filePath) . ';' . "\r\n";
            $strRawMessage .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
            $strRawMessage .= chunk_split($fileData, 76, "\n") . "\r\n";
            $strRawMessage .= "--{$boundary}\r\n";
            $strRawMessage .= 'Content-Type: text/html; charset=' . $charset . "\r\n";
            $strRawMessage .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
            $strRawMessage .= $messageText . "\r\n";

            $trawMessage = strtr(base64_encode($strRawMessage), array('+' => '-', '/' => '_'));
            $message->setRaw($trawMessage);
            return $message;
        }


    }

//to display inbox emails(nth)
    function allInboxMessage($service,$noOfMsg,$lable){
        $optParams = [];
        $optParams['maxResults'] = $noOfMsg; // Return Only 5 Messages
        $optParams['labelIds'] = $lable; // Only show messages in Inbox
        $messages = $service->users_messages->listUsersMessages('me',$optParams);
        return $messages;

    }

//to get first five/nth  new emails
    function newemails($service, $userid, $noOfMsg){
        $messages= $service->users_messages->listUsersMessages($userid,['maxResults' => $noOfMsg, 'q' => "in:inbox is:unread"]);
        return $messages;
    }

//to get with in time limit new emails
    function newEmailsWithTimeLimit($service, $userid,$timerange, $noOfsg){
        date_default_timezone_set('Asia/Karachi');

        $limit= strtotime(date('Y-m-d H:i:s', strtotime($timerange)));
        $messages= $service->users_messages->listUsersMessages($userid,['maxResults' => $noOfsg,'q' => "in:inbox after:$limit"]);
        return $messages;
    }
}


    