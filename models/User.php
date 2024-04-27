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
        return $this->conn->lastInsertId();
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
        $stmt = $this->conn->prepare("SELECT * 
        FROM users
        JOIN team_members ON users.id = team_members.user_id
        WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            return false;
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getUserLoginData($token)
    {
        $stmt = $this->conn->prepare("SELECT 
        u.id,
        u.name,
        u.surname, 
        u.email,
        tm.team_id as team_id
      FROM users u
      JOIN team_members tm ON u.id = tm.user_id
      WHERE 
        u.token = :token
    ");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // get user by id 
    public function getTeamMembers($token)
    {
        $stmt = $this->conn->prepare("SELECT 
        u.id,
        u.name,
        u.surname, 
        u.email,
        tm.team_id as team_id
      FROM users u
      JOIN team_members tm ON u.id = tm.user_id
      WHERE 
        u.token != :token
    ");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
