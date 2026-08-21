<!--inside tab for dashboard.php, this is the settings tab content-->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';
$u_id = $_SESSION['user_id'];
mysqli_select_db($conn, "thoughtpad_db");

$user_res = mysqli_query($conn, "SELECT username, about, profile_pic FROM users WHERE id = '$u_id' LIMIT 1");
$user_data = mysqli_fetch_assoc($user_res);
$username = $user_data['username'] ?? 'User';
$about = $user_data['about'] ?? '';
$profile_pic = $user_data['profile_pic'] ?? 'images/user.png';

// If the profile picture is empty or set to the default avatar, use a fallback image
if (empty($profile_pic) || $profile_pic === 'images/default-avatar.png' || !file_exists($profile_pic)) {
    $profile_pic = 'images/user.png';
}
?>
<div class="content-header">
    <h2>Account Settings</h2>
</div>

<div class="settings-box-panel">
    <form action="processes/update-settings.php" method="POST" enctype="multipart/form-data">
                <div class="blog-group settings-avatar-center-zone">
            <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile" class="settings-display-avatar">
            <input type="file" name="profile_image" accept="image/*" class="settings-file-uploader">
        </div>
        
        <div class="blog-group">
            <label>Change Username</label>
            <input type="text" name="new_username" required value="<?php echo htmlspecialchars($username); ?>" class="settings-input-field">
        </div>
        
        <div class="blog-group">
            <label>About Me</label>
            <textarea name="new_about" rows="4" placeholder="Write a short bio about yourself..." class="settings-textarea-field"><?php echo htmlspecialchars($about); ?></textarea>
        </div>
        
        <div class="blog-group">
            <label>New Password</label>
            <input type="password" name="new_password" placeholder="Enter new password" class="settings-input-field">
        </div>
        
        <button type="submit" class="btn-blog-submit settings-submit-btn">Save Changes</button>
    </form>
</div>
