<?php
require_once '..\database\database.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_type = "admin";

    $db = \Database::getInstance()->getConnection();

    $query = "SELECT password FROM user WHERE username = ? AND usertype = ? LIMIT 1";

    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param('ss', $username, $user_type);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            if ($password === $row['password']) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'admin';
                echo 'Login Successful';
                die();
            }
        }
    }

    echo 'Invalid Credentials';
    // header('Location: ../../index.php');
    exit();
}