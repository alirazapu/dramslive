<?php

$call_execute = 0;
$sms_execute = 0;
ini_set('memory_limit', '19999999990024M');
ini_set('wait_timeout', '19999999990024');

// Validate we have enough data
if (sizeof($data) < 2) {
    if (!empty($userrequestid)) {
        Model_ErrorLog::log(
            'parse_partially_zong',
            'Insufficient data rows - file must have at least 1 header row and 1 data row',
            array(
                'request_id' => $userrequestid,
                'file_id' => $file_id,
                'row_count' => sizeof($data)
            ),
            null,
            'validation_error',
            'cdr_partial_parsing'
        );
        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
    }
    Model_Generic::update_file_status_val($file_id, 0);
    echo '5';
    exit;
}

// Start from row 1 (0-indexed, so second row after header)
$start_row = 1;

// Get total records (excluding header)
$total_record = sizeof($data) - 1;

// Validate indices are set
if ($party_aindex === false || $imei_index === false || $date_index === false) {
    if (!empty($userrequestid)) {
        Model_ErrorLog::log(
            'parse_partially_zong',
            'Required column indices not found in header',
            array(
                'request_id' => $userrequestid,
                'file_id' => $file_id,
                'party_aindex' => $party_aindex,
                'imei_index' => $imei_index,
                'date_index' => $date_index
            ),
            null,
            'validation_error',
            'cdr_partial_parsing'
        );
        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
    }
    Model_Generic::update_file_status_val($file_id, 0);
    echo '6';
    exit;
}

// Validate first data row has required values
if (empty($data[$start_row][$imei_index]) || empty($data[$start_row][$party_aindex])) {
    if (!empty($userrequestid)) {
        Model_ErrorLog::log(
            'parse_partially_zong',
            'First data row missing required values',
            array(
                'request_id' => $userrequestid,
                'file_id' => $file_id,
                'imei_value' => isset($data[$start_row][$imei_index]) ? $data[$start_row][$imei_index] : 'MISSING',
                'mobile_value' => isset($data[$start_row][$party_aindex]) ? $data[$start_row][$party_aindex] : 'MISSING'
            ),
            null,
            'validation_error',
            'cdr_partial_parsing'
        );
        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
    }
    Model_Generic::update_file_status_val($file_id, 0);
    echo '7';
    exit;
}

// Get date from first data row
$date_check = ($data[$start_row][$date_index]);

// Handle both Unix timestamp and Excel date formats
if (is_numeric($date_check) && $date_check > 25569) {
    $from = date('Y-m-d H:i:s', PHPExcel_Shared_Date::ExcelToPHP($date_check));
} elseif (is_numeric($date_check)) {
    $from = date('Y-m-d H:i:s', $date_check);
} else {
    $from = date('Y-m-d H:i:s', strtotime($date_check));
}

$to = $from;

/* Company MNC */
$company_mnc = 4;
$upload_status = 1;
$request_type = 2;

// Get party A (mobile number) - handle various formats
$party_a_raw = trim($data[$start_row][$party_aindex]);
$party_a_clean = preg_replace('/[^0-9+]/', '', $party_a_raw);
$party_a_clean = preg_replace('/^(\+92|92|0)/', '', $party_a_clean);
$party_a = substr($party_a_clean, -10);

// ===== IMPROVED IMEI READING =====
// Get raw IMEI value
$imei_raw = $data[$start_row][$imei_index];

// Log raw value for debugging
if (!empty($file_id)) {
    Model_ErrorLog::log(
        'parse_partially_zong_debug',
        'Raw IMEI value from Excel',
        array(
            'file_id' => $file_id,
            'imei_raw' => $imei_raw,
            'imei_type' => gettype($imei_raw),
            'input_imei' => $field_imei_no
        ),
        null,
        'debug',
        'cdr_partial_parsing'
    );
}

// Handle IMEI - convert from scientific notation if needed
if (is_numeric($imei_raw)) {
    // Convert to string with full precision
    $imei = sprintf('%.0f', $imei_raw);
} else {
    // Already a string, just clean it
    $imei = preg_replace('/[^0-9]/', '', trim($imei_raw));
}

// Ensure IMEI is exactly 15 digits
if (strlen($imei) < 15) {
    $imei = str_pad($imei, 15, '0', STR_PAD_RIGHT);
} elseif (strlen($imei) > 15) {
    $imei = substr($imei, 0, 15);
}

// Get IMSI if available
$imsi_number = isset($imsi_index) && $imsi_index !== false && !empty($data[$start_row][$imsi_index])
    ? $data[$start_row][$imsi_index]
    : '';

// Get user ID
if (!empty(Auth::instance()->get_user())) {
    $user_id = Auth::instance()->get_user()->id;
} else {
    $user_id = 9999;
}

// Log processing start
if (!empty($file_id)) {
    Model_ErrorLog::log(
        'parse_partially_zong_start',
        'Starting Zong partial CDR processing',
        array(
            'file_id' => $file_id,
            'request_id' => !empty($userrequestid) ? $userrequestid : null,
            'total_records' => $total_record,
            'input_imei' => $field_imei_no,
            'file_imei' => $imei,
            'imei_raw_value' => $imei_raw,
            'mobile_number' => $party_a,
            'from_date' => $from
        ),
        null,
        'info',
        'cdr_partial_parsing'
    );
}

/*
   Compare IMEIs - use first 14 digits for comparison
   because Excel may lose precision on the 15th digit
 */
$field_imei_compare = substr($field_imei_no, 0, 14);
$file_imei_compare = substr($imei, 0, 14);

if ($field_imei_compare == $file_imei_compare) {
    // IMEIs match (at least first 14 digits)
    // Use the input IMEI as the authoritative version
    $imei = $field_imei_no;

    /* Update file record */
    Model_Parse::update_file_against_phone($file_id, $imei, $total_record, $from, $to, $upload_status, $request_type);

    /* 4. Get device id from Person_phone_device */
    $device_id = Model_Parse::check_imei_number($imei);
    if (empty($device_id)) {
        /* If not exist new entry without person_id  */
        $device_id = Model_Shortparse::insert_person_phone_device($imei, $from, $user_id);
    }

    /* 5. Process phone number if available */
    if (!empty($party_a) && strlen($party_a) == 10) {
        /* 6. Change flag is_active to 0 in person_device_number against mobile number  */
        Model_Shortparse::update_person_device_number_change_status($party_a);

        /* 7-8. Update or insert person_device_number */
        Model_Shortparse::updateorinsert_person_device_number_withfirstuse($device_id, $party_a, $from, $to);

        /* 9. Update or insert person_phone_number */
        Model_Shortparse::updateorinsert_person_phone_number_withfirstuse($party_a, $imsi_number, $from, $company_mnc, 1, $user_id, $to);
    }

    /* Process remaining rows */
    $processed_rows = 1;
    if (sizeof($data) > ($start_row + 1)) {
        for ($row = ($start_row + 1); $row < sizeof($data); $row++) {

            // Skip empty rows
            if (empty($data[$row]) || empty($data[$row][$imei_index])) {
                continue;
            }

            // Get IMEI for current row
            $imei_raw_new = $data[$row][$imei_index];

            if (is_numeric($imei_raw_new)) {
                $imei_new = sprintf('%.0f', $imei_raw_new);
            } else {
                $imei_new = preg_replace('/[^0-9]/', '', trim($imei_raw_new));
            }

            if (strlen($imei_new) < 15) {
                $imei_new = str_pad($imei_new, 15, '0', STR_PAD_RIGHT);
            } elseif (strlen($imei_new) > 15) {
                $imei_new = substr($imei_new, 0, 15);
            }

            // Compare first 14 digits
            $file_imei_compare_new = substr($imei_new, 0, 14);

            if ($field_imei_compare != $file_imei_compare_new) {
                if (!empty($userrequestid)) {
                    Model_ErrorLog::log(
                        'parse_partially_zong',
                        'IMEI mismatch detected during CDR parsing - expected IMEI does not match IMEI in row ' . ($row + 1),
                        array(
                            'request_id' => $userrequestid,
                            'processing_index' => 3,
                            'file_id' => $file_id,
                            'expected_imei' => $field_imei_no,
                            'expected_imei_14' => $field_imei_compare,
                            'found_imei' => $imei_new,
                            'found_imei_14' => $file_imei_compare_new,
                            'row_number' => ($row + 1),
                            'processed_rows' => $processed_rows
                        ),
                        null,
                        'validation_error',
                        'cdr_partial_parsing'
                    );
                    $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                }
                Model_Generic::update_file_status_val($file_id, 0);
                echo '4';
                exit;
            }

            // Get date for current row
            $date_check = ($data[$row][$date_index]);

            // Handle date formats
            if (is_numeric($date_check) && $date_check > 25569) {
                $from = date('Y-m-d H:i:s', PHPExcel_Shared_Date::ExcelToPHP($date_check));
            } elseif (is_numeric($date_check)) {
                $from = date('Y-m-d H:i:s', $date_check);
            } else {
                $from = date('Y-m-d H:i:s', strtotime($date_check));
            }
            $to = $from;

            // Get previous row date
            $past_row = $row - 1;
            $date_check_1 = ($data[$past_row][$date_index]);

            if (is_numeric($date_check_1) && $date_check_1 > 25569) {
                $to_old = date('Y-m-d H:i:s', PHPExcel_Shared_Date::ExcelToPHP($date_check_1));
            } elseif (is_numeric($date_check_1)) {
                $to_old = date('Y-m-d H:i:s', $date_check_1);
            } else {
                $to_old = date('Y-m-d H:i:s', strtotime($date_check_1));
            }

            $imsi_number = isset($imsi_index) && $imsi_index !== false && !empty($data[$row][$imsi_index])
                ? $data[$row][$imsi_index]
                : '';

            // Check if mobile number changed
            $current_party_a_raw = trim($data[$row][$party_aindex]);
            $current_party_a_clean = preg_replace('/[^0-9+]/', '', $current_party_a_raw);
            $current_party_a_clean = preg_replace('/^(\+92|92|0)/', '', $current_party_a_clean);
            $current_party_a = substr($current_party_a_clean, -10);

            if ($party_a != $current_party_a) {
                /* Update last use for previous number */
                if (!empty($party_a) && strlen($party_a) == 10) {
                    Model_Shortparse::updateorinsert_person_device_number_withlastuse($device_id, $party_a, $to_old);
                    Model_Shortparse::updateorinsert_person_phone_number_withfirstuse($party_a, $imsi_number, $to_old, $company_mnc, 1, $user_id);
                }

                $party_a = $current_party_a;

                if (!empty($party_a) && strlen($party_a) == 10) {
                    /* Process new number */
                    Model_Shortparse::update_person_device_number_change_status($party_a);
                    Model_Shortparse::updateorinsert_person_device_number_withfirstuse($device_id, $party_a, $from, $to);
                    Model_Shortparse::updateorinsert_person_phone_number_withfirstuse($party_a, $imsi_number, $from, $company_mnc, 1, $user_id, $to);
                }
            }

            $processed_rows++;
        }
    }

    // Log success
    if (!empty($file_id)) {
        Model_ErrorLog::log(
            'parse_partially_zong_success',
            'Zong partial CDR processing completed successfully',
            array(
                'file_id' => $file_id,
                'request_id' => !empty($userrequestid) ? $userrequestid : null,
                'total_records' => $total_record,
                'processed_rows' => $processed_rows,
                'imei' => $imei
            ),
            null,
            'info',
            'cdr_partial_parsing'
        );
    }

    if (!empty($userrequestid)) {
        $reference_number = Model_Email::email_status($userrequestid, 2, 5);
    }
    Model_Generic::update_file_status_val($file_id, 1);
    echo '1';
    exit;
} else {
    if (!empty($userrequestid)) {
        Model_ErrorLog::log(
            'parse_partially_zong',
            'Initial IMEI validation failed - input IMEI does not match first CDR record IMEI',
            array(
                'request_id' => $userrequestid,
                'processing_index' => 3,
                'file_id' => $file_id,
                'input_imei' => $field_imei_no,
                'input_imei_14' => $field_imei_compare,
                'cdr_imei' => $imei,
                'cdr_imei_14' => $file_imei_compare,
                'cdr_imei_raw' => $imei_raw
            ),
            null,
            'validation_error',
            'cdr_partial_parsing'
        );
        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
    }
    Model_Generic::update_file_status_val($file_id, 0);
    echo '2';
    exit;
}