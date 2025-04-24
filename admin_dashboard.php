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
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white p-3">
        <div class="container">
            <h1 class="h3">Admin Dashboard</h1>
            <nav class="nav">
                <a class="nav-link text-white" href="admin_dashboard.php">Dashboard</a>
                <a class="nav-link text-white" href="view_registrations.php">View Registrations</a>
                <a class="nav-link text-white" href="view_document_requests.php">View Document Requests</a>
                <?php if (isset($_SESSION['admin_level']) && $_SESSION['admin_level'] == '2'): ?>
                    <a class="nav-link text-white" href="create_admin.php">Create Admin</a>
                <?php endif; ?>
                <a class="nav-link text-white" href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <main class="container mt-4">
        <section id="announcements">
            <h2 class="h4">Manage Announcements</h2>
            <button class="btn btn-primary mb-3" onclick="location.href='add_announcement.php'">Add Announcement</button>

            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                        <p class="card-text"><strong>Genre:</strong> <?php echo htmlspecialchars($row['genre']); ?></p>
                        <p class="card-text"><strong>Date Created:</strong> <?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></p>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                        <p class="card-text"><strong>Type:</strong> <?php echo ucfirst($row['type']); ?></p>

                        <?php if ($row['type'] === "event"): ?>
                            <p class="card-text"><strong>Max Participants:</strong> <?php echo $row['max_participants']; ?></p>
                            <p class="card-text"><strong>Registered Participants:</strong> <?php echo $row['registered_participants']; ?></p>
                        <?php endif; ?>

                        <p class="card-text"><strong>Active Until:</strong> 
                            <?php echo $row['active_until'] ? date("F j, Y, g:i a", strtotime($row['active_until'])) : 'No expiration'; ?>
                        </p>

                        <!-- Display Status -->
                        <p class="card-text"><strong>Status:</strong> 
                            <?php echo $row['is_active'] ? '<span class="text-success">Visible to Public</span>' : '<span class="text-danger">Archived</span>'; ?>
                        </p>

                        <button class="btn btn-secondary" onclick="location.href='edit_announcement.php?id=<?php echo $row['id']; ?>'">Edit</button>
                        <button class="btn btn-info" onclick="showFullImage('<?php echo htmlspecialchars($row['image_path'] ?? 'uploads/default.jpg'); ?>')">Show Full Image</button>
                        <button class="btn btn-danger" onclick="deleteAnnouncement(<?php echo $row['id']; ?>)">Delete</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </section>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="admin.js"></script>
    <script>
    function showFullImage(imagePath) {
        // Open the full-size image in a new tab
        window.open(imagePath, '_blank');
    }
    </script>
</body>
</html>