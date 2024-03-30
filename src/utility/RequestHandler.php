<?php

namespace Utility;

class RequestHandler
{


<<<<<<< HEAD
    public static function getRequestBody()
=======
    public static function getPayload()
>>>>>>> 0baf2b003ea3b1515210b02d5d448faaa0ffe32e
    {
        return json_decode(file_get_contents("php://input"), true);
    }

<<<<<<< HEAD
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
=======
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
    // check route
    public static function invalidEndpoint()
    {
        self::sendResponseArray(404, ['error' => 'Invalid endpoint!']);
    }
    public static function unprocessableEntity($item)
    {
        // self::sendResponseArray(422, ['error' => 'Unprocessable Entity', 'message' => $item . ' is required!']);
    }
}
>>>>>>> 0baf2b003ea3b1515210b02d5d448faaa0ffe32e
