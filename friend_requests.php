<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Friend Requests | Connectova</title>

<link rel="stylesheet" href="assets/css/common.css">
<link rel="stylesheet" href="assets/css/friend_requests.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body>

<?php include "includes/navbar.php"; ?>

<div class="container">

    <div class="page-header">

        <h1>

            <i class="fa-solid fa-user-check"></i>

            Friend Requests

        </h1>

        <p>People who want to connect with you.</p>

    </div>

    <div id="requestContainer">

    </div>

</div>

<script src="assets/js/friend_requests.js"></script>

</body>

</html>