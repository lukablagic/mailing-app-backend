<?php

namespace Model;

use PDO;

class Folders
{

    private $conn;


    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function insert($team_id, $folder)
    {
        $query = "INSERT INTO folders (team_id, folder) VALUES (:team_id, :folder)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':folder', $folder);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
    public function exists($team_id, $folder)
    {
        $query = "SELECT COUNT(*) as `counter` FROM folders WHERE team_id = :team_id AND folder = :folder";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':folder', $folder);
        $stmt->execute();
        $folder = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() > 0 && $folder['counter'] > 0) {
            return true;
        }
        return false;
    }
}
