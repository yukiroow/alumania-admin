document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.querySelector(".search-input");
  const totalUsersElement = document.querySelector(".total-users");
  const filterDropdown = document.getElementById("filterDropdown");
  const statusFilters = document.querySelectorAll(
    ".filter-section:nth-child(1) ul li"
  );
  const locationFilters = document.querySelectorAll(
    ".filter-section:nth-child(2) ul li"
  );
  const tableRows = document.querySelectorAll("tbody tr");
  let activeFilters = {
    status: null,
    location: null,
  };

  function filterRows() {
    const tableRows = document.querySelectorAll("tbody tr");
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

  if (filterDropdown) {
    const toggleFilterDropdown = () => {
      filterDropdown.classList.toggle("show");
    };

    const filterButton = document.querySelector(".filter-btn");
    if (filterButton) {
      filterButton.addEventListener("click", toggleFilterDropdown);
    }
  }

  const alumniTab = document.getElementById("alumniTab");
  const managerTab = document.getElementById("managerTab");
  const userPanel = document.getElementById("userPanel");
  const addManagerButtonContainer = document.querySelector(
    ".add-manager-button-container"
  );

  const updateTabUI = (activeTab, inactiveTab) => {
    activeTab.classList.add("active");
    inactiveTab.classList.remove("active");

    const activeImg = activeTab.querySelector("img");
    const inactiveImg = inactiveTab.querySelector("img");
    activeImg.src = activeTab.getAttribute("selected-icon");
    inactiveImg.src = inactiveTab.getAttribute("unselected-icon");
  };

  alumniTab.addEventListener("click", () => {
    userPanel.innerHTML = alumniContent;
    updateTabUI(alumniTab, managerTab);
    addManagerButtonContainer.classList.add("hidden");
    filterDropdown.classList.remove("show");
  });

  managerTab.addEventListener("click", () => {
    userPanel.innerHTML = managerContent;
    updateTabUI(managerTab, alumniTab);
    addManagerButtonContainer.classList.remove("hidden");
    filterDropdown.classList.remove("show");
  });
});

document.addEventListener("DOMContentLoaded", () => {
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

    window.addEventListener("click", (event) => {
      if (event.target === modal) {
        modal.style.display = "none";
      }
    });

    const form = document.getElementById("addManagerForm");
    form.addEventListener("submit", (e) => {
      e.preventDefault();
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
            alert(data.message || "Failed to add manager.");
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
});

document.addEventListener("DOMContentLoaded", function () {
  const togglePassword = document.getElementById("togglePassword");
  const password = document.getElementById("password");

  togglePassword.addEventListener("click", function () {
    const type = password.type === "password" ? "text" : "password";
    password.type = type;
    togglePassword.textContent =
      type === "password" ? "Show Password" : "Hide Password";
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const alumniTab = document.getElementById("alumniTab");
  const managerTab = document.getElementById("managerTab");
  const userPanel = document.getElementById("userPanel");
  const userDetails = document.getElementById("userDetails");
  const userInfo = document.getElementById("userInfo");
  const goBackButton = document.getElementById("goBackButton");
  const addManagerButtonContainer = document.querySelector(
    ".add-manager-button-container"
  );
  const filterButtonContainer = document.getElementById(
    "filterButtonContainer"
  );
  const searchInput = document.querySelector(".search-input");

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
    filterButtonContainer.style.display = showFilter ? "block" : "none";
    if (searchInput) {
      searchInput.placeholder = placeholder;
    }
    userPanel.classList.remove("hidden");
    userDetails.classList.add("hidden");
  };

  const showUserDetails = (userData) => {
    userInfo.innerHTML = `
      <h2>${userData.name}</h2>
      <p><strong>Email:</strong> ${userData.email}</p>
      <p><strong>Employment Status:</strong> ${userData.empstatus}</p>
      <p><strong>Location:</strong> ${userData.location}</p>
    `;
    userPanel.classList.add("hidden");
    userDetails.classList.remove("hidden");
  };

  goBackButton.addEventListener("click", () => {
    userDetails.classList.add("hidden");
    userPanel.classList.remove("hidden");
  });

  userPanel.addEventListener("click", (event) => {
    const row = event.target.closest("tr");
    if (row && row.dataset.userData) {
      const userData = JSON.parse(row.dataset.userData);
      showUserDetails(userData);
    }
  });
});
