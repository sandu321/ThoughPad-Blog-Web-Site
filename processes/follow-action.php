//control user follow and unfollow action
<?php
session_start();
require_once '../db.php';//login to db file

//check user login
if (!isset($_SESSION['user_id']) || !isset($_GET['author_id']) || !isset($_GET['post_id'])) {
    header("Location: ../index.php");
    exit();
}

$my_id = $_SESSION['user_id'];
$author_id = mysqli_real_escape_string($conn, $_GET['author_id']);
$post_id = mysqli_real_escape_string($conn, $_GET['post_id']);

// prevent user from following themselves
if ($my_id == $author_id) {
    header("Location: ../view-blog.php?id=" . $post_id);
    exit();
}

mysqli_select_db($conn, "thoughtpad_db");

$check_follow = mysqli_query($conn, "SELECT * FROM user_follows WHERE follower_id = '$my_id' AND following_id = '$author_id'");

if (mysqli_num_rows($check_follow) == 0) {
    mysqli_query($conn, "INSERT INTO user_follows (follower_id, following_id) VALUES ('$my_id', '$author_id')");
} else {
    mysqli_query($conn, "DELETE FROM user_follows WHERE follower_id = '$my_id' AND following_id = '$author_id'");
}

// Redirect back to the appropriate page after follow/unfollow action
if ($post_id == '0') {
    header("Location: ../dashboard.php?tab=followers");
} else {
    header("Location: ../view-blog.php?id=" . $post_id);
}
exit();
?>
