<?php
$data['received_body'] = array_filter(explode('From:', strip_tags($data['received_body'])));

if ($data['received_body'][0] == 'na')
    $data['received_body'] = array_filter(explode('From:', strip_tags($data['received_body_raw'])));

include 'notfound.inc';
if ($not_fount != 1) {


    $data['received_body'] = str_replace('_', '', $data['received_body'][0]);
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
            $i = $i + 1;  // ← was missing increment in original → bug

            if (strpos($parse_val[$i] ?? '', '/') !== false) {

                $parse_val[$i] = str_replace("'", ' ', $parse_val[$i] ?? '');

                $strhlp = explode(" ", trim($parse_val[$i] ?? ''));

                if (count($strhlp) >= 2) {
                    $date_part = $strhlp[0];           // e.g. "2024-04-22"
                    $time_part = $strhlp[1] ?? '';     // optional

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
$status = (!empty($status) && $active == 'Active') ? 1 : 0;

?>