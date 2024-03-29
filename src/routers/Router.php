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
            RequestHandler::invalidEndpoint();
        }
        if ($_SERVER['REQUEST_METHOD'] != $method) {
            RequestHandler::invalidEndpoint();
        }
    }
    public static function createConnection()
    {
        $db = new Database();
        return $db->connect();
    }
    public static function getCollection($endpoint, $controllerName, $isProtected)
    {
        self::init($endpoint, 'GET');
        $conn = self::createConnection();
        $controller = new $controllerName($conn);
        $controller->getCollection();
    }
    public static function getResource($endpoint, $controllerName, $isProtected)
    {

    }

    public static function postCollection($endpoint, $controllerName, $isProtected)
    {
        self::init($endpoint, 'POST');
        $conn = self::createConnection();
        $controller = new $controllerName($conn);
        $controller->postCollection();
    }
    public static function postResource($endpoint, $controllerName, $isProtected)
    {
        self::init($endpoint, 'POST');
        $conn = self::createConnection();
        $controller = new $controllerName($conn);
        $controller->postResource();
    }
    public static function putCollection($endpoint, $controllerName, $isProtected)
    {

    }
    public static function putResource($endpoint, $controllerName, $isProtected)
    {

    }
    public static function deleteCollection($endpoint, $controllerName, $isProtected)
    {

    }
}