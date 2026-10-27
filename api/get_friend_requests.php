<?php

session_start();
require "../config.php";

header("Content-Type: application/json");

if(!isset($_SESSION['user_id'])){
    exit(json_encode([
        "success"=>false
    ]));
}

$user=$_SESSION['user_id'];

$stmt = $conn->prepare("

SELECT

friend_requests.id AS request_id,

users.id AS sender_id,

users.full_name,

users.username,

users.profile_pic

FROM friend_requests

JOIN users

ON users.id = friend_requests.sender_id

WHERE

receiver_id = ?

AND status='pending'

ORDER BY friend_requests.created_at DESC

");

$stmt->bind_param("i",$user);

$stmt->execute();

$result=$stmt->get_result();

$data=[];

while($row=$result->fetch_assoc()){

    $data[]=$row;

}

echo json_encode([

"success"=>true,

"requests"=>$data

]);