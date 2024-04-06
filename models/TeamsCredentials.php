<?php

namespace Model;

use PDO;

class TeamsCredentials
{

    private $conn;


    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    public function getByTeamId($id)
    {
        $query = "SELECT * FROM teams_credentials WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $team = $stmt->fetch(PDO::FETCH_ASSOC);
        return $team;
    }


}
