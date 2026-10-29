<?php
/**
 * cli/tests/databank_api_test.php
 *
 * Tests of the Databank API client (Helpers_DatabankApi), the record
 * mapping of the DRAMS Databank searches (Helpers_Databank) and the
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

$cfg = Kohana::$config->load('databank');
$use = function ($scenario, $token = MOCK_TOKEN) use ($cfg, $port) {
    $cfg->set('base_url', 'http://127.0.0.1:' . $port . '/' . $scenario . '/api/v1');
    $cfg->set('token', $token);
};
$use('ok');
$cfg->set('connect_timeout', 2);
$cfg->set('timeout', 2);
$cfg->set('advanced_timeout', 2);
$cfg->set('records_per_search', 25);
$cfg->set('max_response_bytes', 65536);
$IMG = 'data:image/jpeg;base64,QUJDRA==';

// ---- Configuration --------------------------------------------------------
$use('ok', '');
$e = expect_fail('missing token fails as not-configured', function () { Helpers_DatabankApi::me(); }, 'not-configured');
check('missing token: actionable diagnostic logged', $e && strpos(json_encode(end(Model_ErrorLog::$calls)), 'DATABANK_API_TOKEN') !== FALSE);
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

// ---- Successful answers and record mapping --------------------------------------
$me = Helpers_DatabankApi::me();
check('GET integration/me decoded', isset($me['apiUser']['name']) && $me['apiUser']['name'] === 'Drams');

$all = Helpers_DatabankApi::search_all('mobile', '+92 300 1234567', TRUE);
check('search sends the normalised value', $all['valueSent'] === '03001234567', json_encode($all['valueSent']));
check('search sends type, limit 25 and linked', $all['sent']['type'] === 'mobile' && $all['sent']['limit'] === 25 && $all['sent']['linked'] === TRUE,
      json_encode($all['sent']));
$all = Helpers_DatabankApi::search_all('name', 'Test Person', TRUE);
check('linked is only sent for mobile / CNIC', !isset($all['sent']['linked']), json_encode($all['sent']));
$cfg->set('records_per_search', 100);
$all = Helpers_DatabankApi::search_all('cnic', '17301-1234567-1');
check('records_per_search is clamped to 50', $all['sent']['limit'] === 50, json_encode($all['sent']));
$cfg->set('records_per_search', 25);

$r = Helpers_Databank::search_subscriber_unified('mobile', '03001234567');
check('subscriber: rows mapped', count($r['rows']) === 2 && $r['limit'] === 25 && $r['request_id'] === 'abc123def456');
check('subscriber: local row', $r['rows'][0]['msisdn'] === '03001234567' && $r['rows'][0]['cnic'] === '17301-1234567-1'
      && $r['rows'][0]['source'] === 'Local subscriber' && $r['rows'][0]['bvs_status'] === 'Verified' && $r['rows'][0]['operator'] === 'jazz',
      json_encode($r['rows'][0]));
check('subscriber: number taken from the title when the field is absent', $r['rows'][1]['msisdn'] === '923001112223'
      && $r['rows'][1]['master_pak_district'] === 'Peshawar' && $r['rows'][1]['address'] === '', json_encode($r['rows'][1]));

$r = Helpers_Databank::search_ecp('cnic', '17301-1234567-1');
$row = $r['rows'][0];
check('ECP: id from the record reference', $row['id'] === '4242');
check('ECP: fields mapped', $row['name_text'] === 'Test Person' && $row['uc_block_code'] === '123456'
      && $row['linked_numbers'] === '03001234567, 03111234567' && $row['age'] === '40', json_encode($row));
check('ECP: address image kept as data URI', $row['address_image_base64'] === $IMG);

$r = Helpers_Databank::search_ctd_kpk('fir', '12/2024');
check('FIR: latest FIR mapped', $r['rows'][0]['LatestFirRefNo'] === '12/2024' && $r['rows'][0]['FatherName'] === 'Test Father');

$r = Helpers_Databank::search_dlms('cnic', '1730112345671');
check('DLMS: licence fields mapped', $r['rows'][0]['LicenseNo'] === 'LIC-001' && $r['rows'][0]['ExpiryDate'] === '01 Jan 2025'
      && $r['rows'][0]['Name'] === 'Test Middle Person');
$dlms = $r['rows'];

$r = Helpers_Databank::search_govt_emp('name', 'Test Person');
check('Govt: employee fields mapped', $r['rows'][0]['pers_no'] === '55501' && $r['rows'][0]['org_unit_short_text'] === 'Education');

$r = Helpers_Databank::search_vehicle('cnic', '17301-1234567-1');
$labels = array();
foreach ($r['columns'] as $c) {
    $labels[] = $c['label'];
}
check('Vehicle: columns from the record fields', in_array('Registration number', $labels) && in_array('Make / model', $labels), implode(', ', $labels));
check('Vehicle: a malformed image value is dropped', !in_array('Vehicle image', $labels) && strpos(json_encode($r['rows']), 'script') === FALSE);

$r = Helpers_Databank::search_ecp('cnic', '17301-9999999-0');
check('empty result gives no rows', $r['rows'] === array());

$r = Helpers_Databank::search_all_sources('cnic', '17301-1234567-1', TRUE);
check('all sources: one block per source', count($r['blocks']) === 6 && $r['blocks'][0]['code'] === 'MOBILE', count($r['blocks']));
check('all sources: linked steps returned', count($r['linked']) === 2);
$imageCols = 0;
foreach ($r['blocks'] as $b) {
    foreach ($b['columns'] as $c) {
        if (isset($c['formatter']) && $c['formatter'] === 'image_jpeg') $imageCols++;
    }
}
check('all sources: valid images become image columns', $imageCols >= 2, $imageCols);

$fam = Helpers_Databank::ecp_family(4242);
check('ECP family: seed and members mapped', $fam['seed'] && $fam['seed']->id === '4242' && count($fam['family']) === 1
      && $fam['family'][0]->id === '4343' && $fam['seed']->father_text !== '');

// ---- refine_rows -----------------------------------------------------------------
$x = Helpers_Databank::refine_rows($dlms, array(array('field' => 'Father', 'keys' => array('FatherName'), 'value' => ' test  FATHER ', 'match' => 'contains')));
check('refine: contains, case and space insensitive', count($x['rows']) === 1 && $x['rows'][0]['LicenseNo'] === 'LIC-001');
$x = Helpers_Databank::refine_rows($dlms, array(array('field' => 'Licence', 'keys' => array('LicenseNo'), 'value' => 'lic-002', 'match' => 'exact')));
check('refine: exact', count($x['rows']) === 1 && $x['rows'][0]['LicenseNo'] === 'LIC-002');
$x = Helpers_Databank::refine_rows($dlms, array(array('field' => 'Licence', 'keys' => array('LicenseNo'), 'value' => 'LIC-00', 'match' => 'exact')));
check('refine: exact does not match a prefix', count($x['rows']) === 0);
$x = Helpers_Databank::refine_rows($dlms, array(array('field' => 'CNIC', 'keys' => array('CNIC'), 'value' => '1730112345671', 'match' => 'cnic')));
check('refine: CNIC compared by digits', count($x['rows']) === 2);
$x = Helpers_Databank::refine_rows($dlms, array(array('field' => 'IMSI', 'keys' => array('imsi'), 'value' => '41001', 'match' => 'exact')));
check('refine: a field the API never returned is reported, not silently matched', $x['unavailable'] === array('IMSI') && $x['rows'] === array());

// ---- Failures -------------------------------------------------------------------
Model_ErrorLog::$calls = array();
$cases = array(
    array('status401', 'unauthorized'), array('status403', 'forbidden'), array('status422', 'invalid-request'),
    array('status429', 'rate-limited'), array('status404', 'not-found'), array('status503', 'unavailable'),
    array('status500empty', 'server-error'), array('notjson', 'malformed-response'), array('okfalse', 'malformed-response'),
    array('big', 'response-too-large'), array('redirect', 'http-error'), array('unavailable', 'source-unavailable'),
    array('unsupported', 'unsupported'), array('missing', 'source-missing'), array('badstatus', 'malformed-response'),
);
$messages = array();
foreach ($cases as $c) {
    $use($c[0]);
    $e = expect_fail('HTTP scenario ' . $c[0] . ' -> ' . $c[1],
        function () { Helpers_DatabankApi::search('cnic', '17301-1234567-1', 'ECP'); }, $c[1]);
    if ($e) $messages[] = $e->getMessage();
}
$use('status403');
$e = expect_fail('403 keeps the Databank message', function () { Helpers_DatabankApi::me(); }, 'forbidden');
check('403 message shown to the user', $e && strpos($e->getMessage(), 'not permitted to search: ECP') !== FALSE, $e ? $e->getMessage() : '');
check('Databank reference added to the message', $e && strpos($e->getMessage(), 'req403abc') !== FALSE);
$use('unavailable');
$e = expect_fail('source unavailable keeps the Databank message', function () { Helpers_DatabankApi::search('cnic', '1730112345671', 'ECP'); }, 'source-unavailable');
check('source unavailable message', $e && strpos($e->getMessage(), 'took too long') !== FALSE);

$use('slow');
$t = microtime(TRUE);
expect_fail('timeout is reported as timeout', function () { Helpers_DatabankApi::me(); }, 'timeout');
check('timeout honoured (about 2 s, no retry)', microtime(TRUE) - $t < 4.5, round(microtime(TRUE) - $t, 1) . ' s');

$cfg->set('base_url', 'http://databank.invalid/api/v1');
expect_fail('unresolvable host is reported as connection', function () { Helpers_DatabankApi::me(); }, 'connection');
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
check('failures are logged', count(Model_ErrorLog::$calls) >= 15, count(Model_ErrorLog::$calls));
check('422 (user input) is not logged as a system error', strpos($logs, 'invalid-request') === FALSE);
check('the token never reaches a log', strpos($logs, MOCK_TOKEN) === FALSE && strpos($logs, 'testtesttest') === FALSE);
check('searched values never reach a log', strpos($logs, '1234567') === FALSE);
check('the token never reaches a user message', strpos(implode(' ', $messages), 'testtesttest') === FALSE);

// ---- Advanced Search proxy ---------------------------------------------------
$sid = 'abcdef0123456789abcdef0123456789';
$r = Helpers_DatabankApi::advanced('graph', array('classes' => array('direct'), 'evil' => 'x', 'searchId' => 'other'), $sid);
check('advanced: only whitelisted keys are forwarded', $r['endpoint'] === 'advanced/graph' && $r['sentKeys'] === array('classes', 'searchId'),
      json_encode($r['sentKeys']));
check('advanced: the server-side searchId is used', $r['sent']['searchId'] === $sid);
$r = Helpers_DatabankApi::advanced('start', array('identifiers' => array('mobile' => array('03001234567')), 'from' => '2026-01-01'), '');
check('advanced start: no searchId sent', $r['sentKeys'] === array('identifiers', 'from'), json_encode($r['sentKeys']));
$r = Helpers_DatabankApi::advanced('timeline', array('types' => array(array(array(array('too deep'))))), $sid);
check('advanced: values nested deeper than 3 are dropped', $r['sentKeys'] === array('searchId'), json_encode($r['sentKeys']));
expect_fail('advanced: unknown call refused', function () use ($sid) { Helpers_DatabankApi::advanced('delete', array(), $sid); }, 'invalid-request');
expect_fail('advanced: malformed searchId refused', function () { Helpers_DatabankApi::advanced('summary', array(), '../../me'); }, 'invalid-request');
foreach (array('cdr_records', 'location', 'geo', 'path', 'indicators', 'profile', 'expand', 'relationship', 'cdr_activity', 'findings', 'records', 'run') as $op) {
    $r = Helpers_DatabankApi::advanced($op, array(), $sid);
    check("advanced $op -> " . Helpers_DatabankApi::$advanced_ops[$op][0], $r['endpoint'] === Helpers_DatabankApi::$advanced_ops[$op][0]);
}

$csv = Helpers_DatabankApi::export($sid, 'timeline');
check('export: CSV body and file name', $csv['filename'] === 'databank-timeline-INV-1.csv' && strpos($csv['body'], '"Header"') !== FALSE);
expect_fail('export: unknown kind refused', function () use ($sid) { Helpers_DatabankApi::export($sid, 'passwords'); }, 'invalid-request');

// ---- Result table escaping -------------------------------------------------------
$html = View::factory('templates/user/databank_advanced_results')
    ->set('rows', array(array('a' => '<script>alert(1)</script>', 'img' => 'x" onerror="alert(1)')))
    ->set('columns', array(array('key' => 'a', 'label' => 'A'), array('key' => 'img', 'label' => 'I', 'formatter' => 'image_jpeg')))
    ->set('summary', '1 <b>result</b>')
    ->set('notes', array('note <i>x</i>'))
    ->render();
check('results view escapes values', strpos($html, '<script>') === FALSE && strpos($html, '&lt;script&gt;') !== FALSE);
check('results view escapes image sources', strpos($html, '" onerror="') === FALSE);
check('results view escapes summary and notes', strpos($html, '<b>result</b>') === FALSE && strpos($html, '<i>x</i>') === FALSE);

echo "\n", $passed, ' passed, ', $failed, " failed\n";
exit($failed ? 1 : 0);
