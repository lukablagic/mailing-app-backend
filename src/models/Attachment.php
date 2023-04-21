<?php

//db config
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../controllers/MailController.php";

class Attachment
{

    private ?PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->connect();
    }

    public function get(string $email)
    {
        $query = "SELECT * FROM attachments WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function insert( $attachment, $email_id)
   {
//        $attachmentData->email_id = $emailId;
//        $attachmentData->file_name = $attachment->getFileName();
//        $attachmentData->file_path = ''; // TODO: Set the file path
//        $attachmentData->file_type = $attachment->getType();
//        $attachmentData->data = $attachment->getContent();
        //{"name":"CV Luka.pdf","full_path":"CV Luka.pdf","type":"application\/pdf","tmp_name":"C:\\xampp\\tmp\\php9CB.tmp","error":0,"size":161268}
        $query = "insert into attachments (file_name, file_path, file_type, data, emails_id) VALUES (:name, :path, :type, :data, :emails_id)";
        $stmt = $this->conn->prepare($query);

        $name = $attachment->file_name;
        $path = $attachment->file_path;
        $type = $attachment->file_type;
     //   $data = file_get_contents($path);
        $emailId = $email_id->id;
        print_r($emailId);
        $data = $attachment->data;
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':path', $path);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':emails_id', $emailId);

        $stmt->execute();
    }





}
