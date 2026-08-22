<!--update profile picture, username, password, and about me section-->
<?php
session_start();
require_once '../db.php';// read db file

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $new_username = mysqli_real_escape_string($conn, trim($_POST['new_username']));
    $new_password = trim($_POST['new_password']);

    mysqli_select_db($conn, "thoughtpad_db");

    $check_query = "SELECT * FROM users WHERE username = '$new_username' AND id != '$user_id' LIMIT 1";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['settings_error'] = "Username already taken! Please try another.";
        header("Location: ../dashboard.php?tab=settings");
        exit();
    }

    $update_user = "UPDATE users SET username = '$new_username' WHERE id = '$user_id'";
    mysqli_query($conn, $update_user);
    $_SESSION['username'] = $new_username;

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_pass = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'";
        mysqli_query($conn, $update_pass);
    }

    $new_about = mysqli_real_escape_string($conn, trim($_POST['new_about']));
    mysqli_query($conn, "UPDATE users SET about = '$new_about' WHERE id = '$user_id'");

    //PROFILE IMAGE UPLOAD LOGIC
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png", "webp" => "image/webp");
        $filename = $_FILES['profile_image']['name'];
        $filetype = $_FILES['profile_image']['type'];
        $filesize = $_FILES['profile_image']['size'];
    
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (array_key_exists(strtolower($ext), $allowed)) {
            $new_filename = "profile_" . $user_id . "." . $ext;
            $upload_path = "../images/" . $new_filename;
            $db_save_path = "images/" . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                mysqli_query($conn, "UPDATE users SET profile_pic = '$db_save_path' WHERE id = '$user_id'");
            }
        }
    }

    $_SESSION['settings_success'] = "Settings updated successfully!";
    header("Location: ../dashboard.php?tab=settings");
    exit();
} else {
    header("Location: ../dashboard.php");
    exit();
}
?>
