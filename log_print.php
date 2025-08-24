<?php
header('Content-Type: application/json');
include 'db.php'; // Include database connection
session_start(); // Start session to access admin data

// Get the admin username from the session
$admin_name = isset($_SESSION['admin']) ? $_SESSION['admin'] : 'Unknown Admin'; // Fix: Use $_SESSION['admin']

// Get POST data
$request_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$document_type = isset($_POST['document_type']) ? $_POST['document_type'] : '';

if (!$request_id || !$document_type) {
    echo json_encode(['success' => false, 'message' => 'Invalid document ID or type.']);
    exit();
}

// Fetch the current print count
$sql = "SELECT print_count FROM $document_type WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();
$document = $result->fetch_assoc();
$stmt->close();

if (!$document) {
    echo json_encode(['success' => false, 'message' => 'Document not found.']);
    exit();
}

$print_count = $document['print_count'];
$print_limit = 5; // Set the print limit

if ($print_count >= $print_limit) {
    echo json_encode(['success' => false, 'message' => 'Print limit reached. You cannot print this document anymore.']);
    exit();
}

// Increment the print count
$new_print_count = $print_count + 1;
$updateSql = "UPDATE $document_type SET print_count = ? WHERE id = ?";
$stmt = $conn->prepare($updateSql);
$stmt->bind_param("ii", $new_print_count, $request_id);
$stmt->execute();
$stmt->close();

// Log the print action in the history table
$logSql = "INSERT INTO history (document_id, document_type, action, admin_name) VALUES (?, ?, ?, ?)";
$logStmt = $conn->prepare($logSql);
$action = "Printed document (Count: $new_print_count)";
$logStmt->bind_param("isss", $request_id, $document_type, $action, $admin_name);
$logStmt->execute();
$logStmt->close();

echo json_encode(['success' => true]);
?>