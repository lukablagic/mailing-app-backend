<?php

namespace Utility;

class RequestHandler
{


    public static function getRequestBody()
    {
        return json_decode(file_get_contents("php://input"), true);
    }

    public static function sendResponseArray(int $code, array $data, ?string $contentType = 'application/json')
    {
        http_response_code($code);

        $response = [];

        for ($i = 0; $i < count($data); $i += 2) {
            if (isset($data[$i + 1])) {
                $response[$data[$i]] = $data[$i + 1];
            }
        }

        header('Content-Type: ' . $contentType);
        echo json_encode($response);
    }
    // enable cors 
    public static function enableCORS()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }
    // check route
    public static function checkRoute($allowedRoute)
    {
      
      
    }
}
?>