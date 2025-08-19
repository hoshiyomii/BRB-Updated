<?php
include 'db_connection.php'; // Include database connection
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // or path to PHPMailer

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash password
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $gender = $_POST["gender"];
    $phone_number = trim($_POST["phone_number"]);
    $email = trim($_POST["email"]);
    $birthdate = $_POST["birthdate"];
    $street = trim($_POST["street"]);
    $house_number = trim($_POST["house_number"]);
    $middle_name = trim($_POST["middle_name"]);
    $lot_block = trim($_POST["lot_block"]);
    $blood_type = $_POST["blood_type"];

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo "Username or Email already exists!";
        exit();
    }
    $stmt->close();

    // Insert user into database (set is_verified=0 by default)
    $stmt = $conn->prepare("INSERT INTO users (username, password, first_name, last_name, phone_number, email, birthdate, gender, street, house_number, middle_name, lot_block, blood_type, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("sssssssssssss", $username, $password, $first_name, $last_name, $phone_number, $email, $birthdate, $gender, $street, $house_number, $middle_name, $lot_block, $blood_type);

    if ($stmt->execute()) {
        // Generate verification token
        $token = bin2hex(random_bytes(32));
        $stmt2 = $conn->prepare("UPDATE users SET verify_token=? WHERE email=?");
        $stmt2->bind_param("ss", $token, $email);
        $stmt2->execute();

        // Send verification email using Gmail SMTP
        $verify_link = "http://localhost/barangay/verify.php?token=$token";
        $subject = "Verify your email address";
        $message = "Hello $first_name,<br><br>Please click the link below to verify your email address:<br><a href='$verify_link'>$verify_link</a><br><br>If you did not register, please ignore this email.";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'cjlimpin23@gmail.com'; // <-- your Gmail address
            $mail->Password = 'bqtl uthg zbnp jins';   // <-- your Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('yourgmail@gmail.com', 'Barangay System');
            $mail->addAddress($email, $first_name);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();
            echo "Registration successful! Please check your email to verify your account.";
        } catch (Exception $e) {
            echo "Registration successful, but failed to send verification email. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Registration failed: " . $stmt->error;
    }
    $stmt->close();
}
?>

