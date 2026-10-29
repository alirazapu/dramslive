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
 * Exit code 0 when the API answers and the five Databank search pages'
 * sources are granted, 1 otherwise.
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
try {
    $me = Helpers_DatabankApi::me();
} catch (Helpers_DatabankApi_Exception $e) {
    echo 'FAILED   : ', $e->reason, ' - ', $e->getMessage(), "\n";
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

$granted = array();
foreach ((array) (isset($me['sources']) ? $me['sources'] : array()) as $s) {
    if (is_array($s) && isset($s['code'])) {
        $granted[] = $s['code'];
    }
}
$missing = array_diff(array('MOBILE', 'ECP', 'FIR', 'DRIVING_LICENCE', 'GOVT_EMPLOYEE'), $granted);
if ($missing) {
    echo 'MISSING  : sources needed by the Databank search pages: ', implode(', ', $missing), "\n";
    exit(1);
}
echo "OK\n";
exit(0);
