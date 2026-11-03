<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Read-only Gmail mailbox (IMAP) + compose (SMTP) for the admin Mailbox tab.
 *
 * Always the DCS account (DB token 5) - in every environment, for both
 * reading and sending, so mail composed here lands in its Sent folder.
 *
 * Everything reads with FT_PEEK on an OP_READONLY stream, so browsing never
 * marks a message as read - Helpers_Email::receive_email() relies on UNSEEN.
 */
abstract class Helpers_Mailbox
{
    const PER_PAGE = 100;
    const HOST = '{imap.gmail.com:993/imap/ssl}';
    const SENDER_NAME = 'Addl IG';

    public static $folders = array(
        'inbox'   => array('name' => 'Inbox',    'mailbox' => 'INBOX',             'icon' => 'fa-inbox'),
        'starred' => array('name' => 'Starred',  'mailbox' => '[Gmail]/Starred',   'icon' => 'fa-star'),
        'sent'    => array('name' => 'Sent',     'mailbox' => '[Gmail]/Sent Mail', 'icon' => 'fa-send'),
        'all'     => array('name' => 'All Mail', 'mailbox' => '[Gmail]/All Mail',  'icon' => 'fa-envelope'),
        'spam'    => array('name' => 'Spam',     'mailbox' => '[Gmail]/Spam',      'icon' => 'fa-exclamation-circle'),
    );

    private static $credentials = NULL;

    /** Hits the DB and decrypts, so load it once per request. */
    private static function credentials()
    {
        if (self::$credentials === NULL) {
            self::$credentials = Helpers_Inneruse::get_db_send_account();
        }
        return self::$credentials;
    }

    /** TRUE when the logged-in user's email is listed in config/mailbox.php. */
    public static function can_access()
    {
        $user = Auth::instance()->get_user();
        if (!$user) {
            return FALSE;
        }
        $email = strtolower(trim((string) ORM::factory('User', $user)->email));
        $allowed = array_map('strtolower', array_map('trim', (array) Kohana::$config->load('mailbox')->get('allowed_emails', array())));
        return $email !== '' && in_array($email, $allowed, TRUE);
    }

    public static function folder_key($key)
    {
        return isset(self::$folders[$key]) ? $key : 'inbox';
    }

    /**
     * Opens the mailbox account on the given folder (read-only).
     * Throws on failure so the page can show the IMAP error.
     */
    public static function open($folder_key)
    {
        $folder = self::$folders[self::folder_key($folder_key)];
        $result = self::credentials();

        imap_timeout(IMAP_OPENTIMEOUT, 20);
        imap_timeout(IMAP_READTIMEOUT, 60);

        $inbox = @imap_open(self::HOST . $folder['mailbox'], $result['user'], $result['password'], OP_READONLY, 1);
        if (!$inbox) {
            $error = imap_last_error();
            imap_errors();
            imap_alerts();
            throw new Exception('Could not open Gmail ' . $folder['name'] . ': ' . $error);
        }
        return $inbox;
    }

    public static function close($inbox)
    {
        if ($inbox) {
            imap_errors();
            imap_alerts();
            imap_close($inbox);
        }
    }

    public static function account()
    {
        $result = self::credentials();
        return $result['user'];
    }

    public static function unread_count($inbox)
    {
        $status = @imap_status($inbox, self::HOST . 'INBOX', SA_UNSEEN);
        return $status ? (int) $status->unseen : 0;
    }

    /**
     * One page of messages, newest first.
     * Returns array('total' => int, 'messages' => array of rows).
     */
    public static function list_messages($inbox, $page, $search = '')
    {
        $page = max(1, (int) $page);
        $offset = ($page - 1) * self::PER_PAGE;
        $overview = array();

        if ($search !== '') {
            // Quotes/backslashes would break the IMAP search string
            $term = str_replace(array('"', '\\'), '', $search);
            $uids = imap_search($inbox, 'TEXT "' . $term . '"', SE_UID, 'UTF-8');
            $uids = $uids ? $uids : array();
            rsort($uids);
            $total = count($uids);
            $page_uids = array_slice($uids, $offset, self::PER_PAGE);
            if ($page_uids) {
                $overview = imap_fetch_overview($inbox, implode(',', $page_uids), FT_UID);
            }
        } else {
            // Sequence numbers are in arrival order, so the newest are the highest
            $total = imap_num_msg($inbox);
            $last = $total - $offset;
            $first = max(1, $last - self::PER_PAGE + 1);
            if ($last >= 1) {
                $overview = imap_fetch_overview($inbox, $first . ':' . $last, 0);
            }
        }

        $messages = array();
        foreach ((array) $overview as $row) {
            $messages[] = array(
                'uid'     => (int) $row->uid,
                'from'    => self::decode_header(isset($row->from) ? $row->from : ''),
                'to'      => self::decode_header(isset($row->to) ? $row->to : ''),
                'subject' => self::decode_header(isset($row->subject) ? $row->subject : ''),
                'date'    => isset($row->udate) ? (int) $row->udate : strtotime(isset($row->date) ? $row->date : 'now'),
                'seen'    => !empty($row->seen),
                'flagged' => !empty($row->flagged),
            );
        }
        usort($messages, function ($a, $b) {
            return $b['uid'] - $a['uid'];
        });

        return array('total' => $total, 'messages' => $messages);
    }

    /**
     * Which of $uids have attachments, using Gmail's own "has:attachment"
     * (same rule as Gmail's paperclip). php-imap cannot send X-GM-RAW and a
     * per-message fetchstructure costs ~0.5s each, so this is one raw IMAP
     * search over its own read-only (EXAMINE) connection.
     * Returns array(uid => TRUE); empty on any failure (icons just don't show).
     */
    public static function attachment_uids($folder_key, array $uids)
    {
        if (empty($uids)) {
            return array();
        }
        $folder = self::$folders[self::folder_key($folder_key)];
        $account = self::credentials();
        $quote = function ($value) {
            return '"' . addcslashes($value, '"' . chr(92)) . '"';
        };

        $fp = @stream_socket_client('ssl://imap.gmail.com:993', $errno, $errstr, 15);
        if (!$fp) {
            return array();
        }
        stream_set_timeout($fp, 20);
        $command = function ($tag, $line) use ($fp) {
            fwrite($fp, $tag . ' ' . $line . "\r\n");
            $response = '';
            while (($row = fgets($fp)) !== FALSE) {
                $response .= $row;
                if (strpos($row, $tag . ' ') === 0) {
                    break;
                }
            }
            return $response;
        };

        $found = array();
        fgets($fp); // greeting
        $login = $command('a1', 'LOGIN ' . $quote($account['user']) . ' ' . $quote($account['password']));
        if (strpos($login, 'a1 OK') !== FALSE && strpos($command('a2', 'EXAMINE ' . $quote($folder['mailbox'])), 'a2 OK') !== FALSE) {
            $response = $command('a3', 'UID SEARCH UID ' . implode(',', array_map('intval', $uids)) . ' X-GM-RAW "has:attachment"');
            if (preg_match('/^\* SEARCH([\d ]*)/m', $response, $m)) {
                foreach (preg_split('/\s+/', trim($m[1]), -1, PREG_SPLIT_NO_EMPTY) as $uid) {
                    $found[(int) $uid] = TRUE;
                }
            }
        }
        $command('a4', 'LOGOUT');
        fclose($fp);
        return $found;
    }

    /**
     * Full message: headers, best body (HTML preferred) and attachment list.
     */
    public static function get_message($inbox, $uid)
    {
        $msgno = imap_msgno($inbox, $uid);
        if (!$msgno) {
            return NULL;
        }
        $header = imap_headerinfo($inbox, $msgno);
        $structure = imap_fetchstructure($inbox, $uid, FT_UID);
        if (!$header || !$structure) {
            return NULL;
        }

        $message = array(
            'uid'         => (int) $uid,
            'subject'     => self::decode_header(isset($header->subject) ? $header->subject : ''),
            'from'        => self::decode_header(isset($header->fromaddress) ? $header->fromaddress : ''),
            'from_email'  => self::first_address(isset($header->from) ? $header->from : array()),
            'reply_to'    => self::first_address(isset($header->reply_to) ? $header->reply_to : array()),
            'to'          => self::decode_header(isset($header->toaddress) ? $header->toaddress : ''),
            'cc'          => self::decode_header(isset($header->ccaddress) ? $header->ccaddress : ''),
            'date'        => isset($header->udate) ? (int) $header->udate : 0,
            'message_id'  => isset($header->message_id) ? trim($header->message_id) : '',
            'references'  => isset($header->references) ? trim($header->references) : '',
            'html'        => '',
            'text'        => '',
            'attachments' => array(),
        );

        if (empty($structure->parts)) {
            self::read_part($inbox, $uid, $structure, '1', $message, TRUE);
        } else {
            foreach ($structure->parts as $i => $part) {
                self::walk_part($inbox, $uid, $part, (string) ($i + 1), $message);
            }
        }
        return $message;
    }

    private static function walk_part($inbox, $uid, $part, $number, &$message)
    {
        if (!empty($part->parts) && $part->type == TYPEMULTIPART) {
            foreach ($part->parts as $i => $sub) {
                self::walk_part($inbox, $uid, $sub, $number . '.' . ($i + 1), $message);
            }
            return;
        }
        self::read_part($inbox, $uid, $part, $number, $message, FALSE);
    }

    private static function read_part($inbox, $uid, $part, $number, &$message, $single)
    {
        $filename = self::part_filename($part);
        $subtype = strtolower(isset($part->subtype) ? $part->subtype : '');

        if ($filename !== '' || ($part->type != TYPETEXT && $part->type != TYPEMULTIPART)) {
            $message['attachments'][] = array(
                'part' => $number,
                'name' => $filename !== '' ? $filename : 'attachment-' . $number,
                'size' => isset($part->bytes) ? (int) $part->bytes : 0,
            );
            return;
        }

        if ($subtype !== 'html' && $subtype !== 'plain') {
            return;
        }
        $key = $subtype === 'html' ? 'html' : 'text';
        if ($message[$key] !== '') {
            return;
        }
        $raw = $single
            ? imap_body($inbox, $uid, FT_UID | FT_PEEK)
            : imap_fetchbody($inbox, $uid, $number, FT_UID | FT_PEEK);
        $message[$key] = self::to_utf8(self::decode_body($raw, $part->encoding), self::part_charset($part));
    }

    /**
     * Raw attachment bytes for download. Returns array(name, mime, data) or NULL.
     */
    public static function get_attachment($inbox, $uid, $part_number)
    {
        $structure = imap_fetchstructure($inbox, $uid, FT_UID);
        if (!$structure || !preg_match('/^\d+(\.\d+)*$/', $part_number)) {
            return NULL;
        }
        $part = $structure;
        foreach (explode('.', $part_number) as $index) {
            if (empty($part->parts[$index - 1])) {
                return NULL;
            }
            $part = $part->parts[$index - 1];
        }
        $types = array('text', 'multipart', 'message', 'application', 'audio', 'image', 'video', 'model', 'other');
        $mime = (isset($types[$part->type]) ? $types[$part->type] : 'application') . '/' . strtolower($part->subtype);
        $name = self::part_filename($part);

        return array(
            'name' => $name !== '' ? $name : 'attachment-' . $part_number,
            'mime' => $mime,
            'data' => self::decode_body(imap_fetchbody($inbox, $uid, $part_number, FT_UID | FT_PEEK), $part->encoding),
        );
    }

    /**
     * Sends from the DCS account via Gmail SMTP (sending.inc settings).
     * $attachments: array of array('path' => tmp path, 'name' => original name).
     * Returns TRUE or the PHPMailer error string.
     */
    public static function send($to, $cc, $subject, $body, $attachments = array(), $in_reply_to = '', $references = '')
    {
        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        $to_name = '';
        include 'gmail/sending.inc';

        // sending.inc uses the environment's account; the mailbox is always DCS
        $account = self::credentials();
        $mail->Username = $account['user'];
        $mail->Password = $account['password'];
        $mail->setFrom($account['user'], self::SENDER_NAME);

        foreach (array_filter(array_map('trim', preg_split('/[,;]+/', (string) $cc))) as $cc_address) {
            $mail->addCC($cc_address);
        }
        foreach ($attachments as $file) {
            $mail->addAttachment($file['path'], $file['name']);
        }
        if ($in_reply_to !== '') {
            $mail->addCustomHeader('In-Reply-To', $in_reply_to);
            $mail->addCustomHeader('References', trim($references . ' ' . $in_reply_to));
        }

        $sent = $mail->Send();
        Model_ErrorLog::log(
            'mailbox_send',
            $sent ? 'Mailbox email sent' : 'Mailbox email failed: ' . $mail->ErrorInfo,
            array('to' => $to, 'cc' => $cc, 'subject' => substr($subject, 0, 100), 'attachments' => count($attachments)),
            null,
            $sent ? 'smtp_send_success' : 'smtp_send_failure',
            'email_sending',
            $sent ? 'success' : 'error'
        );
        return $sent ? TRUE : $mail->ErrorInfo;
    }

    public static function decode_header($value)
    {
        if ($value === '' || $value === NULL) {
            return '';
        }
        $decoded = @iconv_mime_decode($value, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
        return $decoded !== FALSE ? $decoded : $value;
    }

    /** "Name <a@b.com>" -> "Name"; bare address stays as is. */
    public static function display_name($address)
    {
        if (preg_match('/^\s*"?([^"<]+?)"?\s*<[^>]+>/', $address, $m)) {
            return trim($m[1]);
        }
        return trim($address, " <>");
    }

    /** Gmail-style short date: time today, "Nov 2" this year, else d/m/Y. */
    public static function short_date($timestamp)
    {
        if (date('Y-m-d', $timestamp) === date('Y-m-d')) {
            return date('g:i A', $timestamp);
        }
        if (date('Y', $timestamp) === date('Y')) {
            return date('M j', $timestamp);
        }
        return date('d/m/Y', $timestamp);
    }

    public static function format_size($bytes)
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        return max(1, round($bytes / 1024)) . ' KB';
    }

    private static function first_address($list)
    {
        if (empty($list[0]->mailbox) || empty($list[0]->host)) {
            return '';
        }
        return $list[0]->mailbox . '@' . $list[0]->host;
    }

    private static function decode_body($data, $encoding)
    {
        if ($encoding == ENCBASE64) {
            return base64_decode($data);
        }
        if ($encoding == ENCQUOTEDPRINTABLE) {
            return quoted_printable_decode($data);
        }
        return $data;
    }

    private static function to_utf8($text, $charset)
    {
        $charset = strtoupper($charset);
        if ($charset === '' || $charset === 'UTF-8' || $charset === 'US-ASCII') {
            return $text;
        }
        $converted = @iconv($charset, 'UTF-8//IGNORE', $text);
        return $converted !== FALSE ? $converted : $text;
    }

    private static function part_param($part, $field, $attribute)
    {
        $flag = 'if' . $field;
        if (!empty($part->$flag)) {
            foreach ($part->$field as $param) {
                if (strtolower($param->attribute) === $attribute) {
                    return $param->value;
                }
            }
        }
        return '';
    }

    private static function part_charset($part)
    {
        return self::part_param($part, 'parameters', 'charset');
    }

    private static function part_filename($part)
    {
        $name = self::part_param($part, 'dparameters', 'filename');
        if ($name === '') {
            $name = self::part_param($part, 'parameters', 'name');
        }
        return self::decode_header($name);
    }
}
