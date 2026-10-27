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

$currentUser = (int)$_SESSION['user_id'];
$profileUser = (int)($_GET['user_id'] ?? 0);

if ($profileUser <= 0 || $profileUser === $currentUser) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid user"
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Already Friends?
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM friends
    WHERE
        (user1 = ? AND user2 = ?)
        OR
        (user1 = ? AND user2 = ?)
    LIMIT 1
");

$stmt->bind_param(
    "iiii",
    $currentUser,
    $profileUser,
    $profileUser,
    $currentUser
);

$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {

    echo json_encode([
        "success" => true,
        "status" => "friends"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Request Sent?
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM friend_requests
    WHERE sender_id = ?
      AND receiver_id = ?
      AND status = 'pending'
    LIMIT 1
");

$stmt->bind_param(
    "ii",
    $currentUser,
    $profileUser
);

$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {

    echo json_encode([
        "success" => true,
        "status" => "sent"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Request Received?
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM friend_requests
    WHERE sender_id = ?
      AND receiver_id = ?
      AND status = 'pending'
    LIMIT 1
");

$stmt->bind_param(
    "ii",
    $profileUser,
    $currentUser
);

$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {

    echo json_encode([
        "success" => true,
        "status" => "received"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| No Relationship
|--------------------------------------------------------------------------
*/

echo json_encode([
    "success" => true,
    "status" => "none"
]);

?>