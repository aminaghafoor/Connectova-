const container = document.getElementById("peopleContainer");

fetch("api/get_people.php")
  .then((r) => r.json())
  .then((data) => {
    data.people.forEach((user) => {
      let button = "";

      switch (user.relationship) {
        case "friends":
          button = `

<button
class="friends-btn"
disabled>

👥 Friends

</button>

`;

          break;

        case "sent":
          button = `

<button
class="sent-btn"
disabled>

⏳ Request Sent

</button>

`;

          break;

        case "received":
          button = `

<button
class="accept-btn"
onclick="acceptFriend(${user.id},this)">

✅ Accept

</button>

`;

          break;

        default:
          button = `

<button
class="add-friend-btn"
onclick="addFriend(${user.id},this)">

➕ Add Friend

</button>

`;
      }

      container.innerHTML += `

<div class="person-card">

<img
src="uploads/images/${user.profile_pic}"
class="person-image">

<h3>${user.full_name}</h3>

<p>@${user.username}</p>

${button}

</div>

`;
    });
  });

function addFriend(id, btn) {
  const form = new FormData();

  form.append("receiver_id", id);

  fetch("api/send_friend_request.php", {
    method: "POST",

    body: form,
  })
    .then((r) => r.json())

    .then((data) => {
      if (data.success) {
        btn.innerHTML = "✓ Request Sent";

        btn.disabled = true;
      } else {
        alert(data.message);
      }
    });
}
