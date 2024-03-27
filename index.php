<?php

require __DIR__ . '../vendor/autoload.php';

use Utility\RequestHandler;
use Utility\Router;

error_reporting(E_ALL);


RequestHandler::enableCORS();


Router::getCollection('mail', "Controller\MailController", true);





RequestHandler::invalidEndpoint();

