<?php

session_start();

require "../config.php";

$user_id = $_SESSION['user_id'];

$sql = "

SELECT

id,

full_name,

username

FROM users

WHERE id != ?

ORDER BY full_name

";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i",$user_id);

$stmt->execute();

$result = $stmt->get_result();

$users=[];

while($row=$result->fetch_assoc()){

    $users[]=$row;

}

echo json_encode($users);