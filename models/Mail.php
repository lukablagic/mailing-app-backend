<?php

namespace Model;

use PDO;

class Mail
{

    private $conn;


    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllThreads($team_id, $folder = 'INBOX', $limit = 20)
    {
        $query = "SELECT subject,
                        MAX(is_read) as is_read,
                        MAX(id) as id,
                        MAX(sent_date) as latest_sent_date,
                        MAX(from_name) as from_name, 
                        folder,
                        MAX(`from`) as `from`, 
                        MAX(sent_date) as sent_date
                    FROM mails
                    WHERE team_id = :team_id AND folder = :folder
                    GROUP BY subject, folder  
                    ORDER BY latest_sent_date DESC 
                    LIMIT " . $limit;
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':folder', $folder);
        $stmt->execute();
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $emails;
    }

    public function getThreadMembersBySubject($team_id, $subject, $reply_to)
    {
        $query = "SELECT mails.*,
                        GROUP_CONCAT(DISTINCT ecc.address SEPARATOR '#') as cc,
                        GROUP_CONCAT(DISTINCT eto.address SEPARATOR '#') as `to`,
                        GROUP_CONCAT(DISTINCT ebcc.address SEPARATOR '#') as bcc
                    FROM mails
                    LEFT JOIN mails_to eto ON mails.id = eto.mail_id
                    LEFT JOIN mails_cc ecc ON mails.id = ecc.mail_id
                    LEFT JOIN mails_bcc ebcc ON mails.id = ebcc.mail_id
                    WHERE 
                    team_id = :team_id AND
                    subject LIKE :subject AND
                    reply_to = :reply_to
                    GROUP BY mails.id
                    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':reply_to', $reply_to);
        $stmt->execute();
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($members as &$email) {
            if ($email['to'] != null && $email['to'] != '') {
                $email['to'] = explode('#', $email['to']);
            } else {
                $email['to'] = [];
            }
            if ($email['cc'] != null && $email['cc'] != '') {
                $email['cc'] = explode('#', $email['cc']);
            } else {
                $email['cc'] = [];
            }
            if ($email['bcc'] != null && $email['bcc'] != '') {
                $email['bcc'] = explode('#', $email['bcc']);
            } else {
                $email['bcc'] = [];
            }
        }

        return $members;
    }
    public function insert($email)
    {
        $query = "INSERT INTO mails (`uid`,  `subject`, body, sent_date, is_read, `size`, from_name, `from`,  reply_to, imap_number,charset,team_id,folder)
        VALUES (:uid, :subject, :body, :sent_date, :is_read, :size, :from_name, :from, :reply_to, :imap_number, 'UTF-8',:team_id,:folder)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $email->uid);
        $stmt->bindParam(':subject', $email->subject);
        $stmt->bindParam(':body', $email->body);
        $stmt->bindParam(':sent_date', $email->sent_date);
        $stmt->bindParam(':is_read', $email->is_read);
        $stmt->bindParam(':size', $email->size);
        $stmt->bindParam(':from_name', $email->from_name);
        $stmt->bindParam(':from', $email->from);
        $stmt->bindParam(':reply_to', $email->reply_to);
        $stmt->bindParam(':imap_number', $email->imap_number);
        $stmt->bindParam(':team_id', $email->team_id);
        $stmt->bindParam(':folder', $email->folder);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
    public function getImapNumbers($team_id, $folder)
    {
        $query = "SELECT imap_number FROM mails WHERE team_id = :team_id AND folder = :folder";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':folder', $folder);
        $stmt->execute();
        $imapNumbers = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $imapNumbers;
    }
    public function get($team_id, $id)
    {
        $query = 'SELECT * FROM mails WHERE team_id = :team_id AND id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam('team_id', $team_id);
        $stmt->bindParam('id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }
    public function exists($imap_number, $folder, $team_id)
    {
        $query = "SELECT COUNT(*) as `counter` FROM mails WHERE imap_number = :imap_number AND folder = :folder AND team_id = :team_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':imap_number', $imap_number);
        $stmt->bindParam(':folder', $folder);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['counter'] > 0;
    }
    public function updateUnread($unreadEmails, $folder,$team_id)
    {
        $query = "UPDATE mails SET is_read = 0 WHERE imap_number IN (" . implode(',', $unreadEmails) . ") AND folder = :folder AND team_id = :team_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':folder', $folder);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
    }
    public function updateRead($unreadEmails, $folder,$team_id)
    {
        $query = "UPDATE mails SET is_read = 1 WHERE imap_number NOT IN (" . implode(',', $unreadEmails) . ") AND folder = :folder AND team_id = :team_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':folder', $folder);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
    }
}
