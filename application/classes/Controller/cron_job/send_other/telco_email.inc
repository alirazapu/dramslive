<?php

/*
// Load config once (Kohana caches it automatically)
$company_emails = Kohana::config('company_emails');

// Default fallback in case company is missing/invalid
$to = 'ali.razapu@gmail.com';  // or throw exception, log error, etc.
$to_name = 'Fallback Recipient';

// Get company config
if (isset($company_emails[$company_name])) {
    $cfg = $company_emails[$company_name];

    // Simple case: fixed email for company
    if (isset($cfg['email'])) {
        $to = $cfg['email'];
        $to_name = $cfg['name'] ?? '';
    } // Complex case: email depends on user_request_type_id
    elseif (isset($cfg['types'])) {
        $request_type = (int)($res['user_request_type_id'] ?? 0);
        $matched = false;

        foreach ($cfg['types'] as $types_key => $details) {
            if ($types_key === 'default') {
                continue;
            }

            // $types_key can be array [3,5,1,2] or single value
            $allowed_types = is_array($types_key) ? $types_key : [$types_key];

            if (in_array($request_type, $allowed_types)) {
                $to = $details['email'];
                $to_name = $details['name'] ?? '';
                $matched = true;
                break;
            }
        }

        // Use default if no specific match found
        if (!$matched && isset($cfg['types']['default'])) {
            $to = $cfg['types']['default']['email'];
            $to_name = $cfg['types']['default']['name'] ?? '';
        }
    }
}

// Optional: log when fallback is used (good for production monitoring)
if ($to === 'fallback@yourdomain.com') {
    Kohana::$log->add(Log::ERROR, "No email configured for company {$company_name}, type " . ($res['user_request_type_id'] ?? 'unknown'));
}

// Now $to and $to_name are ready to use in your email sending code
// Example:
// Email::send($to, $to_name, $subject, $body, ...);

*/
switch ($company_name) {
    case 1: // Mobilink
        $to = 'leasupportteam@jazz.com.pk';
        $to = 'ali.razapu+leasupportteamjazzemail@gmail.com';
        $to_name = '';
        break;
    case 3: // Ufone
        if (($res['user_request_type_id'] == 6 || $res['user_request_type_id'] == 1 || $res['user_request_type_id'] == 2)) {
            $to = 'cdr.requests@ptclgroup.com';
            $to_name = ''; //ufone auto for cdr msisdn/imei
        } elseif (($res['user_request_type_id'] == 4 || $res['user_request_type_id'] == 3)) {
            $to = 'ufone.location@ptclgroup.com';
            $to_name = ''; //ufone auto for cdr msisdn/imei
        } else {
            $to = 'racentral@ufone.com';
            $to_name = '';
        }
        $to = 'ali.razapu+racentralufoneemail@gmail.com';
        break;
    case 4: // Zong
        $to = 'reg@zong.com.pk';
        $to = 'ali.razapu+regzongemail@gmail.com';
        $to_name = '';
        break;
    case 6: // Telenor
        $is_second = Helpers_Utilities::get_telenor_flag();
        if (($res['user_request_type_id'] == 3 || $res['user_request_type_id'] == 5 || $res['user_request_type_id'] == 1 || $res['user_request_type_id'] == 2)) {
            $to = 'lea.2@telenor.com.pk';
            $to = 'ali.razapu+lea2telenoremail@gmail.com';
            $to_name = ''; //Law Enforcement Agency
        } elseif ($res['user_request_type_id'] == 4) {
            $to = 'lea.1@telenor.com.pk';
            $to = 'ali.razapu+lea1telenoremail@gmail.com';
            $to_name = '';
        } else {
            $to = 'lea@newsystem123.com';
            $to = 'ali.razapu+leanewsystem123telenoremail@gmail.com';
            $to_name = '';
        }
        break;
    case 7: // Warid 3 = subscriber , 4 = location ,  5, cnic
        if (($res['user_request_type_id'] == 5 || $res['user_request_type_id'] == 3 || $res['user_request_type_id'] == 4)) {
            $to = 'leasupportteam@jazz.com.pk';
            $to_name = '';
        } else {
            $to = 'leasupportteam@jazz.com.pk';
            $to_name = '';
        }
        $to = 'ali.razapu+leasupportteam@gmail.com';
        break;
    case 11:
        $to = 'mega.radata@ptcl.net.pk';
        $to = 'ali.razapu+megaradataptclemail@gmail.com';
        $to_name = 'MegaRAdata/PTCL';
        break;
    case 12:
        $to = 'mega.radata@ptcl.net.pk';
        $to = 'ali.razapu+megaradataptclintelemail@gmail.com';
        $to_name = 'MegaRAdata/PTCL';
        break;
    case 8:
        $to = 'info.lea@sco.gov.pk';
        $to = 'ali.razapu+infoleascoemail@gmail.com';
        $to_name = 'scom';
        break;
    case 13:
        $to = 'naumana.manzoor@nadra.gov.pk';
        $to = 'ali.razapu+naumanamanzoornadraemail@gmail.com';
        $to_name = 'Nadra';
        break;

}

?>