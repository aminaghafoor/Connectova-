<?php
/**
 * =====================================
 * CONNECTOVA
 * API : Fetch Posts
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

        "message"=>"Unauthorized access."

    ]);

    exit;

}


$user_id = $_SESSION['user_id'];



/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/


$limit = isset($_GET['limit']) 
        ? intval($_GET['limit']) 
        : 5;


$offset = isset($_GET['offset']) 
        ? intval($_GET['offset']) 
        : 0;



/*
|--------------------------------------------------------------------------
| Fetch Posts
|--------------------------------------------------------------------------
*/


$sql = "

SELECT

posts.id,

posts.user_id,

posts.post_text,

posts.created_at,


users.username,

users.profile_pic,


COUNT(post_likes.id) AS likes_count


FROM posts


INNER JOIN users

ON posts.user_id = users.id


LEFT JOIN post_likes

ON posts.id = post_likes.post_id


GROUP BY posts.id


ORDER BY posts.created_at DESC


LIMIT ? OFFSET ?

";



$stmt = $conn->prepare($sql);



$stmt->bind_param(

"ii",

$limit,

$offset

);



$stmt->execute();



$result = $stmt->get_result();



$posts = [];



/*
|--------------------------------------------------------------------------
| Prepare Posts
|--------------------------------------------------------------------------
*/


while($row = $result->fetch_assoc())
{


    $post_id = $row['id'];



    /*
    |--------------------------------------------------------------------------
    | Check Current User Like Status
    |--------------------------------------------------------------------------
    */


    $like_check = $conn->prepare("

        SELECT id

        FROM post_likes

        WHERE post_id = ?

        AND user_id = ?

    ");



    $like_check->bind_param(

        "ii",

        $post_id,

        $user_id

    );



    $like_check->execute();



    $like_result = $like_check->get_result();



    if($like_result->num_rows > 0)
    {

        $row['user_liked'] = 1;

    }
    else
    {

        $row['user_liked'] = 0;

    }



    $like_check->close();



    /*
    |--------------------------------------------------------------------------
    | Fetch Media
    |--------------------------------------------------------------------------
    */


    $media_stmt = $conn->prepare("

        SELECT

        file_path,

        media_type


        FROM post_media


        WHERE post_id = ?

    ");



    $media_stmt->bind_param(

        "i",

        $post_id

    );



    $media_stmt->execute();



    $media_result = $media_stmt->get_result();



    $media = [];



    while($media_row = $media_result->fetch_assoc())
    {

        $media[] = $media_row;

    }



    $row['media'] = $media;



    $posts[] = $row;



}



echo json_encode([

    "success"=>true,

    "posts"=>$posts

]);



$stmt->close();

$conn->close();


?>