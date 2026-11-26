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
        'azmatsaani@gmail.com',     // azmatsaani (Administrator)
        'coordinate.wa@gmail.com',  // waqasahmad (Administrator)
        'zahidgulcpo@gmail.com',    // zahidgul
        'rizwanahmadkhalil77@gmail.com', // rizwandcs
        'nadirchamkani1989@gmail.com',   // nadirdcs
        'israrud90@gmail.com',           // israruddin
        'nazeer0015@gmail.com',          // nazeerkhan
    ),

    // Who can see the "Sent Log" page (which user sent each email).
    // These users must also be listed in allowed_emails above.
    'log_viewer_emails' => array(
        'junaid.pk18@gmail.com',    // junaidpk
    ),
);
