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

$success = false; // Flag to track if the announcement was successfully updated

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $type = $_POST['type'];
    $genre = $_POST['genre'];
    $max_participants = ($type === "event") ? (int) $_POST['max_participants'] : NULL;
    $image_path = $announcement['image_path']; // Default to current image

    // Validate "Registration Open Until" for events
    $registrationOpenUntil = isset($_POST['registration_open_until']) && !empty($_POST['registration_open_until']) ? $_POST['registration_open_until'] : null;
    if ($type === "event" && !$registrationOpenUntil) {
        die("Error: 'Registration Open Until' is required for event announcements.");
    }

    $activeUntil = isset($_POST['active_until']) && !empty($_POST['active_until']) ? $_POST['active_until'] : null;

    // Handle image upload (if new image is uploaded)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "uploads/";
        $image_name = basename($_FILES['image']['name']);
        $target_path = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = $target_path;
        }
    }

    $sql = "UPDATE announcements SET title=?, content=?, genre=?, type=?, max_participants=?, image_path=?, active_until=?, registration_open_until=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssisssi", $title, $content, $genre, $type, $max_participants, $image_path, $activeUntil, $registrationOpenUntil, $id);

    if ($stmt->execute()) {
        $success = true; // Set success flag to true
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/admin_head.php'; ?>
<link rel="stylesheet" href="edit_announcement.css">
<body>
<?php include 'includes/admin_navbar.php'; ?>

<div class="container-fluid hero-landing">
    <div class="row g-0 align-items-center min-vh-100 justify-content-center">
        <div class="col-lg-9 col-md-11 col-12 mx-auto">
            <div class="row gy-4 gx-2">
                <!-- Guidelines Box -->
                <div class="col-lg-5">
                    <div class="guidelines-box">
                        <h3 class="mb-3 text-center">Edit Announcement Guidelines</h3>
                        <ul class="guideline-list">
                            <li><i class="fa fa-edit"></i> Update the title to reflect the changes.</li>
                            <li><i class="fa fa-list-alt"></i> Ensure the content is accurate and up-to-date.</li>
                            <li><i class="fa fa-users"></i> Adjust the maximum participants for events if needed.</li>
                            <li><i class="fa fa-image"></i> Upload a new image if required.</li>
                            <li><i class="fa fa-calendar-alt"></i> Modify the expiration date if applicable.</li>
                        </ul>
                    </div>
                </div>
                <!-- Edit Announcement Form Box -->
                <div class="col-lg-7">
                    <div class="register-card">
                        <h2 class="text-center mb-4">Edit Announcement</h2>
                        <form action="edit_announcement.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-12">
                                    <input type="text" class="form-control input-dark" placeholder="Title" id="title" name="title" value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control input-dark" placeholder="Content" id="content" name="content" required><?php echo htmlspecialchars($announcement['content']); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label for="type">Type:</label>
                                    <select class="form-control input-dark" id="type" name="type" onchange="toggleMaxParticipants()" required>
                                        <option value="view-only" <?php echo ($announcement['type'] == 'view-only') ? 'selected' : ''; ?>>View-only</option>
                                        <option value="event" <?php echo ($announcement['type'] == 'event') ? 'selected' : ''; ?>>Event</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="genre">Genre:</label>
                                    <select class="form-control input-dark" id="genre" name="genre" required>
                                        <option value="Work and Employment" <?php echo ($announcement['genre'] == 'Work and Employment') ? 'selected' : ''; ?>>Work and Employment</option>
                                        <option value="Healthcare and Safety" <?php echo ($announcement['genre'] == 'Healthcare and Safety') ? 'selected' : ''; ?>>Healthcare and Safety</option>
                                        <option value="Animals" <?php echo ($announcement['genre'] == 'Animals') ? 'selected' : ''; ?>>Animals</option>
                                        <option value="Safety" <?php echo ($announcement['genre'] == 'Safety') ? 'selected' : ''; ?>>Safety</option>
                                        <option value="Emergency" <?php echo ($announcement['genre'] == 'Emergency') ? 'selected' : ''; ?>>Emergency</option>
                                        <option value="Holidays and Events" <?php echo ($announcement['genre'] == 'Holidays and Events') ? 'selected' : ''; ?>>Holidays and Events</option>
                                        <option value="Education" <?php echo ($announcement['genre'] == 'Education') ? 'selected' : ''; ?>>Education</option>
                                        <option value="Transportation and Traffic" <?php echo ($announcement['genre'] == 'Transportation and Traffic') ? 'selected' : ''; ?>>Transportation and Traffic</option>
                                        <option value="Government and Public Affairs" <?php echo ($announcement['genre'] == 'Government and Public Affairs') ? 'selected' : ''; ?>>Government and Public Affairs</option>
                                        <option value="Social and Community" <?php echo ($announcement['genre'] == 'Social and Community') ? 'selected' : ''; ?>>Social and Community</option>
                                    </select>
                                </div>
                                <div class="col-12" id="maxParticipantsField" style="display: <?php echo ($announcement['type'] == 'event') ? 'block' : 'none'; ?>;">
                                    <label for="max_participants">Max Participants (only for Event):</label>
                                    <input type="number" class="form-control input-dark" id="max_participants" name="max_participants" value="<?php echo ($announcement['max_participants'] != NULL) ? $announcement['max_participants'] : ''; ?>">
                                </div>
                                <div class="col-12">
                                    <label for="image">Upload New Image:</label>
                                    <input type="file" class="form-control-file input-dark" id="image" name="image">
                                </div>
                                <div class="col-12">
                                    <label for="active_until">Active Until (Optional):</label>
                                    <input type="datetime-local" id="active_until" name="active_until" class="form-control input-dark" value="<?php echo isset($announcement['active_until']) ? date('Y-m-d\TH:i', strtotime($announcement['active_until'])) : ''; ?>">
                                </div>
                                <div class="col-12" id="registrationOpenUntilField" style="display: <?php echo ($announcement['type'] == 'event') ? 'block' : 'none'; ?>;">
                                    <label for="registration_open_until">Registration Open Until (Required):</label>
                                    <input type="date" id="registration_open_until" name="registration_open_until" class="form-control input-dark" value="<?php echo isset($announcement['registration_open_until']) ? date('Y-m-d', strtotime($announcement['registration_open_until'])) : ''; ?>">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100 py-3">Update Announcement</button>
                                </div>
                            </div>
                        </form>
                        <p class="text-center mt-3"><a href="admin_dashboard.php">Back to Admin Dashboard</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>The announcement has been successfully updated!</p>
            </div>
            <div class="modal-footer">
                <a href="admin_dashboard.php" class="btn btn-primary">Go to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleMaxParticipants() {
        var type = document.getElementById('type').value;
        var maxParticipantsField = document.getElementById('maxParticipantsField');
        var registrationOpenUntilField = document.getElementById('registrationOpenUntilField');

        if (type === 'event') {
            maxParticipantsField.style.display = 'block';
            registrationOpenUntilField.style.display = 'block';
            document.getElementById('registration_open_until').setAttribute('required', 'required'); // Make required
        } else {
            maxParticipantsField.style.display = 'none';
            registrationOpenUntilField.style.display = 'none';
            document.getElementById('registration_open_until').removeAttribute('required'); // Remove required
        }
    }

    // Show the success modal if the announcement was successfully updated
    <?php if ($success): ?>
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    <?php endif; ?>
</script>
</body>
</html>
