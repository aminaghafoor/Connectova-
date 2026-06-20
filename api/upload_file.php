<?php
header("Content-Type: application/json");

$maxSize = 10 * 1024 * 1024; // 10MB

if (!isset($_FILES['file'])) {
    echo json_encode(["success" => false, "error" => "No file uploaded"]);
    exit;
}

$file = $_FILES['file'];

if ($file['size'] > $maxSize) {
    echo json_encode(["success" => false, "error" => "File exceeds 10MB"]);
    exit;
}

$allowedImages = ["jpg","jpeg","png","gif","webp"];
$allowedDocs = ["pdf","doc","docx","txt"];

$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (in_array($extension, $allowedImages)) {
    $type = "image";
    $folder = "../uploads/images/";
} elseif (in_array($extension, $allowedDocs)) {
    $type = "document";
    $folder = "../uploads/documents/";
} else {
    echo json_encode(["success" => false, "error" => "Unsupported file type"]);
    exit;
}

$fileName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $file['name']);
$target = $folder . $fileName;

if (!move_uploaded_file($file['tmp_name'], $target)) {
    echo json_encode(["success" => false, "error" => "Upload failed"]);
    exit;
}

echo json_encode([
    "success" => true,
    "type" => $type,
    "filename" => $fileName,
    "fileUrl" => str_replace("../", "", $target)
]);