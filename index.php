<?php
declare(strict_types=1);

spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});


require_once __DIR__ . "/src/config/Database.php";
require_once __DIR__ . "/src/controllers/MailController.php";
require_once __DIR__ . "/src/controllers/AuthController.php";
require_once __DIR__ . "/src/services/EmailFetcher.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Content-Type: application/json charset=utf-8');

set_exception_handler("ErrorHandler::handleException");
set_error_handler("ErrorHandler::handleError");

header("Access-Control-Allow-Origin: *");

// Get the request method and URI path
$parts = explode("/", $_SERVER["REQUEST_URI"]);
$endpoint = $parts[1] ?? null;
$id = $parts[2] ?? null;

//echo json_encode($parts);


$database = new Database("DB_HOST", "DB_NAME", "DB_USER", "DB_PASSWORD");
$userGateway = new User($database);
$mailGateway = new Mail($database, $userGateway);
$authGateway = new Auth($database, $userGateway);
$attachmentGateway = new Attachment($database);
$emailFetcherGateway = new EmailFetcher($attachmentGateway, $mailGateway);

switch ($endpoint) {
    case 'emails':
        $mailController = new MailController($mailGateway, $userGateway, $emailFetcherGateway,$attachmentGateway);
        $mailController->processRequest($_SERVER["REQUEST_METHOD"], $id);
         break;
    case 'auth':
        $authController = new AuthController($authGateway);
        $authController->processRequest($_SERVER["REQUEST_METHOD"]);
        break;
    default:
        http_response_code(404);
        echo json_encode(["error" => "Invalid API endpoint"]);
}
