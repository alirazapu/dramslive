<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * BebanSoft WhatsApp gateway config (used for login OTP).
 */
return array(

    'api_key'  => '2310ccb70b61beb4eeca019ef7fc2babc529d243',
    'account'  => '1788453893c4ca4238a0b923820dcc509a6f75849b6a99a405c4c76',
    'base_url' => 'http://api.bebansoft.com',

    // How long an OTP stays valid, in seconds.
    'otp_ttl' => 60,

    // Digits in the generated OTP code.
    'otp_length' => 6,

);
