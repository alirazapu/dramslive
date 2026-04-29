<?php

abstract class Helpers_Email
{

    public static function send_email($to, $to_name, $subject, $body, $attachment = NULL)
    {
        $mail = new PHPMailer(); // create a new object

        $file_name = '';
        if (!empty($attachment)) {
            if (!empty(strip_tags($body))) {
                $file_name = strip_tags($body) . '.txt';
            } else {
                $file_name = 'request.txt';
            }
            $body = '<p></p> ';
        }
        
        // Log SMTP connection attempt
        Model_ErrorLog::log(
            'send_email',
            'Attempting SMTP connection',
            [
                'to' => $to,
                'to_name' => $to_name,
                'subject' => substr($subject, 0, 100),
                'has_attachment' => !empty($attachment) ? 'yes' : 'no'
            ],
            null,
            'smtp_connection_attempt',
            'email_sending',
            'info'
        );
        error_log("[" . date('c') . "] send_email: Attempting SMTP connection for $to");
        
        include 'gmail/sending.inc';
        if (!empty($attachment)) {
            //$mail->addAttachment($attachment,'application/octet-stream');
            $mail->addStringAttachment(file_get_contents($attachment), $file_name);         // Add attachments
        }
        if (!$mail->Send()) {
            //  echo '<pre>';
            // echo "Mailer Error: " . $mail->ErrorInfo;
            
            // Log SMTP send failure
            Model_ErrorLog::log(
                'send_email',
                'SMTP send failed: ' . $mail->ErrorInfo,
                [
                    'to' => $to,
                    'to_name' => $to_name,
                    'subject' => substr($subject, 0, 100),
                    'error' => $mail->ErrorInfo
                ],
                null,
                'smtp_send_failure',
                'email_sending',
                'error'
            );
            error_log("[" . date('c') . "] send_email FAILED for $to: " . $mail->ErrorInfo);
            
            return 2;
            //exit;
        } else {
            //echo "Message has been sent";
            //exit;
            
            // Log SMTP send success
            Model_ErrorLog::log(
                'send_email',
                'SMTP send successful',
                [
                    'to' => $to,
                    'to_name' => $to_name,
                    'subject' => substr($subject, 0, 100)
                ],
                null,
                'smtp_send_success',
                'email_sending',
                'success'
            );
            error_log("[" . date('c') . "] send_email SUCCESS for $to");
            
            return 1;
        }
        exit;
    }


    public static function receive_email($subject, $sender)
    {
        /* ===================== SAFETY GUARDS ===================== */

        // Execution limit (cron-safe)
        ini_set('max_execution_time', 240);
        set_time_limit(240);

        // Runtime lock (prevents parallel runs)
        $lockFile = DOCROOT . 'application/logs/gmail_imap_receive.lock';
        $lockFp   = fopen($lockFile, 'c');

        if (!flock($lockFp, LOCK_EX | LOCK_NB)) {
            error_log('[' . date('c') . '] IMAP already running, exiting.');
            return 0;
        }

        // Cooldown after failure (5 minutes)
        $cooldownFile = DOCROOT . 'application/logs/gmail_imap_fail.cooldown';
        if (file_exists($cooldownFile) && (time() - filemtime($cooldownFile)) < 300) {
            error_log('[' . date('c') . '] IMAP cooldown active, skipping run.');
            return 0;
        }

        /* ===================== GMAIL SETTINGS ===================== */

        if ((int)$sender !== 2) {
            return 0;
        }
        //$hostname = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
        $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
        $result   = Helpers_Inneruse::get_gmail_pw();
        $username = $result['receive']['user'];
        $password = $result['receive']['password'];

        // IMAP timeouts (very important)
        imap_timeout(IMAP_OPENTIMEOUT, 20);
        imap_timeout(IMAP_READTIMEOUT, 60);
        imap_timeout(IMAP_WRITETIMEOUT, 60);
        imap_timeout(IMAP_CLOSETIMEOUT, 20);

        error_log('[' . date('c') . "] IMAP connecting to Gmail as {$username}");

        /* ===================== IMAP CONNECT ===================== */

        $inbox = @imap_open($hostname, $username, $password, 0, 1);

        if (!$inbox) {
            $error = imap_last_error();
            imap_errors();
            imap_alerts();

            // Start cooldown
            file_put_contents($cooldownFile, time());

            Model_ErrorLog::log(
                'receive_email',
                'Gmail IMAP connection failed',
                [
                    'username' => $username,
                    'hostname' => $hostname,
                    'error'    => $error
                ],
                null,
                'imap_connection_failure',
                'email_receiving',
                'error'
            );

            error_log('[' . date('c') . '] IMAP FAILED: ' . $error);
            return 0;
        }

        error_log('[' . date('c') . '] IMAP connection SUCCESS');

        /* ===================== SEARCH EMAILS ===================== */

        $emails = imap_search($inbox, 'UNSEEN');

        if ($emails === false || empty($emails)) {
            imap_close($inbox);
            flock($lockFp, LOCK_UN);
            fclose($lockFp);
            return 1;
        }

        rsort($emails);
        /* For each email... */
        foreach ($emails as $email_number) {
            $is_file_exist = 0;
            /* 🔴 CRITICAL VALIDATION */

            if (!is_numeric($email_number) || $email_number < 1) {
                continue;
            }

         //   $headerInfo = imap_headerinfo($inbox, $email_number);
            $structure = imap_fetchstructure($inbox, $email_number);

            /* get information specific to this email */
            $overview = imap_fetch_overview($inbox, $email_number, 0);

            $message = imap_fetchbody($inbox, $email_number, 1.2);
            if (empty($message))
                $message = quoted_printable_decode(imap_fetchbody($inbox, $email_number, 1));
            if (empty($message))
                $message = imap_fetchbody($inbox, $email_number, 2);

            /* explode subject */
            if(!isset($overview[0]->subject) || empty($overview[0]->subject)){
                imap_clearflag_full($inbox, $email_number, '\\Seen');  //Seen
                continue;
            }
            // Token-priority routing: ADM- and QRM markers in subject OR body
            // identify admin requests (both /admin_custom_request_form and
            // /admin_request_sent_form embed 'ADM-<reference_id>'). Without a
            // marker we fall back to the FIR/DD body footer or to subject
            // digits and default to user_request lookup. See
            // Helpers_Email::extract_reference_from_email and
            // ::find_request_by_reference for full details.
            $extracted = Helpers_Email::extract_reference_from_email(
                $overview[0]->subject,
                $message
            );
            $query_subject_final = $extracted['reference_id'];
            $source_hint         = $extracted['source'];

            if (empty($query_subject_final)) {
                imap_clearflag_full($inbox, $email_number, '\\Seen');
                continue;
            }
            $query_subject_final = Helpers_Email::emailreadstatuscheckUpdate($email_number, $query_subject_final);
            $members = Helpers_Email::find_request_by_reference($query_subject_final, $source_hint);
            if (empty($members)) {
                $members = '';
            }
            if (!empty($members)) {
                $part = !empty($structure->parts[1]) ? $structure->parts[1] : '';
                if ($members['company_name'] == 13 || $members['company_name'] == 12 || $members['company_name'] == 11 || $members['company_name'] == 4 || $members['company_name'] == 3) {
                    $message_raw = imap_fetchbody($inbox, $email_number, 2);
                    if (!empty($part) && $part->encoding == 3) {
                        $message_raw = imap_base64($message_raw);
                    } else if (!empty($part) && $part->encoding == 1) {
                        $message_raw = imap_8bit($message_raw);
                    } else {
                        $message_raw = imap_qprint($message_raw);
                    }
                } elseif ($members['company_name'] == 6)
                    $message_raw = imap_base64(imap_fetchbody($inbox, $email_number, 1));
                else
                    $message_raw = imap_fetchbody($inbox, $email_number, 1);
                /*telco code for received email */
                $date_for_telco = date('Y-m-d'); //$date;
                $tel_report = "select * from telco_request_summary where date = '{$date_for_telco}'";
                $sql_telco = DB::query(Database::SELECT, $tel_report);
                $report_telco_result = $sql_telco->execute()->as_array();

                if (!empty($report_telco_result)) {
                    $query = DB::update('telco_request_summary')->set(array('total_received' => DB::expr('total_received + 1')))
                        ->where('date', '=', $date_for_telco)
                        ->and_where('company_mnc', '=', $members['company_name']);
                       $query->execute();
                } else {
                    $tel_co_mnc = array(1, 3, 4, 6, 7, 11, 12, 13);
                    $telco_array = array();
                    foreach ($tel_co_mnc as $mnc) {
                        $company_mnc = $mnc;
                        $send_high = 0;
                        $send_medium = 0;
                        $send_low = 0;
                        $total_send = 0;
                        $total_received = 0;
                        $t_date = '"' . $date_for_telco . '"';
                        $telco_array[] = '(' . $t_date . ', ' . $company_mnc . ', ' . $send_high . ', ' . $send_medium . ', ' . $send_low . ', ' . $total_send . ')';

                    }
                    $query = 'INSERT INTO telco_request_summary (`date`, `company_mnc`, `send_high`, `send_medium`, `send_low`, `total_send`) VALUES ' . implode(',', $telco_array);
                    $sql = DB::query(Database::INSERT, $query)->execute();
                }
                $output='';
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
                            } /* 4 = QUOTED-PRINTABLE encoding */ elseif ($structure->parts[$i]->encoding == 4) {
                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                            }
                        }
                    }
                }
                $filename = '';

                foreach ($attachments as $attachment) {
                    if ($attachment['is_attachment'] != 1) continue;

                    $original_name = !empty($attachment['name']) ? $attachment['name'] : ($attachment['filename'] ?? 'no-name');
                    /*$file_id = Helpers_Upload::get_fileid_with_requestid($members['request_id'] ?? 0)
                        ?: Helpers_Utilities::id_generator("file_id");
                    if (!empty($file_id)) {
                        $is_file_exist = 1;
                    } else {
                        $is_file_exist = 0;
                    }*/
                    $file_id = Helpers_Upload::get_fileid_with_requestid($members['request_id']);
                    if(!empty($file_id))
                    {
                        $is_file_exist =1;
                    }else{
                        $is_file_exist =0;
                        $file_id = Helpers_Utilities::id_generator("file_id");
                    }
                    $file_path = Helpers_Upload::get_request_data_path($file_id, 'save');

                    $extension = pathinfo($original_name, PATHINFO_EXTENSION) ?: 'bin';
                    $safe_filename = 'rqt' . $query_subject_final . 'fid' . $file_id . '.' . $extension;
                    $full_path = rtrim($file_path, '/\\') . DIRECTORY_SEPARATOR . $safe_filename;

                    // Debug logging
                    error_log("[" . date('c') . "] Attachment processing - file_id: $file_id, " .
                        "data_length: " . (isset($attachment['attachment']) ? strlen($attachment['attachment']) : 'MISSING') . " bytes, " .
                        "target_dir: $file_path, " .
                        "is_dir: " . (is_dir($file_path) ? 'YES' : 'NO') . ", " .
                        "is_writable: " . (is_writable($file_path) ? 'YES' : 'NO'));

                    // Check if attachment data is empty
                    if (empty($attachment['attachment'])) {
                        Model_ErrorLog::log(
                            'receive_email_attachment',
                            'Attachment data is empty or missing',
                            array(
                                'request_id' => isset($members['request_id']) ?$members['request_id']: null,
                                'file_id' => $file_id,
                                'original_name' => $original_name,
                                'file_path' => $file_path
                            ),
                            debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),
                            'attachment_error',
                            'email_receiving',
                            'warning'
                        );
                        error_log("Empty attachment data for file_id $file_id");
                        continue;
                    }

                    // Check if directory exists and is writable
                    if (!is_dir($file_path) || !is_writable($file_path)) {
                        Model_ErrorLog::log(
                            'receive_email_attachment',
                            'Directory missing or not writable',
                            array(
                                'request_id' => isset($members['request_id']) ?$members['request_id']: null,
                                'file_id' => $file_id,
                                'original_name' => $original_name,
                                'file_path' => $file_path,
                                'is_dir' => is_dir($file_path),
                                'is_writable' => is_writable($file_path)
                            ),
                            debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),
                            'attachment_error',
                            'email_receiving',
                            'error'
                        );
                        error_log("Cannot save - dir issue: $file_path");
                        continue;
                    }

                    // Attempt to open file for writing
                    $fp = @fopen($full_path, 'wb');
                    if ($fp === false) {
                        $err = error_get_last();
                        Model_ErrorLog::log(
                            'receive_email_attachment',
                            'Cannot open file for writing',
                            array(
                                'request_id' => isset($members['request_id']) ?$members['request_id']: null,
                                'file_id' => $file_id,
                                'original_name' => $original_name,
                                'full_path' => $full_path,
                                'error_message' => isset($err['message']) ?$err['message']: 'unknown'
                            ),
                            debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),
                            'attachment_error',
                            'email_receiving',
                            'error'
                        );
                        error_log("fopen failed: $full_path - " . (isset($err['message']) ?$err['message']: 'no error info'));
                        continue;
                    }

                    // Write attachment data to file
                    $bytes = fwrite($fp, $attachment['attachment']);
                    fclose($fp);

                    // Check if write was successful
                    if ($bytes === false || $bytes !== strlen($attachment['attachment'])) {
                        Model_ErrorLog::log(
                            'receive_email_attachment',
                            'Partial write - file may be incomplete',
                            array(
                                'request_id' => isset($members['request_id']) ? $members['request_id']: null,
                                'file_id' => $file_id,
                                'original_name' => $original_name,
                                'full_path' => $full_path,
                                'bytes_written' => $bytes,
                                'expected_bytes' => strlen($attachment['attachment'])
                            ),
                            debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),
                            'attachment_warning',
                            'email_receiving',
                            'warning'
                        );
                        error_log("Partial write: $full_path - $bytes bytes");
                    } else {
                        error_log("[" . date('c') . "] File written successfully: $safe_filename ($bytes bytes)");
                    }
                    $filename=$safe_filename;
                }
                $status = imap_setflag_full($inbox, $email_number, "\Seen \Flagged"); //i will use later
                $result = array();
                 $result['file'] = $filename;

                try {
                    $extrac = explode("On Sun,", $message);
                    $extrac = explode("On Mon,", $extrac[0]);
                    $extrac = explode("On Tue,", $extrac[0]);
                    $extrac = explode("On Wed,", $extrac[0]);
                    $extrac = explode("On Thu,", $extrac[0]);
                    $extrac = explode("On Fri,", $extrac[0]);
                    $extrac = explode("On Sat,", $extrac[0]);
                    $result['body'] = strip_tags($extrac[0]);
                } catch (Exception $e) {
                    $result['body'] = $message_raw;
                }
                $result['body_raw'] = $message_raw;
                $file_name = !empty($result['file']) ? $result['file'] : 'na';
                $body = !empty($result['body']) ? $result['body'] : 'na';
                $body_raw = !empty($result['body_raw']) ? $result['body_raw'] : 'na';
                if ($members['company_name'] >= 11 && $members['company_name'] <= 13) {
                    $process_index = 7;
                } else {
                    $process_index = 4;
                }
                $source = isset($members['source']) ? $members['source'] : 'user';
                // Admin replies can carry attachments too — keep the files-table
                // record for both sources.
                if (!empty($file_id) && $file_name != 'na' && $is_file_exist == 0) {
                    Helpers_Upload::insert_file_record($file_name, $members['user_id'], $members['user_request_type_id'], $members['company_name'], $members['requested_value'], $members['request_id'], $members['reason'], $file_id);
                }
                Helpers_Email::change_status_raw($file_name, $body_raw, $body, $members['message_id'], $members['request_id'], $is_file_exist, null, $source);
            }
            else {
                imap_clearflag_full($inbox, $email_number, '\\Seen');  //Seen
            }
        }
        imap_close($inbox, CL_EXPUNGE);
        flock($lockFp, LOCK_UN);
        fclose($lockFp);

        error_log('[' . date('c') . '] IMAP run completed successfully');
        return 1;

    }

    public static function receive_email_backup($subject, $sender)
    {
        $filename = '';

        /* ===================== SAFETY GUARDS ===================== */

        // Execution limit (cron-safe).
        ini_set('max_execution_time', 240);
        set_time_limit(240);

        // Runtime lock — shared with receive_email() because both connect to
        // the same Gmail account. If either method is running, the other
        // exits immediately rather than racing on the same mailbox (which
        // would cause double Seen-flagging and duplicate row updates).
        $lockFile = DOCROOT . 'application/logs/gmail_imap_receive2.lock';
        $lockFp   = fopen($lockFile, 'c');

        if (!flock($lockFp, LOCK_EX | LOCK_NB)) {
            error_log('[' . date('c') . '] IMAP (backup) already running, exiting.');
            return 0;
        }

        // Cooldown after failure (5 minutes) — also shared with receive_email()
        // so an upstream Gmail outage causes both methods to back off together
        // instead of hammering the failing connection.
        $cooldownFile = DOCROOT . 'application/logs/gmail_imap_fail.cooldown';
        if (file_exists($cooldownFile) && (time() - filemtime($cooldownFile)) < 300) {
            error_log('[' . date('c') . '] IMAP (backup) cooldown active, skipping run.');
            flock($lockFp, LOCK_UN);
            fclose($lockFp);
            return 0;
        }

        /* ===================== GMAIL SETTINGS ===================== */

        // Credentials come from the same config-driven source that receive_email()
        // uses — Helpers_Inneruse::get_gmail_pw()['receive']. The previous hardcoded
        // kpkctd@gmail.com / wjlrthkqsmansnqe pair drifted out of sync with the
        // mailbox that receives admin replies (dcs@ctkpmail.com), so admin-flow
        // replies were arriving in a mailbox the cron never opened.
        $hostname  = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
        $gmail_pw  = Helpers_Inneruse::get_gmail_pw();
        $username  = isset($gmail_pw['receive']['user'])     ? $gmail_pw['receive']['user']     : '';
        $password  = isset($gmail_pw['receive']['password']) ? $gmail_pw['receive']['password'] : '';

        // IMAP timeouts so a hung Gmail connection can't pin the cron worker.
        imap_timeout(IMAP_OPENTIMEOUT,  20);
        imap_timeout(IMAP_READTIMEOUT,  60);
        imap_timeout(IMAP_WRITETIMEOUT, 60);
        imap_timeout(IMAP_CLOSETIMEOUT, 20);

        $inbox = @imap_open($hostname, $username, $password, 0, 1);
        if (!$inbox) {
            // Log IMAP connection failure
            $error = imap_last_error();
            imap_errors();
            imap_alerts();

            // Start cooldown — both methods will skip for the next 5 minutes.
            file_put_contents($cooldownFile, time());

            Model_ErrorLog::log(
                'receive_email_backup',
                'IMAP connection failed (backup): ' . $error,
               array(
                    'username' => $username,
                    'hostname' => $hostname,
                    'error' => $error
               ),
                null,
                'imap_connection_failure',
                'email_receiving',
                'error'
            );
            error_log("[" . date('c') . "] receive_email_backup: IMAP connection FAILED for $username: " . $error);

            // Release the lock and bail gracefully (was: die() — which left
            // the lock held and dumped a fatal error to the cron output).
            flock($lockFp, LOCK_UN);
            fclose($lockFp);
            return 0;
        }


        $since = date('d-M-Y', strtotime('-3 days'));  // e.g. "19-Apr-2025" :contentReference[oaicite:7]{index=7}
        $before = date('d-M-Y', strtotime('today'));    // e.g. "21-Apr-2025"

        // 3) Search unseen within that window
        $criteria = sprintf(
            'UNSEEN SINCE "%s" BEFORE "%s"',
            $since,
            $before
        );
        //$emails = imap_search($inbox, $criteria);  // returns messages with internal date ≥ $since and < $before :contentReference[oaicite:8]{index=8}
        $emails = imap_search($inbox, 'UNSEEN');
        if ($emails) {

            $output = '';

            /* Make the newest emails on top */
            rsort($emails);
            /* For each email... */
            foreach ($emails as $email_number) {
                $is_file_exist = 0;
                $structure = '';
                $structure = imap_fetchstructure($inbox, $email_number);

                /* get information specific to this email */
                $overview = imap_fetch_overview($inbox, $email_number, 0);

                $message = imap_fetchbody($inbox, $email_number, 1.2);
                if (empty($message))
                    $message = quoted_printable_decode(imap_fetchbody($inbox, $email_number, 1));
                if (empty($message))
                    $message = imap_fetchbody($inbox, $email_number, 2);
                $check_match = trim(str_replace("Re:", "", $overview[0]->subject));

                // Token-priority routing: ADM- and QRM markers in subject OR
                // body identify admin requests (both /admin_custom_request_form
                // and /admin_request_sent_form embed 'ADM-<reference_id>' in
                // the outgoing subject and body). Without a marker, fall back
                // to the FIR/DD body footer or to subject digits, and default
                // to user_request lookup. See Helpers_Email::extract_reference_from_email
                // and ::find_request_by_reference for full details.
                $extracted = Helpers_Email::extract_reference_from_email(
                    $overview[0]->subject,
                    $message
                );
                $query_subject_final = $extracted['reference_id'];
                $source_hint         = $extracted['source'];

                if (empty($query_subject_final) || $query_subject_final < 1000) {
                    $status = imap_setflag_full($inbox, $email_number, "\Seen \Flagged");
                    continue;
                }

                $query_subject_final = Helpers_Email::emailreadstatuscheckUpdate($email_number, $query_subject_final);

                // Look up the request — admin first when the source hint says
                // so (ADM-/QRM token), otherwise user_request first.
                $members = Helpers_Email::find_request_by_reference($query_subject_final, $source_hint);
                if (empty($members)) {
                    $members = '';
                }

                if (!empty($members)) {
                    $part = !empty($structure->parts[1]) ? $structure->parts[1] : '';
                    if ($members['company_name'] == 13 || $members['company_name'] == 12 || $members['company_name'] == 11 || $members['company_name'] == 4 || $members['company_name'] == 3) {
                        $message_raw = imap_fetchbody($inbox, $email_number, 2);
                        if (!empty($part) && $part->encoding == 3) {
                            $message_raw = imap_base64($message_raw);
                        } else if (!empty($part) && $part->encoding == 1) {
                            $message_raw = imap_8bit($message_raw);
                        } else {
                            $message_raw = imap_qprint($message_raw);
                        }
                    } elseif ($members['company_name'] == 6)
                        $message_raw = imap_base64(imap_fetchbody($inbox, $email_number, 1));
                    else
                        $message_raw = imap_fetchbody($inbox, $email_number, 1);

                    /*telco code for received email */
                    $date_for_telco = date('Y-m-d'); //$date;
                    $tel_report = "select * from telco_request_summary where date = '{$date_for_telco}'";
                    $sql_telco = DB::query(Database::SELECT, $tel_report);
                    $report_telco_result = $sql_telco->execute()->as_array();

                    if (!empty($report_telco_result)) {
                        $query = DB::update('telco_request_summary')->set(array('total_received' => DB::expr('total_received + 1')))
                            ->where('date', '=', $date_for_telco)
                            ->and_where('company_mnc', '=', $members['company_name'])
                            ->execute();
                    } else {
                        $tel_co_mnc = array(1, 3, 4, 6, 7, 11, 12, 13);
                        $telco_array = array();
                        foreach ($tel_co_mnc as $mnc) {
                            $company_mnc = $mnc;
                            $send_high = 0;
                            $send_medium = 0;
                            $send_low = 0;
                            $total_send = 0;
                            $total_received = 0;
                            $t_date = '"' . $date_for_telco . '"';
                            $telco_array[] = '(' . $t_date . ', ' . $company_mnc . ', ' . $send_high . ', ' . $send_medium . ', ' . $send_low . ', ' . $total_send . ')';

                        }
                        $query = 'INSERT INTO telco_request_summary (`date`, `company_mnc`, `send_high`, `send_medium`, `send_low`, `total_send`) VALUES ' . implode(',', $telco_array);
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
                                } /* 4 = QUOTED-PRINTABLE encoding */ elseif ($structure->parts[$i]->encoding == 4) {
                                    $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                                }
                            }
                        }
                    }
                    $filename = '';

                    foreach ($attachments as $attachment) {
                        if ($attachment['is_attachment'] == 1) {
                            $filename = !empty($attachment['name']) ? $attachment['name'] : $attachment['filename'];
                            //getting file_id
                            $file_id = Helpers_Upload::get_fileid_with_requestid($members['request_id']);
                            if (!empty($file_id)) {
                                $is_file_exist = 1;
                            } else {
                                $is_file_exist = 0;
                                $file_id = Helpers_Utilities::id_generator("file_id");
                            }
                            $file_path = !empty($file_id) ? Helpers_Upload::get_request_data_path($file_id, 'save') : '';

                            $new_file_info = PATHINFO($attachment['filename']);
                            if (!empty($filename) && !empty($new_file_info) && !empty($new_file_info['extension'])) {
                                if (!empty($filename)) {
                                    $filename = 'rqt' . $query_subject_final . 'fid' . $file_id . '.' . $new_file_info['extension'];
                                } else {
                                    $filename = 'rqt' . $query_subject_final . 'fid' . $file_id . '.' . $new_file_info['extension'];
                                    //$filename = $query_subject_final . '_' . $attachment['filename'];
                                }
                            }
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
                    try {
                        $extrac = explode("On Sun,", $message);
                        $extrac = explode("On Mon,", $extrac[0]);
                        $extrac = explode("On Tue,", $extrac[0]);
                        $extrac = explode("On Wed,", $extrac[0]);
                        $extrac = explode("On Thu,", $extrac[0]);
                        $extrac = explode("On Fri,", $extrac[0]);
                        $extrac = explode("On Sat,", $extrac[0]);
                        $result['body'] = strip_tags($extrac[0]);
                    } catch (Exception $e) {
                        $result['body'] = $message_raw;
                    }
                    $result['body_raw'] = $message_raw;
                    //imap_close($inbox);

                    $file_name = !empty($result['file']) ? $result['file'] : 'na';
                    $body = !empty($result['body']) ? $result['body'] : 'na';
                    $body_raw = !empty($result['body_raw']) ? $result['body_raw'] : 'na';

                    if ($members['company_name'] >= 11 && $members['company_name'] <= 13) {
                        $process_index = 7;
                    } else {
                        $process_index = 4;
                    }

                    //Helpers_Email::change_status_raw($file_name, $body_raw, $body, $members['message_id'], $members['request_id'], $process_index);
                    $source = isset($members['source']) ? $members['source'] : 'user';
                    // Admin replies can carry attachments too — keep the files-table
                    // record for both sources.
                    if (!empty($file_id) && $file_name != 'na' && $is_file_exist == 0) {
                        Helpers_Upload::insert_file_record($file_name, $members['user_id'], $members['user_request_type_id'], $members['company_name'], $members['requested_value'], $members['request_id'], $members['reason'], $file_id);
                    }
                    Helpers_Email::change_status_raw($file_name, $body_raw, $body, $members['message_id'], $members['request_id'], $is_file_exist, null, $source);
                } else {
                    imap_clearflag_full($inbox, $email_number, '\\Seen');  //Seen
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

        // Release the runtime lock so the next scheduled cron tick can run.
        flock($lockFp, LOCK_UN);
        fclose($lockFp);

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

    /**
     * Resolve a reference_id to the originating request row.
     *
     * @param int         $reference_id  Numeric reference id extracted from the email.
     * @param string|null $source_hint   'admin' when the email carried ADM-/QRM
     *                                   (admin custom or single request); null
     *                                   defaults to user-request-first ordering.
     *
     * Returns the matched row plus a 'source' field ('user'|'admin') so callers
     * know which set of tables to update on reply (user_request/email_messages
     * vs admin_request/admin_email_messages). Returns null when nothing matches.
     */
    public static function find_request_by_reference($reference_id, $source_hint = null)
    {
        $reference_id = (int) $reference_id;
        if ($reference_id < 1) {
            return null;
        }

        if ($source_hint === 'admin') {
            // Token said admin — try admin first, fall back to user as a safety net.
            $row = self::_query_admin_request($reference_id);
            if (!empty($row)) return $row;
            return self::_query_user_request($reference_id);
        }

        // Default ordering: user_request first (high-volume path), admin second.
        $row = self::_query_user_request($reference_id);
        if (!empty($row)) return $row;
        return self::_query_admin_request($reference_id);
    }

    /**
     * Query user_request awaiting a reply: status=1 (sent), processing_index=0.
     */
    private static function _query_user_request($reference_id)
    {
        $sql = "SELECT 'user' as source, t1.request_id, t1.reason, t1.user_id,
                       t1.user_request_type_id, t2.email_type_name, t1.requested_value,
                       t1.concerned_person_id, t1.company_name, t1.created_at,
                       t1.status, t1.processing_index,
                       em.message_id, em.message_subject, em.sender_id
                FROM user_request as t1
                JOIN email_templates_type as t2 ON t1.user_request_type_id = t2.id
                JOIN email_messages as em ON em.message_id = t1.message_id
                WHERE t1.user_request_type_id != 8
                  AND t1.status = 1 AND t1.processing_index = 0
                  AND t1.reference_id = {$reference_id}
                LIMIT 1";
        $row = DB::query(Database::SELECT, $sql)->execute()->current();
        return !empty($row) ? $row : null;
    }

    /**
     * Query admin_request awaiting a reply: status=1 (sent), processing_index=7.
     * Covers BOTH admin routes — Adminrequest/admin_custom_request_form
     * (action_admincustomsend) and Adminrequest/admin_request_sent_form
     * (action_adminsend) — because both go through the same
     * Model_AdminRequest::admin_request() insert and the same
     * Model_AdminRequest::admin_email_sended(...,7,1) status update.
     * LEFT JOIN email_templates_type because admin requests can use ad-hoc
     * request_type_ids that may not have a matching template row.
     */
    private static function _query_admin_request($reference_id)
    {
        $sql = "SELECT 'admin' as source, t1.request_id, t1.reason, t1.user_id,
                       t1.user_request_type_id,
                       COALESCE(t2.email_type_name, '') as email_type_name,
                       t1.requested_value, NULL as concerned_person_id, t1.company_name,
                       t1.created_at, t1.status, t1.processing_index,
                       em.message_id, em.message_subject, em.sender_id
                FROM admin_request as t1
                LEFT JOIN email_templates_type as t2 ON t1.user_request_type_id = t2.id
                JOIN admin_email_messages as em ON em.message_id = t1.message_id
                WHERE t1.status = 1 AND t1.processing_index = 7
                  AND t1.reference_id = {$reference_id}
                LIMIT 1";
        $row = DB::query(Database::SELECT, $sql)->execute()->current();
        return !empty($row) ? $row : null;
    }

    /**
     * Extract the request reference_id and a source hint from an incoming email.
     *
     * Token-priority routing — both admin routes (Adminrequest/admin_custom_request_form
     * and Adminrequest/admin_request_sent_form) embed "ADM-<reference_id>" in
     * the outgoing subject AND body, so the marker survives most reply forms
     * (Re:-chain, fresh subject, body-only quote). QRM is reserved for the
     * quick-request module and is treated as admin-routed too.
     *
     * Returns ['reference_id' => int, 'source' => 'admin'|null]:
     *   - source='admin' → caller should look up admin_request first
     *   - source=null    → no source preference, default user_request first
     */
    public static function extract_reference_from_email($subject, $body)
    {
        // Combined haystack so a quoted original (in the reply body) or a
        // fresh-subject reply both have enough material to match on.
        $haystack = (string) $subject . "\n" . (string) $body;

        // 1) ADM- / QRM markers → admin route (covers both admin custom and
        //    admin single requests; both share the 'ADM-' prefix today, QRM
        //    is reserved for future quick-request module use).
        if (preg_match('/\b(?:ADM|QRM)[-\s]*(\d{4,})/i', $haystack, $m)) {
            return array('reference_id' => (int) $m[1], 'source' => 'admin');
        }

        // 2) Universal CTD body footer "FIR/DD NO <id> PS CTD ...".
        //    Used by BOTH user and admin outgoing emails, so we know the
        //    reference but not the source — caller's default ordering applies.
        if (preg_match('/FIR[\s\/]*DD[\s\/]*NO[\s\/\.\#:]*(\d{4,})/i', $haystack, $m)) {
            return array('reference_id' => (int) $m[1], 'source' => null);
        }

        // 3) Subject fallback — first 4+ digit number, must be >= 1000.
        $clean_subject = str_replace(array('/', ',', '.'), array(' /', ' ,', ' . '), (string) $subject);
        if (preg_match('/\b(\d{4,})\b/', $clean_subject, $m) && (int) $m[1] >= 1000) {
            return array('reference_id' => (int) $m[1], 'source' => null);
        }

        // 4) Generic FIR/DD/REF fallback for variations like "FIR # 88556".
        if (preg_match('/(?:FIR|DD|REF|REFERENCE)[\s\/\.\#:NO]*(\d{4,})/i', $haystack, $m)) {
            return array('reference_id' => (int) $m[1], 'source' => null);
        }

        return array('reference_id' => 0, 'source' => null);
    }

    /**
     * Guarantee that an outgoing admin email carries the reference token
     * needed for reply matching. Both admin send actions (action_admincustomsend
     * and action_adminsend) call this just before Helpers_Email::send_email().
     *
     * Why this is needed: the subject usually comes from a template with a
     * "[case_number]" placeholder we substitute, but in the custom flow the
     * admin can type their own subject (and a template may have no placeholder
     * at all). Without a marker, a reply lands in the inbox with no way to
     * route it back to the originating admin_request row.
     *
     * Strategy:
     *   - Body always gets a "Reference: ADM-<id>" footer (or HTML comment
     *     equivalent) when no marker is already present. The receive matcher
     *     scans both subject AND body, so the body footer alone is enough
     *     to recover the reply when the subject can't carry the token —
     *     e.g. Ufone enforces a strict standard subject format that we
     *     cannot append to.
     *   - Subject gets "[ADM-<id>]" appended ONLY when the caller passes
     *     $inject_subject = true AND no marker is already there. Callers
     *     sending to telcos with locked subject formats (Ufone, company_id=3)
     *     pass false so the standard subject travels untouched.
     *
     * @param string $subject         The proposed outgoing subject.
     * @param string $body            The proposed outgoing body (HTML or text).
     * @param int    $reference_id    Numeric admin_request.reference_id.
     * @param bool   $inject_subject  Allow appending '[ADM-<id>]' to subject
     *                                when no marker is present. Pass false
     *                                for Ufone & similar strict-format telcos.
     * @return array [string $subject, string $body] with the body marker
     *               guaranteed and the subject marker guaranteed iff allowed.
     */
    public static function ensure_admin_reference_token($subject, $body, $reference_id, $inject_subject = true)
    {
        $reference_id = (int) $reference_id;
        if ($reference_id < 1) {
            return array((string) $subject, (string) $body);
        }

        $token = 'ADM-' . $reference_id;
        $subject = (string) $subject;
        $body    = (string) $body;

        // Subject — caller-controlled. Skipped entirely for telcos that
        // enforce a strict subject format (Ufone). Otherwise we append
        // "[ADM-<id>]" only when no admin token already exists.
        if ($inject_subject && !preg_match('/\b(?:ADM|QRM)[-\s]*\d{4,}/i', $subject)) {
            $subject = trim($subject);
            $subject .= ($subject === '' ? '' : ' ') . '[' . $token . ']';
        }

        // Body — always inject when no marker is present. The receive matcher
        // scans subject + body, so this guarantees a route back to the
        // admin_request row even when the subject couldn't carry the token.
        if (!preg_match('/\b(?:ADM|QRM)[-\s]*\d{4,}/i', $body)) {
            $sep = (strpos($body, '<') !== false) ? '<br><br>' : "\n\n";
            $body .= $sep . 'Reference: ' . $token;
        }

        return array($subject, $body);
    }

    public static function change_status_raw($file_name, $body_raw, $body, $messge_id, $request_id, $is_file_exist = NULL, $process_index = Null, $source = 'user')
    {
        if (empty($process_index))
            $process_index = 4;

        // Source-aware: admin custom requests live in admin_request /
        // admin_email_messages, regular ones in user_request / email_messages.
        $request_table = ($source === 'admin') ? 'admin_request'        : 'user_request';
        $message_table = ($source === 'admin') ? 'admin_email_messages' : 'email_messages';

        try {
            // Log status change attempt
            Model_ErrorLog::log(
                'change_status_raw',
                'Attempting to update request status',
                [
                    'request_id' => $request_id,
                    'message_id' => $messge_id,
                    'process_index' => $process_index,
                    'file_name' => $file_name,
                    'is_file_exist' => $is_file_exist
                ],
                null,
                'status_update_attempt',
                'email_processing',
                'info'
            );

            $DB = Database::instance();
            $date = date('Y-m-d H:i:s');
            DB::update($request_table)->set(array('status' => 2, 'processing_index' => $process_index))->where('request_id', '=', $request_id)->execute();
            DB::update($message_table)->set(array('received_date' => $date, 'received_file_path' => $file_name, 'received_body_raw' => $body_raw, 'received_body' => $body))->where('message_id', '=', $messge_id)->execute();

            // Admin replies can carry attachments too (e.g. attached XLSX of
            // subscriber data), so update the files row for both sources.
            if ($is_file_exist == 1 && !empty($file_name) && $file_name != 'na')
                DB::update("files")->set(array('file' => $file_name))->where('request_id', '=', $request_id)->execute();

            // Log success
            Model_ErrorLog::log(
                'change_status_raw',
                'Request status updated successfully',
                [
                    'request_id' => $request_id,
                    'message_id' => $messge_id,
                    'process_index' => $process_index
                ],
                null,
                'status_update_success',
                'email_processing',
                'success'
            );
            
        } catch (Exception $e) {
            // Log failure
            Model_ErrorLog::log(
                'change_status_raw',
                'Failed to update request status: ' . $e->getMessage(),
                [
                    'request_id' => $request_id,
                    'message_id' => $messge_id,
                    'process_index' => $process_index,
                    'error' => $e->getMessage()
                ],
                $e->getTraceAsString(),
                'status_update_failure',
                'email_processing',
                'error'
            );
            error_log("[" . date('c') . "] change_status_raw FAILED for request_id=$request_id: " . $e->getMessage());
            throw $e;
        }
    }

    public static function change_status($file_name, $body, $messge_id, $request_id)
    {
        $DB = Database::instance();
        $date = date('Y-m-d H:i:s');
        DB::update("user_request")->set(array('status' => 2, 'processing_index' => 4))->where('request_id', '=', $request_id)->execute();
        DB::update("email_messages")->set(array('received_date' => $date, 'received_file_path' => $file_name, 'received_body' => $body))->where('message_id', '=', $messge_id)->execute();

    }

    //check in blocked number list
    public static function check_in_blocked_number_list($request)
    {
        $DB = Database::instance();
        $sql = "SELECT * 
                FROM blocked_numbers 
                WHERE blocked_value = '{$request}' ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;

    }

    public static function check_old_familytree_request($cnic)
    {
        $DB = Database::instance();
        $sql = "SELECT request_id "
            . "FROM user_request as t1 "
            . "WHERE (t1.user_request_type_id = 10 AND t1.status = 2 AND t1.requested_value = '{$cnic}') ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $request_id = isset($results->request_id) && !empty($results->request_id) ? $results->request_id : 0;
        return $request_id;

    }

    public static function check_old_travelhistory_request($cnic)
    {
        $DB = Database::instance();
        $sql = "SELECT count(request_id) as cnt FROM user_request as t1
                WHERE (t1.user_request_type_id = 12 AND t1.requested_value = '{$cnic}') ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $request_count = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        return $request_count;

    }

    //get request send permission for sub
    public static function get_sub_request_permission($requesttype, $mnc, $msisdn, $date)
    {
        $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype} AND requested_value='{$msisdn}' AND company_name = {$mnc} AND created_at > '{$date}'";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $per = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if ($per > 0) {
            $per = 1;
        }
        return $per;
    }

    //get request send permission for location
    public static function get_location_request_permission($requesttype, $msisdn, $date)
    {
        $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype} AND requested_value='{$msisdn}'
                AND created_at > '{$date}' AND (status=1 OR status=0 OR processing_index=4) ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $per = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if ($per > 0) {
            $per = 1;
        }
        return $per;

    }

    //get request send permission for international number
    public static function get_cdrint_request_permission($requesttype, $msisdn)
    {
        $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype} AND requested_value='{$msisdn}'
                AND (status=1 OR status=0 OR processing_index=4) ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $per = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if ($per > 0) {
            $per = 1;
        }
        return $per;
    }

    //get request send permission for ptcl sub
    public static function get_subptcl_request_permission($requesttype, $msisdn, $date)
    {
        $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype} AND requested_value='{$msisdn}'
                AND created_at > '{$date}' AND (status=1 OR status=0 OR processing_index=4) ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $per = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if ($per > 0) {
            $per = 1;
        }
        return $per;
    }

    //get request send permission for cdr against imei
    public static function get_cdr_against_imei_request_permission($requesttype, $imei, $date)
    {
        $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype} AND requested_value='{$imei}'AND created_at > '{$date}' AND (status=1 OR status=0 OR processing_index=4) ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $per = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if ($per > 0) {
            $per = 1;
        }
        return $per;

    }

    //get request send permission for cdr against imei and cnic
    public static function get_mnc_of_request_in_queue($requesttype, $requested_value, $date)
    {
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
    public static function get_sims_against_cnic_request_permission($requesttype, $mnc, $cnic, $date)
    {
        $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype} AND requested_value='{$cnic}' AND company_name IN ({$mnc}) AND created_at > '{$date}'";

        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $per = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if ($per > 0) {
            $per = 1;
        }
        return $per;

    }

    //get request send permission for verisysrequest
    public static function get_verisys_request_permission($requesttype, $cnic)
    {
        $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype} AND requested_value='{$cnic}'
                AND (status=1 OR status=0 OR processing_index=4) ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $per = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if ($per > 0) {


            $per = 1;
        }
        return $per;
    }

    //check any type of request in queue by request type main requested value
    public static function check_request_in_queue_status($requesttype, $requested_value)
    {
        $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM user_request 
                WHERE user_request_type_id = {$requesttype}
                AND requested_value='{$requested_value}'
                AND (status=1 OR status=0 OR processing_index=4) ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $request_count = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if ($request_count > 0) {
            $request_count = 1;
        }
        return $request_count;
    }

    //check branchless banking request in queue
    public static function request_in_queue_branchlessbanking($requested_value)
    {
        $DB = Database::instance();
        $sql = "SELECT count(user_request_type_id) as cnt
                FROM ctfu_user_request 
                WHERE requested_value = {$requested_value}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $request_count = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        if ($request_count > 0) {
            $request_count = 1;
        }
        return $request_count;
    }

    //check any type of request in last days (date) with requested value and type
    public static function check_old_request_with_date($requesttype, $requested_value, $date)
    {
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
        if ($request_count > 0) {
            $request_count = 1;
        }
        return $request_count;
    }

    //get telco emails
    public static function get_telco_emails()
    {
        $DB = Database::instance();
        $sql = "SELECT *
                FROM telco_emails 
                WHERE 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        return $results;

    }

    /* Update email read status table */
    public static function emailreadstatus($request_id)
    {
        $DB = Database::instance();
        $sql = "SELECT id FROM email_read_status WHERE request_id = {$request_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();

        if (empty($results->id)) {
            $query = DB::insert('email_read_status', array('request_id', 'is_read'))
                ->values(array($request_id, 0))
                ->execute();

        } else {
            $query = DB::update('email_read_status')->set(array('is_read' => 0))
                ->where('id', '=', $results->id)
                ->execute();
        }
    }

    public static function emailreadstatuscheckUpdate($email_id, $request_id)
    {

        if (substr($request_id, 0, 2) === "92" && strlen($request_id) == 12) {
            $DB = Database::instance();
            $str2 = substr($request_id, 2);
            $sql = "select reference_id from user_request ur 
                    where requested_value = {$str2} and status = 1 and ur.user_request_type_id IN (3,4)";
            // and request_type in (1,2,3);
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();

            if (!empty($results->reference_id))
                $request_id = $results->reference_id;

        }

        $query = DB::update('email_read_status')->set(array('is_read' => 1, 'gmail_id' => $email_id))
            ->where('request_id', '=', $request_id);
        $query->execute();

        return $request_id;
    }

    public static function emailreadstatuscheck($email_id)
    {
        $DB = Database::instance();
        $sql = "SELECT id FROM email_read_status WHERE gmail_id = {$email_id} and is_read = 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();

        if (empty($results->id)) {
            return '';
        } else {
            return $results->id;
        }
    }

    public static function receive_single_email($request_id)
    {
        $is_file_exist = '';
        $DB = Database::instance();
        $sql = "SELECT gmail_id FROM email_read_status WHERE request_id = {$request_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $file_id = Helpers_Upload::get_fileid_with_requestid($request_id);
        if (!empty($results->gmail_id)) {
            $email_number = $results->gmail_id;
            $filename = '';
            $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
            include 'gmail/receiving.inc';
            // Log IMAP connection attempt
            $inbox = imap_open($hostname, $username, $password);
            
            if (!$inbox) {
                // Log IMAP connection failure
                $error = imap_last_error();
                Model_ErrorLog::log(
                    'get_email_status',
                    'IMAP connection failed: ' . $error,
                    array(
                        'username' => $username,
                        'hostname' => $hostname,
                        'gmail_id' => $email_number,
                        'error' => $error
                    ),
                    null,
                    'imap_connection_failure',
                    'email_status_check',
                    'error'
                );
                error_log("[" . date('c') . "] get_email_status: IMAP connection FAILED for $username: " . $error);
                die('Cannot connect to Gmail: ' . $error);
            }
            $output = '';
           // $headerInfo = imap_headerinfo($inbox, $email_number);
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

                $part = !empty($structure->parts[1]) ? $structure->parts[1] : '';
                if ($members['company_name'] == 13 || $members['company_name'] == 12 || $members['company_name'] == 11 || $members['company_name'] == 4 || $members['company_name'] == 3) {
                    $message_raw = imap_fetchbody($inbox, $email_number, 2);
                    if (!empty($part) && $part->encoding == 3) {
                        $message_raw = imap_base64($message_raw);
                    } else if (!empty($part) && $part->encoding == 1) {
                        $message_raw = imap_8bit($message_raw);
                    } else {
                        $message_raw = imap_qprint($message_raw);
                    }
                } elseif ($members['company_name'] == 6)
                    $message_raw = imap_base64(imap_fetchbody($inbox, $email_number, 1));
                else
                    $message_raw = imap_fetchbody($inbox, $email_number, 1);


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
                            } /* 4 = QUOTED-PRINTABLE encoding */ elseif ($structure->parts[$i]->encoding == 4) {
                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                            }
                        }
                    }
                }
                $filename = '';

                foreach ($attachments as $attachment) {
                    if (!empty($file_id)) {
                        $is_file_exist = 1;
                        $file_path = !empty($file_id) ? Helpers_Upload::get_request_data_path($file_id, 'save') : '';
                    } else {
                        $is_file_exist = 0;
                        $file_id = Helpers_Utilities::id_generator("file_id");
                        $file_path = !empty($file_id) ? Helpers_Upload::get_request_data_path($file_id, 'save') : '';
                    }
                    if ($attachment['is_attachment'] == 1) {
                        $filename = $attachment['name'];
                        $new_file_info = PATHINFO($attachment['filename']);
                        if (!empty($filename)) {
                            $filename = 'rqt' . $members['request_id'] . 'fid' . $file_id . '.' . $new_file_info['extension'];
                        } else {
                            $filename = 'rqt' . $members['request_id'] . 'fid' . $file_id . '.' . $new_file_info['extension'];
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


                if (!empty($file_id) && $file_name != 'na' && !empty($is_file_exist) && $is_file_exist == 0)
                    Helpers_Upload::insert_file_record($file_name, $members['user_id'], $members['user_request_type_id'], $members['company_name'], $members['requested_value'], $members['request_id'], $members['reason'], $file_id);
                else
                    Helpers_Email::change_status_raw($file_name, $body_raw, $body, $members['message_id'], $members['request_id'], $is_file_exist);
            }
            imap_close($inbox);
        }
    }

    public static function email_status($reference_number, $status, $process_status)
    {
        $query = DB::update('user_request')->set(array('status' => $status, 'processing_index' => $process_status))
            ->where('request_id', '=', $reference_number)
            ->execute();
        return $query;

    }
}

?>
