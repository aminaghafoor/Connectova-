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

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Discover People | Connectova</title>

<link rel="stylesheet" href="assets/css/common.css">

<link rel="stylesheet" href="assets/css/people.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body>

<?php include "includes/navbar.php"; ?>

<div class="container">

    <div class="people-header">

        <h1>

            <i class="fa-solid fa-user-group"></i>

            Discover People

        </h1>

        <p>

            Find new people and send friend requests.

        </p>

    </div>

    <div
        id="peopleContainer"
        class="people-grid">

        <!-- Users will load here -->

    </div>

</div>

<script src="assets/js/people.js"></script>

</body>

</html>