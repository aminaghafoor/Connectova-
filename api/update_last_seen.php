<?php

session_start();
include "../config.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "error" => "Not logged in"
    ]);
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = mysqli_prepare(
    $conn,
    "UPDATE users SET last_seen = NOW() WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "success" => true
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "Database update failed"
    ]);
}

mysqli_stmt_close($stmt);