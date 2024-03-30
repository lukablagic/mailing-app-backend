<?php
require 'src/utility/RequestHandler.php'; 
 
require __DIR__ . '../vendor/autoload.php';

<<<<<<< HEAD
require __DIR__ . '/vendor/autoload.php';

use Utility\RequestHandler;
// use AuthController;
// use Config\Database;
=======
use Utility\RequestHandler;
use Router\Router;

>>>>>>> 0baf2b003ea3b1515210b02d5d448faaa0ffe32e
error_reporting(E_ALL);


RequestHandler::enableCORS();
<<<<<<< HEAD


// print current route 
=======
Router::postResource('auth/login', "Controller\AuthController", false);
Router::getCollection('mail', "Controller\MailController", true);
>>>>>>> 0baf2b003ea3b1515210b02d5d448faaa0ffe32e



// Router::handle($conn,'/', "MailController",'GET',true);


RequestHandler::invalidEndpoint();

