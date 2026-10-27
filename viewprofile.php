```php
<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "config.php";

$currentUserId = $_SESSION['user_id'];
$profileUserId = intval($_GET['user_id'] ?? 0);

if ($profileUserId <= 0 || $profileUserId == $currentUserId) {
    header("Location: profile.php");
    exit();
}


/* =========================
   GET USER
========================= */

$stmt = $conn->prepare("
    SELECT id, full_name, username, email, birth_date, gender, profile_pic
    FROM users
    WHERE id = ?
");

$stmt->bind_param("i", $profileUserId);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}


/* =========================
   POST COUNT
========================= */

$stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM posts
    WHERE user_id = ?
");

$stmt->bind_param("i", $profileUserId);
$stmt->execute();
$stmt->bind_result($totalPosts);
$stmt->fetch();
$stmt->close();


/* =========================
   LIKES RECEIVED
========================= */

$stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM post_likes
    INNER JOIN posts
        ON posts.id = post_likes.post_id
    WHERE posts.user_id = ?
");

$stmt->bind_param("i", $profileUserId);
$stmt->execute();
$stmt->bind_result($totalLikes);
$stmt->fetch();
$stmt->close();


/* =========================
   FRIEND STATUS
========================= */

$friendStatus = "none";

/* Already friends? */

$stmt = $conn->prepare("
    SELECT id
    FROM friends
    WHERE
        (user1 = ? AND user2 = ?)
        OR
        (user1 = ? AND user2 = ?)
    LIMIT 1
");

$stmt->bind_param(
    "iiii",
    $currentUserId,
    $profileUserId,
    $profileUserId,
    $currentUserId
);

$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {

    $friendStatus = "friends";

}

$stmt->close();


/* Pending request sent by current user */

if ($friendStatus == "none") {

    $stmt = $conn->prepare("
        SELECT id
        FROM friend_requests
        WHERE sender_id = ?
        AND receiver_id = ?
        AND status = 'pending'
        LIMIT 1
    ");

    $stmt->bind_param(
        "ii",
        $currentUserId,
        $profileUserId
    );

    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        $friendStatus = "sent";
    }

    $stmt->close();
}


/* Pending request received */

if ($friendStatus == "none") {

    $stmt = $conn->prepare("
        SELECT id
        FROM friend_requests
        WHERE sender_id = ?
        AND receiver_id = ?
        AND status = 'pending'
        LIMIT 1
    ");

    $stmt->bind_param(
        "ii",
        $profileUserId,
        $currentUserId
    );

    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        $friendStatus = "received";
    }

    $stmt->close();
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($user['full_name']); ?> - Connectova
    </title>

    <link
        rel="stylesheet"
        href="assets/css/common.css">

    <link
        rel="stylesheet"
        href="assets/css/profile.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>


<body>

<?php include 'includes/navbar.php'; ?>


<div class="container fade-in">


    <!-- =========================
         COVER
    ========================== -->

    <div class="profile-cover"></div>


    <!-- =========================
         PROFILE IMAGE
    ========================== -->

    <div class="profile-image-wrapper">

        <img
            src="<?php
                if (!empty($user['profile_pic'])) {
                    echo 'uploads/images/' . htmlspecialchars($user['profile_pic']);
                } else {
                    echo 'assets/images/default-profile.png';
                }
            ?>"
            class="profile-image"
            alt="Profile Picture">

    </div>


    <!-- =========================
         NAME
    ========================== -->

    <div class="profile-name">

        <h2>
            <?php echo htmlspecialchars($user['full_name']); ?>
        </h2>

        <p>
            @<?php echo htmlspecialchars($user['username']); ?>
        </p>

    </div>


    <!-- =========================
         FRIEND BUTTON
    ========================== -->

    <div class="profile-buttons">

        <?php if ($friendStatus == "friends"): ?>

            <button
                class="btn"
                disabled>

                <i class="fa-solid fa-user-check"></i>
                Friends

            </button>


        <?php elseif ($friendStatus == "sent"): ?>

            <button
                class="btn"
                disabled>

                <i class="fa-solid fa-clock"></i>
                Request Sent

            </button>


        <?php elseif ($friendStatus == "received"): ?>

            <button
                class="btn"
                onclick="acceptRequest()">

                <i class="fa-solid fa-user-plus"></i>
                Accept Request

            </button>


        <?php else: ?>

            <button
                class="btn"
                id="addFriendBtn"
                onclick="sendFriendRequest(<?php echo $profileUserId; ?>)">

                <i class="fa-solid fa-user-plus"></i>
                Add Friend

            </button>

        <?php endif; ?>

    </div>


    <!-- =========================
         STATISTICS
    ========================== -->

    <div class="profile-stats">


        <div class="stat-card">

            <h3>
                <?php echo $totalPosts; ?>
            </h3>

            <span>
                Posts
            </span>

        </div>


        <div class="stat-card">

            <h3>
                <?php echo $totalLikes; ?>
            </h3>

            <span>
                Likes
            </span>

        </div>


    </div>


    <!-- =========================
         USER INFORMATION
    ========================== -->

    <div class="profile-info">


        <div class="info-item">

            <strong>
                Email
            </strong>

            <span>
                <?php echo htmlspecialchars($user['email']); ?>
            </span>

        </div>


        <div class="info-item">

            <strong>
                Date of Birth
            </strong>

            <span>
                <?php echo htmlspecialchars($user['birth_date']); ?>
            </span>

        </div>


        <div class="info-item">

            <strong>
                Gender
            </strong>

            <span>
                <?php echo htmlspecialchars($user['gender']); ?>
            </span>

        </div>


    </div>

</div>


<script>

function sendFriendRequest(userId) {

    const button = document.getElementById("addFriendBtn");

    button.disabled = true;

    button.innerHTML = `
        <i class="fa-solid fa-spinner fa-spin"></i>
        Sending...
    `;

    const formData = new FormData();

    formData.append("receiver_id", userId);

    fetch("api/send_friend_request.php", {
        method: "POST",
        body: formData
    })

    .then(response => response.json())

    .then(data => {

        if (data.success) {

            button.innerHTML = `
                <i class="fa-solid fa-clock"></i>
                Request Sent
            `;

        } else {

            alert(data.message || "Unable to send request.");

            button.disabled = false;

            button.innerHTML = `
                <i class="fa-solid fa-user-plus"></i>
                Add Friend
            `;
        }

    })

    .catch(error => {

        console.error(error);

        alert("Something went wrong.");

        button.disabled = false;

        button.innerHTML = `
            <i class="fa-solid fa-user-plus"></i>
            Add Friend
        `;

    });

}

</script>


</body>

</html>
```
