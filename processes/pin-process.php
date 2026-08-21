//used to pin a post to the dashboard also unpin
<?php
session_start();
require_once '../db.php';//load db.php file

//check if user is logged in and post id is set, if not redirect to dashboard
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: ../dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = mysqli_real_escape_string($conn, $_GET['id']);

mysqli_select_db($conn, "thoughtpad_db");

$check_pin = mysqli_query($conn, "SELECT * FROM post_highlights WHERE post_id = '$post_id' AND user_id = '$user_id'");

if (mysqli_num_rows($check_pin) == 0) {
    mysqli_query($conn, "INSERT INTO post_highlights (user_id, post_id, highlighted_text) VALUES ('$user_id', '$post_id', 'pinned')");
} else {
    mysqli_query($conn, "DELETE FROM post_highlights WHERE post_id = '$post_id' AND user_id = '$user_id'");
}

//redirect back to dashboard after pinning or unpinning
header("Location: ../dashboard.php");
exit();
?>
