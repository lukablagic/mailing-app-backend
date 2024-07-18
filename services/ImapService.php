<?php

namespace Service;

use Model\Teams;
use Model\TeamsCredentials;
use Model\Mail;
use Model\MailTo;
use Model\MailCc;
use Model\MailReference;
use Model\Folders;
use Model\MailBcc;
use Exception;
use Utility\ImapUtility;

class ImapService
{
    private $conn;
    private $teams;
    private $mail;
    private $mailTo;
    private $mailCc;
    private $mailBcc;
    private $mailReference;
    private $teamsCredentials;
    private $folders;


    public function __construct($conn)
    {
        $this->conn             = $conn;
        $this->teams            = new Teams($this->conn);
        $this->mail             = new Mail($this->conn);
        $this->mailTo           = new MailTo($this->conn);
        $this->mailCc           = new MailCc($this->conn);
        $this->mailBcc          = new MailBcc($this->conn);
        $this->teamsCredentials = new TeamsCredentials($this->conn);
        $this->folders          = new Folders($this->conn);
        $this->mailReference    = new MailReference($this->conn);
    }

    public function syncMailsToday()
    {

        $allTeams = $this->teams->getAll();
        foreach ($allTeams as $team) {
            $credentials = $this->teamsCredentials->getByTeamId($team['id']);
            $imapUtlity = new ImapUtility($credentials['imap_server'], $credentials['imap_port'], $credentials['protocol'], $credentials['use_ssl'] === 1);

            $userFolders = $this->folders->getAll($team['id']);

            foreach ($userFolders as $folder) {
                $parsedEmails = $imapUtlity->getParsedEmails($credentials['email'], $credentials['access_password'], $folder);
                $this->conn->beginTransaction();
                try {
                    foreach ($parsedEmails as $parsedEmail) {
                        $parsedEmail->team_id = $team['id'];
                        $parsedEmail->folder  = $folder;
                        // chcek if mail exits 
                        $mailExists = $this->mail->exists($parsedEmail->imap_number, $folder, $parsedEmail->team_id);
                        if ($mailExists) {
                            continue;
                        }
                        $mail_id = $this->mail->insert($parsedEmail);

                        if (!empty($parsedEmail->to)) {
                            foreach ($parsedEmail->to as $to) {
                                $this->mailTo->insert($mail_id, $to);
                            }
                        }
                        if (!empty($parsedEmail->cc)) {
                            foreach ($parsedEmail->cc as $cc) {
                                $this->mailCc->insert($mail_id, $cc);
                            }
                        }
                        if (!empty($parsedEmail->bcc)) {
                            foreach ($parsedEmail->bcc as $bcc) {
                                $this->mailBcc->insert($mail_id, $bcc);
                            }
                        }
                        if (!empty($parsedEmail->references)) {
                            foreach ($parsedEmail->references as $reference) {
                                $this->mailReference->insert($mail_id, $reference);
                            }
                        }
                    }

                    $this->conn->commit();
                } catch (Exception $e) {
                    $this->conn->rollback();

                    var_dump('Error inserting email', $e->getMessage(), $e->getTraceAsString());
                    continue;
                }
            }
        }
    }
    public function syncAllFolders()
    {

        $allTeams = $this->teams->getAll();
        foreach ($allTeams as $team) {
            $credentials = $this->teamsCredentials->getByTeamId($team['id']);
            $imapUtility = new ImapUtility($credentials['imap_server'], $credentials['imap_port'],  $credentials['protocol'], $credentials['use_ssl'] === 1);

            $imapFolders = $imapUtility->getFolders($credentials['email'], $credentials['access_password']);

            foreach ($imapFolders as $folder) {
                if ($this->folders->exists($team['id'], $folder) === false) {
                    $this->folders->insert($team['id'], $folder);
                }
            }
        }
    }
    // Sync all emails from all folders
    public function syncAllEmails()
    {
        $allTeams = $this->teams->getAll();
        foreach ($allTeams as $team) {
            var_dump($team['name']);

            $credentials       = $this->teamsCredentials->getByTeamId($team['id']);
            $credentialsActive = $this->validateCredentials($credentials);
            
            var_dump('active', $credentialsActive);
            
            if (!$credentialsActive) {
                continue;
            }

            $imapUtility = new ImapUtility($credentials['imap_server'], $credentials['imap_port'],  $credentials['protocol'], $credentials['use_ssl'] === 1);

            $userFolders = $this->folders->getAll($team['id']);

            foreach ($userFolders as $folder) {
                $parsedEmails = $imapUtility->getAll($credentials['email'], $credentials['access_password'], $folder);
                $this->conn->beginTransaction();
                try {
                    foreach ($parsedEmails as $parsedEmail) {
                        $parsedEmail->team_id = $team['id'];
                        $parsedEmail->folder  = $folder;
                        // chcek if mail exits 
                        $mailExists = $this->mail->exists($parsedEmail->imap_number, $folder, $parsedEmail->team_id);
                        if ($mailExists) {
                            continue;
                        }
                        $mail_id = $this->mail->insert($parsedEmail);

                        if (!empty($parsedEmail->to)) {
                            foreach ($parsedEmail->to as $to) {
                                $this->mailTo->insert($mail_id, $to);
                            }
                        }
                        if (!empty($parsedEmail->cc)) {
                            foreach ($parsedEmail->cc as $cc) {
                                $this->mailCc->insert($mail_id, $cc);
                            }
                        }
                        if (!empty($parsedEmail->bcc)) {
                            foreach ($parsedEmail->bcc as $bcc) {
                                $this->mailBcc->insert($mail_id, $bcc);
                            }
                        }
                        if (!empty($parsedEmail->references)) {
                            foreach ($parsedEmail->references as $reference) {
                                $this->mailReference->insert($mail_id, $reference);
                            }
                        }
                    }

                    $this->conn->commit();
                } catch (Exception $e) {
                    $this->conn->rollback();

                    var_dump('Error inserting email', $e->getMessage(), $e->getTraceAsString());
                    continue;
                }
            }
        }
    }

    public function syncIsRead()
    {
        $allTeams = $this->teams->getAll();
        foreach ($allTeams as $team) {
            $credentials = $this->teamsCredentials->getByTeamId($team['id']);
            $imapUtility = new ImapUtility($credentials['imap_server'], $credentials['imap_port'],  $credentials['protocol'], $credentials['use_ssl'] === 1);

            $userFolders = $this->folders->getAll($team['id']);

            foreach ($userFolders as $folder) {
                $unreadEmails = $imapUtility->getUnreadMails($credentials['email'], $credentials['access_password'], $folder);

                if (empty($unreadEmails) === false) {
                    $this->mail->updateUnread($unreadEmails, $folder, $team['id']);
                    $this->mail->updateRead($unreadEmails, $folder, $team['id']);
                }
            }
        }
    }

    private function validateCredentials($credentials)
    {
        if (empty($credentials['imap_server']) || empty($credentials['imap_port']) || empty($credentials['protocol']) || empty($credentials['use_ssl']) || empty($credentials['email']) || empty($credentials['access_password'])) {
            return false;
        }

        return true;
    }

    public function syncSent($team_id)
    {
        $credentials = $this->teamsCredentials->getByTeamId($team_id);
        $imapUtility = new ImapUtility($credentials['imap_server'], $credentials['imap_port'],  $credentials['protocol'], $credentials['use_ssl'] === 1);

        $folder = 'INBOX.Sent';// TODO " get from db

        $parsedEmails = $imapUtility->getAll($credentials['email'], $credentials['access_password'], $folder);

        $this->conn->beginTransaction();
        
        try {
            foreach ($parsedEmails as $parsedEmail) {
                $parsedEmail->team_id = $team_id;
                $parsedEmail->folder  = $folder;

                $mailExists = $this->mail->exists($parsedEmail->imap_number, $folder, $parsedEmail->team_id);

                if ($mailExists) {
                    continue;
                }

                $mail_id = $this->mail->insert($parsedEmail);

                if (!empty($parsedEmail->to)) {
                    foreach ($parsedEmail->to as $to) {
                        $this->mailTo->insert($mail_id, $to);
                    }
                }
                if (!empty($parsedEmail->cc)) {
                    foreach ($parsedEmail->cc as $cc) {
                        $this->mailCc->insert($mail_id, $cc);
                    }
                }
                if (!empty($parsedEmail->bcc)) {
                    foreach ($parsedEmail->bcc as $bcc) {
                        $this->mailBcc->insert($mail_id, $bcc);
                    }
                }
                if (!empty($parsedEmail->references)) {
                    foreach ($parsedEmail->references as $reference) {
                        $this->mailReference->insert($mail_id, $reference);
                    }
                }
            }

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollback();

            var_dump('Error inserting email', $e->getMessage(), $e->getTraceAsString());
        }
    }
}
