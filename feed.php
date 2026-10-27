<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once "config.php";

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT full_name, username, profile_pic
FROM users
WHERE id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>Connectova Feed</title>


<link rel="stylesheet" href="assets/css/common.css">

<link rel="stylesheet" href="assets/css/feed.css">


<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


</head>


<body>

<?php include 'includes/navbar.php'; ?>


<div class="feed-wrapper">


    <!-- =========================
            LEFT SIDE AREA
    ========================== -->

    <aside class="side-area">


        <div class="left-sidebar">


            <img
            src="uploads/images/<?php echo htmlspecialchars($user['profile_pic']); ?>"
            class="sidebar-profile"
            >


            <h3>
                <?php echo htmlspecialchars($user['full_name']); ?>
            </h3>


            <p>
                @<?php echo htmlspecialchars($user['username']); ?>
            </p>


            <a href="profile.php" class="btn">
                View Profile
            </a>


        </div>



        <div class="right-sidebar">


            <h3>
                Connectova
            </h3>


            <p>
                Share posts, connect with friends and discover new content.
            </p>


        </div>


    </aside>





    <!-- =========================
            MAIN FEED
    ========================== -->


    <main class="feed-center">


        <!-- CREATE POST -->

<!-- 
        <div class="create-post-card">


            <div class="create-top">


                <img
                src="uploads/images/<?php echo htmlspecialchars($user['profile_pic']); ?>"
                class="create-profile"
                >



                <textarea
                id="post_text"
                placeholder="What's on your mind, <?php echo htmlspecialchars($user['username']); ?>?"
                ></textarea>


            </div>



            <div id="previewArea"></div>



            <form id="postForm" enctype="multipart/form-data">


                <div class="create-actions">


                    <label class="upload-btn">

                        <i class="fa-solid fa-image"></i>
                        Photos


                        <input
                        type="file"
                        name="images[]"
                        id="images"
                        multiple
                        accept="image/*"
                        hidden>

                    </label>




                    <label class="upload-btn">


                        <i class="fa-solid fa-video"></i>
                        Video


                        <input
                        type="file"
                        name="video"
                        id="video"
                        accept="video/*"
                        hidden>


                    </label>




                    <button
                    type="submit"
                    class="publish-btn">


                        <i class="fa-solid fa-paper-plane"></i>
                        Publish


                    </button>



                </div>




                <div class="progress-wrapper">

                    <div
                    class="progress-bar"
                    id="progressBar">

                    0%

                    </div>

                </div>



                <div id="message"></div>



            </form>


        </div> -->






        <!-- POSTS -->


        <div id="feedContainer">

        </div>



        <button id="loadMoreBtn">

            Load More

        </button>

    </main>


</div>


<script>

const currentUserId = <?php echo $_SESSION['user_id']; ?>;

</script>


<script
type="module"
src="https://cdn.jsdelivr.net/npm/emoji-picker-element@latest/index.js">
</script>


<script src="assets/js/post_upload.js"></script>

<script src="assets/js/feed.js"></script>





<!-- SHARE MODAL -->


<div id="shareModal" class="share-modal">


    <div class="share-content">


        <div class="share-header">


            <h2>
                Share Post
            </h2>


            <button
            id="closeShareModal"
            type="button"
            class="share-close">

                &times;

            </button>


        </div>



        <input
        id="shareSearch"
        type="text"
        placeholder="Search users...">


        <div id="shareUserList"></div>


    </div>


</div>



</body>

</html>