<?php

session_start();
require "../config.php";

header("Content-Type: application/json");

$user=$_SESSION['user_id'];

$friend=(int)$_POST['friend_id'];

$stmt=$conn->prepare("

DELETE FROM friends

WHERE

(user1=? AND user2=?)

OR

(user1=? AND user2=?)

");

$stmt->bind_param(

"iiii",

$user,
$friend,
$friend,
$user

);

$stmt->execute();

echo json_encode([
"success"=>true
]);