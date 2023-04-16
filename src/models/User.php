<?php
//db config 
class User
{

    private $conn;





    public function __construct(Database $db)
    {
        $this->conn = $db->connect();
    }

    public function getByToken($token)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
    return $user;
    }
    
}
?>