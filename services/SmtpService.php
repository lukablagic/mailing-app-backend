<?php

namespace Service;

use Model\TeamsCredentials;
use PHPMailer\PHPMailer\PHPMailer;
use Exception;
use Utility\RequestHandler;

class SmtpService
{

    private $teamCredentials;

    public function __construct($conn)
    {
        $this->teamCredentials = new TeamsCredentials($conn);
    }

    public function sendEmail($team_id, $data, $attachments = null)
    {
        $credentials = $this->teamCredentials->getByTeamId($team_id);
        $email = $credentials['email'];
        $password = $credentials['access_password'];
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host = $credentials['smtp_server'];
        $mail->SMTPAuth = true;
        $mail->Username = $email;
        $mail->Password = $password;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        //Recipients
        try {
            $mail->setFrom($email);
            if (empty($data['to']) === false && is_array($data['to'])) {
                foreach ($data['to'] as $to) {
                    if ($to != null) {
                        $mail->addAddress($to);
                    }
                }
            } else {
                return false;
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
        try {
            if (!$mail->send()) {
                echo 'Message has been sent';
            }
        } catch (Exception $e) {
            var_dump(json_encode('Mailer Error: ' . $mail->ErrorInfo));
            return false;
        }
    }
}
