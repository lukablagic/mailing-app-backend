<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization');
//require '../config/Database.php';
//require '../models/Mail.php';
//require_once '../vendor/psr/http-message/src/MessageInterface.php';
require_once __DIR__ . "/../models/Mail.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Auth.php";
require_once __DIR__ . "/../models/Attachment.php";
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//1.	GET /emails - Retrieves a list of all received emails from the MySQL database
//2.	GET /emails/{id} - Retrieves the details of a specific email by its ID

//4.	PUT /emails/{id}/status - Updates the status of an email (read/unread) on the mail server
//5.	POST /emails/{id}/reply - Sends a reply email to the sender of the specified email
//6.	POST /emails/{id}/forward - Sends a forwarded email to one or more recipients
//7.	GET /emails/{id}/conversation - Retrieves the conversation history for the specified email
//8.	GET /emails/{id}/attachments - Retrieves the attachments (if any) for the specified email


class MailController
{
    public $mailGateway;
    private $emailFetcherGateway;
    private $authGateway;
    private $attachmentGateway;

    public function __construct(Mail $mailGateway, EmailFetcher $emailFetcherGateway, Auth $authGateway, Attachment $attachmentGateway)
    {
        $this->attachmentGateway = $attachmentGateway;
        $this->mailGateway = $mailGateway;
        $this->emailFetcherGateway = $emailFetcherGateway;
        $this->authGateway = $authGateway;
    }

    public function processRequest(string $method, ?string $id, ?string $action): void
    {
        if ($id) {

            $this->processResourceRequest($method, $id, $action);

        } else {

            $this->processCollectionRequest($method);

        }
    }


    private function processCollectionRequest(string $method): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS,PUT');
        header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization');

        $user = $this->authGateway->authenticateCall();
        if (!$user) {

            http_response_code(401);
            echo json_encode(["message" => "Unauthorized"]);
            return;
        }

        switch ($method) {
            case "GET":
                //fetch all emails from imap server
                $response = $this->mailGateway->getEmailsByUser($user['email']);
                $this->emailFetcherGateway->fetchSent($user['email'], $user['password']);
                $this->emailFetcherGateway->fetchInbox($user['email'], $user['password']);

                http_response_code(200);
                echo json_encode(["message" => "Emails fetched",
                    "emails" => $response
                ]);

                break;
            //3.	POST /emails - Sends an email using SMTP protocol to one or more recipients
            case "POST":
                $data = json_decode(file_get_contents("php://input"), true);

                if (isset($_FILES['file'])) {
                    $attachment = $_FILES['file'];
                    $this->emailFetcherGateway->sendEmail($user['email'], $user['password'], $data, $attachment);
                }

                $this->emailFetcherGateway->sendEmail($user['email'], $user['password'], $data, null);
                http_response_code(201);
                echo json_encode([
                    "message" => "Message sent",
                    // "id" => $data
                ]);
                break;

            default:

                http_response_code(405);
                header("Allow: GET, POST");
        }
    }

    private function processResourceRequest(string $method, string $id, $action): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization');

        $user = $this->authGateway->authenticateCall();
        //  var_dump($user);
        if (!$user) {

            http_response_code(401);
            echo json_encode(["message" => "Unauthorized"]);
            return;
        }
        switch ($method) {

            case "PUT":
                if ($action == "status") {
                    $data = json_decode(file_get_contents("php://input"), true);
                    $status = $data["status"];
                    $this->emailFetcherGateway->updateEmailStatus($user['email'], $user['password'], $id, $status);

                    $this->mailGateway->updateStatus($id, $status);
                    $responseStatus = $status;
                    http_response_code(200);
                    echo json_encode([
                        "message" => "Email with id $id status updated to  $responseStatus",
                    ]);
                } else {
                    http_response_code(405);
                    echo json_encode(["error" => "Invalid action parameter"]);
                }
                break;

            case "DELETE":
                $data = json_decode(file_get_contents("php://input"), true);
                //  var_dump($data['uid']);
                if ($id == "delete") {
                    $imapRemove = $this->emailFetcherGateway->deleteEmail($user['email'], $user['password'], $data['uid']);
                   $database = $this->mailGateway->delete($id); // delete from database
                        http_response_code(200);
                        echo json_encode([
                            "message" => "Email " . $data['uid'] . " deleted",
                        ]);

                }
                break;
            case "GET":

                if ($action == "attachments") {
                    // Set the appropriate headers
                    header('Content-Type: application/json');
                    $response = $this->attachmentGateway->getAttachemntsByMail($id);
                    http_response_code(200);
                    echo json_encode(["message" => "Attachments fetched",
                        "attachments" => $response
                    ]);
                }
                if ($action == "data") {
                    // Set the appropriate headers
                    $attachment = $this->attachmentGateway->getAttachmentById($id);
                    header('Content-Type: ' . $attachment['file_type'] . '/' . $attachment['file_subtype']);
                    if ($attachment['file_type'] == 'image') {
                        header('Content-Type: application/json');
                        $response = $this->attachmentGateway->returnImage($id);
                        echo json_encode(["data" => '<img  src="data:' . $attachment['file_type'] . '/' . $attachment['file_subtype'] . ';base64,' . $response . '" />']);
                    } else {
                        $response = $this->attachmentGateway->getAttachmentData($id);
                        echo $response;

                    }
                }


                break;
            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
        }
    }


}

?>