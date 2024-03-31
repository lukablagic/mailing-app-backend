<?php

namespace Model;

use PDO;

class Mail
{

    private $conn;


    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    // getall
    public function getAll()
    {
        $query = "SELECT * FROM mails";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $emails;
    }


}
