<?php
require 'src/utility/RequestHandler.php'; 
 
require __DIR__ . '../vendor/autoload.php';

use Utility\RequestHandler;
use Router\Router;

error_reporting(E_ALL);


RequestHandler::enableCORS();
Router::postResource('auth/login', "Controller\AuthController", false);
Router::getCollection('mail', "Controller\MailController", true);



// Router::handle($conn,'/', "MailController",'GET',true);


RequestHandler::invalidEndpoint();

