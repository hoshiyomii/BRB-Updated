<?php
session_start();

if (!isset($_SESSION['admin'])) {
    error_log("Admin session is not set.", 3, "reservation_logs.txt");
    echo json_encode(['success' => false, 'message' => 'Admin session is not set.']);
    exit();
}

include 'db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservationId = $_POST['id'];

    if (!$reservationId) {
        error_log("Reservation ID is missing.", 3, "reservation_logs.txt");
        echo json_encode(['success' => false, 'message' => 'Reservation ID is required.']);
        exit();
    }

    $adminName = $_SESSION['admin']; // Assuming admin name is stored in session

    $sql = "UPDATE reservations SET status = 'approved', approved_by = ?, time_approved = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Failed to prepare statement: " . $conn->error, 3, "reservation_logs.txt");
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param('si', $adminName, $reservationId);

    // Log the values being passed
    error_log("Admin Name: $adminName, Reservation ID: $reservationId", 3, "reservation_logs.txt");

    if ($stmt->execute()) {
        error_log("Reservation approved successfully for ID: $reservationId", 3, "reservation_logs.txt");
        echo json_encode(['success' => true, 'message' => 'Reservation approved successfully.']);
    } else {
        error_log("Failed to approve reservation: " . $stmt->error, 3, "reservation_logs.txt");
        echo json_encode(['success' => false, 'message' => 'Failed to approve reservation: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>