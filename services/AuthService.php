<?php

namespace Service;

use PHPMailer\PHPMailer\PHPMailer;
use Exception;
use Model\User;
use Model\Teams;
use Model\TeamMembers;
use Utility\RequestHandler;
use Validator\AuthValidator;

class AuthService
{

    private $user;
    private $teams;
    private $teamMembers;


    public function __construct($conn)
    {
        $this->user        = new User($conn);
        $this->teams       = new Teams($conn);
        $this->teamMembers = new TeamMembers($conn);
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

        $token = bin2hex(random_bytes(32));

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
        $password     = password_hash($data['password'], PASSWORD_DEFAULT);
        $token        = bin2hex(random_bytes(32));
        $userResponse = $this->user->insert($data['name'], $data['surname'], $data['email'], $password, $token);

        if ($userResponse === false) {
            return false;
        }
        $userId       = $userResponse;
        $teamName     = 'Team#' . rand(1000, 9999);
        $responseTeam = $this->teams->insert($teamName);

        if ($responseTeam === false) {
            return false;
        } else {
            $teamId             = $responseTeam;
            $color              = '#' . dechex(rand(0x000000, 0xFFFFFF));
            $responseTeamMember = $this->teamMembers->insert($userId, $teamId, $color);
            if ($responseTeamMember === false) {
                return false;
            }
        }

        return true;
    }
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
