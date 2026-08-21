<!--home page-->
<?php
session_start();
require_once 'db.php';
mysqli_select_db($conn, "thoughtpad_db");

function get_first_image($html) {
    if (preg_match('/<img.+?src=["\']([^"\']+)["\']/is', $html, $matches)) {
        return $matches[1];
    }
    return "images/default-blog.png"; 
}

function get_clean_text($html) {
    $text = strip_tags($html);
    return (strlen($text) > 120) ? substr($text, 0, 120) . '...' : $text;
}

$search_query = "";
$sql_query = "SELECT blog_posts.*, users.username FROM blog_posts JOIN users ON blog_posts.user_id = users.id";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = mysqli_real_escape_string($conn, trim($_GET['search']));
    $sql_query .= " WHERE blog_posts.title LIKE '%$search_query%' OR blog_posts.content LIKE '%$search_query%' OR users.username LIKE '%$search_query%'";
}

$sql_query .= " ORDER BY blog_posts.created_at DESC";
$result = mysqli_query($conn, $sql_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ThoughtPad - Home</title>
    <link rel="stylesheet" href="css/main.css?v=12000">
    <link rel="stylesheet" href="css/navbar.css?v=12000">
    <link rel="stylesheet" href="css/home.css?v=12000">
    <link rel="stylesheet" href="css/dashboard.css?v=12000">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div id="d-background" class="bg-image-1 thoughtpad-hero-boost">
        <div class="d-content">
            <h1>Embrace the journey, live your truth</h1>
            <p>Documenting the beauty of everyday life and personal growth.</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="create-blog.php" class="bton-create">CREATE YOUR BLOG</a>
            <?php else: ?>
                <a href="register.php" class="bton-create">CREATE YOUR BLOG</a>
            <?php endif; ?>
        </div>
    </div>
        <div class="home-blog-section" id="search-results">
        <h2><?php echo !empty($search_query) ? "Search Results for '" . htmlspecialchars($search_query) . "'" : "Latest Stories"; ?></h2>
        
        <div class="thoughtpad-home-grid">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <?php 
                    $thumbnail = get_first_image($row['content']); 
                    $summary = get_clean_text($row['content']);
                    ?>

                    <div class="thoughtpad-vertical-card">
                        <div class="vertical-card-image">
                            <img src="<?php echo $thumbnail; ?>" alt="Blog Thumbnail">
                        </div>
                        <div class="vertical-card-body">
                            <div class="vertical-card-meta">
                                <span class="author-info">By <a href="user-profile.php?id=<?php echo $row['user_id']; ?>" class="vertical-author-link">@<?php echo htmlspecialchars($row['username']); ?></a></span>
                                <span class="dot">•</span>
                                <span class="card-date"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></span>
                            </div>
                            
                            <h3 class="vertical-card-title">
                                <a href="view-blog.php?id=<?php echo $row['id']; ?>" class="card-title-anchor"><?php echo htmlspecialchars($row['title']); ?></a>
                            </h3>
                            <p class="vertical-card-summary"><?php echo htmlspecialchars($summary); ?></p>
                            
                            <div class="vertical-card-footer">
                                <a href="view-blog.php?id=<?php echo $row['id']; ?>" class="btn-read-more-link">Read More →</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-posts" style="width: 100%; text-align: center; padding: 50px;">
                    <p>No blogs found matching your search. Try another keyword!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>

