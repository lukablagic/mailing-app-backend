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
        $config = require_once '.Config.php';
        $this->host = $config['host'];
        $this->db_name = $config['db_name'];
        $this->username = $config['username'];
        $this->password = $config['password'];
    }

    public function connect()
    {

        $this->conn = null;
        try {
            $options = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            );
            $this->conn = new PDO('mysql:host=' . $this->host . ';port=3306;dbname=' . $this->db_name, $this->username, $this->password, $options);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            var_dump($exception->getMessage() , $exception->getTraceAsString() , $exception->getCode());
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}