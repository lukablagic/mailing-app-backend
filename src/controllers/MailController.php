<?php

namespace Controller;

use Model\Mail;
use Utility\RequestHandler;

class MailController
{
    private $mail;

<<<<<<< HEAD
    public function __construct($con,)
    {

=======
    public function __construct($con)
    {
        $this->mail = new Mail($con);
>>>>>>> 0baf2b003ea3b1515210b02d5d448faaa0ffe32e
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
