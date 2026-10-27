<?php

session_start();
require "../config.php";

header("Content-Type: application/json");

if(!isset($_SESSION['user_id'])){
    exit(json_encode([
        "success"=>false
    ]));
}

$sender=$_SESSION['user_id'];
$receiver=(int)$_POST['receiver_id'];

// Prevent sending to yourself
if($sender==$receiver){
    exit(json_encode([
        "success"=>false,
        "message"=>"Invalid request."
    ]));
}

// Already pending?
$check=$conn->prepare("
SELECT id
FROM friend_requests
WHERE sender_id=?
AND receiver_id=?
AND status='pending'
");

$check->bind_param(
"ii",
$sender,
$receiver
);

$check->execute();

if($check->get_result()->num_rows){
    exit(json_encode([
        "success"=>false,
        "message"=>"Already sent."
    ]));
}

$stmt=$conn->prepare("
INSERT INTO friend_requests
(sender_id,receiver_id)
VALUES(?,?)
");

$stmt->bind_param(
"ii",
$sender,
$receiver
);

$stmt->execute();

echo json_encode([
    "success"=>true
]);