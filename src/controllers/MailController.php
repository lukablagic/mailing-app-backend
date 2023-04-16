<?php

//require '../config/Database.php';
//require '../models/Mail.php';
//require_once '../vendor/psr/http-message/src/MessageInterface.php';
require_once __DIR__ . "/../models/Mail.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Auth.php";
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
header('Access-Control-Allow-Origin: *');

class MailController
{
    public $gateway;

    function __construct(Mail $gateway)
    {
        $this->gateway = $gateway;
    }

  public function processRequest(string $method, ?string $id): void
    {
        if ($id) {
            
            $this->processResourceRequest($method, $id);
            
        } else {
            
            $this->processCollectionRequest($method);
            
        }
    }
    
    private function processResourceRequest(string $method, string $id): void
    {
        $product = $this->gateway->get($id);
        $rows = [];
        if ( ! $product) {
            http_response_code(404);
            echo json_encode(["message" => "Product not found"]);
            return;
        }
        
        switch ($method) {
            case "GET":
                echo json_encode($product);
                break;
                
            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"), true);
                
                $errors = $this->getValidationErrors($data, false);
                
                if ( ! empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                
                $rows = $this->gateway->update($product, $data);
                
                echo json_encode([
                    "message" => "Product $id updated",
                    "rows" => $rows
                ]);
                break;
                
            case "DELETE":
                $rows = $this->gateway->delete($id);
                
                echo json_encode([
                    "message" => "Product $id deleted",
                    "rows" => $rows
                ]);
                break;
                
            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
        }
    }
    
    private function processCollectionRequest(string $method): void
    {
        // Check if the user is authenticated
        $database = new Database("DB_HOST", "DB_NAME", "DB_USER", "DB_PASSWORD");
        $auth = new Auth($database);
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        if (!$auth->authorize($token)) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized"]);
            return;
        }
    
        switch ($method) {
            case "GET":
                // Get the user's ID from the authentication token
                $user = $auth->getUser();
                $userId = $user["id"];
    
                // Get the collection for the user
                echo json_encode($this->gateway->getAll($userId));
                break;
    
            case "POST":
                $data = (array) json_decode(file_get_contents("php://input"), true);
    
                $errors = $this->getValidationErrors($data);
    
                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
    
                // Get the user's ID from the authentication token
                $user = $auth->getUser();
                $userId = $user["id"];
    
                $data["user_id"] = $userId;
    
                $id = $this->gateway->insert($data);
    
                http_response_code(201);
                echo json_encode([
                    "message" => "Product created",
                    "id" => $id
                ]);
                break;
    
            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }
    
    
    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];
        
        if ($is_new && empty($data["name"])) {
            $errors[] = "name is required";
        }
        
        if (array_key_exists("size", $data)) {
            if (filter_var($data["size"], FILTER_VALIDATE_INT) === false) {
                $errors[] = "size must be an integer";
            }
        }
        
        return $errors;
    }





}

?>