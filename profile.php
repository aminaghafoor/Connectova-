
<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "config.php";

$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Fetch User Information
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        full_name,
        username,
        email,
        birth_date,
        gender,
        profile_pic
    FROM users
    WHERE id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Total Posts
|--------------------------------------------------------------------------
*/

$post_stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM posts
    WHERE user_id = ?
");

$post_stmt->bind_param("i", $user_id);
$post_stmt->execute();
$post_stmt->bind_result($totalPosts);
$post_stmt->fetch();
$post_stmt->close();


/*
|--------------------------------------------------------------------------
| Total Likes Received
|--------------------------------------------------------------------------
*/

$like_stmt = $conn->prepare("
    SELECT COUNT(post_likes.id)
    FROM post_likes
    INNER JOIN posts
        ON posts.id = post_likes.post_id
    WHERE posts.user_id = ?
");

$like_stmt->bind_param("i", $user_id);
$like_stmt->execute();
$like_stmt->bind_result($totalLikes);
$like_stmt->fetch();
$like_stmt->close();


/*
|--------------------------------------------------------------------------
| Total Groups
|--------------------------------------------------------------------------
*/

$group_stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM group_members
    WHERE user_id = ?
");

$group_stmt->bind_param("i", $user_id);
$group_stmt->execute();
$group_stmt->bind_result($totalGroups);
$group_stmt->fetch();
$group_stmt->close();


/*
|--------------------------------------------------------------------------
| Profile Picture
|--------------------------------------------------------------------------
*/

if (!empty($user['profile_pic'])) {
    $profileImage = "uploads/images/" . htmlspecialchars($user['profile_pic']);
} else {
    $profileImage = "assets/images/default-profile.png";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="View and manage your Connectova social profile."
    >

    <title>
        <?php echo htmlspecialchars($user['full_name']); ?> | Connectova
    </title>

    <link rel="stylesheet" href="assets/css/common.css">

    <link rel="stylesheet" href="assets/css/profile.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

</head>


<body>


<?php include 'includes/navbar.php'; ?>


<main class="profile-page">

    <div class="container profile-container">


        <!-- ==================================================
             PROFILE HERO
        ================================================== -->

        <section class="profile-hero">


            <!-- Cover -->

            <div class="profile-cover">

                <div class="cover-glow cover-glow-one"></div>

                <div class="cover-glow cover-glow-two"></div>

                <div class="cover-pattern"></div>

            </div>


            <!-- Profile Identity -->

            <div class="profile-identity">


                <div class="profile-image-area">

                    <div class="profile-image-wrapper">

                        <img
                            src="<?php echo $profileImage; ?>"
                            class="profile-image"
                            id="profilePreview"
                            alt="Profile Picture"
                        >

                        <form
                            id="profilePicForm"
                            action="api/upload_profile_picture.php"
                            method="POST"
                            enctype="multipart/form-data"
                        >

                            <input
                                type="file"
                                name="profile_pic"
                                id="profilePicInput"
                                accept="image/*"
                                hidden
                            >

                        </form>


                        <button
                            type="button"
                            class="camera-overlay"
                            onclick="document.getElementById('profilePicInput').click();"
                            aria-label="Change profile picture"
                            title="Change profile picture"
                        >

                            <i class="fa-solid fa-camera"></i>

                        </button>

                    </div>

                </div>


                <div class="profile-heading">

                    <div class="profile-name-row">

                        <h1>
                            <?php echo htmlspecialchars($user['full_name']); ?>
                        </h1>

                        <span class="profile-status">
                            <span></span>
                            Active profile
                        </span>

                    </div>

                    <p class="profile-username">
                        @<?php echo htmlspecialchars($user['username']); ?>
                    </p>

                    <p class="profile-description">
                        Welcome to your Connectova profile. Manage your
                        personal information, view your social activity
                        and keep your profile up to date.
                    </p>

                </div>


                <div class="profile-actions">

                    <button
                        type="button"
                        class="profile-edit-btn"
                        id="editProfileBtn"
                    >

                        <i class="fa-solid fa-pen"></i>

                        Edit Profile

                    </button>

                </div>

            </div>

        </section>



        <!-- ==================================================
             PROFILE CONTENT
        ================================================== -->

        <section class="profile-content">


            <!-- ==================================================
                 STATISTICS
            ================================================== -->

            <div class="profile-section-heading">

                <div>

                    <span class="section-label">
                        YOUR ACTIVITY
                    </span>

                    <h2>
                        Your Connectova
                        <span>at a glance.</span>
                    </h2>

                </div>

                <p>
                    A quick overview of your activity and
                    participation across Connectova.
                </p>

            </div>


            <div class="profile-stats">


                <!-- Posts -->

                <article class="stat-card">

                    <div class="stat-card-top">

                        <div class="stat-icon">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </div>

                        <span class="stat-number-label">
                            01
                        </span>

                    </div>

                    <div class="stat-content">

                        <strong>
                            <?php echo $totalPosts; ?>
                        </strong>

                        <span>
                            Posts
                        </span>

                        <p>
                            Content you've shared with your community.
                        </p>

                    </div>

                </article>


                <!-- Likes -->

                <article class="stat-card">

                    <div class="stat-card-top">

                        <div class="stat-icon">
                            <i class="fa-regular fa-heart"></i>
                        </div>

                        <span class="stat-number-label">
                            02
                        </span>

                    </div>

                    <div class="stat-content">

                        <strong>
                            <?php echo $totalLikes; ?>
                        </strong>

                        <span>
                            Likes Received
                        </span>

                        <p>
                            Reactions received on your posts.
                        </p>

                    </div>

                </article>


                <!-- Groups -->

                <article class="stat-card">

                    <div class="stat-card-top">

                        <div class="stat-icon">
                            <i class="fa-solid fa-users"></i>
                        </div>

                        <span class="stat-number-label">
                            03
                        </span>

                    </div>

                    <div class="stat-content">

                        <strong>
                            <?php echo $totalGroups; ?>
                        </strong>

                        <span>
                            Groups
                        </span>

                        <p>
                            Communities you're currently part of.
                        </p>

                    </div>

                </article>


            </div>



            <!-- ==================================================
                 PROFILE INFORMATION
            ================================================== -->

            <div class="profile-details-layout">


                <!-- Personal Information -->

                <section class="profile-info-card">

                    <div class="card-heading">

                        <div>

                            <span class="section-label">
                                PERSONAL INFORMATION
                            </span>

                            <h2>
                                Profile details
                            </h2>

                        </div>

                        <div class="heading-icon">
                            <i class="fa-regular fa-id-card"></i>
                        </div>

                    </div>


                    <div class="profile-info-list">


                        <div class="info-item">

                            <div class="info-icon">
                                <i class="fa-regular fa-user"></i>
                            </div>

                            <div class="info-content">

                                <span>
                                    Full Name
                                </span>

                                <strong>
                                    <?php echo htmlspecialchars($user['full_name']); ?>
                                </strong>

                            </div>

                        </div>


                        <div class="info-item">

                            <div class="info-icon">
                                <i class="fa-solid fa-at"></i>
                            </div>

                            <div class="info-content">

                                <span>
                                    Username
                                </span>

                                <strong>
                                    @<?php echo htmlspecialchars($user['username']); ?>
                                </strong>

                            </div>

                        </div>


                        <div class="info-item">

                            <div class="info-icon">
                                <i class="fa-regular fa-envelope"></i>
                            </div>

                            <div class="info-content">

                                <span>
                                    Email Address
                                </span>

                                <strong>
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </strong>

                            </div>

                        </div>


                        <div class="info-item">

                            <div class="info-icon">
                                <i class="fa-regular fa-calendar"></i>
                            </div>

                            <div class="info-content">

                                <span>
                                    Date of Birth
                                </span>

                                <strong>
                                    <?php echo htmlspecialchars($user['birth_date']); ?>
                                </strong>

                            </div>

                        </div>


                        <div class="info-item">

                            <div class="info-icon">
                                <i class="fa-solid fa-venus-mars"></i>
                            </div>

                            <div class="info-content">

                                <span>
                                    Gender
                                </span>

                                <strong>
                                    <?php echo htmlspecialchars($user['gender']); ?>
                                </strong>

                            </div>

                        </div>


                    </div>

                </section>



                <!-- Profile Summary -->

                <aside class="profile-summary-card">

                    <div class="summary-orb"></div>

                    <span class="section-label">
                        CONNECTOVA PROFILE
                    </span>

                    <h2>
                        Your identity,
                        <span>your space.</span>
                    </h2>

                    <p>
                        Your Connectova profile is where your identity
                        and social activity come together. Keep your
                        information current so your community can
                        recognize and connect with you.
                    </p>


                    <div class="summary-points">

                        <div>

                            <span class="summary-check">
                                <i class="fa-solid fa-check"></i>
                            </span>

                            <span>
                                Personal profile
                            </span>

                        </div>


                        <div>

                            <span class="summary-check">
                                <i class="fa-solid fa-check"></i>
                            </span>

                            <span>
                                Social activity
                            </span>

                        </div>


                        <div>

                            <span class="summary-check">
                                <i class="fa-solid fa-check"></i>
                            </span>

                            <span>
                                Community participation
                            </span>

                        </div>

                    </div>


                    <button
                        type="button"
                        class="summary-button"
                        id="summaryEditBtn"
                    >

                        Update your profile

                        <span>
                            →
                        </span>

                    </button>

                </aside>


            </div>


        </section>

    </div>

</main>



<!-- ==================================================
     EDIT PROFILE MODAL
================================================== -->

<div
    id="editProfileModal"
    class="modal"
    aria-hidden="true"
>

    <div class="modal-backdrop"></div>


    <div
        class="modal-content"
        role="dialog"
        aria-modal="true"
        aria-labelledby="editProfileTitle"
    >

        <button
            type="button"
            class="close-modal"
            aria-label="Close modal"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>


        <div class="modal-header">

            <div class="modal-mark">
                <i class="fa-solid fa-pen"></i>
            </div>

            <div>

                <span class="section-label">
                    PROFILE SETTINGS
                </span>

                <h2 id="editProfileTitle">
                    Edit Profile
                </h2>

                <p>
                    Update your Connectova information.
                </p>

            </div>

        </div>


        <form
            id="editProfileForm"
            class="edit-profile-form"
        >


            <div class="modal-form-row">

                <div class="modal-form-group">

                    <label for="edit_full_name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="edit_full_name"
                        name="full_name"
                        value="<?php echo htmlspecialchars($user['full_name']); ?>"
                        required
                    >

                </div>


                <div class="modal-form-group">

                    <label for="edit_username">
                        Username
                    </label>

                    <input
                        type="text"
                        id="edit_username"
                        name="username"
                        value="<?php echo htmlspecialchars($user['username']); ?>"
                        required
                    >

                </div>

            </div>


            <div class="modal-form-group">

                <label for="edit_email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="edit_email"
                    name="email"
                    value="<?php echo htmlspecialchars($user['email']); ?>"
                    required
                >

            </div>


            <div class="modal-form-row">

                <div class="modal-form-group">

                    <label for="edit_birth_date">
                        Date of Birth
                    </label>

                    <input
                        type="date"
                        id="edit_birth_date"
                        name="birth_date"
                        value="<?php echo htmlspecialchars($user['birth_date']); ?>"
                    >

                </div>


                <div class="modal-form-group">

                    <label for="edit_gender">
                        Gender
                    </label>

                    <select
                        id="edit_gender"
                        name="gender"
                    >

                        <option
                            value="Male"
                            <?php
                            if ($user['gender'] == "Male") {
                                echo "selected";
                            }
                            ?>
                        >
                            Male
                        </option>

                        <option
                            value="Female"
                            <?php
                            if ($user['gender'] == "Female") {
                                echo "selected";
                            }
                            ?>
                        >
                            Female
                        </option>

                        <option
                            value="Other"
                            <?php
                            if ($user['gender'] == "Other") {
                                echo "selected";
                            }
                            ?>
                        >
                            Other
                        </option>

                    </select>

                </div>

            </div>


            <div class="modal-actions">

                <button
                    type="button"
                    class="modal-cancel"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="modal-save"
                >

                    Save Changes

                    <span>
                        →
                    </span>

                </button>

            </div>


        </form>

    </div>

</div>



<script>

/*
|--------------------------------------------------------------------------
| Profile Picture Upload
|--------------------------------------------------------------------------
*/

const profileInput =
    document.getElementById("profilePicInput");

const profilePreview =
    document.getElementById("profilePreview");

const profileForm =
    document.getElementById("profilePicForm");


profileInput?.addEventListener("change", function () {

    if (!this.files.length) {
        return;
    }

    profilePreview.src =
        URL.createObjectURL(this.files[0]);

    profileForm.submit();

});


/*
|--------------------------------------------------------------------------
| Edit Profile Modal
|--------------------------------------------------------------------------
*/

const editProfileBtn =
    document.getElementById("editProfileBtn");

const summaryEditBtn =
    document.getElementById("summaryEditBtn");

const modal =
    document.getElementById("editProfileModal");

const closeModal =
    document.querySelector(".close-modal");

const cancelModal =
    document.querySelector(".modal-cancel");


function openProfileModal() {

    if (!modal) return;

    modal.style.display = "flex";

    modal.setAttribute("aria-hidden", "false");

    document.body.classList.add("modal-open");

}


function closeProfileModal() {

    if (!modal) return;

    modal.style.display = "none";

    modal.setAttribute("aria-hidden", "true");

    document.body.classList.remove("modal-open");

}


editProfileBtn?.addEventListener(
    "click",
    openProfileModal
);


summaryEditBtn?.addEventListener(
    "click",
    openProfileModal
);


closeModal?.addEventListener(
    "click",
    closeProfileModal
);


cancelModal?.addEventListener(
    "click",
    closeProfileModal
);


modal?.addEventListener("click", function (event) {

    if (event.target === modal) {
        closeProfileModal();
    }

});


document.addEventListener("keydown", function (event) {

    if (event.key === "Escape") {
        closeProfileModal();
    }

});

</script>


<script src="assets/js/profile.js"></script>


</body>
</html>

