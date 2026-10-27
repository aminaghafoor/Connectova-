// const chatBox = document.getElementById("chatBox");
// const messageInput = document.getElementById("messageInput");
// const sendBtn = document.getElementById("sendBtn");

// function loadGroupMessages(groupId) {
//   fetch("api/fetch_group_messages.php?group_id=" + groupId)
//     .then((response) => response.json())

//     .then((messages) => {
//       chatBox.innerHTML = "";

//       messages.forEach((message) => {
//         let messageType = "";

//           Number(message.user_id) === Number(currentUserId),
//         );

//         if (message.user_id == currentUserId) {
//           messageType = "sent";
//         } else {
//           messageType = "received";
//         }

//         chatBox.innerHTML += `

//         console.log(messageType);

// <div class="message ${messageType}">

//     <div class="sender">
//         ${message.full_name}
//     </div>

//     <div class="text">
//         ${message.message}
//     </div>

//     <div class="time">
//         ${message.created_at}
//     </div>

// </div>

// `;
//       });

//       chatBox.scrollTop = chatBox.scrollHeight;
//     });
// }
// function sendMessage() {
//   const message = messageInput.value.trim();

//   if (message === "") return;

//   fetch("api/send_group_message.php", {
//     method: "POST",

//     headers: {
//       "Content-Type": "application/x-www-form-urlencoded",
//     },

//     body:
//       "group_id=" + currentGroupId + "&message=" + encodeURIComponent(message),
//   })
//     .then((response) => response.json())

//     .then((result) => {
//       messageInput.value = "";

//       loadGroupMessages(currentGroupId);
//     });
// }

// sendBtn.addEventListener("click", sendMessage);

// messageInput.addEventListener("keypress", function (event) {
//   if (event.key === "Enter") {
//     sendMessage();
//   }
// });

// loadGroupMessages(currentGroupId);

// setInterval(function () {
//   loadGroupMessages(currentGroupId);
// }, 2000);
