<?php

namespace Automated;
ini_set("display_errors", 1);


require_once '../vendor/autoload.php';

use Config\Database;

use Model\Teams;
use Model\Folders;
use Model\TeamsCredentials;

use Service\ImapService;


$db = new Database();
$conn = $db->connect();

$teams = new Teams($conn);
$teamsCredentials = new TeamsCredentials($conn);
$folders = new Folders($conn);


$allTeams = $teams->getAll();
foreach ($allTeams as $team) {
    $credentials = $teamsCredentials->getByTeamId($team['id']);
    $imapService = new ImapService($credentials['imap_server'], $credentials['imap_port'],  $credentials['protocol'], $credentials['use_ssl'] === 1);

    $imapFolders = $imapService->getFolders($credentials['email'], $credentials['imap_password']);

    foreach ($imapFolders as $folder) {
        if ($folders->exists($team['id'], $folder) === false) {
            $folders->insert($team['id'], $folder);
        }
    }
}
