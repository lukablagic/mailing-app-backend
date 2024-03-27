<?php

require __DIR__ . '../vendor/autoload.php';

use Utility\RequestHandler;
use Router\Router;

error_reporting(E_ALL);


RequestHandler::enableCORS();


Router::getCollection('mail', "Controller\MailController", true);





RequestHandler::invalidEndpoint();

