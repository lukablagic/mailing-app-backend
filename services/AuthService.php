<?php

namespace Service;

use PHPMailer\PHPMailer\PHPMailer;
use Exception;
use Model\User;
use Utility\RequestHandler;
use Validator\AuthValidator;

class AuthService
{

    private $user;


    public function __construct($conn)
    {
        $this->user = new User($conn);
    }

    public function login()
    {
        $data = RequestHandler::getPayload();
        AuthValidator::validateLogin($data);
        $user = $this->user->exists($data['email']);
        if ($user === false) {
            return false;
        }

    }
    public function register()
    {
        $data = RequestHandler::getPayload();
        AuthValidator::validateLogin($data);
        $user = $this->user->exists($data['email']);
        if ($user === false) {
            return false;
        }
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));
        $response = $this->user->insert($data['name'], $data['surname'], $data['email'], $password, $token);
        if ($response === false) {
            return false;
        }
        return true;
    }
}
?>