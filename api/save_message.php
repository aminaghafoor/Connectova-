
<?php
session_start();
header("Content-Type: application/json");


$conn = new mysqli("localhost", "root", "", "connectova");

if ($conn->connect_error) {
    echo json_encode(["success"=>false,"error"=>$conn->connect_error]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success"=>false,"error"=>"No JSON received"]);
    exit;
}

/*
|--------------------------------------------------------------------------
| SENDER = LOGGED IN USER FROM users TABLE
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success"=>false,"error"=>"User not logged in"]);
    exit;
}

$sender_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| RECEIVER = SELECTED USER FROM users TABLE
|--------------------------------------------------------------------------
*/
$receiver_id = intval($data['receiver_id'] ?? 0);

$message = trim($data['message'] ?? '');
$message_type = $data['message_type'] ?? 'text';
$file_name = $data['file_name'] ?? null;
$file_path = $data['file_path'] ?? null;

if (!$receiver_id || !$message) {
    echo json_encode(["success"=>false,"error"=>"Missing data"]);
    exit;
}

/*
|--------------------------------------------------------------------------
| INSERT INTO chats
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
INSERT INTO chats
(sender_id, receiver_id, message, message_type, file_name, file_path)
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
?>