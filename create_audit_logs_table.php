<?php
// This script creates the missing audit_logs table in the database
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Check if audit_logs table exists
$tableExists = false;
$result = $conn->query("SHOW TABLES LIKE 'audit_logs'");
if ($result->num_rows > 0) {
    $tableExists = true;
}

if (!$tableExists) {
    // Create the audit_logs table
    $sql = "CREATE TABLE `audit_logs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `action` varchar(64) NOT NULL,
        `details` text DEFAULT NULL,
        `performed_by` varchar(64) NOT NULL,
        `timestamp` datetime DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

    if ($conn->query($sql) === TRUE) {
        echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; margin: 15px; border-radius: 5px;'>
            <h3>Success!</h3>
            <p>The audit_logs table was created successfully.</p>
            <p><a href='admin_dashboard.php'>Return to Dashboard</a></p>
        </div>";
    } else {
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; margin: 15px; border-radius: 5px;'>
            <h3>Error</h3>
            <p>Error creating table: " . $conn->error . "</p>
            <p><a href='admin_dashboard.php'>Return to Dashboard</a></p>
        </div>";
    }
} else {
    echo "<div style='background-color: #d1ecf1; color: #0c5460; padding: 15px; margin: 15px; border-radius: 5px;'>
        <h3>Information</h3>
        <p>The audit_logs table already exists.</p>
        <p><a href='admin_dashboard.php'>Return to Dashboard</a></p>
    </div>";
}
?>
