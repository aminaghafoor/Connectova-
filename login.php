
<?php

session_start();
include 'config.php';

$message = "";

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM users WHERE email = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            $update = mysqli_prepare(
                $conn,
                "UPDATE users SET last_seen = NOW() WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update,
                "i",
                $user['id']
            );

            mysqli_stmt_execute($update);
            mysqli_stmt_close($update);

            header("Location: profile.php");
            exit();

        } else {

            $message = "Incorrect password!";

        }

    } else {

        $message = "User not found!";

    }
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
        content="Sign in to your Connectova account."
    >

    <title>Login | Connectova</title>

    <link rel="stylesheet" href="assets/css/auth.css">

</head>

<body>

<header class="auth-header">

    <div class="auth-nav">

        <a href="index.php" class="auth-brand">

            <span class="auth-brand-mark">
                C
            </span>

            <span class="auth-brand-name">
                Connectova
            </span>

        </a>


        <nav class="auth-navbar" aria-label="Main navigation">

            <a href="index.php">
                Home
            </a>

            <a href="register.php">
                Register
            </a>

            <a href="login.php" class="active">
                Login
            </a>

            <button
                type="button"
                class="auth-theme-toggle"
                id="themeToggle"
                aria-label="Switch to dark mode"
                title="Toggle dark mode"
            >
                <span class="theme-sun">☀</span>
                <span class="theme-moon">☾</span>
            </button>

        </nav>

    </div>

</header>


<main class="auth-page">

    <div class="auth-container">

        <!-- ==========================================
             INTRODUCTION
        =========================================== -->

        <section class="auth-intro">

            <span class="auth-eyebrow">
                WELCOME BACK
            </span>

            <h1>
                Your social space
                <span>is waiting.</span>
            </h1>

            <p class="auth-intro-text">
                Sign in to continue connecting with people,
                sharing your thoughts and staying in touch
                with your Connectova community.
            </p>


            <div class="auth-benefits">

                <div class="auth-benefit">

                    <span class="benefit-number">
                        01
                    </span>

                    <div>

                        <h3>
                            Stay connected
                        </h3>

                        <p>
                            Keep up with your friends and community.
                        </p>

                    </div>

                </div>


                <div class="auth-benefit">

                    <span class="benefit-number">
                        02
                    </span>

                    <div>

                        <h3>
                            Share your world
                        </h3>

                        <p>
                            Post updates and share moments with others.
                        </p>

                    </div>

                </div>


                <div class="auth-benefit">

                    <span class="benefit-number">
                        03
                    </span>

                    <div>

                        <h3>
                            Continue the conversation
                        </h3>

                        <p>
                            Chat and interact with your connections.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <!-- ==========================================
             LOGIN CARD
        =========================================== -->

        <section class="auth-card">

            <div class="auth-card-header">

                <div class="auth-card-mark">
                    C
                </div>

                <div>

                    <h2>
                        Welcome back
                    </h2>

                    <p>
                        Sign in to your Connectova account.
                    </p>

                </div>

            </div>


            <?php if ($message != "") { ?>

                <div class="auth-message">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php } ?>


            <form method="POST" class="auth-form">

                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        autocomplete="email"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >

                </div>


                <button
                    type="submit"
                    name="login"
                    class="auth-submit"
                >

                    <span>
                        Sign In
                    </span>

                    <span class="btn-arrow">
                        →
                    </span>

                </button>

            </form>


            <div class="auth-card-footer">

                <span>
                    Don't have an account?
                </span>

                <a href="register.php">
                    Create an account
                </a>

            </div>

        </section>

    </div>

</main>


<footer class="auth-footer-bar">

    <div class="auth-footer-main">

        <div class="auth-footer-brand">

            <a href="index.php" class="auth-brand">

                <span class="auth-brand-mark">
                    C
                </span>

                <span class="auth-brand-name">
                    Connectova
                </span>

            </a>

            <p>
                Connect, communicate and share.
            </p>

        </div>


        <nav class="auth-footer-links">

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


    <div class="auth-footer-bottom">

        <p>
            © <?php echo date("Y"); ?> Connectova.
            All Rights Reserved.
        </p>

    </div>

</footer>


<script>

(function () {

    const themeToggle =
        document.getElementById("themeToggle");

    if (!themeToggle) return;


    const savedTheme =
        localStorage.getItem("connectova-theme");


    if (savedTheme === "dark") {

        document.documentElement.classList.add("dark-mode");

    }


    function updateThemeButton() {

        const isDark =
            document.documentElement.classList.contains("dark-mode");

        themeToggle.setAttribute(
            "aria-label",
            isDark
                ? "Switch to light mode"
                : "Switch to dark mode"
        );

    }


    updateThemeButton();


    themeToggle.addEventListener("click", function () {

        document.documentElement.classList.toggle("dark-mode");


        const isDark =
            document.documentElement.classList.contains("dark-mode");


        localStorage.setItem(
            "connectova-theme",
            isDark ? "dark" : "light"
        );


        updateThemeButton();

    });

})();

</script>

</body>
</html>

