<?php
header('Content-Type: application/json');
include 'db.php';

$sql = "SELECT id, registration_open_until FROM announcements WHERE type = 'event' AND is_active = 1";
$result = $conn->query($sql);

$statuses = [];
$now = new DateTime();

while ($row = $result->fetch_assoc()) {
    $registrationOpenUntil = new DateTime($row['registration_open_until']);
    $isActive = $now->format('Y-m-d') <= $registrationOpenUntil->format('Y-m-d'); // Compare only the date parts
    $statuses[] = [
        'id' => $row['id'],
        'is_active' => $isActive
    ];
}

echo json_encode($statuses);
?>