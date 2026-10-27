<?php

session_start();

header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "connectova");

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "error" => "Database connection failed"
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "error" => "User not logged in"
    ]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| GET FORM DATA
|--------------------------------------------------------------------------
*/

$name = trim($_POST['name'] ?? '');

$members = $_POST['members'] ?? [];

/*
|--------------------------------------------------------------------------
| VALIDATE GROUP NAME
|--------------------------------------------------------------------------
*/

if ($name === '') {
    echo json_encode([
        "success" => false,
        "error" => "Group name is required"
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| NORMALIZE MEMBER IDS
|--------------------------------------------------------------------------
|
| Convert submitted IDs to integers and remove duplicates.
|
*/

$members = array_map('intval', $members);
$members = array_unique($members);

/*
|--------------------------------------------------------------------------
| REMOVE LOGGED-IN USER FROM MEMBER LIST
|--------------------------------------------------------------------------
|
| The creator is automatically added to the group below.
|
*/

$members = array_filter(
    $members,
    function ($member_id) use ($user_id) {
        return $member_id > 0 && $member_id !== $user_id;
    }
);

/*
|--------------------------------------------------------------------------
| START DATABASE TRANSACTION
|--------------------------------------------------------------------------
*/

$conn->begin_transaction();

try {

    /*
    |--------------------------------------------------------------------------
    | VERIFY EVERY MEMBER IS A CONFIRMED FRIEND
    |--------------------------------------------------------------------------
    */

    $friendStmt = $conn->prepare("
        SELECT id
        FROM friends
        WHERE
            (user1 = ? AND user2 = ?)
            OR
            (user1 = ? AND user2 = ?)
        LIMIT 1
    ");

    foreach ($members as $member_id) {

        $friendStmt->bind_param(
            "iiii",
            $user_id,
            $member_id,
            $member_id,
            $user_id
        );

        $friendStmt->execute();

        $friendResult = $friendStmt->get_result();

        /*
        |--------------------------------------------------------------------------
        | MEMBER IS NOT A FRIEND
        |--------------------------------------------------------------------------
        */

        if ($friendResult->num_rows === 0) {

            throw new Exception(
                "You can only add confirmed friends to a group."
            );
        }
    }

    $friendStmt->close();

    /*
    |--------------------------------------------------------------------------
    | CREATE GROUP
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        INSERT INTO groups
        (
            name,
            created_by
        )
        VALUES (?, ?)
    ");

    $stmt->bind_param(
        "si",
        $name,
        $user_id
    );

    $stmt->execute();

    $group_id = $conn->insert_id;

    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | ADD GROUP CREATOR
    |--------------------------------------------------------------------------
    */

    $memberStmt = $conn->prepare("
        INSERT INTO group_members
        (
            group_id,
            user_id
        )
        VALUES (?, ?)
    ");

    $memberStmt->bind_param(
        "ii",
        $group_id,
        $user_id
    );

    $memberStmt->execute();

    /*
    |--------------------------------------------------------------------------
    | ADD CONFIRMED FRIENDS
    |--------------------------------------------------------------------------
    */

    foreach ($members as $member_id) {

        $memberStmt->bind_param(
            "ii",
            $group_id,
            $member_id
        );

        $memberStmt->execute();
    }

    $memberStmt->close();

    /*
    |--------------------------------------------------------------------------
    | COMMIT
    |--------------------------------------------------------------------------
    */

    $conn->commit();

    echo json_encode([
        "success" => true,
        "group_id" => $group_id
    ]);

} catch (Exception $e) {

    /*
    |--------------------------------------------------------------------------
    | ROLLBACK
    |--------------------------------------------------------------------------
    */

    $conn->rollback();

    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}

$conn->close();

?>