<?php
session_start();

// Check login
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

    <title>Create Post | Connectova</title>

    <link rel="stylesheet" href="assets/css/post_upload.css">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>



<body>
    <?php include 'includes/navbar.php'; ?>

<div class="post-container">

    <div class="post-card">

        <h2>Create Post</h2>

        <form id="postForm" enctype="multipart/form-data">

           <div class="post-text-wrapper">

    <textarea
        id="post_text"
        name="post_text"
        placeholder="What's on your mind?"
        maxlength="5000"></textarea>

    <button
        type="button"
        id="postEmojiBtn"
        class="emoji-btn">
        😊
    </button>

    <div
        id="postEmojiWrapper"
        class="emoji-wrapper">

        <emoji-picker id="postEmojiPicker"></emoji-picker>

    </div>

</div>

            <div id="previewArea"></div>

            <div class="upload-buttons">

                <label class="upload-btn">

                    <i class="fa-solid fa-image"></i>

                    Images

                    <input
                        type="file"
                        id="images"
                        name="images[]"
                        accept="image/*"
                        multiple
                        hidden>

                </label>

                <label class="upload-btn">

                    <i class="fa-solid fa-video"></i>

                    Video

                    <input
                        type="file"
                        id="video"
                        name="video"
                        accept="video/*"
                        hidden>

                </label>

            </div>

            <div class="progress-wrapper">

                <div class="progress-bar" id="progressBar">
                    0%
                </div>

            </div>

            <button type="submit" class="submit-btn">

                <i class="fa-solid fa-paper-plane"></i>

                Publish Post

            </button>

        </form>

        <div id="message"></div>

    </div>

</div>

<script
    type="module"
    src="https://cdn.jsdelivr.net/npm/emoji-picker-element@latest/index.js">
</script>



<script src="assets/js/post_upload.js"></script>

</body>
</html>