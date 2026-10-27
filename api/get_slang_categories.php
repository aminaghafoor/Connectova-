<?php

session_start();

header("Content-Type: application/json");

require_once "../config.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
    ]);
    exit;
}

$sql = "
SELECT DISTINCT category
FROM slangs
WHERE category IS NOT NULL
AND category <> ''
ORDER BY category ASC
";

$result = mysqli_query($conn, $sql);

$categories = [];

while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row['category'];
}

echo json_encode([
    "success" => true,
    "categories" => $categories
]);

mysqli_close($conn);
?>