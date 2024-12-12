<?php

session_start();
require_once '..\database\database.php';
#to get last id
function getNextEventID($db) {
    
    $query = "SELECT eventid FROM event ORDER BY CAST(SUBSTRING(eventid, 2) AS UNSIGNED) DESC LIMIT 1";
    
    $result = $db->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        
        $lastEventID = $row['eventid'];
        $numPart = (int) substr($lastEventID, 1); 
        $nextNum = $numPart + 1; 
    } else {
        
        $nextNum = 1;
    }

    
    return 'E' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventTitle = htmlspecialchars($_POST['eventTitle']);
    $description = htmlspecialchars($_POST['description']);
    $location = htmlspecialchars($_POST['location']);
    $category = $_POST['category'];
    $schedule = $_POST['schedule'];

    $eventDate = date('Y-m-d', strtotime($schedule));
    $eventTime = date('H:i:s', strtotime($schedule));
    $userId = '7777'; 

    if (isset($_FILES['eventPhoto']) && $_FILES['eventPhoto']['error'] == UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['eventPhoto']['tmp_name']);
        $imageType = $_FILES['eventPhoto']['type'];

        if (!in_array($imageType, ['image/jpeg', 'image/png', 'image/gif'])) {
            die("Invalid image format. Please upload JPEG, PNG, or GIF.");
        }

        $db = \Database::getInstance()->getConnection();

        $eventId = getNextEventID($db);

        try {
            $query = "INSERT INTO event (eventid, title, description, category, eventtime, eventdate, eventloc, eventphoto, publishtimestamp, userid)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

            if ($stmt = $db->prepare($query)) {
                $stmt->bind_param(
                    "sssssssss",
                    $eventId,
                    $eventTitle,
                    $description,
                    $category,
                    $eventTime,
                    $eventDate,
                    $location,
                    $imageData,
                    $userId
                );

                if ($stmt->execute()) {
                    echo "Event created successfully!";
                } else {
                    echo "Failed to create event.";
                }
                $stmt->close();
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        $db->close();
    } else {
        echo "No image uploaded or an error occurred.";
    }
}