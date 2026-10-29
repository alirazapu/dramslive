<?php
/**
 * cli/tests/databank_api_mock.php
 *
 * Mock Databank integration API for cli/tests/databank_api_test.php,
 * served by PHP's built-in server:
 *
 *   php -S 127.0.0.1:8765 cli/tests/databank_api_mock.php
 *
 * The first path segment picks the scenario, the rest is the API path:
 *   /<scenario>/api/v1/integration/<endpoint>
 * Answers mirror the shapes of the real API (databank repo,
 * app/Controllers/IntegrationApi.php); the records are made-up test data.
 */
if (PHP_SAPI !== 'cli-server') {
    http_response_code(404);
    exit;
}

const MOCK_TOKEN = 'dbk_testtesttest_0123456789abcdef0123456789abcdef0123456789abcdef';

$path     = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts    = explode('/', trim($path, '/'));
$scenario = array_shift($parts);
$endpoint = implode('/', array_slice($parts, 3)); // drop api/v1/integration
$raw      = file_get_contents('php://input');
$body     = json_decode($raw, true) ?: array();
$auth     = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';

function answer($status, $data, $type = 'application/json; charset=utf-8')
{
    http_response_code($status);
    header('Content-Type: ' . $type);
    echo is_string($data) ? $data : json_encode($data);
    exit;
}

function fail($status, $code, $error)
{
    answer($status, array('ok' => false, 'error' => $error, 'code' => $code, 'requestId' => 'req' . $status . 'abc'));
}

function field($label, $value, array $extra = array())
{
    return array('label' => $label, 'value' => $value) + $extra;
}

function rec($source, $type, $title, array $fields, $reference = '')
{
    return array('source' => $source, 'sourceLabel' => $source, 'recordType' => $type, 'recordLabel' => $type,
                 'title' => $title, 'subtitle' => '', 'reference' => $reference, 'fields' => $fields);
}

$IMG = 'data:image/jpeg;base64,QUJDRA==';
$records = array(
    'MOBILE' => array(
        rec('MOBILE', 'subscription', '03001234567', array(
            field('Subscriber category', 'Local subscriber'), field('Mobile number', '03001234567'),
            field('CNIC', '17301-1234567-1'), field('Name', 'Test Person'), field('Address', 'House 1 Test Town'),
            field('Mobile operator', 'jazz'), field('Status', 'Active'), field('Biometric verification (BVS)', 'Verified'))),
        rec('MOBILE', 'subscription_foreign', '923001112223', array(
            field('Subscriber category', 'Foreign national'), field('CNIC', '17301-7654321-1'), field('Name', 'Second Person'),
            field('District', 'Peshawar'), field('Tehsil', 'City'))),
    ),
    'ECP' => array(
        rec('ECP', 'ecp_record', 'Test Person', array(
            field('Name', 'Test Person', array('state' => 'available', 'stateLabel' => 'Available')),
            field("Father's name", 'Image available - text not extracted', array('state' => 'image', 'stateLabel' => 'Image available', 'image' => $IMG)),
            field('Address', 'Village 2', array('state' => 'available', 'stateLabel' => 'Available', 'image' => $IMG)),
            field('CNIC', '17301-1234567-1'), field('Phone numbers', '03001234567, 03111234567'),
            field('Age', '40'), field('Gender', 'Male'), field('Electoral block code', '123456')), 'Record reference 4242'),
    ),
    'FIR' => array(
        rec('FIR', 'fir_profile', 'Test Person', array(
            field('CNIC', '17301-1234567-1'), field('Name', 'Test Person'), field("Father's name", 'Test Father'),
            field('Alias', 'TP'), field('Tribe', 'T'), field('Place of birth', 'Town'),
            field('Latest FIR number', '12/2024'), field('FIR date', '01 Jan 2024')), 'Record reference 7'),
    ),
    'DRIVING_LICENCE' => array(
        rec('DRIVING_LICENCE', 'licence', 'Test Person', array(
            field('CNIC', '17301-1234567-1'), field('Name', 'Test Middle Person'), field("Father's name", 'Test Father'),
            field('Photo', $IMG), field('Licence number', 'LIC-001'), field('Date of birth', '01 Jan 1980'), field('Gender', 'M'),
            field('Issued on', '01 Jan 2020'), field('Expires on', '01 Jan 2025')), 'Licence No. LIC-001'),
        rec('DRIVING_LICENCE', 'licence', 'Other Person', array(
            field('CNIC', '17301-1234567-1'), field('Name', 'Other Person'), field("Father's name", 'Someone Else'),
            field('Licence number', 'LIC-002'))),
    ),
    'GOVT_EMPLOYEE' => array(
        rec('GOVT_EMPLOYEE', 'employee', 'Test Person', array(
            field('CNIC', '17301-1234567-1'), field('Name', 'Test Person'), field("Father's name", 'Test Father'),
            field('Personnel number', '55501'), field('Job title', 'Clerk'), field('Organisation unit', 'Education'),
            field('Personnel area', 'Peshawar')), 'Personnel No. 55501'),
    ),
    'VEHICLE' => array(
        rec('VEHICLE', 'vehicle', 'ABC-123', array(
            field('Registration number', 'ABC-123'), field('Make / model', 'Test Car'), field('Colour', 'White'),
            field('Vehicle image', 'data:image/png;base64,"><script>alert(1)</script>'))),
    ),
);

// --- scenarios that answer every endpoint the same way ---------------
switch ($scenario) {
    case 'status401': fail(401, 'token-invalid', 'The token is not valid.');
    case 'status403': fail(403, 'not-permitted', 'This API user is not permitted to search: ECP.');
    case 'status422': fail(422, 'invalid-request', 'A CNIC has 13 digits (e.g. 17301-1234567-1).');
    case 'status429': fail(429, 'rate-limited', 'Rate limit reached.');
    case 'status404': fail(404, 'not-found', 'Unknown endpoint. See the integration API documentation.');
    case 'status503': fail(503, 'integration-disabled', 'The integration API is switched off on this server.');
    case 'status500empty': http_response_code(500); exit;
    case 'notjson': answer(200, '<html>proxy error page</html>', 'text/html');
    case 'okfalse': answer(200, array('ok' => false));
    case 'slow': sleep(3); answer(200, array('ok' => true, 'requestId' => 'slow'));
    case 'big': answer(200, '{"ok":true,"pad":"' . str_repeat('x', 150000) . '"}');
    case 'redirect':
        http_response_code(302);
        header('Location: http://127.0.0.1:' . $_SERVER['SERVER_PORT'] . '/ok/api/v1/integration/me');
        exit;
}

// --- per-endpoint answers ----------------------------------------------
$request_id = 'abc123def456';
if ($auth !== 'Bearer ' . MOCK_TOKEN) {
    fail(401, 'token-invalid', 'The token is not valid.');
}

if ($endpoint === 'me') {
    answer(200, array('ok' => true, 'requestId' => $request_id, 'apiUser' => array('name' => 'Drams'),
        'features' => array(array('code' => 'NORMAL_SEARCH', 'label' => 'Normal search', 'available' => true)),
        'sources' => array(array('code' => 'ECP', 'label' => 'Election Commission (ECP)')),
        'withheldFields' => array(), 'limits' => array('requestsPerMinute' => 30, 'maxBodyBytes' => 32768, 'recordsPerSource' => 25),
        'version' => 'v1'));
}

if ($endpoint === 'search') {
    $wanted = isset($body['sources']) ? $body['sources'] : array_keys($records);
    $blocks = array();
    foreach ($wanted as $code) {
        $recs = isset($records[$code]) ? $records[$code] : array();
        $status = $recs ? 'ok' : 'empty';
        if (isset($body['value']) && $body['value'] === '1730199999990') { $recs = array(); $status = 'empty'; }
        $block = array('code' => $code, 'label' => $code, 'status' => $status, 'statusLabel' => $status, 'count' => count($recs), 'records' => $recs);
        if ($scenario === 'unavailable') $block = array('code' => $code, 'label' => $code, 'status' => 'unavailable', 'statusLabel' => 'Source unavailable', 'count' => 0, 'records' => array(), 'message' => 'This source took too long to answer.');
        if ($scenario === 'unsupported') $block = array('code' => $code, 'label' => $code, 'status' => 'unsupported', 'statusLabel' => 'Not searchable', 'count' => 0, 'records' => array(), 'message' => $code . ' cannot be searched by FIR Number.');
        if ($scenario === 'badstatus') $block['status'] = 'weird';
        $blocks[] = $block;
    }
    if ($scenario === 'missing') $blocks = array();
    // Echo what was sent (minus the value) so the test can check the request.
    $sent = $body;
    unset($sent['value']);
    $out = array('ok' => true, 'requestId' => $request_id, 'search' => array('type' => isset($body['type']) ? $body['type'] : ''),
                 'sources' => $blocks, 'totals' => array('records' => 0), 'sent' => $sent, 'valueSent' => isset($body['value']) ? $body['value'] : null);
    if (!empty($body['linked'])) $out['linked'] = array('followed' => true, 'steps' => array('Searched 1 value in 6 sources.', 'Found CNIC <b>x</b>.'));
    answer(200, $out);
}

if ($endpoint === 'ecp/family') {
    if (empty($body['ecpId'])) fail(422, 'invalid-request', 'Send {"ecpId": 123}.');
    $member = $records['ECP'][0];
    $member['reference'] = 'Record reference 4343';
    answer(200, array('ok' => true, 'requestId' => $request_id, 'found' => true, 'record' => $records['ECP'][0],
                      'members' => array($member), 'count' => 1));
}

if ($endpoint === 'advanced/export') {
    http_response_code(200);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="databank-timeline-INV-1.csv"');
    echo "\xEF\xBB\xBF\"Header\"\n\"row\"\n";
    exit;
}

if (strpos($endpoint, 'advanced/') === 0) {
    answer(200, array('ok' => true, 'requestId' => $request_id, 'searchId' => 'abcdef0123456789abcdef0123456789',
                      'endpoint' => $endpoint, 'sentKeys' => array_keys($body), 'sent' => $body));
}

fail(404, 'not-found', 'Unknown endpoint. See the integration API documentation.');
