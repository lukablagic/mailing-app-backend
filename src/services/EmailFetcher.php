<?php

require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Ddeboer\Imap\Server;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\Search\Email\To;
use Ddeboer\Imap\Search\Text\Body;
use Ddeboer\Imap\Connection;
use Ddeboer\Imap\Message\EmailAddress;
use Ddeboer\Imap\Message\Attachment;
use \Ddeboer\Imap\Search\Flag\Unseen;
use \Ddeboer\imap\MailboxInterface;

class EmailFetcher
{

    private $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
    public $attachmentGateway;
    public $userGateway;
    public $mailGateway;

    public function __construct(\Attachment $attachmentGateway, Mail $mailGateway, User $userGateway)
    {
//        $config = parse_ini_file('.env');
//        $this->hostname = $config[`IMAP_HOST`];
        $this->mailGateway = $mailGateway;
        $this->userGateway = $userGateway;
        $this->attachmentGateway = $attachmentGateway;

    }

    public function saveEmail(\Ddeboer\Imap\MessageInterface $message, $email, $password)
    {
        $emailData = new stdClass();


        $emailData->in_reply_to = $message->getInReplyTo();
        $emailData->references = $message->getReferences();
       // var_dump($emailData->in_reply_to);
        if ($emailData->in_reply_to == null) {
            $emailData->in_reply_to = [];
        }
        $emailData->uid = $message->getId();
        $emailData->from = $message->getFrom()->getAddress();
        $emailData->to = $message->getTo();
        $emailData->cc = $message->getCc();
        $emailData->bcc = $message->getBcc();
        //     var_dump($emailData->cc);

        $emailData->body = $message->getBodyHtml();

        if ($emailData->body == null) {
            $emailData->body = $message->getBodyText();
        }

        $emailData->has_attachemnt = $message->hasAttachments() ? 1 : 0;
        $emailData->replied_to = null;
        $emailData->sent_date = $message->getDate()->format('Y-m-d H:i:s');
        $emailData->is_read = $message->isSeen();
        $emailData->has_attachment = (count($message->getAttachments()) > 0) ? 1 : 0;
        $emailData->created_at = date('Y-m-d H:i:s');
        $emailData->subject = $message->getSubject();

        // Insert the email
        $userId = $this->userGateway->getUserId($email, $password);
        $emailId = $this->mailGateway->insert($emailData, $userId);


        // Insert attachments
        foreach ($message->getAttachments() as $attachment) {
            $attachmentData = new stdClass();
            $attachmentData->email_id = $emailId;
            $attachmentData->file_name = $attachment->getFileName();
            $attachmentData->file_path = ''; // TODO: Set the file path
            $attachmentData->file_type = $attachment->getType();
            $attachmentData->data = $attachment->getContent();

            $this->attachmentGateway->insert($attachmentData, $emailId['id']);
        }

    }

    public function fetchInbox($email, $password)
    {
        $server = new Server('imap.gmail.com');
        $connection = $server->authenticate($email, $password);

        //  $search = new SearchExpression();
        $mailbox = $connection->getMailbox('INBOX');

        $messages = $mailbox->getMessages();
        // var_dump($connection->getMailbox('INBOX')->getThread());
        foreach ($messages as $message) {
            if (!$this->mailGateway->checkMailExists($message->getId())) {
                $this->saveEmail($message, $email, $password);
            }
        }

        header("HTTP/1.1 200 OK");
        //  echo json_encode(['status' => 'success']);
    }

    public function fetchSent($email, $password)
    {

        $server = new Server('imap.gmail.com');

        $connection = $server->authenticate($email, $password);
        $mailbox = $connection->getMailbox('[Gmail]/Sent Mail');
        $messages = $mailbox->getMessages();

        foreach ($messages as $message) {

          //   if ($this->mailGateway->checkMailExists($message->getId())) {
            $this->saveEmail($message, $email, $password);
           // }
        }
    }

    public function sendEmail($email, $password, $data, $attachments)
    {
        // Instantiate a new PHPMailer object
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $email;
        $mail->Password = $password;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        //Recipients
        try {
            $mail->setFrom($email);
            if ($data['to'] != null ) {
                foreach ($data['to'] as $to) {
                    $mail->addAddress($to);

                }
            }
            $mail->addCustomHeader("In-Reply-To", $data['inReplyTo']);
                $mail->addCustomHeader("References", $data['references']);
            if ($data['cc'] != null ) {
                foreach ($data['cc'] as $cc) {
                    $mail->addCC($cc);

                }
            }

            if ($data['bcc'] != null ) {
                foreach ($data['bcc'] as $bcc) {

                    $mail->addBCC($bcc);
                }
            }
        } catch (Exception $e) {
            http_response_code(400);
            json_encode("Invalid recipient parameters!");
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $data['subject'];
        $mail->Body = $data['body'];

        //save attachments to the database
        $filePath = 'C:\xampp\tmp';
        //  echo json_encode($attachments);
        if ($attachments != null) {

            try {
                $mail->addAttachment($attachments['tmp_name'], $attachments['name']);
            } catch (Exception $e) {
                http_response_code(400);
                json_encode("Invalid attachment parameters!");
            }

        }
        // Send the email
        try {
            if (!$mail->send()) {
                echo 'Message has been sent';
            }


        } catch (Exception $e) {
            echo json_encode('Message could not be sent.');
            echo json_encode('Mailer Error: ' . $mail->ErrorInfo);
        }
    }

    function updateEmailStatus($email, $password, $id, $status)
    {

        //var_dump($status);
        $uid = $this->mailGateway->getUid($id);
        if (!$uid) {
            return json_encode(['status' => 'Invalid email id!']);
        }

        $server = new Server('imap.gmail.com');
        $connection = $server->authenticate($email, $password);

        $mailbox = $connection->getMailbox('INBOX');
        $messages = $mailbox->getMessages();
        foreach ($messages as $message) {
            if ($message->getId() == $uid) {
                if ($message->isSeen() == $status) {
                    return json_encode(['status' => 'ID doesnt exist!']);
                }
                if ($status) {
                    $message->markAsSeen();
                    return "Email marked as read!";
                } else {
                    $message->clearFlag('\Seen');
                    return "Email marked as unseen!";
                }

            }

        }
    }
}


?>