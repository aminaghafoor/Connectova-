<?php

session_start();
require "../config.php";

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "User not logged in"
    ]);
    exit;
}

$sender   = (int)$_SESSION['user_id'];
$receiver = (int)$_POST['receiver_id'];
$post     = (int)$_POST['post_id'];

$sql = "INSERT INTO chats
(sender_id, receiver_id, message, message_type)
VALUES (?, ?, ?, 'post')";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);
    exit;
}

$stmt->bind_param("iii", $sender, $receiver, $post);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "insert_id" => $conn->insert_id
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => $stmt->error
    ]);

}