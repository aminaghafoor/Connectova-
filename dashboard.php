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

<title>My Dashboard</title>

<link rel="stylesheet" href="assets/css/common.css">
<link rel="stylesheet" href="assets/css/dashboard.css">

</head>

<body>

<?php include "includes/navbar.php"; ?>

<div class="dashboard-container">

<h1>My Dashboard</h1>

<!-- Statistics -->

<div id="dashboardStats" class="stats-grid">

<div class="stat-card">

<h2 id="totalPosts">0</h2>

<p>Posts</p>

</div>

<div class="stat-card">

<h2 id="totalLikes">0</h2>

<p>Likes</p>

</div>

<div class="stat-card">

<h2 id="totalComments">0</h2>

<p>Comments</p>

</div>

<div class="stat-card">

<h2 id="totalFriends">0</h2>

<p>Friends</p>

</div>

<div class="dashboard-post-section">

    <h2>
        My Posts
    </h2>


</div>

</div>

<!-- Search & Sort -->

<div class="dashboard-tools">

<input
type="text"
id="searchPost"
placeholder="Search your posts...">

<select id="sortPosts">

<option value="new">Newest First</option>

<option value="old">Oldest First</option>

</select>

</div>

<!-- Posts -->

<div id="dashboardPosts">

</div>

</div>

<script src="assets/js/dashboard.js"></script>

</body>

</html>