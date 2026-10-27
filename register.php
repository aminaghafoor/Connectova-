
<?php

include 'config.php';

$message = "";

if (isset($_POST['register'])) {

    $full_name  = trim($_POST['full_name']);
    $username   = trim($_POST['username']);
    $email      = trim($_POST['email']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $birth_date = $_POST['birth_date'];
    $gender     = $_POST['gender'];

    $check = mysqli_prepare(
        $conn,
        "SELECT id FROM users WHERE username = ? OR email = ?"
    );

    mysqli_stmt_bind_param($check, "ss", $username, $email);
    mysqli_stmt_execute($check);

    $result = mysqli_stmt_get_result($check);

    if (mysqli_num_rows($result) > 0) {

        $message = "Username or email already exists!";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO users
            (full_name, username, email, password, birth_date, gender, profile_pic)
            VALUES (?, ?, ?, ?, ?, ?, 'default.jpg')"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssssss",
            $full_name,
            $username,
            $email,
            $password,
            $birth_date,
            $gender
        );

        if (mysqli_stmt_execute($stmt)) {

            header("Location: login.php");
            exit();

        } else {

            $message = "Something went wrong. Please try again.";

        }
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
        content="Create your Connectova account and start connecting with people."
    >

    <title>Create Account | Connectova</title>

    <link rel="stylesheet" href="assets/css/auth.css">

</head>

<body>

<header class="auth-header">

    <div class="auth-nav">

        <a href="index.php" class="auth-brand">

            <span class="auth-brand-mark">C</span>

            <span class="auth-brand-name">
                Connectova
            </span>

        </a>


        <nav class="auth-navbar" aria-label="Main navigation">

            <a href="index.php">
                Home
            </a>

            <a href="register.php" class="active">
                Register
            </a>

            <a href="login.php">
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
                JOIN CONNECTOVA
            </span>

            <h1>
                Your social space
                <span>starts here.</span>
            </h1>

            <p class="auth-intro-text">
                Create your Connectova account and connect with people,
                share experiences and communicate in one simple social space.
            </p>


            <div class="auth-benefits">

                <div class="auth-benefit">

                    <span class="benefit-number">
                        01
                    </span>

                    <div>

                        <h3>
                            Build your profile
                        </h3>

                        <p>
                            Create your own identity on Connectova.
                        </p>

                    </div>

                </div>


                <div class="auth-benefit">

                    <span class="benefit-number">
                        02
                    </span>

                    <div>

                        <h3>
                            Connect with people
                        </h3>

                        <p>
                            Find and interact with other users.
                        </p>

                    </div>

                </div>


                <div class="auth-benefit">

                    <span class="benefit-number">
                        03
                    </span>

                    <div>

                        <h3>
                            Stay connected
                        </h3>

                        <p>
                            Share posts and communicate with your community.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <!-- ==========================================
             REGISTER CARD
        =========================================== -->

        <section class="auth-card">

            <div class="auth-card-header">

                <div class="auth-card-mark">
                    C
                </div>

                <div>

                    <h2>
                        Create your account
                    </h2>

                    <p>
                        Enter your details to get started.
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

                    <label for="full_name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        placeholder="Enter your full name"
                        autocomplete="name"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="username">
                        Username
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Choose a username"
                        autocomplete="username"
                        required
                    >

                </div>


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
                        placeholder="Create a password"
                        autocomplete="new-password"
                        required
                    >

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label for="birth_date">
                            Date of Birth
                        </label>

                        <input
                            type="date"
                            id="birth_date"
                            name="birth_date"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="gender">
                            Gender
                        </label>

                        <select
                            id="gender"
                            name="gender"
                            required
                        >

                            <option value="">
                                Select
                            </option>

                            <option value="Male">
                                Male
                            </option>

                            <option value="Female">
                                Female
                            </option>

                            <option value="Other">
                                Other
                            </option>

                        </select>

                    </div>

                </div>


                <button
                    type="submit"
                    name="register"
                    class="auth-submit"
                >

                    <span>
                        Create Account
                    </span>

                    <span class="btn-arrow">
                        →
                    </span>

                </button>

            </form>


            <div class="auth-card-footer">

                <span>
                    Already have an account?
                </span>

                <a href="login.php">
                    Sign in
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

