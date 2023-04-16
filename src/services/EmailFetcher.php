<?php
class EmailFethcer {


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



}
?>