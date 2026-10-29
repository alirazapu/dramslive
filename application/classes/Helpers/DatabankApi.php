<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Helpers_DatabankApi — server-side client for the Databank integration
 * API (databank repo: app/Controllers/IntegrationApi.php and
 * docs/INTEGRATION_API.md). DRAMS uses two of its endpoints:
 *
 *   GET  integration/me      the API user's features, sources, withheld
 *                            field categories and limits
 *   POST integration/search  normal search: ONE identifier (mobile, cnic,
 *                            name or fir) in every permitted source
 *
 * Security rules:
 *   - The token only ever travels in the Authorization header. It never
 *     goes into a URL, a log line, an exception message or the browser.
 *   - Redirects are not followed, so the token is never re-sent to
 *     another host.
 *   - Searched values are never logged; a failure is logged with the
 *     endpoint, HTTP status, Databank error code and requestId only.
 *   - Searches are not retried: one request per user action.
 *
 * Every failure is thrown as Helpers_DatabankApi_Exception, whose message
 * is safe to show to the user.
 */
class Helpers_DatabankApi
{
    /** Identifier types POST integration/search accepts. */
    const TYPES = array('mobile', 'cnic', 'name', 'fir');

    protected static function config()
    {
        return Kohana::$config->load('databank');
    }

    /**
     * GET integration/me — who the token belongs to and what it may do.
     *
     * @return array decoded JSON ({ok, requestId, apiUser, features, sources, withheldFields, limits, version})
     * @throws Helpers_DatabankApi_Exception
     */
    public static function me()
    {
        return self::request('GET', 'integration/me');
    }

    /**
     * POST integration/search for one identifier in every source the API
     * user may read, optionally following the CNICs / numbers found
     * ("linked" — mobile and CNIC only).
     *
     * @return array decoded JSON ({search, sources[{code, label, status, statusLabel, count, records, message?}], totals, linked?})
     * @throws Helpers_DatabankApi_Exception
     */
    public static function search_all($type, $value, $linked = FALSE)
    {
        $body = array(
            'type'  => $type,
            'value' => self::normalize_identifier($type, $value),
            // The API clamps limit to its own cap (25 by default, 50 at most).
            'limit' => max(1, min(50, (int) self::config()->get('records_per_search', 25))),
        );
        if ($linked && ($type === 'mobile' || $type === 'cnic')) {
            $body['linked'] = TRUE;
        }
        $json = self::request('POST', 'integration/search', $body);
        if (!isset($json['sources']) || !is_array($json['sources'])) {
            throw self::fail('Unexpected answer from the Databank API.', 'malformed-response', 200,
                self::request_id_of($json), 'POST', 'integration/search', 'no sources list');
        }
        return $json;
    }

    /**
     * Validate and normalise an identifier with the same rules the API
     * applies (NormalSearch::identifier / IdentifierParser::mobileCore),
     * so bad input is refused before any request is made.
     *
     * @return string the value to send
     * @throws Helpers_DatabankApi_Exception reason 'invalid-request'
     */
    public static function normalize_identifier($type, $value)
    {
        $value = trim((string) $value);
        switch ($type) {
            case 'mobile':
                $d = preg_replace('/\D/', '', $value);
                if (strpos($d, '0092') === 0) {
                    $d = substr($d, 4);
                } elseif (strpos($d, '92') === 0 && strlen($d) === 12) {
                    $d = substr($d, 2);
                } elseif (strpos($d, '0') === 0 && strlen($d) === 11) {
                    $d = substr($d, 1);
                }
                if (preg_match('/^3\d{9}$/', $d)) {
                    return '0' . $d;
                }
                throw new Helpers_DatabankApi_Exception('Not a valid Pakistani mobile number (e.g. 03001234567).', 'invalid-request');
            case 'cnic':
                $d = preg_replace('/[\s\-]/', '', $value);
                if (preg_match('/^\d{13}$/', $d)) {
                    return $d;
                }
                throw new Helpers_DatabankApi_Exception('A CNIC has 13 digits (e.g. 17301-1234567-1).', 'invalid-request');
            case 'name':
                $v = (string) preg_replace('/\s+/u', ' ', $value);
                if (mb_strlen($v) >= 3 && mb_strlen($v) <= 100 && preg_match("/^\\p{L}[\\p{L}\\s.'\\-]*$/u", $v)) {
                    return $v;
                }
                throw new Helpers_DatabankApi_Exception('A name has at least 3 letters and only letters, spaces, dots, apostrophes and hyphens.', 'invalid-request');
            case 'fir':
                if (preg_match('~^[A-Za-z0-9][A-Za-z0-9 /\-.]{0,39}$~', $value)) {
                    return $value;
                }
                throw new Helpers_DatabankApi_Exception('An FIR number has letters, digits, spaces, "/", "-" or "." only (e.g. 123/2024).', 'invalid-request');
        }
        throw new Helpers_DatabankApi_Exception('Unsupported search type.', 'invalid-request');
    }

    /**
     * One HTTP call to the API. Returns the decoded body of a 2xx
     * {"ok": true, ...} answer; everything else throws.
     *
     * @throws Helpers_DatabankApi_Exception
     */
    protected static function request($method, $endpoint, array $body = NULL)
    {
        $config = self::config();
        $base   = rtrim(trim((string) $config->get('base_url')), '/');
        $token  = trim((string) $config->get('token'));
        $max    = max(65536, (int) $config->get('max_response_bytes', 8388608));

        if ($token === '' || !preg_match('/^[\x21-\x7E]+$/', $token) || !preg_match('#^https?://[^/\s]+(/\S*)?$#i', $base)) {
            throw self::fail('The Databank API is not configured on this server. Contact the system administrator.',
                'not-configured', 0, '', $method, $endpoint,
                $token === '' ? 'no token: set DATABANK_API_TOKEN or application/config/databank.local.php'
                              : (preg_match('/^[\x21-\x7E]+$/', $token) ? 'base_url is not an http(s) URL'
                                                                         : 'token contains spaces or control characters'));
        }

        $buffer    = '';
        $too_large = FALSE;
        $headers   = array('Authorization: Bearer ' . $token, 'Accept: application/json');
        $options   = array(
            CURLOPT_CONNECTTIMEOUT => max(1, (int) $config->get('connect_timeout', 5)),
            CURLOPT_TIMEOUT        => max(1, (int) $config->get('timeout', 60)),
            CURLOPT_FOLLOWLOCATION => FALSE,
            CURLOPT_WRITEFUNCTION  => function ($ch, $chunk) use (&$buffer, &$too_large, $max) {
                if (strlen($buffer) + strlen($chunk) > $max) {
                    $too_large = TRUE;
                    return 0; // aborts the transfer
                }
                $buffer .= $chunk;
                return strlen($chunk);
            },
        );
        if ($method === 'POST') {
            $headers[] = 'Content-Type: application/json';
            $options[CURLOPT_POST]       = TRUE;
            $options[CURLOPT_POSTFIELDS] = json_encode($body === NULL ? new stdClass() : $body);
        }
        $options[CURLOPT_HTTPHEADER] = $headers;

        $ch = curl_init($base . '/' . $endpoint);
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        $errno  = curl_errno($ch);
        $error  = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($too_large) {
            throw self::fail('The Databank API answer was too large to display.',
                'response-too-large', $status, '', $method, $endpoint, 'over ' . $max . ' bytes');
        }
        if ($errno) {
            $timeout = ($errno === 28); // CURLE_OPERATION_TIMEDOUT
            throw self::fail($timeout ? 'The Databank API did not answer in time. Try again in a moment.'
                                      : 'The Databank API could not be reached. Try again later.',
                $timeout ? 'timeout' : 'connection', 0, '', $method, $endpoint,
                'curl ' . $errno . ': ' . self::clip($error) . ' (base URL ' . $base . ')');
        }

        $json       = json_decode($buffer, TRUE);
        $request_id = self::request_id_of($json);

        if ($status >= 200 && $status < 300) {
            if (!is_array($json) || !isset($json['ok']) || $json['ok'] !== TRUE) {
                throw self::fail('Unexpected answer from the Databank API.',
                    'malformed-response', $status, $request_id, $method, $endpoint, 'body is not an {"ok": true} JSON object');
            }
            return $json;
        }

        $api_code  = (is_array($json) && isset($json['code']) && is_string($json['code'])) ? self::clip($json['code'], 40) : '';
        $api_error = (is_array($json) && isset($json['error']) && is_string($json['error'])) ? self::clip($json['error']) : '';

        if ($status === 401) {
            $reason  = 'unauthorized';
            $message = 'The Databank API rejected the DRAMS credentials' . ($api_code !== '' ? ' (' . $api_code . ')' : '')
                     . '. Contact the system administrator.';
        } elseif ($status === 403) {
            $reason  = 'forbidden';
            $message = $api_error !== '' ? 'Databank API: ' . $api_error : 'The Databank API refused this request.';
        } elseif (in_array($status, array(400, 413, 415, 422), TRUE)) {
            $reason  = 'invalid-request';
            $message = $api_error !== '' ? $api_error : 'The Databank API rejected the search.';
        } elseif ($status === 429) {
            $reason  = 'rate-limited';
            $message = 'The Databank API rate limit was reached. Wait a minute and search again.';
        } elseif ($status === 404) {
            $reason  = 'not-found';
            $message = $api_error !== '' ? 'Databank API: ' . $api_error
                                         : 'The Databank API endpoint was not found. Contact the system administrator.';
        } elseif ($status === 503) {
            $reason  = 'unavailable';
            $message = 'The Databank API is switched off or unavailable at the moment. Try again later.';
        } elseif ($status >= 500) {
            $reason  = 'server-error';
            $message = 'The Databank API could not complete the request. Try again later.';
        } else {
            $reason  = 'http-error';
            $message = 'Unexpected answer from the Databank API (HTTP ' . $status . ').';
        }
        throw self::fail($message, $reason, $status, $request_id, $method, $endpoint,
            trim('code ' . $api_code . ($reason !== 'invalid-request' && $api_error !== '' ? ': ' . $api_error : '')));
    }

    /**
     * Log a failure (never the token or the searched value) and build its
     * exception. $detail — the underlying cause, e.g. the cURL error — is
     * for administrators (log, cli/databank_api_check.php), not the page.
     */
    protected static function fail($message, $reason, $status, $request_id, $method, $endpoint, $detail = '')
    {
        // A rejected search value is the user's typo, not a system fault.
        if ($reason !== 'invalid-request') {
            try {
                Model_ErrorLog::log(
                    'helpers_databank_api',
                    $method . ' ' . $endpoint . ' failed: ' . $reason . ($detail !== '' ? ' - ' . $detail : ''),
                    array(
                        'reason'              => $reason,
                        'http_status'         => (int) $status,
                        'databank_request_id' => $request_id,
                    ),
                    NULL,
                    'api_error',
                    'databank_search',
                    $reason === 'rate-limited' ? 'warning' : 'error'
                );
            } catch (Exception $log_failed) {
                error_log('Helpers_DatabankApi: ' . $method . ' ' . $endpoint . ' failed: ' . $reason
                    . '; logger also failed: ' . $log_failed->getMessage());
            }
        }
        $suffix = $request_id !== '' ? ' (Databank reference ' . $request_id . ')' : '';
        return new Helpers_DatabankApi_Exception($message . $suffix, $reason, $status, $request_id, $detail);
    }

    /** The Databank requestId of a decoded answer, reduced to safe characters. */
    protected static function request_id_of($json)
    {
        if (is_array($json) && isset($json['requestId']) && is_string($json['requestId'])) {
            return (string) preg_replace('/[^A-Za-z0-9\-]/', '', substr($json['requestId'], 0, 64));
        }
        return '';
    }

    protected static function clip($text, $max = 300)
    {
        $text = trim((string) preg_replace('/\s+/', ' ', (string) $text));
        return mb_strlen($text) > $max ? mb_substr($text, 0, $max) . '...' : $text;
    }
}
