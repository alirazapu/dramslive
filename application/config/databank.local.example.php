<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Template for application/config/databank.local.php.
 *
 * Copy this file to databank.local.php and fill in the real values.
 * databank.local.php is git-ignored so the Databank API token never gets
 * committed. Alternatively export DATABANK_API_TOKEN (and optionally
 * DATABANK_API_BASE_URL) in the web server's environment and skip the
 * file entirely.
 */
return array(
    // The dbk_... token issued on the Databank "API Users" page. Left
    // empty, the Databank searches report "Databank API not configured".
    'token'    => '',
    'base_url' => 'http://databank.ctdkpk.gov.pk/api/v1',
);
