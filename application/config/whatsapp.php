<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * BebanSoft WhatsApp gateway config (used for login OTP).
 *
 * [!!] Credentials are NOT stored in this file - it is tracked in git.
 * They come from application/config/whatsapp.local.php (git-ignored, see
 * whatsapp.local.example.php) or, failing that, from the environment.
 * A missing credential leaves the value empty, which Helpers_Whatsapp
 * treats as "gateway not configured" and falls back to e-mail.
 */
$local_file = __DIR__ . DIRECTORY_SEPARATOR . 'whatsapp.local.php';
$local = is_file($local_file) ? (array) require $local_file : array();

$secret = function ($key, $env, $default = '') use ($local) {
    if (isset($local[$key]) && $local[$key] !== '')
        return $local[$key];

    $value = getenv($env);

    return ($value !== FALSE && $value !== '') ? $value : $default;
};

return array(

    'api_key'  => $secret('api_key', 'WHATSAPP_API_KEY'),
    'account'  => $secret('account', 'WHATSAPP_ACCOUNT'),
    'base_url' => $secret('base_url', 'WHATSAPP_BASE_URL', 'http://api.bebansoft.com'),

    // How long an OTP stays valid, in seconds.
    'otp_ttl' => 60,

    // Digits in the generated OTP code.
    'otp_length' => 6,

    // Seconds a user must wait between OTP resend requests, and the maximum
    // number of resends allowed for a single login attempt.
    'resend_cooldown' => 30,
    'max_resends'     => 3,

    // cURL timeouts for the gateway. Kept short so a dead gateway falls
    // through to the e-mail fallback quickly instead of hanging the login.
    'connect_timeout' => 5,
    'timeout'         => 15,

);
