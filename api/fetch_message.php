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

$sender_id = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| GET RECEIVER
|--------------------------------------------------------------------------
*/

$receiver_id = intval($_GET['receiver_id'] ?? 0);

/*
|--------------------------------------------------------------------------
| VALIDATE RECEIVER
|--------------------------------------------------------------------------
*/

if ($receiver_id <= 0) {
    echo json_encode([
        "success" => false,
        "error" => "Invalid receiver."
    ]);
    exit;
}

if ($receiver_id === $sender_id) {
    echo json_encode([
        "success" => false,
        "error" => "Invalid chat."
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK FRIENDSHIP
|--------------------------------------------------------------------------
|
| Private conversations can only be viewed between confirmed friends.
|
| The friends table can store the relationship in either direction:
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
        "error" => "You can only view conversations with confirmed friends."
    ]);

    $friendStmt->close();
    $conn->close();

    exit;
}

$friendStmt->close();

/*
|--------------------------------------------------------------------------
| FETCH PRIVATE MESSAGES
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("

    SELECT

        chats.*,

        posts.id AS shared_post_id,

        posts.post_text,

        (

            SELECT file_path

            FROM post_media

            WHERE post_media.post_id = posts.id

            LIMIT 1

        ) AS shared_image

    FROM chats

    LEFT JOIN posts

        ON chats.message_type = 'post'

        AND posts.id = chats.message

    WHERE

        (chats.sender_id = ? AND chats.receiver_id = ?)

        OR

        (chats.sender_id = ? AND chats.receiver_id = ?)

    ORDER BY chats.created_at ASC

");

$stmt->bind_param(
    "iiii",
    $sender_id,
    $receiver_id,
    $receiver_id,
    $sender_id
);

$stmt->execute();

$result = $stmt->get_result();

$messages = [];

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);

$stmt->close();
$conn->close();

?>