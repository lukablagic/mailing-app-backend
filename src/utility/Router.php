<?php

namespace Utility;

class Router
{


    public static function get($conn, $endpoint, $controllerName,  $isProtected)
    {
        $request = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        $id = null;
        $user = null;
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        }
        if ($isProtected) {
            $user = self::authenticate();
        }
        $controller = new $controllerName($conn);
        $controller->processRequest( $id, $user);
    }
    public static function post($conn, $endpoint, $controllerName,  $isProtected)
    {
        $request = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        $id = null;
        $user = null;
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        }
        if ($isProtected) {
            $user = self::authenticate();
        }
        $controller = new $controllerName($conn);
        $controller->processRequest( $id, $user);
    }
    public static function put($conn, $endpoint, $controllerName,  $isProtected)
    {
        $request = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        $id = null;
        $user = null;
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        }
        if ($isProtected) {
            $user = self::authenticate();
        }
        $controller = new $controllerName($conn);
        $controller->delete( $id, $user);
    }
    public static function delete($conn, $endpoint, $controllerName,  $isProtected)
    {
        $request = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        $id = null;
        $user = null;
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        }
        if ($isProtected) {
            $user = self::authenticate();
        }
        $controller = new $controllerName($conn);
        $controller->processRequest( $id, $user);
    }
}
?>