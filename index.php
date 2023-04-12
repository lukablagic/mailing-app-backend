<?php

// Load necessary files
//require_once 'api/routes/api.php';
declare(strict_types=1);

spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});
require 'src/controllers/MailController.php';
require_once __DIR__ . "/src/config/Database.php";
require_once __DIR__ . "/src/controllers/MailController.php";
require_once __DIR__ . "/src/controllers/UserController.php";
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Content-Type: application/json charset=utf-8');


set_exception_handler("ErrorHandler::handleException");
set_error_handler("ErrorHandler::handleError");
header("Access-Control-Allow-Origin: *");
// Create a new instance of the User class
$userController = new UserController();
$mailController = new MailController();
// Get the request method and URI path
$requestMethod = $_SERVER["REQUEST_METHOD"];
$requestPath = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$parts = explode("/", $requestPath);
//var_dump($parts);
//var_dump($requestMethod);
//var_dump($requestPath);
// Check if the request method is GET and the endpoint is /register
if ($requestMethod === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}
if ($requestMethod === 'POST' && $parts[1] === 'register') {

    // Get the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (!isset($data['email'], $data['password'], $data['name'], $data['surname'])) {
        http_response_code(400); // Bad request
        header('Content-Type: application/json');
        json_encode(['message' => 'Missing required parameters']);
        return;
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400); // Bad request
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Invalid email format']);
        return;
    }

    // Call the register function on the userController
    $userController->register($data['password'], $data['email'], $data['name'], $data['surname']);
} else {
    // Invalid API endpoint
    http_response_code(404);
}

if ($requestMethod == "POST" && $parts[1] == "emails") {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json charset=utf-8');
    $data = json_decode(file_get_contents('php://input'), true);
    $mailController->fetchEmailsFromServer($data['email'], $data['password']);

}

if ($requestMethod == "POST" && $parts[1] == "send-mail") {
    $data = json_decode(file_get_contents('php://input'), true);

    $mailController->sendEmail(
        $data['to'], $data['from'], $data['password'], $data['subject'], $data['body'], $data['attachment'] ?? null,
        $data['cc'] ?? null,
        $data['bcc'] ?? null,
        $data['name'] ?? null,
        $data['surname'] ?? null
    );
}
?>