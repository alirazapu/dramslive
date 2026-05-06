<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * OCR engine wrapper.
 *
 * Decodes base64 / data-URI image strings and runs them through a chosen
 * OCR engine, returning the recognised text. Used by the ECP address
 * backfill cronjob to populate ecp_persons.address_text from the
 * address_image_base64 column.
 *
 * Supported engines:
 *   - 'tesseract' : local Tesseract 5 binary (free, offline).
 *                   Requires the `tesseract` executable on PATH (or the
 *                   tesseract_bin config key set to its absolute path).
 *                   For Urdu, install the `urd` traineddata file in
 *                   tessdata/ and call with opts ['lang' => 'eng+urd'].
 *   - 'gvision'   : Google Cloud Vision DOCUMENT_TEXT_DETECTION over HTTPS.
 *                   Requires an API key in application/config/ocr.php
 *                   ($config['google_vision_api_key']) or env GVISION_API_KEY.
 *
 * Adding a new engine: implement a private static method and route it in
 * recognise(). Keep the public surface small.
 */
class Helpers_Ocr
{
    /**
     * Run OCR on raw image bytes.
     *
     * @param string $image_bytes  binary image data (already base64-decoded)
     * @param string $engine       'tesseract' | 'gvision'
     * @param array  $opts         engine-specific options (e.g. ['lang' => 'eng+urd'])
     * @return string              recognised text, trimmed; '' on empty input or failure
     */
    public static function recognise($image_bytes, $engine = 'tesseract', $opts = array())
    {
        if (empty($image_bytes)) {
            return '';
        }
        switch ($engine) {
            case 'tesseract':
                return self::tesseract($image_bytes, $opts);
            case 'gvision':
                return self::google_vision($image_bytes, $opts);
            default:
                throw new Kohana_Exception('Unknown OCR engine: :engine', array(':engine' => $engine));
        }
    }

    /**
     * Decode a base64 (with or without `data:image/...;base64,` prefix)
     * into raw binary image bytes. Returns '' on malformed input.
     */
    public static function decode_base64_image($base64_or_data_uri)
    {
        if (empty($base64_or_data_uri)) {
            return '';
        }
        // Strip the data-URI prefix `data:image/jpeg;base64,` if present.
        if (strpos($base64_or_data_uri, 'data:') === 0) {
            $comma = strpos($base64_or_data_uri, ',');
            if ($comma !== false) {
                $base64_or_data_uri = substr($base64_or_data_uri, $comma + 1);
            }
        }
        $decoded = base64_decode($base64_or_data_uri, true);
        return $decoded === false ? '' : $decoded;
    }

    /**
     * Local Tesseract via shell. Writes a temp .jpg, calls `tesseract <in> -`,
     * captures stdout. stderr (progress messages) is redirected to NUL/null
     * so it does not pollute the recognised text.
     */
    private static function tesseract($bytes, $opts)
    {
        $lang          = isset($opts['lang']) ? $opts['lang'] : 'eng';
        $tesseract_bin = self::config('tesseract_bin', 'tesseract');

        $tmp = tempnam(sys_get_temp_dir(), 'ocr_');
        // tempnam doesn't add an extension; tesseract is usually fine without one
        // but renaming to .jpg avoids any format auto-detection edge cases.
        $tmp_jpg = $tmp . '.jpg';
        if (@rename($tmp, $tmp_jpg)) {
            $tmp = $tmp_jpg;
        }
        if (file_put_contents($tmp, $bytes) === false) {
            @unlink($tmp);
            throw new Kohana_Exception('OCR: could not write temp image to :path', array(':path' => $tmp));
        }

        $cmd = sprintf(
            '%s %s - -l %s 2>%s',
            escapeshellarg($tesseract_bin),
            escapeshellarg($tmp),
            escapeshellarg($lang),
            self::null_device()
        );
        $out = shell_exec($cmd);
        @unlink($tmp);
        return trim((string) $out);
    }

    /**
     * Google Cloud Vision DOCUMENT_TEXT_DETECTION.
     *
     * Falls back from `fullTextAnnotation.text` (preferred — preserves
     * paragraph layout) to `textAnnotations[0].description` (whole-image
     * concatenation) if the former is absent.
     */
    private static function google_vision($bytes, $opts)
    {
        $api_key = isset($opts['api_key'])
            ? $opts['api_key']
            : self::config('google_vision_api_key', getenv('GVISION_API_KEY') ?: '');
        if (empty($api_key)) {
            throw new Kohana_Exception(
                'OCR: Google Vision API key not configured. ' .
                'Set $config[\'google_vision_api_key\'] in application/config/ocr.php ' .
                'or env GVISION_API_KEY.'
            );
        }

        $url = 'https://vision.googleapis.com/v1/images:annotate?key=' . urlencode($api_key);
        $payload = json_encode(array(
            'requests' => array(array(
                'image'    => array('content' => base64_encode($bytes)),
                'features' => array(array('type' => 'DOCUMENT_TEXT_DETECTION')),
            )),
        ));

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $resp = curl_exec($ch);
        $err  = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($resp === false) {
            throw new Kohana_Exception('OCR: Google Vision request failed: :err', array(':err' => $err));
        }
        if ($code < 200 || $code >= 300) {
            throw new Kohana_Exception('OCR: Google Vision returned HTTP :code: :body',
                array(':code' => $code, ':body' => substr((string) $resp, 0, 300)));
        }

        $data = json_decode($resp, true);
        if (isset($data['responses'][0]['fullTextAnnotation']['text'])) {
            return trim((string) $data['responses'][0]['fullTextAnnotation']['text']);
        }
        if (isset($data['responses'][0]['textAnnotations'][0]['description'])) {
            return trim((string) $data['responses'][0]['textAnnotations'][0]['description']);
        }
        return '';
    }

    /** Read application/config/ocr.php with a default fallback. */
    private static function config($key, $default = null)
    {
        try {
            $cfg = Kohana::$config->load('ocr');
            $val = $cfg->get($key, $default);
            return $val === null ? $default : $val;
        } catch (Exception $e) {
            return $default;
        }
    }

    /** Platform-appropriate null device for shell stderr redirection. */
    private static function null_device()
    {
        return DIRECTORY_SEPARATOR === '\\' ? 'NUL' : '/dev/null';
    }
}
