<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid resident ID']);
    exit();
}

$residentId = intval($_GET['id']);

// Prepare and execute query
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $residentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Resident not found']);
    exit();
}

// Fetch resident data
$resident = $result->fetch_assoc();

// Return as JSON
header('Content-Type: application/json');
echo json_encode($resident);
