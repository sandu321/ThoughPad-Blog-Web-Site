# Web Project -ThoughPad Blog web site

## How to run this project locally:
## ⚙️ Database Configuration
Since the `db.php` file is ignored for security reasons, you need to create it manually to run the project.

1. Create a database named `your_database_name` in your local server (MySQL/XAMPP).
2. Import the database schema (if an `.sql` file is provided).
3. Create a new file named `db.php` in the root directory.
4. Add the following connection code into your `db.php` file and update it with your local credentials:

```php
<?php
$host = 'localhost';
$db   = 'your_database_name'; // Change to your local DB name
$user = 'root';               // Change to your local DB user
$pass = '';                   // Change to your local DB password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

5. Import the database and run the project using XAMPP/WAMP.




