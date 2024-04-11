<?php

namespace Router;

use Config\Database;
use Service\AuthService;
use Utility\RequestHandler;

class Router
{

    public static function init($endpoint, $method)
    {
        $endpoints = explode('/', $_SERVER['REQUEST_URI']);
        $access = explode('/', $endpoint);
        if ($access[0] !== $endpoints[2]) {
            return false;
        }
        if ($_SERVER['REQUEST_METHOD'] != $method) {
            return false;
        }
        if ($access[0] === $endpoints[2] && $_SERVER['REQUEST_METHOD'] != $method) {
            RequestHandler::sendResponseArray(405, ['error' => 'Method Not Allowed']);
        }
        if (!isset($endpoints[3])) {
            $endpoints[3] = '';
        }
        if (!isset($endpoints[4])) {
            $endpoints[4] = '';
        }
        return $endpoints;
    }
    public static function createConnection()
    {
        $db = new Database();
        return $db->connect();
    }
    public static function authorize($conn)
    {
        $authService = new AuthService($conn);
        $user = $authService->authorize();
        if ($user === false) {
            RequestHandler::sendResponseArray(401, ['error' => 'Unauthorized!']);
        }
        return $user;
    }
    public static function getCollection($endpoint, $controllerName, $isProtected)
    {
        $endpoins =  self::init($endpoint, 'GET');
        var_dump($endpoins);
        if ($endpoins === false) {
            return;
        }
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize($conn);
        $controller = new $controllerName($conn);
        $controller->getCollection($endpoins[2],  $endpoins[3], $queryParams, $userData);
    }
    public static function getResource($endpoint, $controllerName, $isProtected)
    {
        $endpoins =  self::init($endpoint, 'GET');
        if ($endpoins === false) {
            return;
        }
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize($conn);
        $controller = new $controllerName($conn);
        $controller->getResource($endpoins[2],  $endpoins[3], $queryParams, $userData);
    }

    public static function postCollection($endpoint, $controllerName, $isProtected)
    {
        $endpoins =  self::init($endpoint, 'POST');
        if ($endpoins === false) {
            return;
        }
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize($conn);
        $controller = new $controllerName($conn);
        $controller->postCollection($endpoins[2],  $endpoins[3], $queryParams, $userData);
    }
    public static function postResource($endpoint, $controllerName, $isProtected)
    {
        $endpoins =   self::init($endpoint, 'POST');
        if ($endpoins === false) {
            return;
        }
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize($conn);
        $controller = new $controllerName($conn);
        $controller->postResource($endpoins[2],  $endpoins[3], $queryParams, $userData);
    }
    public static function putCollection($endpoint, $controllerName, $isProtected)
    {
        $endpoins =   self::init($endpoint, 'PUT');
        if ($endpoins === false) {
            return;
        }
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize($conn);
        $controller = new $controllerName($conn);
        $controller->putResource($endpoins[2],  $endpoins[3], $queryParams, $userData);
    }
    public static function putResource($endpoint, $controllerName, $isProtected)
    {
        $endpoins =   self::init($endpoint, 'PUT');
        if ($endpoins === false) {
            return;
        }
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize($conn);
        $controller = new $controllerName($conn);
        $controller->putResource($endpoins[2],  $endpoins[3], $queryParams, $userData);
    }
    public static function deleteCollection($endpoint, $controllerName, $isProtected)
    {
        $endpoins =   self::init($endpoint, 'DELETE');
        if ($endpoins === false) {
            return;
        }
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize($conn);
        $controller = new $controllerName($conn);
        $controller->deleteCollection($endpoins[2],  $endpoins[3], $queryParams, $userData);
    }
}
