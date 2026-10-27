<?php

session_start();
require "../config.php";

header("Content-Type: application/json");

if(!isset($_SESSION['user_id'])){

echo json_encode([
"success"=>false
]);

exit;

}

$user=$_SESSION['user_id'];

/* Total Posts */

$stmt=$conn->prepare("

SELECT COUNT(*)

FROM posts

WHERE user_id=?

");

$stmt->bind_param("i",$user);

$stmt->execute();

$stmt->bind_result($posts);

$stmt->fetch();

$stmt->close();

/* Total Likes */

$stmt=$conn->prepare("

SELECT COUNT(post_likes.id)

FROM post_likes

JOIN posts

ON posts.id=post_likes.post_id

WHERE posts.user_id=?

");

$stmt->bind_param("i",$user);

$stmt->execute();

$stmt->bind_result($likes);

$stmt->fetch();

$stmt->close();

/* Total Comments */

/* Total Comments */

$stmt = $conn->prepare("

SELECT COUNT(post_comments.id)

FROM post_comments

JOIN posts

ON posts.id = post_comments.post_id

WHERE posts.user_id = ?

");

$stmt->bind_param("i", $user);

$stmt->execute();

$stmt->bind_result($comments);

$stmt->fetch();

$stmt->close();

/* Friends */

$stmt=$conn->prepare("

SELECT COUNT(*)

FROM friends

WHERE

user1=?

OR user2=?

");

$stmt->bind_param(

"ii",

$user,

$user

);

$stmt->execute();

$stmt->bind_result($friends);

$stmt->fetch();

$stmt->close();

/* Return JSON */

echo json_encode([

"success"=>true,

"posts"=>$posts,

"likes"=>$likes,

"comments"=>$comments,

"friends"=>$friends

]);