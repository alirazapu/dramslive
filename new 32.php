<?php 
	public static function receive_email($subject, $sender)
    {
        $processed = 0;
        $errors    = 0;

        $filename = '';
        $hostname = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
        if (!empty($sender) && $sender == 2) {
            $result = Helpers_Inneruse::get_gmail_pw();
            $username = $result['send']['user'];
            $password = $result['send']['password'];
            $since = date("D, d M Y", strtotime("-15 days"));
            $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());
            $emails = imap_search($inbox, 'UNSEEN');
        } else {
            include 'gmail/receiving.inc';
            //echo $username; exit;
            $since = date("D, d M Y", strtotime("-12 days")); /* added range */
            $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());
            $search_criteria= 'UNSEEN SINCE "' . $since . ' 00:00:00 -0700 (PDT)"';
            $emails = imap_search($inbox,$search_criteria);
        }
        //echo "<pre>";
        if ($emails === false || empty($emails)) {
            imap_close($inbox);
            return 1; // No emails found
        }

        /* newest first */
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
            $string_replace = str_replace("/", " /", $overview[0]->subject);
            $string_replace = str_replace(",", " ,", $string_replace);
            $string_replace = str_replace(".", " . ", $string_replace);
            $query_subject = explode(' ', $string_replace);
            $query_subject_final = '';
            preg_match_all('/\b\d+\b/', $string_replace, $matches);
            $query_subject_final = $matches[0][0] ?? '';
            if (empty($query_subject_final)){
                continue;
            }
            if (!empty($query_subject_final)) {
                $query_subject_final = Helpers_Email::emailreadstatuscheckUpdate($email_number, $query_subject_final);
                $sql = "SELECT request_id, reason, user_id, user_request_type_id, email_type_name, requested_value, concerned_person_id, t1.company_name,
                   created_at, status, processing_index, em.message_id, em.message_subject, em.sender_id FROM 
                   user_request as t1 
                   join email_templates_type as t2                                 
                   on t1.user_request_type_id = t2.id                       
                   join email_messages as em on em.message_id = t1.message_id   
                   and t1.user_request_type_id != 8
                   and t1.status = 1 and t1.processing_index = 0 
                   and t1.reference_id = {$query_subject_final};
                   ";

                $members = DB::query(Database::SELECT, $sql)->execute()->current();//->as_array();
            } else {
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
                    $file_id = Helpers_Upload::get_fileid_with_requestid($members['request_id'] ?? 0)
                        ?: Helpers_Utilities::id_generator("file_id");

                    $file_path = Helpers_Upload::get_request_data_path($file_id, 'save');

                    $extension = pathinfo($original_name, PATHINFO_EXTENSION) ?: 'bin';
                    $safe_filename = 'rqt' . $query_subject_final . 'fid' . $file_id . '.' . $extension;
                    $full_path = rtrim($file_path, '/\\') . DIRECTORY_SEPARATOR . $safe_filename;

                    // ──────────────────────────────────────────────
                    //          DEBUG OUTPUT – keep until fixed
                    // ──────────────────────────────────────────────
                    echo "<pre style='background:#f8f8f8; padding:10px; border:1px solid #ccc;'>";
                    echo "Attachment data length: " . (isset($attachment['attachment']) ? strlen($attachment['attachment']) : 'MISSING') . " bytes\n";
                    echo "Target directory:       " . htmlspecialchars($file_path) . "\n";
                    echo "Final file path:        " . htmlspecialchars($full_path) . "\n";
                    echo "query_subject_final:    " . htmlspecialchars($query_subject_final ?? '(not set)') . "\n";
                    echo "is_dir?                 " . (is_dir($file_path) ? 'YES' : 'NO') . "\n";
                    echo "is_writable?            " . (is_writable($file_path) ? 'YES' : 'NO') . "\n";
                    echo "</pre>";

                    // ──────────────────────────────────────────────
                    //          ACTUAL SAVE WITH ERROR CHECKS
                    // ──────────────────────────────────────────────
                    if (empty($attachment['attachment'])) {
                        echo "<div style='color:red; font-weight:bold;'>ERROR: Attachment data is empty / missing</div>";
                        error_log("Empty attachment data for file_id $file_id");
                        continue;
                    }

                    if (!is_dir($file_path) || !is_writable($file_path)) {
                        echo "<div style='color:red; font-weight:bold;'>ERROR: Directory missing or not writable → "
                            . htmlspecialchars($file_path) . "</div>";
                        error_log("Cannot save - dir issue: $file_path");
                        continue;
                    }

                    $fp = @fopen($full_path, 'wb');
                    if ($fp === false) {
                        $err = error_get_last();
                        echo "<div style='color:red; font-weight:bold;'>ERROR: Cannot open file for writing</div>";
                        echo "<pre>Error: " . htmlspecialchars($err['message'] ?? 'unknown') . "</pre>";
                        error_log("fopen failed: $full_path - " . ($err['message'] ?? 'no error info'));
                        continue;
                    }

                    $bytes = fwrite($fp, $attachment['attachment']);
                    fclose($fp);

                    if ($bytes === false || $bytes !== strlen($attachment['attachment'])) {
                        echo "<div style='color:orange; font-weight:bold;'>WARNING: Only wrote $bytes bytes (should be "
                            . strlen($attachment['attachment']) . ")</div>";
                        error_log("Partial write: $full_path - $bytes bytes");
                    } else {
                        echo "<div style='color:green; font-weight:bold;'>File written OK → $safe_filename ($bytes bytes)</div>";
                        // Optionally: chmod($full_path, 0644);
                    }
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
                if (!empty($file_id) && $file_name != 'na' && $is_file_exist == 0) {
                    Helpers_Upload::insert_file_record($file_name, $members['user_id'], $members['user_request_type_id'], $members['company_name'], $members['requested_value'], $members['request_id'], $members['reason'], $file_id);
                }
                Helpers_Email::change_status_raw($file_name, $body_raw, $body, $members['message_id'], $members['request_id'], $is_file_exist);
            }
            else {
                imap_clearflag_full($inbox, $email_number, '\\Seen');  //Seen
            }
        }
        imap_close($inbox);
        return 1;

    }
