<!--how user views a blog post and can like, comment, and follow the author. The right sidebar shows who viewed the post.-->
<?php
session_start();
require_once 'db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$post_id = mysqli_real_escape_string($conn, $_GET['id']);
$my_user_id = $_SESSION['user_id'] ?? 0;
mysqli_select_db($conn, "thoughtpad_db");

//track member in db
if ($my_user_id > 0) {
    mysqli_query($conn, "INSERT IGNORE INTO post_views_tracks (post_id, user_id) VALUES ('$post_id', '$my_user_id')");
}

// views
if (!isset($_SESSION['viewed_posts'])) {
    $_SESSION['viewed_posts'] = array();
}
if (!in_array($post_id, $_SESSION['viewed_posts'])) {
    mysqli_query($conn, "UPDATE blog_posts SET views = views + 1 WHERE id = '$post_id'");
    $_SESSION['viewed_posts'][] = $post_id;
}

//comment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_comment']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $comment_text = mysqli_real_escape_string($conn, trim($_POST['comment_text']));
    if (!empty($comment_text)) {
        mysqli_query($conn, "INSERT INTO blog_comments (post_id, user_id, comment) VALUES ('$post_id', '$user_id', '$comment_text')");
    }
    header("Location: view-blog.php?id=" . $post_id);
    exit();
}

//like
if (isset($_GET['action']) && $_GET['action'] == 'like' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $check_like = mysqli_query($conn, "SELECT * FROM blog_likes WHERE post_id = '$post_id' AND user_id = '$user_id'");
    if (mysqli_num_rows($check_like) == 0) {
        mysqli_query($conn, "INSERT INTO blog_likes (post_id, user_id) VALUES ('$post_id', '$user_id');");
    } else {
        mysqli_query($conn, "DELETE FROM blog_likes WHERE post_id = '$post_id' AND user_id = '$user_id';");
    }
    header("Location: view-blog.php?id=" . $post_id);
    exit();
}

//post data
$query = "SELECT blog_posts.*, users.username FROM blog_posts JOIN users ON blog_posts.user_id = users.id WHERE blog_posts.id = '$post_id' LIMIT 1";
$result = mysqli_query($conn, $query);
$post = mysqli_fetch_assoc($result);

$likes_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM blog_likes WHERE post_id = '$post_id'");
$likes_row = mysqli_fetch_assoc($likes_res);
$total_likes = $likes_row['total'];

$who_viewed_res = mysqli_query($conn, "SELECT users.username, users.id, users.profile_pic FROM post_views_tracks JOIN users ON post_views_tracks.user_id = users.id WHERE post_views_tracks.post_id = '$post_id' GROUP BY post_views_tracks.user_id ORDER BY MAX(post_views_tracks.id) DESC");

$comments_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM blog_comments WHERE post_id = '$post_id'");
$comments_row = mysqli_fetch_assoc($comments_res);
$total_comments_count = $comments_row['total'];

$comments_query = "SELECT blog_comments.*, users.username FROM blog_comments JOIN users ON blog_comments.user_id = users.id WHERE blog_comments.post_id = '$post_id' ORDER BY blog_comments.created_at DESC";
$comments_result = mysqli_query($conn, $comments_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title'] ?? 'Blog'); ?></title>
    <link rel="stylesheet" href="css/main.css?v=19500">
    <link rel="stylesheet" href="css/navbar.css?v=19500">
    <link rel="stylesheet" href="css/home.css?v=19500">
    <link rel="stylesheet" href="css/dashboard.css?v=19500">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="blog-viewer-split-layout">
        
        <!--main blog panel -->
        <div class="blog-content-left-pane">
            <article class="single-post-article">
                <h1 class="post-view-title"><?php echo htmlspecialchars($post['title'] ?? 'Title'); ?></h1>
                
                <div class="post-meta-toolbar-row">
                    <div class="post-meta-left-insights">
                        <span>By <a href="user-profile.php?id=<?php echo $post['user_id']; ?>" class="post-author-link-bold"><?php echo htmlspecialchars($post['username'] ?? 'Anonymous'); ?></a></span>
                        <span class="dot">•</span>
                        <span class="post-published-date-lbl"><?php echo date('M d, Y', strtotime($post['created_at'] ?? 'now')); ?></span>
                        <span class="dot">•</span>
                        <span class="insights-trigger-lbl-static" onclick="openInsightsSidebar()">
                            <?php echo $post['views'] ?? 0; ?> Views
                        </span>
                    </div>
                    
                    <div class="post-meta-right-actions">
                        <div class="follow-btn-wrapper-zone">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php if ($_SESSION['user_id'] != $post['user_id']): ?>
                                    <?php
                                        $my_id = $_SESSION['user_id'];
                                        $author_id = $post['user_id'];
                                        $check_follow = mysqli_query($conn, "SELECT * FROM user_follows WHERE follower_id = '$my_id' AND following_id = '$author_id'");
                                        $is_following = mysqli_num_rows($check_follow) > 0;
                                    ?>
                                    <a href="processes/follow-action.php?author_id=<?php echo $author_id; ?>&post_id=<?php echo $post_id; ?>" class="<?php echo $is_following ? 'btn-view-author-following' : 'btn-view-author-follow'; ?>">
                                        <?php echo $is_following ? 'Following' : 'Follow'; ?>
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="login.php" class="btn-view-author-follow">Follow</a>
                            <?php endif; ?>
                        </div>

                        <div class="bookmark-btn-wrapper-zone">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php
                                    $my_id = $_SESSION['user_id'];
                                    $check_saved = mysqli_query($conn, "SELECT * FROM saved_lists WHERE user_id = '$my_id' AND post_id = '$post_id'");
                                    $is_saved = mysqli_num_rows($check_saved) > 0;
                                ?>
                                <a href="processes/save-action.php?post_id=<?php echo $post_id; ?>" class="btn-bookmark-view-toggle">
                                    <?php if ($is_saved): ?>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M19 21L12 16L5 21V5C5 3.89543 5.89543 3 7 3H17C18.1046 3 19 3.89543 19 5V21Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                                    <?php else: ?>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!--blog content section-->
                <div class="post-view-content">
                    <?php echo $post['content'] ?? ''; ?>
                </div>

                <div class="interaction-likes-row-footer">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="view-blog.php?id=<?php echo $post_id; ?>&action=like" class="btn-like-toggle-footer-lbl">❤️(<?php echo $total_likes; ?>)</a>
                    <?php else: ?>
                        <a href="login.php" class="btn-like-toggle-footer-lbl">❤️ <?php echo $total_likes; ?></a>
                    <?php endif; ?>
                </div>

                <!--comment section -->
                <section class="comments-section" id="comments-section-box">
                    <h3 class="comments-count-main-title">Comments (<?php echo $total_comments_count; ?>)</h3>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="" method="POST" class="comment-form">
                            <textarea name="comment_text" rows="3" required placeholder="Add a public comment..." class="comment-textarea-field"></textarea>
                            <button type="submit" name="submit_comment" class="btn-comment-submit-lbl">Comment</button>
                        </form>
                    <?php else: ?>
                        <p class="login-prompt-box"><a href="login.php" class="login-link-bold">Log in</a> to leave a comment.</p>
                    <?php endif; ?>

                    <div class="comments-list">
                        <?php if (mysqli_num_rows($comments_result) > 0): ?>
                            <?php while($com = mysqli_fetch_assoc($comments_result)): ?>
                                <div class="comment-item-layout">
                                    <span class="comment-user-title"><?php echo htmlspecialchars($com['username']); ?></span>
                                    <p class="comment-body-para"><?php echo htmlspecialchars($com['comment']); ?></p>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="empty-list-text">No comments yet. Be the first to share your thoughts!</p>
                        <?php endif; ?>
                    </div>
                </section>
            </article>
        </div>

        <!--STICKY VIEWS SIDEBAR-->
        <div class="blog-insights-right-sidebar">
            <div class="sidebar-insights-card">
                <div class="sidebar-views-header-row">
                    <h3>Viewed By</h3>
                    <span class="views-count-badge-lbl"><?php echo $post['views'] ?? 0; ?></span>
                </div>
                
                <div class="sidebar-users-list-wrapper">
                    <?php if (mysqli_num_rows($who_viewed_res) > 0): ?>
                        <?php while($v_row = mysqli_fetch_assoc($who_viewed_res)): ?>
                            <?php 
                            $v_pic = (!empty($v_row['profile_pic']) && file_exists($v_row['profile_pic'])) ? $v_row['profile_pic'] : 'images/user.png'; 
                            $v_target_user = $v_row['id'];
                            $check_if_liked = mysqli_query($conn, "SELECT * FROM blog_likes WHERE post_id = '$post_id' AND user_id = '$v_target_user'");
                            $has_liked = mysqli_num_rows($check_if_liked) > 0;
                            ?>
                            <div class="sidebar-user-row">
                                <div class="sidebar-user-left-group">
                                    <img src="<?php echo htmlspecialchars($v_pic); ?>" class="sidebar-avatar">
                                    <a href="user-profile.php?id=<?php echo $v_row['id']; ?>" class="sidebar-username-link"><?php echo htmlspecialchars($v_row['username']); ?></a>
                                </div>
                                <?php if ($has_liked): ?>
                                    <span class="blue-heart">💙</span>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="empty-list-text">No member views recorded yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</body>
</html>

