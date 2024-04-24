<?php

namespace Service;

use Model\User;
use Model\Teams;
use Model\TeamAddresses;
use Utility\RequestHandler;
use Validator\AuthValidator;

class UserService
{

    private $user;
    private $teams;
    private $teamAddresses;



    public function __construct($conn)
    {
        $this->user          = new User($conn);
        $this->teams         = new Teams($conn);
        $this->teamAddresses = new TeamAddresses($conn);
    }
    /**
     * Returns a token if the user exists and the password is correct 
     * @return string|bool
     **/
    public function getUserLoginData($token)
    {
        $userData      = $this->user->getUserLoginData($token);
        $teamData      = $this->teams->get($userData['team_id']);
        $auth['team']  = $teamData;
        $auth['user']  = $userData;
        $auth['token'] = $token;

        $auth['team']['members'] = [];
        $auth['team']['members'] = $this->user->getTeamMembers($token);
        $auth['team']['addresses'] = [];
        $auth['team']['addresses'] = $this->teamAddresses->getAllAddresses($userData['team_id']);

        return $auth;
    }
}
