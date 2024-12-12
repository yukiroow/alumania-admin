<?php
session_start();

if (isset($_SESSION['username']) && $_SESSION['role'] == 'Admin') {
    require_once '..\database\database.php';

    $db = Database::getInstance();
    $conn = $db->getConnection();

    $sqlAlumni = "SELECT * FROM alumni";
    $resultAlumni = $conn->query($sqlAlumni);

    $sqlManagers = "
    SELECT 
        username,
        password
    FROM user
    WHERE userType = 'Manager';
    ";
    $resultManagers = $conn->query($sqlManagers);
?>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../res/styles/users.css">
        <title>Alumania</title>
    </head>

    <body>
        <div id="notificationContainer"></div>
        <?php include 'navbar.php'; ?>
        <script defer> setActiveNav("userstab", "usersicon", 3); </script>

        <div class="content-container">
            <div class="header">
                <h1>Users</h1>
                <p>View and Manage User Accounts</p>
            </div>

            <div class="navigation">
                <ul id="ul-users">
                    <li id="alumniTab" class="active" selected-icon="../../res/alumni-blue.png" unselected-icon="../../res/alumni.png">
                        <img src="../../res/alumni-blue.png" alt="Alumni">
                        <p>Alumni</p>
                    </li>
                    <li id="managerTab" selected-icon="../../res/manager-blue.png" unselected-icon="../../res/manager.png">
                        <img src="../../res/manager.png" alt="Managers">
                        <p>Managers</p>
                    </li>
                </ul>
            </div>
            <div class="searchfilter">
                <div class="total-users">Total Users: </div>
                    <div class="search-box">
                        <input type="text" class="search-input">
                        <img src="../../res/search.png" class="search-icon" alt="Search">
                        <div id="filterButtonContainer">
                        <button class="filter-btn" onclick="toggleFilterDropdown()">
                            <img src="../../res/sort.png" class="filter-icon" alt="Filter">
                        </button>
                        </div>
                        <div class="filter-dropdown" id="filterDropdown">
                            <h3>Search Filters</h3>
                            <div class="filter-content">
                                <div class="filter-section">
                                    <h4>Status</h4>
                                    <ul>
                                        <li>Employed</li>
                                        <li>Unemployed</li>
                                        <li>Underemployed</li>
                                    </ul>
                                </div>
                                <div class="filter-section">
                                    <h4>Location</h4>
                                    <ul>
                                        <li>Domestic</li>
                                        <li>Foreign</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="add-manager-button-container hidden">
                            <button class="add-manager-button">
                            <img src="../../res/add.png" alt="Add Icon" class="add-manager-icon"/>Add Manager</button>
                        </div>
                    </div>
            </div>
            <div id="addManagerModal" class="modal hidden">
                <div class="modal-content">
                    <span class="close-btn">&times;</span>
                    <h2>Add New Manager</h2>
                    <form id="addManagerForm">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username">

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password">
                    <button id="togglePassword" type="button">Show Password</button> 
                    <button type="submit" class="submit-button">Add Manager</button>
                    </form>
                </div>
            </div>

            <div id="editManagerModal" class="modal hidden">
                <div class="modal-content">
                    <span class="close-btn">&times;</span>
                    <h2>Edit Manager</h2>
                    <form id="editManagerForm">
                        <input type="hidden" id="editManagerId" name="manager_id">
                        <label for="editUsername">Username:</label>
                        <input type="text" id="editUsername" name="username">

                        <label for="editPassword">Password:</label>
                        <input type="password" id="editPassword" name="password">
                        <button id="togglePassword1" type="button">Show Password</button>
                        <button type="submit" class="submit-button">Save Changes</button>
                    </form>
                </div>
            </div>

            <div id="deleteManagerModal" class="modal hidden">
                <div class="modal-content">
                    <span class="close-btn">&times;</span>
                    <h2>Confirm Delete</h2>
                    <p>Are you sure you want to delete the manager <strong id="deleteManagerName"></strong>?</p>
                    <button id="confirmDeleteManager" class="submit-button">Yes, Delete</button>
                </div>
            </div>

            <div class="user-panel" id="userPanel">
                
            </div>
        </div>

        <div class="modal hidden" id="userModal">
            <div class="modal-content">
                <span class="close-btn" id="closeModal">&times;</span>
                <div id="userInfo"></div>
            </div>
        </div>

        <script>
            const alumniContent = `
                <div class="section-title">
                    <table>
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Employment Status</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            if ($resultAlumni && $resultAlumni->num_rows > 0) {
                                while ($row = $resultAlumni->fetch_assoc()) { 
                                    $userData = json_encode([
                                        "userid" => $row['userid'],
                                        "email" => $row['email'],
                                        "name" => $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'],
                                        "empstatus" => $row['empstatus'],
                                        "location" => $row['location'],
                                        "displaypic" => base64_encode($row['displaypic']),
                                        "course" => $row['course'],    // Ensure course field exists
                                        "company" => $row['company']   // Ensure company field exists
                                    ]);
                            ?>
                                    <tr data-user-data='<?php echo htmlspecialchars($userData); ?>'>
                                        <td data-label="User ID"><?php echo htmlspecialchars($row['userid']); ?></td>
                                        <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td data-label="Name"><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']); ?></td>
                                        <td data-label="Employment Status"><?php echo htmlspecialchars($row['empstatus']); ?></td>
                                        <td data-label="Location"><?php echo htmlspecialchars($row['location']); ?></td>
                                    </tr>
                            <?php }
                            } else { ?>
                                <tr> 
                                    <td colspan="5">No alumni found</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            `;

            /** Comment **/
            const managerContent = `
                <div class="section-title">
                    <table>
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($resultManagers && $resultManagers->num_rows > 0) {
                                while ($row = $resultManagers->fetch_assoc()) { 
                                    $managerData = json_encode([
                                        "username" => $row['username'],
                                        "password" => $row['password']
                                    ]);
                            ?>
                                <tr data-manager-data='<?php echo htmlspecialchars($managerData); ?>'>
                                    <td data-label="Username"><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td data-label="Actions">
                                        <button class="edit-manager-btn" onclick='openEditModal(<?php echo htmlspecialchars($managerData); ?>)'>Edit</button>
                                        <button class="delete-manager-btn" onclick='openDeleteModal("<?php echo htmlspecialchars($row['username']); ?>")'>Delete</button>
                                    </td>
                                </tr>
                            <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="3">No managers found</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            `;

            document.addEventListener('DOMContentLoaded', () => {
                // Variables for modals
                const editManagerModal = document.getElementById("editManagerModal");
                const deleteManagerModal = document.getElementById("deleteManagerModal");
                const editManagerForm = document.getElementById("editManagerForm");
                const deleteManagerConfirmBtn = document.getElementById("confirmDeleteManager");

                let currentManagerUsername = null;

                window.openEditModal = function (managerData) {
                    document.getElementById("editUsername").value = managerData.username;
                    document.getElementById("editPassword").value = managerData.password;
                    currentManagerUsername = managerData.username; // Displayed for confirmation
                    editManagerModal.style.display = "block";
                };

                editManagerForm.addEventListener("submit", (e) => {
                    e.preventDefault();

                    // Validate form fields
                    const username = editManagerForm.elements["username"].value.trim();
                    const password = editManagerForm.elements["password"].value.trim();

                    if (!username || !password) {
                        alert("All fields are required.");
                    }

                    if (password.length < 4) {
                        alert("Password must be at least 4 characters long.");
                        return;
                    }

                    const formData = new FormData(editManagerForm);
                    formData.append("currentUsername", currentManagerUsername); 

                    fetch("edit_manager.php", {
                        method: "POST",
                        body: formData,
                    })
                    .then((response) => response.text())
                    .then((data) => {
                        alert(data);  
                        location.reload();
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        alert("An error occurred while updating the manager.");
                    });

                    editManagerModal.style.display = "none"; 
                });


                window.openDeleteModal = function (username) {
                    currentManagerUsername = username;
                    document.getElementById("deleteManagerName").textContent = username;
                    deleteManagerModal.style.display = "block";
                };

                deleteManagerConfirmBtn.addEventListener("click", () => {
                    fetch("delete_manager.php", {
                        method: "POST",
                        body: new URLSearchParams({
                            username: currentManagerUsername
                        }),
                    })
                        .then((response) => response.text())
                        .then((data) => {
                            alert(data);  // Assuming the server returns a success message
                            location.reload();
                        })
                        .catch((error) => {
                            console.error("Error:", error);
                            alert("An error occurred while deleting the manager.");
                        });

                    deleteManagerModal.style.display = "none";
                });


                // Close modals
                document.querySelectorAll(".close-btn").forEach((btn) => {
                    btn.addEventListener("click", () => {
                        editManagerModal.style.display = "none";
                        deleteManagerModal.style.display = "none";
                    });
                });
            });     
        </script>

        <script src="../js/users.js" defer></script>
    </body>
</html>
<?php } else { ?>
    <h1 style='margin:auto;'>Access Forbidden</h1>
    <p>Please log in to your proper account.</p>
<?php } ?>