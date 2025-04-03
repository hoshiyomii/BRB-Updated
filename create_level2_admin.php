<?php
include 'db.php';

$username = 'superadmin';
$password = password_hash('superpassword', PASSWORD_BCRYPT);
$admin_level = '2';

$sql = "INSERT INTO admins (username, password, admin_level) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $password, $admin_level);

if ($stmt->execute()) {
    echo "Level 2 admin account created successfully.";
} else {
    echo "Error creating level 2 admin account: " . $stmt->error;
}
?>