<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Databank integration API config (used by the DRAMS Databank searches,
 * see Helpers_DatabankApi and docs/DATABANK.md).
 *
 * [!!] The bearer token is NOT stored in this file - it is tracked in git.
 * It comes from application/config/databank.local.php (git-ignored, see
 * databank.local.example.php) or, failing that, from the environment
 * (DATABANK_API_TOKEN). A missing token leaves the value empty, which
 * Helpers_DatabankApi reports as "Databank API not configured" instead of
 * calling the API.
 */
$local_file = __DIR__ . DIRECTORY_SEPARATOR . 'databank.local.php';
$local = is_file($local_file) ? (array) require $local_file : array();

$secret = function ($key, $env, $default = '') use ($local) {
    if (isset($local[$key]) && $local[$key] !== '')
        return $local[$key];

    $value = getenv($env);

    return ($value !== FALSE && $value !== '') ? $value : $default;
};

return array(

    // Base URL of the Databank API, up to and including the version
    // segment. Endpoints are appended to it (integration/me, ...).
    'base_url' => $secret('base_url', 'DATABANK_API_BASE_URL', 'http://databank.ctdkpk.gov.pk/api/v1'),

    // Integration API token (dbk_...) of the DRAMS API user.
    'token'    => $secret('token', 'DATABANK_API_TOKEN'),

    // cURL timeouts in seconds. One normal search queries one source,
    // which the Databank caps at roughly 10 seconds; an all-sources
    // search with linked identifiers may follow them for 30 seconds.
    // Advanced Search calls run their source searches inside the request
    // (the Databank asks clients to allow at least 120 seconds).
    'connect_timeout'  => 5,
    'timeout'          => 60,
    'advanced_timeout' => 150,

    // Records requested per search. The Databank caps this per API user
    // (GET integration/me -> limits.recordsPerSource, 25 by default).
    'records_per_search' => 25,

    // Responses larger than this are refused instead of being decoded.
    'max_response_bytes' => 8388608,

);
