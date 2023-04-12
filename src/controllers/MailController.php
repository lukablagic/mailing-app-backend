<?php

//require '../config/Database.php';
//require '../models/Mail.php';
//require_once '../vendor/psr/http-message/src/MessageInterface.php';
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../models/Mail.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
header('Access-Control-Allow-Origin: *');

class MailController
{
    public $app;
    private $db;
    private $mailModel;

    function __construct()
    {
        // Initialize database connection
        $this->db = new Database();
        $this->mailModel = new Mail();
    }

    public function displayEmails($to, $subject, $body)
    {

    }



    public function fetchEmailsFromServer($email, $password)
    {
        $emails = new Mail();
        $result = new stdClass();
        // Create an instance of the MailController class
        $user = new User($this->db->connect());
        if($user->validateUser($email, $password)){
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
    public function replyToEmail()
    {
    }
    public function forwardEmail()
    {

    }
}

?>