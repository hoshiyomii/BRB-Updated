<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$id = $_GET['id'];
$sql = "SELECT * FROM announcements WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$announcement = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $type = $_POST['type'];
    $max_participants = ($type === "event") ? (int) $_POST['max_participants'] : NULL;
    $image_path = $announcement['image_path']; // Default to current image

    // Handle image upload (if new image is uploaded)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = "uploads/"; // Define your upload directory
        $image_name = basename($_FILES['image']['name']);
        $target_path = $upload_dir . $image_name;

        // Move the uploaded image to the specified directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = $target_path; // Update the image path
        }
    }

    // Update the announcement in the database
    $sql = "UPDATE announcements SET title=?, content=?, type=?, max_participants=?, image_path=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisi", $title, $content, $type, $max_participants, $image_path, $id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Announcement</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Edit Announcement</h1>
        <form action="edit_announcement.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="content">Content:</label>
                <textarea class="form-control" id="content" name="content" required><?php echo htmlspecialchars($announcement['content']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="type">Type:</label>
                <select class="form-control" id="type" name="type">
                    <option value="view-only" <?php echo ($announcement['type'] == 'view-only') ? 'selected' : ''; ?>>View-only</option>
                    <option value="event" <?php echo ($announcement['type'] == 'event') ? 'selected' : ''; ?>>Event</option>
                </select>
            </div>
            <div class="form-group">
                <label for="max_participants">Max Participants (only for Event):</label>
                <input type="number" class="form-control" id="max_participants" name="max_participants" value="<?php echo ($announcement['max_participants'] != NULL) ? $announcement['max_participants'] : ''; ?>" <?php echo ($announcement['type'] == 'event') ? '' : 'disabled'; ?>>
            </div>
            <div class="form-group">
                <label for="image">Upload New Image:</label>
                <input type="file" class="form-control-file" id="image" name="image">
            </div>
            <button type="submit" class="btn btn-primary">Update Announcement</button>
        </form>
        <p><a href="admin_dashboard.php">Back to Admin Dashboard</a></p>
    </div>
</body>
</html>
