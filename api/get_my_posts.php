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

$sort="DESC";

if(isset($_GET['sort']) && $_GET['sort']=="old"){

$sort="ASC";

}

$sql="

SELECT

posts.*,

COUNT(DISTINCT post_likes.id) AS likes_count,


COUNT(DISTINCT post_comments.id) AS comments_count

FROM posts

LEFT JOIN post_likes
ON post_likes.post_id = posts.id

LEFT JOIN post_comments
ON post_comments.post_id = posts.id

WHERE posts.user_id=?

GROUP BY posts.id

ORDER BY posts.created_at $sort

";

$stmt=$conn->prepare($sql);

$stmt->bind_param("i",$user);

$stmt->execute();

$result=$stmt->get_result();

$posts=[];

while($row=$result->fetch_assoc()){

/* Media */

$media=[];

$m=$conn->prepare("

SELECT *

FROM post_media

WHERE post_id=?

");

$m->bind_param("i",$row['id']);

$m->execute();

$r=$m->get_result();

while($file=$r->fetch_assoc()){

$media[]=$file;

}

$row['media']=$media;

$posts[]=$row;

}

echo json_encode([

"success"=>true,

"posts"=>$posts

]);