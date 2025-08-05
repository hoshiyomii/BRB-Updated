<?php
session_start();
if (!isset($_SESSION['admin'])) {
    exit(json_encode(["success" => false, "message" => "Unauthorized"]));
}

include 'db.php';

$filter = $_GET['filter'] ?? 'active'; // Default to active announcements
$page = $_GET['page'] ?? 1; // Default to page 1
$limit = 4; // Number of announcements per page
$offset = ($page - 1) * $limit;

// Determine the filter condition
$condition = $filter === 'active' ? 'is_active = 1' : 'is_active = 0';

// Fetch announcements with pagination
$sql = "SELECT id, title, content, genre, image_path, 
        IF(is_active = 1, active_until, created_at) AS date 
        FROM announcements 
        WHERE $condition 
        ORDER BY created_at DESC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$announcements = [];
while ($row = $result->fetch_assoc()) {
    $announcements[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'content' => nl2br(htmlspecialchars($row['content'])),
        'genre' => $row['genre'],
        'image_path' => $row['image_path'] ?? 'uploads/default.jpg',
        'date' => $row['date'] ? date("F j, Y, g:i a", strtotime($row['date'])) : 'No expiration'
    ];
}

// Get total count for pagination
$countSql = "SELECT COUNT(*) AS total FROM announcements WHERE $condition";
$countResult = $conn->query($countSql);
$totalAnnouncements = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalAnnouncements / $limit);

echo json_encode([
    'announcements' => $announcements,
    'totalPages' => $totalPages
]);
?>