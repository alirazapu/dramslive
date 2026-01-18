<?php

require APPPATH . DIRECTORY_SEPARATOR . 'classes/Controller/gmail_api/vendor/autoload.php';

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;

class Controller_Gmailapi extends Controller
{
    private function getGoogleClient()
    {
        $client = new Client();
        $client->setApplicationName('Gmail Test App');
        $client->setScopes([
            'https://www.googleapis.com/auth/gmail.compose',
            'https://www.googleapis.com/auth/gmail.send',
            'https://www.googleapis.com/auth/gmail.insert',
            'https://www.googleapis.com/auth/gmail.readonly',
            // 'https://www.googleapis.com/auth/documents' // uncomment if needed
        ]);
        $client->setAuthConfig(APPPATH . DIRECTORY_SEPARATOR . 'classes/Controller/gmail_api/credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // IMPORTANT: Must match exactly what is in Google Console Authorized redirect URIs
        $client->setRedirectUri(URL::base(TRUE) .('gmailapi/callback'));

        $tokenPath = APPPATH . DIRECTORY_SEPARATOR . 'classes/Controller/gmail_api/token.json';

        if (file_exists($tokenPath)) {
            $tokenContent = file_get_contents($tokenPath);
            $accessToken = json_decode($tokenContent, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($accessToken) && !empty($accessToken['access_token'])) {
                $client->setAccessToken($accessToken);
            } else {
                // Invalid token file → force re-auth
                @unlink($tokenPath);
                header('Location: ' . URL::site('gmailapi/authorize'));
                exit;
            }
        }

        // Auto-refresh if expired
        if ($client->isAccessTokenExpired() && $client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());

            // Save refreshed token
            file_put_contents($tokenPath, json_encode($client->getAccessToken(), JSON_PRETTY_PRINT));
        }

        return $client;
    }

    public function action_authorize()
    {
        $client = $this->getGoogleClient();
        $authUrl = $client->createAuthUrl();
        header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
        exit;
    }

    public function action_callback()
    {
        $client = $this->getGoogleClient();

        if (isset($_GET['code'])) {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

            if (isset($token['error'])) {
                die('Error: ' . htmlspecialchars($token['error_description']));
            }

            // Save full token (includes refresh_token on first auth)
            $tokenPath = APPPATH . DIRECTORY_SEPARATOR . 'classes/Controller/gmail_api/token.json';
            file_put_contents($tokenPath, json_encode($token, JSON_PRETTY_PRINT));

            echo "Success! Gmail connected. Token saved.<br>";
            echo "<a href='" . URL::site('gmailapi/send_email') . "'>Test Send Email</a>";
        } else {
            echo "Callback error - no code received.";
        }
    }

    public function action_send_email()
    {
        $send_type = 1;  // 1: without file, 2: with file

        try {
            $client = $this->getGoogleClient();  // ← This handles load + refresh
            $service = new Gmail($client);

            // Create and send message
            $create_email = $this->createMessage(
                'dcsctd@gmail.com',
                'ali.razapu@gmail.com',
                "Separate action method - " . date('Y-m-d H:i:s'),
                'Your message body here...',
                $send_type
            );

            $sent = $service->users_messages->send('me', $create_email);

            echo "Email sent successfully! Message ID: " . $sent->getId();
        } catch (Exception $e) {
            echo 'Error sending email: ' . htmlspecialchars($e->getMessage());
            if (strpos($e->getMessage(), 'invalid_grant') !== false || strpos($e->getMessage(), '401') !== false) {
                echo "<br>Token invalid/expired. <a href='" . URL::site('gmailapi/authorize') . "'>Re-authorize</a>";
            }
        }
    }

    public function action_read_email()
    {
        $receive_type = 2;  // 1: without file, 2: with file
        $email_type   = 2;  // 1: with timestamp, 2: without time
        $email_status = 2;  // 1: unread, 2: all

        try {
            $client = $this->getGoogleClient();
            $service = new Gmail($client);

            // Choose which list to use
            $show_emails          = $this->allInboxMessage($service, 15, 'INBOX');
            $latest_mails         = $this->newemails($service, 'me', 5);
            $time_limit_latest    = $this->newEmailsWithTimeLimit($service, 'me', '-15 minutes', 100);

            if ($email_type == 1) {
                $list = $time_limit_latest->getMessages() ?? [];
            } else {
                if ($email_status == 1) {
                    $list = $latest_mails->getMessages() ?? [];
                } else {
                    $list = $show_emails->getMessages() ?? [];
                }
            }

            if (empty($list)) {
                echo "No messages found.";
                return;
            }

            foreach ($list as $msg) {
                $messageId = $msg->getId();
                $message = $service->users_messages->get('me', $messageId, ['format' => 'full']);

                $headers = $message->getPayload()->getHeaders();

                // Get subject
                $subjectHeader = array_filter($headers, fn($h) => $h['name'] === 'Subject');
                $subject = !empty($subjectHeader) ? reset($subjectHeader)->getValue() : '(No subject)';

                echo "<strong>Subject:</strong> " . htmlspecialchars($subject) . "<br>";

                // Get body (prefer text/plain or html)
                $payload = $message->getPayload();
                $body = $this->getMessageBody($payload);

                echo "<strong>Body:</strong><br>" . nl2br(htmlspecialchars($body)) . "<br><hr>";

                // Attachments (if receive_type == 2)
                if ($receive_type == 2) {
                    $this->saveAttachments($service, $messageId, $payload);
                }
            }
        } catch (Exception $e) {
            echo 'Error reading emails: ' . htmlspecialchars($e->getMessage());
        }
    }

    // Helper: Extract readable body (text or html)
    private function getMessageBody($payload)
    {
        $body = '';

        // Try direct body
        if ($payload->getBody() && $payload->getBody()->getData()) {
            $data = $payload->getBody()->getData();
            $body = $this->decodeBase64Url($data);
        }

        // Or from parts (most common)
        if (empty($body) && $payload->getParts()) {
            foreach ($payload->getParts() as $part) {
                if ($part->getMimeType() === 'text/plain' && $part->getBody() && $part->getBody()->getData()) {
                    $body = $this->decodeBase64Url($part->getBody()->getData());
                    break;
                }
                if ($part->getMimeType() === 'text/html' && $part->getBody() && $part->getBody()->getData()) {
                    $body = $this->decodeBase64Url($part->getBody()->getData());
                    // You can use HTML purifier here if needed
                }
            }
        }

        return $body ?: '(No body content)';
    }

    private function decodeBase64Url($data)
    {
        $data = strtr($data, '-_', '+/');
        return base64_decode($data);
    }

    // Helper: Save attachments to disk
    private function saveAttachments($service, $messageId, $payload)
    {
        $attachments = [];

        $parts = $payload->getParts() ?? [];

        foreach ($parts as $part) {
            if ($part->getFilename() && $part->getBody() && $part->getBody()->getAttachmentId()) {
                $attachment = $service->users_messages_attachments->get('me', $messageId, $part->getBody()->getAttachmentId());

                $data = $this->decodeBase64Url($attachment->getData());
                $filename = $part->getFilename();

                $path = DOCUMENT_ROOT."\uploads\gmailapi_files\\" . $filename;

                file_put_contents($path, $data);

                echo "Attachment saved: <a href='/drams/uploads/gmailapi_files/" . urlencode($filename) . "' target='_blank'>" . htmlspecialchars($filename) . "</a><br>";
            }
        }
    }

    // Your createMessage function (minor cleanup)
    function createMessage($sender, $to, $subject, $messageText, $send_type)
    {
        $message = new Message();

        if ($send_type == 1) {
            // Simple text/html
            $raw = "From: <$sender>\r\n";
            $raw .= "To: <$to>\r\n";
            $raw .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "?=\r\n";
            $raw .= "MIME-Version: 1.0\r\n";
            $raw .= "Content-Type: text/html; charset=utf-8\r\n";
            $raw .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
            $raw .= $messageText . "\r\n";

            $raw = strtr(base64_encode($raw), '+/', '-_');
            $message->setRaw($raw);
        } elseif ($send_type == 2) {
            // With attachment (your original code, kept similar)
            $boundary = uniqid(rand(), true);
            $charset = 'utf-8';

            $strRaw = 'To: ' . $to . "\r\n";
            $strRaw .= 'From: ' . $sender . "\r\n";
            $strRaw .= 'Subject: =?' . $charset . '?B?' . base64_encode($subject) . "?=\r\n";
            $strRaw .= 'MIME-Version: 1.0' . "\r\n";
            $strRaw .= 'Content-type: Multipart/Mixed; boundary="' . $boundary . '"' . "\r\n\r\n";

            $filePath = '/root/Downloads/Accused File.xlsx';  // ← Update this path!
            if (!file_exists($filePath)) {
                throw new Exception("Attachment file not found: $filePath");
            }

            $mimeType = mime_content_type($filePath);
            $fileName = basename($filePath);
            $fileData = chunk_split(base64_encode(file_get_contents($filePath)));

            $strRaw .= "--$boundary\r\n";
            $strRaw .= "Content-Type: $mimeType; name=\"$fileName\"\r\n";
            $strRaw .= "Content-Disposition: attachment; filename=\"$fileName\"\r\n";
            $strRaw .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $strRaw .= $fileData . "\r\n";
            $strRaw .= "--$boundary\r\n";
            $strRaw .= "Content-Type: text/html; charset=$charset\r\n";
            $strRaw .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
            $strRaw .= $messageText . "\r\n";
            $strRaw .= "--$boundary--";

            $raw = strtr(base64_encode($strRaw), '+/', '-_');
            $message->setRaw($raw);
        }

        return $message;
    }

    // Your other helpers (unchanged)
    function allInboxMessage($service, $noOfMsg, $label)
    {
        $optParams = ['maxResults' => $noOfMsg, 'labelIds' => $label];
        return $service->users_messages->listUsersMessages('me', $optParams);
    }

    function newemails($service, $userid, $noOfMsg)
    {
        return $service->users_messages->listUsersMessages($userid, ['maxResults' => $noOfMsg, 'q' => "in:inbox is:unread"]);
    }

    function newEmailsWithTimeLimit($service, $userid, $timerange, $noOfMsg)
    {
        date_default_timezone_set('Asia/Karachi');
        $limit = strtotime(date('Y-m-d H:i:s', strtotime($timerange)));
        $q = "in:inbox after:$limit";
        return $service->users_messages->listUsersMessages($userid, ['maxResults' => $noOfMsg, 'q' => $q]);
    }
}