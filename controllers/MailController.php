<?php

namespace Controller;

use Service\MailService;
use Service\ImapService;
use Service\SmtpService;
use Utility\RequestHandler;
use Validator\MailValidator;

class MailController
{
    private $mailService;
    private $smtpService;
    private $imapService;

    public function __construct($con)
    {
        $this->mailService = new MailService($con);
        $this->smtpService = new SmtpService($con);
        $this->imapService = new ImapService($con);
    }

    public function getCollection($id, $action, $queryParams, $userData)
    {
        if ($id === 'all') {
            $folder = '';
            $page   = 1;
            
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
            RequestHandler::sendResponseArray(200, ['emails' => $members, 'message' => 'Members retrieved successfully']);
        }
    }
    public function postResource($id, $action, $queryParams, $userData)
    {
        if ($id === 'send-mail') {
            $attachments = RequestHandler::getFiles();
            $payload = RequestHandler::getPayload();
            $data =  MailValidator::validateSendingDraft($payload);
            $response = $this->smtpService->sendEmail($userData['team_id'], $data, $attachments);

            if ($response === false) {
                RequestHandler::sendResponseArray(400, ['message' => 'Email not sent!']);
            }
            $this->imapService->syncSent($userData['team_id']);

            RequestHandler::sendResponseArray(200, ['message' => 'Email sent successfully!']);
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
