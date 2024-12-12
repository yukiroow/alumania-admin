<?php
session_start();

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

require_once '../database/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentUsername = $_POST['currentUsername'];
    $newUsername = $_POST['username'];
    $password = $_POST['password'];

    if (empty($currentUsername) || empty($newUsername) || empty($password)) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }

    $sql = "UPDATE user SET username = ?, password = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $newUsername, $password, $currentUsername);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Manager updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update manager."]);
    }

    $stmt->close();
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
