<?php
session_start();
include "./db_connection.php";

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success"=>false,"error"=>"User not logged in"]);
    exit;
}

$sender_id = $_SESSION['user_id'];
$receiver_id = intval($_GET['receiver_id']);

$stmt = $conn->prepare("
SELECT *
FROM chats
WHERE
(sender_id = ? AND receiver_id = ?)
OR
(sender_id = ? AND receiver_id = ?)
ORDER BY created_at ASC
");

$stmt->bind_param(
    "iiii",
    $sender_id,
    $receiver_id,
    $receiver_id,
    $sender_id
);

$stmt->execute();

$result = $stmt->get_result();

$messages = [];

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
?>