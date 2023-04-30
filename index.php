    <?php


    spl_autoload_register(function ($class) {
        require __DIR__ . "/src/$class.php";
    });


    require_once __DIR__ . "/src/config/Database.php";
    require_once __DIR__ . "/src/controllers/MailController.php";
    require_once __DIR__ . "/src/controllers/AuthController.php";
    require_once __DIR__ . "/src/services/EmailFetcher.php";


    set_exception_handler("ErrorHandler::handleException");
    set_error_handler("ErrorHandler::handleError");

    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
    // Get the request method and URI path
    $parts = explode("/", $_SERVER["REQUEST_URI"]);
    $endpoint = $parts[1] ?? null;
    $id = $parts[2] ?? null;
    $action = $parts[3] ?? null;

    $database = new Database("DB_HOST", "DB_NAME", "DB_USER", "DB_PASSWORD");
    $userGateway = new User($database);
    $mailGateway = new Mail($database, $userGateway);
    $authGateway = new Auth($database, $userGateway);
    $attachmentGateway = new Attachment($database);
    $emailFetcherGateway = new EmailFetcher($attachmentGateway, $mailGateway, $userGateway);

    switch ($endpoint) {
        case 'emails':
            $mailController = new MailController($mailGateway, $userGateway, $emailFetcherGateway,$attachmentGateway);
            $mailController->processRequest($_SERVER["REQUEST_METHOD"], $id,$action);
             break;
        case 'auth':
            $authController = new AuthController($authGateway);
            $authController->processRequest($_SERVER["REQUEST_METHOD"],$id);
            break;
        default:
            http_response_code(404);
            echo json_encode(["error" => "Invalid API endpoint"]);
    }





