<?php

$conn = new mysqli("localhost", "root", "", "connectova");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Database connection failed"
    ]);
    exit;
}