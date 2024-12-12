<?php

session_start();
require_once '..\database\database.php';

#to get last id
function getNextJobID($db) {
    $query = "SELECT jobpid FROM jobpost ORDER BY CAST(SUBSTRING(jobpid, 3) AS UNSIGNED) DESC LIMIT 1";
    
    $result = $db->query($query);

    if ($result && $row = $result->fetch_assoc()) {
        $lastJobID = $row['jobpid'];
        $numPart = (int) substr($lastJobID, 2);
        $nextNum = $numPart + 1; 
    } else {
        $nextNum = 1;
    }

    return 'JP' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $jobTitle = htmlspecialchars($_POST['jobTitle']);
    $description = htmlspecialchars($_POST['description']);
    $location = htmlspecialchars($_POST['location']);
    $company = htmlspecialchars($_POST['company']); 
    $contactName = htmlspecialchars($_POST['contactName']);
    $contactEmail = htmlspecialchars($_POST['contactEmail']);
    $contactNumber = htmlspecialchars($_POST['contactNumber']);
    $jobCategory = htmlspecialchars($_POST['jobCategory']); 
  
    $db = Database::getInstance()->getConnection();

    $jobId = getNextJobID($db);

    $userId = 'U010';

    try {
        $query = "INSERT INTO jobpost (jobpid, title, type, location, description, companyname, contactname, contactemail, contactnumber, publishtimestamp, userid) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

        if ($stmt = $db->prepare($query)) {
            
            $stmt->bind_param("ssssssssss", $jobId, $jobTitle, $jobCategory, $location, $description, $company, $contactName, $contactEmail, $contactNumber, $userId);

            if ($stmt->execute()) {
                
                echo "Job post created successfully!";
            } else {
                echo "Failed to create job post." . $stmt->error;
            }
            
            $stmt->close();
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    $db->close();
}
?>