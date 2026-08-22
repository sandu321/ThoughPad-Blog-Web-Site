//after reading post user can save the post to their saved list, if already saved it will unsave it
<?php
session_start();
require_once '../db.php';//load db.php file

//check user login
if (!isset($_SESSION['user_id']) || !isset($_GET['post_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = mysqli_real_escape_string($conn, $_GET['post_id']);

mysqli_select_db($conn, "thoughtpad_db");

// check if the post is already saved by the user
$check_query = "SELECT * FROM saved_lists WHERE user_id = '$user_id' AND post_id = '$post_id'";
$result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($result) == 0) {
    // save the post to the database
    mysqli_query($conn, "INSERT INTO saved_lists (user_id, post_id) VALUES ('$user_id', '$post_id')");
} else {
    // unsave the post from the database
    mysqli_query($conn, "DELETE FROM saved_lists WHERE user_id = '$user_id' AND post_id = '$post_id'");
}

//redirect back to the referring page or to index.php if no referrer is set
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: ../index.php#search-results");
}
exit();
?>
