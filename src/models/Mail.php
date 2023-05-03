<?php
//db config 
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../controllers/MailController.php";

class Mail
{

    private $conn;
    private $userGateway;


    public function __construct(Database $database, User $userGateway)
    {
        $this->conn = $database->connect();
        $this->userGateway = $userGateway;
    }

    public function get(string $uid)
    {
        $query = "SELECT * FROM emails WHERE uid = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        $email = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($email && $stmt->rowCount() > 0) {
            return $email;
        }
        return false;
    }

    public function getUid($id){
        $query = "SELECT * FROM emails WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $email = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($email && $stmt->rowCount() > 0) {
            return $email['uid'];
        }
        return false;
    }
    public function updateStatus($id, $status)
    {
        if($status){
            $status = 1;
        }else{
            $status = 0;
        }
        $query = "UPDATE emails SET is_read = :is_read WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':is_read', $status);
        $stmt->execute();
    }

    public function getEmailsByUser($email) {
        $query = "SELECT 
        emails.id, 
        emails.in_reply_to, 
        emails.uid, 
        emails.subject, 
        emails.`from`, 
        emails.body, 
        emails.sent_date, 
        emails.is_read, 
        emails.has_attachment,
        GROUP_CONCAT(DISTINCT IF(recipients_type.type = 'to', recipients.`to`, NULL)) AS to_recipients,
        GROUP_CONCAT(DISTINCT IF(recipients_type.type = 'cc', recipients.`to`, NULL)) AS cc_recipients,
        GROUP_CONCAT(DISTINCT IF(recipients_type.type = 'bcc', recipients.`to`, NULL)) AS bcc_recipients
    FROM emails
    LEFT JOIN recipients ON recipients.emails_id = emails.id
    LEFT JOIN recipients_type ON recipients_type.id = recipients.recipients_type_id
    JOIN users ON users.id = recipients.users_id
    WHERE users.email = :email
    GROUP BY emails.id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $emails;
    }


    public function insert($email, $userId)
    {

        $existingEmail = $this->get($email->uid);
        if ($existingEmail) {
            return false;
        }
        $query = "INSERT INTO emails (uid, subject, `from`,sent_date,in_reply_to, body,is_read,has_attachment) VALUES (:uid, :subject, :from, :sent_date,:in_reply_to, :body,:is_read,:has_attachment)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $email->uid);
        $stmt->bindParam(':subject', $email->subject);
        $stmt->bindParam(':is_read', $email->is_read);
        $stmt->bindParam(':has_attachment', $email->has_attachment);
        $stmt->bindParam(':in_reply_to', $email->in_reply_to[0]);
        $stmt->bindParam(':from', $email->from);
        $stmt->bindParam(':sent_date', $email->sent_date);
        $stmt->bindParam(':body', $email->body);
        $stmt->execute();
        $emailId = $this->getEmailId($email->uid);
        $recipients = $this->combineRecipients($email->to, $email->cc, $email->bcc);
        var_dump($recipients);
        $this->setRecipients($recipients , $emailId['id'], $userId);
        return $emailId;
    }
    private function combineRecipients($to, $cc, $bcc)
    {
        $recipients = array();
        foreach ($to as $recipient) {
            $recipients[] = array(
                'address' => $recipient->getAddress(),
                'type' => 1
            );
        }
        foreach ($cc as $recipient) {
            $recipients[] = array(
                'address' => $recipient->getAddress(),
                'type' => 2
            );
        }
        foreach ($bcc as $recipient) {
            $recipients[] = array(
                'address' => $recipient->getAddress(),
                'type' => 3
            );
        }

        return $recipients;
    }

    public function setRecipients($recipients, $emailId, $users_id )
    {

        foreach ($recipients as $recipient) {
            if (!$recipient['address'] == null) {
                $query = "INSERT INTO recipients (emails_id,users_id, recipients_type_id,`to`) VALUES (:emails_id,:users_id,:recipients_type_id,:to)";
                $stmt = $this->conn->prepare($query);

                $stmt->bindParam(':emails_id', $emailId);
                $stmt->bindParam(':recipients_type_id', $recipient['type']);
                $stmt->bindParam(':users_id', $users_id['id']);
                $stmt->bindParam(':to', $recipient['address']);
                $stmt->execute();
            }

        }

    }


    public function getEmailId($uid)
    {
        $query = "SELECT id FROM emails WHERE uid = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        $email = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($email && $stmt->rowCount() > 0) {
            return $email;
        }
        return false;
    }

    public function checkMailExists($uid)
    {
        $query = "SELECT id FROM emails WHERE uid = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        $email = $stmt->fetch(PDO::FETCH_ASSOC);
        //var_dump($email);
        if ($email && $stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public function update($email, $data)
    {
        $query = "UPDATE emails SET subject = :subject, `from` = :from, `to` = :to, sent_date = :sent_date, body = :body WHERE uid = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $email->uid);
        $stmt->bindParam(':subject', $email->subject);
        $stmt->bindParam(':from', $email->from);
        $stmt->bindParam(':to', $email->to);
        $stmt->bindParam(':sent_date', $email->sent_date);
        $stmt->bindParam(':body', $email->body);
        $stmt->execute();
        return $data;
    }

    public function delete($uid)
    {
        $query = "DELETE FROM emails WHERE uid = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        return $uid;
    }

    public function query($query)
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $emails;
    }


}

?>