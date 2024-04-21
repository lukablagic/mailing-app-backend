<?php

namespace Service;

use Model\User;
use Model\Teams;
use Utility\RequestHandler;
use Validator\AuthValidator;

class UserService
{

    private $user;
    private $teams;


    public function __construct($conn)
    {
        $this->user = new User($conn);
        $this->teams = new Teams($conn);
    }
    /**
     * Returns a token if the user exists and the password is correct 
     * @return string|bool
     **/
    public function getUserLoginData($token)
    {
        $userData = $this->user->getUserLoginData($token);
        $teamData = $this->teams->getAll($userData['team_id']);
        $auth['team']   = $teamData;
        $auth['user']   = $userData;
        $auth['token']  = $token;

        $team_members_ids = $this->teams->getMembers($userData['team_id']);

        $auth['team']['members'] = [];
        foreach ($team_members_ids as $value) {
            if ($value == $userData['id']) continue;
            $auth['team']['members'][] = $this->user->getUserLoginData($value);
        }

        return $auth;
    }
}
