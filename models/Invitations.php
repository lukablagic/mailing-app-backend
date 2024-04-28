<?php

namespace Model;

use PDO;

class Invitations
{

    private $conn;


    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    //   create
    public function create($team_id, $code, $invitation_uid, $valid_to)
    {
        $query = "INSERT INTO invitations (team_id, code, invitation_uid, valid_to) VALUES (:team_id, :code, :invitation_uid, :valid_to)";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':invitation_uid', $invitation_uid);
        $stmt->bindParam(':valid_to', $valid_to);
        return   $stmt->execute();
    }
    public function getTeamId($code,$invitation_uid){
        $query = "SELECT team_id FROM invitations WHERE code = :code AND invitation_uid = :invitation_uid";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':invitation_uid', $invitation_uid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }
}
