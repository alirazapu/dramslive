<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Template for application/config/whatsapp.local.php.
 *
 * Copy this file to whatsapp.local.php and fill in the real values.
 * whatsapp.local.php is git-ignored so gateway credentials never get
 * committed. Alternatively export WHATSAPP_API_KEY / WHATSAPP_ACCOUNT /
 * WHATSAPP_BASE_URL in the environment and skip the file entirely.
 */
return array(
    'api_key'  => 'YOUR_GATEWAY_API_SECRET',
    'account'  => 'YOUR_GATEWAY_ACCOUNT_ID',
    'base_url' => 'http://api.bebansoft.com',
);
