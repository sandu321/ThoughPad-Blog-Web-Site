<!--remove followers and unfollow users-->
<?php
session_start();
require_once '../db.php';// read db file

//check if user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id']) || !isset($_GET['action']) || !isset($_GET['id'])) {
    header("Location: ../dashboard.php");
    exit();
}

$my_id = $_SESSION['user_id'];
$target_id = mysqli_real_escape_string($conn, $_GET['id']);
$action = $_GET['action'];

mysqli_select_db($conn, "thoughtpad_db");
//unfollow a user or remove a follower based on the action parameter
if ($action == 'unfollow') {
    mysqli_query($conn, "DELETE FROM user_follows WHERE follower_id = '$my_id' AND following_id = '$target_id'");
} elseif ($action == 'remove') {
    mysqli_query($conn, "DELETE FROM user_follows WHERE follower_id = '$target_id' AND following_id = '$my_id'");
}

// Redirect back to the followers tab after the action
header("Location: ../dashboard.php?tab=followers");
exit();
?>
