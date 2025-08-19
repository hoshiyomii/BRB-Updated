<?php
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'barangay_website';

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// error handling
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    
    die("Database connection error.");
}

$conn->set_charset("utf8mb4");

// $conn->query("SET time_zone = '+08:00'"); // Philippines time zone

return $conn;
?>
