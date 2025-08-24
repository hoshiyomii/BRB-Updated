<?php
session_start();
if (!isset($_SESSION['admin'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];

$sql = "DELETE FROM announcements WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Announcement deleted."]);
} else {
    echo json_encode(["success" => false, "message" => "Error deleting announcement."]);
}
?>
