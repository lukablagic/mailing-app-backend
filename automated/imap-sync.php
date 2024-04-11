<?php

namespace Automated;

ini_set("display_errors", 1);
require_once '../vendor/autoload.php';

use Config\Database;
use Model\Teams;
use Model\TeamsCredentials;
use Service\ImapService;
use Model\Mail;
use Model\MailTo;
use Model\MailCc;
use Model\MailBcc;
use Exception;

$db = new Database();
$conn = $db->connect();

$teams = new Teams($conn);
$mail = new Mail($conn);
$mailTo = new MailTo($conn);
$mailCc = new MailCc($conn);
$mailBcc = new MailBcc($conn);
$teamsCredentials = new TeamsCredentials($conn);

$allTeams = $teams->getAll();


foreach ($allTeams as $team) {
    $credentials = $teamsCredentials->getByTeamId($team['id']);

    $folder = 'INBOX';
    $imapService = new ImapService($credentials['imap_server'], $credentials['imap_port'], $credentials['protocol'], $credentials['use_ssl'] === 1);
    $imap = $imapService->connect($credentials['email'], $credentials['imap_password'], $folder);
    if ($imap === false) {
        continue;
    }
  
    $emails = array_diff($emails, $existingImapNumbers);


    $parsedEmails = $imapService->parseEmails($imap, $emails);

    $conn->beginTransaction();
    try {
        foreach ($parsedEmails as $parsedEmail) {
            $parsedEmail->team_id = $team['id'];
            $parsedEmail->folder = $folder;

            $mail_id = $mail->insert($parsedEmail);

            if (!empty($parsedEmail->to)) {
                foreach ($parsedEmail->to as $to) {
                    $mailTo->insert($mail_id, $to);
                }
            }
            if (!empty($parsedEmail->cc)) {
                foreach ($parsedEmail->cc as $cc) {
                    $mailCc->insert($mail_id, $cc);
                }
            }
            if (!empty($parsedEmail->bcc)) {
                foreach ($parsedEmail->bcc as $bcc) {
                    $mailBcc->insert($mail_id, $bcc);
                }
            }
        }

        if ($imap !== false) {
            imap_close($imap);
        }
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        if ($imap !== false) {
            imap_close($imap);
        }
        var_dump('Error inserting email', $e->getMessage(), $e->getTraceAsString());
        continue;
    }
}
