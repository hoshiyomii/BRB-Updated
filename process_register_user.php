<?php
include 'db_connection.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash password
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $gender = $_POST["gender"];
    $phone_number = trim($_POST["phone_number"]);
    $email = trim($_POST["email"]);
    $birthdate = $_POST["birthdate"];
    $street = trim($_POST["street"]);
    $house_number = trim($_POST["house_number"]);

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo "Username or Email already exists!";
        exit();
    }
    $stmt->close();

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (username, password, first_name, last_name, gender, phone_number, email, birthdate, street, house_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $username, $password, $first_name, $last_name, $gender, $phone_number, $email, $birthdate, $street, $house_number);

    if ($stmt->execute()) {
        header("Location: user_login.php"); // Redirect after successful registration
        exit(); // Stop script execution
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

