<?php

namespace Service;

use Exception;
use IMAP\Connection;
use DateTime;
use Ddeboer\Imap\Connection as ImapConnection;
use Ddeboer\Imap\ImapResource;
use Ddeboer\Imap\Message;

class Imap
{
    private  $server = 'imap.gmail.com';
    private  $port = 993;
    private  $protocol = 'imap';
    private  $ssl = 'ssl';

    public function __construct($server, $port = 993, $protocol = 'imap', $useSSL = true)
    {
        $this->server = $server;
        $this->port = $port;
        $this->protocol = $protocol;
        $this->ssl = $useSSL ? 'ssl' : 'novalidate-cert';
    }

    public function connect($email, $password, $folder): Connection|false
    {
        $mailbox = "{" . $this->server . ":" . $this->port . "/" . $this->protocol . "/" . $this->ssl . "}" . $folder;
        $imap = imap_open($mailbox, $email, $password);

        if ($imap === false) {
            return false;
        }

        return $imap;
    }
    public function openFolder($imap, $folder): bool
    {
        $result = imap_reopen($imap, $folder);
        if ($result === false) {
            return false;
        }
        return $result;
    }
    public function fetchEmails($imap, $criteria): array | null
    {
        $emails = imap_search($imap, $criteria, SE_UID);
        if ($emails === false) {
            return null;
        }
        return $emails;
    }
    public function parseEmails($imap, $imap_numbers): array
    {
        $parsedEmails = [];
        foreach ($imap_numbers as $imap_id) {
            $emailObject = new \stdClass();

            $headers = $this->parseHeaders($imap, $imap_id);
            $imapRespource =  new ImapResource($imap);
            $message =  new Message($imapRespource, $imap_id);

            $emailObject->subject = $message->getSubject();
            $emailObject->id = $message->getId();
            $emailObject->charset = $message->getCharset();
            $emailObject->attachments = $message->getAttachments();
            $emailObject->body = $this->parseBody($message);
            $emailObject->sent_date = new DateTime($headers->date, new \DateTimeZone('UTC'));
            $emailObject->sent_date->setTimezone(new \DateTimeZone('UTC'));
            $emailObject->sent_date = $emailObject->sent_date->format('Y-m-d-H:i:s+00:00');
            $emailObject->is_read = $headers->Unseen;
            $emailObject->size = $headers->Size;
            $emailObject->from = $message->getFrom()->getAddress();
            $emailObject->from_name = $message->getFrom()->getName();
            $allTo = $message->getTo();
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
            $replyTo = $message->getReplyTo();
            $emailObject->reply_to = $replyTo[0]->getAddress();

            $in_reply_to = $message->getInReplyTo();
            if (!empty($in_reply_to)) {
                $emailObject->in_reply_to = $in_reply_to[0];
            }

            $emailObject->references = $message->getReferences();
            $parsedEmails[] = $emailObject;
        }
        return $parsedEmails;
    }
    public function parseBody($message)
    {
        $body = $message->getBodyHtml();
        if ($body === null) {
            $body = $message->getBodyText();
        }
        return $body;
    }
    public function parseEmailsHeaders($connction, $imap_numbers): array
    {
        $parsedEmails = [];
        foreach ($imap_numbers as $number) {
            $parsedEmails[] = imap_fetch_overview($connction, $number);
        }
        return $parsedEmails;
    }
    public function parseHeaders($imap, $email)
    {
        $headers = imap_headerinfo($imap, $email);
        if ($headers === false) {
            return null;
        }
        return $headers;
    }
    public function getFolders(Connection $imap, $server): array
    {
        // using ddebore/imap get all folders names 
        $imapRespource =  new ImapResource($imap);
        $connection = new ImapConnection($imapRespource, $server);
        $mailboxes = $connection->getMailBoxes();
        $folders = [];
        foreach ($mailboxes as $mailbox) {
            $folders[] = $mailbox->getName();
        }
        var_dump($folders);
        return $folders;
    }
    // basic connection 
    public function basicConnection()
    {
        $mailbox = "{" . $this->server . ":" . $this->port . "/" . $this->protocol . "/" . $this->ssl . "}*";
        $imap = imap_open($mailbox, 'email', 'password');
        if ($imap === false) {
            throw new Exception('Cannot connect to mailbox');
        }
        return $imap;
    }
}
