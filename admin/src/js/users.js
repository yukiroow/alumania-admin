document.addEventListener("DOMContentLoaded", () => {
  const alumniTab = document.getElementById("alumniTab");
  const managerTab = document.getElementById("managerTab");
  const userPanel = document.getElementById("userPanel");
  const addManagerButtonContainer = document.querySelector(
    ".add-manager-button-container"
  );
  const filterButtonContainer = document.getElementById(
    "filterButtonContainer"
  );
  const searchInput = document.querySelector(".search-input");
  const totalUsersElement = document.querySelector(".total-users");
  const filterDropdown = document.getElementById("filterDropdown");
  const filterButton = document.querySelector(".filter-btn");

  // Filters
  const statusFilters = document.querySelectorAll(
    ".filter-section:nth-child(1) ul li"
  );
  const locationFilters = document.querySelectorAll(
    ".filter-section:nth-child(2) ul li"
  );
  let tableRows; // This will be set dynamically based on the active tab
  let activeFilters = {
    status: null,
    location: null,
  };

  function updateTabState(
    activeTab,
    inactiveTab,
    content,
    showFilter,
    placeholder
  ) {
    userPanel.innerHTML = content;
    tableRows = userPanel.querySelectorAll("tbody tr"); // Update table rows based on new content
    activeTab.classList.add("active");
    inactiveTab.classList.remove("active");
    addManagerButtonContainer.classList.toggle("hidden", showFilter);
    filterButtonContainer.style.display = showFilter ? "block" : "none";
    if (searchInput) {
      searchInput.placeholder = placeholder;
      searchInput.value = ""; // Clear search input on tab switch
    }
    resetFilters(); // Reset filters when switching tabs
    filterRows(); // Reapply filters after switching tabs

    // Update icons based on active/inactive state
    updateIcons(activeTab, inactiveTab);
  }

  function updateIcons(activeTab, inactiveTab) {
    const activeImg = activeTab.querySelector("img");
    const inactiveImg = inactiveTab.querySelector("img");

    // Set the icon sources based on data attributes
    activeImg.src = activeTab.getAttribute("selected-icon");
    inactiveImg.src = inactiveTab.getAttribute("unselected-icon");
  }

  function resetFilters() {
    activeFilters.status = null;
    activeFilters.location = null;
    statusFilters.forEach((f) => f.classList.remove("active"));
    locationFilters.forEach((f) => f.classList.remove("active"));
  }

  function filterRows() {
    if (!tableRows) return; // Ensure tableRows is set

    const searchQuery = searchInput.value.toLowerCase();
    let visibleCount = 0;

    tableRows.forEach((row) => {
      const cells = row.querySelectorAll("td");
      const rowText = Array.from(cells)
        .map((cell) => cell.textContent.toLowerCase())
        .join(" ");
      const status = cells[3]?.textContent.toLowerCase();
      const location = cells[4]?.textContent.toLowerCase();
      const matchesSearch = rowText.includes(searchQuery);
      const matchesStatus = activeFilters.status
        ? status === activeFilters.status
        : true;
      const matchesLocation = activeFilters.location
        ? location === activeFilters.location
        : true;

      if (matchesSearch && matchesStatus && matchesLocation) {
        row.style.display = "";
        visibleCount++;
      } else {
        row.style.display = "none";
      }
    });

    if (totalUsersElement) {
      totalUsersElement.textContent = `Total Users: ${visibleCount}`;
    }
  }

  // Event listeners for filtering
  if (searchInput) {
    searchInput.addEventListener("input", filterRows);
  }

  statusFilters.forEach((filter) => {
    filter.addEventListener("click", () => {
      const selectedStatus = filter.textContent.toLowerCase();
      activeFilters.status =
        activeFilters.status === selectedStatus ? null : selectedStatus;
      statusFilters.forEach((f) => f.classList.remove("active"));
      if (activeFilters.status) filter.classList.add("active");
      filterRows();
    });
  });

  locationFilters.forEach((filter) => {
    filter.addEventListener("click", () => {
      const selectedLocation = filter.textContent.toLowerCase();
      activeFilters.location =
        activeFilters.location === selectedLocation ? null : selectedLocation;
      locationFilters.forEach((f) => f.classList.remove("active"));
      if (activeFilters.location) filter.classList.add("active");
      filterRows();
    });
  });

  // Toggle filter dropdown
  if (filterButton) {
    filterButton.addEventListener("click", (event) => {
      event.preventDefault(); // Prevent default action
      filterDropdown.classList.toggle("show");
    });
  }

  // Close dropdown when clicking outside
  document.addEventListener("click", (event) => {
    if (
      !filterDropdown.contains(event.target) &&
      !filterButton.contains(event.target)
    ) {
      filterDropdown.classList.remove("show");
    }
  });

  // Tab click events
  alumniTab.addEventListener("click", () => {
    updateTabState(
      alumniTab,
      managerTab,
      alumniContent,
      true,
      "Name, ID, Email"
    );
  });

  managerTab.addEventListener("click", () => {
    updateTabState(managerTab, alumniTab, managerContent, false, "Username");
  });

  // Set default tab to Alumni on page load
  updateTabState(alumniTab, managerTab, alumniContent, true, "Name, ID, Email");
});

const addManagerButton = document.querySelector(".add-manager-button");
const modal = document.getElementById("addManagerModal");
const closeBtn = document.querySelector(".close-btn");

if (addManagerButton && modal && closeBtn) {
  addManagerButton.addEventListener("click", () => {
    modal.style.display = "block"; // Show modal
  });

  closeBtn.addEventListener("click", () => {
    modal.style.display = "none"; // Hide modal
  });

  const form = document.getElementById("addManagerForm");

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    // Validate form fields
    const username = form.elements["username"].value.trim();
    const password = form.elements["password"].value.trim();

    if (!username || !password) {
      alert("All fields are required.");
      return; // Stop the submission if fields are empty
    }

    if (password.length < 4) {
      alert("Password must be at least 4 characters long.");
      return; // Stop the submission if password is too short
    }

    const formData = new FormData(form);

    fetch("add_manager.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Manager added successfully!");
          modal.style.display = "none";
          form.reset();
        } else {
          if (data.message === "User already exists") {
            alert("A user with this username already exists.");
          } else {
            alert(data.message || "Failed to add manager.");
          }
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("An error occurred while adding the manager.");
      });
  });
} else {
  console.error("Required modal elements not found.");
}

const togglePassword = document.getElementById("togglePassword");
const password = document.getElementById("password");

togglePassword.addEventListener("click", function () {
  const type = password.type === "password" ? "text" : "password";
  password.type = type;
  togglePassword.textContent =
    type === "password" ? "Show Password" : "Hide Password";
});

const togglePassword1 = document.getElementById("togglePassword1");
const password1 = document.getElementById("editPassword");

togglePassword1.addEventListener("click", function () {
  const type = password1.type === "password" ? "text" : "password";
  password1.type = type;
  togglePassword1.textContent =
    type === "password" ? "Show Password" : "Hide Password";
});

const userDetails = document.getElementById("userDetails");
const userInfo = document.getElementById("userInfo");

const updateTabState = (
  activeTab,
  inactiveTab,
  content,
  showFilter,
  placeholder
) => {
  userPanel.innerHTML = content;
  activeTab.classList.add("active");
  inactiveTab.classList.remove("active");
  addManagerButtonContainer.classList.toggle("hidden", showFilter);
  if (searchInput) {
    searchInput.placeholder = placeholder;
  }
  userPanel.classList.remove("hidden");
  userDetails.classList.add("hidden");
};

// Function to show user details in the modal
function showUserDetails(userData) {
  userInfo.innerHTML = `
    <div class="profile-section">
      <div class="profile-picture">
        <img src="data:image/jpeg;base64,${userData.displaypic}" alt="${userData.name}'s Display Picture" class="circle-pic" />
      </div>
      <div class="profile-details">
        <h2>${userData.name}</h2>
        <p><strong>User ID:</strong> ${userData.userid}</p>
      </div>
    </div>

    <div class="details-section">
      <div class="header-split">
        <div class="header-image">
          <img src="../../res/info.png" alt="Alumni Info" />
        </div>
        <div class="header-text">
          <h3>Alumni Information</h3>
        </div>
      </div>

      <p><strong>Email:</strong> ${userData.email}</p>
      <p><strong>Course:</strong> ${userData.course}</p>
      <p><strong>Status:</strong> ${userData.empstatus}</p>
      <p><strong>Location:</strong> ${userData.location}</p>
      <p><strong>Company:</strong> ${userData.company}</p>
    </div>
  `;

  // Show the modal
  const modal = document.getElementById("userModal");
  modal.classList.remove("hidden");
}

// Close button functionality
document.getElementById("closeModal").addEventListener("click", () => {
  document.getElementById("userModal").classList.add("hidden");
});

userPanel.addEventListener("click", (event) => {
  const row = event.target.closest("tr");
  if (row && row.dataset.userData) {
    const userData = JSON.parse(row.dataset.userData);
    showUserDetails(userData);
  }
});
/** Comment **/
