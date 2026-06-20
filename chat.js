const socket = io("http://localhost:3000");

const chatBox = document.getElementById("chatBox");
const messageInput = document.getElementById("messageInput");
const sendBtn = document.getElementById("sendBtn");
const emojiBtn = document.getElementById("emojiBtn");
const uploadBtn = document.getElementById("uploadBtn");
const fileInput = document.getElementById("fileInput");

console.log("Frontend loaded");

/*
|--------------------------------------------------------------------------
| CURRENT CHAT STATE
|--------------------------------------------------------------------------
| receiver = selected user (from user list)
| sender = backend session (PHP)
|--------------------------------------------------------------------------
*/

let currentReceiverId = 1; // TEMP (replace when user list exists)
let currentUserId = null; // will be set later from server (optional)

// emojis picker logic
window.addEventListener("load", () => {
  const wrapper = document.getElementById("emojiWrapper");
  const picker = document.getElementById("emojiPicker");
  const input = document.getElementById("messageInput");

  if (!picker) {
    console.error("Emoji picker not loaded");
    return;
  }

  emojiBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    wrapper.classList.toggle("show");
  });

  picker.addEventListener("emoji-click", (event) => {
    input.value += event.detail.unicode;
    input.focus();
  });

  document.addEventListener("click", (e) => {
    if (!wrapper.contains(e.target) && !emojiBtn.contains(e.target)) {
      wrapper.classList.remove("show");
    }
  });
});

/*
|--------------------------------------------------------------------------
| LOAD MESSAGES
|--------------------------------------------------------------------------
*/

function loadMessages() {
  fetch(`api/fetch_message.php?receiver_id=${currentReceiverId}`)
    .then((res) => res.json())
    .then((data) => {
      console.log("FETCH DATA:", data);

      chatBox.innerHTML = "";

      if (!Array.isArray(data)) return;

      data.forEach(renderMessage);

      chatBox.scrollTop = chatBox.scrollHeight;
    })
    .catch((err) => console.error("FETCH ERROR:", err));
}

/*
|--------------------------------------------------------------------------
| RENDER MESSAGE
|--------------------------------------------------------------------------
*/

function renderMessage(msg) {
  const div = document.createElement("div");
  div.classList.add("message");

  // IMPORTANT: dynamic sender check (NOT hardcoded 2)
  div.classList.add(msg.sender_id == currentUserId ? "outgoing" : "incoming");

  const content = document.createElement("div");
  content.classList.add("message-content");

  if (msg.message_type === "text") {
    const p = document.createElement("p");
    p.innerText = msg.message;
    content.appendChild(p);
  } else if (msg.message_type === "image") {
    const img = document.createElement("img");
    img.src = msg.file_path;
    img.style.maxWidth = "200px";
    img.style.borderRadius = "10px";
    content.appendChild(img);
  } else if (msg.message_type === "document") {
    const a = document.createElement("a");
    a.href = msg.file_path;
    a.target = "_blank";
    a.innerText = "📄 Download File";
    content.appendChild(a);
  }

  div.appendChild(content);
  chatBox.appendChild(div);
}

/*
|--------------------------------------------------------------------------
| SEND MESSAGE (TEXT)
|--------------------------------------------------------------------------
*/

sendBtn.addEventListener("click", sendMessage);

messageInput.addEventListener("keypress", (e) => {
  if (e.key === "Enter") sendMessage();
});

function sendMessage() {

 console.log("Send button clicked");

  const message = messageInput.value.trim();
  console.log("Message:", message);

  if (!message) return;

 
  if (!message) return;

  console.log("SENDING MESSAGE:", message);
  console.log("RECEIVER:", currentReceiverId);

  fetch("api/save_message.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      message: message,
      receiver_id: currentReceiverId,
      message_type: "text",
    }),
  })
    .then((res) => res.text()) // IMPORTANT CHANGE
    .then((text) => {
      console.log("RAW SAVE RESPONSE:", text);

      let data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        console.error("Invalid JSON from PHP:", text);
        return;
      }

      console.log("DB RESPONSE:", data);

      if (!data.success) {
        alert(data.error);
        return;
      }

      loadMessages();
    })

    .catch((err) => console.error("FETCH ERROR:", err));

  messageInput.value = "";
  // REAL-TIME SOCKET EMIT
  socket.emit("new_message", {
    message,
    receiver_id: currentReceiverId,
  });
}

/*
|--------------------------------------------------------------------------
| FILE UPLOAD (IMAGE / DOC)
|--------------------------------------------------------------------------
*/

uploadBtn.addEventListener("click", () => fileInput.click());

fileInput.addEventListener("change", () => {
  const file = fileInput.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append("file", file);

  fetch("api/upload_file.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (!data.success) return;

      return fetch("api/save_message.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          message: data.filename,
          receiver_id: currentReceiverId,
          message_type: data.type,
          file_name: data.filename,
          file_path: data.fileUrl,
        }),
      });
    })
    .then((res) => res?.json())
    .then((result) => {
      console.log("FILE SAVED:", result);
      loadMessages();

      socket.emit("new_message", {
        receiver_id: currentReceiverId,
      });
    })
    .catch((err) => console.error("UPLOAD ERROR:", err));

  fileInput.value = "";
});

/*
|--------------------------------------------------------------------------
| SOCKET REAL-TIME RECEIVE
|--------------------------------------------------------------------------
*/

socket.on("new_message", (data) => {
  loadMessages();
});

/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

loadMessages();
