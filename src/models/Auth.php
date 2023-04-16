<?php
class Auth
{

    private $conn;
    private $user;
    private $token;

    public function __construct($database)
    { {
            $this->conn = $database->connect();
            $this->user = null;
            $this->token = null;
        }

    }

    public function login($email, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        //echo json_encode($user);

        if ($user && password_verify($password, $user["password"])) {

            return true;
        }

        return false;
    }
    public function register($name, $surname, $email, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (name, surname, email, `password`,token) VALUES ('$name','$surname','$email','$hash',NULL)");

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }

    public function generateToken($email)
    {
            $token = bin2hex(random_bytes(32));
            $stmt = $this->conn->prepare("UPDATE users SET token = ? WHERE email =  ?");
            $stmt->execute([$token, $email]);
            return $token;
    }

    public function logout( $email, $password)
    {   
        $stmt = $this->conn->prepare("UPDATE users SET token = NULL WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $user = $stmt->execute();
         echo json_encode($user);  
        
        if ($user) {
           //
            return true;
        }
        return false;
    }
    

    public function authorize($token)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        echo json_encode($token);
        if ($user) {

            return true;
        }

        return false;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getToken()
    {
        return $this->token;
    }

}
?>