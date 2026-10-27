<?php

session_start();

header("Content-Type: application/json");

require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
    ]);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing ID"
    ]);
    exit;
}

$id = intval($_GET['id']);

/* Increase view count */

$stmt = $conn->prepare("
UPDATE slangs
SET views = views + 1
WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

/* Fetch slang */

$stmt = $conn->prepare("
SELECT
slangs.*,
users.username
FROM slangs
LEFT JOIN users
ON users.id = slangs.added_by
WHERE slangs.id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {

    echo json_encode([
        "success" => false,
        "message" => "Slang not found"
    ]);

    exit;
}

echo json_encode([
    "success" => true,
    "slang" => $result->fetch_assoc()
]);

$conn->close();

?>