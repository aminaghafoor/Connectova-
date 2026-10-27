<?php
include "db.php";
session_start();

$group_id = $_POST['group_id'];
$user_id = $_SESSION['user_id'];

$conn->query("INSERT INTO group_members (group_id, user_id) VALUES ('$group_id', '$user_id')");

echo json_encode(["status" => "joined"]);
?>