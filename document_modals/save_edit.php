<?php
header('Content-Type: application/json'); // Ensure the response is JSON
include '../db.php'; // Include database connection

session_start(); // Start the session to access admin data

// Get the admin username from the session
$admin_username = isset($_SESSION['admin']) ? $_SESSION['admin'] : null;

if (!$admin_username) {
    echo json_encode(['success' => false, 'message' => 'Admin not logged in.']);
    exit();
}

// Fetch the admin's username from the `admins` table
$adminSql = "SELECT username FROM admins WHERE username = ?";
$stmt = $conn->prepare($adminSql);
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$adminResult = $stmt->get_result();
$admin = $adminResult->fetch_assoc();
$stmt->close();

if (!$admin) {
    echo json_encode(['success' => false, 'message' => 'Admin not found.']);
    exit();
}

$admin_name = $admin['username']; // Use the admin's username

// Get POST data
$request_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$document_type = isset($_POST['document_type']) ? $_POST['document_type'] : '';
unset($_POST['id'], $_POST['document_type']); // Remove non-field data

// Debugging: Log the received data
error_log("Request ID: $request_id");
error_log("Document Type: $document_type");
error_log("POST Data: " . json_encode($_POST));

if (!$request_id || !$document_type || empty($_POST)) {
    error_log("Invalid input data: Request ID or Document Type is missing, or POST data is empty.");
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit();
}

// Fetch the current data for comparison
$currentDataSql = "SELECT * FROM $document_type WHERE id = ?";
$stmt = $conn->prepare($currentDataSql);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$currentData = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$currentData) {
    error_log("Record not found for Request ID: $request_id");
    echo json_encode(['success' => false, 'message' => 'Record not found.']);
    exit();
}

// Build the SQL dynamically for updates
$fields = [];
$values = [];
$changedFields = []; // Array to store the names of changed fields

foreach ($_POST as $field => $newValue) {
    $oldValue = $currentData[$field] ?? null;

    // Check if the value has changed
    if ($oldValue !== $newValue) {
        $fields[] = "$field = ?";
        $values[] = $newValue;
        $changedFields[] = $field; // Log the name of the changed field
    }
}

if (empty($fields)) {
    echo json_encode(['success' => true, 'message' => 'No changes detected.']);
    exit();
}

$values[] = $request_id; // Add the request ID for the WHERE clause

$sql = "UPDATE $document_type SET " . implode(', ', $fields) . " WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('s', count($values) - 1) . 'i', ...$values);

if ($stmt->execute()) {
    // Log the names of the changed fields in the history table
    $historySql = "INSERT INTO history (document_id, document_type, action, admin_name) VALUES (?, ?, ?, ?)";
    $historyStmt = $conn->prepare($historySql);

    foreach ($changedFields as $field) {
        $action = "Updated $field";
        $historyStmt->bind_param("isss", $request_id, $document_type, $action, $admin_name);
        $historyStmt->execute();
    }
    $historyStmt->close();

    echo json_encode(['success' => true, 'changed_fields' => $changedFields]);
} else {
    error_log("SQL Error: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Failed to update the record.']);
}
$stmt->close();
?>