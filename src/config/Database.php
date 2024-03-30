<?php

namespace Config;

use PDO;
use PDOException;

class Database
{
    private $host;
    private $db_name;
    private $username;
    public $conn;
    private $password = "";

    public function __construct()
    {
<<<<<<< HEAD
        $this->host = '127.0.0.1:3310';
        $this->db_name = 'mailingdb';
=======
        $this->host = '127.0.0.1:3306';
        $this->db_name = 'devmail';
>>>>>>> 0baf2b003ea3b1515210b02d5d448faaa0ffe32e
        $this->password = "AkCdGHWL@Ubh6prb";
        $this->username = 'mailing_app_normal';
    }

    public function connect()
    {

        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }

}
