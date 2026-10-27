<?php
/**
 * =====================================
 * CONNECTOVA
 * API : Search Slangs
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



/*
|--------------------------------------------------------------------------
| Get Search Data
|--------------------------------------------------------------------------
*/


$search = isset($_GET['search'])
        ? trim($_GET['search'])
        : "";


$category = isset($_GET['category'])
        ? trim($_GET['category'])
        : "";



/*
|--------------------------------------------------------------------------
| Base Query
|--------------------------------------------------------------------------
*/


$sql = "

SELECT

slangs.id,

slangs.slang_term,

slangs.meaning,

slangs.example_sentence,

slangs.category,

slangs.created_at,

users.username


FROM slangs


LEFT JOIN users

ON slangs.added_by = users.id


WHERE 1=1

";



$params = [];

$types = "";



/*
|--------------------------------------------------------------------------
| Search Filter
|--------------------------------------------------------------------------
*/


if($search !== "")
{

    $sql .= "

    AND (

        slang_term LIKE ?

        OR meaning LIKE ?

        OR example_sentence LIKE ?

    )

    ";


    $keyword = "%".$search."%";


    $params[] = $keyword;

    $params[] = $keyword;

    $params[] = $keyword;


    $types .= "sss";

}



/*
|--------------------------------------------------------------------------
| Category Filter
|--------------------------------------------------------------------------
*/


if($category !== "")
{

    $sql .= "

    AND category = ?

    ";


    $params[] = $category;


    $types .= "s";

}



/*
|--------------------------------------------------------------------------
| Order
|--------------------------------------------------------------------------
*/


$sql .= "

ORDER BY created_at DESC

";



$stmt = $conn->prepare($sql);



if(!empty($params))
{

    $stmt->bind_param(

        $types,

        ...$params

    );

}



$stmt->execute();



$result = $stmt->get_result();



$slangs = [];



while($row = $result->fetch_assoc())
{

    $slangs[] = $row;

}



echo json_encode([

    "success"=>true,

    "slangs"=>$slangs

]);



$stmt->close();

$conn->close();


?>