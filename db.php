<?php
$host = "localhost";
$user = "root"; // Change if you have a different MySQL user
$pass = ""; // Change if you have a MySQL password
$dbname = "barangay_website";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
