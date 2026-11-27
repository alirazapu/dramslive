<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Gmail refuses mail ("Daily user sending limit exceeded") once the account has
 * sent about 2,000 emails in the last 24 hours.
 *
 * Automatic request sending pauses when DRAMS has sent 'max_per_24_hours' emails
 * in the last 24 hours. Held requests stay queued and go out as older sends
 * drop out of the window. The gap below Gmail's limit is kept for login OTP,
 * Mailbox and manual sends, which are counted but never blocked.
 */
return array(
    'max_per_24_hours' => 1700,
);
