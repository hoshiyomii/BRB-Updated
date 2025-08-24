<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch all events for the dropdown
$events_result = $conn->query("SELECT id, title FROM announcements WHERE type = 'event'");

// Get selected event ID from the query parameter
$selected_event_id = isset($_GET['event_id']) ? $_GET['event_id'] : '';

// Get the selected sorting order from the query parameter
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'newest';

// Fetch registrations with contact numbers, filtered by selected event if any
$sql = "SELECT CONCAT(users.first_name, ' ', users.last_name) AS name, 
               users.email, 
               users.phone_number AS contact_number, 
               registrations.registered_at 
        FROM registrations 
        JOIN announcements ON registrations.announcement_id = announcements.id
        JOIN users ON registrations.user_id = users.id";
if ($selected_event_id) {
    $sql .= " WHERE announcements.id = " . $conn->real_escape_string($selected_event_id);
}

// Apply sorting order
if ($sort_order === 'oldest') {
    $sql .= " ORDER BY registered_at ASC";
} else {
    $sql .= " ORDER BY registered_at DESC";
}

$result = $conn->query($sql);

// Check for errors
if (!$result) {
    die("Error fetching registrations: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

    <?php include 'includes/admin_head.php'; ?>
    <link href="view_registrations.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="admin_dashboard.css" rel="stylesheet">
    <link href="view_document_requests.css" rel="stylesheet">




<body>
    <?php include 'includes/admin_navbar.php'; ?>

    <div class="container mt-5">
        <h1>View Registrations</h1>

        <!-- Filter by Event -->
        <form method="GET" class="mb-3 d-flex align-items-center">
            <!-- Event Filter -->
            <div class="form-group d-flex align-items-center mr-3">
                <label for="event_id" class="mr-2">Filter by Event:</label>
                <select name="event_id" id="event_id" class="form-control" style="width: 300px;" onchange="filterRegistrations()">
                    <option value="" <?php if ($selected_event_id === '') echo 'selected'; ?>>Select Event</option>
                    <?php while ($event = $events_result->fetch_assoc()): ?>
                        <option value="<?php echo $event['id']; ?>" <?php if ($event['id'] == $selected_event_id) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($event['title']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Sort By Filter -->
            <div class="form-group d-flex align-items-center">
                <label for="sort_order" class="mr-2">Sort By:</label>
                <select name="sort_order" id="sort_order" class="form-control" style="width: 200px;" onchange="filterRegistrations()">
                    <option value="newest" <?php if (!isset($_GET['sort_order']) || $_GET['sort_order'] === 'newest') echo 'selected'; ?>>Newest</option>
                    <option value="oldest" <?php if (isset($_GET['sort_order']) && $_GET['sort_order'] === 'oldest') echo 'selected'; ?>>Oldest</option>
                </select>
            </div>
        </form>

        <!-- Registrations Table -->
        <?php if ($selected_event_id && $result && $result->num_rows > 0): ?>
            <div id="registrationsTable" class="visible">
                <table class="table table-bordered table-striped mt-3">
                    <thead class="thead-dark">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact Number</th>
                            <th>Date Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                                <td><?php echo date("F j, Y, g:i a", strtotime($row['registered_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Export to Excel -->
            <div class="export-container">
                <form method="POST" action="export_registrations.php">
                    <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($selected_event_id); ?>">
                    <button type="submit" class="btn btn-success"><i class="fas fa-file-excel"></i> Export to Excel</button>
                </form>
            </div>
        <?php elseif ($selected_event_id): ?>
            <p>No registrations found for the selected event.</p>
        <?php endif; ?>
    </div>

    <!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center modal-outline modal-glow">
            <div class="modal-header border-0 justify-content-center">
                <!-- Optional: Add a title or leave empty -->
            </div>
            <div class="modal-body">
                <!-- Warning Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#ffc107" class="mb-4" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                </svg>
                <div class="mb-3 fs-5 text-secondary">Are you sure you want to log out?</div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary me-2 px-4" data-bs-dismiss="modal">Cancel</button>
                <a href="logout.php" class="btn btn-danger px-4">Yes, Log-out</a>
            </div>
        </div>
    </div>
</div>

    <script>
        function filterRegistrations() {
            var eventId = document.getElementById('event_id').value;
            var sortOrder = document.getElementById('sort_order').value;
            window.location.href = 'view_registrations.php?event_id=' + eventId + '&sort_order=' + sortOrder;
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
