<?php

class Database
{
    private $host;
    private $db_name;
    private $username;
    public $conn;
    private $password = "";

    public function __construct(string $host, string $db_name, string $username, string $password)
    {
        $this->host = '127.0.0.1:3310';
        $this->db_name = 'mailingdb';
        $this->password = "/pXdAmAwbnxIYS9t";
        $this->username = 'mailing_app_normal';
    }

    public function connect()
    {

        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }

}

?>