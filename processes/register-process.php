//register new user and connect to register.php
<?php
session_start();
require_once '../db.php';// read db.php file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    mysqli_select_db($conn, "thoughtpad_db");

    $check_user = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
    $result = mysqli_query($conn, $check_user);
    
    if (!$result) {
        die("Query Failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['register_error'] = "Username or Email already exists! Please try another.";
        header("Location: ../register.php");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

    if (mysqli_query($conn, $query)) {
    //registration successful, redirect to login page
    $_SESSION['register_success'] = "Registration successful! Please login.";
        header("Location: ../login.php");
        exit();
    } else {
        die("Insert Failed: " . mysqli_error($conn));
    }
} else {
    header("Location: ../register.php");
    exit();
}
?>
