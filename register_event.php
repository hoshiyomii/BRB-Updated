<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$eventId = $data['eventId'];
$name = $data['name'];
$email = $data['email'];

// Check event capacity
$sql = "SELECT max_participants, registered_participants FROM announcements WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if ($event['registered_participants'] >= $event['max_participants']) {
    echo json_encode(["success" => false, "message" => "Registration full."]);
    exit();
}

// Insert registration
$sql = "INSERT INTO registrations (event_id, name, email) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $eventId, $name, $email);

if ($stmt->execute()) {
    // Update registered count
    $updateSql = "UPDATE announcements SET registered_participants = registered_participants + 1 WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("i", $eventId);
    $updateStmt->execute();

    echo json_encode(["success" => true, "message" => "Successfully registered!"]);
} else {
    echo json_encode(["success" => false, "message" => "Error registering."]);
}
?>
