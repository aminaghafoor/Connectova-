// =====================================
// CONNECTOVA
// Slangs JavaScript
// assets/js/slangs.js
// =====================================

const slangContainer = document.getElementById("slangContainer");

const categoryContainer = document.getElementById("categoryContainer");

const popularContainer = document.getElementById("popularContainer");

const recentContainer = document.getElementById("recentContainer");

const searchInput = document.getElementById("searchSlang");

let currentCategory = "";

// =====================================
// Load Categories
// =====================================

function loadCategories() {
  fetch("api/get_slang_categories.php")
    .then((response) => response.json())

    .then((data) => {
      if (!data.success) {
        return;
      }

      categoryContainer.innerHTML = "";

      categoryContainer.innerHTML += `
            <button onclick="filterCategory('')">
                All
            </button>
        `;

      data.categories.forEach((category) => {
        categoryContainer.innerHTML += `

                <button
                    onclick="filterCategory('${category}')">

                    ${category}

                </button>

            `;
      });
    });
}

// =====================================
// Recently Added
// =====================================

function loadRecentSlangs() {
  fetch("api/recent_slangs.php")
    .then((response) => response.json())

    .then((data) => {
      if (!data.success) {
        return;
      }

      recentContainer.innerHTML = "";

      data.recent.forEach((item) => {
        recentContainer.innerHTML += `

            <div class="recent-card">

                <strong>

                ${item.slang_term}

                </strong>

                <br>

                <small>

                ${item.category}

                </small>

            </div>

            `;
      });
    })

    .catch((error) => {
      console.log(error);
    });
}

// =====================================
// Popular Slangs
// =====================================

function loadPopularSlangs() {
  fetch("api/popular_slangs.php")
    .then((response) => response.json())

    .then((data) => {
      if (!data.success) {
        return;
      }

      popularContainer.innerHTML = "";

      data.popular.forEach((item) => {
        popularContainer.innerHTML += `

            <div class="popular-card">

                <strong>

                ${item.slang_term}

                </strong>

                <br>

                <small>

                ${item.views} views

                </small>

            </div>

            `;
      });
    });
}

// =====================================
// Load Slangs
// =====================================

function loadSlangs() {
  let search = searchInput.value;

  fetch(
    `api/search_slangs.php?search=${encodeURIComponent(search)}&category=${encodeURIComponent(currentCategory)}`,
  )
    .then((response) => response.json())

    .then((data) => {
      if (data.success) {
        displaySlangs(data.slangs);
      } else {
        slangContainer.innerHTML = `

            <div class="no-slangs">

            No slangs found.

            </div>

            `;
      }
    })

    .catch((error) => {
      console.log("Slang Error:", error);
    });
}

// =====================================
// Display Cards
// =====================================

function displaySlangs(slangs) {
  slangContainer.innerHTML = "";

  if (slangs.length === 0) {
    slangContainer.innerHTML = `

        <div class="no-slangs">

            No slangs available.

        </div>

        `;

    return;
  }

  slangs.forEach((slang) => {
    slangContainer.innerHTML += `

        <div class="slang-card">

            <div class="slang-header">

                <h2>

                    ${slang.slang_term}

                </h2>

                <span>

                    ${slang.category}

                </span>

            </div>


            <p class="slang-meaning">

                ${
                  slang.meaning.length > 100
                    ? slang.meaning.substring(0, 100) + "..."
                    : slang.meaning
                }

            </p>


            <button

                class="read-more-btn"

                onclick="readMore(${slang.id})">

                📖 Read More

            </button>


            <small>

                Added by:
                ${slang.username ?? "User"}

            </small>

        </div>

        `;
  });
}

// =====================================
// Search Event
// =====================================

searchInput.addEventListener("keyup", function () {
  loadSlangs();
});

// =====================================
// Search Event
// =====================================

searchInput.addEventListener(
  "keyup",

  function () {
    loadSlangs();
  },
);

// =====================================
// Category Filter
// =====================================

function filterCategory(category) {
  currentCategory = category;

  loadSlangs();
}

// slangs read more function
function readMore(id) {
  fetch("api/get_slang.php?id=" + id)
    .then((response) => response.json())

    .then((data) => {
      if (!data.success) {
        return;
      }

      const slang = data.slang;

      document.getElementById("modalBody").innerHTML = `

            <h2>${slang.slang_term}</h2>

            <p>

                <strong>Meaning</strong><br>

                ${slang.meaning}

            </p>

            <p>

                <strong>Example</strong><br>

                ${slang.example_sentence}

            </p>

            <p>

                <strong>Category:</strong>

                ${slang.category}

            </p>

            <p>

                <strong>Views:</strong>

                ${slang.views}

            </p>

            <p>

                <strong>Added By:</strong>

                ${slang.username ?? "Admin"}

            </p>

        `;

      document.getElementById("slangModal").style.display = "flex";

      // Refresh Popular section because views changed
      loadPopularSlangs();
    });
}

function closeModal() {
  document.getElementById("slangModal").style.display = "none";
}

// =====================================
// Initial Load
// =====================================

loadCategories();

loadPopularSlangs();

loadRecentSlangs();

loadSlangs();
