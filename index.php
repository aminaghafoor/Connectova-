
<?php

session_start();

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
        content="Connectova is a social networking platform for connecting, communicating, sharing posts and building social communities."
    >

    <title>Connectova | Connect. Share. Belong.</title>

    <link rel="stylesheet" href="assets/css/home.css">

    <script src="https://unpkg.com/lucide@latest"></script>

</head>


<body>

<!-- ==================================================
     HEADER
================================================== -->

<header class="site-header">

    <div class="container nav-container">

        <a href="index.php" class="brand">

            <span class="brand-mark">C</span>

            <span class="brand-name">Connectova</span>

        </a>


        <nav class="main-nav" aria-label="Main navigation">

            <a
                href="index.php"
                class="nav-link active"
            >
                Home
            </a>

            <a
                href="#about"
                class="nav-link"
            >
                About
            </a>

            <a
                href="#features"
                class="nav-link"
            >
                Features
            </a>


            <?php if (isset($_SESSION['user_id'])) { ?>

                <a
                    href="feed.php"
                    class="nav-link nav-feed"
                >
                    Go to Feed
                </a>

            <?php } else { ?>

                <a
                    href="login.php"
                    class="nav-link"
                >
                    Login
                </a>

                <a
                    href="register.php"
                    class="nav-button"
                >
                    Register
                </a>

            <?php } ?>


            <!-- Dark Mode Toggle - Always Last -->

            <button
                type="button"
                class="theme-toggle"
                id="themeToggle"
                aria-label="Toggle dark mode"
                title="Toggle dark mode"
            >
                <span class="theme-icon theme-sun">☀</span>
                <span class="theme-icon theme-moon">☾</span>
            </button>

        </nav>

    </div>

</header>


<main>


<!-- ==================================================
     HERO
================================================== -->

<section class="hero-section">

    <div class="container hero-container">


        <div class="hero-content">

            <div class="eyebrow">

                <span class="eyebrow-dot"></span>

                SOCIAL CONNECTION, SIMPLIFIED

            </div>


            <h1 class="hero-title">

                Stay connected to
                <span>the people who matter.</span>

            </h1>


            <p class="hero-description">

                Connectova gives you one place to connect with people,
                communicate in real time, share posts and media, and
                take part in your social community.

            </p>


            <div class="hero-actions">

                <?php if (!isset($_SESSION['user_id'])) { ?>

                    <a
                        href="register.php"
                        class="btn btn-primary"
                    >
                        Create an Account
                        <span class="btn-arrow">→</span>
                    </a>

                    <a
                        href="login.php"
                        class="btn btn-secondary"
                    >
                        Sign In
                    </a>

                <?php } else { ?>

                    <a
                        href="feed.php"
                        class="btn btn-primary"
                    >
                        Open Your Feed
                        <span class="btn-arrow">→</span>
                    </a>

                <?php } ?>

            </div>


            <div class="hero-meta">

                <span class="meta-check">✓</span>

                <span>
                    Connect. Communicate. Share.
                </span>

            </div>

        </div>


        <!-- HERO IMAGE -->

        <div class="hero-image">

            <img
                src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1400&q=85"
                alt="Friends spending time together and connecting"
            >

            <div class="image-caption">

                <span class="caption-dot"></span>

                <div>

                    <strong>
                        Real people. Real connections.
                    </strong>

                    <small>
                        Connect with your community
                    </small>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- ==================================================
     ABOUT
================================================== -->

<section class="about-section" id="about">

<div class="container">

    <div class="section-intro">

        <div>

            <span class="section-label">
                ABOUT CONNECTOVA
            </span>

            <h2>
                A social platform
                <span>built around people.</span>
            </h2>

        </div>

        <p>
            Connectova brings conversations, communities and
            shared moments together in one simple social space.
        </p>

    </div>


    <div class="about-content">


        <!-- ABOUT CARD 01 -->

        <article class="about-card">

            <div class="about-card-top">

                <div class="about-icon-wrap">
                    <span class="about-icon">◎</span>
                </div>

                <span class="about-number">
                    01
                </span>

            </div>


            <div class="about-card-content">

                <h3>
                    Connect with people
                </h3>

                <p>
                    Create your profile, discover people and build
                    meaningful connections within your social network.
                </p>

            </div>


            <div class="about-card-link">

                <span>
                    Build connections
                </span>

                <span class="about-arrow">
                    ↗
                </span>

            </div>

        </article>


        <!-- ABOUT CARD 02 -->

        <article class="about-card">

            <div class="about-card-top">

                <div class="about-icon-wrap">
                    <span class="about-icon">✦</span>
                </div>

                <span class="about-number">
                    02
                </span>

            </div>


            <div class="about-card-content">

                <h3>
                    Communicate naturally
                </h3>

                <p>
                    Move seamlessly between private conversations
                    and group chats with real-time communication.
                </p>

            </div>


            <div class="about-card-link">

                <span>
                    Start a conversation
                </span>

                <span class="about-arrow">
                    ↗
                </span>

            </div>

        </article>


        <!-- ABOUT CARD 03 -->

        <article class="about-card">

            <div class="about-card-top">

                <div class="about-icon-wrap">
                    <span class="about-icon">◇</span>
                </div>

                <span class="about-number">
                    03
                </span>

            </div>


            <div class="about-card-content">

                <h3>
                    Share what matters
                </h3>

                <p>
                    Share posts, images and videos while interacting
                    with the people who make up your community.
                </p>

            </div>


            <div class="about-card-link">

                <span>
                    Share your world
                </span>

                <span class="about-arrow">
                    ↗
                </span>

            </div>

        </article>


    </div>

</div>

</section>


<!-- ==================================================
     FEATURES
================================================== -->

<section class="features-section" id="features">

<div class="container">

    <div class="section-heading">

        <span class="section-label">
            CONNECTOVA FEATURES
        </span>

        <h2>
            Everything you need to
            <span>stay connected.</span>
        </h2>

        <p>
            Connectova brings the essential tools for communication,
            sharing and social interaction into one connected space.
        </p>

    </div>


    <div class="feature-grid">


        <!-- FEATURE 01 -->

        <article class="feature-card">

            <div class="feature-top">

                <span class="feature-number">
                    01
                </span>

                <div class="feature-icon-wrap">
                    <span class="feature-icon">◎</span>
                </div>

            </div>


            <div class="feature-content">

                <h3>
                    User Profiles
                </h3>

                <p>
                    Create and manage your personal profile and
                    let others learn more about you.
                </p>

            </div>


            <div class="feature-footer">

                <span>
                    Personal identity
                </span>

                <span class="feature-arrow">
                    ↗
                </span>

            </div>

        </article>


        <!-- FEATURE 02 -->

        <article class="feature-card">

            <div class="feature-top">

                <span class="feature-number">
                    02
                </span>

                <div class="feature-icon-wrap">
                    <span class="feature-icon">✦</span>
                </div>

            </div>


            <div class="feature-content">

                <h3>
                    One-to-One Chat
                </h3>

                <p>
                    Have private conversations through fast,
                    real-time one-to-one messaging.
                </p>

            </div>


            <div class="feature-footer">

                <span>
                    Private messaging
                </span>

                <span class="feature-arrow">
                    ↗
                </span>

            </div>

        </article>


        <!-- FEATURE 03 -->

        <article class="feature-card">

            <div class="feature-top">

                <span class="feature-number">
                    03
                </span>

                <div class="feature-icon-wrap">
                    <span class="feature-icon">◉</span>
                </div>

            </div>


            <div class="feature-content">

                <h3>
                    Real-Time Group Chat
                </h3>

                <p>
                    Communicate with multiple people through
                    real-time group conversations.
                </p>

            </div>


            <div class="feature-footer">

                <span>
                    Group conversations
                </span>

                <span class="feature-arrow">
                    ↗
                </span>

            </div>

        </article>


        <!-- FEATURE 04 -->

        <article class="feature-card">

            <div class="feature-top">

                <span class="feature-number">
                    04
                </span>

                <div class="feature-icon-wrap">
                    <span class="feature-icon">⌁</span>
                </div>

            </div>


            <div class="feature-content">

                <h3>
                    Social Feed
                </h3>

                <p>
                    Publish posts and discover content shared
                    by people in your social community.
                </p>

            </div>


            <div class="feature-footer">

                <span>
                    Share & discover
                </span>

                <span class="feature-arrow">
                    ↗
                </span>

            </div>

        </article>


        <!-- FEATURE 05 -->

        <article class="feature-card">

            <div class="feature-top">

                <span class="feature-number">
                    05
                </span>

                <div class="feature-icon-wrap">
                    <span class="feature-icon">▣</span>
                </div>

            </div>


            <div class="feature-content">

                <h3>
                    Media Sharing
                </h3>

                <p>
                    Add images and videos to your posts to make
                    your shared content more engaging.
                </p>

            </div>


            <div class="feature-footer">

                <span>
                    Images & videos
                </span>

                <span class="feature-arrow">
                    ↗
                </span>

            </div>

        </article>


        <!-- FEATURE 06 -->

        <article class="feature-card">

            <div class="feature-top">

                <span class="feature-number">
                    06
                </span>

                <div class="feature-icon-wrap">
                    <span class="feature-icon">♡</span>
                </div>

            </div>


            <div class="feature-content">

                <h3>
                    Comments & Interactions
                </h3>

                <p>
                    Respond to posts and participate in
                    conversations through social interactions.
                </p>

            </div>


            <div class="feature-footer">

                <span>
                    Community engagement
                </span>

                <span class="feature-arrow">
                    ↗
                </span>

            </div>

        </article>


    </div>

</div>

</section>


<!-- ==================================================
     CALL TO ACTION
================================================== -->

<section class="cta-section">

    <div class="container cta-container">

        <div>

            <span class="section-label cta-label">
                JOIN CONNECTOVA
            </span>

            <h2>
                Your people are
                <span>already out there.</span>
            </h2>

            <p>
                Create your Connectova account and start connecting,
                communicating and sharing with your community.
            </p>

        </div>


        <div class="cta-action">

            <?php if (!isset($_SESSION['user_id'])) { ?>

                <a
                    href="register.php"
                    class="btn btn-white"
                >
                    Create Your Account
                    <span class="btn-arrow">→</span>
                </a>

            <?php } else { ?>

                <a
                    href="feed.php"
                    class="btn btn-white"
                >
                    Go to Your Feed
                    <span class="btn-arrow">→</span>
                </a>

            <?php } ?>

        </div>

    </div>

</section>


</main>


<!-- ==================================================
     FOOTER
================================================== -->

<footer class="site-footer">

    <div class="container footer-main">


        <div class="footer-brand">

            <a
                href="index.php"
                class="brand footer-logo"
            >

                <span class="brand-mark">
                    C
                </span>

                <span class="brand-name">
                    Connectova
                </span>

            </a>


            <p>
                A social space to connect, communicate and share.
            </p>

        </div>


        <nav
            class="footer-links"
            aria-label="Footer navigation"
        >

            <a href="index.php">
                Home
            </a>

            <a href="register.php">
                Register
            </a>

            <a href="login.php">
                Login
            </a>

        </nav>

    </div>


    <div class="container footer-bottom">

        <p>
            © <?php echo date("Y"); ?> Connectova. All Rights Reserved.
        </p>

    </div>

</footer>


<!-- ==================================================
     DARK MODE
================================================== -->

<script>

(function () {

    const themeToggle =
        document.getElementById("themeToggle");

    if (!themeToggle) return;


    const savedTheme =
        localStorage.getItem("connectova-theme");


    if (savedTheme === "dark") {

        document.documentElement.classList.add(
            "dark-mode"
        );

    }


    themeToggle.addEventListener(
        "click",
        function () {

            document.documentElement.classList.toggle(
                "dark-mode"
            );


            const isDark =
                document.documentElement.classList.contains(
                    "dark-mode"
                );


            localStorage.setItem(
                "connectova-theme",
                isDark ? "dark" : "light"
            );

        }
    );

})();

</script>


<script>
    lucide.createIcons();
</script>


</body>

</html>

