<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['resident_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$residentId = intval($_POST['resident_id']);

// Start transaction
$conn->begin_transaction();

try {
    // Fetch resident info for audit log
    $userStmt = $conn->prepare("SELECT username, email, first_name, last_name FROM users WHERE id = ?");
    $userStmt->bind_param("i", $residentId);
    $userStmt->execute();
    $userData = $userStmt->get_result()->fetch_assoc();
    
    if (!$userData) {
        throw new Exception("Resident not found");
    }
    
    // Delete related records (reservations, document requests, etc.)
    // This assumes you have foreign key constraints with ON DELETE CASCADE
    // If not, you'll need to delete related records manually
    
    // Delete user record
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $residentId);
    $result = $stmt->execute();
    
    if (!$result) {
        throw new Exception("Failed to delete resident");
    }
    
    // Log deletion action
    $adminUsername = $_SESSION['admin'];
    $details = "Deleted resident: " . $userData['username'] . " (" . $userData['first_name'] . " " . $userData['last_name'] . ", " . $userData['email'] . ")";
    
    // Try to log the deletion action but don't interrupt the process if it fails
    try {
        $logSql = "INSERT INTO audit_logs (action, details, performed_by) VALUES ('DELETE_RESIDENT', ?, ?)";
        $logStmt = $conn->prepare($logSql);
        if ($logStmt) {
            $logStmt->bind_param("ss", $details, $adminUsername);
            $logStmt->execute();
        }
    } catch (Exception $e) {
        // Just log the error, but continue with the deletion
        error_log("Failed to log deletion action: " . $e->getMessage(), 0);
    }
    
    // Commit transaction
    $conn->commit();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
