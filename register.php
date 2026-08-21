<!--register to website page-->
<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    <title>ThoughtPad - Register</title>
    <link rel="stylesheet" href="css/main.css?v=15000">
    <link rel="stylesheet" href="css/navbar.css?v=15000">
    <link rel="stylesheet" href="css/home.css?v=15000">
    <link rel="stylesheet" href="css/dashboard.css?v=15000">
    <script src="js/interaction.js?v=15000" defer></script>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="form-box">
        <div class="form-d">
            <h1>Create an Account</h1>
            <p>Join ThoughtPad and start blogging today!</p>
            
            <!-- Display error message if registration fails-->
            <?php if (isset($_SESSION['register_error'])): ?>
                <div class="thoughtpad-error-container-box">
                    <p class="thoughtpad-error-text-lbl"><?php echo $_SESSION['register_error']; ?></p>
                </div>
                <?php unset($_SESSION['register_error']); ?>
            <?php endif; ?>
            
            <form action="processes/register-process.php" method="POST" id="thoughtpad-register-form">
                
                <div class="input-details">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter Username" required>
                </div>

                <div class="input-details">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter Email" required>
                </div>
                
                <div class="input-details">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create Password" required>
                </div>
                
                <button type="submit" class="bton-submit">Sign Up</button>
            </form>
            
            <p class="end-form">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

</body>
</html>
