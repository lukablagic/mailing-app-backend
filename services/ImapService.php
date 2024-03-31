<?php

namespace Service;

use PHPMailer\PHPMailer\PHPMailer;
use Exception;

class ImapService
{
    public function __construct()
    {


    }

    public function sendEmail($email, $password, $data, $attachments)
    {
        // Instantiate a new PHPMailer object
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $email;
        $mail->Password = $password;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        //Recipients
        try {
            $mail->setFrom($email);
            if ($data['to'] != null && is_array($data['to'])) {
                foreach ($data['to'] as $to) {
                    if ($to != null) {
                        $mail->addAddress($to);
                    }
                }
            }
            if (isset($data['in_reply_to']) && is_array($data['in_reply_to'])) {
                $mail->addCustomHeader("In-Reply-To", $data['in_reply_to']);
            }
            if ($data['cc'] != null && is_array($data['cc'])) {
                foreach ($data['cc'] as $cc) {
                    if ($cc != null) {
                        $mail->addCC($cc);
                    }
                }
            }

            if ($data['bcc'] != null && is_array($data['bcc'])) {
                foreach ($data['bcc'] as $bcc) {
                    if ($bcc != null) {
                        $mail->addBCC($bcc);
                    }
                }
            }
        } catch (Exception $e) {
            http_response_code(400);
            json_encode("Invalid recipient parameters!");
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $data['subject'];
        $mail->Body = $data['body'];

        //  echo json_encode($attachments);
        if ($attachments != null) {

            try {
                $mail->addAttachment($attachments['tmp_name'], $attachments['name']);
            } catch (Exception $e) {
                http_response_code(400);
                json_encode("Invalid attachment parameters!");
            }

        }
        // Send the email
        try {
            if (!$mail->send()) {
                echo 'Message has been sent';
            }

        } catch (Exception $e) {
            echo json_encode('Message could not be sent.');
            echo json_encode('Mailer Error: ' . $mail->ErrorInfo);
        }
    }

}


?>