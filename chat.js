const socket = io("http://localhost:3000");

// Join Socket.IO room using logged-in user id
socket.emit("join", currentUserId);

// ================================
// DOM ELEMENTS
// ================================

const chatBox = document.getElementById("chatBox");
const messageInput = document.getElementById("messageInput");
const sendBtn = document.getElementById("sendBtn");
const emojiBtn = document.getElementById("emojiBtn");
const uploadBtn = document.getElementById("uploadBtn");
const fileInput = document.getElementById("fileInput");

console.log("Frontend loaded");
// ================================
// UPDATE ONLINE STATUS
// ================================

function updateLastSeen() {
  fetch("api/update_last_seen.php", {
    method: "POST",
  })
    .then((res) => res.json())
    .then((data) => {
      if (!data.success) {
        console.error("LAST SEEN ERROR:", data.error);
      }
    })
    .catch((err) => {
      console.error("LAST SEEN REQUEST ERROR:", err);
    });
}

updateLastSeen();

setInterval(updateLastSeen, 30000);

// ================================
// CURRENT CHAT STATE
// ================================

// Private Chat
let currentReceiverId = null;

// Group Chat
let currentGroupId = null;

// ================================
// EMOJI PICKER
// ================================

window.addEventListener("load", () => {
  const wrapper = document.getElementById("emojiWrapper");
  const picker = document.getElementById("emojiPicker");

  if (!picker) return;

  emojiBtn.addEventListener("click", (e) => {
    e.stopPropagation();

    wrapper.classList.toggle("show");
  });

  picker.addEventListener("emoji-click", (event) => {
    messageInput.value += event.detail.unicode;

    messageInput.focus();
  });

  document.addEventListener("click", (e) => {
    if (!wrapper.contains(e.target) && !emojiBtn.contains(e.target)) {
      wrapper.classList.remove("show");
    }
  });
});

// ==========================================================================
// LOAD PRIVATE MESSAGES
// ==========================================================================

function loadMessages() {
  if (currentReceiverId === null) {
    return;
  }

  fetch(
    "api/fetch_message.php?receiver_id=" +
      encodeURIComponent(currentReceiverId),
  )
    .then((res) => res.json())

    .then((data) => {
      console.log("FETCH DATA:", data);

      // Backend rejected the request
      if (!Array.isArray(data)) {
        chatBox.innerHTML = "";

        if (data.error) {
          console.error(data.error);
        }

        return;
      }

      chatBox.innerHTML = "";

      data.forEach(renderMessage);

      chatBox.scrollTop = chatBox.scrollHeight;
    })

    .catch((err) => {
      console.error("FETCH ERROR:", err);
    });
}

// ==========================================================================
// RENDER PRIVATE MESSAGE
// ==========================================================================

function renderMessage(msg) {
  const div = document.createElement("div");

  div.classList.add("message");

  div.classList.add(
    parseInt(msg.sender_id) === parseInt(currentUserId)
      ? "outgoing"
      : "incoming",
  );

  const content = document.createElement("div");

  content.classList.add("message-content");

  // ==========================
  // TEXT
  // ==========================

  if (msg.message_type === "text") {
    const p = document.createElement("p");

    p.innerText = msg.message;

    content.appendChild(p);
  }

  // ==========================
  // IMAGE
  // ==========================
  else if (msg.message_type === "image") {
    const img = document.createElement("img");

    img.src = msg.file_path;

    img.style.maxWidth = "200px";

    img.style.borderRadius = "10px";

    content.appendChild(img);
  }

  // ==========================
  // DOCUMENT
  // ==========================
  else if (msg.message_type === "document") {
    const a = document.createElement("a");

    a.href = msg.file_path;

    a.target = "_blank";

    a.rel = "noopener noreferrer";

    a.innerText = "📄 Download File";

    content.appendChild(a);
  }

  // ==========================
  // SHARED POST
  // ==========================
  else if (msg.message_type === "post") {
    const card = document.createElement("div");

    card.className = "shared-post-card";

    card.innerHTML = `
            <strong>📢 Shared Post</strong>

            ${
              msg.shared_image
                ? `
                        <img
                            src="${msg.shared_image}"
                            class="shared-post-image"
                        >
                    `
                : ""
            }

            <p>
                ${msg.post_text || ""}
            </p>

            <button
                class="open-post-btn"
                onclick="openSharedPost(${msg.shared_post_id})"
            >
                Open Post
            </button>
        `;

    content.appendChild(card);
  }

  // ==========================
  // TIME
  // ==========================

  const time = document.createElement("div");

  time.classList.add("time");

  time.innerText = msg.created_at;

  // Add content ONCE
  div.appendChild(content);

  div.appendChild(time);

  // Add message ONCE
  chatBox.appendChild(div);
}

// ==========================================================================
// SEND MESSAGE
// ==========================================================================

sendBtn.addEventListener("click", sendMessage);

messageInput.addEventListener("keypress", function (e) {
  if (e.key === "Enter") {
    sendMessage();
  }
});

function sendMessage() {
  const message = messageInput.value.trim();

  if (message === "") {
    return;
  }

  // ======================================================================
  // GROUP CHAT
  // ======================================================================

  if (currentGroupId !== null) {
    fetch("api/send_group_message.php", {
      method: "POST",

      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },

      body:
        "group_id=" +
        encodeURIComponent(currentGroupId) +
        "&message=" +
        encodeURIComponent(message),
    })
      .then((res) => res.json())

      .then((data) => {
        console.log("GROUP MESSAGE:", data);

        if (!data.success) {
          alert(data.error || "Unable to send group message.");

          return;
        }

        messageInput.value = "";

        loadGroupMessages();

        socket.emit("group_message", {
          group_id: currentGroupId,
        });
      })

      .catch((err) => {
        console.error("GROUP MESSAGE ERROR:", err);
      });

    return;
  }

  // ======================================================================
  // PRIVATE CHAT
  // ======================================================================

  if (currentReceiverId === null) {
    alert("Please select a friend or group.");

    return;
  }

  fetch("api/save_message.php", {
    method: "POST",

    headers: {
      "Content-Type": "application/json",
    },

    body: JSON.stringify({
      receiver_id: currentReceiverId,

      message: message,

      message_type: "text",
    }),
  })
    .then((res) => res.json())

    .then((data) => {
      console.log("PRIVATE MESSAGE:", data);

      // IMPORTANT:
      // Backend friendship check is authoritative.
      if (!data.success) {
        alert(data.error || "You can only message confirmed friends.");

        return;
      }

      messageInput.value = "";

      loadMessages();

      socket.emit("new_message", {
        sender_id: currentUserId,

        receiver_id: currentReceiverId,
      });
    })

    .catch((err) => {
      console.error("PRIVATE MESSAGE ERROR:", err);
    });
}

// ==========================================================================
// SOCKET.IO - PRIVATE MESSAGE
// ==========================================================================

socket.on("new_message", (data) => {
  if (
    currentReceiverId !== null &&
    parseInt(data.receiver_id) === parseInt(currentReceiverId)
  ) {
    loadMessages();
  }
});

// ==========================================================================
// SOCKET.IO - GROUP MESSAGE
// ==========================================================================

socket.on("group_message", (data) => {
  if (
    currentGroupId !== null &&
    parseInt(data.group_id) === parseInt(currentGroupId)
  ) {
    loadGroupMessages();
  }
});
// ================================
// ONLINE STATUS
// ================================

function isUserOnline(lastSeen) {
  if (!lastSeen) {
    return false;
  }

  const normalized = lastSeen.replace(" ", "T");
  const lastSeenTime = new Date(normalized).getTime();

  if (isNaN(lastSeenTime)) {
    console.error("Invalid last_seen:", lastSeen);
    return false;
  }

  const difference = Date.now() - lastSeenTime;

  console.log({
    lastSeen: lastSeen,
    difference: difference,
    seconds: Math.round(difference / 1000),
  });

  return difference >= 0 && difference <= 60000;
}
// ==========================================================================
// PRIVATE CHATS
// ==========================================================================

function loadPrivateChats() {
  fetch("api/get_users.php")
    .then((res) => res.json())

    .then((users) => {
      const privateChats = document.getElementById("privateChats");

      privateChats.innerHTML = "";

      // Backend error
      if (!Array.isArray(users)) {
        if (users.error) {
          privateChats.innerHTML = `<p class="empty-groups">${users.error}</p>`;
        }

        return;
      }

      // No friends
      if (users.length === 0) {
        privateChats.innerHTML = `<p class="empty-groups">
                        No Friends Yet
                    </p>`;

        return;
      }

      // Friends only
      users.forEach((user) => {
        const div = document.createElement("div");

        div.className = "chat-user";

        div.dataset.id = user.id;

        div.dataset.name = user.full_name;

        const online = isUserOnline(user.last_seen);

        div.innerHTML = `
  <div class="chat-user-info">
    <strong>${user.full_name}</strong>

    <span class="user-status ${online ? "online" : "offline"}">
      <span class="status-dot"></span>
      ${online ? "Online" : "Offline"}
    </span>
  </div>
`;

        div.onclick = function () {
          // Leave group mode
          currentGroupId = null;

          // Enter private chat
          currentReceiverId = user.id;

          document.getElementById("chatTitle").innerText = user.full_name;

          document.getElementById("groupMembers").innerHTML = "";

          loadMessages();
        };

        privateChats.appendChild(div);
      });
    })

    .catch((err) => {
      console.error("PRIVATE CHAT LIST ERROR:", err);
    });
}

// ==========================================================================
// LOAD GROUPS
// ==========================================================================

function loadGroups() {
  fetch("api/fetch_groups.php")
    .then((res) => res.json())

    .then((groups) => {
      const groupList = document.getElementById("groupList");

      groupList.innerHTML = "";

      if (!Array.isArray(groups)) {
        if (groups.error) {
          groupList.innerHTML = `<p class="empty-groups">
                            ${groups.error}
                        </p>`;
        }

        return;
      }

      if (groups.length === 0) {
        groupList.innerHTML = `<p class="empty-groups">
                        No Groups Yet
                    </p>`;

        return;
      }

      groups.forEach((group) => {
        const div = document.createElement("div");

        div.className = "group-item";

        div.dataset.id = group.id;

        div.innerHTML = "👥 " + group.name;

        div.onclick = function () {
          // Leave private chat
          currentReceiverId = null;

          // Enter group chat
          currentGroupId = group.id;

          socket.emit("join_group", currentGroupId);

          document.getElementById("chatTitle").innerText = group.name;

          loadGroupMembers();

          loadGroupMessages();
        };

        groupList.appendChild(div);
      });
    })

    .catch((err) => {
      console.error("GROUP LIST ERROR:", err);
    });
}

// ==========================================================================
// LOAD GROUP MEMBERS
// ==========================================================================

function loadGroupMembers() {
  if (currentGroupId === null) {
    return;
  }

  fetch(
    "api/get_group_members.php?group_id=" + encodeURIComponent(currentGroupId),
  )
    .then((res) => res.json())

    .then((members) => {
      const container = document.getElementById("groupMembers");

      container.innerHTML = "";

      // Backend rejected access
      if (!Array.isArray(members)) {
        if (members.error) {
          container.innerHTML = `<span class="group-member">
                            ${members.error}
                        </span>`;
        }

        return;
      }

      members.forEach((member) => {
        container.innerHTML += `
                    <span class="group-member">
                        ${member.full_name}
                    </span>
                `;
      });
    })

    .catch((err) => {
      console.error("GROUP MEMBERS ERROR:", err);
    });
}

// ==========================================================================
// LOAD GROUP MESSAGES
// ==========================================================================

function loadGroupMessages() {
  if (currentGroupId === null) {
    return;
  }

  fetch(
    "api/fetch_group_messages.php?group_id=" +
      encodeURIComponent(currentGroupId),
  )
    .then((res) => res.json())

    .then((messages) => {
      // Backend rejected access
      if (!Array.isArray(messages)) {
        console.error(messages.error || "Unable to load group messages.");

        return;
      }

      chatBox.innerHTML = "";

      messages.forEach((msg) => {
        const div = document.createElement("div");

        div.className = "message";

        if (parseInt(msg.user_id) === parseInt(currentUserId)) {
          div.classList.add("outgoing");
        } else {
          div.classList.add("incoming");
        }

        let content = `
                    <strong>
                        ${msg.full_name}
                    </strong>
                    <br>
                `;

        // ==========================
        // FILE
        // ==========================

        if (msg.file) {
          const ext = msg.file.split(".").pop().toLowerCase();

          if (["jpg", "jpeg", "png", "gif", "webp"].includes(ext)) {
            content += `
                            <img
                                src="${msg.file}"
                                class="chat-image"
                            >
                        `;
          } else {
            content += `
                            <a
                                href="${msg.file}"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                📄 Download File
                            </a>
                            <br>
                        `;
          }
        }

        // ==========================
        // TEXT
        // ==========================

        if (msg.message) {
          content += `
                        <p>
                            ${msg.message}
                        </p>
                    `;
        }

        div.innerHTML = `
                    <div class="message-content">
                        ${content}
                    </div>

                    <div class="time">
                        ${msg.created_at}
                    </div>
                `;

        chatBox.appendChild(div);
      });

      chatBox.scrollTop = chatBox.scrollHeight;
    })

    .catch((err) => {
      console.error("GROUP MESSAGES ERROR:", err);
    });
}

// ==========================================================================
// FILE UPLOAD
// ==========================================================================

uploadBtn.addEventListener("click", () => {
  // Don't allow upload without a selected chat
  if (currentReceiverId === null && currentGroupId === null) {
    alert("Please select a friend or group first.");

    return;
  }

  fileInput.click();
});

fileInput.addEventListener("change", () => {
  const file = fileInput.files[0];

  if (!file) {
    return;
  }

  const formData = new FormData();

  formData.append("file", file);

  fetch("api/upload_file.php", {
    method: "POST",

    body: formData,
  })
    .then((res) => res.json())

    .then((data) => {
      if (!data.success) {
        alert(data.error || "File upload failed.");

        return null;
      }

      // ==============================================================
      // GROUP FILE
      // ==============================================================

      if (currentGroupId !== null) {
        const groupForm = new FormData();

        groupForm.append("group_id", currentGroupId);

        groupForm.append("message", "");

        groupForm.append("file", data.fileUrl);

        return fetch("api/send_group_message.php", {
          method: "POST",
          body: groupForm,
        })
          .then((res) => res.json())

          .then((result) => {
            console.log("GROUP FILE:", result);

            if (!result.success) {
              alert(result.error || "Unable to send file.");

              return;
            }

            loadGroupMessages();

            socket.emit("group_message", {
              group_id: currentGroupId,
            });
          });
      }

      // ==============================================================
      // PRIVATE FILE
      // ==============================================================

      if (currentReceiverId === null) {
        alert("Please select a friend first.");

        return null;
      }

      return fetch("api/save_message.php", {
        method: "POST",

        headers: {
          "Content-Type": "application/json",
        },

        body: JSON.stringify({
          receiver_id: currentReceiverId,

          message: data.filename,

          message_type: data.type,

          file_name: data.filename,

          file_path: data.fileUrl,
        }),
      })
        .then((res) => res.json())

        .then((result) => {
          console.log("PRIVATE FILE:", result);

          if (!result.success) {
            alert(
              result.error || "You can only send files to confirmed friends.",
            );

            return;
          }

          loadMessages();

          socket.emit("new_message", {
            sender_id: currentUserId,

            receiver_id: currentReceiverId,
          });
        });
    })

    .catch((err) => {
      console.error("FILE ERROR:", err);
    });

  fileInput.value = "";
});

// ==========================================================================
// CREATE GROUP MODAL
// ==========================================================================

const createBtn = document.getElementById("createGroupBtn");

const modal = document.getElementById("createGroupModal");

const close = document.getElementById("closeModal");

const memberList = document.getElementById("memberList");

const saveGroupBtn = document.getElementById("saveGroupBtn");

createBtn.addEventListener("click", () => {
  modal.style.display = "block";

  loadUsers();
});

close.addEventListener("click", () => {
  modal.style.display = "none";
});

window.addEventListener("click", (e) => {
  if (e.target === modal) {
    modal.style.display = "none";
  }
});

// ==========================================================================
// LOAD FRIENDS FOR GROUP CREATION
// ==========================================================================

function loadUsers() {
  fetch("api/get_users.php")
    .then((res) => res.json())

    .then((users) => {
      memberList.innerHTML = "";

      // Backend error
      if (!Array.isArray(users)) {
        memberList.innerHTML = `<p>${users.error || "Unable to load friends."}</p>`;

        return;
      }

      // No friends
      if (users.length === 0) {
        memberList.innerHTML = `<p>No friends available.</p>`;

        return;
      }

      // get_users.php now returns CONFIRMED FRIENDS ONLY
      users.forEach((user) => {
        memberList.innerHTML += `
                    <div class="member">

                        <label>

                            <input
                                type="checkbox"
                                value="${user.id}"
                            >

                            ${user.full_name}

                        </label>

                    </div>
                `;
      });
    })

    .catch((err) => {
      console.error("FRIENDS LOAD ERROR:", err);
    });
}

// ==========================================================================
// CREATE GROUP
// ==========================================================================

saveGroupBtn.addEventListener("click", () => {
  const name = document.getElementById("groupName").value.trim();

  if (name === "") {
    alert("Please enter a group name.");

    return;
  }

  const members = [];

  document.querySelectorAll("#memberList input:checked").forEach((cb) => {
    members.push(cb.value);
  });

  if (members.length === 0) {
    alert("Please select at least one friend.");

    return;
  }

  const form = new FormData();

  form.append("name", name);

  members.forEach((id) => {
    form.append("members[]", id);
  });

  saveGroupBtn.disabled = true;

  fetch("api/create_group.php", {
    method: "POST",

    body: form,
  })
    .then((res) => res.json())

    .then((data) => {
      console.log("CREATE GROUP:", data);

      if (!data.success) {
        alert(data.error || "Unable to create group.");

        return;
      }

      alert("Group Created!");

      modal.style.display = "none";

      document.getElementById("groupName").value = "";

      loadGroups();
    })

    .catch((err) => {
      console.error("CREATE GROUP ERROR:", err);

      alert("An error occurred while creating the group.");
    })

    .finally(() => {
      saveGroupBtn.disabled = false;
    });
});

// ==========================================================================
// SHARED POST
// ==========================================================================

function openSharedPost(postId) {
  window.location.href = "feed.php#post-" + postId;
}

// ==========================================================================
// INITIAL LOAD
// ==========================================================================

loadPrivateChats();

loadGroups();
