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
created_at

FROM slangs

ORDER BY created_at DESC

LIMIT 5

";

$result = mysqli_query($conn,$sql);

$recent = [];

while($row=mysqli_fetch_assoc($result))
{
    $recent[]=$row;
}

echo json_encode([
    "success"=>true,
    "recent"=>$recent
]);

mysqli_close($conn);

?>