
<?php
include 'db.php';

// Define the admin account details
$username = "admin-golf";
$password = password_hash("password", PASSWORD_BCRYPT); // Default password
$admin_level = '2'; // Highest admin level

// Check if the admin account already exists
$sql = "SELECT * FROM admins WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Admin account with username '$username' already exists.";
} else {
    // Insert the new admin account
    $sql = "INSERT INTO admins (username, password, admin_level) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $password, $admin_level);

    if ($stmt->execute()) {
        echo "Admin account created successfully!<br>";
        echo "Username: $username<br>";
        echo "Password: password<br>";
    } else {
        echo "Error creating admin account: " . $conn->error;
    }
}

$stmt->close();
$conn->close();
?>