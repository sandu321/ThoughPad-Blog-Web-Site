//connect to login-process.php
/* when user clicks the login button, this file will be called to process the login credentials */
<?php
session_start();
require_once '../db.php';//login to db file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);

    $query = "SELECT * FROM users WHERE username='$username' LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        //password verification 
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            //if password is correct, redirect to dashboard.php
            header("Location: ../dashboard.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid password. Please try again.";
            header("Location: ../login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "User not found. Please register first.";//not registered
        header("Location: ../login.php");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>
