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
     * [!!] The reference helper called /api/get/wa.accounts, but that endpoint
     * returns a 403 "no permission" for this key. /api/get/devices is the one
     * this key can actually use.
     *
     * @return array Decoded API response
     */
    public static function get_devices()
    {
        $url = self::base_url() . '/api/get/devices?secret=' . urlencode(self::api_key());

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
