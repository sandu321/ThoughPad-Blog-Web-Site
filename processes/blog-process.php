//connect to create-blog.php
<?php
session_start();
require_once '../db.php';// read db file

//check if user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

mysqli_select_db($conn, "thoughtpad_db");

$sql_blog_table = "CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
mysqli_query($conn, $sql_blog_table);

$sql_alter = "ALTER TABLE blog_posts MODIFY COLUMN content LONGTEXT NOT NULL";
mysqli_query($conn, $sql_alter);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $content = mysqli_real_escape_string($conn, trim($_POST['content']));

    $query = "INSERT INTO blog_posts (user_id, title, content) VALUES ('$user_id', '$title', '$content')";
    $category = mysqli_real_escape_string($conn, $_POST['blog_category'] ?? 'General');

    $query = "INSERT INTO blog_posts (user_id, title, content, category) VALUES ('$user_id', '$title', '$content', '$category')";

    if (mysqli_query($conn, $query)) {
        header("Location: ../dashboard.php");
    } else {
        die("Publish Failed: " . mysqli_error($conn));
    }
} else {
    header("Location: ../create-blog.php");
    exit();
}
?>
