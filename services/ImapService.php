<?php

namespace Service;

use Exception;
use IMAP\Connection;

class ImapService
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
        $emails = imap_search($imap, $criteria,SE_UID);
        if ($emails === false) {
            return null;
        }
        return $emails;
    }
    public function parseEmails($imap, $emails): array
    {
        $parsedEmails = [];
        foreach ($emails as $email) {
            $parsedEmails[] = imap_fetch_overview($imap, $email);
        }
        return $parsedEmails;
    }
    public function parseBody($imap, $email)
    {
        $body = imap_fetchbody($imap, $email, 1);
        return $body;
    }
    public function parseHeaders($imap, $email)
    {
        $headers = imap_fetchheader($imap, $email);
        if ($headers === false) {
            return null;
        }
        return imap_rfc822_parse_headers($headers);
    }
}
