<?php

namespace Utility;

use Ddeboer\Imap\Connection;
use Ddeboer\Imap\Search\Date\Since;
use Ddeboer\Imap\Message;
use Ddeboer\Imap\Server;
use DateTime;

class ImapUtility
{


    private $server   = 'imap.gmail.com';
    private $port     = 993;
    private $protocol = 'imap';
    private $ssl      = 'ssl';

    public function __construct($server, $port = 993, $protocol = 'imap', $useSSL = true)
    {
        $this->server   = $server;
        $this->port     = $port;
        $this->protocol = $protocol;
        $this->ssl      = $useSSL ? 'ssl' : 'novalidate-cert';
    }

    public function connect($email, $password, $folder): \IMAP\Connection|false
    {
        $mailbox = "{" . $this->server . ":" . $this->port . "/" . $this->protocol . "/" . $this->ssl . "}" . $folder;
        $imap    = imap_open($mailbox, $email, $password);

        if ($imap === false) {
            return false;
        }

        return $imap;
    }
    public function getParsedEmails($email, $password, $folder)
    {
        $server = new Server($this->server);

        $connection = $server->authenticate($email, $password);

        $mbox = $connection->getMailbox($folder);

        // // get all emails from 2 days ago 
        // $twoDaysAgo = new DateTime('20 days ago');
        // $criteria   = new Since($twoDaysAgo);
        $emails     = $mbox->getMessages();

        $response = [];

        $counter = 0;

        foreach ($emails as $email) {
            $counter += 1;

            $response[] = $this->parseEmails($email);
            if ($counter > 200) {
                break;
            }
        }

        return $response;
    }
    public function getAll($email, $password, $folder)
    {
        $server = new Server($this->server);

        $connection = $server->authenticate($email, $password);

        $mbox = $connection->getMailbox($folder);

        $emails = $mbox->getMessages();

        $response = [];

        $counter = 0;

        foreach ($emails as $email) {
            $counter += 1;

            $response[] = $this->parseEmails($email);
        }

        return $response;
    }
    public function openFolder($imap, $folder): bool
    {
        $result = imap_reopen($imap, $folder);
        if ($result === false) {
            return false;
        }
        return $result;
    }
    public function fetchEmails($imap, $criteria): array|null
    {
        $emails = imap_search($imap, $criteria, SE_UID);
        if ($emails === false) {
            return null;
        }
        return $emails;
    }
    public function parseEmails(Message $message)
    {
        $emailObject              = new \stdClass();
        $emailObject->subject     = $message->getSubject();
        $emailObject->uid         = $message->getId();
        $emailObject->imap_number = (int) $message->getNumber();
        $emailObject->charset     = $message->getCharset();
        $emailObject->attachments = $message->getAttachments();
        $emailObject->body        = $this->parseBody($message);
        $emailObject->sent_date   = DateTime::createFromFormat('U', $message->getDate()->getTimestamp())->format('Y-m-d H:i:s+00:00');
        $emailObject->is_read     = $message->isSeen() ? 1 : 0;
        $emailObject->size        = $message->getSize();
        $emailObject->from        = $message->getFrom()->getAddress();
        $emailObject->from_name   = $message->getFrom()->getName();
        $allTo                    = $message->getTo();
        foreach ($allTo as $to) {
            $emailObject->to[] = $to->getAddress();
        }
        $allCc = $message->getCc();
        foreach ($allCc as $cc) {
            $emailObject->cc[] = $cc->getAddress();
        }
        $allBcc = $message->getBcc();
        foreach ($allBcc as $bcc) {
            $emailObject->bcc[] = $bcc->getAddress();
        }
        $replyTo               = $message->getReplyTo();
        $emailObject->reply_to = $replyTo[0]->getAddress();

        $in_reply_to = $message->getInReplyTo();
        if (!empty($in_reply_to)) {
            $emailObject->in_reply_to = $in_reply_to[0];
        }
        $emailObject->references = $message->getReferences();
        $emailObject->references = $this->parseReferences($emailObject->references);
        return $emailObject;
    }
    public function parseReferences($references)
    {
        $parsedReferences = [];

        foreach ($references as &$reference) {
            if (strlen($reference) < 10) {
                continue;
            }
            if (strlen($reference) > 500) {
                continue;
            }
            if (strpos($reference, '<') === false || strpos($reference, '>') === false) {
                continue;
            }
            if (strpos($reference, '@') === false) {
                continue;
            }

            // test if rerence has , 
            if (strpos($reference, ',') !== false) {
                $reference = explode(',', $reference);
                foreach ($reference as $ref) {
                    $parsedReferences[] = $ref;
                }
            }
            $parsedReferences[] = $reference;
        }

        return $parsedReferences;
    }
    public function parseBody($message)
    {
        $body = $message->getBodyHtml();
        if ($body === null) {
            $body = $message->getBodyText();
        }
        return $body;
    }
    public function getFolders($email, $password): array
    {
        $server     = new Server($this->server);
        $connection = $server->authenticate($email, $password);
        $mailboxes  = $connection->getMailboxes();

        $folders = [];

        foreach ($mailboxes as $mailbox) {
            $folders[] = $mailbox->getName();
        }

        $connection->close();

        return $folders;
    }
    public function getUnreadMails($email, $password, $folder)
    {
      $con = $this->connect($email, $password, $folder);
        if ($con === false) {
            return false;
        }
      return imap_search($con, 'UNSEEN');
    }

    // get sent emails 
    public function getSentMails($email, $password, $folder)
    {
        $con = $this->connect($email, $password, $folder);
        if ($con === false) {
            return false;
        }
        return imap_search($con, 'SENT');
    }
}
