<?php

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../models/User.php";
class UserController
{

    private $db;
    private $mailModel;

    function __construct()
    {
        // Initialize database connection
        $this->db = new Database();
     //    $this->mailModel = new User();
    }

    public function register( $password, $email, $name, $surname)
    {
        $user = new User($this->db->connect());
        $user->name = $name;
        $user->surname = $surname;
        $user->email = $email;
        $user->password = $password; 
     //   $user->profile_picture = $profile_picture; to be implemented later
        if ($user->validateInput( $email, $password)) {
            $user->register();
            echo json_encode("User registered");
        }else{
            echo json_encode("Invalid user already exists");
        }
    }

    public function login()
    {

    }
    public function logout()
    {
    }

}

?>