<?php
session_start();
if (isset($_SESSION['username'])) { ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../res/styles/posts.css">
        <title>Alumania</title>
    </head>

    <body>
        <?php include 'navbar.php'; ?>

        <?php
        require_once '..\database\database.php';
        $db = \Database::getInstance()->getConnection();

        function getDefaultExperience($db): array
        {
            $experiences = [];
            $experience_query = "SELECT 
                e.xpid, e.body, e.publishtimestamp, e.userid,
                CONCAT(a.firstname, ' ', a.lastname) AS fullname,
                a.displaypic
                FROM experience e 
                JOIN alumni a ON e.userid = a.userid
                ORDER BY e.publishtimestamp DESC;";

            $experience_result = mysqli_query($db, $experience_query);

            if (mysqli_num_rows($experience_result) > 0) {
                while ($rowexperience = mysqli_fetch_assoc($experience_result)) {

                    $userImage = $rowexperience["displaypic"] ? 'data:image/jpeg;base64,' . base64_encode($rowexperience["displaypic"]) : null;

                    $experiences[] = [
                        "xpid" => $rowexperience["xpid"],
                        "body" => $rowexperience["body"],
                        "publishtimestamp" => $rowexperience["publishtimestamp"],
                        "userid" => $rowexperience["userid"],
                        "fullname" => $rowexperience["fullname"],
                        "userImage" => $userImage
                    ];
                }
            }

            return $experiences;
        }

        function getDefaultEvents($db): array
        {
            $events = [];
            $event_query = "SELECT 
            e.eventid, e.title, e.description, e.category,
            e.eventloc, e.eventtime, e.eventdate, e.eventphoto,
            (SELECT COUNT(*) FROM interestedinevent ie WHERE ie.eventid = e.eventid) AS interested
            FROM event e ORDER BY e.eventdate DESC, e.eventtime DESC;";
            $event_result = mysqli_query($db, $event_query);

            if (mysqli_num_rows($event_result) > 0) {
                while ($rowevent = mysqli_fetch_assoc($event_result)) {
                    $imageData = $rowevent["eventphoto"] ? 'data:image/jpeg;base64,' . base64_encode($rowevent["eventphoto"]) : null;
                    $events[] = [
                        "eventid" => $rowevent["eventid"],
                        "title" => $rowevent["title"],
                        "description" => $rowevent["description"],
                        "category" => $rowevent["category"],
                        "eventtime" => $rowevent["eventtime"],
                        "eventdate" => $rowevent["eventdate"],
                        "eventloc" => $rowevent["eventloc"],
                        "eventphoto" => $imageData,
                        "interested" => $rowevent["interested"]
                    ];
                }
            }

            return $events;
        }

        function getDefaultJobs($db)
        {
            $jobs = [];
            $job_query = "SELECT 
            jp.jobpid, jp.title, jp.location, jp.description, jp.publishtimestamp,
            jp.companyname, (SELECT COUNT(*) FROM interestedinjobpost ijp 
            WHERE ijp.jobpid = jp.jobpid) AS interested FROM jobpost jp
            ORDER BY jp.publishtimestamp DESC;";
            $job_result = mysqli_query($db, $job_query);
            if (mysqli_num_rows($job_result) > 0) {
                while ($rowjob = mysqli_fetch_assoc($job_result)) {
                    $jobs[] = [
                        "jobpid" => $rowjob["jobpid"],
                        "title" => $rowjob["title"],
                        "location" => $rowjob["location"],
                        "description" => $rowjob["description"],
                        "publishtimestamp" => $rowjob["publishtimestamp"],
                        "companyname" => $rowjob["companyname"],
                        "interested" => $rowjob["interested"]
                    ];
                }
            }
            return $jobs;
        }
        $jobpostings = getDefaultJobs($db);
        $eventpostings = getDefaultEvents($db);
        $experiencepostings = getDefaultExperience($db);
        ?>

        <main class="content-container">
            <div class="header">
                <h1>Posts</h1>
                <p>
                    Delete or edit a post
                </p>
            </div>

            <div id="searchContainer" class="search-container">
                <div class="event-search-bar">
                    <input type="text" class="event-search-input" placeholder="Event Name">
                    <button class="search-button">
                        <img src="../../res/search.png" alt="Search">
                    </button>
                </div>

                <div class="event-category-dropdown">
                    <button class="event-category-button" onclick="eventCategory()">
                        Category
                        <img src="../../res/arrow.png" alt="Dropdown Arrow" class="event-dropdown-arrow">
                    </button>
                    <div class="event-dropdown-content" id="categoryDropdown">
                        <button onclick="eventCategory()">Seminar</button>
                        <button onclick="eventCategory()">Thanksgiving</button>
                        <button onclick="eventCategory()">Festival</button>
                        <button onclick="eventCategory()">Reunion</button>
                    </div>
                </div>

                <button class="event-sort-button">
                    <img src="../../res/sort.png" alt="Sort">
                </button>
            </div>

            <div class="navigation">
                <ul id="ul-posts">
                    <li onclick="displayExperience(experiences)">
                        <img src="../../res/experience-posts.png" alt="User experience">
                        <p>Experience</p>
                    </li>
                    <li onclick="displayEvents(events)">
                        <img src="../../res/calendar-posts.png" alt="Events">
                        <p>Events</p>
                    </li>
                    <li onclick="displayJobs(jobs)">
                        <img src="../../res/jlisting-posts.png" alt="Listing">
                        <p>Job Listing</p>
                    </li>
                </ul>
            </div>

            <div id="card-experiences">

            </div>

            <div id="card-jobs">
            </div>

            <div id="card-events">
            </div>
        </main>

        <script src="../js/posts.js"></script>

        <script>
            let jobs = <?php echo json_encode($jobpostings); ?>;
            let events = <?php echo json_encode($eventpostings); ?>;
            let experiences = <?php echo json_encode($experiencepostings); ?>;
            let currentEvents = JSON.parse(JSON.stringify(events));
            let currentJobs = JSON.parse(JSON.stringify(jobs));
            let currentExperience = JSON.parse(JSON.stringify(experiences));

            function wipAlert() {
                alert('This section is currently being worked on :)');
            }

            function displayJobs(jobsData) {
                setActiveTab(2);
                document.getElementById("card-events").innerHTML = '';
                document.getElementById("card-experiences").innerHTML = '';
                const container = document.getElementById("card-jobs");
                container.innerHTML = '';

                for (let i = 0; i < jobsData.length; i++) {
                    const cardContainer = document.createElement('div');
                    cardContainer.id = jobsData[i].jobpid;
                    cardContainer.classList.add("joblisting");
                    cardContainer.innerHTML = `
                        <div class="listing-title">
                            <h3>${jobsData[i].title}</h3>
                        </div>
                        <div class="listing-com-loc">
                            <p>${jobsData[i].companyname}</p>
                            <p>${jobsData[i].location}</p>
                        </div>
                        <div class="listing-summary">
                            <p>${jobsData[i].description}</p>
                        </div>
                        <div class="job-interest-count">
                            <button class="delete-button" onclick="deletePost('${jobsData[i].jobpid}', 'job')">Delete</button>
                            <button class="view-interested-button" onclick="getInterestedUser('${jobsData[i].jobpid}', 'job')">View Interested</button>
                        </div>
                    `;
                    container.appendChild(cardContainer);
                }
            }

            function displayEvents(eventsData) {
                setActiveTab(1);
                document.getElementById("card-jobs").innerHTML = '';
                document.getElementById("card-experiences").innerHTML = '';
                const container = document.getElementById("card-events");
                container.innerHTML = ''; // Clear existing content

                for (let i = 0; i < eventsData.length; i++) {

                    const eventDate = new Date(eventsData[i].eventdate + "T" + eventsData[i].eventtime);
                    const formattedDate = eventDate.toLocaleDateString('en-US', {
                        month: 'long',
                        day: 'numeric',
                        year: 'numeric'
                    });
                    const formattedTime = eventDate.toLocaleTimeString('en-US', {
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });

                    const cardContainer = document.createElement('div');
                    cardContainer.id = eventsData[i].eventid;
                    cardContainer.classList.add("event-card");
                    const eventImage = eventsData[i].eventphoto || '../../res/event_placeholder.jpg';
                    cardContainer.innerHTML = `
                        <div class="event-card-image">
                            <img src="${eventImage}" alt="${eventsData[i].title}">
                        </div>
                        <div class="event-card-content">
                            <h2 class="event-title">${eventsData[i].title}</h2>
                            <div class="event-details">
                                <div class="event-date">${formattedDate}</div>
                                <div class="event-time">${formattedTime}</div>
                            </div>
                            <div class="event-location">${eventsData[i].eventloc}</div>
                            <div class="event-interest-count">
                                <button class="view-interested-button" onclick="getInterestedUser('${eventsData[i].eventid}', 'event')">Interested</button>
                                <button class="sponsors-button" onclick="showEventSponsors('${eventsData[i].eventid}')" , 'event')">Sponsors</button>
                                <button class="delete-button" onclick="deletePost('${eventsData[i].eventid}', 'event')">
                                    <img src="../../res/delete.png" alt="Delete Icon" class="delete-button-icon">
                                </button>
                            </div>
                        </div>
                    `;
                    container.appendChild(cardContainer);
                }
            }

            function displayExperience(experienceData) {
                setActiveTab(0);
                document.getElementById("card-events").innerHTML = '';
                document.getElementById("card-jobs").innerHTML = '';
                const container = document.getElementById("card-experiences");
                container.innerHTML = ''; // Clear existing content

                if (experienceData.length === 0) {
                    container.innerHTML = '<p>No experiences found.</p>';
                    return;
                }

                experienceData.forEach((exp) => {
                    const cardContainer = document.createElement('div');
                    cardContainer.id = exp.xpid; // Set ID to experience ID
                    cardContainer.classList.add("experience-card");

                    const formattedDate = new Date(exp.publishtimestamp).toLocaleString('en-US', {
                        month: 'long', // Full month name
                        day: 'numeric', // Day of the month
                        year: 'numeric', // Full year
                        hour: 'numeric', // Hour
                        minute: '2-digit', // Minutes
                        hour12: true // 12-hour clock
                    });

                    cardContainer.innerHTML = `
                        <div class="experience-header">
                            <img src="${exp.userImage || '../../res/account_circle.jpg'}" alt="User pic">
                            <div>
                                <h3 class="username">${exp.fullname}</h3>
                                <small class="experience-timestamp">${formattedDate}</small>
                            </div>
                        </div>
                        <div class="experience-body">
                            <p>${exp.body}</p>
                            <div class="experience-image"></div>
                        </div>
                        <div class="experience-details">
                            <img src="../../res/heartlike.png" alt="like button" style="margin-right:10px;">
                            <span class="like-count">0</span><span> Likes</span>
                            <button class="delete" onclick="deletePost('${exp.xpid}', 'experience')">Delete</button>
                        </div>
                    `;
                    container.appendChild(cardContainer);
                    const imageDiv = cardContainer.querySelector('.experience-image');
                    fetch(`fetchImages.php?xpid=${exp.xpid}`)
                        .then(response => response.json())
                        .then(images => {
                            images.forEach(img => {
                                const imgElement = document.createElement('img');
                                imgElement.src = `data:image/jpeg;base64,${img.base64Image}`;
                                imageDiv.appendChild(imgElement);
                            });
                        });

                    // Fetch and display like count for the experience
                    fetch(`fetchLikes.php?xpid=${exp.xpid}`)
                        .then(response => response.json())
                        .then(data => {
                            const likeSpan = cardContainer.querySelector('.like-count');
                            likeSpan.textContent = data.likes || 0;
                        });
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                if (typeof setActiveNav === 'function') {
                    setActiveNav("poststab", "postsicon", 5);
                }
                displayExperience(experiences);
            });

            function getInterestedUser(id, type) {
                fetch(`getInterestedUsers.php?type=${type}&id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            alert("No users are interested yet.");
                            return;
                        }

                        // Create popup content
                        let popupContent = `<div class="popup">
                <div class="popup-header">
                    <h3>No. of Interested: ${data.length} users</h3>
                    <button class="closePopup" onclick="closePopup()">X</button>
                </div>
                <div class="popup-body">
                    <ul>`;

                        data.forEach(user => {
                            popupContent += `<li>
                    <img src="${user.profilePic}" alt="User Image">
                    <span>${user.name}</span>
                    <span>${user.course}</span>
                </li><hr>`;
                        });

                        popupContent += `</ul></div></div>`;

                        // Append popup to the body and display the overlay
                        const popupContainer = document.createElement('div');
                        popupContainer.id = "interestedPopup";
                        popupContainer.classList.add("active"); 
                        popupContainer.innerHTML = popupContent;
                        document.body.appendChild(popupContainer);
                    })
                    .catch(error => {
                        console.error("Error fetching interested users:", error);
                        alert("Failed to fetch interested users.");
                    });
            }

            // Function to close the popup
            function closePopup() {
                const popup = document.getElementById("interestedPopup");
                if (popup) {
                    popup.classList.remove("active"); 
                    setTimeout(() => popup.remove(), 300); 
                }
            }

            function showEventSponsors(eventid) {
                fetch(`getEventSponsors.php?eventid=${eventid}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            alert("No sponsors for this event.");
                            return;
                        }

                        // Create popup content
                        let popupContent = `<div class="popup">
                            <div class="popup-header">
                                <h3>Sponsors for ${data[0].title}</h3>
                                <button class="closePopupBtn" onclick="closeEventPopup()">X</button>
                            </div>
                            <div class="popup-body">
                                <ul>`;
                        
                        data.forEach(sponsor => {
                            popupContent += `<li>
                                <span><strong>Name:</strong> ${sponsor.name}</span><br>
                                <span><strong>Type:</strong> ${sponsor.type}</span><br>
                                <span><strong>Amount:</strong> ${sponsor.amount}</span>
                            </li><hr>`;
                        });

                        popupContent += `</ul></div></div>`;

                        // Append popup to the body and display the overlay
                        const popupContainer = document.createElement('div');
                        popupContainer.id = "eventPopup";
                        popupContainer.classList.add("active");
                        popupContainer.innerHTML = popupContent;
                        document.body.appendChild(popupContainer);
                    })
                    .catch(error => {
                        console.error("Error fetching event sponsors:", error);
                        alert("Failed to fetch event sponsors.");
                    });
            }

            // Function to close the popup
            function closeEventPopup() {
                const popup = document.getElementById("eventPopup");
                if (popup) {
                    popup.classList.remove("active");
                    setTimeout(() => popup.remove(), 300);
                }
            }

            function deletePost(id, type) {
                if (!confirm(`Are you sure you want to delete this ${type}?`)) return;

                fetch(`deletePost.php`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id,
                            type
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(`${type.charAt(0).toUpperCase() + type.slice(1)} deleted successfully.`);
                            location.reload(); // Reload the page to update the list
                        } else {
                            alert(`Failed to delete ${type}: ${data.error || 'Unknown error'}`);
                        }
                    })
                    .catch(error => {
                        console.error("Error deleting item:", error);
                        alert("An error occurred while deleting the item.");
                    });
            }
        </script>
        <script src="../js/contentmove.js"></script>
    </body>

    </html>
<?php } else { ?>
    <h1 style='margin:auto;'>Access Forbidden</h1>
    <p>Please log in to your account.</p>
<?php } ?>