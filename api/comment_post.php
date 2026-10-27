<?php
/**
 * =====================================
 * CONNECTOVA
 * API : Add Comment
 * =====================================
 */


session_start();

header("Content-Type: application/json");


require_once "../config.php";



/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/


if(!isset($_SESSION['user_id']))
{

    echo json_encode([

        "success"=>false,

        "message"=>"Login required."

    ]);

    exit;

}



$user_id = $_SESSION['user_id'];



/*
|--------------------------------------------------------------------------
| Validate Request
|--------------------------------------------------------------------------
*/


if(
    !isset($_POST['post_id']) ||
    !isset($_POST['comment'])
)
{

    echo json_encode([

        "success"=>false,

        "message"=>"Missing data."

    ]);

    exit;

}



$post_id = intval($_POST['post_id']);



$comment = trim($_POST['comment']);



if(empty($comment))
{

    echo json_encode([

        "success"=>false,

        "message"=>"Comment cannot be empty."

    ]);

    exit;

}



/*
|--------------------------------------------------------------------------
| Prevent XSS
|--------------------------------------------------------------------------
*/


$comment = htmlspecialchars(
    $comment,
    ENT_QUOTES,
    'UTF-8'
);



/*
|--------------------------------------------------------------------------
| Insert Comment
|--------------------------------------------------------------------------
*/


$stmt = $conn->prepare("

INSERT INTO post_comments

(
post_id,
user_id,
comment
)

VALUES
(?,?,?)

");



$stmt->bind_param(

"iis",

$post_id,

$user_id,

$comment

);



if($stmt->execute())
{

    echo json_encode([

        "success"=>true,

        "message"=>"Comment added successfully."

    ]);

}
else
{

    echo json_encode([

        "success"=>false,

        "message"=>"Failed to add comment."

    ]);

}



$stmt->close();

$conn->close();


?>