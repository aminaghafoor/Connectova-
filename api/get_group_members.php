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
| Only members of a group can view its member list.
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
| FETCH GROUP MEMBERS
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        u.id,
        u.full_name,
        u.profile_pic
    FROM group_members gm
    INNER JOIN users u
        ON gm.user_id = u.id
    WHERE gm.group_id = ?
    ORDER BY u.full_name
");

$stmt->bind_param(
    "i",
    $group_id
);

$stmt->execute();

$result = $stmt->get_result();

$members = [];

while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

echo json_encode($members);

$stmt->close();
$conn->close();

?>