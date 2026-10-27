<?php

session_start();


if(!isset($_SESSION['user_id']))
{

    header("Location: login.php");

    exit;

}

?>


<!DOCTYPE html>

<html lang="en">


<head>


<meta charset="UTF-8">


<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>
Connectova - Internet Slangs
</title>

<link rel="stylesheet" href="assets/css/common.css">

<link rel="stylesheet" href="chat.css">


<link rel="stylesheet" href="assets/css/slangs.css">


</head>



<body>



<nav class="navbar">


<div class="navbar-logo">

Connectova

</div>



<div class="navbar-links">


<a href="index.php">
Home
</a>


<a href="profile.php">
Profile
</a>


<a href="feed.php">
Feed
</a>


<a href="chat.php">
Chat
</a>


<a href="slangs.php">
Slangs
</a>


<a href="logout.php">
Logout
</a>



</div>


</nav>





<div class="slangs-container">



<h1 class="slangs-title">

Internet Slangs Dictionary

</h1>



<p class="slangs-description">

Understand modern internet words, abbreviations, and online expressions.

</p>





<!-- Search -->


<div class="slang-search">


<input

type="text"

id="searchSlang"

placeholder="Search slang...">


</div>






<!-- Categories -->

<div
id="categoryContainer"
class="slang-categories">

</div>

<h2 class="section-title">

🔥 Popular Slangs

</h2>

<div

id="popularContainer"

class="popular-slangs">

</div>

<h2 class="section-title">

🕒 Recently Added

</h2>

<div

id="recentContainer"

class="recent-slangs">

</div>






<!-- Slang Cards -->


<div id="slangContainer" class="slang-grid">



</div>



</div>





<script src="assets/js/slangs.js"></script>

<div id="slangModal" class="modal">

    <div class="modal-content">

        <span class="close-modal"
              onclick="closeModal()">

            &times;

        </span>

        <div id="modalBody"></div>

    </div>

</div>

</body>


</html>