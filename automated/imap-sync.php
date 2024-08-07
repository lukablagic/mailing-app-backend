<?php

namespace Automated;

ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;

$db = new Database();
$conn = $db->connect();
$imapService = new \Service\ImapService($conn);

$imapService->syncMailsToday();
