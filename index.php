<?php
session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit();
}

echo "<h1>Welcome to Connectova</h1>";
?>

<nav>
    <a href="index.php">Home</a>
    <a href="register.php">Register</a>
    <a href="login.php">Login</a>
    <a href="profile.php">Profile</a>
    <a href="chat.php">Chat</a>
</nav>