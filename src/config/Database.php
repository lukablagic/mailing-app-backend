<?php
class Database
{
    private $host;
    private $db_name;
    private $username;
    public $conn;
    private $password = "";

    public function __construct()
    {
        $config = parse_ini_file('.env');
        $this->host = $config['DB_HOST'];
        $this->db_name = $config['DB_NAME'];
        $this->username = $config['DB_USER'];
        $this->password = $config['DB_PASSWORD'];
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