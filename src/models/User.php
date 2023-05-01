<?php

//db config
class User
{

    private $conn;


    public function __construct(Database $db)
    {
        $this->conn = $db->connect();
    }

    public function insert($name, $surname, $email, $password, $token)
    {
        $stmt = $this->conn->prepare("INSERT INTO users (name, surname, email, password, token) VALUES (:name, :surname, :email, :password, :token)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':surname', $surname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
    public function getUserByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user;
    }
    public function getUserByToken($token)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && $stmt->rowCount() > 0) {
            return $user;
        }
        return false;
    }
    public function getUserData($token){
        $stmt = $this->conn->prepare("SELECT id, name, surname,email,profile_picture FROM users WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && $stmt->rowCount() > 0) {
            return $user;
        }
        return false;
    }
    public function getUserId($email,$password){
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email AND password = :password");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $userId = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($userId && $stmt->rowCount() > 0) {

            return $userId;
        }
        return false;
    }
    public function getAllUsers()
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE active = 1");
        $stmt->execute();
        $users = $stmt->fetch(PDO::FETCH_ASSOC);
        return $users;
    }

    public function getAllUsersWithToken()
    {
        try {
            $query = "SELECT * FROM users WHERE token IS NOT NULL";
            $stmt = $this->conn->prepare($query);
            //     $stmt = $this->conn->prepare("SELECT * FROM users WHERE token IS NOT NULL");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $users;
        } catch (PDOException $e) {
            // handle the exception here
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function userExisits(string $email, string $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email AND password = :password");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && $stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public function removeUserToken($token)
    {
        $stmt = $this->conn->prepare("UPDATE users SET token = NULL WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

}

?>