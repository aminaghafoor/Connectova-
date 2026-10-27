<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>My Friends</title>

<link rel="stylesheet" href="assets/css/common.css">
<link rel="stylesheet" href="assets/css/friends.css">

</head>

<body>

<?php include "includes/navbar.php"; ?>

<div class="container">

<h1>My Friends</h1>

<div id="friendsContainer"></div>

</div>

<script src="assets/js/friends.js"></script>

</body>

</html>