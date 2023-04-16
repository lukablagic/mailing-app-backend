<?php
class Database
{
    private $host;
    private $db_name;
    private $username;
    public $conn;
    private $password = "";

    public function __construct(string $host, string $db_name,string $username, string $password)
    {
        $config = parse_ini_file('.env');
        $this->host = $config[$host];
        $this->db_name = $config[$db_name];
        $this->username = $config[$username];
        $this->password = $config[$password];
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