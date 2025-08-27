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

// Prepare and execute the update query
$stmt = $conn->prepare("UPDATE users SET is_verified = 1, verify_token = NULL WHERE id = ?");
$stmt->bind_param("i", $residentId);
$result = $stmt->execute();

if ($result) {
    // Log verification action
    $adminUsername = $_SESSION['admin'];
    $details = "Verified resident with ID: $residentId";
    
    // Try to log the verification action but don't interrupt the process if it fails
    try {
        $logSql = "INSERT INTO audit_logs (action, details, performed_by) VALUES ('VERIFY_RESIDENT', ?, ?)";
        $logStmt = $conn->prepare($logSql);
        if ($logStmt) {
            $logStmt->bind_param("ss", $details, $adminUsername);
            $logStmt->execute();
        }
    } catch (Exception $e) {
        // Log error to file instead of displaying to user
        error_log("Failed to log verification action: " . $e->getMessage(), 0);
    }
    
    // Fetch user email for notification
    $userStmt = $conn->prepare("SELECT email, first_name, last_name FROM users WHERE id = ?");
    $userStmt->bind_param("i", $residentId);
    $userStmt->execute();
    $userData = $userStmt->get_result()->fetch_assoc();
    
    if ($userData) {
        // Send verification confirmation email
        $to = $userData['email'];
        $subject = "Account Verification Confirmation - Barangay Blue Ridge B";
        $message = "
        <html>
        <head>
            <title>Account Verification Confirmation</title>
        </head>
        <body>
            <h2>Account Verification Confirmed</h2>
            <p>Dear " . htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']) . ",</p>
            <p>Your account has been verified by the Barangay Blue Ridge B administration. You now have full access to all barangay services.</p>
            <p>Thank you for registering with us!</p>
            <p>Regards,<br>Barangay Blue Ridge B Administration</p>
        </body>
        </html>
        ";
        
        // To send HTML mail, the Content-type header must be set
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: no-reply@barangayblueridgeb.gov.ph' . "\r\n";
        
        mail($to, $subject, $message, $headers);
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to verify resident']);
}
