<?php

session_start();
require "../config.php";

header("Content-Type: application/json");

if(!isset($_SESSION['user_id'])){
    echo json_encode([
        "success"=>false,
        "message"=>"Login required"
    ]);
    exit;
}

$user_id=$_SESSION['user_id'];

$full_name=trim($_POST['full_name']);
$username=trim($_POST['username']);
$email=trim($_POST['email']);
$birth_date=$_POST['birth_date'];
$gender=$_POST['gender'];

$stmt=$conn->prepare("
UPDATE users
SET
full_name=?,
username=?,
email=?,
birth_date=?,
gender=?
WHERE id=?
");

$stmt->bind_param(
"sssssi",
$full_name,
$username,
$email,
$birth_date,
$gender,
$user_id
);

if($stmt->execute()){

    echo json_encode([
        "success"=>true,
        "message"=>"Profile updated successfully."
    ]);

}else{

    echo json_encode([
        "success"=>false,
        "message"=>"Update failed."
    ]);

}