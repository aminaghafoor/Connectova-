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

$stmt=$conn->prepare("

SELECT

u.id,
u.full_name,
u.username,
u.profile_pic

FROM friends f

JOIN users u

ON (

(f.user1=? AND u.id=f.user2)

OR

(f.user2=? AND u.id=f.user1)

)

ORDER BY u.full_name

");

$stmt->bind_param(
"ii",
$user,
$user
);

$stmt->execute();

$result=$stmt->get_result();

$data=[];

while($row=$result->fetch_assoc()){
    $data[]=$row;
}

echo json_encode([
"success"=>true,
"friends"=>$data
]);