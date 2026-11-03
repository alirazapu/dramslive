<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Gmail-style mailbox for the account DRAMS uses to talk to the telcos.
 * Only the users listed in config/mailbox.php. Reading is read-only - see Helpers_Mailbox.
 */
class Controller_Mailbox extends Controller_Working
{
    const MAX_ATTACHMENT_BYTES = 20971520; // 20 MB, below Gmail's 25 MB limit

    public function before()
    {
        parent::before();
        if (!Helpers_Mailbox::can_access()) {
            $this->redirect('Userdashboard/dashboard');
        }
    }

    public function action_index()
    {
        $folder = Helpers_Mailbox::folder_key(Arr::get($_GET, 'folder', 'inbox'));
        $search = trim((string) Arr::get($_GET, 'q', ''));
        $page = max(1, (int) Arr::get($_GET, 'page', 1));

        $view = View::factory('templates/mailbox/index')
            ->set('folder', $folder)
            ->set('search', $search)
            ->set('page', $page)
            ->set('per_page', Helpers_Mailbox::PER_PAGE)
            ->set('total', 0)
            ->set('messages', array())
            ->set('unread', 0)
            ->set('error', '');

        $inbox = NULL;
        try {
            $inbox = Helpers_Mailbox::open($folder);
            $result = Helpers_Mailbox::list_messages($inbox, $page, $search);
            $with_files = Helpers_Mailbox::attachment_uids($folder, array_map(function ($m) {
                return $m['uid'];
            }, $result['messages']));
            foreach ($result['messages'] as &$m) {
                $m['attachment'] = isset($with_files[$m['uid']]);
            }
            unset($m);
            $view->set('total', $result['total'])
                ->set('messages', $result['messages'])
                ->set('unread', Helpers_Mailbox::unread_count($inbox))
                ->set('account', Helpers_Mailbox::account());
        } catch (Exception $e) {
            $view->set('error', $e->getMessage());
        }
        Helpers_Mailbox::close($inbox);

        $this->template->content = $view;
    }

    public function action_view()
    {
        $folder = Helpers_Mailbox::folder_key(Arr::get($_GET, 'folder', 'inbox'));
        $uid = (int) Arr::get($_GET, 'uid', 0);

        $view = View::factory('templates/mailbox/view')
            ->set('folder', $folder)
            ->set('back_query', $this->back_query())
            ->set('message', NULL)
            ->set('error', '');

        $inbox = NULL;
        try {
            $inbox = Helpers_Mailbox::open($folder);
            $message = $uid ? Helpers_Mailbox::get_message($inbox, $uid) : NULL;
            if ($message) {
                $view->set('message', $message);
            } else {
                $view->set('error', 'This email could not be found. It may have been moved or deleted.');
            }
        } catch (Exception $e) {
            $view->set('error', $e->getMessage());
        }
        Helpers_Mailbox::close($inbox);

        $this->template->content = $view;
    }

    public function action_attachment()
    {
        $folder = Helpers_Mailbox::folder_key(Arr::get($_GET, 'folder', 'inbox'));
        $uid = (int) Arr::get($_GET, 'uid', 0);
        $part = (string) Arr::get($_GET, 'part', '');

        $inbox = NULL;
        $file = NULL;
        try {
            $inbox = Helpers_Mailbox::open($folder);
            $file = Helpers_Mailbox::get_attachment($inbox, $uid, $part);
        } catch (Exception $e) {
        }
        Helpers_Mailbox::close($inbox);

        if (!$file) {
            throw HTTP_Exception::factory(404, 'Attachment not found');
        }
        $this->auto_render = FALSE;
        $this->response->body($file['data']);
        $this->response->send_file(TRUE, $file['name'], array('mime_type' => $file['mime']));
    }

    /**
     * Compose form. ?reply=<uid>&folder=<key> pre-fills a reply,
     * &all=1 replies to everyone, ?forward=<uid> forwards the text.
     */
    public function action_compose()
    {
        $form = array(
            'to' => '', 'cc' => '', 'subject' => '', 'body' => '',
            'in_reply_to' => '', 'references' => '',
        );
        $error = '';
        $folder = Helpers_Mailbox::folder_key(Arr::get($_GET, 'folder', 'inbox'));
        $source_uid = (int) Arr::get($_GET, 'reply', Arr::get($_GET, 'forward', 0));

        if ($source_uid) {
            $inbox = NULL;
            try {
                $inbox = Helpers_Mailbox::open($folder);
                $original = Helpers_Mailbox::get_message($inbox, $source_uid);
                if ($original) {
                    $form = $this->prefill($form, $original, Arr::get($_GET, 'forward') ? 'forward' : (Arr::get($_GET, 'all') ? 'all' : 'reply'));
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            Helpers_Mailbox::close($inbox);
        }

        $this->template->content = View::factory('templates/mailbox/compose')
            ->set('form', $form)
            ->set('error', $error);
    }

    public function action_send()
    {
        if ($this->request->method() !== Request::POST) {
            $this->redirect('mailbox/compose');
        }

        $form = array(
            'to'          => trim((string) Arr::get($_POST, 'to', '')),
            'cc'          => trim((string) Arr::get($_POST, 'cc', '')),
            'subject'     => trim((string) Arr::get($_POST, 'subject', '')),
            'body'        => (string) Arr::get($_POST, 'body', ''),
            'in_reply_to' => trim((string) Arr::get($_POST, 'in_reply_to', '')),
            'references'  => trim((string) Arr::get($_POST, 'references', '')),
        );

        $error = '';
        if (!Security::check(Arr::get($_POST, 'csrf'))) {
            $error = 'Your session token expired. Please send again.';
        } elseif (($bad = $this->invalid_addresses($form['to'], TRUE)) !== '') {
            $error = 'Invalid "To" address: ' . $bad;
        } elseif (($bad = $this->invalid_addresses($form['cc'], FALSE)) !== '') {
            $error = 'Invalid "Cc" address: ' . $bad;
        } elseif ($form['subject'] === '') {
            $error = 'Please enter a subject.';
        }

        $attachments = array();
        if ($error === '') {
            list($attachments, $error) = $this->uploaded_files();
        }

        if ($error === '') {
            // Plain-text body sent as HTML, so line breaks survive
            $html = '<div style="font-family:Arial,sans-serif;font-size:14px;">'
                . nl2br(htmlspecialchars($form['body'], ENT_QUOTES, 'UTF-8')) . '</div>';
            // Header values must not carry line breaks into the SMTP headers
            $in_reply_to = preg_replace('/[\r\n]+/', ' ', $form['in_reply_to']);
            $references = preg_replace('/[\r\n]+/', ' ', $form['references']);

            $sent = Helpers_Mailbox::send($form['to'], $form['cc'], $form['subject'], $html, $attachments, $in_reply_to, $references);
            if ($sent === TRUE) {
                Session::instance()->set('mailbox_success', 'Email sent to ' . $form['to']);
                $this->redirect('mailbox?folder=sent');
            }
            $error = 'Gmail could not send the email: ' . $sent;
        }

        $this->template->content = View::factory('templates/mailbox/compose')
            ->set('form', $form)
            ->set('error', $error);
    }

    private function prefill($form, $original, $mode)
    {
        $subject = $original['subject'];
        $quote_text = $original['text'] !== ''
            ? $original['text']
            : html_entity_decode(strip_tags(preg_replace('/<(br|\/p|\/div)[^>]*>/i', "\n", $original['html'])), ENT_QUOTES, 'UTF-8');
        $quote_text = trim(preg_replace("/\n{3,}/", "\n\n", $quote_text));
        $when = $original['date'] ? date('D, M j, Y \a\t g:i A', $original['date']) : '';

        if ($mode === 'forward') {
            $form['subject'] = preg_match('/^fwd?:/i', $subject) ? $subject : 'Fwd: ' . $subject;
            $form['body'] = "\n\n---------- Forwarded message ---------\n"
                . 'From: ' . $original['from'] . "\nDate: " . $when . "\nSubject: " . $subject
                . "\nTo: " . $original['to'] . "\n\n" . $quote_text;
            return $form;
        }

        $form['to'] = $original['reply_to'] !== '' ? $original['reply_to'] : $original['from_email'];
        // Replying to our own sent mail goes to its recipients, like Gmail
        if (strcasecmp($original['from_email'], Helpers_Mailbox::account()) === 0) {
            preg_match_all('/[^\s<>",]+@[^\s<>",]+/', $original['to'], $m);
            $form['to'] = implode(', ', $m[0]);
        }
        if ($mode === 'all') {
            $mine = array(strtolower(Helpers_Mailbox::account()), strtolower($form['to']));
            $others = array();
            foreach (preg_split('/,/', $original['to'] . ',' . $original['cc']) as $address) {
                if (preg_match('/[^\s<>"]+@[^\s<>"]+/', $address, $m)) {
                    $email = strtolower($m[0]);
                    if (!in_array($email, $mine)) {
                        $others[$email] = $email;
                    }
                }
            }
            $form['cc'] = implode(', ', $others);
        }
        $form['subject'] = preg_match('/^re:/i', $subject) ? $subject : 'Re: ' . $subject;
        $form['in_reply_to'] = $original['message_id'];
        $form['references'] = $original['references'];
        $form['body'] = "\n\nOn " . $when . ', ' . $original['from'] . " wrote:\n"
            . preg_replace('/^/m', '> ', $quote_text);
        return $form;
    }

    /** Returns the first invalid address, or '' when all are valid. */
    private function invalid_addresses($list, $required)
    {
        $addresses = array_filter(array_map('trim', preg_split('/[,;]+/', $list)));
        if (empty($addresses)) {
            return $required ? '(empty)' : '';
        }
        foreach ($addresses as $address) {
            if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
                return $address;
            }
        }
        return '';
    }

    /** Returns array(attachments, error). */
    private function uploaded_files()
    {
        $attachments = array();
        $total = 0;
        if (empty($_FILES['attachments']['name']) || !is_array($_FILES['attachments']['name'])) {
            return array($attachments, '');
        }
        foreach ($_FILES['attachments']['name'] as $i => $name) {
            $code = $_FILES['attachments']['error'][$i];
            if ($code === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($code !== UPLOAD_ERR_OK || !is_uploaded_file($_FILES['attachments']['tmp_name'][$i])) {
                return array(array(), 'Upload of "' . $name . '" failed.');
            }
            $total += (int) $_FILES['attachments']['size'][$i];
            $attachments[] = array(
                'path' => $_FILES['attachments']['tmp_name'][$i],
                'name' => basename($name),
            );
        }
        if ($total > self::MAX_ATTACHMENT_BYTES) {
            return array(array(), 'Attachments are larger than 20 MB in total.');
        }
        return array($attachments, '');
    }

    private function back_query()
    {
        return http_build_query(array_filter(array(
            'folder' => Arr::get($_GET, 'folder'),
            'q'      => Arr::get($_GET, 'q'),
            'page'   => Arr::get($_GET, 'page'),
        )));
    }
}
