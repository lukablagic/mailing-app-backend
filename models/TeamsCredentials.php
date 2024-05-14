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

    public function updateTeamSettings($team_id, $settings)
    {
        $query = "UPDATE teams_credentials SET  imap_server = :imap_server, imap_port = :imap_port, protocol = :protocol, email = :email, smtp_server = :smtp_server,smtp_port = :smtp_port,  access_password = :access_password WHERE team_id = :team_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':imap_server', $settings['imap_server']);
        $stmt->bindParam(':imap_port', $settings['imap_port']);
        $stmt->bindParam(':smtp_port', $settings['smtp_port']);
        $stmt->bindParam(':protocol', $settings['protocol']);
        $stmt->bindParam(':email', $settings['email']);
        $stmt->bindParam(':smtp_server', $settings['smtp_server']);
        $stmt->bindParam(':access_password', $settings['access_password']);
        return $stmt->execute();
    }

    public function insert($team_id, $imap_server, $imap_port, $protocol, $email, $smtp_server, $smtp_port, $access_password)
    {
        $query = "INSERT INTO teams_credentials (team_id, imap_server, imap_port, protocol, email, smtp_server, smtp_port, access_password) VALUES (:team_id, :imap_server, :imap_port, :protocol, :email, :smtp_server,:smtp_port, :access_password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':imap_server', $imap_server);
        $stmt->bindParam(':imap_port', $imap_port);
        $stmt->bindParam(':smtp_port', $smtp_port);
        $stmt->bindParam(':protocol', $protocol);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':smtp_server', $smtp_server);
        $stmt->bindParam(':access_password', $access_password);
        return $stmt->execute();
    }

}
