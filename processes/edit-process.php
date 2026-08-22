// connect to edit-blog.php
/* when user edit thier current blogs this file update the blog */
<?php
session_start();
require_once '../db.php';//read db file

// check user login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $post_id = mysqli_real_escape_string($conn, $_POST['post_id']);
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $content = mysqli_real_escape_string($conn, trim($_POST['content']));

    mysqli_select_db($conn, "thoughtpad_db");

    $query = "UPDATE blog_posts SET title = '$title', content = '$content' WHERE id = '$post_id' AND user_id = '$user_id'";

    if (mysqli_query($conn, $query)) {
        header("Location: ../dashboard.php");
    } else {
        die("Update Failed: " . mysqli_error($conn));
    }
} else {
    header("Location: ../dashboard.php");
    exit();
}
?>
