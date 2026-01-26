<?php defined('SYSPATH') or die('No direct script access.');

$base_emails = array(
    1  => ['email' => 'leasupportteam@jazz.com.pk',               'name' => ''],
    3  => [
        'types' => [
            '6,1,2'   => ['email' => 'cdr.requests@ptclgroup.com',     'name' => '', 'type_ids' => [6,1,2]],
            '4,3'     => ['email' => 'ufone.location@ptclgroup.com',   'name' => '', 'type_ids' => [4,3]],
            'default' => ['email' => 'racentral@ufone.com',            'name' => ''],
        ],
    ],
    4  => ['email' => 'reg@zong.com.pk',                          'name' => ''],
    6  => [
        'types' => [
            '3,5,1,2' => ['email' => 'lea.2@telenor.com.pk',           'name' => '', 'type_ids' => [3,5,1,2]],
            '4'       => ['email' => 'lea.1@telenor.com.pk',           'name' => '', 'type_ids' => [4]],
            'default' => ['email' => 'lea@newsystem123.com',           'name' => ''],
        ],
    ],
    7  => [
        'types' => [
            '5,3,4'   => ['email' => 'leasupportteam@jazz.com.pk',     'name' => '', 'type_ids' => [5,3,4]],
            'default' => ['email' => 'leasupportteam@jazz.com.pk',     'name' => ''],
        ],
    ],
    11 => ['email' => 'mega.radata@ptcl.net.pk',                  'name' => 'MegaRAdata/PTCL'],
    12 => ['email' => 'mega.radata@ptcl.net.pk',                  'name' => 'MegaRAdata/PTCL'],
    8  => ['email' => 'info.lea@sco.gov.pk',                      'name' => 'scom'],
    13 => ['email' => 'naumana.manzoor@nadra.gov.pk',             'name' => 'Nadra'],
);

// Development email base - can be overridden by environment variable
$dev_base = getenv('DEV_EMAIL_BASE') ?: 'ali.razapu';

// Check if we're in development mode
// This will use masked emails for testing in DEV, real emails in PRODUCTION
$is_dev_mode = (Kohana::$environment === Kohana::DEVELOPMENT);

if ($is_dev_mode) {
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
            // Simple case - for simple emails, we use the suffix directly
            $suffix = is_callable($suffix_map[$company_id] ?? null)
                ? $suffix_map[$company_id](0) // default request type
                : ($suffix_map[$company_id] ?? 'unknown-' . $company_id);

            $cfg['email'] = $dev_base . '+' . str_replace(['@','.'], '', $suffix) . '@gmail.com';
        } elseif (isset($cfg['types'])) {
            // Type-based case
            foreach ($cfg['types'] as $key => &$details) {
                if ($key === 'default') continue;
                
                // Get type_ids from the details array
                $type_ids = $details['type_ids'] ?? [];
                $first_type = !empty($type_ids) ? $type_ids[0] : 0;
                
                $suffix = is_callable($suffix_map[$company_id])
                    ? $suffix_map[$company_id]($first_type)  // use first type as example
                    : ($suffix_map[$company_id] ?? 'unknown-' . $company_id);

                $details['email'] = $dev_base . '+' . str_replace(['@','.'], '', $suffix) . '@gmail.com';
            }
            // Also override default
            if (isset($cfg['types']['default'])) {
                $suffix = is_callable($suffix_map[$company_id] ?? null)
                    ? $suffix_map[$company_id](0)  // use 0 for default
                    : ($suffix_map[$company_id] ?? 'unknown-' . $company_id);
                $cfg['types']['default']['email'] = $dev_base . '+' . str_replace(['@','.'], '', $suffix) . '@gmail.com';
            }
        }
    }
}

return $base_emails;