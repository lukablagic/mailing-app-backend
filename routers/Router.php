<?php

namespace Router;

use Config\Database;
use Service\AuthService;
use Utility\RequestHandler;

class Router
{

    public static function init($endpoint, $method)
    {
        $urlComponents = parse_url($_SERVER['REQUEST_URI']);
        $path = $urlComponents['path'];
        $endpoints = explode('/', $path);
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
        $endpoints =  self::init($endpoint, 'GET');
        if ($endpoints === false) {
            return;
        }
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize($conn);
        $controller = new $controllerName($conn);
        $controller->getCollection($endpoints[3],  $endpoints[4], $queryParams, $userData);
    }
    public static function getResource($endpoint, $controllerName, $isProtected)
    {
        $endpoints =  self::init($endpoint, 'GET');
        if ($endpoints === false) {
            return;
        }
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize($conn);
        $controller = new $controllerName($conn);
        $controller->getResource($endpoints[3],  $endpoints[4], $queryParams, $userData);
    }

    public static function postCollection($endpoint, $controllerName, $isProtected)
    {
        $endpoints =  self::init($endpoint, 'POST');
        if ($endpoints === false) {
            return;
        }
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize($conn);
        $controller = new $controllerName($conn);
        $controller->postCollection($endpoints[3],  $endpoints[4], $queryParams, $userData);
    }
    public static function postResource($endpoint, $controllerName, $isProtected)
    {
        $endpoints =   self::init($endpoint, 'POST');
        if ($endpoints === false) {
            return;
        }
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = null;
        if ($endpoints[2] !== 'auth' && in_array($endpoints[3], ['login', 'register']) === false && $isProtected === true) {
            $userData = self::authorize($conn);
        }
        $controller = new $controllerName($conn);
        $controller->postResource($endpoints[3],  $endpoints[4], $queryParams, $userData);
    }
    public static function putCollection($endpoint, $controllerName, $isProtected)
    {
        $endpoints =   self::init($endpoint, 'PUT');
        if ($endpoints === false) {
            return;
        }
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize($conn);
        $controller = new $controllerName($conn);
        $controller->putResource($endpoints[3],  $endpoints[4], $queryParams, $userData);
    }
    public static function putResource($endpoint, $controllerName, $isProtected)
    {
        $endpoints =   self::init($endpoint, 'PUT');
        if ($endpoints === false) {
            return;
        }
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize($conn);
        $controller = new $controllerName($conn);
        $controller->putResource($endpoints[3],  $endpoints[4], $queryParams, $userData);
    }
    public static function deleteCollection($endpoint, $controllerName, $isProtected)
    {
        $endpoints =   self::init($endpoint, 'DELETE');
        if ($endpoints === false) {
            return;
        }
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize($conn);
        $controller = new $controllerName($conn);
        $controller->deleteCollection($endpoints[3],  $endpoints[4], $queryParams, $userData);
    }
}
