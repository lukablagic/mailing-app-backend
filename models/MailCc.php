<?php

namespace Model;

use PDO;

class MailCc
{

    private $conn;


    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    public function insert($mail_id, $address)
    {
        $query = "INSERT INTO mails_cc (mail_id, `address`) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $mail_id);
        $stmt->bindParam(2, $address);
        $stmt->execute();
    }
}
