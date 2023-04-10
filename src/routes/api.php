<?php


require_once '../controllers/MailController.php';





// Require the autoload file of composer
require_once '../vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

$method = $_SERVER['REQUEST_METHOD'];
$req_uri = $_SERVER['REQUEST_URI'];

$url = rtrim('/', $req_uri);
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);
print_r($url);


?>
