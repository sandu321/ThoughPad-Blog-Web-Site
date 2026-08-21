<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';
$u_id = $_SESSION['user_id'];
mysqli_select_db($conn, "thoughtpad_db");

$query = "SELECT * FROM blog_posts WHERE user_id = '$u_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

$total_views = 0;
$total_likes_all = 0;
$total_comments_all = 0;
$detailed_posts = array();

while($row = mysqli_fetch_assoc($result)) {
    $pid = $row['id'];
    $l_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM blog_likes WHERE post_id = '$pid'");
    $l_row = mysqli_fetch_assoc($l_res);
    $post_likes = $l_row['total'];
    
    $c_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM blog_comments WHERE post_id = '$pid'");
    $c_row = mysqli_fetch_assoc($c_res);
    $post_comments = $c_row['total'];
    
    $total_views += $row['views'];
    $total_likes_all += $post_likes;
    $total_comments_all += $post_comments;
    
    $row['post_likes'] = $post_likes;
    $row['post_comments'] = $post_comments;
    $detailed_posts[] = $row;
}
?>
<div class="content-header">
    <h2>Blog Statistics</h2>
</div>

<div class="stats-overview-grid">
    <div class="stats-card">
        <h3>Total Views</h3>
        <p>👁️ <?php echo $total_views; ?></p>
    </div>
    <div class="stats-card">
        <h3>Total Likes</h3>
        <p>❤️ <?php echo $total_likes_all; ?></p>
    </div>
    <div class="stats-card">
        <h3>Total Comments</h3>
        <p>💬 <?php echo $total_comments_all; ?></p>
    </div>
</div>

<div class="post-breakdown-section">
    <h3 class="breakdown-main-title">Post Insights Breakdowns</h3>
    <div class="stats-table-wrapper">
        <table class="stats-insights-table">
            <thead>
                <tr class="table-header-row">
                    <th class="th-title">Blog Title</th>
                    <th class="th-stat-center">Views</th>
                    <th class="th-stat-center">Likes</th>
                    <th class="th-stat-center">Comments</th>
                    <th class="th-date-right">Published Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($detailed_posts) > 0): ?>
                    <?php foreach($detailed_posts as $post): ?>
                        <tr class="table-body-data-row">
                            <td class="td-title-cell">
                                <a href="view-blog.php?id=<?php echo $post['id']; ?>" class="card-title-anchor">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </td>
                            <td class="td-stat-cell-center">👁️ <?php echo $post['views']; ?></td>
                            <td class="td-stat-cell-center">❤️ <?php echo $post['post_likes']; ?></td>
                            <td class="td-stat-cell-center">💬 <?php echo $post['post_comments']; ?></td>
                            <td class="td-date-cell-right"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="table-empty-row-text">No posts found to analyze. Write a blog first!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
