<!--this is the dashboard page where users can view their library, profile, stats, and settings.-->
<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
mysqli_select_db($conn, "thoughtpad_db");

//user profile
$user_info_res = mysqli_query($conn, "SELECT email, profile_pic FROM users WHERE id = '$user_id' LIMIT 1");
$user_info = mysqli_fetch_assoc($user_info_res);
$user_email = $user_info['email'] ?? '';
$profile_pic = $user_info['profile_pic'] ?? 'images/user.png';

if (empty($profile_pic) || $profile_pic === 'images/default-avatar.png' || !file_exists($profile_pic)) {
    $profile_pic = 'images/user.png';
}

$followers_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user_follows WHERE following_id = '$user_id'");
$followers_row = mysqli_fetch_assoc($followers_res);
$total_followers = $followers_row['total'];

$following_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user_follows WHERE follower_id = '$user_id'");
$following_row = mysqli_fetch_assoc($following_res);
$total_following = $following_row['total'];

$following_list = mysqli_query($conn, "SELECT users.id, users.username, users.profile_pic FROM user_follows JOIN users ON user_follows.following_id = users.id WHERE user_follows.follower_id = '$user_id'");
$is_profile_active = isset($_GET['tab']) ? true : false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ThoughtPad - Dashboard</title>
    <link rel="stylesheet" href="css/main.css?v=9999">
    <link rel="stylesheet" href="css/navbar.css?v=9999">
    <link rel="stylesheet" href="css/dashboard.css?v=9999">
    <script src="js/interaction.js?v=9999" defer></script>
    <script src="js/script.js?v=9999" defer></script>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="dashboard-container">
        
        <aside class="sidebar">
            <div class="sidebar-profile-info">
                <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="sidebar-top-avatar-pic">
                <h3><?php echo htmlspecialchars($username); ?></h3>
                <div class="sidebar-follow-stats">
                    <span><strong><?php echo $total_followers; ?></strong> Followers</span>
                    <span><strong><?php echo $total_following; ?></strong> Following</span>
                </div>
            </div>
            
            <a href="create-blog.php" class="btn-new-post">+ NEW POST</a>
            
            <ul class="sidebar-menu">
                <li class="menu-item <?php echo !$is_profile_active ? 'active' : ''; ?>" onclick="switchTab('library-tab', this)">Library</li>
                <li class="menu-item <?php echo $is_profile_active ? 'active' : ''; ?>" onclick="switchTab('profile-tab', this)">Profile</li>
                <li class="menu-item" onclick="switchTab('stats-tab', this)">Stats</li>
                <li class="menu-item" onclick="switchTab('settings-tab', this)">Settings</li>
            </ul>
        </aside>

        <main class="main-content">
            <div id="library-tab" class="final-tab-panel <?php echo !$is_profile_active ? 'active-panel' : ''; ?>">
                <?php include 'tabs/library-tab.php'; ?>
            </div>
            <div id="stats-tab" class="final-tab-panel">
                <?php include 'tabs/stats-tab.php'; ?>
            </div>
            <div id="profile-tab" class="final-tab-panel <?php echo $is_profile_active ? 'active-panel' : ''; ?>">
                <?php include 'tabs/profile-tab.php'; ?>
            </div>
            <div id="settings-tab" class="final-tab-panel">
                <?php include 'tabs/settings-tab.php'; ?>
            </div>
        </main>

    </div>

</body>
</html>
