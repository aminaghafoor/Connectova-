const friendBtn = document.getElementById("friendBtn");

if (friendBtn) {
  const userId = friendBtn.dataset.userId;

  fetch("api/get_friend_status.php?user_id=" + userId)
    .then((response) => response.json())

    .then((data) => {
      if (!data.success) {
        return;
      }

      updateFriendButton(data.status);
    })

    .catch((error) => {
      console.log("Friend status error:", error);
    });
}

function updateFriendButton(status) {
  if (!friendBtn) {
    return;
  }

  friendBtn.dataset.status = status;

  if (status === "none") {
    friendBtn.innerText = "Add Friend";

    friendBtn.disabled = false;
  } else if (status === "sent") {
    friendBtn.innerText = "Request Sent";

    friendBtn.disabled = true;
  } else if (status === "received") {
    friendBtn.innerText = "Accept Request";

    friendBtn.disabled = false;
  } else if (status === "friends") {
    friendBtn.innerText = "Friends";

    friendBtn.disabled = true;
  }
}
