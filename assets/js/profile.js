const modal = document.getElementById("editProfileModal");

const editBtn = document.getElementById("editProfileBtn");

const closeBtn = document.querySelector(".close-modal");

editBtn.onclick = () => {
  modal.style.display = "block";
};

closeBtn.onclick = () => {
  modal.style.display = "none";
};

window.onclick = (e) => {
  if (e.target === modal) {
    modal.style.display = "none";
  }
};

document
  .getElementById("editProfileForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("api/update_profile.php", {
      method: "POST",

      body: formData,
    })
      .then((r) => r.json())

      .then((data) => {
        alert(data.message);

        if (data.success) {
          location.reload();
        }
      });
  });
