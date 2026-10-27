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

$sql = "

SELECT

id,
slang_term,
category,
views

FROM slangs

ORDER BY views DESC,
created_at DESC

LIMIT 5

";

$result = mysqli_query($conn,$sql);

$popular=[];

while($row=mysqli_fetch_assoc($result))
{
    $popular[]=$row;
}

echo json_encode([
    "success"=>true,
    "popular"=>$popular
]);

mysqli_close($conn);

?>