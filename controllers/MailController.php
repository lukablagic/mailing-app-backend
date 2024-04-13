<?php

namespace Controller;

use Model\Mail;
use Service\MailService;
use Utility\RequestHandler;

class MailController
{
    private $mail;
    private $mailService;

    public function __construct($con)
    {
        $this->mail = new Mail($con);
        $this->mailService = new MailService($con);
    }

    public function getCollection($id, $action, $queryParams, $userData)
    {
        if ($id === 'all') {
            $folder = '';

            if (isset($queryParams['folder'])) {
                $folder = $queryParams['folder'];
            }

            $mails = $this->mailService->getAllThreads($userData['team_id'], $folder);
            RequestHandler::sendResponseArray(200, ['emails' => $mails, 'message' => 'Emails retrieved successfully']);
        }
        if ($id === 'members') {
            
            if (isset($queryParams['id'])) {
                $id = $queryParams['id'];
            }

            $members = $this->mailService->getMembers($userData['team_id'], $queryParams['id']);
            RequestHandler::sendResponseArray(200, ['members' => $members, 'message' => 'Members retrieved successfully']);
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
