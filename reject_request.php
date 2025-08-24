<?php
include 'db.php';
session_start(); // Ensure the session is started

// Get POST data
$request_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$document_type = isset($_POST['document_type']) ? $_POST['document_type'] : '';
$rejection_reason = isset($_POST['rejection_reason']) ? $_POST['rejection_reason'] : '';

if (!$request_id || !$document_type || !$rejection_reason) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit();
}

// Ensure the admin is logged in
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Get the admin's username from the session
$admin_username = $_SESSION['admin'];

// Update the request status to "rejected" and set the rejection reason
$sql = "UPDATE $document_type SET status = 'rejected', rejection_reason = ?, time_rejected = NOW(), rejected_by = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssi', $rejection_reason, $admin_username, $request_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();