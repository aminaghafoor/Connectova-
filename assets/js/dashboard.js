// =====================================
// CONNECTOVA DASHBOARD
// assets/js/dashboard.js
// =====================================

// Load statistics
fetch("api/get_dashboard_stats.php")
  .then((response) => response.json())
  .then((data) => {
    if (data.success) {
      document.getElementById("totalPosts").innerText = data.posts;
      document.getElementById("totalLikes").innerText = data.likes;
      document.getElementById("totalComments").innerText = data.comments;
      document.getElementById("totalFriends").innerText = data.friends;
    }
  });

const dashboardPosts = document.getElementById("dashboardPosts");

let currentSort = "new";

// ===========================
// Load My Posts
// ===========================

function loadMyPosts() {
  fetch("api/get_my_posts.php?sort=" + currentSort)
    .then((response) => response.json())
    .then((data) => {
      dashboardPosts.innerHTML = "";

      if (!data.success || data.posts.length === 0) {
        dashboardPosts.innerHTML = `
            <div class="dashboard-empty">
                <h3>No posts yet.</h3>
            </div>
        `;
        return;
      }

      data.posts.forEach((post) => {
        dashboardPosts.innerHTML += createDashboardCard(post);
      });

      searchPosts();
    });
}

// ===========================
// Dashboard Card
// ===========================

function createDashboardCard(post) {
  let mediaHTML = "";

  if (post.media) {
    post.media.forEach((item) => {
      if (item.media_type === "image") {
        mediaHTML += `
            <img
            src="${item.file_path}"
            class="dashboard-media">
        `;
      } else if (item.media_type === "video") {
        mediaHTML += `
            <video
            controls
            class="dashboard-media">

                <source src="${item.file_path}">

            </video>
        `;
      }
    });
  }

  return `

<div class="dashboard-card">

<div class="dashboard-date">

${post.created_at}

</div>

<div
class="dashboard-text"
id="post-text-${post.id}">

${post.post_text ?? ""}

</div>

${mediaHTML}

<div class="dashboard-stats">

<span>❤️ ${post.likes_count}</span>

<span>💬 ${post.comments_count}</span>

</div>

<div class="dashboard-actions">

<button
class="edit-btn"
onclick="editPost(${post.id})">

✏ Edit

</button>

<button
class="delete-btn"
onclick="deletePost(${post.id})">

🗑 Delete

</button>

</div>

</div>

`;
}

// ===========================
// Search
// ===========================

function searchPosts() {
  const keyword = document.getElementById("searchPost").value.toLowerCase();

  document.querySelectorAll(".dashboard-card").forEach((card) => {
    card.style.display = card.innerText.toLowerCase().includes(keyword)
      ? "block"
      : "none";
  });
}

document.getElementById("searchPost").addEventListener("input", searchPosts);

// ===========================
// Sort
// ===========================

document.getElementById("sortPosts").addEventListener("change", function () {
  currentSort = this.value;
  loadMyPosts();
});

// ===========================
// Delete
// ===========================

function deletePost(id) {
  if (!confirm("Delete this post?")) return;

  const form = new FormData();

  form.append("post_id", id);

  fetch("api/delete_post.php", {
    method: "POST",
    body: form,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        loadMyPosts();

        location.reload();
      }
    });
}

// ===========================
// Edit
// ===========================

function editPost(id) {
  const text = document.getElementById("post-text-" + id).innerText;

  const newText = prompt("Edit your post", text);

  if (newText === null) return;

  const form = new FormData();

  form.append("post_id", id);

  form.append("post_text", newText);

  fetch("api/edit_post.php", {
    method: "POST",
    body: form,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        loadMyPosts();
      }
    });
}

// Initial Load
loadMyPosts();
