<?php
session_start();

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    echo "Unauthorized access";
    exit;
}

require_once '../database/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['newPassword'];

    if (strlen($newPassword) < 6) {
        echo "Password must be at least 6 characters long.";
        exit;
    }
    $sql = "UPDATE user SET password = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ss", $newPassword, $_SESSION['username']); // Assuming username is stored in session
        if ($stmt->execute()) {
            echo "success: Password changed successfully.";
        } else {
            echo "Error updating password: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Database error";
    }
} else {
    http_response_code(405);
    echo "Invalid request method";
}
?>
