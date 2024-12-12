<?php
session_start();
if (isset($_SESSION['username'])) { ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../res/styles/dashboard.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
        <title>Alumania</title>
    </head>

    <body>
        <div id="notificationContainer"></div>
        <?php include 'navbar.php'; ?>
        <script defer>
            setActiveNav("dashtab", "dashicon", 1);
        </script>


        <?php
        require_once '..\database\database.php';
        $db = \Database::getInstance()->getConnection();

        $counts = [
            'alumni' => 0,
            'managers' => 0,
            'event' => 0,
            'jobpost' => 0,
        ];

        foreach (['alumni', 'event', 'jobpost'] as $key) {
            $result = $db->query("SELECT COUNT(*) AS count FROM $key"); // Replace table names as needed
            if ($result && $row = $result->fetch_assoc()) {
                $counts[$key] = $row['count'];
            }
        }

        $result = $db->query("SELECT COUNT(*) AS count FROM user WHERE usertype = 'manager'");
        if ($result && $row = $result->fetch_assoc()) {
            $counts['managers'] = $row['count'];
        }

        // Employment status query
        $empstatusCounts = [
            'Employed' => 0,
            'Unemployed' => 0,
            'Underemployed' => 0,
        ];

        $result = $db->query("SELECT empstatus, COUNT(*) AS count FROM alumni GROUP BY empstatus");
        while ($row = $result->fetch_assoc()) {
            if (isset($empstatusCounts[$row['empstatus']])) {
                $empstatusCounts[$row['empstatus']] = $row['count'];
            }
        }

        // Location query
        $locationCounts = [
            'Domestic' => 0,
            'Foreign' => 0,
        ];

        $result = $db->query("SELECT location, COUNT(*) AS count FROM alumni GROUP BY location");
        while ($row = $result->fetch_assoc()) {
            if (isset($locationCounts[$row['location']])) {
                $locationCounts[$row['location']] = $row['count'];
            }
        }


        // Fetch recent alumni
        $recentAlumniData = [];
        $result = $db->query("SELECT alumni.firstname AS name, alumni.location, user.jointimestamp AS joined, alumni.displaypic as profpic FROM alumni JOIN user ON alumni.userid = user.userid ORDER BY user.jointimestamp DESC LIMIT 2");

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $recentAlumniData[] = $row; // Store results in an array
            }
        }

        // Fetch recent managers
        $recentManagersData = [];
        $result = $db->query("SELECT username, jointimestamp AS joined FROM user WHERE usertype = 'manager' ORDER BY joined DESC LIMIT 2");

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $recentManagersData[] = $row;
            }
        }

        // Fetching Interested Alumni data
        $interestedAlumniData = [];
        $result = $db->query(
            "SELECT 
                                    'Event' AS type, 
                                    e.title AS title, 
                                    e.eventtime AS time, 
                                    e.eventdate AS date, 
                                    e.eventloc AS location, 
                                    e.eventphoto AS eventphoto,
                                    COUNT(ie.userid) AS interested_count
                                FROM 
                                    event e
                                LEFT JOIN 
                                    interestedinevent ie ON e.eventid = ie.eventid
                                GROUP BY 
                                    e.eventid
                                UNION ALL
                                SELECT 
                                    'Job' AS type, 
                                    jp.title AS title, 
                                    NULL AS time, 
                                    NULL AS date, 
                                    jp.location AS location,
                                    NULL AS eventphoto, 
                                    COUNT(ij.userid) AS interested_count
                                FROM 
                                    jobpost jp
                                LEFT JOIN 
                                    interestedinjobpost ij ON jp.jobpid = ij.jobpid
                                GROUP BY 
                                    jp.jobpid
                                ORDER BY 
                                    interested_count DESC
                                LIMIT 4;"
        );
        $topEntries = $result->fetch_all(MYSQLI_ASSOC);
        ?>

        <div class="content-container">
            <div class="header">
                <h1>Dashboard</h1>
                <h2 class="date"><?php echo date("F d, Y") ?></h2>
            </div>
            <div class="total-count-section">
                <?php
                $cards = [
                    'alumni' => ['Alumni', $counts['alumni'], 'fa-graduation-cap', '#0b1975'],
                    'managers' => ['Managers', $counts['managers'], 'fa-user', '#0077c8'],
                    'event' => ['Events', $counts['event'], 'fa-calendar', '#0498b6'],
                    'jobpost' => ['Job Posting', $counts['jobpost'], 'fa-briefcase', '#00d4ff'],
                ];

                foreach ($cards as $key => $details) {
                    echo "
        <div class='card'>
            <div class='card-content'>
                <div class='icon-container' style='background-color: {$details[3]}'>
                    <i class='fa {$details[2]}'></i>
                </div>
                <div class='text-container'>
                    <div class='card-title'>{$details[0]}</div>
                    <div class='card-count'>{$details[1]}</div>
                </div>
            </div>
        </div>";
                }
                ?>
            </div>

            <div class="big-container">
                <div class="row-container">
                    <div class="chart-container">
                        <!-- Employment Status Chart -->
                        <div class="chart-card">
                            <canvas id="employmentChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-container">
                        <!-- User Location Chart -->
                        <div class="chart-card">
                            <canvas id="locationChart"></canvas>
                        </div>
                    </div>

                    <div class="interested-alumni">
                        <h2>Interested Alumni</h2>
                        <?php if (!empty($topEntries)) {
                            foreach ($topEntries as $entry) { 
                                $eventPhotoData = !empty($entry['eventphoto']) ? 'data:image/jpeg;base64,' . base64_encode($entry['eventphoto']) : null;
                                ?>
                                <div class="alumni-card">
                                    <div class="alumni-card-header">
                                        <?php if ($entry['type'] === 'Event') { ?>
                                            <img src="<?php echo $eventPhotoData; ?>" alt="Event Image" class="alumni-card-header-img">
                                        <?php } else { ?>
                                            <img src="../../res/Briefcase.png" alt="Job Icon" class="alumni-card-header-img-job">
                                        <?php } ?>
                                        <div class="alumni-card-info">
                                            <h3><?php echo htmlspecialchars($entry['title']); ?></h3>
                                            <?php if ($entry['type'] === 'Event') { ?>
                                                <p>
                                                    <span class="event-date">
                                                        <img src="../../res/Calendar.png" alt="calendar img">
                                                        <?php echo htmlspecialchars($entry['date']); ?> |
                                                        <?php echo htmlspecialchars($entry['time']); ?>
                                                    </span>
                                                </p>
                                            <?php } else { ?>
                                                <p>
                                                    <span class="job-location">
                                                        <img src="../../res/mingcute_location-fill.png" alt="location img">
                                                        <?php echo htmlspecialchars($entry['location']); ?>
                                                    </span>
                                                </p>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="alumni-card-footer">
                                        <span class="interested-count">
                                            <img src="../../res/material-symbols_star.png" alt="star img">
                                            <span><?php echo htmlspecialchars($entry['interested_count']); ?></span>
                                        </span>
                                    </div>
                                </div>
                            <?php }
                        } else { ?>
                            <h2 class="no-data">No interested alumni found.</h2>
                        <?php } ?>
                        </ul>
                    </div>
                </div>


                <div class="bottom-row">
                    <div class="recent-managers">
                        <h2>Recent Managers</h2>
                        <table class="managers-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentManagersData)) {
                                    foreach ($recentManagersData as $manager) { ?>
                                        <tr>
                                            <td>
                                                <div class='manager-info'>
                                                    <img src='../../res/manager_pfp.png' alt='Avatar' class='alumni-avatar'>
                                                    <span><?php echo htmlspecialchars($manager['username']); ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="timestamp" data-timestamp="<?php echo htmlspecialchars($manager['joined']); ?>">

                                                </span>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="2" class="no-data">No recent managers found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="recent-alumni">
                        <h2>Recent Alumni</h2>
                        <table class="alumni-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentAlumniData)) {
                                    foreach ($recentAlumniData as $alumni) { 
                                        $profpicData = !empty($alumni['profpic']) ? 'data:image/jpeg;base64,' . base64_encode($alumni['profpic']) : '../../res/manager.png'; 
                                        ?>
                                        <tr>
                                            <td>
                                                <div class='alumni-info'>
                                                    <img src='<?php echo $profpicData; ?>' alt='Avatar' class='alumni-avatar'>
                                                    <span><?php echo htmlspecialchars($alumni['name']); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($alumni['location']); ?></td>
                                            <td>
                                                <span class="timestamp" data-timestamp="<?php echo htmlspecialchars($alumni['joined']); ?>">
                                                    <!-- Placeholder for formatted date -->
                                                </span>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="3" class="no-data">No recent alumni found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        </div>
        </div>

        </div>


    </body>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data for Employment Status
        const employmentData = {
            labels: ['Employed', 'Unemployed', 'Underemployed'],
            datasets: [{
                data: [
                    <?php echo $empstatusCounts['Employed']; ?>,
                    <?php echo $empstatusCounts['Unemployed']; ?>,
                    <?php echo $empstatusCounts['Underemployed']; ?>
                ],
                backgroundColor: ['#0059CD', '#41a1e7', '#99D2FF'],
                borderWidth: 1
            }]
        };

        // Employment Status Pie Chart
        new Chart(document.getElementById('employmentChart'), {
            type: 'doughnut',
            data: employmentData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            generateLabels: function(chart) {
                                const dataset = chart.data.datasets[0];
                                return chart.data.labels.map((label, index) => {
                                    const meta = chart.getDatasetMeta(0).data[index];
                                    return {
                                        text: `${label} (${dataset.data[index]})`,
                                        fillStyle: dataset.backgroundColor[index],
                                        hidden: meta.hidden, // Check if hidden
                                        textDecoration: meta.hidden ? 'line-through' : 'none', // Apply strikethrough if hidden
                                        index: index,
                                    };
                                });
                            },
                            usePointStyle: true, // Optional: For circle icons
                            font: {
                                size: 12,
                            },
                            color: '#333',
                            padding: 10, // Adjust padding between legend items
                        },
                        onClick: function(e, legendItem, legend) {
                            // Toggle visibility of dataset slice on click
                            const index = legendItem.index;
                            const chart = legend.chart;
                            chart.toggleDataVisibility(index);
                            chart.update();
                        },
                    },
                    title: {
                        display: true,
                        text: 'Employment Status',
                        font: {
                            size: 18,
                            weight: 'bold',
                        },
                        color: '#333333',
                    },
                },
            },
        });


        // Data for User Location
        const locationData = {
            labels: ['Domestic', 'Foreign'],
            datasets: [{
                data: [
                    <?php echo $locationCounts['Domestic']; ?>,
                    <?php echo $locationCounts['Foreign']; ?>
                ],
                backgroundColor: ['#2196F3', '#0059CD'],
                borderWidth: 1
            }]
        };

        // Location Pie Chart
        new Chart(document.getElementById('locationChart'), {
            type: 'doughnut',
            data: locationData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            generateLabels: function(chart) {
                                const dataset = chart.data.datasets[0];
                                return chart.data.labels.map((label, index) => {
                                    const meta = chart.getDatasetMeta(0).data[index];
                                    return {
                                        text: `${label} (${dataset.data[index]})`,
                                        fillStyle: dataset.backgroundColor[index],
                                        hidden: meta.hidden,
                                        textDecoration: meta.hidden ? 'line-through' : 'none',
                                        index: index,
                                    };
                                });
                            },
                            usePointStyle: true,
                            font: {
                                size: 12,
                            },
                            color: '#333',
                            padding: 10,
                        },
                        onClick: function(e, legendItem, legend) {
                            // Toggle visibility of dataset slice on click
                            const index = legendItem.index;
                            const chart = legend.chart;
                            chart.toggleDataVisibility(index);
                            chart.update();
                        },
                    },
                    title: {
                        display: true,
                        text: 'User Location',
                        font: {
                            size: 18,
                            weight: 'bold',
                        },
                        color: '#333333',
                    },
                },
            },
        });


        function formatTimestamp(timestamp) {
            const date = new Date(timestamp);

            const options = {
                year: 'numeric',
                month: 'long', // Full month name (e.g., December)
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                hour12: true, // 12-hour clock
            };

            return date.toLocaleString('en-US', options);
        }

        // Find all elements with the "data-timestamp" attribute
        document.querySelectorAll('.timestamp').forEach(element => {
            const rawTimestamp = element.getAttribute('data-timestamp'); // Get raw timestamp
            if (rawTimestamp) {
                const formattedTimestamp = formatTimestamp(rawTimestamp); // Format it
                element.textContent = formattedTimestamp; // Update the content
            }
        });
    </script>
    <script src="../js/contentmove.js"></script>

    </html>
<?php } else { ?>
    <h1 style='margin:auto;'>Access Forbidden</h1>
    <p>Please log in to your account.</p>
<?php } ?>