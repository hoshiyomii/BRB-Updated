<?php
session_start();
include 'db_connection.php'; // Ensure this file contains the database connection logic
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer

// Decode the incoming JSON request
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || empty($data['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email is required.']);
    exit();
}

$email = $data['email'];

// Check if the email exists in the database
$stmt = $conn->prepare("SELECT first_name, verify_token FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Email not found.']);
    exit();
}

$user = $result->fetch_assoc();
$first_name = $user['first_name'];
$verify_token = $user['verify_token'];

// If no token exists, generate a new one
if (empty($verify_token)) {
    $verify_token = bin2hex(random_bytes(32));
    $update_stmt = $conn->prepare("UPDATE users SET verify_token = ? WHERE email = ?");
    $update_stmt->bind_param("ss", $verify_token, $email);
    $update_stmt->execute();
    $update_stmt->close();
}

// Send the verification email
$verify_link = "http://localhost/Barangay/verify.php?token=$verify_token";
$subject = "Verify your email address";
$message = "Hello $first_name,<br><br>Please click the link below to verify your email address:<br><a href='$verify_link'>$verify_link</a><br><br>If you did not register, please ignore this email.";

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'cjlimpin23@gmail.com'; // Replace with your Gmail address
    $mail->Password = 'bqtl uthg zbnp jins';   // Replace with your Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('your-email@gmail.com', 'Barangay System');
    $mail->addAddress($email, $first_name);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Verification email has been resent successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to send verification email. Mailer Error: ' . $mail->ErrorInfo]);
}

$stmt->close();
$conn->close();
?>