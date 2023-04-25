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

    public function getAllRecieved($token)
    {

        $query = "SELECT * FROM emails JOIN recipients ON recipients.emails_id = emails.id JOIN users ON users.id = recipients.users_id WHERE users.token = :token";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
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
        $query = "INSERT INTO emails (uid, subject, `from`,sent_date, body) VALUES (:uid, :subject, :from, :sent_date, :body)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $email->uid);
        $stmt->bindParam(':subject', $email->subject);
        $stmt->bindParam(':from', $email->from);
        //   $stmt->bindParam(':to', $email->to);
        $stmt->bindParam(':sent_date', $email->sent_date);
        $stmt->bindParam(':body', $email->body);
        $conv = 5;
        //    $stmt->bindParam(':conversations_id', $conv);
        $stmt->execute();

        $emailId = $this->getEmailId($email->uid);
        //  var_dump($emailId);
        $this->setRecipients($email->to, $emailId['id'], $userId);
        //    var_dump($emailId);
        return $emailId;
    }

    public function setRecipients($recipients, $emailId, $userID)
    {
        //   var_dump($recipients);
        foreach ($recipients as $recipient) {
            $query = "INSERT INTO recipients (emails_id,users_id) VALUES (:emails_id, :users_id)";

            $from = $recipient->getAddress();
            $user = $this->userGateway->getUserByEmail($from);
            $userId = $user['id'];
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':emails_id', $emailId);
            $stmt->bindParam(':users_id', $userId);
            $stmt->execute();
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