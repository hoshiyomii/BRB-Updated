<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Prepare search and filter conditions
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$verificationFilter = isset($_GET['verification']) ? $_GET['verification'] : 'all';

// Build SQL conditions
$conditions = [];
$params = [];

if (!empty($searchTerm)) {
    $conditions[] = "(first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone_number LIKE ? OR street LIKE ?)";
    $searchPattern = "%$searchTerm%";
    for ($i = 0; $i < 6; $i++) {
        $params[] = $searchPattern;
    }
}

if ($verificationFilter !== 'all') {
    $conditions[] = "is_verified = ?";
    $params[] = ($verificationFilter === 'verified') ? 1 : 0;
}

$whereClause = '';
if (!empty($conditions)) {
    $whereClause = "WHERE " . implode(" AND ", $conditions);
}

// Fetch residents
$sql = "SELECT * FROM users $whereClause ORDER BY date_registered DESC";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=residents_export_' . date('Y-m-d') . '.csv');

// Create a file handle for output
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel compatibility with special characters
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Output the column headings
fputcsv($output, [
    'ID',
    'Username',
    'First Name',
    'Middle Name',
    'Last Name',
    'Gender',
    'Phone Number',
    'Email',
    'Birthdate',
    'Blood Type',
    'Street',
    'Lot/Block',
    'House Number',
    'Date Registered',
    'Verification Status'
]);

// Output each resident
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['username'],
        $row['first_name'],
        $row['middle_name'],
        $row['last_name'],
        $row['gender'],
        $row['phone_number'],
        $row['email'],
        $row['birthdate'],
        $row['blood_type'] ?: 'Not provided',
        $row['street'],
        $row['lot_block'] ?: 'Not provided',
        $row['house_number'],
        $row['date_registered'],
        $row['is_verified'] ? 'Verified' : 'Not Verified'
    ]);
}

// Log the export action
$adminUsername = $_SESSION['admin'];
$details = "Exported resident list";
if (!empty($searchTerm)) {
    $details .= " with search term: '$searchTerm'";
}
if ($verificationFilter !== 'all') {
    $details .= " filtered by status: '$verificationFilter'";
}

// Try to log the export action but don't interrupt the export if it fails
try {
    $logSql = "INSERT INTO audit_logs (action, details, performed_by) VALUES ('EXPORT_RESIDENTS', ?, ?)";
    $logStmt = $conn->prepare($logSql);
    if ($logStmt) {
        $logStmt->bind_param("ss", $details, $adminUsername);
        $logStmt->execute();
    }
} catch (Exception $e) {
    // Log error to file instead of displaying to user
    error_log("Failed to log export action: " . $e->getMessage(), 0);
}

fclose($output);
exit();
