<?php

namespace Automated;

require_once '../vendor/autoload.php';

use Config\Database;

$db = new Database();
$conn = $db->connect();
$imapService = new \Service\ImapService($conn);

$imapService->syncIsRead();
