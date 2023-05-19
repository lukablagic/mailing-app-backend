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
       $query = "insert into attachments (file_name, file_path, file_type,file_subtype,encoding,charset,content, data, emails_id) VALUES (:name, :path, :type,:file_subtype,:encoding,:content,:charset, :data, :emails_id)";
        $stmt = $this->conn->prepare($query);

        $name = $attachment->file_name;
        $path = $attachment->file_path;
        $type = $attachment->file_type;
        $file_subtype = $attachment->file_subtype;
        $encoding = $attachment->encoding;

        $content = $attachment->content;
        $charset = $attachment->charset;

        $data = $attachment->data;
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':path', $path);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':file_subtype', $file_subtype);
        $stmt->bindParam(':encoding', $encoding);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':charset', $charset);


        $stmt->bindParam(':emails_id', $email_id);

        $stmt->execute();
    }
    public function getAttachemntsByMail($emails_id)
    {
        $query = "SELECT id, file_name, file_path, file_type, file_subtype, emails_id FROM attachments WHERE emails_id = :emails_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':emails_id', $emails_id);
        $stmt->execute();
        $attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);



        return $attachments;
    }
    public function getAttachmentById($file_name){
        $query = "SELECT id, file_name, file_path, file_type,file_subtype, emails_id FROM attachments WHERE file_name = :file_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':file_name', $file_name);
        $stmt->execute();
        $attachment = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($attachment){
            return $attachment;
        }
        return false;
}
    public function getAttachmentData($file_name)
    {
        $query = "SELECT data FROM attachments WHERE file_name = :file_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':file_name', $file_name);
        $stmt->execute();
        $attachment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($attachment) {
            return $attachment['data'];
        }

        return false;
    }
    public function returnImage($file_name)
    {
      return  base64_encode($this->getAttachmentData($file_name));

    }
    public function getAttachmentContent($file_name)
    {
        $query = "SELECT content FROM attachments WHERE file_name = :file_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':file_name', $file_name);
        $stmt->execute();
        $attachment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($attachment) {
            return $attachment['content'];
        }

        return false;
    }

}
