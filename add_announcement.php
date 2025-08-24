<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$success = false; // Flag to track if the announcement was successfully created

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $type = $_POST['type'];
    $genre = $_POST['genre'];
    $max_participants = ($type === "event") ? (int) $_POST['max_participants'] : NULL;
    $image_path = NULL;

    // Validate "Registration Open Until" for events
    $registrationOpenUntil = isset($_POST['registration_open_until']) && !empty($_POST['registration_open_until']) ? $_POST['registration_open_until'] : null;
    if ($type === "event" && !$registrationOpenUntil) {
        die("Error: 'Registration Open Until' is required for event announcements.");
    }
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['image']['name']);
        $targetFilePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $imagePath = $targetFilePath;
        } else {
            echo "Error uploading the file.";
            exit();
        }
    } else {
        $imagePath = 'uploads/default.jpg';
    }

    $activeUntil = isset($_POST['active_until']) && !empty($_POST['active_until']) ? $_POST['active_until'] : null;

    $sql = "INSERT INTO announcements (title, content, genre, type, image_path, active_until, registration_open_until, is_active, max_participants) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $title, $content, $genre, $type, $imagePath, $activeUntil, $registrationOpenUntil, $max_participants);

    if ($stmt->execute()) {
        $success = true; // Set success flag to true
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/admin_head.php'; ?>
<link rel="stylesheet" href="add_announcement.css">
<body>
<?php include 'includes/admin_navbar.php'; ?>

<div class="container-fluid hero-landing">
    <div class="row g-0 align-items-center min-vh-100 justify-content-center">
        <div class="col-lg-9 col-md-11 col-12 mx-auto">
            <div class="row gy-4 gx-2">
                <!-- Guidelines Box -->
                <div class="col-lg-5">
                    <div class="guidelines-box">
                        <h3 class="mb-3 text-center">Announcement Guidelines</h3>
                        <ul class="guideline-list">
                            <li><i class="fa fa-bullhorn"></i> Ensure the title is concise and descriptive.</li>
                            <li><i class="fa fa-list-alt"></i> Select the appropriate type and genre.</li>
                            <li><i class="fa fa-users"></i> For events, specify the maximum number of participants.</li>
                            <li><i class="fa fa-image"></i> Upload an image to make the announcement visually appealing.</li>
                            <li><i class="fa fa-calendar-alt"></i> Set an expiration date if the announcement is time-sensitive.</li>
                        </ul>
                    </div>
                </div>
                <!-- Announcement Form Box -->
                <div class="col-lg-7">
                    <div class="register-card">
                        <h2 class="text-center mb-4">Add Announcement</h2>
                        <form action="add_announcement.php" method="POST" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-12">
                                    <input type="text" class="form-control input-dark" placeholder="Title" id="title" name="title" required>
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control input-dark" placeholder="Content" id="content" name="content" required></textarea>
                                </div>
                                <div class="col-12">
                                    <label for="type">Type:</label>
                                    <select class="form-control input-dark" id="type" name="type" onchange="toggleEventFields()" required>
                                        <option value="view-only">View-only</option>
                                        <option value="event">Event</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="genre">Genre:</label>
                                    <select class="form-control input-dark" id="genre" name="genre" required>
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
                                <div class="col-12" id="maxParticipantsField" style="display: none;">
                                    <label for="max_participants">Max Participants (only for Event):</label>
                                    <input type="number" class="form-control input-dark" id="max_participants" name="max_participants">
                                </div>
                                <div class="col-12">
                                    <label for="image">Upload Image:</label>
                                    <input type="file" class="form-control-file input-dark" id="image" name="image">
                                </div>
                                <div class="col-12">
                                    <label for="active_until">Active Until (Optional):</label>
                                    <input type="datetime-local" id="active_until" name="active_until" class="form-control input-dark">
                                </div>
                                <div class="col-12" id="registrationOpenUntilField" style="display: none;">
                                    <label for="registration_open_until">Registration Open Until (Required):</label>
                                    <input type="date" id="registration_open_until" name="registration_open_until" class="form-control input-dark">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100 py-3">Add Announcement</button>
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
                <p>Your announcement has been successfully created!</p>
            </div>
            <div class="modal-footer">
                <a href="admin_dashboard.php" class="btn btn-primary">Go to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleEventFields() {
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

    // Show the success modal if the announcement was successfully created
    <?php if ($success): ?>
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    <?php endif; ?>
</script>

</body>
</html>
