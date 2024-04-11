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

    public function getCollection()
    {
        $mails = $this->mail->getAll();
        RequestHandler::sendResponseArray(200, ['emails' => $mails, 'message' => 'Emails retrieved successfully']);
    }
    public function getResource($id)
    {
      
    }
    public function postCollection()
    {
      
    }
    public function putResource($id)
    {
      
    }
    public function deleteResource($id)
    {
      
    }
}
