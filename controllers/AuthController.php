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
    public function postResource($id, $action, $queryParams)
    {
        
        if ($id === 'login') {
            $token = $this->authService->login();
            if ($token === false) {
                RequestHandler::sendResponseArray(400, ['message' => 'Wrong email or password!']);
            }
            RequestHandler::sendResponseArray(200, ['token' => $token]);
        }
       
        if ($id === 'register') {
            $response = $this->authService->register();
            if ($response === false) {
                RequestHandler::sendResponseArray(400, ['message' => 'User with this email already exists!']);
            }
            RequestHandler::sendResponseArray(200, ['message' => 'User registered successfully!']);
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
