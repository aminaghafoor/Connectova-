<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <title>Group Chat</title>

    <link rel="stylesheet" href="assets/css/group_chat.css">

</head>

<body>

<div class="group-chat-container">

    <div class="chat-header">
        <h2>Group Chat</h2>
    </div>

    <div id="chatBox" class="chat-box">

    </div>

    <div class="message-area">

        <input
            type="text"
            id="messageInput"
            placeholder="Type a message..."
        >

        <button id="sendBtn">
            Send
        </button>

    </div>

</div>

<script>

const currentGroupId = <?php echo $group_id; ?>;

</script>

<script src="assets/js/group_chat.js"></script>

</body>

</html>