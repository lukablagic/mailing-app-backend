<?php

namespace Controller;

use Utility\RequestHandler;
use Service\InvitationsService;

class InviteController
{
    private $invitationService;

    public function __construct($con)
    {
        $this->invitationService = new InvitationsService($con);
    }

    public function getCollection($id, $action, $queryParams, $userData)
    {
    }
    public function postResource($id, $action, $queryParams, $userData)
    {
        if ($id === 'create-link') {
            $url = $this->invitationService->createInvitationLink($userData['team_id']);

            if ($url === false) {
                RequestHandler::sendResponseArray(400, ['message' => 'Unable to create invitation link!']);
            }

            RequestHandler::sendResponseArray(200, ['message' => 'Invite link created successfully!', 'url' => $url]);
        }
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
