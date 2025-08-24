<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: user_login.php');
    exit();
}

$username = $_SESSION['username'];

// Fetch current user data
$userQuery = $conn->prepare("SELECT * FROM users WHERE username = ?");
$userQuery->bind_param("s", $username);
$userQuery->execute();
$userResult = $userQuery->get_result();
$userData = $userResult->fetch_assoc();

if (!$userData) {
    $_SESSION['profile_error'] = "User not found.";
    header('Location: dashboard.php');
    exit();
}

$userId = $userData['id'];

// Collect and sanitize POST data
$new_username   = trim($_POST['username']);
$email          = trim($_POST['email']);
$first_name     = trim($_POST['first_name']);
$middle_name    = trim($_POST['middle_name']);
$last_name      = trim($_POST['last_name']);
$phone_number   = trim($_POST['phone_number']);
$birthdate      = $_POST['birthdate'];
$gender         = $_POST['gender'];
$street         = $_POST['street'];
$blood_type     = $_POST['blood_type'];
$lot_block      = trim($_POST['lot_block']);
$house_number   = trim($_POST['house_number']);
$password       = $_POST['password'];

// Validation
$errors = [];

// Username: 5-16 chars
if (strlen($new_username) < 5 || strlen($new_username) > 16) {
    $errors[] = "Username must be 5-16 characters.";
}

// Email: valid format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email address.";
}

// Age: must be at least 13
$age = (int) ((time() - strtotime($birthdate)) / (365.25*24*60*60));
if ($age < 13) {
    $errors[] = "You must be at least 13 years old.";
}

// Password: if provided, must be 8-32 chars
if (!empty($password) && (strlen($password) < 8 || strlen($password) > 32)) {
    $errors[] = "Password must be 8-32 characters.";
}

// Optionally, check for duplicate username/email (excluding current user)
$checkQuery = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
$checkQuery->bind_param("ssi", $new_username, $email, $userId);
$checkQuery->execute();
$checkQuery->store_result();
if ($checkQuery->num_rows > 0) {
    $errors[] = "Username or email already taken.";
}

// If errors, redirect back with error message
if (!empty($errors)) {
    $_SESSION['profile_error'] = implode("<br>", $errors);
    header('Location: dashboard.php');
    exit();
}

// Password validation
if (!empty($_POST['new_password']) || !empty($_POST['confirm_new_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    if (strlen($new_password) < 8 || strlen($new_password) > 32) {
        $errors[] = "New password must be 8-32 characters.";
    } elseif ($new_password !== $confirm_new_password) {
        $errors[] = "New password and confirm password do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    }
}

// If there are errors, redirect back to the dashboard with error messages
if (!empty($errors)) {
    $_SESSION['profile_error'] = implode("<br>", $errors);
    header("Location: dashboard.php");
    exit();
}

// Update query (password only if provided)
if (!empty($new_password)) {
    $updateQuery = $conn->prepare("UPDATE users SET username=?, email=?, first_name=?, middle_name=?, last_name=?, phone_number=?, birthdate=?, gender=?, street=?, blood_type=?, lot_block=?, house_number=?, password=? WHERE id=?");
    $updateQuery->bind_param("sssssssssssssi", $new_username, $email, $first_name, $middle_name, $last_name, $phone_number, $birthdate, $gender, $street, $blood_type, $lot_block, $house_number, $hashed_password, $userId);
} else {
    $updateQuery = $conn->prepare("UPDATE users SET username=?, email=?, first_name=?, middle_name=?, last_name=?, phone_number=?, birthdate=?, gender=?, street=?, blood_type=?, lot_block=?, house_number=? WHERE id=?");
    $updateQuery->bind_param("ssssssssssssi", $new_username, $email, $first_name, $middle_name, $last_name, $phone_number, $birthdate, $gender, $street, $blood_type, $lot_block, $house_number, $userId);
}


// After successful update
if ($updateQuery->execute()) {
    $_SESSION['profile_success'] = "Profile updated successfully!";
    $_SESSION['username'] = $new_username;

    // --- Audit log: Profile update ---
    $changedFields = [];
    foreach ($_POST as $field => $value) {
        // Skip password fields in the loop
        if ($field !== 'new_password' && $field !== 'confirm_new_password' && isset($userData[$field]) && $userData[$field] != $value) {
            $changedFields[] = $field;
        }
    }

    // Specifically check for password update
    if (!empty($_POST['new_password']) && !empty($_POST['confirm_new_password']) && $_POST['new_password'] === $_POST['confirm_new_password']) {
        $changedFields[] = 'password';
    }
    
    $actionType = 'profile_update';
    $actionDetails = "Changed fields: " . implode(', ', $changedFields);

    $log = $conn->prepare("INSERT INTO user_audit_logs (user_id, action_type, action_details, created_at) VALUES (?, ?, ?, NOW())");
    $log->bind_param("iss", $userId, $actionType, $actionDetails);
    $log->execute();
} else {
    $_SESSION['profile_error'] = "Failed to update profile. Please try again.";
}

header('Location: dashboard.php');
exit();