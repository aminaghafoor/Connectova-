<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "connectova");

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT full_name, username, profile_pic, last_seen
FROM users
WHERE id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$currentUser = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Connectova Chat</title>

    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="chat.css">

</head>

<body>
    <?php include 'includes/navbar.php'; ?>


<!-- =========================
        CHAT LAYOUT
========================= -->

<div class="chat-layout">

    <!-- =========================
            LEFT SIDEBAR
    ========================== -->

    <aside class="chat-sidebar">

        <div class="sidebar-profile-card">

            <img
                src="uploads/images/<?php echo htmlspecialchars($currentUser['profile_pic']); ?>"
                alt="Profile"
                class="sidebar-profile-pic"
            >

            <h3>
                <?php echo htmlspecialchars($currentUser['full_name']); ?>
            </h3>

            <p>
                @<?php echo htmlspecialchars($currentUser['username']); ?>
            </p>

  

        </div>

        <!-- PRIVATE CHATS -->

        <div class="sidebar-section">

            <div class="section-title">

                <h3>Private Chats</h3>

            </div>

            <div id="privateChats">

                <!-- Loaded by JS -->

            </div>

        </div>

        <!-- GROUPS -->

        <div class="sidebar-section">

            <div class="group-header">

                <h3>Groups</h3>

                <button
                    id="createGroupBtn"
                    type="button">

                    + Create

                </button>

            </div>

            <div id="groupList">

                <p class="empty-groups">

                    No Groups Yet

                </p>

            </div>

        </div>

    </aside>

    <!-- =========================
            CHAT AREA
    ========================== -->

    <main class="chat-container">

        <div class="chat-header">


            <div>

                <h2 id="chatTitle">

                    Connectova Chat

                </h2>

                        <div id="userStatus"></div>

                        
                <div id="groupMembers"></div>

            </div>

        </div>

        <!-- MESSAGES -->

        <div id="chatBox">

            <!-- Loaded by JS -->

        </div>

        <!-- INPUT -->

        <div class="input-area">

            <input

                type="text"

                id="messageInput"

                class="message-input"

                placeholder="Type a message..."

            >

            <!-- Upload -->

            <button

                id="uploadBtn"

                type="button"

                class="upload-btn"

            >

                📎

            </button>

            <input

                type="file"

                id="fileInput"

                accept="image/*,.pdf,.doc,.docx,.txt"

                hidden

            >

            <!-- Emoji -->

            <div class="emoji-container">

                <button

                    id="emojiBtn"

                    type="button"

                    class="emoji-btn"

                >

                    😊

                </button>

                <div

                    id="emojiWrapper"

                    class="emoji-wrapper"

                >

                    <emoji-picker id="emojiPicker"></emoji-picker>

                </div>

            </div>

            <!-- Send -->

            <button

                id="sendBtn"

                class="send-btn"

                type="button"

            >

                <i class="fa-solid fa-paper-plane"></i>

                Send

            </button>

        </div>

    </main>

</div>

<!-- =========================
        CREATE GROUP MODAL
========================= -->

<div id="createGroupModal" class="modal">

    <div class="modal-content">

        <span
            id="closeModal"
            class="close">

            &times;

        </span>

        <h2>Create Group</h2>

        <input

            type="text"

            id="groupName"

            placeholder="Enter Group Name"

        >

        <h3>Select Members</h3>

        <div id="memberList">

            Loading...

        </div>

        <button

            id="saveGroupBtn"

            type="button">

            Create Group

        </button>

    </div>

</div>

<!-- SOCKET.IO -->

<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>

<!-- EMOJI PICKER -->

<script
type="module"
src="https://cdn.jsdelivr.net/npm/emoji-picker-element@latest/index.js">
</script>

<script>
const currentUserId = <?= $_SESSION['user_id']; ?>;
</script>

<script src="chat.js"></script>

<script src="assets/js/status.js"></script>
</body>
</html>