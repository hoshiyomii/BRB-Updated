<?php
include 'db.php';
session_start();

header('Content-Type: application/json');

// Ensure the database connection is working
if (!$conn) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Ensure the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'You must be logged in to pre-register for this event.']);
    exit;
}

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate the input
if (!isset($data['id']) || empty($data['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Invalid announcement ID.']);
    exit;
}

$announcementId = intval($data['id']);
$userId = $_SESSION['user_id']; // Assuming the user is logged in and their ID is stored in the session

// Check if the user has already registered for this event
$sql = "SELECT * FROM registrations WHERE user_id = ? AND announcement_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $announcementId);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user is already registered
if ($result->num_rows > 0) {
    echo json_encode(['error' => 'You are already registered for this event.']);
    exit;
}

// Fetch the announcement
$sql = "SELECT max_participants, registered_participants, registration_open_until FROM announcements WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $announcementId);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    http_response_code(404); // Not Found
    echo json_encode(['success' => false, 'message' => 'Announcement not found.']);
    exit;
}

// Check if the event is full
if ($event['registered_participants'] >= $event['max_participants']) {
    echo json_encode(['error' => 'Pre-registration slots are full.']);
    exit;
}

$now = new DateTime();
$currentDate = $now->format('Y-m-d'); // Get only the date part
$registrationOpenUntil = $event['registration_open_until']; // Already a DATE field

// Check if the registration is still open
if ($currentDate > $registrationOpenUntil) {
    echo json_encode(['success' => false, 'error' => 'Registration is already closed.']);
    exit;
}

// Insert the registration into the registrations table
$sql = "INSERT INTO registrations (user_id, announcement_id, registered_at) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $announcementId);

if ($stmt->execute()) {
    // Update the registered participants count
    $sql = "UPDATE announcements SET registered_participants = registered_participants + 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $announcementId);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Successfully registered!', 'registered_participants' => $event['registered_participants'] + 1]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Failed to register. Please try again later.']);
}
?>
