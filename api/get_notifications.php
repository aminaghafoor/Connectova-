
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
   GET NOTIFICATIONS
========================================== */

$stmt = $conn->prepare("

    SELECT
        n.id,
        n.user_id,
        n.type,
        n.sender_id,
        n.message,
        n.is_read,
        n.created_at,

        u.full_name,
        u.username,
        u.profile_pic

    FROM notifications n

    LEFT JOIN users u
        ON u.id = n.sender_id

    WHERE n.user_id = ?

    ORDER BY n.created_at DESC

    LIMIT 50

");


$stmt->bind_param(
    "i",
    $user_id
);


$stmt->execute();


$result = $stmt->get_result();


$notifications = [];


while ($row = $result->fetch_assoc()) {

    $notifications[] = $row;

}


$stmt->close();


/* ==========================================
   UNREAD COUNT
========================================== */

$countStmt = $conn->prepare("

    SELECT COUNT(*) AS total

    FROM notifications

    WHERE user_id = ?

    AND is_read = 0

");


$countStmt->bind_param(
    "i",
    $user_id
);


$countStmt->execute();


$countResult =
    $countStmt
        ->get_result()
        ->fetch_assoc();


$unreadCount =
    (int) $countResult['total'];


$countStmt->close();


$conn->close();


/* ==========================================
   RESPONSE
========================================== */

echo json_encode([

    "success" => true,

    "logged_in_user" => $user_id,

    "notification_count" => $unreadCount,

    "notifications" => $notifications

]);

?>

