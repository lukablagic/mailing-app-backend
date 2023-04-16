<?php
//db config 
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../controllers/MailController.php";
class MailData{

}
class Mail
{
    private $db;
    private $conn;
    private $table_name = "emails";
    private $hostname ;


  public function __construct(Database $database)
    {
        $this->conn = $database->connect();
    }
    public function get (string $uid)
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
        $query = "SELECT * FROM emails WHERE `from` = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $emails;
    }
    public function insert($email){
        $query = "INSERT INTO emails (uid, subject, `from`, `to`, sent_date, body) VALUES (:uid, :subject, :from, :to, :sent_date, :body)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $email->uid);
        $stmt->bindParam(':subject', $email->subject);
        $stmt->bindParam(':from', $email->from);
        $stmt->bindParam(':to', $email->to);
        $stmt->bindParam(':sent_date', $email->sent_date);
        $stmt->bindParam(':body', $email->body);
        $stmt->execute();
        return $email;
    }
    public function update( $email,$data){
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
    public function delete($uid){
        $query = "DELETE FROM emails WHERE uid = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        return $uid;
    }
    public function query($query){
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $emails;
    }
    public function saveEmails($email)
    {
        $this->conn = $this->db->connect();
    
        // Check if the email already exists in the target table
        $stmt = $this->conn->prepare('SELECT * FROM emails WHERE uid = :uid');
        $stmt->bindParam(':uid', $email->uid);
        $stmt->execute();
        $existing_email = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($existing_email) {
            // Update existing email
            $query = "UPDATE emails SET subject = :subject, `from` = :from, `to` = :to, sent_date = :sent_date, body = :body WHERE uid = :uid";
            $stmt = $this->conn->prepare($query);
        } else {
            // Insert new email
            $query = "INSERT INTO emails (uid, subject, `from`, `to`, sent_date, body) VALUES (:uid, :subject, :from, :to, :sent_date, :body)";
            $stmt = $this->conn->prepare($query);
        }
    
        // Bind parameters and execute the query
        $stmt->bindParam(':uid', $email->uid);
        $stmt->bindParam(':subject', $email->subject);
        $stmt->bindParam(':from', $email->from);
        $stmt->bindParam(':to', $email->to);
        $stmt->bindParam(':sent_date', $email->sent_date);
        $stmt->bindParam(':body', $email->body);
        $stmt->execute();
    }
    
 

}
?>