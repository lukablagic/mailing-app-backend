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
    public function getByTeamId($team_id)
    {
        $query = "SELECT * FROM teams_credentials WHERE team_id = :team_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        $team = $stmt->fetch(PDO::FETCH_ASSOC);
        return $team;
    }


}
