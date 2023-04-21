## My Email Client

My Email Client is a simple PHP-based web application that allows users to view and manage their email messages in one place.

### Features

- Connects to your email account via IMAP protocol
- Displays email messages in a user-friendly interface
- Allows you to mark emails as read or unread, delete messages, and move emails to different folders
- Provides search functionality to find emails by keyword, sender, recipient, or date range
- Supports multiple email accounts with different configurations

### Installation

1. Clone this repository to your local machine.
2. Run `composer install` to install the required dependencies.
3. Set up a web server with PHP and point the root directory to the `public` folder.
4. Copy the `.env.example` file to `.env` and update it with your email account details and other configurations.
5. Run the application and access it through your web browser.

### Configuration

The application is configured using environment variables defined in the `.env` file. Here are the available variables:

- `APP_ENV`: The environment mode, can be `development`, `production`, or `testing`.
- `APP_DEBUG`: Whether to enable debug mode or not.
- `APP_KEY`: The encryption key used to secure user sessions.
- `DB_HOST`: The hostname of the MySQL database server.
- `DB_PORT`: The port number of the MySQL database server.
- `DB_DATABASE`: The name of the MySQL database to use.
- `DB_USERNAME`: The username of the MySQL database user.
- `DB_PASSWORD`: The password of the MySQL database user.
- `IMAP_HOST`: The hostname of the IMAP server to connect to.
- `IMAP_PORT`: The port number of the IMAP server.
- `IMAP_USERNAME`: The username of the email account to use.
- `IMAP_PASSWORD`: The password of the email account.

### Contributing

Contributions are welcome and encouraged! To contribute to this project, please follow these steps:

1. Fork this repository to your own GitHub account and clone it to your local machine.
2. Create a new branch for your feature or bug fix.
3. Make your changes and commit them with descriptive messages.
4. Push your branch to your GitHub repository and create a pull request to merge your changes back into the main branch of this repository.

### License

This project is licensed under the MIT License. See the `LICENSE` file for details.
