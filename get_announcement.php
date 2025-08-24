<?php
include 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid announcement ID.']);
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT id, title, content, genre, type, max_participants, registered_participants, image_path, created_at, active_until, registration_open_until 
        FROM announcements 
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$announcement = $result->fetch_assoc();

echo json_encode($announcement);
?>