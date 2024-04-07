<?php

namespace Automated;

require_once '../vendor/autoload.php';

use Config\Database;
use Model\Teams;
use Model\TeamsCredentials;
use Service\Imap;

$db = new Database();
$conn = $db->connect();

$teams = new Teams($conn);
$teamsCredentials = new TeamsCredentials($conn);

$allTeams = $teams->getAll();


foreach ($allTeams as $team) {
    $credentials = $teamsCredentials->getByTeamId($team['id']);


    $imapService = new Imap($credentials['imap_server'], $credentials['imap_port'],  $credentials['protocol'], $credentials['use_ssl'] === 1);
    $imap =  $imapService->basicConnection();
    var_dump($folders);
}
