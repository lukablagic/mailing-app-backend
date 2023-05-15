 


DROP TABLE IF EXISTS `references`;
DROP TABLE IF EXISTS attachments;
DROP TABLE IF EXISTS recipients;
DROP TABLE IF EXISTS recipients_type;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS emails;
set global max_allowed_packet=1000000000;
-- Create tables
CREATE TABLE users (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(45),
  surname VARCHAR(45),
  email VARCHAR(45),
  password VARCHAR(255),
  profile_picture VARCHAR(45),
  failed_attempts INT(11),
  locked TINYINT(4),
  locked_until DATETIME,
  token VARCHAR(64)
);

CREATE TABLE recipients_type (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  `type` VARCHAR(150)
);
CREATE TABLE emails (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  uid VARCHAR(150),
  in_reply_to VARCHAR(150),
  `from` VARCHAR(45),
  body VARCHAR(500),
  replied_to INT(11),
  sent_date VARCHAR(45),
  is_read TINYINT(1),
  has_attachment TINYINT(4),
  created_at DATETIME,
  `subject` VARCHAR(80),
  conversations_id INT(11)
);

CREATE TABLE recipients (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  `to` VARCHAR(150),
  emails_id INT(11),
  users_id INT(11),
  recipients_type_id INT(11),
  CONSTRAINT fk_recipients_emails FOREIGN KEY (emails_id) REFERENCES emails (id)  ON DELETE CASCADE,
  CONSTRAINT fk_recipients_users FOREIGN KEY (users_id) REFERENCES users (id)  ON DELETE CASCADE,
  CONSTRAINT fk_recipients_recipients_type FOREIGN KEY (recipients_type_id) REFERENCES recipients_type (id)  ON DELETE CASCADE
);



CREATE TABLE attachments (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  file_name VARCHAR(45),
  file_path VARCHAR(45),
  file_type VARCHAR(50),
  file_subtype VARCHAR(50),
  encoding VARCHAR(50),
   `charset` VARCHAR(50),
  content LONGBLOB,
  emails_id INT(11),
  data LONGBLOB,
  CONSTRAINT fk_attachments_emails FOREIGN KEY (emails_id) REFERENCES emails (id) ON UPDATE RESTRICT ON DELETE CASCADE
);

CREATE TABLE `references` (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  reference VARCHAR(80),
  emails_id INT(11),
  created_at DATETIME,
  CONSTRAINT fk_references_emails FOREIGN KEY (emails_id) REFERENCES emails (id) ON UPDATE RESTRICT ON DELETE CASCADE
);

INSERT INTO recipients_type (type)
VALUES ('to'), ('cc'), ('bcc');
