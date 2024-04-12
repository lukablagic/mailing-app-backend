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
    public function getAll($team_id)
    {
        $query = "SELECT folder FROM folders WHERE team_id = :team_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    public function getAllFolders($team_id)
    {
        $query = "SELECT folder as name,id FROM folders WHERE team_id = :team_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
