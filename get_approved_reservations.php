<?php
// get_approved_reservations.php
header('Content-Type: application/json');
include 'db.php';

$venue = isset($_GET['venue_type']) ? $_GET['venue_type'] : (isset($_GET['facility_type']) ? $_GET['facility_type'] : '');
$date = isset($_GET['date']) ? $_GET['date'] : '';

if (!$venue || !$date) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters.']);
    exit;
}

// Get all approved reservations for this venue and date
if (isset($_GET['facility_type'])) {
    $stmt = $conn->prepare("SELECT start_time, end_time, approved_by FROM facilities_reservations WHERE status = 'approved' AND facility_type = ? AND DATE(start_time) = ? ORDER BY start_time");
    $stmt->bind_param('ss', $venue, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservations = [];
    while ($row = $result->fetch_assoc()) {
        $reservations[] = [
            'start_time' => $row['start_time'],
            'end_time' => $row['end_time'],
            'approved_by' => $row['approved_by']
        ];
    }
    echo json_encode(['success' => true, 'reservations' => $reservations]);
} else {
    $stmt = $conn->prepare("SELECT start_time, end_time, approved_by FROM reservations WHERE status = 'approved' AND venue_type = ? AND DATE(start_time) = ? ORDER BY start_time");
    $stmt->bind_param('ss', $venue, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservations = [];
    while ($row = $result->fetch_assoc()) {
        $reservations[] = [
            'start_time' => $row['start_time'],
            'end_time' => $row['end_time'],
            'approved_by' => $row['approved_by']
        ];
    }
    echo json_encode(['success' => true, 'reservations' => $reservations]);
}
