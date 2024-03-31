<?php

<<<<<<< HEAD

class AuthController
{
    private $conn;


    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function processRequest(string $method, ?string $id, $user)
    {
        switch ($method) {
            case "POST":
                switch ($id) {
                    case "login":
                        $this->login();
                        break;
                    case "logout":
                        $this->logout();
                        break;
                    case "register":
                        $this->register();
                        break;

                    default:
                        http_response_code(400);
                        echo json_encode(["error" => "Invalid action parameter"]);
                }

                break;
            case "GET":

                switch ($id) {
                    case "user":

                        break;
                    default:
                        http_response_code(405);
                        echo json_encode(["error" => "Invalid action parameter"]);
                }
                break;
            default:
                http_response_code(405);
                header("Allow: POST,GET");
=======
namespace Controller;

use PDO;
use Utility\RequestHandler;
use Service\AuthService;
use Model\User;

class AuthController
{
    private $authService;



    public function __construct(PDO $conn)
    {
        $this->authService = new AuthService($conn);
    }

    public function getCollection($id, $action)
    {
        RequestHandler::invalidEndpoint();
    }
    public function getResource($id, $action, $queryParams)
    {
   
    }
    public function postCollection($id, $action, $queryParams)
    {
>>>>>>> 0baf2b003ea3b1515210b02d5d448faaa0ffe32e

    }
    public function postResource($id,  $queryParams)
    {
        die('wrong endpoint');
        if($id === 'register') {
            $this->authService->login();
        }
        RequestHandler::invalidEndpoint();
    }
    public function putResource($id, $action, $queryParams)
    {

    }
    public function deleteResource($id, $action, $queryParams)
    {
    }

}

?>