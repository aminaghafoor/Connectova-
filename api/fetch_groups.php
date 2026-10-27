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
| FETCH GROUPS FOR CURRENT USER
|--------------------------------------------------------------------------
|
| Only groups where the logged-in user is a member
| will be returned.
|
*/

$stmt = $conn->prepare("
    SELECT
        g.id,
        g.name
    FROM groups g
    INNER JOIN group_members gm
        ON g.id = gm.group_id
    WHERE gm.user_id = ?
    ORDER BY g.name
");

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$groups = [];

while ($row = $result->fetch_assoc()) {
    $groups[] = $row;
}

echo json_encode($groups);

$stmt->close();
$conn->close();

?>