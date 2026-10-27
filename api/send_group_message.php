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
        "error" => "Login required"
    ]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| GET DATA
|--------------------------------------------------------------------------
*/

$group_id = intval($_POST['group_id'] ?? 0);

$message = trim($_POST['message'] ?? "");

$file = $_POST['file'] ?? null;

/*
|--------------------------------------------------------------------------
| VALIDATE GROUP
|--------------------------------------------------------------------------
*/

if ($group_id <= 0) {
    echo json_encode([
        "success" => false,
        "error" => "Invalid group."
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| VALIDATE MESSAGE
|--------------------------------------------------------------------------
*/

if ($message === "" && empty($file)) {
    echo json_encode([
        "success" => false,
        "error" => "Empty message."
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK GROUP MEMBERSHIP
|--------------------------------------------------------------------------
|
| A user can only send messages to a group if they are
| already a member of that group.
|
*/

$memberStmt = $conn->prepare("
    SELECT id
    FROM group_members
    WHERE group_id = ?
      AND user_id = ?
    LIMIT 1
");

$memberStmt->bind_param(
    "ii",
    $group_id,
    $user_id
);

$memberStmt->execute();

$memberResult = $memberStmt->get_result();

if ($memberResult->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "error" => "You are not a member of this group."
    ]);

    $memberStmt->close();
    $conn->close();

    exit;
}

$memberStmt->close();

/*
|--------------------------------------------------------------------------
| SAVE GROUP MESSAGE
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    INSERT INTO group_messages
    (
        group_id,
        user_id,
        message,
        file
    )
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param(
    "iiss",
    $group_id,
    $user_id,
    $message,
    $file
);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message_id" => $stmt->insert_id,
        "file" => $file
    ]);

} else {

    echo json_encode([
        "success" => false,
        "error" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();

?>