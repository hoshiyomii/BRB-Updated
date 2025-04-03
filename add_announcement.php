<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $type = $_POST['type'];
    $genre = $_POST['genre']; // Ensure this is retrieving the correct value
    $max_participants = ($type === "event") ? (int) $_POST['max_participants'] : NULL;
    $image_path = NULL;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = "uploads/"; // Define your upload directory
        $image_name = basename($_FILES['image']['name']);
        $target_path = $upload_dir . $image_name;

        // Move the uploaded image to the specified directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = $target_path; // Update the image path
        }
    }

    // SQL query
    $sql = "INSERT INTO announcements (title, content, type, genre, max_participants, image_path) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssds", $title, $content, $type, $genre, $max_participants, $image_path);

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
    <title>Add Announcement</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function toggleMaxParticipants() {
            var type = document.getElementById('type').value;
            var maxParticipantsField = document.getElementById('maxParticipantsField');
            if (type === 'event') {
                maxParticipantsField.style.display = 'block';
            } else {
                maxParticipantsField.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <div class="container mt-4">
        <h1>Add Announcement</h1>
        <form action="add_announcement.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="content">Content:</label>
                <textarea class="form-control" id="content" name="content" required></textarea>
            </div>
            <div class="form-group">
                <label for="type">Type:</label>
                <select class="form-control" id="type" name="type" onchange="toggleMaxParticipants()">
                    <option value="view-only">View-only</option>
                    <option value="event">Event</option>
                </select>
            </div>
            <div class="form-group">
                <label for="genre">Genre:</label>
                <select class="form-control" id="genre" name="genre" required>
                    <option value="Work and Employment">Work and Employment</option>
                    <option value="Healthcare and Safety">Healthcare and Safety</option>
                    <option value="Animals">Animals</option>
                    <option value="Safety">Safety</option>
                    <option value="Emergency">Emergency</option>
                    <option value="Holidays and Events">Holidays and Events</option>
                    <option value="Education">Education</option>
                    <option value="Transportation and Traffic">Transportation and Traffic</option>
                    <option value="Government and Public Affairs">Government and Public Affairs</option>
                    <option value="Social and Community">Social and Community</option>
                </select>
            </div>
            <div class="form-group" id="maxParticipantsField" style="display: none;">
                <label for="max_participants">Max Participants (only for Event):</label>
                <input type="number" class="form-control" id="max_participants" name="max_participants">
            </div>
            <div class="form-group">
                <label for="image">Upload Image:</label>
                <input type="file" class="form-control-file" id="image" name="image">
            </div>
            <button type="submit" class="btn btn-primary">Add Announcement</button>
        </form>
        <p><a href="admin_dashboard.php">Back to Admin Dashboard</a></p>
    </div>
</body>
</html>
