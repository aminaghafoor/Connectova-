<?php

session_start();

include "./db_connection.php";

header("Content-Type: application/json");

/*
|--------------------------------------------------------------------------
| CHECK LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "error" => "User not logged in"
    ]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| GET GROUP ID
|--------------------------------------------------------------------------
*/

$group_id = intval($_GET['group_id'] ?? 0);

if ($group_id <= 0) {
    echo json_encode([
        "success" => false,
        "error" => "Invalid group."
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK GROUP MEMBERSHIP
|--------------------------------------------------------------------------
|
| Only users who are members of the group can
| retrieve its messages.
|
*/

$accessStmt = $conn->prepare("
    SELECT id
    FROM group_members
    WHERE group_id = ?
      AND user_id = ?
    LIMIT 1
");

$accessStmt->bind_param(
    "ii",
    $group_id,
    $user_id
);

$accessStmt->execute();

$accessResult = $accessStmt->get_result();

if ($accessResult->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "error" => "You are not a member of this group."
    ]);

    $accessStmt->close();
    $conn->close();

    exit;
}

$accessStmt->close();

/*
|--------------------------------------------------------------------------
| FETCH GROUP MESSAGES
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        gm.*,
        u.full_name
    FROM group_messages gm
    INNER JOIN users u
        ON gm.user_id = u.id
    WHERE gm.group_id = ?
    ORDER BY gm.created_at ASC
");

$stmt->bind_param(
    "i",
    $group_id
);

$stmt->execute();

$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

$stmt->close();
$conn->close();

?>