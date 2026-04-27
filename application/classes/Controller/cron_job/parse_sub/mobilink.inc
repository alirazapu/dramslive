<?php
$data['received_body'] = array_filter(explode('From:', strip_tags($data['received_body'])));

if ($data['received_body'][0] == 'na')
    $data['received_body'] = array_filter(explode('From:', strip_tags($data['received_body_raw'])));

include 'notfound.inc';
if ($not_fount != 1) {


    $data['received_body'] = str_replace('_', '', $data['received_body'][0]);
    // Decode quoted-printable soft line breaks (=CRLF / =LF means line continuation)
    $data['received_body'] = preg_replace('/=\r?\n\s*/', '', $data['received_body']);
    $data['received_body'] = preg_replace("/\r|\n/", "", $data['received_body']);

    $data['received_body'] = str_replace('*', '', $data['received_body']);
    $data['received_body'] = str_replace('=', '', $data['received_body']);
    $data['received_body'] = str_replace('P/O', ' PO Box', $data['received_body']);
    $data['received_body'] = str_replace(' / ', ' ', $data['received_body']);
    $data['received_body'] = str_replace('.', '', $data['received_body']);
    $data['received_body'] = str_replace('ID', '', $data['received_body']);
    $data['received_body'] = str_replace(PHP_EOL, ' ', $data['received_body']);
    $parse_val = array_filter(explode(',', strip_tags($data['received_body'])));
    $parse_final = array();
    foreach ($parse_val as $key => $value) {
        if (!empty($parse_val[$key]) && strlen(trim($parse_val[$key])) > 2) {
            $parse_final[] = trim($parse_val[$key]);
        }
    }
    $parse_val = $parse_final;


    /** Block Number */
    $block_reason = preg_split('/[^a-zA-Z]/', $parse_val[2]);


    if (strpos(strtoupper($block_reason[0]), 'UAO') !== false) {
        $detail = implode(", ", $parse_val);
        $block_phone = new Model_Userrequest;
        $data['numbertype'] = 1;
        $data['numbervalue'] = trim($parse_val[1]);
        $data['blockreason'] = strtoupper($block_reason[0]);
        $data['blockdetails'] = $detail;
        //  $block_phone->add_blocked_number($data, 999);
        $not_fount = 1;
        $reference_number = $data['request_id'];
        
        Model_ErrorLog::log(
            'parse_sub_mobilink',
            'UAO block reason detected - marking as status 5 (Not Found)',
            [
                'request_id' => $reference_number,
                'company_name' => $data['company_name'] ?? 'mobilink',
                'user_request_type_id' => $data['user_request_type_id'] ?? null,
                'processing_index' => 5,
                'block_reason' => strtoupper($block_reason[0]),
                'block_details' => $detail,
                'reason' => 'Number contains UAO (Unauthorized) block reason'
            ],
            null,
            'not_found',
            'subscriber_parsing','success'
        );
        
        $reference_number = Model_Email::email_status($reference_number, 2, 5);
    } else {

        // Detect new format: response starts directly with the mobile number (digits only).
        // Old format: [status/active], [mobile], [name], [CNIC], [address], [date]
        // New format: [mobile], [name], [CNIC], [address], [ISO-DOB + response info]
        $first_field = trim($parse_val[0] ?? '');
        $is_new_format = ctype_digit($first_field) && strlen($first_field) >= 10 && strlen($first_field) <= 13;

        if ($is_new_format) {

            // Field 0: mobile number
            $mobile_number = substr($first_field, -10);

            // Field 1: subscriber name
            $name = trim($parse_val[1] ?? '');

            // Field 2: CNIC (13-digit numeric or alphanumeric for foreigners)
            $cnic_field = str_replace('-', '', trim($parse_val[2] ?? ''));
            if (!ctype_digit($cnic_field) && strlen($cnic_field) == 13) {
                $cnic_original = $cnic_field;
                $cnic = $cnic_field;
                $is_foreigner = 1;
            } elseif (ctype_digit($cnic_field) && strlen($cnic_field) == 13) {
                $cnic = $cnic_field;
            }

            // Field 3: address — but the new Mobilink format does NOT put a
            // comma between the address and the DOB+metadata trailer, so the
            // whole tail (ISO DOB, email send-time, sender email, originating
            // MSISDN, FIR id) gets concatenated onto the address. Example bad
            // input:
            //   "Khyber Agency Pakistan 2014-05-11t00:00:00000+05:0004/24/2026
            //    2:14:16 Amdcs@ctkpmailcom923045242005fir 1039508"
            // Strategy: pull the DOB out first, then chop everything from the
            // first "trailer marker" onwards.
            $raw_field3 = trim($parse_val[3] ?? '');

            // Extract DOB if it's hidden inside the address trailer.
            // The earlier str_replace('.', '', …) at line 20 has already removed
            // the milliseconds dot, so the pattern is YYYY-M[M]-D[D]T (case-insensitive).
            $dob_pattern = '/(\d{4})-(\d{1,2})-(\d{1,2})[Tt]/';
            if (empty($date) && preg_match($dob_pattern, $raw_field3, $dm_match)) {
                $dy = (int) $dm_match[1];
                $dm = (int) $dm_match[2];
                $dd = (int) $dm_match[3];
                if ($dy >= 1900 && $dy <= 2100 && $dm >= 1 && $dm <= 12
                    && $dd >= 1 && $dd <= 31 && checkdate($dm, $dd, $dy)) {
                    $date = sprintf('%04d-%02d-%02d', $dy, $dm, $dd);
                }
            }

            // Strip the trailer. Markers we cut from:
            //   - ISO timestamp:    "YYYY-M[M]-D[D]T..."
            //   - US date+time:     "MM/DD/YYYY HH:MM[:SS] [AM|PM]..."
            // The leading \s* swallows the space that separates the address
            // from the trailer. The .*$ greedily eats everything after.
            $trailer_pattern = '/\s*(?:\d{4}-\d{1,2}-\d{1,2}[Tt]|\d{1,2}\/\d{1,2}\/\d{4}\s+\d{1,2}:\d{2}).*$/';
            $address = trim(preg_replace($trailer_pattern, '', $raw_field3));

            // Field 4 fallback: if no date was extracted above and a separate
            // field 4 exists, try the original field-4 ISO-date logic.
            $dob_field = trim($parse_val[4] ?? '');
            if (empty($date) && !empty($dob_field)) {
                $iso_raw = explode(' ', $dob_field)[0];
                $iso_date_only = (strpos($iso_raw, 'T') !== false || strpos($iso_raw, 't') !== false)
                    ? preg_split('/[Tt]/', $iso_raw, 2)[0]
                    : $iso_raw;
                $dp = explode('-', $iso_date_only);
                if (count($dp) === 3) {
                    $dy = (int)$dp[0];
                    $dm = (int)$dp[1];
                    $dd = (int)$dp[2];
                    if ($dy >= 1900 && $dy <= 2100 && $dm >= 1 && $dm <= 12
                        && $dd >= 1 && $dd <= 31 && checkdate($dm, $dd, $dy)) {
                        $date = $iso_date_only;
                    }
                }
            }

        } else {

            // Old format: [status/active], [mobile], [name], [CNIC], ...
            $i = 0;
            if (strpos(strtoupper($parse_val[$i]), 'ACTIVE') !== false) {
                $active = 'Active';
            } else {
                $active = trim($parse_val[$i]);
            }


            $i += 1;
            $mobile_number = substr(trim($parse_val[$i]), -10);


            $i += 1;
            $name = trim($parse_val[$i]);


            $i += 1;
            $parse_val[$i] = str_replace('-', '', $parse_val[$i]);


            if (!ctype_digit(trim($parse_val[$i])) && strlen(trim($parse_val[$i])) == 13) {
                $cnic_original = trim($parse_val[$i]);
                $cnic = trim($parse_val[$i]);
                //$cnic = Helpers_Utilities::check_digit(trim($parse_val[$i]));
                $is_foreigner = 1;
                $i += 1;
            } else {
                if (ctype_digit(trim($parse_val[$i])) && strlen(trim($parse_val[$i])) == 13) {
                    $cnic = trim($parse_val[$i]);
                    $i += 1;
                } else {
                    $i += 1;
                    if (ctype_digit(trim($parse_val[$i]))) {
                        $cnic = trim($parse_val[$i]);
                        $i += 1;
                    }
                }
            }


            if (empty($cnic)) {

                foreach ($parse_val as $cnicval) {

                    if (!ctype_digit(trim($cnicval)) && strlen(trim($cnicval)) == 13) {
                        $cnic_original = trim($cnicval);
                        $cnic = trim($cnicval);
                        $is_foreigner = 1;
                        break;
                    } elseif (ctype_digit(trim($cnicval)) && strlen(trim($cnicval)) == 13) {
                        $cnic = trim($cnicval);
                        break;
                    }
                }
            }
            //echo ' cnic ' . $cnic_original;
            if (strpos($parse_val[$i], '/') !== false) {

            }


            if (strpos($parse_val[$i], '/') !== false && strpos($parse_val[$i], ':') !== false) {

                list($d, $m, $y) = explode("/", $parse_val[$i]);
                $yy = explode(" ", $y);
                $y = $yy[0];

                if (checkdate($m, $d, $y)) {
                    $date = trim($parse_val[$i]);
                    $i += 1;

                } else {

                    $status = trim($parse_val[$i]);
                    $i += 1;
                    $date = trim($parse_val[$i]);
                    $i += 1;
                }
            } elseif ((strpos($parse_val[$i], '-') !== false) && (strpos($parse_val[$i], ':') !== false)) {

                list($d, $m, $y) = explode("-", $parse_val[$i]);
                $yy = explode(" ", $y);
                $y = $yy[0];
                if (checkdate($m, $d, $y)) {
                    $date = trim($parse_val[$i]);
                    $i += 1;

                } else {

                    $status = trim($parse_val[$i]);
                    $i += 1;
                    $date = trim($parse_val[$i]);
                    $i += 1;
                }
            } else {

                if (!empty($parse_val[$i])) {
                    $address = $parse_val[$i];
                }
                if (!empty($parse_val[$i + 1])) {
                    $address .= ' ' . $parse_val[$i + 1];
                }
                $i = $i + 1;

                if (strpos($parse_val[$i] ?? '', '/') !== false) {

                    $parse_val[$i] = str_replace("'", ' ', $parse_val[$i] ?? '');

                    $strhlp = explode(" ", trim($parse_val[$i] ?? ''));

                    if (count($strhlp) >= 2) {
                        $date_part = $strhlp[0];           // e.g. "2024-04-22"
                        $time_part = $strhlp[1] ?? '';     // optional

                        // Strip ISO time component if present (e.g. "2011-10-06T00:00:00.000+05:00")
                        if (strpos($date_part, 'T') !== false) {
                            $date_part = explode('T', $date_part)[0];
                            $time_part = '';
                        }

                        $date_parts = explode("-", $date_part);

                        if (count($date_parts) === 3) {
                            $y = (int) trim($date_parts[0]);
                            $m = (int) trim($date_parts[1]);
                            $d = (int) trim($date_parts[2]);

                            if ($y >= 1900 && $y <= 2100 && $m >= 1 && $m <= 12 && $d >= 1 && $d <= 31) {
                                if (checkdate($m, $d, $y)) {
                                    $date = trim($date_part . ($time_part ? ' ' . $time_part : ''));
                                }
                            }
                        }
                    }
                }
            }

        }

    }


}
$status = (!empty($status) && $active == 'Active') ? 1 : 0;

?>