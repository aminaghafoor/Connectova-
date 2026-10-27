<?php

session_start();

header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "connectova");

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "error" => "Database connection failed"
    ]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "error" => "User not logged in"
    ]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT
        u.id,
        u.full_name,
        u.username,
        u.profile_pic,
        u.last_seen
    FROM friends f
    INNER JOIN users u
        ON (
            (f.user1 = ? AND u.id = f.user2)
            OR
            (f.user2 = ? AND u.id = f.user1)
        )
    ORDER BY u.full_name
");

$stmt->bind_param("ii", $user_id, $user_id);

$stmt->execute();

$result = $stmt->get_result();

$users = [];

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);

$stmt->close();
$conn->close();

?>