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
        return $email;
    }

    public function getAll($email)
    {

        $query = "SELECT * FROM emails WHERE `to` = :to";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':to', $email);
        $stmt->execute();
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $emails;
    }

    public function insert($email)
    {
      //  var_dump($email);
        $query = "INSERT INTO emails (uid, subject, `from`,sent_date, body) VALUES (:uid, :subject, :from, :sent_date, :body)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $email->uid);
        $stmt->bindParam(':subject', $email->subject);
        $stmt->bindParam(':from', $email->from);
        //   $stmt->bindParam(':to', $email->to);
        $stmt->bindParam(':sent_date', $email->sent_date);
        $stmt->bindParam(':body', $email->body);
        $conv  = 5;
    //    $stmt->bindParam(':conversations_id', $conv);
        $stmt->execute();
        $email->id = $this->conn->lastInsertId();
    //    $this->insertRecipients($email);
        return $email;
    }
    public function repliedTo($email)
    {


    }
    public function insertRecipients($mailing_list)
    {
        foreach ($mailing_list->to as $recipient) {
            $query = "INSERT INTO recipients (emails_id,users_id) VALUES (:emails_id, :users_id)";
            $from = $recipient->getFullAddress();

            $user = $this->userGateway->getUserByEmail($from);
            $userId =  $user['id'];
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':emails_id', $mailing_list->id);
            $stmt->bindParam(':users_id', $userId);
            $stmt->execute();
            $mailing_list->id = $this->conn->lastInsertId();
           return true;

        }
        return false;
    }
    public function checkIfReply($to){

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