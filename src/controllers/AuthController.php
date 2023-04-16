<?php



class AuthController {

    private $auth;

    public function __construct( Auth $auth) {
        $this->auth = $auth;
    }
    public function processRequest(string $method) {
        switch ($method) {
            case "POST":
                $data = json_decode(file_get_contents("php://input"), true);
                
                if (empty($data)) {
                    http_response_code(400);
                    echo json_encode(["error" => "Invalid input data"]);
                    break;
                }
    
                if (empty($data["action"])) {
                    http_response_code(400);
                    echo json_encode(["error" => "Action parameter is required"]);
                    break;
                }
    
                $action = $data["action"];
                
                switch ($action) {
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
            default:
                http_response_code(405);
                header("Allow: POST");
        }
    }
    
    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);
        $email = $data["email"];
        $password = $data["password"];
        // echo json_encode($email);
        // echo json_encode($password);
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

    public function logout() {
        $data = json_decode(file_get_contents("php://input"), true);
        $email = $data["email"];
        $password = $data["password"];
        if ($this->auth->logout($email, $password)) {
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
    public function register() {
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
                "message" => "Registration failed"
            ]);
        }
    }
    

}
?>