<?php
//db config 
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../controllers/MailController.php";

class Mail
{
    private $db;
    private $conn;
    private $table_name = "emails";
    private $hostname ;


    public function __construct()
    {
        $config = parse_ini_file('.env');
        $this->hostname = $config["IMAP_HOST"];
        $this->db = new Database();
    }

    public function fetchEmailsFromServer($username, $password, $criteria)
    {
        // Connect to the server
        $imap = imap_open($this->hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());

        $emails = array();

        // Fetch all emails matching the criteria
        $emailIds = imap_search($imap, $criteria);
        foreach ($emailIds as $emailId) {
            $headerInfo = imap_headerinfo($imap, $emailId);
            $email = new stdClass();
            $email->uid = imap_uid($imap, $emailId);
            $email->subject = imap_utf8($headerInfo->subject);
            $email->fromName = imap_utf8($headerInfo->fromaddress);
            $email->from = $headerInfo->from[0]->mailbox . "@" . $headerInfo->from[0]->host;
            $email->to = imap_utf8($headerInfo->toaddress);
            $email->sent_date = date('Y-m-d H:i:s', strtotime($headerInfo->date));
            $email->body = imap_body($imap, $emailId);
            $emails[] = $email;

            // Save the email to the database
            $this->saveEmails($email);
        }

        imap_close($imap);
        echo json_encode($emails);
        header("HTTP/1.1 200 OK");
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
    
    private function sendEmail()
    {


    }
    private function getConversationHistoy()
    {

    }
    private function getAttachemnts()
    {
    }

}
?>