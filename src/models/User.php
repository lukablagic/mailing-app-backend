<?php
//db config 
class User
{

    private $conn;





    public function __construct($db)
    {
        $this->conn = $db;
    }


}
?>