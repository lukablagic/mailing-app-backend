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

    public function getAllRecieved($email)
    {

        $query = "SELECT emails.id,in_reply_to, uid,subject,`from`,body,sent_date,is_read,has_attachment 
FROM emails
JOIN recipients ON recipients.emails_id = emails.id
JOIN users ON users.id = recipients.users_id
WHERE users.email = :email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $emails;
    }

    public function insert($email, $userId)
    {
        //var_dump($email);

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
        $this->setRecipients($email->to, $emailId['id'], $userId);
        return $emailId;
    }


    public function setRecipients($recipients, $emailId, $users_id)
    {
        foreach ($recipients as $recipient) {
            if (!$recipient->getAddress() == null) {
                $query = "INSERT INTO recipients (emails_id,users_id, `to`) VALUES (:emails_id,:users_id,:to)";
                $stmt = $this->conn->prepare($query);

                $stmt->bindParam(':emails_id', $emailId);
                $stmt->bindParam(':users_id', $users_id['id']);
                $address = $recipient->getAddress();
                $stmt->bindParam(':to', $address);
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