<?php

namespace Service;

use Model\Teams;
use Model\TeamsCredentials;

class TeamService
{

    private $teams;
    private $teamsCredentials;



    public function __construct($conn)
    {
        $this->teams            = new Teams($conn);
        $this->teamsCredentials = new TeamsCredentials($conn);
    }

    public function getTeamSettings($team_id)
    {
        return $this->teamsCredentials->getTeamSettings($team_id);
    }
}
