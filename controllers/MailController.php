<?php

namespace Controller;

use Model\Mail;
use Utility\RequestHandler;

class MailController
{
    private $mail;

    public function __construct($con)
    {
        $this->mail = new Mail($con);
    }

    public function getCollection($id, $action, $queryParams, $userData)
    {
        if ($id === 'threads') {
            $mails = $this->mail->getAll($userData['team_id']);
            RequestHandler::sendResponseArray(200, ['emails' => $mails, 'message' => 'Emails retrieved successfully']);
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
