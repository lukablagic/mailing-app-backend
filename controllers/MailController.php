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
        if ($id === 'all') {
            $folder = '';

            if (isset($queryParams['folder'])){
                $folder = $queryParams['folder'];
            }

            $mails = $this->mail->getAllThreads($userData['team_id'],$folder);
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
