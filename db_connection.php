<?php
$host = "localhost";
$user = "root"; // Change if you have a different MySQL user
$password = ""; // Change if you have a MySQL password
$database = "barangay_website"; // Make sure this matches your database name

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
