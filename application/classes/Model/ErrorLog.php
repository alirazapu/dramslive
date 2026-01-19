<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * module related with email template
 */

class Model_ErrorLog
{
    /**
     * Unified error logging
     *
     * @param string $source          e.g. 'cron_parse_sub', 'receive_email'
     * @param string $message         Main error message
     * @param array  $context         Associative array with any extra info
     *                                (request_id, company_name, mobile_requested, email_number, body_sample, etc.)
     * @param string $trace           Optional: stack trace (use $e->getTraceAsString())
     * @param string $errorType       Optional: 'validation', 'file_write', etc.
     * @param string $stage           Optional: 'parsing', 'attachment_save', etc.
     */
    public static function log(
        $source,
        $message,
        array $context = [],
        $trace = null,
        $errorType = null,
        $stage = null
    ) {
        $data = [
            'error_source'   => (string)$source,
            'error_message'  => (string)$message,
            'error_type'     => $errorType ? (string)$errorType : null,
            'process_stage'  => $stage ? (string)$stage : null,
            'error_trace'    => $trace,
            'created_at'     => date('Y-m-d H:i:s')
        ];

        // Map known context keys to table columns
        $mapping = [
            'request_id'       => 'request_id',
            'reference_id'     => 'reference_id',
            'company_name'     => 'company_name',
            'mobile_requested' => 'mobile_requested',
            'email_number'     => 'email_number',
        ];

        foreach ($mapping as $ctxKey => $column) {
            if (isset($context[$ctxKey])) {
                $data[$column] = $context[$ctxKey];
                unset($context[$ctxKey]); // remove so it goes to JSON
            }
        }

        // Remaining context → JSON
        if (!empty($context)) {
            $data['context_data'] = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
            DB::insert('system_error_log', array_keys($data))
              ->values(array_values($data))
              ->execute();
        } catch (Exception $e) {
            // Fallback to file log if DB fails
            error_log("[" . date('c') . "] ERROR LOG FAILED TO DB: $source - $message");
            error_log("Trace: " . ($trace ?? 'N/A'));
            error_log("Context: " . json_encode($context));
        }
    }
}