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

$sender_id = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| READ JSON DATA
|--------------------------------------------------------------------------
*/

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "error" => "No JSON received"
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| GET RECEIVER
|--------------------------------------------------------------------------
*/

$receiver_id = (int) ($data['receiver_id'] ?? 0);

$message = trim($data['message'] ?? '');
$message_type = $data['message_type'] ?? 'text';

$file_name = $data['file_name'] ?? null;
$file_path = $data['file_path'] ?? null;

/*
|--------------------------------------------------------------------------
| BASIC VALIDATION
|--------------------------------------------------------------------------
*/

if (!$receiver_id) {
    echo json_encode([
        "success" => false,
        "error" => "Invalid receiver"
    ]);
    exit;
}

if ($receiver_id === $sender_id) {
    echo json_encode([
        "success" => false,
        "error" => "You cannot message yourself"
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| FRIENDSHIP CHECK
|--------------------------------------------------------------------------
|
| A private message can only be sent if the sender
| and receiver exist in the friends table.
|
| The friendship can be stored in either direction:
|
| user1 = sender,   user2 = receiver
| OR
| user1 = receiver, user2 = sender
|
*/

$friendStmt = $conn->prepare("
    SELECT id
    FROM friends
    WHERE
        (user1 = ? AND user2 = ?)
        OR
        (user1 = ? AND user2 = ?)
    LIMIT 1
");

$friendStmt->bind_param(
    "iiii",
    $sender_id,
    $receiver_id,
    $receiver_id,
    $sender_id
);

$friendStmt->execute();

$friendResult = $friendStmt->get_result();

if ($friendResult->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "error" => "You can only message confirmed friends."
    ]);

    $friendStmt->close();
    $conn->close();

    exit;
}

$friendStmt->close();

/*
|--------------------------------------------------------------------------
| MESSAGE VALIDATION
|--------------------------------------------------------------------------
|
| Text messages require message content.
| File messages may use file_path instead.
|
*/

if ($message_type === "text" && $message === "") {

    echo json_encode([
        "success" => false,
        "error" => "Message cannot be empty"
    ]);

    $conn->close();

    exit;
}

/*
|--------------------------------------------------------------------------
| INSERT MESSAGE
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    INSERT INTO chats
    (
        sender_id,
        receiver_id,
        message,
        message_type,
        file_name,
        file_path
    )
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iissss",
    $sender_id,
    $receiver_id,
    $message,
    $message_type,
    $file_name,
    $file_path
);

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "insert_id" => $stmt->insert_id
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