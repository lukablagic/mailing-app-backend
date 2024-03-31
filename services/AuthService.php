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
    }

}
?>