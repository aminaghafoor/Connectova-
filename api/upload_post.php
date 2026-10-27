<?php
/**
 * ==========================================
 * Connectova
 * API : Upload Post
 * Part 1
 * ==========================================
 */

session_start();

header('Content-Type: application/json');

require_once "../config.php";

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {

    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);

    exit;
}

$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Request Method
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Get Post Text
|--------------------------------------------------------------------------
*/

$post_text = "";

if (isset($_POST['post_text'])) {

    $post_text = trim($_POST['post_text']);

}

/*
|--------------------------------------------------------------------------
| Prevent Empty Post
|--------------------------------------------------------------------------
*/

$hasImages = !empty($_FILES['images']['name'][0]);
$hasVideo  = !empty($_FILES['video']['name']);

if ($post_text == "" && !$hasImages && !$hasVideo) {

    echo json_encode([
        "success" => false,
        "message" => "Post cannot be empty."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| XSS Protection
|--------------------------------------------------------------------------
*/

$post_text = htmlspecialchars($post_text, ENT_QUOTES, 'UTF-8');

/*
|--------------------------------------------------------------------------
| Upload Configuration
|--------------------------------------------------------------------------
*/

$imageFolder = "../uploads/images/";
$videoFolder = "../uploads/videos/";

$imageTypes = [

    "image/jpeg",
    "image/png",
    "image/webp",
    "image/gif"

];

$videoTypes = [

    "video/mp4",
    "video/webm",
    "video/ogg",
    "video/quicktime"

];

$maxImageSize = 5 * 1024 * 1024;
$maxVideoSize = 50 * 1024 * 1024;

/*
|--------------------------------------------------------------------------
| Create Upload Folders
|--------------------------------------------------------------------------
*/

if (!is_dir($imageFolder)) {

    mkdir($imageFolder, 0777, true);

}

if (!is_dir($videoFolder)) {

    mkdir($videoFolder, 0777, true);

}

/*
|--------------------------------------------------------------------------
| Start Database Transaction
|--------------------------------------------------------------------------
*/

mysqli_begin_transaction($conn);

try {

    /*
    |--------------------------------------------------------------------------
    | Insert Post
    |--------------------------------------------------------------------------
    */

    $sql = "INSERT INTO posts
            (user_id, post_text)
            VALUES (?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {

        throw new Exception("Failed to prepare statement.");

    }

    mysqli_stmt_bind_param(

        $stmt,
        "is",
        $user_id,
        $post_text

    );

    if (!mysqli_stmt_execute($stmt)) {

        throw new Exception("Failed to create post.");

    }

    $post_id = mysqli_insert_id($conn);

    mysqli_stmt_close($stmt);
    /*
    |--------------------------------------------------------------------------
    | Upload Images
    |--------------------------------------------------------------------------
    */

    if ($hasImages) {

        $totalImages = count($_FILES['images']['name']);

        for ($i = 0; $i < $totalImages; $i++) {

            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                throw new Exception("Image upload failed.");
            }

            $tmpName = $_FILES['images']['tmp_name'][$i];
            $originalName = $_FILES['images']['name'][$i];
            $size = $_FILES['images']['size'][$i];

            $mime = mime_content_type($tmpName);

            if (!in_array($mime, $imageTypes)) {
                throw new Exception("Invalid image type.");
            }

            if ($size > $maxImageSize) {
                throw new Exception("Image size exceeds 5 MB.");
            }

            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            $newFileName = uniqid("img_", true) . "." . $extension;

            $destination = $imageFolder . $newFileName;

            if (!move_uploaded_file($tmpName, $destination)) {
                throw new Exception("Failed to save image.");
            }

            $relativePath = "uploads/images/" . $newFileName;

            $mediaSql = "INSERT INTO post_media
                        (post_id,file_name,file_path,media_type)
                        VALUES (?,?,?,?)";

            $mediaStmt = mysqli_prepare($conn, $mediaSql);

            $mediaType = "image";

            mysqli_stmt_bind_param(
                $mediaStmt,
                "isss",
                $post_id,
                $newFileName,
                $relativePath,
                $mediaType
            );

            if (!mysqli_stmt_execute($mediaStmt)) {
                throw new Exception("Failed to save image information.");
            }

            mysqli_stmt_close($mediaStmt);

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Upload Video
    |--------------------------------------------------------------------------
    */

    if ($hasVideo) {

        if ($_FILES['video']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Video upload failed.");
        }

        $tmpName = $_FILES['video']['tmp_name'];
        $originalName = $_FILES['video']['name'];
        $size = $_FILES['video']['size'];

        $mime = mime_content_type($tmpName);

        if (!in_array($mime, $videoTypes)) {
            throw new Exception("Invalid video type.");
        }

        if ($size > $maxVideoSize) {
            throw new Exception("Video exceeds 50 MB.");
        }

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $newFileName = uniqid("vid_", true) . "." . $extension;

        $destination = $videoFolder . $newFileName;

        if (!move_uploaded_file($tmpName, $destination)) {
            throw new Exception("Failed to save video.");
        }

        $relativePath = "uploads/videos/" . $newFileName;

        $mediaSql = "INSERT INTO post_media
                    (post_id,file_name,file_path,media_type)
                    VALUES (?,?,?,?)";

        $mediaStmt = mysqli_prepare($conn, $mediaSql);

        $mediaType = "video";

        mysqli_stmt_bind_param(
            $mediaStmt,
            "isss",
            $post_id,
            $newFileName,
            $relativePath,
            $mediaType
        );

        if (!mysqli_stmt_execute($mediaStmt)) {
            throw new Exception("Failed to save video information.");
        }

        mysqli_stmt_close($mediaStmt);

    }

    /*
    |--------------------------------------------------------------------------
    | Commit Transaction
    |--------------------------------------------------------------------------
    */

    mysqli_commit($conn);

    echo json_encode([
        "success" => true,
        "message" => "Post uploaded successfully."
    ]);

} catch (Exception $e) {

    mysqli_rollback($conn);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}

mysqli_close($conn);

?>