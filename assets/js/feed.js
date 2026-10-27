// =====================================
// CONNECTOVA - FEED JAVASCRIPT
// assets/js/feed.js
// =====================================

let offset = 0;

const limit = 5;

const feedContainer = document.getElementById("feedContainer");

const loadMoreBtn = document.getElementById("loadMoreBtn");

// ==============================
// Share Modal Elements
// ==============================

const shareModal = document.getElementById("shareModal");

const closeShareModal = document.getElementById("closeShareModal");

// =====================================
// Load Posts
// =====================================

function loadPosts(reset = false) {
  if (reset) {
    offset = 0;

    feedContainer.innerHTML = "";
  }

  fetch(`api/fetch_posts.php?limit=${limit}&offset=${offset}`)
    .then((response) => response.json())

    .then((data) => {
      if (data.success) {
        if (data.posts.length === 0) {
          if (offset === 0) {
            feedContainer.innerHTML = `
                    <div class="post-card">
                        <h3>
                        No posts yet.
                        </h3>
                    </div>
                    `;
          }

          loadMoreBtn.style.display = "none";

          return;
        }

        data.posts.forEach((post) => {
          feedContainer.innerHTML += createPostCard(post);

          // Load existing comments automatically
          setTimeout(() => {
            loadComments(post.id);
          }, 100);
        });

        offset += limit;
      } else {
        console.log(data.message);
      }
    })

    .catch((error) => {
      console.log("Feed Error:", error);
    });
}

// =====================================
// Create Post HTML
// =====================================

function createPostCard(post) {
  let mediaHTML = "";

  // Images and Videos

  if (post.media) {
    post.media.forEach((item) => {
      if (item.media_type === "image") {
        mediaHTML += `
        <img 
        src="${item.file_path}"
        class="post-media">
        `;
      } else if (item.media_type === "video") {
        mediaHTML += `
        <video 
        controls
        class="post-media">

        <source src="${item.file_path}">

        </video>
        `;
      }
    });
  }

  return `

<div
class="post-card"
id="post-${post.id}">


<div class="post-header">

    <img
        src="uploads/images/${post.profile_pic}"
        class="post-user-image">

    <div class="post-user-info">

        <div class="post-username">
            ${post.username}
        </div>

        <div class="post-time">
            ${post.created_at}
        </div>

        ${
          Number(post.user_id) !== Number(currentUserId)
            ? `
                <button
                    type="button"
                    class="view-profile-btn"
                    onclick="viewProfile(${post.user_id})">
                    View Profile
                </button>
              `
            : ""
        }

    </div>

</div>



<div 
class="post-text"
id="post-text-${post.id}">

${post.post_text ?? ""}

</div>



<div>

${mediaHTML}

</div>



<div class="post-actions">


<button onclick="likePost(${post.id})">

${Number(post.user_liked) === 1 ? "❤️ Liked" : "🤍 Like"}

(${post.likes_count})

</button>



<button onclick="commentPost(${post.id})">

💬 Comment

</button>



<button onclick="sharePost(${post.id})">

🔗 Share

</button>


${
  Number(post.user_id) === Number(currentUserId)
    ? `

<button onclick="editPost(${post.id})">

✏ Edit

</button>


<button onclick="deletePost(${post.id})">

🗑 Delete

</button>

`
    : ""
}


</div>


<div
id="comment-box-${post.id}"
class="comment-box"
style="display:none;">

    <div class="comment-form">

        <div class="comment-input-wrapper">

            <input
            type="text"
            id="comment-input-${post.id}"
            class="comment-input"
            placeholder="Write a comment...">

            <button
            type="button"
            class="commentEmojiBtn"
            onclick="toggleCommentEmoji(${post.id})">

                😊

            </button>

            <div
            id="commentEmojiWrapper-${post.id}"
            class="emoji-wrapper">

                <emoji-picker
                id="commentEmojiPicker-${post.id}">
                </emoji-picker>

            </div>

        </div>

        <button
        type="button"
        class="comment-post-btn"
        onclick="submitComment(${post.id})">

            <i class="fa-solid fa-paper-plane"></i>
            Post

        </button>

    </div>

    <div id="comments-${post.id}"></div>

</div>
`;
}

// =====================================
// Like Placeholder
// =====================================

function likePost(id) {
  let formData = new FormData();

  formData.append("post_id", id);

  fetch("api/like_post.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())

    .then((data) => {
      if (data.success) {
        alert(data.action === "liked" ? "Post liked ❤️" : "Post unliked");

        // Refresh feed to update like count
        loadPosts(true);
      } else {
        alert(data.message);
      }
    })

    .catch((error) => {
      console.log("Like Error:", error);
    });
}

// =====================================
// Comment System
// =====================================

// Open / Close Comment Box

function commentPost(id) {
  let box = document.getElementById("comment-box-" + id);

  if (box.style.display === "none" || box.style.display === "") {
    box.style.display = "block";

    initializeCommentPicker(id);
  } else {
    box.style.display = "none";
  }
}
// =====================================
// Comment Emoji Picker
// =====================================

function toggleCommentEmoji(postId) {
  const wrapper = document.getElementById("commentEmojiWrapper-" + postId);

  // close other emoji pickers
  document.querySelectorAll(".emoji-wrapper").forEach((w) => {
    if (w !== wrapper) {
      w.classList.remove("active");
    }
  });

  // toggle current picker
  wrapper.classList.toggle("active");
}

// Close emoji picker when clicking outside

document.addEventListener("click", function (event) {
  // If clicked emoji button, do nothing
  if (event.target.closest(".commentEmojiBtn")) {
    return;
  }

  // If clicked inside emoji picker, do nothing
  if (event.target.closest(".emoji-wrapper")) {
    return;
  }

  // Otherwise close all
  document.querySelectorAll(".emoji-wrapper").forEach((wrapper) => {
    wrapper.classList.remove("active");
  });
});

function initializeCommentPicker(postId) {
  const picker = document.getElementById("commentEmojiPicker-" + postId);

  if (!picker || picker.dataset.loaded) {
    return;
  }

  picker.dataset.loaded = "true";

  picker.addEventListener("emoji-click", function (event) {
    const input = document.getElementById("comment-input-" + postId);

    input.value += event.detail.unicode;

    input.focus();
  });
}
// Submit Comment

function submitComment(postId) {
  let input = document.getElementById("comment-input-" + postId);

  let comment = input.value.trim();

  if (comment === "") {
    alert("Please write a comment");

    return;
  }

  let formData = new FormData();

  formData.append("post_id", postId);

  formData.append("comment", comment);

  fetch("api/comment_post.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())

    .then((data) => {
      if (data.success) {
        input.value = "";

        alert("Comment added successfully");

        // Later this will load real comments
        loadComments(postId);
      } else {
        alert(data.message);
      }
    })

    .catch((error) => {
      console.log("Comment Error:", error);
    });
}

// comment fetch function

function loadComments(postId) {
  fetch("api/fetch_comments.php?post_id=" + postId)
    .then((response) => response.json())

    .then((data) => {
      if (data.success) {
        let container = document.getElementById("comments-" + postId);

        container.innerHTML = "";

        data.comments.forEach((comment) => {
          container.innerHTML += `

                <div class="single-comment">


                    <img

                    src="uploads/images/${comment.profile_pic}"

                    class="comment-profile">


                    <div>


                    <strong>

                    ${comment.username}

                    </strong>


                    <p>

                    ${comment.comment}

                    </p>


                    <small>

                    ${comment.created_at}

                    </small>


                    </div>


                </div>

                `;
        });
      }
    })

    .catch((error) => {
      console.log("Comment Load Error:", error);
    });
}
// =====================================
// Share Placeholder
// =====================================

let currentSharePost = null;

function sharePost(postId) {
  currentSharePost = postId;

  document.getElementById("shareModal").style.display = "block";

  loadShareUsers();
}

// share post function

function loadShareUsers() {
  fetch("api/get_share_users.php")
    .then((response) => response.json())

    .then((users) => {
      const container = document.getElementById("shareUserList");

      container.innerHTML = "";

      if (!Array.isArray(users) || users.length === 0) {
        container.innerHTML = `
                    <p class="no-users">
                        No users found.
                    </p>
                `;

        return;
      }

      users.forEach((user) => {
        container.innerHTML += `

                    <div class="share-user">

                        <div class="share-user-info">

                            <strong>${user.full_name}</strong>

                            <br>

                            <span>@${user.username}</span>

                        </div>

                        <button
                            type="button"
                            onclick="sendSharedPost(${user.id})">

                            Send

                        </button>

                    </div>

                `;
      });
    })

    .catch((error) => {
      console.log("Share User Error:", error);
    });
}
// =====================================
// Share Modal Close
// =====================================

window.addEventListener("load", function () {
  const shareModal = document.getElementById("shareModal");
  const closeShareModal = document.getElementById("closeShareModal");

  if (!shareModal || !closeShareModal) {
    console.log("Share modal not found.");
    return;
  }

  // Close with X
  closeShareModal.addEventListener("click", function () {
    shareModal.style.display = "none";
  });

  // Close by clicking outside
  window.addEventListener("click", function (event) {
    if (event.target === shareModal) {
      shareModal.style.display = "none";
    }
  });

  // Close with Escape
  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      shareModal.style.display = "none";
    }
  });
});
// shared post function
function sendSharedPost(receiverId) {
  let form = new FormData();

  form.append("receiver_id", receiverId);

  form.append("post_id", currentSharePost);

  fetch("api/share_post.php", {
    method: "POST",
    body: form,
  })
    .then((r) => r.json())

    .then((data) => {
      if (data.success) {
        alert("Post shared successfully!");

        document.getElementById("shareModal").style.display = "none";
      } else {
        alert("Share failed.");
      }
    });
}

// =====================================
// View User Profile
// =====================================

function viewProfile(userId) {
  if (parseInt(userId) === parseInt(currentUserId)) {
    window.location.href = "profile.php";
    return;
  }

  window.location.href = "viewprofile.php?user_id=" + userId;
}

// =====================================
// Delete Post
// =====================================

function deletePost(id) {
  if (!confirm("Are you sure you want to delete this post?")) {
    return;
  }

  let formData = new FormData();

  formData.append("post_id", id);

  fetch("api/delete_post.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())

    .then((data) => {
      if (data.success) {
        alert("Post deleted successfully");

        loadPosts(true);
      } else {
        alert(data.message);
      }
    })

    .catch((error) => {
      console.log("Delete Error:", error);
    });
}

// =====================================
// Edit Post
// =====================================

function editPost(id) {
  let textDiv = document.getElementById("post-text-" + id);

  let oldText = textDiv.innerText;

  let newText = prompt("Edit your post:", oldText);

  if (newText === null) {
    return;
  }

  newText = newText.trim();

  let formData = new FormData();

  formData.append("post_id", id);

  formData.append("post_text", newText);

  fetch("api/edit_post.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())

    .then((data) => {
      if (data.success) {
        alert("Post updated successfully");

        loadPosts(true);
      } else {
        alert(data.message);
      }
    })

    .catch((error) => {
      console.log("Edit Error:", error);
    });
}

// =====================================
// Load More
// =====================================

loadMoreBtn.addEventListener("click", function () {
  loadPosts();
});

// =====================================
// Initial Load
// =====================================

loadPosts();

// =====================================
// Auto Refresh Every 30 Seconds
// =====================================

// setInterval(function () {
//   loadPosts(true);
// }, 30000);

// =====================================
// Close Emoji Picker
// =====================================

document.addEventListener("click", function (e) {
  if (
    !e.target.closest(".commentEmojiBtn") &&
    !e.target.closest(".emoji-wrapper")
  ) {
    document.querySelectorAll(".emoji-wrapper").forEach((wrapper) => {
      wrapper.classList.remove("active");
    });
  }
});
