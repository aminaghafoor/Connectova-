
<?php

session_start();

header("Content-Type: application/json");

require_once "../config.php";


/* ==========================================
   CHECK LOGIN
========================================== */

if (!isset($_SESSION['user_id'])) {

    echo json_encode([
        "success" => false,
        "message" => "Login required."
    ]);

    exit;
}


$user_id = (int) $_SESSION['user_id'];


/* ==========================================
   MARK NOTIFICATIONS AS READ
========================================== */

$stmt = $conn->prepare("

    UPDATE notifications

    SET is_read = 1

    WHERE user_id = ?

    AND is_read = 0

");


$stmt->bind_param(
    "i",
    $user_id
);


if ($stmt->execute()) {

    echo json_encode([
        "success" => true
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Unable to mark notifications as read."
    ]);

}


$stmt->close();
$conn->close();

?>

