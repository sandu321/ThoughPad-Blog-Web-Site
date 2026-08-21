<!-- this is for dashboard inside tab name library -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';
$u_id = $_SESSION['user_id'];
mysqli_select_db($conn, "thoughtpad_db");

function get_tab_image($html) {
    if (preg_match('/<img.+?src=["\']([^"\']+)["\']/is', $html, $matches)) {
        return $matches[1]; 
    }
    return "images/default-blog.png"; // Default image if no image found
}

function get_tab_text($html) {
    $text = strip_tags($html);
    return (strlen($text) > 120) ? substr($text, 0, 120) . '...' : $text;
}

$my_posts = mysqli_query($conn, "SELECT * FROM blog_posts WHERE user_id = '$u_id' ORDER BY created_at DESC");
$saved_posts = mysqli_query($conn, "SELECT blog_posts.*, users.username FROM saved_lists JOIN blog_posts ON saved_lists.post_id = blog_posts.id JOIN users ON blog_posts.user_id = users.id WHERE saved_lists.user_id = '$u_id' ORDER BY blog_posts.created_at DESC");
$pinned_posts = mysqli_query($conn, "SELECT blog_posts.* FROM post_highlights JOIN blog_posts ON post_highlights.post_id = blog_posts.id WHERE post_highlights.user_id = '$u_id' ORDER BY post_highlights.id DESC");
$my_responses = mysqli_query($conn, "SELECT blog_comments.*, blog_posts.title FROM blog_comments JOIN blog_posts ON blog_comments.post_id = blog_posts.id WHERE blog_comments.user_id = '$u_id' ORDER BY blog_comments.created_at DESC");

$current_sub_tab = isset($_GET['sub']) ? $_GET['sub'] : 'lists';
?>
<div class="content-header">
    <h2>Your library</h2>
</div>

<div class="library-sub-toolbar-wrapper">
    <a href="dashboard.php?sub=lists" class="sub-tab-anchor <?php echo ($current_sub_tab === 'lists') ? 'active-sub-tab' : ''; ?>">Your lists</a>
    <a href="dashboard.php?sub=saved" class="sub-tab-anchor <?php echo ($current_sub_tab === 'saved') ? 'active-sub-tab' : ''; ?>">Saved lists</a>
    <a href="dashboard.php?sub=featured" class="sub-tab-anchor <?php echo ($current_sub_tab === 'featured') ? 'active-sub-tab' : ''; ?>">Highlights</a>
    <a href="dashboard.php?sub=comments" class="sub-tab-anchor <?php echo ($current_sub_tab === 'comments') ? 'active-sub-tab' : ''; ?>">Comments</a>
</div>

<!-- 1. YOUR LISTS -->
<?php if ($current_sub_tab === 'lists'): ?>
<div class="blog-cards-grid">
    <?php if (mysqli_num_rows($my_posts) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($my_posts)): ?>
            <?php 
            $pid = $row['id'];
            $l_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM blog_likes WHERE post_id = '$pid'");
            $l_row = mysqli_fetch_assoc($l_res);
            $total_likes = $l_row['total'];
            
            $c_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM blog_comments WHERE post_id = '$pid'");
            $c_row = mysqli_fetch_assoc($c_res);
            $total_comments = $c_row['total'];
            
            $check_pin = mysqli_query($conn, "SELECT * FROM post_highlights WHERE post_id = '$pid' AND user_id = '$u_id'");
            $is_pinned = mysqli_num_rows($check_pin) > 0;
            ?>
            <div class="blog-card">
                <div class="card-image-box"><img src="<?php echo get_tab_image($row['content']); ?>"></div>
                <div class="card-content-box">
                    <div class="card-meta">
                        <span class="card-date"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></span>
                        <?php if ($is_pinned): ?>
                            <span class="pinned-status-badge">Pinned</span>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="card-title">
                        <a href="view-blog.php?id=<?php echo $row['id']; ?>" class="card-title-anchor"><?php echo htmlspecialchars($row['title']); ?></a>
                    </h3>
                    <p class="card-summary"><?php echo htmlspecialchars(get_tab_text($row['content'])); ?></p>
                    
                    <div class="card-analytics-bar">
                        <div class="stats-left">
                            <span class="stat-item">👁 <?php echo $row['views']; ?></span>
                            <span class="stat-item">♡ <?php echo $total_likes; ?></span>
                            <span class="stat-item">🗪 <?php echo $total_comments; ?></span>
                        </div>
                        <div class="actions-right">
                            <a href="processes/pin-process.php?id=<?php echo $row['id']; ?>" class="btn-action-pin-lbl"><?php echo $is_pinned ? 'Unpin' : 'Pin'; ?></a>
                            <a href="edit-blog.php?id=<?php echo $row['id']; ?>" class="btn-action-edit">Edit</a>
                            <a href="delete-blog.php?id=<?php echo $row['id']; ?>" class="btn-action-delete" onclick="return confirm('Delete this post?');">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="empty-list-text">You haven't published any stories yet.</p>
    <?php endif; ?>
</div>
<?php endif; ?>
<!-- 2. SAVED LISTS -->
<?php if ($current_sub_tab === 'saved'): ?>
<div class="blog-cards-grid">
    <?php if (mysqli_num_rows($saved_posts) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($saved_posts)): ?>
            <div class="blog-card">
                <div class="card-image-box"><img src="<?php echo get_tab_image($row['content']); ?>"></div>
                <div class="card-content-box">
                    <div>
                        <span class="author-info">Saved from @<?php echo htmlspecialchars($row['username']); ?></span>
                        
                        <h3 class="card-title">
                            <a href="view-blog.php?id=<?php echo $row['id']; ?>" class="card-title-anchor"><?php echo htmlspecialchars($row['title']); ?></a>
                        </h3>
                        <p class="card-summary"><?php echo htmlspecialchars(get_tab_text($row['content'])); ?></p>
                    </div>
                    
                    <div class="saved-card-footer-bar">
                        <span class="card-date"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></span>                        
                        <a href="processes/save-action.php?post_id=<?php echo $row['id']; ?>" class="btn-remove-from-list">Remove from List</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="empty-list-text">No saved stories yet.</p>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- 3. FEATURED PINNED -->
<?php if ($current_sub_tab === 'featured'): ?>
<div class="blog-cards-grid">
    <?php if (mysqli_num_rows($pinned_posts) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($pinned_posts)): ?>
            <div class="blog-card">
                <div class="card-image-box"><img src="<?php echo get_tab_image($row['content']); ?>"></div>
                <div class="card-content-box">
                    <div class="card-meta">
                        <span class="card-date"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></span>
                        <span class="featured-status-badge">Featured</span>
                    </div>
                    
                    <h3 class="card-title">
                        <a href="view-blog.php?id=<?php echo $row['id']; ?>" class="card-title-anchor"><?php echo htmlspecialchars($row['title']); ?></a>
                    </h3>
                    <p class="card-summary"><?php echo htmlspecialchars(get_tab_text($row['content'])); ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="empty-list-text">No pinned stories yet.</p>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- 4. COMMENTS RESPONSES -->
<?php if ($current_sub_tab === 'comments'): ?>
<div class="dashboard-comments-list">
    <?php if (mysqli_num_rows($my_responses) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($my_responses)): ?>
            <div class="dash-comment-item">
                <p class="comment-target-story">Your comment on <em>"<?php echo htmlspecialchars($row['title']); ?>"</em></p>
                <p class="dash-comment-text">"<?php echo htmlspecialchars($row['comment']); ?>"</p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="empty-list-text">You haven't written any comments yet.</p>
    <?php endif; ?>
</div>
<?php endif; ?>

