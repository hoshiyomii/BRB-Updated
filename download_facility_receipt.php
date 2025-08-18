<?php
require 'vendor/autoload.php'; // Make sure you have a PDF library installed, e.g., dompdf/dompdf
use Dompdf\Dompdf;

include 'db.php';

// Get reservation ID from query string
$reservation_id = isset($_GET['id']) ? $_GET['id'] : '';
if (!$reservation_id) {
    die('Reservation ID is required.');
}

// Remove FAC- prefix if present
$id_numeric = intval(str_replace('FAC-', '', $reservation_id));

// Fetch reservation details
$sql = "SELECT fr.*, u.first_name, u.last_name FROM facilities_reservations fr JOIN users u ON fr.user_id = u.id WHERE fr.id = $id_numeric";
$result = $conn->query($sql);
if (!$result || !$result->num_rows) {
    die('Reservation not found.');
}
$row = $result->fetch_assoc();

// Prepare HTML for PDF
$html = '<h2 style="text-align:center;">Facilities Reservation Receipt</h2><hr>';
$html .= '<p><strong>Name:</strong> ' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</p>';
$html .= '<p><strong>Facility:</strong> ' . htmlspecialchars($row['facility_type']) . '</p>';
$html .= '<p><strong>Date:</strong> ' . htmlspecialchars(date('F j, Y', strtotime($row['start_time']))) . '</p>';
$html .= '<p><strong>Start Time:</strong> ' . htmlspecialchars(date('g:i A', strtotime($row['start_time']))) . '</p>';
$html .= '<p><strong>End Time:</strong> ' . htmlspecialchars(date('g:i A', strtotime($row['end_time']))) . '</p>';
$html .= '<p><strong>Total Cost:</strong> ' . htmlspecialchars($row['total_cost']) . ' Php</p>';
$html .= '<hr><p style="font-size:12px;">Please proceed to the barangay hall to complete your payment and signature.</p>';

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('Reservation_Receipt_' . $reservation_id . '.pdf', ['Attachment' => true]);
exit();
