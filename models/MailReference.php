<?php

namespace Model;

class MailReference
{

    private $conn;


    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    public function insert($mail_id, $reference)
    {
        $query = "INSERT INTO mails_references (mail_id, `reference`) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $mail_id);
        $stmt->bindParam(2, $reference);
        $stmt->execute();
    }
}
