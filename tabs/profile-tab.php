<!--inside tab for dashboard.php, this is the profile tab content-->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';
$u_id = $_SESSION['user_id'];
mysqli_select_db($conn, "thoughtpad_db");

$user_res = mysqli_query($conn, "SELECT username, email, about, profile_pic FROM users WHERE id = '$u_id' LIMIT 1");
$user_data = mysqli_fetch_assoc($user_res);
$username = $user_data['username'] ?? 'User';
$user_email = $user_data['email'] ?? '';
$about = $user_data['about'] ?? 'No bio available yet.';
$profile_pic = $user_data['profile_pic'] ?? '';

if (empty($profile_pic) || $profile_pic === 'images/default-avatar.png' || !file_exists($profile_pic)) {
    $profile_pic = 'images/user.png';
}

function get_profile_image($html) {
    if (preg_match('/<img.+?src=["\']([^"\']+)["\']/is', $html, $matches)) {
        return $matches[1];
    }
    return "images/default-blog.png";
}

function get_profile_text($html) {
    $text = strip_tags($html);
    return (strlen($text) > 120) ? substr($text, 0, 120) . '...' : $text;
}

$my_posts = mysqli_query($conn, "SELECT * FROM blog_posts WHERE user_id = '$u_id' ORDER BY created_at DESC");
$followers_list = mysqli_query($conn, "SELECT users.id, users.username, users.profile_pic FROM user_follows JOIN users ON user_follows.follower_id = users.id WHERE user_follows.following_id = '$u_id'");
$following_list = mysqli_query($conn, "SELECT users.id, users.username, users.profile_pic FROM user_follows JOIN users ON user_follows.following_id = users.id WHERE user_follows.follower_id = '$u_id'");
$current_profile_tab = isset($_GET['tab']) ? $_GET['tab'] : 'home';
?>

<div class="thoughtpad-profile-container">
    <div class="profile-left-column">
        <h1 class="profile-main-name"><?php echo htmlspecialchars($username); ?></h1>
        
        <div class="super-profile-navbar">
            <a href="dashboard.php?tab=home" class="super-tab-link <?php echo ($current_profile_tab === 'home') ? 'active-profile-tab' : ''; ?>">Home</a>
            <a href="dashboard.php?tab=followers" class="super-tab-link <?php echo ($current_profile_tab === 'followers') ? 'active-profile-tab' : ''; ?>">Followers</a>
            <a href="dashboard.php?tab=about" class="super-tab-link <?php echo ($current_profile_tab === 'about') ? 'active-profile-tab' : ''; ?>">About</a>
        </div>

        <!-- HOME TAB PANEL -->
        <?php if ($current_profile_tab === 'home'): ?>
        <div class="p-php-panel">
            <div class="blog-cards-grid">
            <?php if (mysqli_num_rows($my_posts) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($my_posts)): ?>
                    <div class="blog-card">
                        <div class="card-image-box"><img src="<?php echo get_profile_image($row['content']); ?>"></div>
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
                <p class="empty-list-text">No stories published yet.</p>
            <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <!-- FOLLOWERS TAB PANEL -->
        <?php if ($current_profile_tab === 'followers'): ?>
        <div class="p-php-panel">
            <div class="thoughtpad-users-list">
            <?php if (mysqli_num_rows($followers_list) > 0): ?>
                <?php while($f_row = mysqli_fetch_assoc($followers_list)): ?>
                    <?php 
                    $f_pic = (!empty($f_row['profile_pic']) && file_exists($f_row['profile_pic'])) ? $f_row['profile_pic'] : 'images/user.png'; 
                    $f_target_id = $f_row['id'];
                    $check_if_i_follow = mysqli_query($conn, "SELECT * FROM user_follows WHERE follower_id = '$u_id' AND following_id = '$f_target_id'");
                    $am_i_following = mysqli_num_rows($check_if_i_follow) > 0;
                    ?>
                    <div class="thoughtpad-user-row">
                        <div class="user-info-left-group">
                            <img src="<?php echo htmlspecialchars($f_pic); ?>" class="thoughtpad-avatar-small">
                            <span class="thoughtpad-user-name">
                                <a href="user-profile.php?id=<?php echo $f_target_id; ?>" class="profile-user-link-anchor"><?php echo htmlspecialchars($f_row['username']); ?></a>
                            </span>
                        </div>
                        <div class="user-actions-right-group">
                            <?php if ($am_i_following): ?>
                                <a href="processes/follow-action.php?author_id=<?php echo $f_target_id; ?>&post_id=0" class="btn-profile-unfollow-action">Unfollow</a>
                            <?php else: ?>
                                <a href="processes/follow-action.php?author_id=<?php echo $f_target_id; ?>&post_id=0" class="btn-profile-follow-action">Follow Back</a>
                            <?php endif; ?>
                            <a href="processes/manage-follow.php?action=remove&id=<?php echo $f_target_id; ?>" class="btn-profile-remove-action">Remove</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="empty-list-text">No followers yet.</p>
            <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- ABOUT TAB PANEL -->
        <?php if ($current_profile_tab === 'about'): ?>
        <div class="p-php-panel">
            <div class="profile-about-text-card">
                <p><?php echo nl2br(htmlspecialchars($about)); ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!--STICKY PROFILE RIGHT COLUMN -->
    <div class="profile-right-column">
        <div class="sticky-profile-card">
            <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="thoughtpad-avatar-large">
            <h2 class="sidebar-user-display-name"><?php echo htmlspecialchars($username); ?></h2>
            
            <div class="thoughtpad-following-section">
                <h3>Following</h3>
                <div class="thoughtpad-users-list">
                <?php if (mysqli_num_rows($following_list) > 0): ?>
                    <?php while($g_row = mysqli_fetch_assoc($following_list)): ?>
                        <?php 
                        $g_pic = $g_row['profile_pic'] ?? '';
                        if (empty($g_pic) || $g_pic === 'images/default-avatar.png' || !file_exists($g_pic)) {
                            $g_pic = 'images/user.png';
                        }
                        ?>
                        <div class="thoughtpad-user-row">
                            <div class="user-info-left-group">
                                <img src="<?php echo htmlspecialchars($g_pic); ?>" class="thoughtpad-avatar-small">
                                <span class="thoughtpad-user-name">
                                    <a href="user-profile.php?id=<?php echo $g_row['id']; ?>" class="profile-user-link-anchor"><?php echo htmlspecialchars($g_row['username']); ?></a>
                                </span>
                            </div>
                            <a href="processes/follow-action.php?author_id=<?php echo $g_row['id']; ?>&post_id=0" class="btn-profile-unfollow-action">Unfollow</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty-list-text">Not following anyone yet.</p>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

