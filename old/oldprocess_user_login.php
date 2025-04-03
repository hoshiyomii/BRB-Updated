<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, email FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email']; // Ensure this is stored

        // Debugging session storage
        error_log("DEBUG: Login successful - Session Data: " . print_r($_SESSION, true));

        echo "success";
    } else {
        error_log("DEBUG: Login failed - Invalid credentials for username: " . $username);
        echo "invalid_credentials";
    }
}

?>
