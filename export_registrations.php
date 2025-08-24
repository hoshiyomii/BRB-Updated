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
$sql = "SELECT 
            registrations.name AS full_name, 
            users.last_name, 
            users.first_name, 
            users.phone_number 
        FROM registrations 
        JOIN announcements ON registrations.announcement_id = announcements.id
        JOIN users ON registrations.user_id = users.id";

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
$sheet->setCellValue('A1', 'No.');
$sheet->setCellValue('B1', 'Last Name');
$sheet->setCellValue('C1', 'First Name');
$sheet->setCellValue('D1', 'Phone Number');
$sheet->setCellValue('E1', 'Signature');

$sheet->getStyle('A1:E1')->applyFromArray([
    'font' => [
        'bold' => true,
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'D9E1F2'], // Light blue background for the header
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
]);


$rowNumber = 2; // Start from the second row
$counter = 1; // Initialize the counter
while ($row = $result->fetch_assoc()) {
    // Set the data for each column
    $sheet->setCellValue('A' . $rowNumber, $counter); // Counter
    $sheet->setCellValue('B' . $rowNumber, $row['last_name']); // Last Name
    $sheet->setCellValue('C' . $rowNumber, $row['first_name']); // First Name
    $sheet->setCellValue('D' . $rowNumber, $row['phone_number']); // Phone Number
    $sheet->setCellValue('E' . $rowNumber, ''); // Signature (Blank)

    // Apply alternating row colors
    if ($rowNumber % 2 == 0) {
        // Light gray for even rows
        $sheet->getStyle('A' . $rowNumber . ':E' . $rowNumber)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F2F2F2'], // Light gray
            ],
        ]);
    }

    $rowNumber++;
    $counter++;
}


// foreach (range('A', 'E') as $columnID) {
//     $sheet->getColumnDimension($columnID)->setAutoSize(true);
// }

//pahirap >:C
$sheet->getColumnDimension('A')->setWidth(10); 
$sheet->getColumnDimension('B')->setWidth(20); 
$sheet->getColumnDimension('C')->setWidth(20); 
$sheet->getColumnDimension('D')->setWidth(20); 
$sheet->getColumnDimension('E')->setWidth(25); 


foreach ($sheet->getRowIterator() as $row) {
    $sheet->getRowDimension($row->getRowIndex())->setRowHeight(-1); // Auto-adjust row height
}

$sheet->getPageSetup()->setFitToWidth(1); // Fit to one page wide
$sheet->getPageSetup()->setFitToHeight(0); // Fit all rows on one page height



// Set the paper size to A4
$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

$event_title = '';
if ($selected_event_id) {
    $event_query = $conn->query("SELECT title FROM announcements WHERE id = " . $conn->real_escape_string($selected_event_id));
    if ($event_query && $event_row = $event_query->fetch_assoc()) {
        $event_title = $event_row['title'];
    }
}

$sanitized_event_title = preg_replace('/[^A-Za-z0-9_\-]/', '_', $event_title);


$filename = $sanitized_event_title ? "Attendance_{$sanitized_event_title}.xlsx" : 'registrations.xlsx';


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();