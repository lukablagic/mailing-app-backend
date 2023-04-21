<?php
//require_once __DIR__ . '/../../src/config/Database.php';
//require_once __DIR__ . '/../../src/models/User.php';
//require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
//require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php';
//require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/SMTP.php';
////require __DIR__ . '/vendor/autoload.php';
//
//class UpdateEmail
//{
//    private $conn;
//    private $db;
//
//    public function __construct(Database $db)
//    {
//        $this->db = $db;
//        try {
//            $this->conn = $db->connect();
//            echo "Database connection successful.";
//        } catch(PDOException $e) {
//            echo "Database connection failed: " . $e->getMessage();
//        }
//    }
//
//    public function update($email, $password)
//    {
//
//        // Connect to the Google IMAP server
//        $imap = imap_open("{imap.gmail.com:993/ssl}INBOX", $email, $password);
//
//        // Fetch new email messages
//        $messages = imap_search($imap, "ALL");
//
//        // Loop through the messages and update the database
//        foreach ($messages as $message_number) {
//            $mailDatabase = new Mail($this->db);
//            // Parse the message data
//            $headerInfo = imap_headerinfo($imap, $message_number);
//            $emailSave = new stdClass();
//            $emailSave->uid = imap_uid($imap, $message_number);
//            $emailSave->subject = imap_utf8($headerInfo->subject);
//            $emailSave->fromName = imap_utf8($headerInfo->fromaddress);
//            $emailSave->from = $headerInfo->from[0]->mailbox . "@" . $headerInfo->from[0]->host;
//            $emailSave->to = imap_utf8($headerInfo->toaddress);
//            $emailSave->sent_date = date('Y-m-d H:i:s', strtotime($headerInfo->date));
//            $emailSave->body = imap_body($imap, $message_number);
//
//            // Insert the message data into the database
//            $mailDatabase->insert($emailSave);
//        }
//
//        // Close the connection to the IMAP server
//        imap_close($imap);
//    }
//    public function updateDB()
//    {
//
//        $user = new User($this->db);
//        $users = $user->getAllUsersWithToken();
//        foreach ($users as $user) {
//            $this->update($user->email, $user->password);
//        }
//    }
//}
//
//$database = new Database("DB_HOST", "DB_NAME", "DB_USER", "DB_PASSWORD");
//
//$updater = new UpdateEmail($database);
//// Update the database with new email messages
//$updater->updateDB();
//
//?>