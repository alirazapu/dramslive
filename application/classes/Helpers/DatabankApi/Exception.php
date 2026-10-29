<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Failure of a Databank integration API call (see Helpers_DatabankApi).
 *
 * getMessage() is safe to show to the user: it never contains the token,
 * the searched value or server internals. $reason is a short code for
 * the kind of failure (not-configured, connection, timeout, unauthorized,
 * forbidden, invalid-request, rate-limited, source-unavailable,
 * malformed-response, ...). $request_id is the Databank requestId when
 * the API answered - administrators find the call on the Databank
 * "API Activity" page by it.
 */
class Helpers_DatabankApi_Exception extends Exception
{
    public $reason;
    public $http_status;
    public $request_id;

    public function __construct($message, $reason, $http_status = 0, $request_id = '')
    {
        parent::__construct($message);
        $this->reason      = (string) $reason;
        $this->http_status = (int) $http_status;
        $this->request_id  = (string) $request_id;
    }
}
