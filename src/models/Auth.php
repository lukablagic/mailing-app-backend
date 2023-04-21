<?php
require_once __DIR__ . '/../../src/services/UpdateEmail.php';

class Auth
{

    private $conn;
    private $user;
    private $token;
    private $userGateway;

    public function __construct($database, User $userGateway)
    {

            $this->conn = $database->connect();
            $this->user = null;
            $this->token = null;
            $this->userGateway = $userGateway;


    }

    public function login($email, $password)
    {
        if ($this->userGateway->userExisits($email, $password)) {

            return true;
        }

        return false;
    }

    public function register($name, $surname, $email, $password)
    {
        $userExists = $this->userGateway->userExisits($email, $password);
        if ($userExists) {
            http_response_code(400);
            return false;
        }


        if ($this->userGateway->insert($name, $surname, $email, $password, null)) {
            return true;
        }

        return false;
    }

    public function generateToken($email)
    {
        $token = bin2hex(random_bytes(32));
        $stmt = $this->conn->prepare("UPDATE users SET token = ? WHERE email =  ?");
        $stmt->execute([$token, $email]);
        return $token;
    }

    public function logout($email, $password)
    {
        $user = $this->userGateway->updateUserToken(null, $email,$password);
        if ($user) {
            return true;
        }

        return false;
    }


    public function authorize($token)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        echo json_encode($token);
        if ($user) {

            return true;
        }

        return false;
    }


}

?>