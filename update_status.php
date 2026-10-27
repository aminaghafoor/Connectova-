<?php

session_start();
include 'config.php';

if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE users SET last_seen = NOW() WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
}

http_response_code(204);
exit();
?>