<?php
session_start();

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

include 'db.php'; // Include database connection

// Get the reservation ID and rejection reason from the POST request
$reservationId = $_POST['id'] ?? null;
$rejectionReason = $_POST['rejection_reason'] ?? null;

if (!$reservationId || !$rejectionReason) {
    echo json_encode(['success' => false, 'message' => 'Reservation ID and rejection reason are required.']);
    exit();
}

// Get the admin username from the session
$rejectedBy = $_SESSION['admin'];
$timeRejected = date('Y-m-d H:i:s');

// Update the reservation status to "rejected"
$sql = "UPDATE facilities_reservations 
        SET status = 'rejected', rejected_by = ?, rejection_reason = ?, time_rejected = ? 
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('sssi', $rejectedBy, $rejectionReason, $timeRejected, $reservationId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Reservation rejected successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to reject reservation.']);
}

$stmt->close();
$conn->close();
?>