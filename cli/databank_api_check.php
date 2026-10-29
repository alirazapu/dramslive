<?php
/**
 * cli/databank_api_check.php
 *
 * Checks the Databank integration API set-up of this server: reads
 * config/databank.php (databank.local.php / DATABANK_API_TOKEN), calls
 * GET integration/me through Helpers_DatabankApi and prints what the
 * DRAMS API user may use. The token itself is never printed.
 *
 * Usage from the project root:
 *
 *   php cli\databank_api_check.php
 *
 * Exit code 0 when the API answers and Normal search (the feature the
 * "Search All Sources" page uses) is available, 1 otherwise. On a
 * failure it prints the underlying cause (e.g. the cURL error).
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

// ---- Boot Kohana the way index.php does, without running a request ----
$root = realpath(__DIR__ . '/..');
$_SERVER['DOCUMENT_ROOT'] = $root;
define('EXT', '.php');
define('DOCROOT', $root . DIRECTORY_SEPARATOR);
define('APPPATH', $root . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);
define('MODPATH', $root . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR);
define('SYSPATH', $root . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR);
define('KOHANA_START_TIME', microtime(TRUE));
define('KOHANA_START_MEMORY', memory_get_usage());
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
ob_start();
require APPPATH . 'bootstrap' . EXT;
ob_end_clean();

$cfg   = Kohana::$config->load('databank');
$token = (string) $cfg->get('token');
echo 'Base URL : ', $cfg->get('base_url'), "\n";
echo 'Token    : ', $token === '' ? 'MISSING (set DATABANK_API_TOKEN or application/config/databank.local.php)'
                                  : 'set (' . strlen($token) . ' characters, prefix ' . substr($token, 0, 4) . '…)', "\n";
$host = (string) parse_url((string) $cfg->get('base_url'), PHP_URL_HOST);
if ($host !== '' && !filter_var($host, FILTER_VALIDATE_IP)) {
    $ip = gethostbyname($host);
    echo 'Resolves : ', $ip === $host ? 'NO - this server cannot resolve ' . $host
                                      . ' (add a hosts entry or use the IP in base_url)' : $ip, "\n";
}
try {
    $me = Helpers_DatabankApi::me();
} catch (Helpers_DatabankApi_Exception $e) {
    echo 'FAILED   : ', $e->reason, ' - ', $e->getMessage(), "\n";
    if ($e->detail !== '') {
        echo 'Cause    : ', $e->detail, "\n";
    }
    exit(1);
}

$codes = function ($list) {
    $out = array();
    foreach ((array) $list as $x) {
        if (is_array($x) && isset($x['code'])) {
            $out[] = $x['code'] . (isset($x['available']) && !$x['available'] ? ' (not usable)' : '');
        }
    }
    return $out ? implode(', ', $out) : 'none';
};
echo 'API user : ', isset($me['apiUser']['name']) ? $me['apiUser']['name'] : '?', "\n";
echo 'Features : ', $codes(isset($me['features']) ? $me['features'] : array()), "\n";
echo 'Sources  : ', $codes(isset($me['sources']) ? $me['sources'] : array()), "\n";
echo 'Withheld : ', $codes(isset($me['withheldFields']) ? $me['withheldFields'] : array()), "\n";
echo 'Limits   : ', json_encode(isset($me['limits']) ? $me['limits'] : array()), "\n";

$normal = FALSE;
foreach ((array) (isset($me['features']) ? $me['features'] : array()) as $f) {
    if (is_array($f) && isset($f['code']) && $f['code'] === 'NORMAL_SEARCH' && !empty($f['available'])) {
        $normal = TRUE;
    }
}
if (!$normal) {
    echo "MISSING  : the NORMAL_SEARCH feature, which the Search All Sources page needs\n";
    exit(1);
}
echo "OK\n";
exit(0);
