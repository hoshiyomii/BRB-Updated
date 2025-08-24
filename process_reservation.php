<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to make a reservation.']);
    exit();
}

// Fetch user information
$username = $_SESSION['username'];
$user_query = $conn->query("SELECT * FROM users WHERE username = '$username'");
$user = $user_query->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch form data
    $user_id = $user['id'];
    $venue_type = $_POST['venue_type'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $is_big_group = isset($_POST['is_big_group']) ? 1 : 0;
    $security_option = isset($_POST['security_option']) ? 1 : 0;
    $caretaker_option = isset($_POST['caretaker_option']) ? 1 : 0;

    // Calculate total hours
    $start = new DateTime($start_time);
    $end = new DateTime($end_time);
    $hours = ceil(($end->getTimestamp() - $start->getTimestamp()) / 3600);

    // Validate hours for big groups
    if ($is_big_group && $hours < 4) {
        echo json_encode(['success' => false, 'message' => 'Big group reservations must be at least 4 hours.']);
        exit();
    }

    // Calculate total cost
    $total_cost = 0;

    if ($is_big_group) {
        $extra_hours = $hours > 4 ? $hours - 4 : 0;
        $power_supply_fee = $hours * 100;

        $total_cost = 4000 + 1000 + ($extra_hours * 1000) + $power_supply_fee;
        if ($security_option) $total_cost += 300;
        if ($caretaker_option) $total_cost += 200;
    } else {
        $rate = $venue_type === 'Court A' ? 100 : 200;
        $total_cost = $rate * $hours;
    }

    // Insert reservation into the database
    $stmt = $conn->prepare("INSERT INTO reservations (user_id, venue_type, start_time, end_time, is_big_group, security_option, caretaker_option, total_cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssiiii", $user_id, $venue_type, $start_time, $end_time, $is_big_group, $security_option, $caretaker_option, $total_cost);

    if ($stmt->execute()) {
        $reservation_id = $stmt->insert_id; // Get the inserted reservation ID
        $control_number = 'RSV-' . str_pad($reservation_id, 3, '0', STR_PAD_LEFT); // Add prefix and pad with zeros
        echo json_encode([
            'success' => true,
            'reservation_id' => $reservation_id, // Numeric ID for internal use
            'control_number' => $control_number // Formatted for display
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save reservation.']);
    }

    $stmt->close();
    $conn->close();
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}
?>