<?php
session_start();
include 'db.php';

// Check if the request is valid
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['status'], $data['table'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request data.']);
    exit();
}

$id = $data['id'];
$status = $data['status'];
$table = $data['table'];
$reason = isset($data['reason']) ? $data['reason'] : null;
$pickupSchedule = isset($data['pickup_schedule']) ? $data['pickup_schedule'] : null;
$adminUsername = $_SESSION['admin']; // Get the admin's username from the session

// Prepare the SQL query
if ($status === 'approved') {
    $timeApproved = date('Y-m-d H:i:s'); // Current timestamp
    $sql = "UPDATE $table SET status = ?, approved_by = ?, time_approved = ?, pickup_schedule = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $status, $adminUsername, $timeApproved, $pickupSchedule, $id);
} elseif ($status === 'rejected') {
    $timeRejected = date('Y-m-d H:i:s'); // Current timestamp
    $sql = "UPDATE $table SET status = ?, rejection_reason = ?, rejected_by = ?, time_rejected = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $status, $reason, $adminUsername, $timeRejected, $id);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid status.']);
    exit();
}

// Execute the query
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>