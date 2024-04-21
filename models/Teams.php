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
        $teams = $stmt->fetch(PDO::FETCH_ASSOC);
        return $teams;
    }
    public function getMembers($team_id)
    {
        $query = "SELECT user_id FROM team_members WHERE team_id = :team_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        $members = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $members;
    }
}
