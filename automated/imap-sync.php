<?php

namespace Automated;

require_once '../vendor/autoload.php';

use Config\Database;
use Model\Teams;
use Model\TeamsCredentials;
use Service\Imap;
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


    $imapService = new Imap($credentials['imap_server'], $credentials['imap_port'],  $credentials['protocol'], $credentials['use_ssl'] === 1);
    $imap = $imapService->connect($credentials['email'], $credentials['password'],  'INBOX');
    if ($imap === false) {
        continue;
    }

    $emails = $imapService->fetchEmails($imap, 'ALL');

    $parsedEmails = $imapService->parseEmails($imap, $emails);

    $conn->beginTransaction();
    try {
        foreach ($parsedEmails as $parsedEmail) {
            $mail->insert($parsedEmail);
            foreach ($parsedEmail->to as $to) {
                $mailTo->insert($parsedEmail->id, $to);
            }
            foreach ($parsedEmail->cc as $cc) {
                $mailCc->insert($parsedEmail->id, $cc);
            }
            foreach ($parsedEmail->bcc as $bcc) {
                $mailBcc->insert($parsedEmail->id, $bcc);
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
