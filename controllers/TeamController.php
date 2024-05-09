<?php

namespace Controller;

use Service\TeamService;
use Utility\RequestHandler;

class TeamController
{
    private $teamService;

    public function __construct($con)
    {
        $this->teamService = new TeamService($con);
    }

    public function getCollection($id, $action, $queryParams, $userData)
    {
        if ($id === 'team-crendentials') {
            $teamCredentials = $this->teamService->getTeamSettings($userData['team_id']);

            if ($teamCredentials === false) {
                RequestHandler::sendResponseArray(400, ['message' => 'Unable to retrieve team credentials!']);
            }

            RequestHandler::sendResponseArray(200, ['message' => 'Team credentials retrieved successfully!', 'credentials' => $teamCredentials]);
        }
    }
    public function postResource($id, $action, $queryParams, $userData)
    {
       
    }
    public function getResource($id, $action, $queryParams, $userData)
    {
    }
    public function postCollection($id, $action, $queryParams, $userData)
    {
    }
    public function putResource($id, $action, $queryParams, $userData)
    {
    }
    public function deleteResource($id, $action, $queryParams, $userData)
    {
    }
}
