<!--user login-->
<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ThoughtPad - Login</title>
    <link rel="stylesheet" href="css/main.css?v=12500">
    <link rel="stylesheet" href="css/navbar.css?v=12500">
    <link rel="stylesheet" href="css/home.css?v=12500">
    <link rel="stylesheet" href="css/dashboard.css?v=12500">
    <script src="js/interaction.js?v=12500" defer></script>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="form-box">
        <div class="form-d">
            <h1>Welcome Back</h1>
            <p>Log in to your account to continue blogging.</p>
            
            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="thoughtpad-error-container-box">
                    <p class="thoughtpad-error-text-lbl"><?php echo $_SESSION['login_error']; ?></p>
                </div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>
            
            <form action="processes/login-process.php" method="POST">
                
                <div class="input-details">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter Username" required>
                </div>
                
                <div class="input-details">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter Password" required>
                </div>
                
                <button type="submit" class="bton-submit">Log In</button>
            </form>
            
            <p class="end-form">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>

</body>
</html>
