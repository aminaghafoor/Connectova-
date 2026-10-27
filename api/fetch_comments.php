<?php

session_start();

header("Content-Type: application/json");

require_once "../config.php";



if(!isset($_SESSION['user_id']))
{

    echo json_encode([

        "success"=>false,

        "message"=>"Unauthorized"

    ]);

    exit;

}



if(!isset($_GET['post_id']))
{

    echo json_encode([

        "success"=>false,

        "message"=>"Post ID missing"

    ]);

    exit;

}



$post_id = intval($_GET['post_id']);



$stmt = $conn->prepare("

SELECT

post_comments.id,

post_comments.comment,

post_comments.created_at,


users.username,

users.profile_pic


FROM post_comments


INNER JOIN users

ON post_comments.user_id = users.id


WHERE post_comments.post_id = ?


ORDER BY post_comments.created_at ASC

");



$stmt->bind_param(

"i",

$post_id

);



$stmt->execute();



$result = $stmt->get_result();



$comments = [];



while($row = $result->fetch_assoc())
{

    $comments[] = $row;

}



echo json_encode([

    "success"=>true,

    "comments"=>$comments

]);



$stmt->close();

$conn->close();


?>