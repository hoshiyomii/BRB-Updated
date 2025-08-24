<?php
header('Content-Type: application/json'); // Ensure the response is JSON
include 'db.php'; // Include database connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log errors to a file for debugging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/fetch_history_error.log');

// Get the document ID and type
$document_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$document_type = isset($_GET['document_type']) ? $_GET['document_type'] : '';

if (!$document_id || !$document_type) {
    echo json_encode(['error' => 'Invalid document ID or type.']);
    exit();
}

// Fetch the history logs
$sql = "SELECT action, admin_name, timestamp 
        FROM history 
        WHERE document_id = ? AND document_type = ? 
        ORDER BY timestamp DESC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('Failed to prepare statement: ' . $conn->error);
    echo json_encode(['error' => 'Failed to prepare statement.']);
    exit();
}

$stmt->bind_param("is", $document_id, $document_type);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    error_log('Failed to execute query: ' . $stmt->error);
    echo json_encode(['error' => 'Failed to execute query.']);
    exit();
}

$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}

echo json_encode($history);
$stmt->close();
?>