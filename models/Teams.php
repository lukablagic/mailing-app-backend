<?php

namespace Model;

use PDO;

class Teams
{

    private $conn;


    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    public function getAll()
    {
        $query = "SELECT * FROM teams";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $teams;
    }


}
