<?php

namespace Router;

use Config\Database;
use Utility\RequestHandler;

class Router
{

    public static function init($endpoint, $method)
    {
        $endpoints = explode('/', $_SERVER['REQUEST_URI']);
        $access = explode('/', $endpoint);
        if ($access[0] !== $endpoints[2]) {
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] != $method) {
            return;
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
    public static function authorize()
    {
        $user = [];
        return $user;
    }
    public static function getCollection($endpoint, $controllerName, $isProtected)
    {
        $endpoins =  self::init($endpoint, 'GET');
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize();
        $controller = new $controllerName($conn);
        $controller->getCollection($endpoins[3],  $endpoins[4], $queryParams, $userData);
    }
    public static function getResource($endpoint, $controllerName, $isProtected)
    {
        $endpoins =  self::init($endpoint, 'GET');
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize();
        $controller = new $controllerName($conn);
        $controller->getResource($endpoins[3], $endpoins[4], $queryParams, $userData);
    }

    public static function postCollection($endpoint, $controllerName, $isProtected)
    {
        $endpoins =  self::init($endpoint, 'POST');
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize();
        $controller = new $controllerName($conn);
        $controller->postCollection($endpoins[3], $endpoins[4], $queryParams, $userData);
    }
    public static function postResource($endpoint, $controllerName, $isProtected)
    {
        $endpoins =   self::init($endpoint, 'POST');
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize();
        $controller = new $controllerName($conn);
        $controller->postResource($endpoins[3], $endpoins[4], $queryParams, $userData);
    }
    public static function putCollection($endpoint, $controllerName, $isProtected)
    {
        $endpoins =   self::init($endpoint, 'PUT');
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize();
        $controller = new $controllerName($conn);
        $controller->putResource($endpoins[3], $endpoins[4], $queryParams, $userData);
    }
    public static function putResource($endpoint, $controllerName, $isProtected)
    {
        $endpoins =   self::init($endpoint, 'PUT');
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize();
        $controller = new $controllerName($conn);
        $controller->putResource($endpoins[3], $endpoins[4], $queryParams, $userData);
    }
    public static function deleteCollection($endpoint, $controllerName, $isProtected)
    {
        $endpoins =   self::init($endpoint, 'DELETE');
        $queryParams = RequestHandler::parseQueryParams();
        $conn = self::createConnection();
        $userData = self::authorize();
        $controller = new $controllerName($conn);
        $controller->deleteCollection($endpoins[3], $endpoins[4], $queryParams, $userData);
    }
}
