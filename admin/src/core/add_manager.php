<?php
require_once '../database/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Both username and password are required.']);
        exit;
    }

    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Check if username already exists
    $sqlCheckUsername = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($sqlCheckUsername);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'A user with this username already exists.']);
    } else {
        // Proceed with adding the manager
        $sqlGetMaxId = "SELECT userId FROM user ORDER BY userId DESC LIMIT 1;";
        $result = $conn->query($sqlGetMaxId);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastUserId = $row['userId'];
            $newUserId = sprintf("U%03d", substr($lastUserId, 1) + 1); 
        } else {
            $newUserId = 'U001'; 
        }

        $userType = 'Manager';
        $joinTimestamp = date('Y-m-d H:i:s');

        // Using prepared statements for secure data insertion
        $sqlInsert = "INSERT INTO user (userId, username, password, userType, jointimestamp)
                      VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sqlInsert);
        $stmt->bind_param('sssss', $newUserId, $username, $password, $userType, $joinTimestamp);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Manager added successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding manager: ' . $stmt->error]);
        }
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
