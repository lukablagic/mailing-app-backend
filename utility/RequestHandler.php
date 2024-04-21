<?php

namespace Utility;

class RequestHandler
{


    public static function getPayload()
    {
        return json_decode(file_get_contents("php://input"), true);
    }

    public static function sendResponseArray(int $code, array $responseArray, ?string $contentType = 'application/json')
    {
        http_response_code($code);
        header('Content-Type: ' . $contentType);

        $response = [];

        if ($code >= 200 && $code < 300) {
            $response['ok'] = true;
        } else {
            $response['ok'] = false;
        }

        foreach ($responseArray as $key => $value) {
            $response[$key] = $value;
        }

        echo json_encode($response);
        die();
    }
    public static function enableCORS()
    {
         if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST, UPDATE, PUT, PATCH, DELETE");
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }

            exit(0);
        }
    }
    public static function invalidEndpoint()
    {
        self::sendResponseArray(404, ['error' => 'Invalid endpoint!']);
    }
    public static function unprocessableEntity($item)
    {
        self::sendResponseArray(422, ['error' => 'Unprocessable Entity', 'message' => $item . ' is required!']);
    }
    // parse query paramns
    public static function parseQueryParams()
    {
        $queryParams = [];
        $query = $_SERVER['QUERY_STRING'];
        $queryArray = explode('&', $query);
        if (empty($query)) {
            return $queryParams;
        }
        foreach ($queryArray as $param) {
            list($key, $value) = explode('=', $param);
            $queryParams[$key] = $value;
        }
    
        return $queryParams;
    }
    // get barer token 
    public static function getBearerToken()
    {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            $authHeaderArray = explode(' ', $authHeader);
            return $authHeaderArray[1];
        }
        return null;
    }
    // getFiles
    public static function getFiles()
    {
        return $_FILES;
    }
}
