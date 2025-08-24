<?php
session_start();

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

include 'db.php'; // Include database connection

// Get the reservation ID from the POST request
$reservationId = $_POST['id'] ?? null;

if (!$reservationId) {
    echo json_encode(['success' => false, 'message' => 'Reservation ID is required.']);
    exit();
}

// Get the admin username from the session
$approvedBy = $_SESSION['admin'];
$timeApproved = date('Y-m-d H:i:s');

// Update the reservation status to "approved"
$sql = "UPDATE facilities_reservations 
        SET status = 'approved', approved_by = ?, time_approved = ? 
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ssi', $approvedBy, $timeApproved, $reservationId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Reservation approved successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to approve reservation.']);
}

$stmt->close();
$conn->close();
?>