<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF']);

?>

<nav class="navbar">

    <div class="nav-container">

        <!-- LOGO -->
        <a
            href="<?= isset($_SESSION['user_id']) ? 'profile.php' : 'index.php'; ?>"
            class="logo"
        >
            <span class="logo-mark">
                <i class="fa-solid fa-link"></i>
            </span>

            <span class="logo-text">
                Connectova
            </span>
        </a>


        <!-- NAVIGATION -->
        <ul class="nav-links">

        <?php if (isset($_SESSION['user_id'])): ?>

            <!-- PROFILE -->
            <li>
                <a
                    href="profile.php"
                    class="<?= $currentPage === 'profile.php' ? 'active' : ''; ?>"
                >
                    <i class="fa-regular fa-user"></i>
                    <span>Profile</span>
                </a>
            </li>


            <!-- FEED -->
            <li>
                <a
                    href="feed.php"
                    class="<?= $currentPage === 'feed.php' ? 'active' : ''; ?>"
                >
                    <i class="fa-solid fa-house"></i>
                    <span>Feed</span>
                </a>
            </li>


            <!-- CREATE -->
            <li>
                <a
                    href="post_upload.php"
                    class="<?= $currentPage === 'post_upload.php' ? 'active' : ''; ?>"
                >
                    <i class="fa-solid fa-plus"></i>
                    <span>Create</span>
                </a>
            </li>


            <!-- CHAT -->
            <li>
                <a
                    href="chat.php"
                    class="<?= $currentPage === 'chat.php' ? 'active' : ''; ?>"
                >
                    <i class="fa-regular fa-message"></i>
                    <span>Chat</span>
                </a>
            </li>


            <!-- DISCOVER PEOPLE -->
            <li>
                <a
                    href="people.php"
                    class="<?= $currentPage === 'people.php' ? 'active' : ''; ?>"
                >
                    <i class="fa-solid fa-compass"></i>
                    <span>Discover</span>
                </a>
            </li>


            <!-- FRIEND REQUESTS -->
            <li>
                <a
                    href="friend_requests.php"
                    class="<?= $currentPage === 'friend_requests.php' ? 'active' : ''; ?>"
                >
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Requests</span>
                </a>
            </li>


            <!-- FRIENDS -->
            <li>
                <a
                    href="friends.php"
                    class="<?= $currentPage === 'friends.php' ? 'active' : ''; ?>"
                >
                    <i class="fa-solid fa-user-group"></i>
                    <span>Friends</span>
                </a>
            </li>


            <!-- DASHBOARD -->
            <li>
                <a
                    href="dashboard.php"
                    class="<?= $currentPage === 'dashboard.php' ? 'active' : ''; ?>"
                >
                    <i class="fa-solid fa-chart-column"></i>
                    <span>Dashboard</span>
                </a>
            </li>


            <!-- NOTIFICATIONS -->
            <li class="notification-wrapper">

                <button
                    type="button"
                    id="notificationBtn"
                    class="notification-btn"
                    aria-label="Notifications"
                >

                    <i class="fa-regular fa-bell"></i>

                    <span
                        id="notificationBadge"
                        class="notification-badge"
                        style="display: none;"
                    >
                        0
                    </span>

                </button>


                <div
                    id="notificationDropdown"
                    class="notification-dropdown"
                >

                    <div class="notification-header">

                        <div>
                            <span class="notification-title">
                                Notifications
                            </span>

                            <span class="notification-subtitle">
                                Stay updated
                            </span>
                        </div>

                        <i class="fa-regular fa-bell notification-header-icon"></i>

                    </div>


                    <div id="notificationList">

                        <p class="no-notifications">
                            <i class="fa-solid fa-spinner"></i>
                            Loading...
                        </p>

                    </div>

                </div>

            </li>


            <!-- LOGOUT -->
            <li class="logout-item">

                <a href="logout.php">

                    <i class="fa-solid fa-arrow-right-from-bracket"></i>

                    <span>Logout</span>

                </a>

            </li>


        <?php else: ?>


            <!-- GUEST MENU -->

            <li>
                <a
                    href="index.php"
                    class="<?= $currentPage === 'index.php' ? 'active' : ''; ?>"
                >
                    <i class="fa-solid fa-house"></i>
                    <span>Home</span>
                </a>
            </li>


            <li>
                <a
                    href="feed.php"
                    class="<?= $currentPage === 'feed.php' ? 'active' : ''; ?>"
                >
                    <i class="fa-solid fa-newspaper"></i>
                    <span>Feed</span>
                </a>
            </li>


            <li>
                <a
                    href="register.php"
                    class="<?= $currentPage === 'register.php' ? 'active' : ''; ?>"
                >
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Register</span>
                </a>
            </li>


            <li>
                <a
                    href="login.php"
                    class="<?= $currentPage === 'login.php' ? 'active' : ''; ?>"
                >
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Login</span>
                </a>
            </li>


        <?php endif; ?>

        </ul>

    </div>

</nav>


<?php if (isset($_SESSION['user_id'])): ?>

<script>

document.addEventListener("DOMContentLoaded", function () {

    const notificationBtn =
        document.getElementById("notificationBtn");

    const notificationDropdown =
        document.getElementById("notificationDropdown");

    const notificationList =
        document.getElementById("notificationList");

    const notificationBadge =
        document.getElementById("notificationBadge");


    /* ==========================================
       LOAD NOTIFICATIONS
    ========================================== */

    function loadNotifications() {

        fetch("api/get_notifications.php")

            .then(response => {

                if (!response.ok) {
                    throw new Error("HTTP error: " + response.status);
                }

                return response.json();

            })

            .then(data => {

                if (!data.success) {

                    notificationList.innerHTML = `
                        <p class="no-notifications">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            Unable to load notifications.
                        </p>
                    `;

                    notificationBadge.style.display = "none";

                    return;
                }


                const notifications =
                    Array.isArray(data.notifications)
                        ? data.notifications
                        : [];


                notificationList.innerHTML = "";


                /* NO NOTIFICATIONS */

                if (notifications.length === 0) {

                    notificationList.innerHTML = `
                        <p class="no-notifications">
                            <i class="fa-regular fa-bell-slash"></i>
                            No notifications
                        </p>
                    `;

                    notificationBadge.style.display = "none";

                    return;
                }


                /* UNREAD COUNT */

                const unreadCount = notifications.filter(
                    notification =>
                        Number(notification.is_read) === 0
                ).length;


                if (unreadCount > 0) {

                    notificationBadge.textContent =
                        unreadCount > 99
                            ? "99+"
                            : unreadCount;

                    notificationBadge.style.display = "flex";

                } else {

                    notificationBadge.style.display = "none";

                }


                /* DISPLAY NOTIFICATIONS */

                notifications.forEach(notification => {

                    const item =
                        document.createElement("div");

                    item.className =
                        "notification-item";


                    if (Number(notification.is_read) === 0) {

                        item.classList.add("unread");

                    }


                    const profilePic =
                        notification.profile_pic &&
                        notification.profile_pic !== ""
                            ? "uploads/images/" +
                              notification.profile_pic
                            : "uploads/images/default.jpg";


                    item.innerHTML = `

                        <img
                            src="${profilePic}"
                            class="notification-avatar"
                            alt="Profile"
                        >

                        <div class="notification-content">

                            <p>
                                ${notification.message}
                            </p>

                            <small>
                                <i class="fa-regular fa-clock"></i>
                                ${notification.created_at}
                            </small>

                        </div>

                    `;


                    notificationList.appendChild(item);

                });

            })

            .catch(error => {

                console.error(
                    "NOTIFICATION FETCH ERROR:",
                    error
                );

                notificationList.innerHTML = `
                    <p class="no-notifications">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        Unable to load notifications.
                    </p>
                `;

            });

    }


    /* ==========================================
       OPEN / CLOSE NOTIFICATION DROPDOWN
    ========================================== */

    notificationBtn.addEventListener(
        "click",
        function (event) {

            event.stopPropagation();

            const isOpening =
                !notificationDropdown.classList.contains("show");


            notificationDropdown.classList.toggle("show");


            /* MARK NOTIFICATIONS AS READ */

            if (isOpening) {

                fetch(
                    "api/mark_notifications_read.php",
                    {
                        method: "POST"
                    }
                )

                .then(response => response.json())

                .then(data => {

                    if (data.success) {

                        notificationBadge.style.display =
                            "none";


                        document
                            .querySelectorAll(
                                ".notification-item.unread"
                            )
                            .forEach(item => {

                                item.classList.remove("unread");

                            });

                    }

                })

                .catch(error => {

                    console.error(
                        "MARK NOTIFICATIONS READ ERROR:",
                        error
                    );

                });

            }

        }
    );


    /* ==========================================
       PREVENT DROPDOWN FROM CLOSING
    ========================================== */

    notificationDropdown.addEventListener(
        "click",
        function (event) {

            event.stopPropagation();

        }
    );


    /* ==========================================
       CLOSE OUTSIDE
    ========================================== */

    document.addEventListener(
        "click",
        function () {

            notificationDropdown.classList.remove("show");

        }
    );


    /* ==========================================
       INITIAL LOAD
    ========================================== */

    loadNotifications();


    /* ==========================================
       REFRESH EVERY 15 SECONDS
    ========================================== */

    setInterval(
        loadNotifications,
        15000
    );

});

</script>

<?php endif; ?>