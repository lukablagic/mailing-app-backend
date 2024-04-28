<?php

namespace Model;

use PDO;

class TeamMembers
{

    private $conn;


    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }
    public function insert($user_id,$team_id,$color)
    {
        $query = "INSERT INTO team_members (user_id,team_id,color) VALUES (:user_id,:team_id,:color)";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':color', $color);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
}
