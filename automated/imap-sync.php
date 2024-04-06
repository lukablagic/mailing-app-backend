<?php

namespace Automated;

require_once '../vendor/autoload.php';

use Config\Database;
use Model\Teams;
use Model\TeamsCredentials;
use Service\ImapService;

$db = new Database();
$conn = $db->connect();

$teams = new Teams($conn);
$teamsCredentials = new TeamsCredentials($conn);

$allTeams = $teams->getAll();


foreach ($allTeams as $team) {
    $credentials = $teamsCredentials->getByTeamId($team['id']);


    $imapService = new ImapService($credentials['imap_server'], $credentials['imap_port'],  $credentials['protocol'], $credentials['use_ssl'] === 1);
    $imap = $imapService->connect($credentials['email'], $credentials['password'],  'INBOX');
    if ($imap === false) {
        continue;
    }

    $emails = $imapService->fetchEmails($imap, 'ALL');
    var_dump($emails);
    foreach ($emails as $email) {
        $parsedEmail = $imapService->parseEmails($imap, $email);
        var_dump($parsedEmail);
    }
    if ($imap !== false) {
        imap_close($imap);
    }
}
