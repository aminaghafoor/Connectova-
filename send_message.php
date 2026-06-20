<?php
include "database.php";
session_start();

$user_id = $_SESSION['user_id'] ?? 1;

$message = $_POST['message'] ?? '';

if ($message != "") {

    $stmt = $conn->prepare("INSERT INTO chats (sender_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $message);

    if ($stmt->execute()) {
        echo "sent";
    } else {
        echo "error: " . $conn->error;
    }

} else {
    echo "empty message";
}
?>