<!--This is the page where users can create a new blog post. 
It includes a rich text editor with various formatting options and multimedia upload capabilities.-->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ThoughtPad - Create Blog</title>
    <link rel="stylesheet" href="css/main.css?v=9995">
    <link rel="stylesheet" href="css/navbar.css?v=9995">
    <link rel="stylesheet" href="css/home.css?v=9995">
    <link rel="stylesheet" href="css/dashboard.css?v=10000">
    <script src="js/rich-editor.js?v=9995" defer></script>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="blog-container">
        <div class="blog-box">
            <h2>Write a New Blog</h2>
            <p>Share your thoughts, multimedia stories, or ideas with the world.</p>
            
            <form action="processes/blog-process.php" method="POST" id="main-blog-form">
                
                <div class="blog-group">
                    <label for="title">Blog Title</label>
                    <input type="text" id="title" name="title" required placeholder="Enter title"><br><br>
                    <div class="blog-group">
                                <label for="blog_category">Select Category</label>
                                <select name="blog_category" id="blog_category" class="ribbon-select" style="width: 100% !important; padding: 12px 15px !important; font-size: 1rem !important; border-radius: 6px !important; border: 1px solid #cbd5e1 !important; background: #ffffff !important;">
                                    <option value="General">General</option>
                                    <option value="Technology">Technology</option>
                                    <option value="Travel">Travel</option>
                                    <option value="Lifestyle">Lifestyle</option>
                                    <option value="Education">Education</option>
                                    <option value="Food">Food</option>
                                </select>
                    </div>

                </div>
                
                <div class="blog-group">
                    <label>Content</label>
                    
                    <div class="word-ribbon-toolbar">
                        
                        <div class="ribbon-row-top">
                            
                            <!-- Font Family Selection -->
                            <select onchange="changeFont(this)" class="ribbon-select" title="Font Family">
                                <option value="Segoe UI">Segoe UI</option>
                                <option value="Arial">Arial</option>
                                <option value="Calibri">Calibri</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Courier New">Courier New</option>
                            </select>

                            <!-- Font Size Selection -->
                            <select onchange="changeSize(this)" class="ribbon-select" title="Font Size">
                                <option value="3">12 (Body)</option>
                                <option value="4">14</option>
                                <option value="5">18 (Subheading)</option>
                                <option value="6">24 (Heading)</option>
                                <option value="7">32</option>
                            </select>

                            <!-- Line & Paragraph Spacing Selection -->
                            <select onchange="changeLineSpacing(this)" class="ribbon-select" title="Line & Paragraph Spacing">
                                <option value="1.15">1.15 (Default)</option>
                                <option value="1.5">1.5 (Standard)</option>
                                <option value="2.0">2.0 (Double)</option>
                                <option value="2.5">2.5 (Wide)</option>
                            </select>

                            <span class="ribbon-v-separator"></span>

                            <!-- Multimedia Uploader Buttons -->
                            <button type="button" onclick="triggerRichUpload('image')" class="ribbon-media-btn" title="Insert Image">🖼️ Image</button>
                            <button type="button" onclick="triggerRichUpload('video')" class="ribbon-media-btn" title="Insert Video">🎬 Video</button>
                            <button type="button" onclick="triggerRichUpload('audio')" class="ribbon-media-btn" title="Insert Audio">🎵 Audio</button>
                        </div>

                        <div class="ribbon-row-bottom">
                            
                            <!-- Basic Formatting Tools -->
                            <button type="button" onclick="formatDoc('bold')" class="ribbon-btn" title="Bold"><b>B</b></button>
                            <button type="button" onclick="formatDoc('italic')" class="ribbon-btn" title="Italic"><i>I</i></button>
                            <button type="button" onclick="formatDoc('underline')" class="ribbon-btn" title="Underline"><u>U</u></button>
                            <button type="button" onclick="formatDoc('strikeThrough')" class="ribbon-btn" title="Strikethrough"><s>ab</s></button>

                            <span class="ribbon-v-separator"></span>

                            <!-- Advanced Colors Picker Trigger -->
                            <div class="ribbon-color-picker-wrapper" title="Font Color">
                                <span class="ribbon-btn text-color-icon-lbl">A</span>
                                <input type="color" onchange="changeTextColor(this)" value="#000000" class="ribbon-color-input">
                            </div>

                            <div class="ribbon-color-picker-wrapper" title="Highlight Color">
                                <span class="ribbon-btn highlight-marker-icon">🖋️</span>
                                <input type="color" onchange="changeHighlightColor(this)" value="#ffffff" class="ribbon-color-input">
                            </div>

                            <span class="ribbon-v-separator"></span>

                            <!-- Standard Alignments SVG -->
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

                            <!-- Pure Lists Line SVG -->
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
                    <div id="rich-editor" contenteditable="true" placeholder="Write your blog story here..."></div>
                    <input type="hidden" id="hidden-content" name="content">
                </div>

                <button type="submit" class="btn-blog-submit">Publish</button>
            </form>
        </div>
    </div>

</body>
</html>
