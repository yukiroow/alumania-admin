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

    // Directly use the password as it is (no hashing)
    $sqlInsert = "INSERT INTO user (userId, username, password, userType, jointimestamp)
                  VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlInsert);
    $stmt->bind_param('sssss', $newUserId, $username, $password, $userType, $joinTimestamp);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Manager added successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding manager: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
