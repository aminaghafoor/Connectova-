const container = document.getElementById("friendsContainer");

fetch("api/get_friends.php")
  .then((r) => r.json())

  .then((data) => {
    container.innerHTML = "";

    if (!data.success) {
      container.innerHTML = `
        <h3>
        Unable to load friends.
        </h3>
        `;

      return;
    }

    if (data.friends.length === 0) {
      container.innerHTML = `
        <h3>
        No friends yet.
        </h3>
        `;

      return;
    }

    data.friends.forEach((friend) => {
      container.innerHTML += `


<div class="friend-card">


    <img

    src="uploads/images/${friend.profile_pic || "default.png"}"

    class="friend-image"

    >



    <div class="friend-info">


        <h3>

        ${friend.full_name}

        </h3>


        <p>

        @${friend.username}

        </p>


    </div>



    <div class="friend-actions">


        <button

        class="message-btn"

        onclick="location.href='chat.php?user=${friend.id}'">

        💬 Message

        </button>



        <button

        class="remove-btn"

        onclick="removeFriend(${friend.id},this)">

        ❌ Remove

        </button>


    </div>


</div>


`;
    });
  })

  .catch((error) => {
    console.log("Friends Error:", error);

    container.innerHTML = `
    <h3>
    Error loading friends.
    </h3>
    `;
  });

// Remove Friend

function removeFriend(friendId, btn) {
  if (!confirm("Remove this friend?")) {
    return;
  }

  const form = new FormData();

  form.append("friend_id", friendId);

  fetch("api/remove_friend.php", {
    method: "POST",

    body: form,
  })
    .then((r) => r.json())

    .then((data) => {
      if (data.success) {
        btn.closest(".friend-card").remove();
      } else {
        alert(data.message || "Failed to remove friend");
      }
    })

    .catch((error) => {
      console.log(error);
    });
}
