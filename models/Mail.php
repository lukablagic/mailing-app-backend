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
    public function getAllThreads($team_id, $folder = 'INBOX')
    {
        $query = "SELECT `subject`,
                is_read,id,
                sent_date,
                `from`,
                folder,
                from_name
            FROM mails
            WHERE team_id = :team_id AND
            folder = :folder
            ORDER BY sent_date DESC 
            LIMIT 30";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':folder', $folder);
        $stmt->execute();
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $emails;
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
    // getImapNumbers
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
}
