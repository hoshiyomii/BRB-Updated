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
    $rejectionReason = $_POST['rejection_reason'];

    if (!$reservationId || !$rejectionReason) {
        error_log("Reservation ID or rejection reason is missing.", 3, "reservation_logs.txt");
        echo json_encode(['success' => false, 'message' => 'Reservation ID and rejection reason are required.']);
        exit();
    }

    $adminName = $_SESSION['admin']; // Assuming admin name is stored in session

    $sql = "UPDATE reservations SET status = 'rejected', rejection_reason = ?, rejected_by = ?, time_rejected = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Failed to prepare statement: " . $conn->error, 3, "reservation_logs.txt");
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param('ssi', $rejectionReason, $adminName, $reservationId);

    // Log the values being passed
    error_log("Admin Name: $adminName, Reservation ID: $reservationId, Rejection Reason: $rejectionReason", 3, "reservation_logs.txt");

    if ($stmt->execute()) {
        error_log("Reservation rejected successfully for ID: $reservationId", 3, "reservation_logs.txt");
        echo json_encode(['success' => true, 'message' => 'Reservation rejected successfully.']);
    } else {
        error_log("Failed to reject reservation: " . $stmt->error, 3, "reservation_logs.txt");
        echo json_encode(['success' => false, 'message' => 'Failed to reject reservation: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>