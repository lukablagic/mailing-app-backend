<?php
 ini_set('display_errors', 1);
// set imap 
error_reporting(E_ALL);
require __DIR__ . '/vendor/autoload.php';

use Utility\RequestHandler;
use Router\Router;



RequestHandler::enableCORS();

Router::postResource('auth/login', "Controller\AuthController", false);
Router::getCollection('threads', "Controller\MailController", true);

RequestHandler::invalidEndpoint();

