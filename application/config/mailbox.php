<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Who can open the Mailbox tab (the DCS Gmail account).
 *
 * Add the user's DRAMS login email (users.email) on its own line.
 * Matching ignores upper/lower case. Anyone not listed does not see the
 * tab and is redirected to the dashboard if they open /mailbox directly.
 */
return array(
    'allowed_emails' => array(
        'junaid.pk18@gmail.com',
        'ali.razapu@gmail.com',
    ),
);
