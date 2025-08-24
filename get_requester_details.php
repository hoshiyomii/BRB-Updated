<?php
include 'db.php';

$id = $_GET['id'];
$type = $_GET['type'];

$sql = "SELECT u.first_name, u.last_name, u.phone_number, u.email 
        FROM $type t
        JOIN users u ON t.user_id = u.id
        WHERE t.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'full_name' => $row['first_name'] . ' ' . $row['last_name'],
        'contact_number' => $row['phone_number'],
        'email' => $row['email']
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Requester not found.']);
}

$stmt->close();
$conn->close();
?>