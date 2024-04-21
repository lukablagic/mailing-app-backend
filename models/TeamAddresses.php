<?php

namespace Model;

use PDO;

class TeamAddresses
{

    private $conn;


    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    public function getAllAddresses($team_id)
    {
        $query = "SELECT email FROM team_addresses WHERE team_id = :team_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        $teams = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $teams;
    }
}
