<?php

session_start();
require "../config.php";

header("Content-Type: application/json");

$id=(int)$_POST['request_id'];

$conn->query("

UPDATE friend_requests

SET status='rejected'

WHERE id=$id

");

echo json_encode([

"success"=>true

]);