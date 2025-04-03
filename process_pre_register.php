<?php
session_start();
include 'db_connection.php';

// DEBUG: Show session data
var_dump($_SESSION);

if (!isset($_SESSION['user_id'])) {
    echo "DEBUG: not_logged_in";
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'UNKNOWN';

// Fetch user email from database
$email_query = "SELECT email FROM users WHERE id = ?";
$email_stmt = $conn->prepare($email_query);
$email_stmt->bind_param("i", $user_id);
$email_stmt->execute();
$email_stmt->bind_result($email);
$email_stmt->fetch();
$email_stmt->close();

if (empty($email)) {
    echo "DEBUG: error_missing_email - Email not found in database";
    exit();
}

echo "DEBUG: User ID = $user_id, Username = $username, Email = $email <br>";

// Get the event ID from POST request
$announcement_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;

if ($announcement_id <= 0) {
    echo "DEBUG: invalid_event - event_id is empty or not a number (Received: " . $_POST['event_id'] . ")";
    exit();
}

// Check if the user is already registered
$check_sql = "SELECT id FROM registrations WHERE user_id = ? AND announcement_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $user_id, $announcement_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo "DEBUG: already_registered";
    exit();
}
$check_stmt->close();

// Check current participant count
$count_sql = "SELECT registered_participants, max_participants FROM announcements WHERE id = ?";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("i", $announcement_id);
$count_stmt->execute();
$count_stmt->bind_result($registered_participants, $max_participants);
$count_stmt->fetch();
$count_stmt->close();

if ($registered_participants >= $max_participants) {
    echo "DEBUG: event_full";
    exit();
}

// Insert the new registration
$insert_sql = "INSERT INTO registrations (announcement_id, user_id, name, email) 
               VALUES (?, ?, ?, ?)";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("iiss", $announcement_id, $user_id, $username, $email);

if ($insert_stmt->execute()) {
    // Update the participant count
    $update_sql = "UPDATE announcements SET registered_participants = registered_participants + 1 WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $announcement_id);
    $update_stmt->execute();
    $update_stmt->close();

    echo "DEBUG: success";
} else {
    echo "DEBUG: error_inserting - " . $conn->error;
}

$insert_stmt->close();
$conn->close();
?>
