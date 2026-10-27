<?php

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
| Validate Data
|--------------------------------------------------------------------------
*/


if(
    !isset($_POST['post_id']) ||
    !isset($_POST['post_text'])
)
{

    echo json_encode([

        "success"=>false,

        "message"=>"Missing data."

    ]);

    exit;

}



$post_id = intval($_POST['post_id']);



$post_text = trim($_POST['post_text']);



/*
|--------------------------------------------------------------------------
| XSS Protection
|--------------------------------------------------------------------------
*/


$post_text = htmlspecialchars(

    $post_text,

    ENT_QUOTES,

    "UTF-8"

);



/*
|--------------------------------------------------------------------------
| Check Ownership
|--------------------------------------------------------------------------
*/


$check = $conn->prepare("

SELECT user_id

FROM posts

WHERE id = ?

");


$check->bind_param(

"i",

$post_id

);


$check->execute();


$result = $check->get_result();



if($result->num_rows == 0)
{

    echo json_encode([

        "success"=>false,

        "message"=>"Post not found."

    ]);

    exit;

}



$post = $result->fetch_assoc();



if($post['user_id'] != $user_id)
{

    echo json_encode([

        "success"=>false,

        "message"=>"You cannot edit this post."

    ]);

    exit;

}



/*
|--------------------------------------------------------------------------
| Update Post
|--------------------------------------------------------------------------
*/


$update = $conn->prepare("

UPDATE posts

SET post_text = ?

WHERE id = ?

");



$update->bind_param(

"si",

$post_text,

$post_id

);



if($update->execute())
{

    echo json_encode([

        "success"=>true,

        "message"=>"Post updated successfully."

    ]);

}
else
{

    echo json_encode([

        "success"=>false,

        "message"=>"Update failed."

    ]);

}



$conn->close();


?>