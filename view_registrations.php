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




<body>
    <?php include 'includes/admin_navbar.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="registration-container">
                    <div class="header-actions">
                        <h2 class="header-title">Event Registrations</h2>
                    </div>

                    <!-- Filter by Event -->
                    <div class="filter-container">
                        <div class="d-flex align-items-center me-4">
                            <label for="event_id" class="filter-label me-2">Event:</label>
                            <select name="event_id" id="event_id" class="form-select filter-select" onchange="filterRegistrations()">
                                <option value="" <?php if ($selected_event_id === '') echo 'selected'; ?>>Select Event</option>
                                <?php 
                                // Reset the result pointer to the beginning
                                $events_result->data_seek(0);
                                while ($event = $events_result->fetch_assoc()): ?>
                                    <option value="<?php echo $event['id']; ?>" <?php if ($event['id'] == $selected_event_id) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($event['title']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Sort By Filter -->
                        <div class="d-flex align-items-center">
                            <label for="sort_order" class="filter-label me-2">Sort By:</label>
                            <select name="sort_order" id="sort_order" class="form-select filter-select" onchange="filterRegistrations()">
                                <option value="newest" <?php if (!isset($_GET['sort_order']) || $_GET['sort_order'] === 'newest') echo 'selected'; ?>>Newest</option>
                                <option value="oldest" <?php if (isset($_GET['sort_order']) && $_GET['sort_order'] === 'oldest') echo 'selected'; ?>>Oldest</option>
                            </select>
                        </div>
                    </div>

                    <!-- Registrations Table -->
                    <?php if ($selected_event_id && $result && $result->num_rows > 0): ?>
                        <div class="table-container">
                            <table class="table registration-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Contact Number</th>
                                        <th>Date Registered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr class="clickable-row" data-name="<?php echo htmlspecialchars($row['name']); ?>" 
                                            data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                            data-contact="<?php echo htmlspecialchars($row['contact_number']); ?>"
                                            data-registered="<?php echo htmlspecialchars($row['registered_at']); ?>">
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
                                <button type="submit" class="btn btn-success export-btn">
                                    <i class="fas fa-file-excel"></i> Export to Excel
                                </button>
                            </form>
                        </div>
                    <?php elseif ($selected_event_id): ?>
                        <div class="empty-state">
                            <i class="fas fa-users-slash empty-state-icon"></i>
                            <p class="empty-state-text">No registrations found for the selected event.</p>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-alt empty-state-icon"></i>
                            <p class="empty-state-text">Please select an event to view registrations.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Registration Detail Modal -->
    <div class="modal fade" id="registrationDetailModal" tabindex="-1" aria-labelledby="registrationDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content registration-detail-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="registrationDetailModalLabel">Registration Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-section">
                        <h5>Participant Information</h5>
                        <div class="detail-row">
                            <div class="detail-label">Name</div>
                            <div class="detail-value" id="modalName"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Email</div>
                            <div class="detail-value" id="modalEmail"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Contact Number</div>
                            <div class="detail-value" id="modalContact"></div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h5>Registration Information</h5>
                        <div class="detail-row">
                            <div class="detail-label">Date Registered</div>
                            <div class="detail-value" id="modalDate"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Event</div>
                            <div class="detail-value" id="modalEvent"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function filterRegistrations() {
            var eventId = document.getElementById('event_id').value;
            var sortOrder = document.getElementById('sort_order').value;
            window.location.href = 'view_registrations.php?event_id=' + eventId + '&sort_order=' + sortOrder;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Get the selected event name
            const eventSelect = document.getElementById('event_id');
            const selectedEventText = eventSelect.options[eventSelect.selectedIndex]?.text || '';
            
            // Make rows clickable and show modal with registration details
            document.querySelectorAll('.clickable-row').forEach(row => {
                row.addEventListener('click', function() {
                    // Add visual feedback when row is clicked
                    document.querySelectorAll('.clickable-row').forEach(r => r.classList.remove('active-row'));
                    this.classList.add('active-row');
                    
                    // Get data from row
                    const name = this.getAttribute('data-name');
                    const email = this.getAttribute('data-email');
                    const contact = this.getAttribute('data-contact');
                    const registered = this.getAttribute('data-registered');
                    
                    // Populate modal with data
                    document.getElementById('modalName').textContent = name;
                    document.getElementById('modalEmail').textContent = email;
                    document.getElementById('modalContact').textContent = contact;
                    document.getElementById('modalDate').textContent = new Date(registered).toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    document.getElementById('modalEvent').textContent = selectedEventText;
                    
                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('registrationDetailModal'));
                    modal.show();
                });
            });
            
            // Clear active row when modal is closed
            document.getElementById('registrationDetailModal').addEventListener('hidden.bs.modal', function () {
                document.querySelectorAll('.clickable-row').forEach(r => r.classList.remove('active-row'));
            });
        });
    </script>
</body>
</html>
