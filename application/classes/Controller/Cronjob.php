<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Controller_Cronjob extends Controller {    
    /* test function */
	public function action_testufone(){
		$reference_number = '996438';
		$reference_idd = '1946024';
		$body='IMEI|Both|11/04/2025|01/29/2026|353580080921376';
		$body= substr_replace( $body, 0, strlen($body)-1);   
		$file_name = PROJECT_ROOT .  'drams' . DS . 'dramsfiles' . DS . 'ufone_tem_files' . DS.$reference_idd . ".txt";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		fwrite($myfile, $body);
		fclose($myfile);                           
		$body = $reference_idd;
		$to='ali.razapu@gmail.com';
		$to_name='Ali Raza';
		$subject='FIR 996438';
		$mail = new PHPMailer(); // create a new object
		$attachment=$file_name;
		$new_file_name='';
		if (!empty($attachment)) {
			if (!empty(strip_tags($body))) {
				$new_file_name = strip_tags($body) . '.txt';
			} else {
				$new_file_name = 'request.txt';
			}
			$body = '<p></p>';
		}			
		echo ("[" . date('c') . "] send_email: Attempting SMTP connection for $to");
		
		$mail->IsSMTP(); // enable SMTP  //for live server open and local server close
		//$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only detail for 4
		$mail->SMTPAuth = true; // authentication enabled
		$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465; // or 587
		$mail->IsHTML(true);
		//$mail->CharSet = "text/html; charset=UTF-8;"; //change for telnor
		$result = Helpers_Inneruse::get_gmail_pw();
		$mail->Username ='kpkctd@gmail.com';
		$mail->Password ='wjlrthkqsmansnqe';
		$mail->FromName = 'CTD KPK';
		$mail->setFrom($mail->Username, $mail->FromName);
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AddAddress($to, $to_name);
		if (!empty($attachment)) {
			//$mail->addAttachment($attachment,'application/octet-stream');
			$mail->addStringAttachment(file_get_contents($attachment), $new_file_name);         // Add attachments
		}
		if (!$mail->Send()) {		
			echo ("[" . date('c') . "] send_email FAILED for $to: " . $mail->ErrorInfo);
		} else {
			echo ("[" . date('c') . "] send_email SUCCESS for $to");
		}
		//if exist then delete
		if (file_exists($file_name)) {
			unlink($file_name);
		}                    
	}
    public function action_testimap(){

        /*
           $result = Helpers_Inneruse::get_gmail_pw();
            $smtp_user = $result['send']['user'];
            $smtp_pass = $result['send']['password'];
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 465;
            $mail->Username = $smtp_user;
            $mail->Password = $smtp_pass;

                // Test connection by sending a test email to self
            $mail->setFrom($smtp_user, 'SMTP Test');
            $mail->addAddress('ali.razapu@gmail.com');
            $mail->Subject = 'SMTP Connection Test - ' . date('Y-m-d H:i:s');
            $mail->Body = 'This is a test email to verify SMTP connection is working.';
            $mail->IsHTML(true);

            if ($mail->Send()) {
                    echo "✓ SMTP Connection: SUCCESS<br>";
                    echo "✓ Test email sent successfully<br>";
            } else {
                echo "✗ SMTP Connection: FAILED\n";
                echo "Error: " . $mail->ErrorInfo . "\n";

            }
            echo "<br>";
        */
        // ────────────────────────────────────────────────
        // Test IMAP Connection (Receive)
        // ────────────────────────────────────────────────
             echo "── IMAP Connection Test (Receive) ──"; echo "<br>";

            $result = Helpers_Inneruse::get_gmail_pw();
            $imap_user = $result['receive']['user'];
            $imap_pass = $result['receive']['password'];

            //$imap_pass = 'bfcihehizxazlphk';//$result['receive']['password'];
            echo "IMAP Username: " . $imap_user ;echo "<br>";
            echo "IMAP PAssword: " . $imap_pass ; echo "<br>";
            echo "IMAP Host: imap.gmail.com"; echo "<br>";
            echo "IMAP Port: 993 (SSL)";

            // Try to connect to IMAP
            $hostname = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
            $inbox = imap_open($hostname, $imap_user, $imap_pass);

            if ($inbox) {
                echo "✓ IMAP Connection: SUCCESS\n";
                // Get mailbox info
                $check = imap_mailboxmsginfo($inbox);
                echo "✓ Mailbox Messages: " . $check->Nmsgs ; echo "<br>";
                echo "✓ Unread Messages: " . $check->Unread ; echo "<br>";
                imap_close($inbox);
            } else {
                echo "✗ IMAP Connection: FAILED\n";
                echo "Error: " . imap_last_error() ; echo "<br>";
            }


            echo "── IMAP Connection Test (Send) ──"; echo "<br>";echo "<br>";

            $result = Helpers_Inneruse::get_gmail_pw();
            $imap_user = $result['send']['user'];
            $imap_pass = $result['send']['password'];

            //$imap_pass = 'bfcihehizxazlphk';//$result['receive']['password'];
            echo "IMAP Username: " . $imap_user ;echo "<br>";
            echo "IMAP PAssword: " . $imap_pass ; echo "<br>";
            echo "IMAP Host: imap.gmail.com"; echo "<br>";
            echo "IMAP Port: 993 (SSL)";
            // Try to connect to IMAP
            $hostname = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
            $inbox = imap_open($hostname, $imap_user, $imap_pass);

            if ($inbox) {
                echo "✓ IMAP Connection: SUCCESS\n";
                // Get mailbox info
                $check = imap_mailboxmsginfo($inbox);
                echo "✓ Mailbox Messages: " . $check->Nmsgs ; echo "<br>";
                echo "✓ Unread Messages: " . $check->Unread ; echo "<br>";
                imap_close($inbox);
            } else {
                echo "✗ IMAP Connection: FAILED\n";
                echo "Error: " . imap_last_error() ; echo "<br>";
            }
        echo "<br>";
        echo "── IMAP Connection Test ADM- reading ──"; echo "<br>";echo "<br>";

            $imap_user = 'kpkctd@gmail.com';
            $imap_pass = 'wjlrthkqsmansnqe';

            //$imap_pass = 'bfcihehizxazlphk';//$result['receive']['password'];
            echo "IMAP Username: " . $imap_user ;echo "<br>";
            echo "IMAP PAssword: " . $imap_pass ; echo "<br>";
            echo "IMAP Host: imap.gmail.com"; echo "<br>";
            echo "IMAP Port: 993 (SSL)";
            // Try to connect to IMAP
            $hostname = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
            $inbox = imap_open($hostname, $imap_user, $imap_pass);

            if ($inbox) {
                echo "✓ IMAP Connection: SUCCESS\n";
                // Get mailbox info
                $check = imap_mailboxmsginfo($inbox);
                echo "✓ Mailbox Messages: " . $check->Nmsgs ; echo "<br>";
                echo "✓ Unread Messages: " . $check->Unread ; echo "<br>";
                imap_close($inbox);
            } else {
                echo "✗ IMAP Connection: FAILED\n";
                echo "Error: " . imap_last_error() ; echo "<br>";
            }
    }
    public function action_testunzipall()
    {
        echo "<pre>";
        echo "========================================\n";
        echo "ZIP & RAR UNZIP FUNCTION TEST\n";
        echo "========================================\n\n";

        // Test file paths
        $zip_file = DOCROOT . 'dramsfiles/requests-data/955001-960000/923009354085.zip';
        $rar_file = DOCROOT . 'dramsfiles/requests-data/955001-960000/rqt15fid959174.rar';
        $request_id = 999999; // dummy

        // 1. Check PHP extensions
        $zip_ext = extension_loaded('zip');
        $rar_ext = extension_loaded('rar');
        $unrar_cli = (shell_exec('where unrar') || shell_exec('which unrar')) ? true : false;

        echo "PHP extension 'zip': " . ($zip_ext ? "✓ Loaded" : "✗ NOT loaded") . "\n";
        echo "PHP extension 'rar': " . ($rar_ext ? "✓ Loaded" : "✗ NOT loaded") . "\n";
        echo "unrar CLI available: " . ($unrar_cli ? "✓ Yes" : "✗ No") . "\n\n";

        // 2. Check file existence
        echo "ZIP file: $zip_file\n";
        echo "RAR file: $rar_file\n";
        echo "ZIP file exists: " . (file_exists($zip_file) ? "✓ Yes" : "✗ No") . "\n";
        echo "RAR file exists: " . (file_exists($rar_file) ? "✓ Yes" : "✗ No") . "\n\n";

        // 3. Check file permissions
        echo "ZIP file readable: " . (is_readable($zip_file) ? "✓ Yes" : "✗ No") . "\n";
        echo "RAR file readable: " . (is_readable($rar_file) ? "✓ Yes" : "✗ No") . "\n\n";

        // 4. Test ZIP extraction
        echo "── Testing ZIP extraction ──\n";
        if ($zip_ext && file_exists($zip_file) && is_readable($zip_file)) {
            try {
                $result = Helpers_Upload::unzip_file($zip_file, $request_id);
                if ($result) {
                    echo "✓ unzip_file() SUCCESS: Extracted file: $result\n";
                } else {
                    echo "✗ unzip_file() returned empty result.\n";
                }
            } catch (Exception $e) {
                echo "✗ unzip_file() EXCEPTION: " . $e->getMessage() . "\n";
            }
        } else {
            echo "✗ Cannot test ZIP extraction (missing extension or file).\n";
        }
        echo "\n";

        // 5. Test RAR extraction
        echo "── Testing RAR extraction ──\n";
        if (($rar_ext || $unrar_cli) && file_exists($rar_file) && is_readable($rar_file)) {
            try {
                $result = Helpers_Upload::unziprar_file($rar_file, $request_id);
                if ($result) {
                    echo "✓ unziprar_file() SUCCESS: Extracted file: $result\n";
                } else {
                    echo "✗ unziprar_file() returned empty result.\n";
                }
            } catch (Exception $e) {
                echo "✗ unziprar_file() EXCEPTION: " . $e->getMessage() . "\n";
            }
        } else {
            echo "✗ Cannot test RAR extraction (missing extension/CLI or file).\n";
        }
        echo "\n";

        // 6. Print summary of missing requirements
        echo "========================================\n";
        echo "SUMMARY OF MISSING REQUIREMENTS:\n";
        if (!$zip_ext) echo "- PHP 'zip' extension is missing.\n";
        if (!$rar_ext && !$unrar_cli) echo "- Neither PHP 'rar' extension nor 'unrar' CLI is available.\n";
        if (!file_exists($zip_file)) echo "- ZIP test file is missing.\n";
        if (!file_exists($rar_file)) echo "- RAR test file is missing.\n";
        if (file_exists($zip_file) && !is_readable($zip_file)) echo "- ZIP file is not readable by PHP.\n";
        if (file_exists($rar_file) && !is_readable($rar_file)) echo "- RAR file is not readable by PHP.\n";
        if ($zip_ext && $rar_ext) echo "- All required PHP extensions are present.\n";
        if ($zip_ext && ($rar_ext || $unrar_cli) && file_exists($zip_file) && file_exists($rar_file)) echo "- All requirements for extraction are present.\n";
        echo "========================================\n";
        exit;
    }
    public function action_testAdminEmail() {

        $email_config = Helpers_CompanyEmail::get_email(1);
        echo     $email_config['email'] ?? '';echo "<br/>";
        echo     $email_config['name'] ?? '';echo "<br/>";


        $email_config = Helpers_CompanyEmail::get_email(3,6);
        echo     $email_config['email'] ?? '';echo "<br/>";
        echo     $email_config['name'] ?? '';echo "<br/>";
        $email_config = Helpers_CompanyEmail::get_email(3,1);
        echo     $email_config['email'] ?? '';echo "<br/>";
        echo     $email_config['name'] ?? '';echo "<br/>";
        $email_config = Helpers_CompanyEmail::get_email(3,2);
        echo     $email_config['email'] ?? '';echo "<br/>";
        echo     $email_config['name'] ?? '';echo "<br/>";

        $email_config = Helpers_CompanyEmail::get_email(4);
        echo     $email_config['email'] ?? '';echo "<br/>";
        echo     $email_config['name'] ?? '';echo "<br/>";

        $email_config = Helpers_CompanyEmail::get_email(6,1);
        echo     $email_config['email'] ?? '';echo "<br/>";
        echo     $email_config['name'] ?? '';echo "<br/>";
        $email_config = Helpers_CompanyEmail::get_email(6,2);
        echo     $email_config['email'] ?? '';echo "<br/>";
        echo     $email_config['name'] ?? '';echo "<br/>";
        $email_config = Helpers_CompanyEmail::get_email(7);
        echo     $email_config['email'] ?? '';echo "<br/>";
        echo     $email_config['name'] ?? '';echo "<br/>";
        $email_config = Helpers_CompanyEmail::get_email(8);
        echo     $email_config['email'] ?? '';echo "<br/>";
        echo     $email_config['name'] ?? '';echo "<br/>";
        $email_config = Helpers_CompanyEmail::get_email(11);
        echo     $email_config['email'] ?? '';echo "<br/>";
        echo     $email_config['name'] ?? '';echo "<br/>";
        $email_config = Helpers_CompanyEmail::get_email(12);
        echo     $email_config['email'] ?? '';echo "<br/>";
        echo     $email_config['name'] ?? '';echo "<br/>";
        $email_config = Helpers_CompanyEmail::get_email(13);
        echo     $email_config['email'] ?? '';echo "<br/>";
        echo     $email_config['name'] ?? '';echo "<br/>";
    }
    public function action_test() {


        // ────────────────────────────────────────────────
        // Test SMTP Connection (Send)
        // ────────────────────────────────────────────────
        echo "── SMTP Connection Test (Send) ──\n";

            $result = Helpers_Inneruse::get_gmail_pw();
            $smtp_user = $result['send']['user'];
            $smtp_pass = $result['send']['password'];
            
            echo "SMTP Username: " . $smtp_user . "\n";
            echo "SMTP Host: smtp.gmail.com\n";
            echo "SMTP Port: 465 (SSL)\n";
            // Try to connect to SMTP
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 465;
            $mail->Username = $smtp_user;
            echo "── IMAP Connection Test (Receive) ──\n";
            $result = Helpers_Inneruse::get_gmail_pw();
            $imap_user = $result['receive']['user'];
            $imap_pass = $result['receive']['password'];
            //$imap_pass = 'bfcihehizxazlphk';//$result['receive']['password'];
            echo "IMAP Username: " . $imap_user . "\n";
            echo "IMAP PAssword: " . $imap_pass . "\n";
            echo "IMAP Host: imap.gmail.com\n";
            echo "IMAP Port: 993 (SSL)\n";



        exit;
    }    
    public function action_email_send_ufone() {
        try {
            /*  High prority  for location */
            include 'cron_job' . DS . 'send_other' . DS . 'low_ufone.inc';
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'action_email_send_ufone',
                $e->getMessage(),
                array('exception' => get_class($e)),
                $e->getTraceAsString(),
                'processing_failure',
                'email_send_ufone'
            );
            error_log("[" . date('c') . "] action_email_send_ufone failed: " . $e->getMessage());
        }
    }
    public function action_email_send_nadira() {
        try {
            /*  High prority  for location */
            include 'cron_job' . DS . 'send_nadira' . DS . 'heigh.inc';
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'action_email_send_nadira',
                $e->getMessage(),
                array('exception' => get_class($e)),
                $e->getTraceAsString(),
                'processing_failure',
                'email_send_nadira'
            );
            error_log("[" . date('c') . "] action_email_send_nadira failed: " . $e->getMessage());
        }
    }
    /* ptcl */
    public function action_email_send_ptcl() {
        try {
            /*  High prority  for location */
            include 'cron_job' . DS . 'send_ptcl' . DS . 'heigh.inc';
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'action_email_send_ptcl',
                $e->getMessage(),
                array('exception' => get_class($e)),
                $e->getTraceAsString(),
                'processing_failure',
                'email_send_ptcl'
            );
            error_log("[" . date('c') . "] action_email_send_ptcl failed: " . $e->getMessage());
        }
    }
    /* Current Location */
    public function action_email_send_loc() {
        try {
            /* Telco Report */
            include 'cron_job' . DS . 'send_other' . DS . 'telco_rep.inc';
            /*  High prority  for location */
            include 'cron_job' . DS . 'send_location' . DS . 'heigh.inc';
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'action_email_send_loc',
                $e->getMessage(),
                array('exception' => get_class($e)),
                $e->getTraceAsString(),
                'processing_failure',
                'email_send_loc'
            );
            error_log("[" . date('c') . "] action_email_send_loc failed: " . $e->getMessage());
        }
    }

    public function action_email_send() {
        try {
            $data = Model_Generic::resend_error_in_queue();
            /* Telco Report */
            include 'cron_job' . DS . 'send_other' . DS . 'telco_rep.inc';
            /*  High prority */
            include 'cron_job' . DS . 'send_other' . DS . 'heigh.inc';
            /*  Medium prority */
            include 'cron_job' . DS . 'send_other' . DS . 'medium.inc';
            /*  Low prority */
            include 'cron_job' . DS . 'send_other' . DS . 'low.inc';
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'action_email_send',
                $e->getMessage(),
                array('exception' => get_class($e)),
                $e->getTraceAsString(),
                'processing_failure',
                'email_send'
            );
            error_log("[" . date('c') . "] action_email_send failed: " . $e->getMessage());
        }
    }

    /* email receive */
    public function action_email_receive() {
        try {
            Helpers_Email::get_email_status();
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'action_email_receive',
                $e->getMessage(),
                array('exception' => get_class($e)),
                $e->getTraceAsString(),
                'processing_failure',
                'email_receive'
            );
            error_log("[" . date('c') . "] action_email_receive failed: " . $e->getMessage());
        }
    }

    /* email receive */
    public function action_email_receive2()
    {
        /*$lockFile = DOCROOT . 'application/logs/email_receive2.lock';

        // Cleanup stale lock (older than 1 hour)
        if (file_exists($lockFile) && (time() - filemtime($lockFile)) > 3600) {
            @unlink($lockFile);
            error_log("[" . date('c') . "] Removed stale lock file: $lockFile");
        }

        $lock = @fopen($lockFile, 'w');
        if (!$lock) {
            error_log("[" . date('c') . "] Cannot create lock file: $lockFile");
            return;
        }

        if (!flock($lock, LOCK_EX | LOCK_NB)) {
            error_log("[" . date('c') . "] email_receive2 already running - skipping");
            fclose($lock);
            return;
        }*/

        try {
            $result = Helpers_Email::receive_email('', 2);
            error_log("[" . date('c') . "] email_receive2 completed - processed: $result");
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'action_email_receive2',
                $e->getMessage(),  array('exception' => get_class($e)) ,
                $e->getTraceAsString(),
                'processing_failure',
                'email_receive2'
            );
            error_log("[" . date('c') . "] email_receive2 failed: " . $e->getMessage());
        }
    }

    public function action_email_parse_sub() {
        //echo 'Current Location';
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
               and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and ur.user_request_type_id = 3				
                ORDER BY ur.request_id  ASC
            ";                              //Where t1.user_id = {$user_id}
        // and ur.request_id=459688
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        $login_user = Auth::instance()->get_user();
        if(!empty($login_user->id) && $login_user->id==11012){
            $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and ur.user_request_type_id = 3
				AND company_name=4
                ORDER BY ur.request_id  DESC
            ";                              //Where t1.user_id = {$user_id}
// and ur.request_id = 1140862
            $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        }
        foreach ($parse_data as $data) {
            try {
                $mobile_number = '';
                $name = '';
                $cnic = '';
                $cnic_original = '';
                $is_foreigner = 0;
                $address = '';
                $active = '';
                $date = '';
                $status = '';
                $not_fount = 0;
                $not_found_for_telenor = 0;
                $reference_number = $data['request_id'];
                $data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $login_user = Auth::instance()->get_user();
                echo 'Company: '.$data['company_name'].'<br>';
                echo 'Request ID: '.$data['request_id'].'<br><br>';
                switch ($data['company_name']) {
                    case 1: // mobilink
                    case 7: // mobilink
                        include 'cron_job' . DS . 'parse_sub' . DS . 'mobilink.inc';
                        $company    = 'mobilink';
                        break;
                    case 3: // Ufone
                        include 'cron_job' . DS . 'parse_sub' . DS . 'ufone.inc';
                        $company    = 'ufone';
                        break;
                    case 6: // Telenor
                        include 'cron_job' . DS . 'parse_sub' . DS . 'telenor.inc';
                        $company    = 'telenor';
                        break;
                    case 4: // Zong
                        include 'cron_job' . DS . 'parse_sub' . DS . 'zong.inc';
                        $company    = 'zong';
                        break;
                }

                //echo $data['company_name'];exit;
               // echo $not_fount.'-'. $not_found_for_telenor;
               // exit;
                // if request is of telenor and not found is 1 (set in telenor sub) then else old logic for all other request
                if($data['company_name'] == 6 && $not_found_for_telenor == 1){
                        $reference_number_1 = Model_Email::email_status($reference_number, 2, 8);
                }else{
                        if ($not_fount == 0) {
					/* ================= Normalize Mobile ================= */
                    $mobile_number = trim($mobile_number);
                    $mobile_number = preg_replace('/\D/', '', $mobile_number);

                    // Normalize Pakistan MSISDN formats
                    if (strlen($mobile_number) === 13 && substr($mobile_number, 0, 4) === '0092') {
                        $mobile_number = substr($mobile_number, 4);
                    } elseif (strlen($mobile_number) === 12 && substr($mobile_number, 0, 2) === '92') {
                        $mobile_number = substr($mobile_number, 2);
                    } elseif (strlen($mobile_number) === 11 && $mobile_number[0] === '0') {
                        $mobile_number = substr($mobile_number, 1);
                    }

                    /* ================= Normalize CNIC ================= */
                    $cnic_original = trim($cnic);
                    $cnic_clean_digits = preg_replace('/\D/', '', $cnic_original);

                    /*
                     * Foreign CNIC detection
                     * Example: CP10854990351
                     * Rule: NOT all digits AND length = 13
                     */
                    if (!ctype_digit($cnic_original) && strlen($cnic_original) == 13) {
                        $is_foreigner = 1;
                        $cnic_number = null;
                        $cnic_number_foreigner = $cnic_original;
                    } else {
                        $is_foreigner = 0;
                        $cnic_number = $cnic_clean_digits;
                        $cnic_number_foreigner = null;
                    }

                    $name = trim($name);
                    $address = trim($address);
                    $active = trim($active);

                    // Defensive trailer strip: if a per-company parser missed cleaning
                    // the address (e.g. when telco response glues DOB + email metadata
                    // + sender MSISDN + FIR id onto the end of the address with no
                    // separator), chop everything from the first datetime marker on.
                    // This is a no-op for clean addresses.
                    if($data['company_name']==1 || $data['company_name']==7) {
                        $address = trim(preg_replace(
                            '/\s*(?:\d{4}-\d{1,2}-\d{1,2}[Tt]|\d{1,2}\/\d{1,2}\/\d{4}\s+\d{1,2}:\d{2}).*$/',
                            '',
                            $address
                        ));
                    }

                    /* ================= Validation ================= */

                    // Mobile must always be valid
                    $isValidMobile = (
                        strlen($mobile_number) === 10 &&
                        ctype_digit($mobile_number) &&
                        preg_match('/^[3]\d{9}$/', $mobile_number)
                    );

                    // CNIC validation depends on nationality
                    if ((int)$is_foreigner === 0) {
                        $isValidCnic = (
                            strlen($cnic_number) === 13 &&
                            ctype_digit($cnic_number)
                        );
                    } else {
                        // Foreigner: allow alphanumeric 13 chars, or at least non-empty
                        $isValidCnic = (
                            !empty($cnic_number_foreigner) &&
                            strlen($cnic_number_foreigner) === 13 &&
                            preg_match('/^[A-Za-z0-9]{13}$/', $cnic_number_foreigner)
                        );
                    }

                    /* ================= Final Gate ================= */
                    if ($isValidMobile && $isValidCnic) {

                        /* -------- Name Handling -------- */
                        $nameParts = array_values(array_filter(explode(' ', strip_tags($name))));

                        /* -------- Status Handling -------- */
                        $active = (empty($active) || $active === 'Active') ? 1 : 0;
                        $status = (!empty($status) && $status === 'Postpaid') ? '0' : '1';

                        /* -------- Date Handling -------- */
                        $date = !empty($date) ? date("Y-m-d H:i:s", strtotime($date)) : '';

                        /* -------- Build Insert Data -------- */
                        $sub_data = [];
                        $sub_data['act_date'] = $date;
                        $sub_data['mobile_number'] = $mobile_number;
                        $sub_data['is_foreigner'] = (int)$is_foreigner;
                        $sub_data['cnic_number'] = $cnic_number;
                        $sub_data['cnic_number_foreigner'] = $cnic_number_foreigner;
                        $sub_data['cnic_number_original'] = $cnic_original;

                        if (count($nameParts) >= 3) {
                            $sub_data['person_name']  = $nameParts[0] . ' ' . $nameParts[1];
                            $sub_data['person_name1'] = $nameParts[2];
                        } else {
                            $sub_data['person_name']  = $nameParts[0] ?? '';
                            $sub_data['person_name1'] = $nameParts[1] ?? '';
                        }

                        $sub_data['address'] = $address;
                        $sub_data['user_id'] = $data['user_id'];
                        $sub_data['imsi'] = '';
                        $sub_data['StatusRadios'] = $active;
                        $sub_data['ConnectionTypeRadios'] = $status;
                        $sub_data['company_name_get'] = $data['company_name'];
                        $sub_data['imei'] = '';
                        $sub_data['phone_name'] = '';
                        $sub_data['requestid'] = $reference_number;
                        $sub_data['project_id'] = isset($data['project_id']) ? (int) $data['project_id'] : 0;
                        $sub_data['concerned_person_id'] = isset($data['concerned_person_id']) ? (int) $data['concerned_person_id'] : 0;
                        /* -------- Final Extra Check (UNCHANGED LOGIC) -------- */
                        if ($mobile_number[0] === '3') {

                            $sub_model = new Model_Generic();
                            $sub_model->ManualSubInfoinsert($sub_data);

                            Model_ErrorLog::log(
                                'cron_parse_sub_warid',
                                'Mobile number valid, subscriber inserted',
                                [
                                    'request_id' => $reference_number,
                                    'company_name' => $data['company_name'],
                                    'mobile_number' => $mobile_number,
                                    'is_foreigner' => $is_foreigner
                                ],
                                null,
                                'validation_success',
                                'subscriber_parsing','success'
                            );

                            $reference_number_1 = Model_Email::email_status($reference_number, 2, 5);

                        } else {
                            Model_ErrorLog::log(
                                'cron_parse_sub_warid',
                                'Mobile number in-valid, subscriber inserted setting status to (3)',
                                [
                                    'request_id' => $reference_number,
                                    'company_name' => $data['company_name'],
                                    'mobile_number' => $mobile_number,
                                    'is_foreigner' => $is_foreigner
                                ],
                                null,
                                'validation_error',
                                'subscriber_parsing'
                            );
                            $reference_number_1 = Model_Email::email_status($reference_number, 2, 3);
                        }

                    } else {

                        Model_ErrorLog::log(
                            'cron_parse_sub_warid',
                            'CNIC or Mobile validation failed',
                            [
                                'request_id' => $reference_number,
                                'mobile_number' => $mobile_number,
                                'cnic_original' => $cnic_original,
                                'is_foreigner' => $is_foreigner
                            ],
                            null,
                            'validation_error',
                            'subscriber_parsing'
                        );

                        $reference_number_1 = Model_Email::email_status($reference_number, 2, 3);
                    }
                }  
                }

            } catch (Exception $e) {
                $error_msg   = $e->getMessage();
                $error_trace = $e->getTraceAsString();
                $body_sample = substr($data['received_body'] ?? $data['received_body_raw'] ?? '', 0, 800);
                Model_ErrorLog::log(
                    'cron_parse_sub',
                    $error_msg,
                    array(
                        'request_id'       => $reference_number,
                        'company_name'     => $company,
                        'mobile_requested' => $data['requested_value'] ?? 'unknown',
                        'email_body_sample'=> $body_sample,
                        'file_id'          => $data['file_id'] ?? null
                    ),
                    $error_trace,
                    'parsing_failure',
                    'after_include'
                );
                $reference_number = $data['request_id'];
                $reference_number_1 = Model_Email::email_status($reference_number, 2, 3);
            }
        }
        //exit;
    }

    /* Cron Job for Current Location */

    public function action_email_parse_loc() {
        //echo 'Current Location';
        //load page after every 5 seconds
         // echo '<script>setTimeout(function(){   window.location.reload(1); }, 5000)</script>';
        /*         * ** */
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and ur.user_request_type_id = 4
              
                ORDER BY ur.request_id  ASC
            ";                              //Where t1.user_id = {$user_id}  
// and ur.request_id = 1140862
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

         $login_user = Auth::instance()->get_user();
   if(!empty($login_user->id) && $login_user->id==1385){
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 3
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and ur.user_request_type_id = 4
               and company_name=8
                ORDER BY ur.request_id  DESC
            ";                              //Where t1.user_id = {$user_id}  
// and ur.request_id = 1140862
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

//         
          } 
        foreach ($parse_data as $data) {
            
//   if(!empty($login_user->id) && $login_user->id==138){
//         echo '<pre>'; print_r(count($parse_data));         
//          exit;
//         
//          } 

            try {

                $loc_data = array();
                $loc_data['locdate'] = date("Y-m-d H:i:s");
                $loc_data['person_id'] = $data['concerned_person_id'];
               echo '<br>'.  $loc_data['requestid'] = $data['request_id'];
                $loc_data['loccompany'] = $data['company_name'];
                $loc_data['user_id'] = $data['user_id'];
                $loc_data['locimsi'] = '';
                $loc_data['locationmsisdn'] = '';
                $loc_data['locimei'] = '';
                $loc_data['locphonename'] = '';
                $loc_data['locnetwork'] = 0;
                $loc_data['loclac'] = '';
                $loc_data['loccellid'] = '';
                $loc_data['loclat'] = '';
                $loc_data['loclong'] = '';
                $loc_data['locaddress'] = '';
                $loc_data['locstatus'] = 0;
                $not_fount = 0;

                $data_body = array_filter(explode('From:', strip_tags($data['received_body'])));
                $data['received_body'] = $data_body[0];

  
//   $login_user = Auth::instance()->get_user();
//          if(!empty($login_user->id) && $login_user->id==138){
//         echo '<pre>'; print_r($data);         
//          exit;
//         
//          } 
             
                switch ($data['company_name']) {
                    case 1: // mobilink                      
                        // echo '<br>' . 'Mobilink' .'<br>';                                                                        
                        include 'cron_job' . DS . 'parse_location' . DS . 'mobilink.inc';

                        break;
                    case 7: // warid
                        //echo '<br>' . 'Warid' .'<br>';
                        include 'cron_job' . DS . 'parse_location' . DS . 'warid.inc';

                        break;
                    case 3: // Ufone
                        //echo '<br>' . 'Ufone' .'<br>';  
                        
                        include 'cron_job' . DS . 'parse_location' . DS . 'ufone.inc';

                        break;
                    case 6: // Telenor
                        //echo '<br>' . 'Telenor' .'<br>';                        
                        include 'cron_job' . DS . 'parse_location' . DS . 'telenor.inc';

                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                        
                        include 'cron_job' . DS . 'parse_location' . DS . 'zong.inc';

                        break;
                    
                         case 8: // scom
                        //echo '<br>' . 'Scom' .'<br>';                                               
                        include 'cron_job' . DS . 'parse_location' . DS . 'scom.inc';

                        break;
                }

                if ($not_fount == 0) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    if (strlen($loc_data['locationmsisdn']) == 10 && ctype_digit($loc_data['locationmsisdn'])) {
                        $sub_model = new Model_Generic();
                        $sub_model_result = $sub_model->ManualLocationinsert($loc_data);
                    } else {
                        Model_ErrorLog::log(
                            'cron_parse_location',
                            'Invalid location MSISDN format - marking as status 3 (Error)',
                            [
                                'request_id' => $reference_number,
                                'company_name' => $data['company_name'] ?? 'unknown',
                                'processing_index' => 3,
                                'locationmsisdn' => $loc_data['locationmsisdn'] ?? '',
                                'reason' => 'Location MSISDN validation failed: invalid length or format'
                            ],
                            null,
                            'validation_error',
                            'location_parsing'
                        );
                        
                        $reference_number = Model_Email::email_status($reference_number, 2, 3);
                      //  break;
                       // exit;
                    }
                }
            } catch (Exception $e) {
                $error_msg = $e->getMessage();
                $error_trace = $e->getTraceAsString();
                
                Model_ErrorLog::log(
                    'cron_parse_loc',
                    $error_msg,
                    array(
                        'request_id'       => $data['request_id'] ?? 'unknown',
                        'company_name'     => $data['company_name'] ?? 'unknown',
                        'mobile_requested' => $data['requested_value'] ?? 'unknown',
                        'user_id'          => $data['user_id'] ?? null
                    ),
                    $error_trace,
                    'parsing_failure',
                    'location_parsing'
                );
                
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);

                //break;
                //exit();
            }
        }
        
      
    }

    /* Cron Job for CNIC # */

    public function action_email_parse_nic() {
        //echo 'nic #';

        /*         * ** */
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and ur.user_request_type_id = 5
                ORDER BY ur.request_id  ASC
            ";                              //Where t1.user_id = {$user_id}  
        
           $login_user = Auth::instance()->get_user();
          if(!empty($login_user->id) && $login_user->id==138){
           $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and ur.user_request_type_id = 5
                and ur.company_name=4
                ORDER BY ur.request_id DESC
            ";
         
          } 

        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($parse_data as $data) {
            try {

                $not_found_for_telenor = 0;
                $loc_data = array();
                $loc_data['user_id'] = $data['user_id'];
                $loc_data['requestid'] = $data['request_id'];
                $not_fount = 0;
                $name_flag = 0;

                $data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);

//                  echo '<pre>tst';
//                         print_r($data['company_name']); 
//                exit;
                
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        // echo '<br>' . 'Mobilink' .'<br>';
                        include 'cron_job' . DS . 'parse_nic' . DS . 'mobilink.inc';

                        break;
                    case 7: // warid
                        //echo '<br>' . 'Warid' .'<br>';
                        include 'cron_job' . DS . 'parse_nic' . DS . 'warid.inc';

                        break;
                    case 3: // Ufone
                        //echo '<br>' . 'Ufone' .'<br>';                                    
                        include 'cron_job' . DS . 'parse_nic' . DS . 'ufone.inc';

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' .'<br>';                        
                        include 'cron_job' . DS . 'parse_nic' . DS . 'telenor.inc';


                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                                               
                        include 'cron_job' . DS . 'parse_nic' . DS . 'zong.inc';

                        break;
                }

                if($data['company_name'] == 6 && $not_found_for_telenor == 1){
                    $reference_number = $data['request_id'];
                    $reference_number_1 = Model_Email::email_status($reference_number, 2, 8);
                }else{
                    
                    if ($not_fount == 0) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    if (strlen($loc_data['cnicsims']) == 13 && ctype_digit($loc_data['cnicsims'])) {
                        $sub_model = new Model_Generic();
                        $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);
                    } else {
                        Model_ErrorLog::log(
                            'cron_parse_nic',
                            'Invalid CNIC format - marking as status 3 (Error)',
                            [
                                'request_id' => $reference_number,
                                'company_name' => $data['company_name'] ?? 'unknown',
                                'processing_index' => 3,
                                'cnicsims' => $loc_data['cnicsims'] ?? '',
                                'reason' => 'CNIC validation failed: invalid length or format'
                            ],
                            null,
                            'validation_error',
                            'nic_parsing'
                        );
                        
                        $reference_number = Model_Email::email_status($reference_number, 2, 3);
                        break;
                        exit;
                    }
                }
                }
            } catch (Exception $e) {
                $error_msg = $e->getMessage();
                $error_trace = $e->getTraceAsString();
                
                Model_ErrorLog::log(
                    'cron_parse_nic',
                    $error_msg,
                    array(
                        'request_id'       => $data['request_id'] ?? 'unknown',
                        'company_name'     => $data['company_name'] ?? 'unknown',
                        'mobile_requested' => $data['requested_value'] ?? 'unknown',
                        'file_id'          => $data['file_id'] ?? null
                    ),
                    $error_trace,
                    'parsing_failure',
                    'nic_parsing'
                );
                
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
                break;
                exit;
            }
        }
    }
// check prorities high 
    public function action_email_parse_phone_high() {

        $request_id = $this->request->param('id');
        $sub_query = ' and ur.company_name not in (1,7,3,6,4) ';
        if(!empty($request_id)){
            $sub_query = " and ur.request_id =  {$request_id}";
        }else{
            exit;
        }
        
       
        $not_fount = 0;
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and (ur.user_request_type_id = 1 or ur.user_request_type_id = 6)
                {$sub_query}
                ORDER BY ur.request_id  ASC";                              //Where t1.user_id = {$user_id}
//                if(Auth::instance()->get_user()->id==419){
//                    print_r($sql);  exit;
//                }
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($parse_data as $data) {
            try {
                $phone_data['company_name'] = $data['company_name'];
                $phone_data['phone_number'] = $data['requested_value'];
                $phone_data['userrequestid'] = $data['request_id'];
                 $phone_data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $data['id'] = $data['file_id'] = $phone_data['file_id'];
                $cdrfile_name = Helpers_Upload::get_file_info_with_request_id($data['request_id']);


                if(empty($cdrfile_name['file']))
                {
                    $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }
                    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));                                 
                    include DOCUMENT_ROOT . 'application' . DS . 'classes' . DS . 'Controller' . DS . 'cron_job' . DS . 'parse_sub' . DS . 'notfound.inc';
                    exit;
                }    
                $data['received_file_path'] = !empty($cdrfile_name['file'])?$cdrfile_name['file']:'';

                
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'mobilink.inc';

                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'warid.inc';


                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'ufone.inc';
                        // echo '<pre>';
                        // print_r($data['received_file_path']);                      

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' . '<br>';
                        //print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_phone' . DS . 'telenor.inc';



                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                        
                        //print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_phone' . DS . 'zong.inc';

                        break;
                }

                if ($not_fount != 1) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    Model_ErrorLog::log(
                        'cron_parse_phone_high',
                        'Phone parsing completed, no records found - marking as status 5 (Not Found)',
                        array(
                            'request_id' => $reference_number,
                            'company_name' => $data['company_name'] ?? 'unknown',
                            'processing_index' => 5,
                            'phone_number' => $data['requested_value'] ?? 'unknown',
                            'reason' => 'No phone records found in response'
                        ),
                        null,
                        'not_found',
                        'phone_parsing_high','success'
                    );
                    
                    $reference_number = Model_Email::email_status($reference_number, 2, 5);
                    /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                      { */
                    $sub_model = new Model_Generic();
                    //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);
                }
                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                $error_msg = $e->getMessage();
                $error_trace = $e->getTraceAsString();
                
                Model_ErrorLog::log(
                    'cron_parse_phone_high',
                    $error_msg,
                    array(
                        'request_id'       => $data['request_id'] ?? 'unknown',
                        'company_name'     => $data['company_name'] ?? 'unknown',
                        'phone_number'     => $data['requested_value'] ?? 'unknown',
                        'file_id'          => $phone_data['file_id'] ?? null
                    ),
                    $error_trace,
                    'parsing_failure',
                    'phone_parsing_high'
                );
                
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
            }
        }
    }
// mobilink 
    public function action_email_parse_phone() {

        $request_id = $this->request->param('id');
        $sub_query = ' and ur.company_name not in (1,7,3,6,4) ';
        if(!empty($request_id)){
            $sub_query = " and ur.request_id =  {$request_id}";
        }
        $not_fount = 0;
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and (ur.user_request_type_id = 1 or ur.user_request_type_id = 6)
                {$sub_query}
                ORDER BY ur.request_id  ASC";                              //Where t1.user_id = {$user_id}
//                if(Auth::instance()->get_user()->id==419){
//                    print_r($sql);  exit;
//                }
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($parse_data as $data) {
            try {
                $phone_data['company_name'] = $data['company_name'];
                $phone_data['phone_number'] = $data['requested_value'];
                $phone_data['userrequestid'] = $data['request_id'];
                 $phone_data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $data['id'] = $data['file_id'] = $phone_data['file_id'];
                $cdrfile_name = Helpers_Upload::get_file_info_with_request_id($data['request_id']);


                if(empty($cdrfile_name['file']))
                {
                    $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }
                    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));
                    include DOCUMENT_ROOT . 'application' . DS . 'classes' . DS . 'Controller' . DS . 'cron_job' . DS . 'parse_sub' . DS . 'notfound.inc';
                    exit;
                }    
                $data['received_file_path'] = !empty($cdrfile_name['file'])?$cdrfile_name['file']:'';

                
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'mobilink.inc';

                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'warid.inc';


                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'ufone.inc';
                        // echo '<pre>';
                        // print_r($data['received_file_path']);                      

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' . '<br>';
                        //print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_phone' . DS . 'telenor.inc';



                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                        
                        //print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_phone' . DS . 'zong.inc';

                        break;
                }

                if ($not_fount != 1) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    Model_ErrorLog::log(
                        'cron_parse_phone',
                        'Phone parsing completed, no records found - marking as status 5 (Not Found)',
                        array(
                            'request_id' => $reference_number,
                            'company_name' => $data['company_name'] ?? 'unknown',
                            'processing_index' => 5,
                            'phone_number' => $data['requested_value'] ?? 'unknown',
                            'reason' => 'No phone records found in response'
                        ),
                        null,
                        'not_found',
                        'phone_parsing','success'
                    );
                    
                    $reference_number = Model_Email::email_status($reference_number, 2, 5);
                    /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                      { */
                    $sub_model = new Model_Generic();
                    //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);
                }
                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                $error_msg = $e->getMessage();
                $error_trace = $e->getTraceAsString();
                
                Model_ErrorLog::log(
                    'cron_parse_phone',
                    $error_msg,
                    array(
                        'request_id'       => $data['request_id'] ?? 'unknown',
                        'company_name'     => $data['company_name'] ?? 'unknown',
                        'phone_number'     => $data['requested_value'] ?? 'unknown',
                        'file_id'          => $phone_data['file_id'] ?? null
                    ),
                    $error_trace,
                    'parsing_failure',
                    'phone_parsing'
                );
                
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
            }
        }
    }
// mobilink 
    public function action_email_parse_phone_1() {
//       phpinfo();
//       exit;
        // echo 'cdr';
        
        $not_fount = 0;
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and (ur.user_request_type_id = 1 or ur.user_request_type_id = 6)
                and ur.company_name = 1
                ORDER BY ur.request_id  ASC
            ";                              //Where t1.user_id = {$user_id}  

        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($parse_data as $data) {
            try {
                $phone_data['company_name'] = $data['company_name'];
                $phone_data['phone_number'] = $data['requested_value'];
             echo    $phone_data['userrequestid'] = $data['request_id'];
             echo '<br>';  
                $phone_data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $data['id'] = $data['file_id'] = $phone_data['file_id'];
                $cdrfile_name = Helpers_Upload::get_file_info_with_request_id($data['request_id']);
                       

                
                if(empty($cdrfile_name['file']))
                {
                    $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII' || $encode_str=='UTF-8'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }

                    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));
                    include DOCUMENT_ROOT . 'application' . DS . 'classes' . DS . 'Controller' . DS . 'cron_job' . DS . 'parse_sub' . DS . 'notfound.inc';
                    exit;
                }    
                $data['received_file_path'] = !empty($cdrfile_name['file'])?$cdrfile_name['file']:'';

                
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'mobilink.inc';

                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'warid.inc';


                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'ufone.inc';
                        // echo '<pre>';
                        // print_r($data['received_file_path']);                      

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' . '<br>';
                        //print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_phone' . DS . 'telenor.inc';



                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                        
                        //print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_phone' . DS . 'zong.inc';

                        break;
                }

                if ($not_fount != 1) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    Model_ErrorLog::log(
                        'cron_parse_phone_mobilink',
                        'Mobilink phone parsing completed, no records found - marking as status 5 (Not Found)',
                        array(
                            'request_id' => $reference_number,
                            'company_name' => $data['company_name'] ?? 'unknown',
                            'processing_index' => 5,
                            'phone_number' => $data['requested_value'] ?? 'unknown',
                            'reason' => 'No phone records found in Mobilink response'
                        ),
                        null,
                        'not_found',
                        'phone_parsing_mobilink','success'
                    );
                    
                    $reference_number = Model_Email::email_status($reference_number, 2, 5);
                    /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                      { */
                    $sub_model = new Model_Generic();
                    //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);
                }die;
                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                $error_msg = $e->getMessage();
                $error_trace = $e->getTraceAsString();
                
                Model_ErrorLog::log(
                    'cron_parse_phone_1',
                    $error_msg,
                    array(
                        'request_id'       => $data['request_id'] ?? 'unknown',
                        'company_name'     => $data['company_name'] ?? 'unknown',
                        'phone_number'     => $data['requested_value'] ?? 'unknown',
                        'file_id'          => $phone_data['file_id'] ?? null
                    ),
                    $error_trace,
                    'parsing_failure',
                    'phone_parsing_mobilink'
                );
                
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
            }
        }
    }
// Warid 
    public function action_email_parse_phone_7() {
//       phpinfo();
//       exit;
        // echo 'cdr';
        
        $not_fount = 0;
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and (ur.user_request_type_id = 1 or ur.user_request_type_id = 6)
                and ur.company_name = 7
                ORDER BY ur.request_id  ASC
            ";                              //Where t1.user_id = {$user_id}  

        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($parse_data as $data) {
            try {
                $phone_data['company_name'] = $data['company_name'];
                $phone_data['phone_number'] = $data['requested_value'];
             echo    $phone_data['userrequestid'] = $data['request_id'];
             echo '<br>';
                $phone_data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $data['id'] = $data['file_id'] = $phone_data['file_id'];
                $cdrfile_name = Helpers_Upload::get_file_info_with_request_id($data['request_id']);
                       
                if(empty($cdrfile_name['file']))
                {
                    $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII' || $encode_str=='UTF-8'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }
                    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));
                    include DOCUMENT_ROOT . 'application' . DS . 'classes' . DS . 'Controller' . DS . 'cron_job' . DS . 'parse_sub' . DS . 'notfound.inc';
                    exit;
                }    
                $data['received_file_path'] = !empty($cdrfile_name['file'])?$cdrfile_name['file']:'';

                
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'mobilink.inc';

                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        //include 'cron_job' . DS . 'parse_phone' . DS . 'warid.inc';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'mobilink.inc';    

                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'ufone.inc';
                        // echo '<pre>';
                        // print_r($data['received_file_path']);                      

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' . '<br>';
                        //print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_phone' . DS . 'telenor.inc';



                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                        
                        //print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_phone' . DS . 'zong.inc';

                        break;
                }

                if ($not_fount != 1) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    Model_ErrorLog::log(
                        'cron_parse_phone_warid',
                        'Warid phone parsing completed, no records found - marking as status 5 (Not Found)',
                        array(
                            'request_id' => $reference_number,
                            'company_name' => $data['company_name'] ?? 'unknown',
                            'processing_index' => 5,
                            'phone_number' => $data['requested_value'] ?? 'unknown',
                            'reason' => 'No phone records found in Warid response'
                        ),
                        null,
                        'not_found',
                        'phone_parsing_warid','success'
                    );
                    
                    $reference_number = Model_Email::email_status($reference_number, 2, 5);
                    /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                      { */
                    $sub_model = new Model_Generic();
                    //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);
                }
                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                $error_msg = $e->getMessage();
                $error_trace = $e->getTraceAsString();
                
                Model_ErrorLog::log(
                    'cron_parse_phone_7',
                    $error_msg,
                    array(
                        'request_id'       => $data['request_id'] ?? 'unknown',
                        'company_name'     => $data['company_name'] ?? 'unknown',
                        'phone_number'     => $data['requested_value'] ?? 'unknown',
                        'file_id'          => $phone_data['file_id'] ?? null
                    ),
                    $error_trace,
                    'parsing_failure',
                    'phone_parsing_warid'
                );
                
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
            }
        }
    }
// ufone 
    public function action_email_parse_phone_3() {
//       phpinfo();
//       exit;
        // echo 'cdr';
        
        $not_fount = 0;
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and (ur.user_request_type_id = 1 or ur.user_request_type_id = 6)
                and ur.company_name = 3
                ORDER BY ur.request_id  ASC
            ";                              //Where t1.user_id = {$user_id}  

        //print_r($sql); exit;
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($parse_data as $data) {
            try {
                $phone_data['company_name'] = $data['company_name'];
                $phone_data['phone_number'] = $data['requested_value'];
             echo    $phone_data['userrequestid'] = $data['request_id'];
                $phone_data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $data['id'] = $data['file_id'] = $phone_data['file_id'];
                $cdrfile_name = Helpers_Upload::get_file_info_with_request_id($data['request_id']);
                       
              
                
                if(empty($cdrfile_name['file']))
                {
                    $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII' || $encode_str=='UTF-8'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }
                    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));
                    include DOCUMENT_ROOT . 'application' . DS . 'classes' . DS . 'Controller' . DS . 'cron_job' . DS . 'parse_sub' . DS . 'notfound.inc';
                    if($not_fount != 1)
                    {    
                        $reference_number = $data['request_id'];
                        
                        Model_ErrorLog::log(
                            'cron_parse_phone_ufone',
                            'Ufone phone parsing - no file found, checking notfound.inc - marking as status 5 (Not Found)',
                            array(
                                'request_id' => $reference_number,
                                'company_name' => $data['company_name'] ?? 'unknown',
                                'processing_index' => 5,
                                'phone_number' => $data['requested_value'] ?? 'unknown',
                                'reason' => 'No CDR file found, processed notfound.inc'
                            ),
                            null,
                            'not_found',
                            'phone_parsing_ufone','success'
                        );
                        
                        $reference_number = Model_Email::email_status($reference_number, 2, 5);                        
                    }
                     
                    exit;
                }
                
                $data['received_file_path'] = !empty($cdrfile_name['file'])?$cdrfile_name['file']:'';

               
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'mobilink.inc';

                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'warid.inc';


                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'ufone.inc';
                        // echo '<pre>';
                        // print_r($data['received_file_path']);                      

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' . '<br>';
                        //print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_phone' . DS . 'telenor.inc';



                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                        
                        //print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_phone' . DS . 'zong.inc';

                        break;
                }

              
               
                if ($not_fount != 1) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    Model_ErrorLog::log(
                        'cron_parse_phone_ufone',
                        'Ufone phone parsing completed, no records found - marking as status 5 (Not Found)',
                        array(
                            'request_id' => $reference_number,
                            'company_name' => $data['company_name'] ?? 'unknown',
                            'processing_index' => 5,
                            'phone_number' => $data['requested_value'] ?? 'unknown',
                            'reason' => 'No phone records found in Ufone response'
                        ),
                        null,
                        'not_found',
                        'phone_parsing_ufone','success'
                    );
                    
                    $reference_number = Model_Email::email_status($reference_number, 2, 5);
                    /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                      { */
                    $sub_model = new Model_Generic();
                    //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);
                }
                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                $error_msg = $e->getMessage();
                $error_trace = $e->getTraceAsString();
                
                Model_ErrorLog::log(
                    'cron_parse_phone_3',
                    $error_msg,
                    array(
                        'request_id'       => $data['request_id'] ?? 'unknown',
                        'company_name'     => $data['company_name'] ?? 'unknown',
                        'phone_number'     => $data['requested_value'] ?? 'unknown',
                        'file_id'          => $phone_data['file_id'] ?? null
                    ),
                    $error_trace,
                    'parsing_failure',
                    'phone_parsing_ufone'
                );
                
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
            }
        }
    }
// telenor 
    public function action_email_parse_phone_6() {
//       phpinfo();
//       exit;
        // echo 'cdr';
        
        $not_fount = 0;
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and (ur.user_request_type_id = 1 or ur.user_request_type_id = 6)
                and ur.company_name = 6
                ORDER BY ur.request_id  ASC
            ";                              //Where t1.user_id = {$user_id}  

        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($parse_data as $data) {
            try {
                $not_found_for_telenor = 0;
                $phone_data['company_name'] = $data['company_name'];
                $phone_data['phone_number'] = $data['requested_value'];
             echo    $phone_data['userrequestid'] = $data['request_id'];
             echo '<br>';
                $phone_data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $data['id'] = $data['file_id'] = $phone_data['file_id'];
                $cdrfile_name = Helpers_Upload::get_file_info_with_request_id($data['request_id']);
                       
                if(empty($cdrfile_name['file']))
                {
                    $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII' || $encode_str=='UTF-8'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }
                    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));
                    include DOCUMENT_ROOT . 'application' . DS . 'classes' . DS . 'Controller' . DS . 'cron_job' . DS . 'parse_sub' . DS . 'notfound.inc';
                    exit;
                }    
                $data['received_file_path'] = !empty($cdrfile_name['file'])?$cdrfile_name['file']:'';

                
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'mobilink.inc';

                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'warid.inc';


                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'ufone.inc';
                        // echo '<pre>';
                        // print_r($data['received_file_path']);                      

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' . '<br>';
                        //print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_phone' . DS . 'telenor.inc';



                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                        
                        //print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_phone' . DS . 'zong.inc';

                        break;
                }

                if($data['company_name'] == 6 && $not_found_for_telenor == 1){
                    $reference_number = $data['request_id'];
                    $reference_number_1 = Model_Email::email_status($reference_number, 2, 8);
                }else{
                    if ($not_fount != 1) {
                        /* Insertion Code */
                        $reference_number = $data['request_id'];

                        Model_ErrorLog::log(
                            'cron_parse_phone_telenor',
                            'Telenor phone parsing completed, no records found - marking as status 5 (Not Found)',
                        array(
                                'request_id' => $reference_number,
                                'company_name' => $data['company_name'] ?? 'unknown',
                                'processing_index' => 5,
                                'phone_number' => $data['requested_value'] ?? 'unknown',
                                'reason' => 'No phone records found in Telenor response'
                        ),
                            null,
                            'not_found',
                            'phone_parsing_telenor','success'
                        );
                        
                        $reference_number = Model_Email::email_status($reference_number, 2, 5);
                        /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                        { */
                        $sub_model = new Model_Generic();
                        //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);
                    }
                }
                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                $error_msg = $e->getMessage();
                $error_trace = $e->getTraceAsString();
                
                Model_ErrorLog::log(
                    'cron_parse_phone_6',
                    $error_msg,
                    array(
                        'request_id'       => $data['request_id'] ?? 'unknown',
                        'company_name'     => $data['company_name'] ?? 'unknown',
                        'phone_number'     => $data['requested_value'] ?? 'unknown',
                        'file_id'          => $phone_data['file_id'] ?? null
                    ),
                    $error_trace,
                    'parsing_failure',
                    'phone_parsing_telenor'
                );
                
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
            }
        }
    }
// zong 
    public function action_email_parse_phone_4() {
//       phpinfo();
//       exit;
        // echo 'cdr';
        
        $not_fount = 0;
        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and (ur.user_request_type_id = 1 or ur.user_request_type_id = 6)
                and ur.company_name = 4
                ORDER BY ur.request_id  ASC
            ";                              //Where t1.user_id = {$user_id}  

        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($parse_data as $data) {
            try {
                $phone_data['company_name'] = $data['company_name'];
                $phone_data['phone_number'] = $data['requested_value'];
                 $phone_data['userrequestid'] = $data['request_id'];
             //echo '<br>';
                $phone_data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $data['id'] = $data['file_id'] = $phone_data['file_id'];
                $cdrfile_name = Helpers_Upload::get_file_info_with_request_id($data['request_id']);
                       
                if(empty($cdrfile_name['file']))
                {
                    $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII' || $encode_str=='UTF-8'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }
                    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));
                    include DOCUMENT_ROOT . 'application' . DS . 'classes' . DS . 'Controller' . DS . 'cron_job' . DS . 'parse_sub' . DS . 'notfound.inc';
                    exit;
                }    
                $data['received_file_path'] = !empty($cdrfile_name['file'])?$cdrfile_name['file']:'';

                
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'mobilink.inc';

                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'warid.inc';


                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        include 'cron_job' . DS . 'parse_phone' . DS . 'ufone.inc';
                        // echo '<pre>';
                        // print_r($data['received_file_path']);                      

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' . '<br>';
                        //print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_phone' . DS . 'telenor.inc';



                        break;
                    case 4: // Zong
                        //echo '<br>' . 'Zong' .'<br>';                        
                        //print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_phone' . DS . 'zong.inc';

                        break;
                }

                if ($not_fount != 1) {
                    /* Insertion Code */
                    $reference_number = $data['request_id'];

                    Model_ErrorLog::log(
                        'cron_parse_phone_zong',
                        'Zong phone parsing completed, no records found - marking as status 5 (Not Found)',
                        array(
                            'request_id' => $reference_number,
                            'company_name' => $data['company_name'] ?? 'unknown',
                            'processing_index' => 5,
                            'phone_number' => $data['requested_value'] ?? 'unknown',
                            'reason' => 'No phone records found in Zong response'
                        ),
                        null,
                        'not_found',
                        'phone_parsing_zong','success'
                    );
                    
                    $reference_number = Model_Email::email_status($reference_number, 2, 5);
                    /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                      { */
                    $sub_model = new Model_Generic();
                    //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);
                }
                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                $error_msg = $e->getMessage();
                $error_trace = $e->getTraceAsString();
                
                Model_ErrorLog::log(
                    'cron_parse_phone_4',
                    $error_msg,
                    array(
                        'request_id'       => $data['request_id'] ?? 'unknown',
                        'company_name'     => $data['company_name'] ?? 'unknown',
                        'phone_number'     => $data['requested_value'] ?? 'unknown',
                        'file_id'          => $phone_data['file_id'] ?? null
                    ),
                    $error_trace,
                    'parsing_failure',
                    'phone_parsing_zong'
                );
                
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
            }
        }
    }

    public function action_email_parse_imei() {

        echo 'IMEI';

        $sql = "select *
                FROM `user_request` as ur
                join email_messages as em on ur.message_id = em.message_id
                where ur.status = 2 and ur.processing_index = 4
                and ur.request_id NOT IN(select os.request_id from user_os_req as os where os.request_id IS NOT NULL)
                and ur.user_request_type_id = 2 
                ORDER BY ur.request_id  DESC
            ";                              //Where t1.user_id = {$user_id}  

              //   and ur.request_id=661186
         
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();
        $not_fount = 0;
        foreach ($parse_data as $data) {
            try {
                $phone_data['company_name'] = $data['company_name'];
                $phone_data['phone_number'] = $data['requested_value'];
             echo   $phone_data['userrequestid'] = $data['request_id'];
             echo '<br>';
                $phone_data['file_id'] = Helpers_Upload::get_fileid_aginst_requestid($data['request_id']);
                $file_id = $phone_data['file_id'];
                $data['file_id'] = $phone_data['file_id'];
                $cdrfile_name = Helpers_Upload::get_file_info_with_request_id($data['request_id']);
                $data['received_file_path'] = !empty($cdrfile_name['file'])?$cdrfile_name['file']:'';
            
            
                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job' . DS . 'parse_imei' . DS . 'mobilink.inc';
                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        //print_r($data['received_file_path']);      
                        include 'cron_job' . DS . 'parse_imei' . DS . 'warid.inc';
                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        // print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_imei' . DS . 'ufone.inc';
                        break;
                    case 6: // Telenor
                        // echo '<br>' . 'Telenor' .'<br>';                        
                        //print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_imei' . DS . 'telenor.inc';

                        break;
                    case 4: // Zong
                        // echo '<br>' . 'Zong' .'<br>';                        
                        include 'cron_job' . DS . 'parse_imei' . DS . 'zong.inc';
                        break;
                }

                /* Insertion Code */
                $reference_number = $data['request_id'];
                /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                  { */
                $sub_model = new Model_Generic();
                //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);

                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                $error_msg = $e->getMessage();
                $error_trace = $e->getTraceAsString();
                
                Model_ErrorLog::log(
                    'cron_parse_imei',
                    $error_msg,
                    array(
                        'request_id'       => $data['request_id'] ?? 'unknown',
                        'company_name'     => $data['company_name'] ?? 'unknown',
                        'imei'             => $data['requested_value'] ?? 'unknown',
                        'file_id'          => $data['file_id'] ?? null
                    ),
                    $error_trace,
                    'parsing_failure',
                    'imei_parsing'
                );
                
                $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 3);
            }
        }
    }

    public function action_bparty_table() {
        try {
            $data = Model_Generic::get_bparty_data();
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'action_bparty_table',
                $e->getMessage(),
                array(),
                $e->getTraceAsString(),
                'processing_failure',
                'bparty_table'
            );
            error_log("[" . date('c') . "] action_bparty_table failed: " . $e->getMessage());
        }
    }

    public function action_family_tree_complete() {
        try {
            $data = Model_Generic::family_tree_complete();
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'action_family_tree_complete',
                $e->getMessage(),
                array(),
                $e->getTraceAsString(),
                'processing_failure',
                'family_tree_complete'
            );
            error_log("[" . date('c') . "] action_family_tree_complete failed: " . $e->getMessage());
        }
    }

    public function action_resend_in_parse_queue() {
        //$current_ip= $_SERVER['REMOTE_ADDR'];     
        //if($current_ip=='202.125.145.104'){
        try {
            $data = Model_Generic::resend_parse_queue();
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'action_resend_in_parse_queue',
                $e->getMessage(),
                array(),
                $e->getTraceAsString(),
                'processing_failure',
                'resend_parse_queue'
            );
            error_log("[" . date('c') . "] action_resend_in_parse_queue failed: " . $e->getMessage());
        }
    }

    public function action_resend_error_in_queue() {
        try {
            $data = Model_Generic::resend_error_in_queue();
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'action_resend_error_in_queue',
                $e->getMessage(),
                array(),
                $e->getTraceAsString(),
                'processing_failure',
                'resend_error_queue'
            );
            error_log("[" . date('c') . "] action_resend_error_in_queue failed: " . $e->getMessage());
        }
    }

    /* ------------------------------------------------------------------ */
    /*  ECP address-text diagnostics + OCR backfill                       */
    /*                                                                    */
    /*  Purpose: ecp_persons.address_image_base64 stores the address as   */
    /*  a base64 JPEG. To make the address searchable we OCR each image   */
    /*  once and write the recognised text into the sibling address_text  */
    /*  column on the same row, then SQL LIKE/FULLTEXT search becomes     */
    /*  trivial (see Helpers_Person::search_ecp_by_address).              */
    /* ------------------------------------------------------------------ */

    /**
     * One-shot read-only diagnostic: how populated is ecp_persons.address_text
     * today, and how big is the OCR backlog?
     *
     * URL: /cronjob/ecp_address_diagnostic
     */
    public function action_ecp_address_diagnostic()
    {
        self::_stream_init();
        $say = function ($s) { echo $s; @flush(); };

        try {
            $say(sprintf("[%s] connecting to ecp database (192.168.0.156)...\n", date('H:i:s')));
            $DB = Database::instance('ecp');
            $say(sprintf("[%s] connected. running fill-rate query (full scan, may take a while)...\n", date('H:i:s')));

            $t0 = microtime(true);
            $sql = "SELECT
                        COUNT(*) AS total,
                        SUM(address_text IS NOT NULL AND address_text <> '')                 AS with_text,
                        SUM(address_image_base64 IS NOT NULL AND address_image_base64 <> '') AS with_image,
                        ROUND(100 * SUM(address_text IS NOT NULL AND address_text <> '')
                              / NULLIF(COUNT(*), 0), 2)                                       AS pct_with_text
                    FROM ecp_persons";
            $row = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $say(sprintf("[%s] fill-rate query done in %.2fs\n", date('H:i:s'), microtime(true) - $t0));

            $say(sprintf("[%s] running backlog query...\n", date('H:i:s')));
            $t0 = microtime(true);
            $backlog = $DB->query(Database::SELECT,
                "SELECT COUNT(*) AS n FROM ecp_persons
                 WHERE (address_text IS NULL OR address_text = '')
                   AND address_image_base64 IS NOT NULL AND address_image_base64 <> ''",
                TRUE)->current();
            $say(sprintf("[%s] backlog query done in %.2fs\n\n", date('H:i:s'), microtime(true) - $t0));

            $say("ECP address_text fill rate\n");
            $say("==========================\n");
            $say(sprintf("Total rows         : %s\n", number_format((int) $row->total)));
            $say(sprintf("With image         : %s\n", number_format((int) $row->with_image)));
            $say(sprintf("With address_text  : %s\n", number_format((int) $row->with_text)));
            $say(sprintf("Pct with text      : %s%%\n", $row->pct_with_text));
            $say("\n");
            $say(sprintf("Backlog needing OCR: %s\n", number_format((int) $backlog->n)));
            $say("\n");
            $say("Next steps:\n");
            $say("  - If pct_with_text >= 85%, address search is already viable;\n");
            $say("    hit /persons/ecp_address_search_page to try it.\n");
            $say("  - Otherwise run:\n");
            $say("      /cronjob/ecp_address_ocr_backfill?limit=100&engine=tesseract&dry_run=1\n");
            $say("    on a sample first to validate accuracy, then drop dry_run=1.\n");
        } catch (Exception $e) {
            http_response_code(500);
            $say('Diagnostic failed: ' . $e->getMessage() . "\n");
        }
        exit;
    }

    /**
     * OCR-backfill ecp_persons.address_text from address_image_base64.
     *
     * Query params:
     *   limit    batch size (default 100, max 1000)
     *   engine   'tesseract' (default) | 'gvision'
     *   lang     tesseract language code, default 'eng' (e.g. 'eng+urd')
     *   dry_run  if 1, OCR but do not UPDATE — useful for sampling accuracy
     *
     * URL example:
     *   /cronjob/ecp_address_ocr_backfill?limit=200&engine=tesseract&lang=eng+urd
     *
     * Recommended one-time schema additions on ecp.ecp_persons (we have
     * write access per the design doc) so we can distinguish manually
     * entered vs OCR'd text and re-process later if the engine changes:
     *
     *   ALTER TABLE ecp_persons
     *     ADD COLUMN address_text_ocr_at DATETIME    NULL AFTER address_text,
     *     ADD COLUMN address_text_source VARCHAR(16) NULL AFTER address_text_ocr_at;
     *
     * The action runs fine without those columns — it just won't stamp
     * provenance.
     */
    public function action_ecp_address_ocr_backfill()
    {
        @set_time_limit(600);
        self::_stream_init();
        $say = function ($s) { echo $s; @flush(); };

        $limit   = max(1, min(1000, isset($_GET['limit'])  ? (int) $_GET['limit'] : 100));
        $engine  = isset($_GET['engine'])  ? (string) $_GET['engine']  : 'tesseract';
        $lang    = isset($_GET['lang'])    ? (string) $_GET['lang']    : 'eng';
        $dry_run = !empty($_GET['dry_run']);

        $say(sprintf("[%s] backfill starting (limit=%d engine=%s lang=%s%s)\n",
            date('H:i:s'), $limit, $engine, $lang, $dry_run ? ' DRY-RUN' : ''));

        // Pre-flight: catch the most common silent failure (engine binary
        // missing) here instead of letting every row fail with empty OCR.
        if ($engine === 'tesseract') {
            $err = self::_tesseract_check();
            if ($err !== '') {
                http_response_code(500);
                $say("[FATAL] tesseract preflight: {$err}\n");
                $say("        install Tesseract on this host (https://github.com/UB-Mannheim/tesseract/wiki)\n");
                $say("        and ensure the binary is on PATH, OR set tesseract_bin in application/config/ocr.php\n");
                $say("        OR re-run with engine=gvision after configuring google_vision_api_key.\n");
                exit;
            }
            $say("[ok] tesseract binary detected\n");
        }

        try {
            $say(sprintf("[%s] connecting to ecp database...\n", date('H:i:s')));
            $DB = Database::instance('ecp');
            $say(sprintf("[%s] connected\n", date('H:i:s')));

            $has_tracking_cols = self::ecp_has_tracking_columns($DB);
            $say(sprintf("[%s] provenance columns present: %s\n",
                date('H:i:s'), $has_tracking_cols ? 'yes' : 'no (will skip stamping)'));

            $say(sprintf("[%s] selecting up to %d rows missing address_text...\n", date('H:i:s'), $limit));
            $t0  = microtime(true);
            $sql = "SELECT id, address_image_base64
                    FROM ecp_persons
                    WHERE (address_text IS NULL OR address_text = '')
                      AND address_image_base64 IS NOT NULL AND address_image_base64 <> ''
                    LIMIT {$limit}";
            $rows = $DB->query(Database::SELECT, $sql, FALSE)->as_array();
            $say(sprintf("[%s] got %d rows in %.2fs\n", date('H:i:s'), count($rows), microtime(true) - $t0));

            if (empty($rows)) {
                $say("nothing to do — backlog is empty (or filter excludes everything).\n");
                exit;
            }

            $stats = array('processed' => 0, 'updated' => 0, 'empty' => 0, 'failed' => 0);

            foreach ($rows as $row) {
                $stats['processed']++;
                $id = (int) $row['id'];
                $t  = microtime(true);
                try {
                    $bytes = Helpers_Ocr::decode_base64_image($row['address_image_base64']);
                    if ($bytes === '') {
                        $stats['empty']++;
                        $say(sprintf("  id=%-10d  [empty after base64 decode]\n", $id));
                        continue;
                    }
                    $text = Helpers_Ocr::recognise($bytes, $engine, array('lang' => $lang));
                    $ms   = (int) round((microtime(true) - $t) * 1000);
                    if ($text === '') {
                        $stats['empty']++;
                        $say(sprintf("  id=%-10d  %5dms  [OCR returned empty]\n", $id, $ms));
                        continue;
                    }
                    $preview = substr(str_replace(array("\r", "\n"), ' ', $text), 0, 80);
                    if ($dry_run) {
                        $stats['updated']++;
                        $say(sprintf("  id=%-10d  %5dms  [dry] %s\n", $id, $ms, $preview));
                        continue;
                    }
                    $set = "address_text = " . $DB->escape($text);
                    if ($has_tracking_cols) {
                        $set .= ", address_text_ocr_at = NOW(), address_text_source = " . $DB->escape($engine);
                    }
                    $upd = "UPDATE ecp_persons SET {$set} WHERE id = {$id}";
                    $DB->query(Database::UPDATE, $upd, FALSE);
                    $stats['updated']++;
                    $say(sprintf("  id=%-10d  %5dms  [ok]  %s\n", $id, $ms, $preview));
                } catch (Exception $e) {
                    $stats['failed']++;
                    $say(sprintf("  id=%-10d  [err] %s\n", $id, $e->getMessage()));
                }
            }

            $say(sprintf("\n[%s] computing remaining backlog...\n", date('H:i:s')));
            $left = $DB->query(Database::SELECT,
                "SELECT COUNT(*) AS n FROM ecp_persons
                 WHERE (address_text IS NULL OR address_text = '')
                   AND address_image_base64 IS NOT NULL AND address_image_base64 <> ''",
                TRUE)->current();

            $say("\n----- summary -----\n");
            $say(sprintf("engine    : %s (lang=%s)%s\n", $engine, $lang, $dry_run ? ' [dry-run]' : ''));
            $say(sprintf("processed : %d\n", $stats['processed']));
            $say(sprintf("updated   : %d\n", $stats['updated']));
            $say(sprintf("empty     : %d\n", $stats['empty']));
            $say(sprintf("failed    : %d\n", $stats['failed']));
            $say(sprintf("remaining : %d\n", (int) $left->n));
        } catch (Exception $e) {
            http_response_code(500);
            $say('Backfill aborted: ' . $e->getMessage() . "\n");
        }
        exit;
    }

    /**
     * Disable Kohana / PHP / web-server output buffering so subsequent
     * `echo $msg; flush();` pairs reach the browser as they happen.
     * Used by the long-running ECP cron actions so they don't appear
     * to hang while the work is in progress.
     */
    private static function _stream_init($content_type = 'text/plain; charset=utf-8')
    {
        while (ob_get_level()) { @ob_end_clean(); }
        @ini_set('output_buffering',         'Off');
        @ini_set('zlib.output_compression',  'Off');
        @ini_set('implicit_flush',           1);
        @ob_implicit_flush(1);
        if (!headers_sent()) {
            header('Content-Type: ' . $content_type);
            header('X-Accel-Buffering: no');                              // nginx hint
            header('Cache-Control: no-cache, no-store, must-revalidate'); // disable client cache
        }
    }

    /**
     * Verify the tesseract binary is callable. Returns '' on success or a
     * short human-readable error description if the engine cannot be used.
     * Without this check, a missing binary just silently produces empty
     * OCR results for every row — which looks identical to a hung process.
     */
    private static function _tesseract_check()
    {
        if (!function_exists('shell_exec')) {
            return 'shell_exec() is disabled in php.ini (disable_functions)';
        }
        $bin = 'tesseract';
        try {
            $cfg = Kohana::$config->load('ocr');
            if ($cfg && $cfg->get('tesseract_bin')) {
                $bin = $cfg->get('tesseract_bin');
            }
        } catch (Exception $e) { /* config file optional */ }

        $cmd = escapeshellarg($bin) . ' --version 2>&1';
        $out = @shell_exec($cmd);
        if (!is_string($out) || stripos($out, 'tesseract') === false) {
            return "binary not found (tried `{$bin}`). Run `where tesseract` on the server to verify.";
        }
        return '';
    }

    /**
     * Detect once per request whether the optional provenance columns
     * exist on ecp_persons. Cached in static for the request lifetime.
     */
    private static function ecp_has_tracking_columns($DB)
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }
        try {
            $r = $DB->query(Database::SELECT,
                "SHOW COLUMNS FROM ecp_persons LIKE 'address_text_source'", TRUE)->current();
            $cache = !empty($r);
        } catch (Exception $e) {
            $cache = false;
        }
        return $cache;
    }

}
