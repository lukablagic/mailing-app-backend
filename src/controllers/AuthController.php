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

    public function getCollection($id, $action)
    {
        RequestHandler::invalidEndpoint();
    }
    public function getResource($id, $action, $queryParams)
    {
   
    }
    public function postCollection($id, $action, $queryParams)
    {

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