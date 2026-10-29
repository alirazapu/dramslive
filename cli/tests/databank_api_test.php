<?php
/**
 * cli/tests/databank_api_test.php
 *
 * Tests of the Databank API client (Helpers_DatabankApi), the record
 * tables of the "Search All Sources" page (Helpers_Databank) and the
 * escaping of the result table — against a MOCK Databank API
 * (cli/tests/databank_api_mock.php, started here on a free local port).
 * No real API call is made and nothing is written to the database
 * (Model_ErrorLog is replaced by an in-memory recorder).
 *
 * Usage from the project root:
 *
 *   php cli/tests/databank_api_test.php
 *
 * Exit code 0 when every check passes, 1 otherwise.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

// Failures are logged through Model_ErrorLog, which inserts into
// system_error_log. Defined before Kohana's autoloader could load the
// real model, this recorder keeps the calls in memory instead.
class Model_ErrorLog
{
    public static $calls = array();

    public static function log()
    {
        self::$calls[] = func_get_args();
    }
}

// ---- Boot Kohana the way index.php does, without running a request ----
$root = realpath(__DIR__ . '/../..');
putenv('KOHANA_ENV=development');
$_SERVER['KOHANA_ENV']      = 'development';
$_SERVER['DOCUMENT_ROOT']   = $root;
$_SERVER['HTTP_HOST']       = 'dev.ctd.drams.com';
define('EXT', '.php');
define('DOCROOT', $root . DIRECTORY_SEPARATOR);
define('APPPATH', $root . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);
define('MODPATH', $root . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR);
define('SYSPATH', $root . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR);
define('KOHANA_START_TIME', microtime(TRUE));
define('KOHANA_START_MEMORY', memory_get_usage());
// Same level as php.ini on the servers: Kohana 3.3 raises deprecation
// notices on PHP 8.1 that its development error handler would turn fatal.
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
ob_start();
require APPPATH . 'bootstrap' . EXT;
ob_end_clean();

const MOCK_TOKEN = 'dbk_testtesttest_0123456789abcdef0123456789abcdef0123456789abcdef';

// ---- Mock API server ----------------------------------------------------
$probe = stream_socket_server('tcp://127.0.0.1:0');
$port  = (int) substr(strrchr(stream_socket_get_name($probe, FALSE), ':'), 1);
fclose($probe);
$null   = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'NUL' : '/dev/null';
$server = proc_open(array(PHP_BINARY, '-S', '127.0.0.1:' . $port, __DIR__ . DIRECTORY_SEPARATOR . 'databank_api_mock.php'),
                    array(0 => array('pipe', 'r'), 1 => array('file', $null, 'w'), 2 => array('file', $null, 'w')), $pipes);
register_shutdown_function(function () use ($server) { proc_terminate($server); });
for ($i = 0; $i < 50 && !@fsockopen('127.0.0.1', $port, $en, $es, 0.2); $i++) {
    usleep(100000);
}

// ---- Tiny test harness --------------------------------------------------
$passed = 0;
$failed = 0;
function check($name, $ok, $detail = '')
{
    global $passed, $failed;
    $ok ? $passed++ : $failed++;
    echo ($ok ? 'PASS ' : 'FAIL ') . $name . ($ok || $detail === '' ? '' : ' -- ' . $detail) . "\n";
}
function expect_fail($name, $fn, $reason)
{
    try {
        $fn();
        check($name, FALSE, 'no exception');
    } catch (Helpers_DatabankApi_Exception $e) {
        check($name, $e->reason === $reason, 'got ' . $e->reason . ': ' . $e->getMessage());
        return $e;
    }
    return NULL;
}
$search = function () { return Helpers_DatabankApi::search_all('cnic', '17301-1234567-1'); };

$cfg = Kohana::$config->load('databank');
$use = function ($scenario, $token = MOCK_TOKEN) use ($cfg, $port) {
    $cfg->set('base_url', 'http://127.0.0.1:' . $port . '/' . $scenario . '/api/v1');
    $cfg->set('token', $token);
};
$use('ok');
$cfg->set('connect_timeout', 2);
$cfg->set('timeout', 2);
$cfg->set('records_per_search', 25);
$cfg->set('max_response_bytes', 65536);

// ---- Configuration --------------------------------------------------------
$use('ok', '');
$e = expect_fail('missing token fails as not-configured', function () { Helpers_DatabankApi::me(); }, 'not-configured');
check('missing token: actionable diagnostic logged', $e && strpos(json_encode(end(Model_ErrorLog::$calls)), 'DATABANK_API_TOKEN') !== FALSE);
check('missing token: diagnostic in the exception detail, not the message', $e && strpos($e->detail, 'DATABANK_API_TOKEN') !== FALSE
      && strpos($e->getMessage(), 'DATABANK_API_TOKEN') === FALSE);
$use('ok', "dbk_bad\r\nX-Injected: 1");
expect_fail('token with CR/LF refused (no header injection)', function () { Helpers_DatabankApi::me(); }, 'not-configured');
$cfg->set('base_url', 'ftp://127.0.0.1/api/v1');
$cfg->set('token', MOCK_TOKEN);
expect_fail('non-http base URL refused', function () { Helpers_DatabankApi::me(); }, 'not-configured');
$use('ok');

// ---- Identifier validation (mirrors the API's NormalSearch rules) ------------
$good = array(
    array('mobile', '+92 300 1234567', '03001234567'), array('mobile', '3001234567', '03001234567'),
    array('mobile', '00923001234567', '03001234567'), array('mobile', '923001234567', '03001234567'),
    array('cnic', '17301-1234567-1', '1730112345671'), array('cnic', '17301 1234567 1', '1730112345671'),
    array('name', '  Ali   Khan ', 'Ali Khan'), array('name', "O'Brien-Khan", "O'Brien-Khan"),
    array('fir', '123/2024', '123/2024'),
);
foreach ($good as $g) {
    try {
        $v = Helpers_DatabankApi::normalize_identifier($g[0], $g[1]);
        check("valid {$g[0]} '{$g[1]}' normalised", $v === $g[2], 'got ' . $v);
    } catch (Exception $ex) {
        check("valid {$g[0]} '{$g[1]}' accepted", FALSE, $ex->getMessage());
    }
}
$bad = array(array('mobile', '12345'), array('mobile', '0400123456'), array('cnic', '123'), array('cnic', '17301-12'),
             array('name', 'x'), array('name', "x' OR 1=1 --"), array('name', '<b>Ali</b>'), array('fir', '1; DROP TABLE x'),
             array('fir', ''), array('passport', 'A1'), array('name', str_repeat('a', 101)));
foreach ($bad as $b) {
    expect_fail("invalid {$b[0]} '" . substr($b[1], 0, 20) . "' refused before any request",
        function () use ($b) { Helpers_DatabankApi::normalize_identifier($b[0], $b[1]); }, 'invalid-request');
}

// ---- Successful answers -------------------------------------------------------------
$me = Helpers_DatabankApi::me();
check('GET integration/me decoded', isset($me['apiUser']['name']) && $me['apiUser']['name'] === 'Drams');

$all = Helpers_DatabankApi::search_all('mobile', '+92 300 1234567', TRUE);
check('search sends the normalised value', $all['valueSent'] === '03001234567', json_encode($all['valueSent']));
check('search sends type, limit 25 and linked, and no source filter', $all['sent']['type'] === 'mobile'
      && $all['sent']['limit'] === 25 && $all['sent']['linked'] === TRUE && !isset($all['sent']['sources']), json_encode($all['sent']));
$all = Helpers_DatabankApi::search_all('name', 'Test Person', TRUE);
check('linked is only sent for mobile / CNIC', !isset($all['sent']['linked']), json_encode($all['sent']));
$cfg->set('records_per_search', 100);
$all = Helpers_DatabankApi::search_all('cnic', '17301-1234567-1');
check('records_per_search is clamped to 50', $all['sent']['limit'] === 50, json_encode($all['sent']));
$cfg->set('records_per_search', 25);

// ---- All-sources tables ------------------------------------------------------------
$r = Helpers_Databank::search_all_sources('cnic', '17301-1234567-1', TRUE);
$codes = array();
foreach ($r['blocks'] as $b) {
    $codes[] = $b['code'];
}
check('one block per source, in the API order', $codes === array('MOBILE', 'ECP', 'FIR', 'DRIVING_LICENCE', 'GOVT_EMPLOYEE', 'VEHICLE'),
      json_encode($codes));
check('limit and Databank reference returned', $r['limit'] === 25 && $r['request_id'] === 'abc123def456');
check('linked steps returned as plain strings', count($r['linked']) === 2);
$block = function ($code) use ($r) {
    foreach ($r['blocks'] as $b) {
        if ($b['code'] === $code) return $b;
    }
    return NULL;
};
$labels = function ($b) {
    $out = array();
    foreach ($b['columns'] as $c) $out[$c['label']] = isset($c['formatter']) ? $c['formatter'] : 'text';
    return $out;
};
$mobile = $block('MOBILE');
$l = $labels($mobile);
check('MOBILE: a column per field label, in first-seen order', array_slice(array_keys($l), 0, 3) === array('Subscriber category', 'Mobile number', 'CNIC'),
      json_encode(array_keys($l)));
check('MOBILE: columns of every record (District only on the 2nd)', isset($l['District'], $l['Biometric verification (BVS)']) && count($mobile['rows']) === 2);
$keyOf = function ($b, $label) { foreach ($b['columns'] as $c) if ($c['label'] === $label) return $c['key']; return NULL; };
check('MOBILE: values land under their column', $mobile['rows'][0][$keyOf($mobile, 'Mobile number')] === '03001234567'
      && $mobile['rows'][1][$keyOf($mobile, 'District')] === 'Peshawar' && !isset($mobile['rows'][0][$keyOf($mobile, 'District')]));
$ecp = $block('ECP');
$l = $labels($ecp);
check('ECP: record images become image columns', ($l["Father's name (image)"] ?? '') === 'image_jpeg' && ($l['Address (image)'] ?? '') === 'image_jpeg',
      json_encode($l));
check('ECP: reference column added', isset($l['Reference']) && $ecp['rows'][0]['_reference'] === 'Record reference 4242');
$dl = $block('DRIVING_LICENCE');
check('DRIVING_LICENCE: photo field is an image column', ($labels($dl)['Photo'] ?? '') === 'image_jpeg');
$veh = $block('VEHICLE');
check('VEHICLE: a malformed image value is dropped', !isset($labels($veh)['Vehicle image']) && strpos(json_encode($veh['rows']), 'script') === FALSE);
check('VEHICLE: other fields kept', isset($labels($veh)['Registration number']));

$r = Helpers_Databank::search_all_sources('cnic', '17301-9999999-0', FALSE);
check('empty result: blocks with no rows', count($r['blocks']) === 6 && $block('ECP') !== NULL && $r['blocks'][1]['rows'] === array()
      && $r['blocks'][1]['status'] === 'empty');

$use('unavailable');
$r = Helpers_Databank::search_all_sources('cnic', '17301-1234567-1', FALSE);
check('a source the Databank could not reach keeps its status and message (not an exception)',
      $r['blocks'][0]['status'] === 'unavailable' && strpos($r['blocks'][0]['message'], 'took too long') !== FALSE);
$use('missing');
$r = Helpers_Databank::search_all_sources('cnic', '17301-1234567-1', FALSE);
check('no permitted sources: no blocks', $r['blocks'] === array());
$use('ok');

$t = Helpers_Databank::records_table(array(array(
    'fields'       => array(array('label' => 'A', 'value' => 'x'), array('label' => 'A', 'value' => 'y'),
                            array('label' => 'B', 'value' => array('nested')), 'junk'),
    'foundThrough' => 'Found through CNIC 1',
)));
check('records_table: first value of a label wins, non-scalar values and junk skipped, Found through column',
      count($t['columns']) === 2 && $t['rows'][0][$t['columns'][0]['key']] === 'x' && $t['columns'][1]['label'] === 'Found through');

// ---- Failures -------------------------------------------------------------------
Model_ErrorLog::$calls = array();
$cases = array(
    array('status401', 'unauthorized'), array('status403', 'forbidden'), array('status422', 'invalid-request'),
    array('status429', 'rate-limited'), array('status404', 'not-found'), array('status503', 'unavailable'),
    array('status500empty', 'server-error'), array('notjson', 'malformed-response'), array('okfalse', 'malformed-response'),
    array('big', 'response-too-large'), array('redirect', 'http-error'), array('nosources', 'malformed-response'),
);
$messages = array();
foreach ($cases as $c) {
    $use($c[0]);
    $e = expect_fail('HTTP scenario ' . $c[0] . ' -> ' . $c[1], $search, $c[1]);
    if ($e) $messages[] = $e->getMessage();
}
$use('status403');
$e = expect_fail('403 keeps the Databank message', function () { Helpers_DatabankApi::me(); }, 'forbidden');
check('403 message shown to the user', $e && strpos($e->getMessage(), 'not permitted to search: ECP') !== FALSE, $e ? $e->getMessage() : '');
check('Databank reference added to the message', $e && strpos($e->getMessage(), 'req403abc') !== FALSE);

$use('slow');
$t = microtime(TRUE);
expect_fail('timeout is reported as timeout', function () { Helpers_DatabankApi::me(); }, 'timeout');
check('timeout honoured (about 2 s, no retry)', microtime(TRUE) - $t < 4.5, round(microtime(TRUE) - $t, 1) . ' s');

$cfg->set('base_url', 'http://databank.invalid/api/v1');
$e = expect_fail('unresolvable host is reported as connection', function () { Helpers_DatabankApi::me(); }, 'connection');
check('connection failure: user message stays generic', $e && $e->getMessage() === 'The Databank API could not be reached. Try again later.');
check('connection failure: detail carries the cURL error and base URL', $e && strpos($e->detail, 'curl 6') === 0
      && strpos($e->detail, 'databank.invalid') !== FALSE, $e ? $e->detail : '');
// A refused port answers at once on Linux; Windows retries until the
// connect timeout. Either way it must fail cleanly as unreachable.
$cfg->set('base_url', 'http://127.0.0.1:1/api/v1');
try {
    Helpers_DatabankApi::me();
    check('closed port fails', FALSE, 'no exception');
} catch (Helpers_DatabankApi_Exception $e) {
    check('closed port fails as connection or timeout', in_array($e->reason, array('connection', 'timeout'), TRUE), $e->reason);
}
$use('ok');

$logs = json_encode(Model_ErrorLog::$calls);
check('failures are logged', count(Model_ErrorLog::$calls) >= 13, count(Model_ErrorLog::$calls));
check('422 (user input) is not logged as a system error', strpos($logs, 'invalid-request') === FALSE);
check('the token never reaches a log', strpos($logs, MOCK_TOKEN) === FALSE && strpos($logs, 'testtesttest') === FALSE);
check('searched values never reach a log', strpos($logs, '1234567') === FALSE);
check('the token never reaches a user message', strpos(implode(' ', $messages), 'testtesttest') === FALSE);

// ---- Result table escaping -------------------------------------------------------
$html = View::factory('templates/user/databank_advanced_results')
    ->set('rows', array(array('a' => '<script>alert(1)</script>', 'img' => 'x" onerror="alert(1)')))
    ->set('columns', array(array('key' => 'a', 'label' => 'A'), array('key' => 'img', 'label' => 'I', 'formatter' => 'image_jpeg')))
    ->set('summary', '1 <b>result</b>')
    ->render();
check('results view escapes values', strpos($html, '<script>') === FALSE && strpos($html, '&lt;script&gt;') !== FALSE);
check('results view escapes image sources', strpos($html, '" onerror="') === FALSE);
check('results view escapes the summary', strpos($html, '<b>result</b>') === FALSE);

echo "\n", $passed, ' passed, ', $failed, " failed\n";
exit($failed ? 1 : 0);
