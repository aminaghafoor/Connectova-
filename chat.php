<!DOCTYPE html>
<html>
<head>
    <title>Connectova Chat</title>
    <link rel="stylesheet" href="chat.css">
</head>

<body>

<nav>
    <a href="index.php">Home</a>
    <a href="register.php">Register</a>
    <a href="login.php">Login</a>
    <a href="profile.php">Profile</a>
    <a href="chat.php">Chat</a>
</nav>
<div class="chat-container">

    <h2>Connectova Chat</h2>

    <div id="chatBox"></div>

    <div class="input-area">

        <input
            type="text"
            id="messageInput"
            class="message-input"
            placeholder="Type message"
        >
<button id="uploadBtn">Upload</button>
<input
  type="file"
  id="fileInput"
  accept="image/*,.pdf,.doc,.docx,.txt"
  hidden
/>

        <!-- Emoji Section -->
      <div class="emoji-container">

    <button
        type="button"
        id="emojiBtn"
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
        </div>

        <button
            type="button"
            id="sendBtn"
            class="send-btn"
        >
            Send
        </button>

    </div>

</div>

<!-- Socket.IO -->
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>

<!-- Emoji Picker -->
<script
type="module"
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@latest/index.js"></script>


<script src="chat.js"></script>

</body>
</html>