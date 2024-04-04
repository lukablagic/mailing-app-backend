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
    private  $folder = 'INBOX';

    public function __construct($server, $port = 993, $folder = 'INBOX', $protocol = 'imap', $ssl = 'ssl',)
    {
        $this->server = $server;
        $this->port = $port;
        $this->folder = $folder;
        $this->protocol = $protocol;
        $this->ssl = $ssl;
    }

    public function connect($email, $password): Connection|false
    {
        $imap = imap_open('{imap.gmail.com:993/imap/ssl}INBOX', $email, $password);
        if ($imap === false) {
            http_response_code(400);
            json_encode("Invalid email or password!");
        }
        return $imap;
    }
    public function openFolder($imap, $folder): bool
    {
        $result = imap_reopen($imap, $folder);
        if ($result === false) {
            http_response_code(400);
            json_encode("Invalid folder!");
        }
        return $result;
    }
    public function fetchEmails($imap, $criteria): array
    {
        $emails = imap_search($imap, $criteria);
        if ($emails === false) {
            http_response_code(400);
            json_encode("Invalid search criteria!");
        }
        return $emails;
    }
}
