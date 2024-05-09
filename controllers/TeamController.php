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
        if($id === 'team-crendentials'){
            $payload = RequestHandler::getPayload();
            $response = $this->teamService->updateTeamSettings($userData['team_id'], $payload['credentials']);

            if ($response === false) {
                RequestHandler::sendResponseArray(400, ['message' => 'Unable to update team credentials!']);
            }

            RequestHandler::sendResponseArray(200, ['message' => 'Team credentials updated successfully!']);
        }
    }
    public function deleteResource($id, $action, $queryParams, $userData)
    {
    }
}
