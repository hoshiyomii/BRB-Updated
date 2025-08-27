<?php
// check_reservation_conflict.php
header('Content-Type: application/json');
include 'db.php';

$venue = isset($_GET['venue_type']) ? $_GET['venue_type'] : (isset($_GET['facility_type']) ? $_GET['facility_type'] : '');
$start = isset($_GET['start_time']) ? $_GET['start_time'] : '';
$end = isset($_GET['end_time']) ? $_GET['end_time'] : '';

if (!$venue || !$start || !$end) {
    echo json_encode(['conflict' => false, 'error' => 'Missing parameters.']);
    exit;
}

// Determine which table to check based on parameter
if (isset($_GET['facility_type'])) {
    // Facilities reservation form: check facilities_reservations table
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM facilities_reservations WHERE status = 'approved' AND facility_type = ? AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?) OR (start_time >= ? AND end_time <= ?))");
    $stmt->bind_param('sssssss', $venue, $end, $start, $end, $start, $start, $end);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['cnt'] > 0) {
        echo json_encode(['conflict' => true]);
    } else {
        echo json_encode(['conflict' => false]);
    }
} else {
    // Sports venue reservation form: check reservations table
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM reservations WHERE status = 'approved' AND venue_type = ? AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?) OR (start_time >= ? AND end_time <= ?))");
    $stmt->bind_param('sssssss', $venue, $end, $start, $end, $start, $start, $end);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['cnt'] > 0) {
        echo json_encode(['conflict' => true]);
    } else {
        echo json_encode(['conflict' => false]);
    }
}
