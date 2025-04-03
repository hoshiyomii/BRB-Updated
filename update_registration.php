<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$event_id = $data['id'];

$sql = "SELECT registered_participants, max_participants FROM announcements WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event || $event['registered_participants'] >= $event['max_participants']) {
    echo json_encode(["success" => false, "message" => "Registration is full."]);
    exit();
}

// Update participant count
$new_count = $event['registered_participants'] + 1;
$update_sql = "UPDATE announcements SET registered_participants = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("ii", $new_count, $event_id);

if ($update_stmt->execute()) {
    echo json_encode(["success" => true, "new_count" => $new_count, "max_participants" => $event['max_participants']]);
} else {
    echo json_encode(["success" => false, "message" => "Error updating registration."]);
}
?>
