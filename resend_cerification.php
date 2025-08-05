<?php
include 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email is required.']);
    exit;
}

// Check if the user exists and is unverified
$query = "SELECT id, verification_token FROM users WHERE email = ? AND is_verified = 0";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No unverified user found with this email.']);
    exit;
}

$user = $result->fetch_assoc();
$verificationToken = $user['verification_token'];

// Resend the verification email
$verificationLink = "http://yourwebsite.com/verify.php?token=$verificationToken";
$subject = "Email Verification";
$message = "Please click the following link to verify your email: $verificationLink";
$headers = "From: no-reply@yourwebsite.com";

if (mail($email, $subject, $message, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Verification email resent successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send verification email.']);
}
?>