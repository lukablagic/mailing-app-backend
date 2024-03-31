<?php

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

    public function getCollection($id, $action, $queryParams, $userData)
    {
        RequestHandler::invalidEndpoint();
    }
    public function getResource($id, $action, $queryParams, $userData)
    {
   
    }
    public function postCollection($id, $action, $queryParams, $userData)
    {

    }
    public function postResource($id, $action, $queryParams, $userData)
    {
        if($id === 'register') {
            $this->authService->login();
        }
        RequestHandler::invalidEndpoint();
    }
    public function putResource($id, $action, $queryParams, $userData)
    {

    }
    public function deleteResource($id, $action, $queryParams, $userData)
    {
    }

}

?>