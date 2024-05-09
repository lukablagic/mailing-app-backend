<?php
 ini_set('display_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/vendor/autoload.php';

use Utility\RequestHandler;
use Router\Router;



RequestHandler::enableCORS();

Router::postResource('auth/login', "Controller\AuthController", false);
Router::postResource('mail/send', "Controller\MailController", true);
Router::getCollection('threads', "Controller\MailController", true);
Router::getCollection('threads/members', "Controller\MailController", true);
Router::getCollection('folders/all', "Controller\FolderController", true);
Router::postResource('invitations/create-link', "Controller\InviteController", true);
Router::getCollection('teams/team-credentials', "Controller\TeamController", true);
Router::putResource('teams/team-credentials', "Controller\TeamController", true);

RequestHandler::invalidEndpoint();

