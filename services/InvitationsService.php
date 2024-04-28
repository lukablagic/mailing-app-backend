<?php

namespace Service;

use Model\User;
use Model\Teams;
use Model\TeamAddresses;
use Model\Invitations;

class InvitationsService
{

    private $invitations;



    public function __construct($conn)
    {
        $this->invitations   = new Invitations($conn);
    }

    public function createInvitationLink($team_id)
    {
        $code           = bin2hex(random_bytes(32));
        $invitation_uid = bin2hex(random_bytes(32));
        $valid_to       = date('Y-m-d H:i:s', strtotime('+1 day'));
        $response       = $this->invitations->create($team_id, $code, $invitation_uid, $valid_to);
        if ($response === false) {
            return false;
        }
        $url = $_SERVER['HTTP_HOST'] . '/register?uid=' . $invitation_uid . '&code=' . $code;
        return $url;
    }
}
