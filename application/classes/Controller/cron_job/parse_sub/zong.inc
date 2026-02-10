<?php
// zong.inc - Fixed for foreigner CNIC formats (GV..., CH... etc.)
// Mobile + CNIC extraction for Zong table replies

$not_fount     = 0;
$mobile_number = '';
$name          = '';
$cnic          = '';
$cnic_original = '';
$is_foreigner  = 0;

// 1. Body selection
// 1. Body selection + clean (keep mostly as is, but add better From: handling)
$body = !empty($data['received_body_raw']) ? $data['received_body_raw'] : $data['received_body'];
if (empty(trim($body))) {
    $not_fount = 1;
    return;
}

$clean = strip_tags(html_entity_decode($body, ENT_QUOTES | ENT_HTML5));
$clean = preg_replace('/\s+/u', ' ', trim($clean));
$clean = str_ireplace(['CNIC','MSISDN','REG_NBR','CUST_NAME','-','&nbsp;','Sent:','To:','Subject:'], ' ', $clean);

// NEW: Split at first "From:" (case insensitive) — take only BEFORE part for reliable parsing
$parts = preg_split('/\s*from:\s*/i', $clean, 2);
$parse_area = !empty($parts[0]) ? trim($parts[0]) : $clean;   // everything before "From:"
$lower_area = strtolower($parse_area);

// 3. No-data check (keep as is, but use $lower_area)
$no_data_keywords = ['not found','no data','no record','nodata','nil','network error','ported out',"can't be fetched",'more than a year','extraction problem'];
foreach ($no_data_keywords as $kw) {
    if (strpos($lower_area, $kw) !== false) {
        $not_fount = 1;
        return;
    }
}

// 4–5. Mobile & CNIC extraction → keep as is (use full $clean for these)
preg_match_all('/\b3\d{9}\b/', $clean, $msisdn_matches);
$msisdn_cands = $msisdn_matches[0] ?? [];

preg_match_all('/\b[A-Za-z0-9]{13}\b/', $clean, $cnic_matches);
$cnic_cands = array_filter($cnic_matches[0], fn($s) => strlen($s) === 13);

// 6. Extraction logic (keep mobile + first CNIC)
if (!empty($msisdn_cands)) {
    $mobile_number = $msisdn_cands[0];
    if (!empty($cnic_cands)) {
        $cnic = $cnic_cands[0];
    }
}

// 7. Name extraction — now only from $parse_area (before "From:")
$name = '';
if ($cnic && strpos($parse_area, $cnic) !== false) {
    $pos = strpos($parse_area, $cnic);
    $after_cnic = substr($parse_area, $pos + 13, 120);  // look max ~120 chars after CNIC

    // Extract candidate name: letters + spaces, at least 4 chars, up to ~40
    if (preg_match('/^[\s,:;]*([A-Za-z\s]{4,40})(?=[A-Z0-9\s\.,;:]|\s*$)/', trim($after_cnic), $matches)) {
        $candidate = trim($matches[1]);

        // Block obvious junk / sender titles
        $junk_patterns = [
            '/\b(addl|additional|deputy|dsp|sp|ig|fia|police|investigation|officer|senior)\b/i',
            '/\b(dcs|ctkpmail|cmpak|regulatory|pta)\b/i'
        ];
        $is_junk = false;
        foreach ($junk_patterns as $pat) {
            if (preg_match($pat, $candidate)) {
                $is_junk = true;
                break;
            }
        }

        if (!$is_junk && strlen($candidate) >= 5) {
            $name = $candidate;
        }
    }
}

// Clear obvious junk name
if (strtolower($name) === 'addl ig' || empty(trim($name))) {
    $name = '';
}

// 8. Assign to correct fields + foreigner detection
if ($cnic) {
    $cnic_original = $cnic;
    $cnic_clean    = preg_replace('/\D/', '', $cnic);

    if (strlen($cnic) === 13 && !ctype_digit($cnic)) {
        // Foreigner (has letters)
        $is_foreigner = 1;
        $cnic_number = null;                    // or $cnic_clean if you want digits part
        $cnic_number_foreigner = $cnic_original;
    } else {
        // Pakistani
        $is_foreigner = 0;
        $cnic_number = (strlen($cnic_clean) === 13) ? $cnic_clean : null;
        $cnic_number_foreigner = null;
    }
}

// 9. Mobile final normalization
if ($mobile_number) {
    $mobile_number = preg_replace('/\D/', '', $mobile_number);

    if (strlen($mobile_number) === 11 && $mobile_number[0] === '0') {
        $mobile_number = substr($mobile_number, 1);
    } elseif (strlen($mobile_number) === 12 && substr($mobile_number, 0, 2) === '92') {
        $mobile_number = substr($mobile_number, 2);
    } elseif (strlen($mobile_number) === 13 && substr($mobile_number, 0, 4) === '0092') {
        $mobile_number = substr($mobile_number, 4);
    }

    if (!preg_match('/^3\d{9}$/', $mobile_number)) {
        $mobile_number = '';
    }
}
echo $msg="Zong final parse - req {$data['request_id']}: "
    . "mobile='$mobile_number' | "
    . "cnic='$cnic' | "
    . "original='$cnic_original' | "
    . "name='$name' | "
    . "is_foreign=$is_foreigner | "
    . "cnic_cands=" . implode(', ', $cnic_cands);
Model_ErrorLog::log(
    'cron_parse_sub_zong_new',
    $msg,
    array(
        'request_id' => $data['request_id'],
        'company_name' => $data['company_name'],
        'mobile_number' => $mobile_number,
        'is_foreigner' => $is_foreigner
    ),
    null,
    'validation_success',
    'subscriber_parsing'
);
// 10. Debug (keep for verification, remove later if stable)


?>