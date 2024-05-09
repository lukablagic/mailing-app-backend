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

    public function getTeamSettings($team_id)
    {
        $query = "SELECT * FROM teams_credentials WHERE team_id = :team_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        $team = $stmt->fetch(PDO::FETCH_ASSOC);
        return $team;
    }

    public function updateTeamSettings($team_id, $settings)
    {
        $query = "UPDATE teams_credentials SET  imap_server = :imap_server, imap_port = :imap_port, protocol = :protocol, email = :email, imap_password = :imap_password, smtp_server = :smtp_server, smtp_password = :smtp_password, password = :password WHERE team_id = :team_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':imap_server', $settings['imap_server']);
        $stmt->bindParam(':imap_port', $settings['imap_port']);
        $stmt->bindParam(':protocol', $settings['protocol']);
        $stmt->bindParam(':email', $settings['email']);
        $stmt->bindParam(':imap_password', $settings['imap_password']);
        $stmt->bindParam(':smtp_server', $settings['smtp_server']);
        $stmt->bindParam(':smtp_password', $settings['smtp_password']);
        $stmt->bindParam(':password', $settings['password']);
        return $stmt->execute();
    }

}
