<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch all announcements
$sql = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);

// Check for errors
if (!$result) {
    die("Error fetching announcements: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Announcements</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Manage Announcements</h1>
    <button onclick="location.href='add_announcement.php'">Add Announcement</button>

    <div id="announcements">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="announcement">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><strong>Date Created:</strong> <?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></p>
            <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
            <p><strong>Type:</strong> <?php echo ucfirst($row['type']); ?></p>

            <?php if ($row['type'] === "event"): ?>
                <p><strong>Max Participants:</strong> <?php echo $row['max_participants']; ?></p>
                <p><strong>Registered Participants:</strong> <?php echo $row['registered_participants']; ?></p>
            <?php endif; ?>

            <button onclick="location.href='edit_announcement.php?id=<?php echo $row['id']; ?>'">Edit</button>
            <button onclick="deleteAnnouncement(<?php echo $row['id']; ?>)">Delete</button>
        </div>
    <?php endwhile; ?>
</div>


    <a href="view_registrations.php">
        <button>View Registrations</button>
    </a>


    <script src="admin.js"></script>
</body>
</html>
