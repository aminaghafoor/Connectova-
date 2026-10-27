<?php

session_start();

header("Content-Type: application/json");

require_once "../config.php";



if(!isset($_SESSION['user_id']))
{

    echo json_encode([

        "success"=>false,

        "message"=>"Login required."

    ]);

    exit;

}



$user_id = $_SESSION['user_id'];



if(!isset($_POST['post_id']))
{

    echo json_encode([

        "success"=>false,

        "message"=>"Post ID missing."

    ]);

    exit;

}



$post_id = intval($_POST['post_id']);



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

        "message"=>"You cannot delete this post."

    ]);

    exit;

}



/*
|--------------------------------------------------------------------------
| Delete Media Files
|--------------------------------------------------------------------------
*/


$media = $conn->prepare("

SELECT file_path

FROM post_media

WHERE post_id = ?

");


$media->bind_param(

"i",

$post_id

);


$media->execute();


$media_result = $media->get_result();



while($file = $media_result->fetch_assoc())
{

    if(file_exists("../".$file['file_path']))
    {

        unlink("../".$file['file_path']);

    }

}



/*
|--------------------------------------------------------------------------
| Delete Related Data
|--------------------------------------------------------------------------
*/


$tables = [

    "post_media",

    "post_likes",

    "post_comments"

];



foreach($tables as $table)
{

    $delete = $conn->prepare("

    DELETE FROM $table

    WHERE post_id = ?

    ");



    $delete->bind_param(

        "i",

        $post_id

    );


    $delete->execute();

}



/*
|--------------------------------------------------------------------------
| Delete Post
|--------------------------------------------------------------------------
*/


$delete_post = $conn->prepare("

DELETE FROM posts

WHERE id = ?

");



$delete_post->bind_param(

"i",

$post_id

);



if($delete_post->execute())
{

    echo json_encode([

        "success"=>true,

        "message"=>"Post deleted successfully."

    ]);

}
else
{

    echo json_encode([

        "success"=>false,

        "message"=>"Delete failed."

    ]);

}



$conn->close();


?>