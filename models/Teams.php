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
        $stmt  = $this->conn->prepare($query);
        $stmt->execute();
        $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $teams;
    }
    public function get($team_id)
    {
        $query = "SELECT * FROM teams WHERE id = :team_id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        $team = $stmt->fetch(PDO::FETCH_ASSOC);
        return $team;
    }
    public function getMembers($team_id)
    {
        $query = "SELECT user_id FROM team_members WHERE team_id = :team_id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        $members = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $members;
    }
    public function insert($name)
    {
        $query = "INSERT INTO teams (name) VALUES (:name)";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
}
