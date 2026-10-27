const container = document.getElementById("requestContainer");

loadRequests();

function loadRequests() {
  fetch("api/get_friend_requests.php")
    .then((r) => r.json())

    .then((data) => {
      container.innerHTML = "";

      if (data.requests.length === 0) {
        container.innerHTML = "<h3>No friend requests.</h3>";

        return;
      }

      data.requests.forEach((user) => {
        container.innerHTML += `

<div class="request-card">

<div class="request-left">

<img
src="uploads/images/${user.profile_pic}"
class="request-image">

<div>

<h3>${user.full_name}</h3>

<p>@${user.username}</p>

</div>

</div>

<div class="request-actions">

<button
class="accept-btn"
onclick="acceptRequest(${user.request_id})"

Accept

</button>

<button
class="reject-btn"
onclick="rejectRequest(${user.request_id})"

Reject

</button>

</div>

</div>

`;
      });
    });
}
// function to accept a friend request

function acceptRequest(id) {
  const form = new FormData();

  form.append("request_id", id);

  fetch("api/accept_request.php", {
    method: "POST",

    body: form,
  })
    .then((r) => r.json())

    .then(() => {
      loadRequests();
    });
}

function rejectRequest(id) {
  const form = new FormData();

  form.append("request_id", id);

  fetch("api/reject_request.php", {
    method: "POST",

    body: form,
  })
    .then((r) => r.json())

    .then(() => {
      loadRequests();
    });
}
