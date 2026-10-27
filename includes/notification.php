<?php

function createNotification(
    $conn,
    $user_id,
    $sender_id,
    $type,
    $reference_id,
    $message
) {
    // Do not notify yourself
    if ((int)$user_id === (int)$sender_id) {
        return;
    }

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO notifications
        (user_id, sender_id, type, reference_id, message)
        VALUES (?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "iisis",
        $user_id,
        $sender_id,
        $type,
        $reference_id,
        $message
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
}