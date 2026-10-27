<?php

session_start();

require "../config.php";

header("Content-Type: application/json");

$user=$_SESSION['user_id'];

$sender=(int)$_POST['sender_id'];

$conn->query("

UPDATE friend_requests

SET status='accepted'

WHERE

sender_id=$sender

AND receiver_id=$user

");

$stmt=$conn->prepare("

INSERT INTO friends

(user1,user2)

VALUES(?,?)

");

$stmt->bind_param(

"ii",

$sender,

$user

);

$stmt->execute();

echo json_encode([

"success"=>true

]);