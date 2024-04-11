<?php

namespace Model;

use PDO;

class User
{

    private $conn;


    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
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
    public function exists($email)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as `counter` FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user['counter'] > 0) {
            return true;
        }
        return false;
    }
    public function updateToken($email, $token)
    {
        $query = "UPDATE users SET token = :token WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':token', $token);
        return  $stmt->execute();
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
    public function getUser($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // getUserByToken
    public function getUserByToken($token)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            return false;
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
