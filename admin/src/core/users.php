<?php
session_start();

if (isset($_SESSION['username'])) {
    require_once '..\database\database.php';

    $db = Database::getInstance();
    $conn = $db->getConnection();

    $sqlAlumni = "SELECT userid, email, firstname, middlename, lastname, empstatus, location FROM alumni";
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
                    <input type="text" id="username" name="username" required>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <button id="togglePassword" type="button">Show Password</button> 
                    <button type="submit" class="submit-button">Add Manager</button>
                    </form>
                </div>
            </div>



            <div class="user-panel" id="userPanel">
                
            </div>
        </div>

        <div class="user-panel" id="userPanel">
            <div id="userDetails" class="hidden">
                <button id="goBackButton">Go Back</button>
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
                                        "name" => $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'],
                                        "email" => $row['email'],
                                        "empstatus" => $row['empstatus'],
                                        "location" => $row['location']
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
                                while ($row = $resultManagers->fetch_assoc()) { ?>
                                    <tr>
                                        <td data-label="Username"><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td data-label="Actions">
                                            <!-- Add any action buttons if needed -->
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
            const alumniTab = document.getElementById('alumniTab');
            const managerTab = document.getElementById('managerTab');
            const userPanel = document.getElementById('userPanel');
            const addManagerButtonContainer = document.querySelector('.add-manager-button-container');
            const filterButtonContainer = document.getElementById('filterButtonContainer');
            const searchInput = document.querySelector('.search-input');
            function updateTabState(activeTab, inactiveTab, content, showFilter, placeholder) {
                userPanel.innerHTML = content; 
                activeTab.classList.add('active'); 
                inactiveTab.classList.remove('active'); 
                addManagerButtonContainer.classList.toggle('hidden', showFilter); 
                filterButtonContainer.style.display = showFilter ? "block" : "none"; 
                if (searchInput) {
                    searchInput.placeholder = placeholder; 
                }
            }

            updateTabState(alumniTab, managerTab, alumniContent, true, "Name, ID, Email");

            alumniTab.addEventListener('click', () => {
                updateTabState(alumniTab, managerTab, alumniContent, true, "Name, ID, Email");
            });

            managerTab.addEventListener('click', () => {
                updateTabState(managerTab, alumniTab, managerContent, false, "Username");
            });
        });

        function showUserDetails(userData) {
        userInfo.innerHTML = `
            <h2>User Details</h2>
            <p><strong>ID:</strong> ${userData.id}</p>
            <p><strong>Name:</strong> ${userData.name}</p>
            <p><strong>Email:</strong> ${userData.email}</p>
            <p><strong>Status:</strong> ${userData.status}</p>
            <p><strong>Location:</strong> ${userData.location}</p>
        `;

        userPanel.classList.add("hidden");
        userDetails.classList.remove("hidden");
        }

        goBackButton.addEventListener("click", () => {
            userDetails.classList.add("hidden");
            userPanel.classList.remove("hidden");
        });

        document.querySelector("tbody").addEventListener("click", (event) => {
            const row = event.target.closest("tr");
            if (row) {
                const userData = {
                    id: row.cells[0].textContent,
                    email: row.cells[1].textContent,
                    name: row.cells[2].textContent,
                    status: row.cells[3].textContent,
                    location: row.cells[4].textContent,
                };

                showUserDetails(userData);
            }
        });
        </script>

        <script src="../js/users.js" defer></script>
        <script src="../js/contentmove.js"></script>
    </body>
</html>
<?php } ?>