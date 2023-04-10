<?php
//db config 
class User
{

    private $conn;

    public $id;
    public $name;
    public $surname;
    public $email;
    public $password;
    public $profile_picture;
    public $faield_attempts;
    public $locked;
    public $locekd_until;





    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function authenticate()
    {

    }
    public function validateInput( $email, $password)
    {
        //check if user already exists
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            return false;
        }
        
        return true; // all checks passed, input is valid
    }
    public function validateUser($email, $password)
    {
        //check if user already exists
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
           return true;
        }
        return false;
    }
    public function register()
    {
        $query = "INSERT INTO users (name, surname, email, password, profile_picture) VALUES (:name, :surname, :email, :password, :profile_picture)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':surname', $this->surname);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':profile_picture', $this->profile_picture);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

}
?>