<?php

namespace Controller;

use PDO;
use Utility\RequestHandler;
use User;

class AuthController
{
    private $conn;
    private $user;



    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
        $this->uesr = new User($conn);
    }

    public function getCollection()
    {

    }
    public function getResource($id)
    {

    }
    public function postCollection()
    {

    }
    public function putResource($id)
    {

    }
    public function deleteResource($id)
    {
    }

}

?>