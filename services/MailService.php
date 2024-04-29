<?php

namespace Service;

use Exception;
use Model\User;
use Model\Mail;

class MailService
{

    private $user;
    private $mail;


    public function __construct($conn)
    {
        $this->mail = new Mail($conn);
        $this->user = new User($conn);
    }
    public function getMembers($team_id, $id)
    {
        $mail  = $this->mail->get($team_id, $id);

        $members = $this->mail->getThreadMembersBySubject($team_id, $mail['subject'], $mail['reply_to']);

        if (!empty($members)) {
            return $members;
        }
        return false;
    }
    // getAllThreads
    public function getAllThreads($team_id, $folder,$page = 1)
    {
        $limit = 20 * $page;
        $mails = $this->mail->getAllThreads($team_id, $folder,$limit);

        if ($mails === false) {
            return false;
        }

        return $mails;
    }
}
