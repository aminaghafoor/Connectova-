<?php
session_start();
require "../config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_FILES['profile_pic'])) {
    header("Location: ../profile.php");
    exit;
}

$file = $_FILES['profile_pic'];

$allowed = ["jpg","jpeg","png","gif","webp"];

$extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

if (!in_array($extension, $allowed)) {
    die("Only JPG, PNG, GIF and WEBP images are allowed.");
}

$newName = time() . "_" . rand(1000,9999) . "." . $extension;

move_uploaded_file(
    $file["tmp_name"],
    "../uploads/images/" . $newName
);

$stmt = $conn->prepare("
UPDATE users
SET profile_pic = ?
WHERE id = ?
");

$stmt->bind_param("si", $newName, $user_id);
$stmt->execute();

header("Location: ../profile.php");
exit;