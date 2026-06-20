<?php
session_start();
include 'config.php';

$message = "";

if(isset($_POST['login']))
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0)
    {
        $user = mysqli_fetch_assoc($result);

        if(password_verify($password, $user['password']))
        {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header("Location: profile.php");
            exit();
        }
        else
        {
            $message = "Incorrect Password!";
        }
    }
    else
    {
        $message = "User Not Found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Connectova</title>
</head>
<body>

<h2>Login</h2>

<nav>
    <a href="index.php">Home</a>
    <a href="register.php">Register</a>
    <a href="login.php">Login</a>
    <a href="profile.php">Profile</a>
    <a href="chat.php">Chat</a>
</nav>
<p><?php echo $message; ?></p>

<form method="POST">

    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit" name="login">
        Login
    </button>

</form>

<p>
    Don't have an account?
    <a href="register.php">Register Here</a>
</p>

</body>
</html>