<?php

session_start();
require "../config.php";

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false
    ]);
    exit;
}

$currentUser = $_SESSION['user_id'];

$stmt = $conn->prepare("

SELECT

u.id,

u.full_name,

u.username,

u.profile_pic,

CASE

WHEN EXISTS(

SELECT 1

FROM friends

WHERE

(user1=? AND user2=u.id)

OR

(user1=u.id AND user2=?)

)

THEN 'friends'

WHEN EXISTS(

SELECT 1

FROM friend_requests

WHERE

sender_id=?

AND receiver_id=u.id

AND status='pending'

)

THEN 'sent'

WHEN EXISTS(

SELECT 1

FROM friend_requests

WHERE

sender_id=u.id

AND receiver_id=?

AND status='pending'

)

THEN 'received'

ELSE 'none'

END AS relationship

FROM users u

WHERE u.id<>?

ORDER BY u.full_name

");

$stmt->bind_param(

"iiiii",

$currentUser,
$currentUser,
$currentUser,
$currentUser,
$currentUser

);


$stmt->execute();

$result = $stmt->get_result();

$people = [];

while($row = $result->fetch_assoc()){
    $people[] = $row;
}

echo json_encode([
    "success" => true,
    "people" => $people
]);