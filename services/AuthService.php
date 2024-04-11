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
    /**
     * Returns a token if the user exists and the password is correct 
     * @return string|bool
     **/
    public function login()
    {
        $data = RequestHandler::getPayload();
        AuthValidator::validateLogin($data);
       
        $exits = $this->user->exists($data['email']);
        if ($exits === false) {
            return false;
        }
        $user = $this->user->getUser($data['email']);
        $password = $user['password'];

        if (password_verify($data['password'], $password) === false) {
            return false;
        }
        
        $token = bin2hex(random_bytes(16));
        
        $response = $this->user->updateToken($data['email'], $token);
        
        if ($response === false) {
            return false;
        }
        
        return $token;
    }
    public function register()
    {
        $data = RequestHandler::getPayload();
        AuthValidator::validateLogin($data);
        $user = $this->user->exists($data['email']);
        if ($user === true) {
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
    // authorize the user if the token is valid return all user data with team id 
    public function authorize()
    {
        $token = RequestHandler::getBearerToken();
        if ($token === false) {
          RequestHandler::sendResponseArray(401, ['message' => 'Unauthorized!']);
        }
        $user = $this->user->getUserByToken($token);
        if ($user === false) {
            return false;
        }
        return $user;
    }
}
