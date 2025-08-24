<?php
session_start();
if (!isset($_SESSION['admin'])) {
    exit(json_encode(["success" => false, "message" => "Unauthorized"]));
}

include 'db.php';

$sql = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);
$announcements = [];

while ($row = $result->fetch_assoc()) {
    $announcements[] = $row;
}

echo json_encode($announcements);
?>
