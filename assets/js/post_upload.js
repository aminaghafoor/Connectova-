// ==========================================
// CONNECTOVA - POST UPLOAD
// assets/js/post_upload.js
// ==========================================

document.addEventListener("DOMContentLoaded", function () {
  // ==========================================
  // ELEMENTS
  // ==========================================

  const postForm = document.getElementById("postForm");
  const imageInput = document.getElementById("images");
  const videoInput = document.getElementById("video");
  const previewArea = document.getElementById("previewArea");
  const progressBar = document.getElementById("progressBar");
  const messageBox = document.getElementById("message");

  // ==========================================
  // LIMITS
  // ==========================================

  const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5 MB
  const MAX_VIDEO_SIZE = 50 * 1024 * 1024; // 50 MB

  // ==========================================
  // IMAGE PREVIEW
  // ==========================================

  if (imageInput && previewArea) {
    imageInput.addEventListener("change", function () {
      previewArea.innerHTML = "";

      const files = this.files;

      for (let file of files) {
        // Check image type
        if (!file.type.startsWith("image/")) {
          alert(file.name + " is not an image.");

          imageInput.value = "";
          previewArea.innerHTML = "";

          return;
        }

        // Check image size
        if (file.size > MAX_IMAGE_SIZE) {
          alert(file.name + " exceeds 5 MB.");

          imageInput.value = "";
          previewArea.innerHTML = "";

          return;
        }

        // Preview image
        const reader = new FileReader();

        reader.onload = function (e) {
          const img = document.createElement("img");

          img.src = e.target.result;

          img.className = "preview-image";

          previewArea.appendChild(img);
        };

        reader.readAsDataURL(file);
      }
    });
  }

  // ==========================================
  // VIDEO PREVIEW
  // ==========================================

  if (videoInput && previewArea) {
    videoInput.addEventListener("change", function () {
      const file = this.files[0];

      if (!file) {
        return;
      }

      // Check video type
      if (!file.type.startsWith("video/")) {
        alert("Please select a valid video.");

        videoInput.value = "";

        return;
      }

      // Check video size
      if (file.size > MAX_VIDEO_SIZE) {
        alert("Video exceeds 50 MB.");

        videoInput.value = "";

        return;
      }

      // Create video preview
      const video = document.createElement("video");

      video.controls = true;

      video.className = "preview-video";

      video.src = URL.createObjectURL(file);

      previewArea.appendChild(video);
    });
  }

  // ==========================================
  // SUBMIT POST FORM
  // ==========================================

  if (postForm) {
    postForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // Make sure required elements exist
      if (!messageBox || !progressBar) {
        console.error("Post upload elements are missing.");

        return;
      }

      messageBox.innerHTML = "";

      const formData = new FormData(postForm);

      const xhr = new XMLHttpRequest();

      xhr.open("POST", "api/upload_post.php", true);

      // ==========================================
      // UPLOAD PROGRESS
      // ==========================================

      xhr.upload.addEventListener("progress", function (e) {
        if (e.lengthComputable) {
          const percent = Math.round((e.loaded / e.total) * 100);

          progressBar.style.width = percent + "%";

          progressBar.innerHTML = percent + "%";
        }
      });

      // ==========================================
      // SERVER RESPONSE
      // ==========================================

      xhr.onload = function () {
        if (xhr.status === 200) {
          try {
            const response = JSON.parse(xhr.responseText);

            // ==============================
            // SUCCESS
            // ==============================

            if (response.success) {
              messageBox.style.color = "green";

              messageBox.innerHTML = response.message;

              postForm.reset();

              if (previewArea) {
                previewArea.innerHTML = "";
              }

              progressBar.style.width = "0%";

              progressBar.innerHTML = "0%";
            }

            // ==============================
            // SERVER ERROR
            // ==============================
            else {
              messageBox.style.color = "red";

              messageBox.innerHTML =
                response.message || "Unable to upload post.";
            }
          } catch (err) {
            console.error("POST UPLOAD JSON ERROR:", err);

            messageBox.style.color = "red";

            messageBox.innerHTML = "Invalid server response.";
          }
        } else {
          messageBox.style.color = "red";

          messageBox.innerHTML = "Upload failed.";
        }
      };

      // ==========================================
      // NETWORK ERROR
      // ==========================================

      xhr.onerror = function () {
        messageBox.style.color = "red";

        messageBox.innerHTML = "Network error. Please try again.";
      };

      // ==========================================
      // SEND
      // ==========================================

      xhr.send(formData);
    });
  }

  // ==========================================
  // POST EMOJI PICKER
  // ==========================================

  const postEmojiBtn = document.getElementById("postEmojiBtn");

  const postEmojiWrapper = document.getElementById("postEmojiWrapper");

  const postEmojiPicker = document.getElementById("postEmojiPicker");

  const postText = document.getElementById("post_text");

  if (postEmojiBtn && postEmojiWrapper && postEmojiPicker && postText) {
    postEmojiBtn.addEventListener("click", function (e) {
      e.stopPropagation();

      postEmojiWrapper.classList.toggle("active");
    });

    postEmojiPicker.addEventListener("emoji-click", function (event) {
      postText.value += event.detail.unicode;

      postText.focus();
    });

    document.addEventListener("click", function (e) {
      if (
        !postEmojiWrapper.contains(e.target) &&
        !postEmojiBtn.contains(e.target)
      ) {
        postEmojiWrapper.classList.remove("active");
      }
    });
  }
});
