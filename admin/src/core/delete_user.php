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
    $userId = $_POST['userId'];

    $sql = "DELETE FROM user WHERE userid = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $userId);  // Bind parameter as string
        if ($stmt->execute()) {
            echo "Alumni deleted successfully";
        } else {
            echo "Error executing query: " . $stmt->error;  // Detailed error
        }
        $stmt->close();
    } else {
        echo "Database error";
    }
} else {
    http_response_code(405);
    echo "Invalid request method";
}
