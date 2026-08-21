<!--for all pages one navba-->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$nav_search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<nav class="navbar">
    <a href="index.php" class="nav-logo-box">
        <img src="images/logo.png" alt="ThoughtPad Logo" class="site-logo">
        <span class="logotext">ThoughtPad</span>
    </a>
    
    <div class="nav-search-wrapper">
        <form action="index.php#search-results" method="GET" class="nav-search-form">
        <!-- search bar -->
        <input type="text" name="search" placeholder="🔍 Search stories..." value="<?php echo htmlspecialchars($nav_search); ?>">
        </form>
    </div>
    
    <!-- main navigation links -->
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="logout.php" class="bton-register">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php" class="bton-register">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>
