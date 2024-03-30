<?php


class AuthController
{
    private $conn;


    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function processRequest(string $method, ?string $id, $user)
    {
        switch ($method) {
            case "POST":
                switch ($id) {
                    case "login":
                        $this->login();
                        break;
                    case "logout":
                        $this->logout();
                        break;
                    case "register":
                        $this->register();
                        break;

                    default:
                        http_response_code(400);
                        echo json_encode(["error" => "Invalid action parameter"]);
                }

                break;
            case "GET":

                switch ($id) {
                    case "user":

                        break;
                    default:
                        http_response_code(405);
                        echo json_encode(["error" => "Invalid action parameter"]);
                }
                break;
            default:
                http_response_code(405);
                header("Allow: POST,GET");

        }
    }

    public function login()
    {

        $data = json_decode(file_get_contents("php://input"), true);
        $email = $data["email"];
        $password = $data["password"];
        if ($this->auth->login($email, $password)) {
            $token = $this->auth->generateToken($email);
            http_response_code(200);
            echo json_encode([
                "message" => "Authentication successful",
                "token" => $token
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "message" => "Authentication failed"
            ]);
        }
    }

    public function logout()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $token = $data["token"];
        if ($this->auth->logout($token)) {
            http_response_code(200);
            echo json_encode([
                "message" => "Logout successful"
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "message" => "Logout failed"
            ]);
        }
    }

    public function register()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $name = $data["name"];
        $surname = $data["surname"];
        $email = $data["email"];
        $password = $data["password"];
        //$profile_picture = $data["profile_picture"];

        if ($this->auth->register($name, $surname, $email, $password)) {
            http_response_code(200);
            echo json_encode([
                "message" => "Registration successful"
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "message" => "Registration failed or user already exists!"
            ]);
        }
    }


}

?>