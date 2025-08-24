
<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $documentType = $_POST['document_type'];

    $sql = "UPDATE {$documentType} SET status = 'picked_up' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
?>