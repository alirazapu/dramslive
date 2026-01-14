<?php defined('SYSPATH') or die('No direct script access.');

$base_emails = array(
    1  => ['email' => 'leasupportteam@jazz.com.pk',               'name' => ''],
    3  => [
        'types' => [
            [6,1,2]   => ['email' => 'cdr.requests@ptclgroup.com',     'name' => ''],
            [4,3]     => ['email' => 'ufone.location@ptclgroup.com',   'name' => ''],
            'default' => ['email' => 'racentral@ufone.com',            'name' => ''],
        ],
    ],
    4  => ['email' => 'reg@zong.com.pk',                          'name' => ''],
    6  => [
        'types' => [
            [3,5,1,2] => ['email' => 'lea.2@telenor.com.pk',           'name' => ''],
            [4]       => ['email' => 'lea.1@telenor.com.pk',           'name' => ''],
            'default' => ['email' => 'lea@newsystem123.com',           'name' => ''],
        ],
    ],
    7  => [
        'types' => [
            [5,3,4]   => ['email' => 'leasupportteam@jazz.com.pk',     'name' => ''],
            'default' => ['email' => 'leasupportteam@jazz.com.pk',     'name' => ''],
        ],
    ],
    11 => ['email' => 'mega.radata@ptcl.net.pk',                  'name' => 'MegaRAdata/PTCL'],
    12 => ['email' => 'mega.radata@ptcl.net.pk',                  'name' => 'MegaRAdata/PTCL'],
    8  => ['email' => 'info.lea@sco.gov.pk',                      'name' => 'scom'],
    13 => ['email' => 'naumana.manzoor@nadra.gov.pk',             'name' => 'Nadra'],
);

// Your Gmail address (or move to config/email.php if you prefer)
$dev_base = 'ali.razapu';   // ← change only here if needed

if (1==1){
    //(Kohana::$environment === Kohana::DEVELOPMENT) {
    $suffix_map = [
        1  => 'leasupportteam-jazz',
        3  => function($type) {
            if (in_array($type, [6,1,2])) return 'cdr-requests-ptcl';
            if (in_array($type, [4,3]))   return 'ufone-location-ptcl';
            return 'racentral-ufone';
        },
        4  => 'reg-zong',
        6  => function($type) {
            if (in_array($type, [3,5,1,2])) return 'lea2-telenor';
            if (in_array($type, [4]))       return 'lea1-telenor';
            return 'lea-newsystem123';
        },
        7  => 'leasupportteam-jazz-warid',
        11 => 'megaradata-ptcl',
        12 => 'megaradata-ptcl',
        8  => 'info-lea-sco',
        13 => 'naumana-nadra',
    ];

    foreach ($base_emails as $company_id => &$cfg) {
        if (isset($cfg['email'])) {
            // Simple case
            $suffix = is_callable($suffix_map[$company_id] ?? null)
                ? $suffix_map[$company_id]($res['user_request_type_id'] ?? 0) // fallback
                : ($suffix_map[$company_id] ?? 'unknown-' . $company_id);

            $cfg['email'] = $dev_base . '+' . str_replace(['@','.'], '', $suffix) . '@gmail.com';
        } elseif (isset($cfg['types'])) {
            // Type-based case
            foreach ($cfg['types'] as $key => &$details) {
                if ($key === 'default') continue;
                $suffix = is_callable($suffix_map[$company_id])
                    ? $suffix_map[$company_id]($key[0])  // use first type as example
                    : ($suffix_map[$company_id] ?? 'unknown-' . $company_id);

                $details['email'] = $dev_base . '+' . str_replace(['@','.'], '', $suffix) . '@gmail.com';
            }
            // Also override default
            if (isset($cfg['types']['default'])) {
                $suffix = $suffix_map[$company_id] ?? 'unknown-' . $company_id;
                $cfg['types']['default']['email'] = $dev_base . '+' . str_replace(['@','.'], '', $suffix) . '@gmail.com';
            }
        }
    }
}

return $base_emails;