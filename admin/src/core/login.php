<?php
require_once '..\database\database.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $db = \Database::getInstance()->getConnection();

    $query = "SELECT userid, usertype FROM user WHERE username = ? AND password = ? LIMIT 1";

    if ($stmt = $db->prepare($query)) {
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) < 1) {
            echo 'Invalid Credentials';
            exit();
        }

        while ($row = $result->fetch_assoc()) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $row['usertype'];
            $_SESSION['userid'] = $row['userid'];
            echo 'Login Successful';
        }
    }
}