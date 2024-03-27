<?php

namespace Utility;

class RequestHandler
{


    public static function getRequestBody()
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
    // enable cors 
    public static function enableCORS()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }
    // check route
    public static function invalidEndpoint()
    {
        self::sendResponseArray(404, ['error' => 'Invalid endpoint!']);
    }
}