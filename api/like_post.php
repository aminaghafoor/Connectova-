
<?php
/**
 * =====================================
 * CONNECTOVA
 * API : Like / Unlike Post
 * =====================================
 */

session_start();

header("Content-Type: application/json");

require_once "../config.php";
require_once "../includes/notification.php";


/*
|--------------------------------------------------------------------------
| Check Login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {

    echo json_encode([
        "success" => false,
        "message" => "Login required."
    ]);

    exit;
}

$user_id = $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| Validate Request
|--------------------------------------------------------------------------
*/

if (!isset($_POST['post_id'])) {

    echo json_encode([
        "success" => false,
        "message" => "Post ID missing."
    ]);

    exit;
}

$post_id = intval($_POST['post_id']);


/*
|--------------------------------------------------------------------------
| Get Post Owner
|--------------------------------------------------------------------------
*/

$postOwner = $conn->prepare("
    SELECT user_id
    FROM posts
    WHERE id = ?
");

$postOwner->bind_param(
    "i",
    $post_id
);

$postOwner->execute();

$postOwnerResult = $postOwner->get_result();

if ($postOwnerResult->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Post not found."
    ]);

    exit;
}

$post = $postOwnerResult->fetch_assoc();

$post_owner_id = (int) $post['user_id'];


/*
|--------------------------------------------------------------------------
| Check Existing Like
|--------------------------------------------------------------------------
*/

$check = $conn->prepare("
    SELECT id
    FROM post_likes
    WHERE post_id = ?
    AND user_id = ?
");

$check->bind_param(
    "ii",
    $post_id,
    $user_id
);

$check->execute();

$result = $check->get_result();


/*
|--------------------------------------------------------------------------
| Unlike / Like
|--------------------------------------------------------------------------
*/

if ($result->num_rows > 0) {

    /*
    |--------------------------------------------------------------------------
    | Unlike
    |--------------------------------------------------------------------------
    */

    $delete = $conn->prepare("
        DELETE FROM post_likes
        WHERE post_id = ?
        AND user_id = ?
    ");

    $delete->bind_param(
        "ii",
        $post_id,
        $user_id
    );

    $delete->execute();

    $action = "unliked";

} else {

    /*
    |--------------------------------------------------------------------------
    | Like
    |--------------------------------------------------------------------------
    */

    $insert = $conn->prepare("
        INSERT INTO post_likes
        (post_id, user_id)
        VALUES (?, ?)
    ");

    $insert->bind_param(
        "ii",
        $post_id,
        $user_id
    );

    $insert->execute();

    $action = "liked";


    /*
    |--------------------------------------------------------------------------
    | Create Notification
    |--------------------------------------------------------------------------
    */

    $senderName = "Someone";

    $nameStmt = $conn->prepare("
        SELECT full_name
        FROM users
        WHERE id = ?
    ");

    $nameStmt->bind_param(
        "i",
        $user_id
    );

    $nameStmt->execute();

    $nameResult = $nameStmt->get_result();

    if ($nameResult->num_rows > 0) {

        $sender = $nameResult->fetch_assoc();

        $senderName = $sender['full_name'];
    }

    $nameStmt->close();


    createNotification(
        $conn,
        $post_owner_id,
        $user_id,
        "like",
        $post_id,
        $senderName . " liked your post."
    );
}


/*
|--------------------------------------------------------------------------
| Get Updated Count
|--------------------------------------------------------------------------
*/

$count = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM post_likes
    WHERE post_id = ?
");

$count->bind_param(
    "i",
    $post_id
);

$count->execute();

$countResult = $count->get_result()->fetch_assoc();


/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

echo json_encode([
    "success" => true,
    "action" => $action,
    "likes" => $countResult['total']
]);


$conn->close();

?>

