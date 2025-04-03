<?php
// filepath: d:\XAMPP\htdocs\Barangay\export_registrations.php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';
require 'vendor/autoload.php'; // Include PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Get the selected event ID
$selected_event_id = isset($_POST['event_id']) ? $_POST['event_id'] : '';

// Fetch registrations for the selected event
$sql = "SELECT registrations.*, announcements.title AS event_title 
        FROM registrations 
        JOIN announcements ON registrations.announcement_id = announcements.id";
if ($selected_event_id) {
    $sql .= " WHERE announcements.id = " . $conn->real_escape_string($selected_event_id);
}
$sql .= " ORDER BY registered_at DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching registrations: " . $conn->error);
}

// Create a new Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set the header row
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Name');
$sheet->setCellValue('C1', 'Email');
$sheet->setCellValue('D1', 'Event');
$sheet->setCellValue('E1', 'Date Registered');

// Populate the data rows
$rowNumber = 2; // Start from the second row
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $row['id']);
    $sheet->setCellValue('B' . $rowNumber, $row['name']);
    $sheet->setCellValue('C' . $rowNumber, $row['email']);
    $sheet->setCellValue('D' . $rowNumber, $row['event_title']);
    $sheet->setCellValue('E' . $rowNumber, date("F j, Y, g:i a", strtotime($row['registered_at'])));
    $rowNumber++;
}

// Set the filename
$filename = 'registrations.xlsx';

// Set headers to force download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Write the file and output it to the browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();