<?php

session_start();
require "../config.php";

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Login required"
    ]);
    exit;
}

$request = (int)$_POST['request_id'];

// Get sender and receiver
$stmt = $conn->prepare("
SELECT sender_id, receiver_id
FROM friend_requests
WHERE id = ?
");

$stmt->bind_param("i", $request);
$stmt->execute();

$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Request not found"
    ]);
    exit;
}

// Update request status
$stmt = $conn->prepare("
UPDATE friend_requests
SET status='accepted'
WHERE id=?
");

$stmt->bind_param("i", $request);
$stmt->execute();

// Prevent duplicate friendships
$stmt = $conn->prepare("
SELECT id
FROM friends
WHERE
(user1=? AND user2=?)
OR
(user1=? AND user2=?)
");

$stmt->bind_param(
    "iiii",
    $data['sender_id'],
    $data['receiver_id'],
    $data['receiver_id'],
    $data['sender_id']
);

$stmt->execute();

if ($stmt->get_result()->num_rows == 0) {

    $stmt = $conn->prepare("
    INSERT INTO friends (user1, user2)
    VALUES (?, ?)
    ");

    $stmt->bind_param(
        "ii",
        $data['sender_id'],
        $data['receiver_id']
    );

    $stmt->execute();
}

echo json_encode([
    "success" => true
]);