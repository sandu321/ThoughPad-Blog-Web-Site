<!--edit existing blog-->
<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = mysqli_real_escape_string($conn, $_GET['id']);
mysqli_select_db($conn, "thoughtpad_db");

$query = "SELECT * FROM blog_posts WHERE id = '$post_id' AND user_id = '$user_id' LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: dashboard.php");
    exit();
}

$post = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ThoughtPad - Edit Blog</title>
    <link rel="stylesheet" href="css/main.css?v=10005">
    <link rel="stylesheet" href="css/navbar.css?v=10005">
    <link rel="stylesheet" href="css/home.css?v=10005">
    <link rel="stylesheet" href="css/dashboard.css?v=10005">
        <script src="js/rich-editor.js?v=10005" defer></script>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="blog-container">
        <div class="blog-box">
            <h2>Edit Your Blog</h2>
            <p>Modify your thoughts, images, or stories and republish them.</p>
            
            <form action="processes/edit-process.php" method="POST" id="main-blog-form">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                
                <div class="blog-group">
                    <label for="title">Blog Title</label>
                    <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($post['title']); ?>">
                </div>
                
                <div class="blog-group">
                    <label>Content Toolbar</label>
                    
                    <div class="word-ribbon-toolbar">                        
                        <div class="ribbon-row-top">
                            <!--Font Family Dropdown-->
                            <select onchange="changeFont(this)" class="ribbon-select" title="Font Family">
                                <option value="Segoe UI">Segoe UI</option>
                                <option value="Arial">Arial</option>
                                <option value="Calibri">Calibri</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Courier New">Courier New</option>
                            </select>

                            <!--Font Size Dropdown-->
                            <select onchange="changeSize(this)" class="ribbon-select" title="Font Size">
                                <option value="3">12 (Body)</option>
                                <option value="4">14</option>
                                <option value="5">18 (Subheading)</option>
                                <option value="6">24 (Heading)</option>
                                <option value="7">32</option>
                            </select>

                            <!--Line & Paragraph Spacing Dropdown-->
                            <select onchange="changeLineSpacing(this)" class="ribbon-select" title="Line & Paragraph Spacing">
                                <option value="1.15">1.15 (Default)</option>
                                <option value="1.5">1.5 (Standard)</option>
                                <option value="2.0">2.0 (Double)</option>
                                <option value="2.5">2.5 (Wide)</option>
                            </select>

                            <span class="ribbon-v-separator"></span>

                            <button type="button" onclick="triggerRichUpload('image')" class="ribbon-media-btn" title="Insert Image">🖼️ Image</button>
                            <button type="button" onclick="triggerRichUpload('video')" class="ribbon-media-btn" title="Insert Video">🎬 Video</button>
                            <button type="button" onclick="triggerRichUpload('audio')" class="ribbon-media-btn" title="Insert Audio">🎵 Audio</button>
                        </div>

                        <!--text formatting buttons-->
                        <div class="ribbon-row-bottom">
                            <button type="button" onclick="formatDoc('bold')" class="ribbon-btn" title="Bold"><b>B</b></button>
                            <button type="button" onclick="formatDoc('italic')" class="ribbon-btn" title="Italic"><i>I</i></button>
                            <button type="button" onclick="formatDoc('underline')" class="ribbon-btn" title="Underline"><u>U</u></button>
                            <button type="button" onclick="formatDoc('strikeThrough')" class="ribbon-btn" title="Strikethrough"><s>ab</s></button>

                            <span class="ribbon-v-separator"></span>

                            <div class="ribbon-color-picker-wrapper" title="Font Color">
                                <span class="ribbon-btn text-color-icon-lbl">A</span>
                                <input type="color" onchange="changeTextColor(this)" value="#000000" class="ribbon-color-input">
                            </div>

                            <div class="ribbon-color-picker-wrapper" title="Highlight Color">
                                <span class="ribbon-btn highlight-marker-icon">🖋️</span>
                                <input type="color" onchange="changeHighlightColor(this)" value="#ffffff" class="ribbon-color-input">
                            </div>

                            <span class="ribbon-v-separator"></span>

                            <button type="button" onclick="formatDoc('justifyLeft')" class="ribbon-btn" title="Align Left">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="17" y1="10" x2="3" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="17" y1="18" x2="3" y2="18"></line></svg>
                            </button>
                            <button type="button" onclick="formatDoc('justifyCenter')" class="ribbon-btn" title="Align Center">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="10" x2="6" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="18" y1="18" x2="6" y2="18"></line></svg>
                            </button>
                            <button type="button" onclick="formatDoc('justifyRight')" class="ribbon-btn" title="Align Right">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="7" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="21" y1="18" x2="7" y2="18"></line></svg>
                            </button>
                            <button type="button" onclick="formatDoc('justifyFull')" class="ribbon-btn" title="Justify">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="3" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="21" y1="18" x2="3" y2="18"></line></svg>
                            </button>

                            <span class="ribbon-v-separator"></span>

                            <button type="button" onclick="formatDoc('insertUnorderedList')" class="ribbon-btn" title="Bullet List">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="9" y1="6" x2="20" y2="6"></line><line x1="9" y1="12" x2="20" y2="12"></line><line x1="9" y1="18" x2="20" y2="18"></line><circle cx="4" cy="6" r="1"></circle><circle cx="4" cy="12" r="1"></circle><circle cx="4" cy="18" r="1"></circle></svg>
                            </button>
                            <button type="button" onclick="formatDoc('insertOrderedList')" class="ribbon-btn" title="Numbered List">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="10" y1="6" x2="21" y2="6"></line><line x1="10" y1="12" x2="21" y2="12"></line><line x1="10" y1="18" x2="21" y2="18"></line><path d="M4 6h1v4M4 18h3"></path></svg>
                            </button>
                        </div>
                    </div>

                    <input type="file" id="rich-uploader" style="display: none !important;" onchange="handleRichMedia(this)">
                </div>

                <div class="blog-group">
                    <label for="rich-editor">Content</label>
                    <div id="rich-editor" contenteditable="true" placeholder="Modify your blog story here..."><?php echo $post['content'] ?? ''; ?></div>
                    <input type="hidden" id="hidden-content" name="content">
                </div>

                <button type="submit" class="btn-blog-submit">Update</button>
            </form>
        </div>
    </div>

</body>
</html>

