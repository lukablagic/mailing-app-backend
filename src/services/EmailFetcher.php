<?php

require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class EmailFetcher {

    private $hostname;
    public function __constructor () {
        $config = parse_ini_file('.env');
        $this->hostname = $config[`IMAP_HOST`];
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


public function fetchEmailsFromServerUser($email, $password)
{
    $emails = new MailData();
    $result = new stdClass();
    // Create an instance of the MailController class
    $user = new User($this->db->connect());
    if(true){   //validate user
        $criteria = 'ALL'; // this is cosntant and should be the same for all users
        // Call the fetchEmailsFromServer function
        $emails->fetchEmailsFromServer($email, $password, $criteria);
    }
    else{
        echo json_encode("Invalid user");
    }

  
}
public function sendEmail($to,$from,$password, $subject, $body, $attachment, $cc, $bcc,$name, $surname)
{
   
    // Instantiate a new PHPMailer object
    $mail = new PHPMailer(true);
    
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $from;
    $mail->Password = $password;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    
   //Recipients
   $mail->setFrom($from, $name . " " . $surname);
 //  $mail->addAddress($to, "sef sefu");     //Add a recipient
 $mail->addAddress($to);                //Name is optional
//   $mail->addReplyTo('info@example.com', 'Information');
//   $mail->addCC('cc@example.com');
//   $mail->addBCC('bcc@example.com');
    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body    = 'This is a test email sent using PHPMailer.';
    
    // Send the email
    if(!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message has been sent';
    }
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