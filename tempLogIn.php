<?php
include 'db.php';

$username = "admin"; // Change this if needed
$password = password_hash("password123", PASSWORD_DEFAULT); // Change password

$sql = "INSERT INTO admins (username, password) VALUES ('$username', '$password')";
if ($conn->query($sql) === TRUE) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . $conn->error;
}
?>
