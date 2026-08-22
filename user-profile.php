<!--profile page for other users to view their profile and posts-->
<?php
session_start();
require_once 'db.php';

//check if user is logged in, if not redirect to login page
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$target_user_id = mysqli_real_escape_string($conn, $_GET['id']);
$my_id = $_SESSION['user_id'] ?? 0;

if ($my_id == $target_user_id) {
    header("Location: dashboard.php?tab=profile");
    exit();
}

mysqli_select_db($conn, "thoughtpad_db");

$user_res = mysqli_query($conn, "SELECT username, email, about, profile_pic FROM users WHERE id = '$target_user_id' LIMIT 1");
if (mysqli_num_rows($user_res) == 0) {
    header("Location: index.php");
    exit();
}

$user_data = mysqli_fetch_assoc($user_res);
$username = $user_data['username'];
$user_email = $user_data['email'];
$about = $user_data['about'] ?? 'No bio available yet.';
$profile_pic = $user_data['profile_pic'] ?? '';

if (empty($profile_pic) || $profile_pic === 'images/default-avatar.png' || !file_exists($profile_pic)) {
    $profile_pic = 'images/user.png';
}

function get_target_image($html) {
    if (preg_match('/<img.+?src=["\']([^"\']+)["\']/is', $html, $matches)) {
        return $matches[1];
    }
    return "images/default-blog.png";
}

if (!function_exists('get_profile_text')) {
    function get_profile_text($html) {
        $text = strip_tags($html);
        return (strlen($text) > 120) ? substr($text, 0, 120) . '...' : $text;
    }
}

$my_posts = mysqli_query($conn, "SELECT * FROM blog_posts WHERE user_id = '$target_user_id' ORDER BY created_at DESC");

$followers_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user_follows WHERE following_id = '$target_user_id'");
$followers_row = mysqli_fetch_assoc($followers_res);
$total_followers = $followers_row['total'];

$current_view_tab = isset($_GET['tab']) ? $_GET['tab'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($username); ?> - Profile</title>
    <link rel="stylesheet" href="css/main.css?v=15500">
    <link rel="stylesheet" href="css/navbar.css?v=15500">
    <link rel="stylesheet" href="css/home.css?v=15500">
    <link rel="stylesheet" href="css/dashboard.css?v=15500">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="main-content" style="max-width: 1200px !important; margin: 0 auto !important; padding: 40px 20px !important; box-sizing: border-box !important;">
        <div class="thoughtpad-profile-container" style="margin-top: 50px !important;">
            
            <div class="profile-left-column">
                <h1 class="profile-main-name"><?php echo htmlspecialchars($username); ?></h1>
                
                <div class="super-profile-navbar">
                    <a href="user-profile.php?id=<?php echo $target_user_id; ?>&tab=home" class="super-tab-link <?php echo ($current_view_tab === 'home') ? 'active-profile-tab' : ''; ?>">Stories</a>
                    <a href="user-profile.php?id=<?php echo $target_user_id; ?>&tab=about" class="super-tab-link <?php echo ($current_view_tab === 'about') ? 'active-profile-tab' : ''; ?>">About</a>
                </div>

                <!-- stories -->
                <?php if ($current_view_tab === 'home'): ?>
                <div class="p-php-panel">
                    <div class="blog-cards-grid">
                    <?php if (mysqli_num_rows($my_posts) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($my_posts)): ?>
                            <div class="blog-card">
                                <div class="card-image-box"><img src="<?php echo get_target_image($row['content']); ?>"></div>
                                <div class="card-content-box">
                                    <span class="card-date"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></span>
                                    
                                    <h3 class="card-title">
                                        <a href="view-blog.php?id=<?php echo $row['id']; ?>" class="card-title-anchor"><?php echo htmlspecialchars($row['title']); ?></a>
                                    </h3>
                                    <p class="card-summary"><?php echo htmlspecialchars(get_profile_text($row['content'])); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                                            <?php else: ?>
                        <p class="empty-list-text">This user hasn't published any stories yet.</p>
                    <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- about -->
                <?php if ($current_view_tab === 'about'): ?>
                <div class="p-php-panel">
                    <div class="profile-about-text-card">
                        <p><?php echo nl2br(htmlspecialchars($about)); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!--profile -->
            <div class="profile-right-column">
                <div class="sticky-profile-card">
                    <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="thoughtpad-avatar-large">
                    <h2 class="sidebar-user-display-name"><?php echo htmlspecialchars($username); ?></h2>
                    <p class="sidebar-follower-count"><strong><?php echo $total_followers; ?></strong> Followers</p>
                    
                    <div class="profile-follow-action-btn-zone" style="margin-top:15px !important;">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['user_id'] != $target_user_id): ?>
                                <?php
                                $my_id = $_SESSION['user_id'];
                                $check_follow = mysqli_query($conn, "SELECT * FROM user_follows WHERE follower_id = '$my_id' AND following_id = '$target_user_id'");
                                $is_following = mysqli_num_rows($check_follow) > 0;
                                ?>
                                <a href="processes/follow-action.php?author_id=<?php echo $target_user_id; ?>&post_id=0" class="<?php echo $is_following ? 'btn-profile-unfollow-action' : 'btn-profile-follow-action'; ?>">
                                    <?php echo $is_following ? 'Following' : 'Follow'; ?>
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="login.php" class="btn-profile-follow-action">Follow</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>

