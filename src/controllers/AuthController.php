<?php

namespace Controller;

use PDO;
use Utility\RequestHandler;

class AuthController
{
    private $conn;


    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
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