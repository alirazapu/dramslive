<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * WhatsApp OTP helper (BebanSoft API integration).
 *
 * Adapted from the reference whatsapp_helper.php used in another project,
 * but the API key here is a single fixed account (application/config/whatsapp.php)
 * rather than one looked up per-company from a database.
 */
class Helpers_Whatsapp {

    protected static function config()
    {
        return Kohana::$config->load('whatsapp');
    }

    protected static function api_key()
    {
        return self::config()->get('api_key');
    }

    protected static function base_url()
    {
        return rtrim(self::config()->get('base_url'), '/');
    }

    /**
     * List WhatsApp devices (accounts) registered under the configured API key.
     *
     * [!!] /api/get/devices returns HTTP 200 with an always-empty data array
     * for this key (confirmed live). /api/get/wa.accounts is the endpoint
     * that actually lists the account and its live connection status - an
     * earlier version of this key got a 403 from it, but that is no longer
     * the case.
     *
     * @return array Decoded API response
     */
    public static function get_devices()
    {
        $url = self::base_url() . '/api/get/wa.accounts?secret=' . urlencode(self::api_key());

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => (int) self::config()->get('connect_timeout', 5),
            CURLOPT_TIMEOUT        => (int) self::config()->get('timeout', 15),
        ));

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch))
        {
            $error = curl_error($ch);
            curl_close($ch);

            return array('status' => false, 'message' => $error);
        }

        curl_close($ch);

        if ($http_code === 200)
            return json_decode($response, true);

        return array('status' => false, 'message' => 'HTTP Error ' . $http_code);
    }

    /**
     * Health-check the gateway before an OTP is handed to it.
     *
     * Hits /api/get/devices and reports only whether the API answered at
     * all: a missing key, a dead host, a timeout or a provider-level
     * rejection all come back as status FALSE. The caller can then skip
     * WhatsApp and go straight to e-mail instead of issuing a code that
     * would silently go nowhere.
     *
     * [!!] Deliberately does NOT fail on an empty device list. The
     * configured account id is not always listed for this key (see the
     * note on get_devices()), so an empty list is not proof of an outage
     * and treating it as one would push every login onto e-mail.
     *
     * @return array ['status' => bool, 'message' => string]
     */
    public static function check_gateway()
    {
        if (self::api_key() === NULL || self::api_key() === '' || self::config()->get('base_url') === NULL)
        {
            return array('status' => FALSE, 'message' => 'WhatsApp gateway is not configured.');
        }

        $devices = self::get_devices();

        if (!is_array($devices) || !isset($devices['status']))
        {
            return array('status' => FALSE, 'message' => 'Malformed response from /api/get/devices');
        }

        // get_devices() reports transport-level failures as status => FALSE;
        // the gateway reports its own rejections as a numeric body status,
        // which is why HTTP 200 alone is never enough to call it healthy.
        if ($devices['status'] === FALSE)
        {
            return array('status' => FALSE, 'message' => Arr::get($devices, 'message', 'unknown error'));
        }

        if ((int) $devices['status'] !== 200)
        {
            return array(
                'status'  => FALSE,
                'message' => 'Gateway error ' . (int) $devices['status'] . ': ' . Arr::get($devices, 'message', 'unknown'),
            );
        }

        return array('status' => TRUE, 'message' => 'OK');
    }

    /**
     * Who gets the gateway-down alert.
     *
     * Config 'alert_email' takes either an array or a comma/semicolon
     * separated string, so extra admins can be added there (or via the
     * WHATSAPP_ALERT_EMAIL environment variable) without touching code.
     * Malformed entries are dropped rather than handed to PHPMailer.
     *
     * @return array Valid addresses; empty means the alert is switched off
     */
    protected static function alert_recipients()
    {
        $configured = self::config()->get('alert_email');

        if (empty($configured))
            return array();

        $list = is_array($configured) ? $configured : preg_split('/[,;]/', $configured);

        $recipients = array();

        foreach ($list as $address)
        {
            $address = trim((string) $address);

            if ($address !== '' AND filter_var($address, FILTER_VALIDATE_EMAIL))
                $recipients[] = $address;
        }

        return array_values(array_unique($recipients));
    }

    /**
     * E-mail the administrators that the WhatsApp gateway is not working.
     *
     * Throttled (config 'alert_throttle') so a gateway that stays down does
     * not send one e-mail per login; the throttle covers the whole recipient
     * list, not each address. Swallows its own errors on purpose - a failed
     * alert must never take the login down with it.
     *
     * @param  string $reason  Why the gateway was considered down
     * @return bool   TRUE if at least one recipient was reached
     */
    public static function notify_gateway_down($reason)
    {
        try
        {
            $recipients = self::alert_recipients();

            if (empty($recipients) || !self::alert_throttle_passed())
                return FALSE;

            $subject = 'DRAMS alert: WhatsApp OTP gateway is not responding';

            $body = '<p>The DRAMS WhatsApp OTP gateway did not respond, so login verification codes are'
                  . ' falling back to e-mail.</p>'
                  . '<p><b>Reason:</b> ' . HTML::chars((string) $reason) . '</p>'
                  . '<p><b>Gateway:</b> ' . HTML::chars(self::base_url()) . '</p>'
                  . '<p><b>Time:</b> ' . date('Y-m-d H:i:s') . '</p>'
                  . '<p>No further alert will be sent for '
                  . (int) self::config()->get('alert_throttle', 3600) . ' seconds.</p>';

            $sent = FALSE;

            foreach ($recipients as $to)
            {
                // A separate message per address rather than one CC list: a
                // bad or bouncing address must not cost the other admins
                // their copy of the alert.
                try
                {
                    if ((int) Helpers_Email::send_email($to, 'DRAMS Administrator', $subject, $body) === 1)
                        $sent = TRUE;
                }
                catch (Exception $e)
                {
                    self::log_alert_failure($e->getMessage(), $to);
                }
            }

            return $sent;
        }
        catch (Exception $e)
        {
            self::log_alert_failure($e->getMessage());

            return FALSE;
        }
    }

    /**
     * Record that an alert could not be sent. Logging is itself wrapped
     * because this runs inside the login path's error handling.
     */
    protected static function log_alert_failure($error, $recipient = 'all recipients')
    {
        try
        {
            Kohana::$log->add(Log::ERROR, 'WhatsApp gateway-down alert to :to could not be sent: :error', array(
                ':to'    => $recipient,
                ':error' => $error,
            ));
        }
        catch (Exception $ignored)
        {
        }
    }

    /**
     * Rate limit for notify_gateway_down(). The Cache module is not enabled
     * in bootstrap.php, so a timestamp file in Kohana's cache directory is
     * the lightest state that survives between requests.
     *
     * @return bool TRUE if an alert may be sent now
     */
    protected static function alert_throttle_passed()
    {
        $throttle = (int) self::config()->get('alert_throttle', 3600);

        if ($throttle <= 0)
            return TRUE;

        $file = rtrim(Kohana::$cache_dir, '/\\') . DIRECTORY_SEPARATOR . 'whatsapp_gateway_alert.txt';

        if (is_file($file) AND (time() - (int) file_get_contents($file)) < $throttle)
            return FALSE;

        // Stamp before sending, not after: SMTP can take seconds, and a
        // parallel login in that window must not fire a duplicate alert.
        @file_put_contents($file, time(), LOCK_EX);

        return TRUE;
    }

    /**
     * The account (device) to send from. Prefers the fixed id in config
     * (application/config/whatsapp.php) since /api/get/devices does not
     * reliably list every account for this API key; falls back to
     * auto-detecting a connected device if no id is configured.
     *
     * @return string|FALSE  account id, or FALSE if no device is available
     */
    public static function get_connected_account()
    {
        $configured = self::config()->get('account');

        if (!empty($configured))
            return $configured;

        $devices = self::get_devices();

        if (empty($devices['data']))
            return FALSE;

        $pick_id = function ($device) {
            foreach (array('account', 'account_unique', 'unique', 'id') as $key)
            {
                if (!empty($device[$key]))
                    return $device[$key];
            }

            return FALSE;
        };

        foreach ($devices['data'] as $device)
        {
            if (isset($device['status']) && strtolower($device['status']) === 'connected')
                return $pick_id($device);
        }

        return $pick_id($devices['data'][0]);
    }

    /**
     * Check whether an actual WhatsApp device/account is connected -
     * distinct from check_gateway(), which only confirms the API itself
     * answers. This looks at the device list and reports the connection
     * status of the configured account (or the first device returned, if
     * none is configured).
     *
     * @return array ['status' => bool, 'message' => string, 'device' => array|NULL]
     */
    public static function test_device_connection()
    {
        $account = self::config()->get('account');
        $devices = self::get_devices();

        if (!is_array($devices) || !isset($devices['status']))
        {
            return array('status' => FALSE, 'message' => 'Malformed response from /api/get/devices', 'device' => NULL);
        }

        if ($devices['status'] === FALSE)
        {
            return array('status' => FALSE, 'message' => Arr::get($devices, 'message', 'unknown error'), 'device' => NULL);
        }

        if ((int) $devices['status'] !== 200)
        {
            return array(
                'status'  => FALSE,
                'message' => 'Gateway error ' . (int) $devices['status'] . ': ' . Arr::get($devices, 'message', 'unknown'),
                'device'  => NULL,
            );
        }

        if (empty($devices['data']))
        {
            return array('status' => FALSE, 'message' => 'No devices returned by gateway', 'device' => NULL);
        }

        $target = NULL;

        if (!empty($account))
        {
            foreach ($devices['data'] as $device)
            {
                $id = Arr::get($device, 'account', Arr::get($device, 'account_unique', Arr::get($device, 'unique', Arr::get($device, 'id'))));

                if ((string) $id === (string) $account)
                {
                    $target = $device;
                    break;
                }
            }

            if ($target === NULL)
            {
                return array(
                    'status'  => FALSE,
                    'message' => "Configured account '{$account}' was not found in the device list",
                    'device'  => NULL,
                );
            }
        }
        else
        {
            $target = $devices['data'][0];
        }

        $connected = isset($target['status']) && strtolower($target['status']) === 'connected';

        return array(
            'status'  => $connected,
            'message' => $connected
                ? 'Device is connected'
                : 'Device is NOT connected (status: ' . Arr::get($target, 'status', 'unknown') . ')',
            'device'  => $target,
        );
    }

    /**
     * Normalize a local Pakistani mobile number (e.g. "3100910677" or
     * "03100910677") to the international format WhatsApp expects
     * ("923100910677").
     */
    public static function format_recipient($mobile_number)
    {
        $digits = preg_replace('/\D/', '', (string) $mobile_number);

        if (strpos($digits, '92') === 0)
            return $digits;

        if (strpos($digits, '0') === 0)
            $digits = substr($digits, 1);

        return '92' . $digits;
    }

    /**
     * Send a plain text WhatsApp message.
     *
     * @param string $mobile_number  Local or international mobile number
     * @param string $message
     * @return array ['status' => bool, 'message' => string, ...]
     */
    public static function send_message($mobile_number, $message)
    {
        if (self::api_key() === NULL || self::api_key() === '' || self::config()->get('base_url') === NULL)
        {
            return array(
                'status'  => false,
                'message' => 'WhatsApp gateway is not configured.',
            );
        }

        $account = self::get_connected_account();

        if (!$account)
        {
            return array(
                'status'  => false,
                'message' => 'No connected WhatsApp device found for this API key.',
            );
        }

        $post_data = array(
            'secret'    => self::api_key(),
            'account'   => $account,
            'recipient' => self::format_recipient($mobile_number),
            'type'      => 'text',
            'message'   => $message,
            'priority'  => 1,
        );

        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL            => self::base_url() . '/api/send/whatsapp',
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $post_data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => (int) self::config()->get('connect_timeout', 5),
            CURLOPT_TIMEOUT        => (int) self::config()->get('timeout', 15),
        ));

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch))
        {
            $error = curl_error($ch);
            curl_close($ch);

            return array('status' => false, 'message' => 'cURL Error: ' . $error);
        }

        curl_close($ch);

        if ($http_code !== 200)
        {
            return array(
                'status'  => false,
                'message' => 'HTTP Error ' . $http_code,
            );
        }

        // [!!] The gateway answers HTTP 200 even when it rejects the request -
        // failures come back as {"status":401|400|404,...}. Only the body's
        // own status field says whether the message was actually queued, so
        // HTTP 200 alone must never be treated as success.
        $body = json_decode($response, true);

        if (!is_array($body) || !isset($body['status']))
        {
            return array(
                'status'  => false,
                'message' => 'Malformed gateway response',
            );
        }

        if ((int) $body['status'] !== 200)
        {
            return array(
                'status'  => false,
                'message' => 'Gateway error ' . (int) $body['status'] . ': ' . (isset($body['message']) ? $body['message'] : 'unknown'),
            );
        }

        return array(
            'status' => true,
            'data'   => $body,
        );
    }

    /**
     * Generate a numeric OTP code (length from config, default 6 digits).
     */
    public static function generate_otp()
    {
        $length = (int) self::config()->get('otp_length', 6);
        $min    = (int) str_pad('1', $length, '0');
        $max    = (int) str_pad('', $length, '9');

        return (string) random_int($min, $max);
    }

    /**
     * Send a login OTP code to the given mobile number.
     *
     * @return array ['status' => bool, 'message' => string, ...]
     */
    public static function send_otp($mobile_number, $otp)
    {
        $ttl_minutes = max(1, (int) round(self::config()->get('otp_ttl', 60) / 60));
        $unit = $ttl_minutes === 1 ? 'minute' : 'minutes';

        $message = "Your DRAMS login verification code is: {$otp}\nThis code expires in {$ttl_minutes} {$unit}. Do not share it with anyone.";

        return self::send_message($mobile_number, $message);
    }

}
