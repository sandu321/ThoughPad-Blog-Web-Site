<!--delete blogs-->
<?php
session_start();
require_once 'db.php';// read db file

//check if user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = mysqli_real_escape_string($conn, $_GET['id']);

mysqli_select_db($conn, "thoughtpad_db");

$query = "DELETE FROM blog_posts WHERE id = '$post_id' AND user_id = '$user_id'";

if (mysqli_query($conn, $query)) {
    $_SESSION['delete_success'] = "Blog deleted successfully!";
    header("Location: dashboard.php");
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}
?>
