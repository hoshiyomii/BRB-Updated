<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Archive expired announcements
$sql = "UPDATE announcements
        SET is_active = 0
        WHERE active_until < NOW() AND is_active = 1";
$conn->query($sql);

// Pagination logic
$limit = 4; // Number of announcements per page (2x2 grid)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter logic
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'active';
$condition = $filter === 'active' ? 'is_active = 1' : 'is_active = 0';

// Fetch announcements based on filter and pagination
$sql = "SELECT id, title, content, genre, image_path, created_at, active_until, registration_open_until, is_active 
        FROM announcements 
        WHERE $condition 
        ORDER BY created_at DESC 
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Get total number of announcements for pagination
$countSql = "SELECT COUNT(*) AS total FROM announcements WHERE $condition";
$countResult = $conn->query($countSql);
$totalAnnouncements = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalAnnouncements / $limit);

// Check for errors
if (!$result) {
    die("Error fetching announcements: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
    <?php include 'includes/admin_head.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href= "admin_dashboard.css" rel="stylesheet">
<body>
    <?php include 'includes/admin_navbar.php'; ?>

    <main class="container mt-4">
        <!-- Header Section -->
        <div class="admin-header">
            <h2>Manage Announcements</h2>
            
            <div class="admin-controls">
                <!-- Add Announcement Button -->
                <button class="admin-button primary" onclick="location.href='add_announcement.php'">Add Announcement</button>
                <!-- Filter Dropdown -->
                <select id="announcementFilter" class="admin-select" onchange="filterAnnouncements(this.value)">
                    <option value="active" <?php echo $filter === 'active' ? 'selected' : ''; ?>>Active Announcements</option>
                    <option value="archived" <?php echo $filter === 'archived' ? 'selected' : ''; ?>>Archived Announcements</option>
                </select>
            </div>
        </div>

        <!-- Announcements Container -->
        <div class="announcements-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="announcement-item" data-id="<?php echo $row['id']; ?>">
                        <!-- Top Section: Title and Dropdown -->
                        <div class="announcement-header">
                            <h5 class="announcement-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <div class="announcement-actions">
                                <button class="action-toggle" type="button" onclick="toggleActionMenu(<?php echo $row['id']; ?>)">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <div class="action-menu" id="actionMenu<?php echo $row['id']; ?>">
                                    <a class="action-link" href="edit_announcement.php?id=<?php echo $row['id']; ?>">
                                        <i class="bi bi-pencil-square"></i> <span>Edit</span>
                                    </a>
                                    <button class="action-link delete" onclick="showDeleteConfirmation(<?php echo $row['id']; ?>)">
                                        <i class="bi bi-trash"></i> <span>Delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Main Content -->
                        <div class="announcement-content">
                            <!-- Left Side: Image -->
                            <div class="announcement-image-container">
                                <img src="<?php echo htmlspecialchars($row['image_path'] ?? 'uploads/default.jpg'); ?>" 
                                     alt="Announcement Image" 
                                     class="announcement-image">
                                <span class="genre-badge" data-genre="<?php echo htmlspecialchars($row['genre']); ?>">
                                    <?php echo htmlspecialchars($row['genre']); ?>
                                </span>
                            </div>
                            <!-- Right Side: Information -->
                            <div class="announcement-details">
                                <p class="announcement-text"><?php echo htmlspecialchars(substr($row['content'], 0, 150)) . '...'; ?></p>
                                <div class="announcement-meta">
                                    <p class="announcement-date"><i class="bi bi-calendar-event"></i> <span><?php echo date('F j, Y', strtotime($row['created_at'])); ?></span></p>
                                    <p class="announcement-date"><i class="bi bi-clock"></i> <span><?php echo $row['active_until'] ? date('F j, Y', strtotime($row['active_until'])) : 'No expiration'; ?></span></p>
                                    <?php if (isset($row['registration_open_until']) && $row['registration_open_until']): ?>
                                    <p class="announcement-date"><i class="bi bi-calendar-check"></i> <span><?php echo date('F j, Y, g:i A', strtotime($row['registration_open_until'])); ?></span></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-announcements">
                    <div class="info-message">
                        <i class="bi bi-info-circle-fill"></i>
                        No announcements found.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination Controls -->
        <nav class="pagination-container" aria-label="Announcement pagination">
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a class="pagination-item" href="?filter=<?php echo $filter; ?>&page=<?php echo $page-1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                <?php endif; ?>

                <?php
                // Show limited page numbers on mobile
                $startPage = max(1, min($page - 1, $totalPages - 2));
                $endPage = min($totalPages, max($page + 1, 3));
                
                if ($startPage > 1) {
                    echo '<a class="pagination-item" href="?filter=' . $filter . '&page=1">1</a>';
                    if ($startPage > 2) {
                        echo '<span class="pagination-ellipsis">...</span>';
                    }
                }
                
                for ($i = $startPage; $i <= $endPage; $i++):
                ?>
                    <a class="pagination-item <?php echo $i === $page ? 'active' : ''; ?>" href="?filter=<?php echo $filter; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endfor; 
                
                if ($endPage < $totalPages) {
                    if ($endPage < $totalPages - 1) {
                        echo '<span class="pagination-ellipsis">...</span>';
                    }
                    echo '<a class="pagination-item" href="?filter=' . $filter . '&page=' . $totalPages . '">' . $totalPages . '</a>';
                }
                ?>

                <?php if ($page < $totalPages): ?>
                    <a class="pagination-item" href="?filter=<?php echo $filter; ?>&page=<?php echo $page+1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    </main>

    <!-- Announcement Modal -->
    <div class="modal fade announcement-modal" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="announcementModalLabel"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="announcement-modal-content">
                        <div class="image-container position-relative">
                            <img id="announcementImage" src="" alt="Announcement Image" class="img-fluid mb-4" style="display:none; object-fit: cover; width: 100%; max-height: 350px;">
                            <span id="announcementGenre" class="badge genre-badge"></span>
                        </div>
                        
                        <div class="announcement-details">
                            <p><i class="bi bi-calendar-event"></i> <strong>Posted on:</strong> <span id="announcementCreated"></span></p>
                            <p><i class="bi bi-clock"></i> <strong>Active Until:</strong> <span id="announcementActiveUntil"></span></p>
                            <p><i class="bi bi-tag"></i> <strong>Type:</strong> <span id="announcementType"></span></p>
                            <p id="registrationUntilField" style="display:none;"><i class="bi bi-calendar-check"></i> <strong>Registration Open Until:</strong> <span id="announcementRegistrationUntil"></span></p>
                        </div>

                        <div class="announcement-content">
                            <p id="announcementContent" class="mb-4"></p>
                        </div>

                        <div id="eventInfoSection" class="event-info" style="display:none;">
                            <p><i class="bi bi-people-fill"></i> <strong>Maximum Participants:</strong> <span id="maxParticipants"></span></p>
                            <p><i class="bi bi-person-check-fill"></i> <strong>Registered Participants:</strong> <span id="registeredParticipants"></span></p>
                            <p><i class="bi bi-percent"></i> <strong>Occupancy Rate:</strong> <span id="occupancyRate"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center modal-outline modal-glow">
                <div class="modal-header border-0 justify-content-center">
                    <!-- Optional: Add a title or leave empty -->
                </div>
                <div class="modal-body py-4">
                    <!-- Warning Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="#ffc107" class="mb-3" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg>
                    <div class="mb-3 fs-6 text-secondary">Are you sure you want to log out?</div>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2">
                    <button type="button" class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <a href="logout.php" class="btn btn-sm btn-danger px-3">Yes, Log-out</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this announcement? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let announcementIdToDelete = null;
        let activeActionMenu = null;

        function filterAnnouncements(filter) {
            window.location.href = `admin_dashboard.php?filter=${filter}`;
        }

        function toggleActionMenu(id) {
            event.stopPropagation();
            const menuElement = document.getElementById(`actionMenu${id}`);
            
            // Close any currently open menus
            if (activeActionMenu && activeActionMenu !== menuElement) {
                activeActionMenu.classList.remove('show');
            }
            
            menuElement.classList.toggle('show');
            
            if (menuElement.classList.contains('show')) {
                activeActionMenu = menuElement;
            } else {
                activeActionMenu = null;
            }
        }

        function showDeleteConfirmation(id) {
            event.stopPropagation();
            announcementIdToDelete = id; // Store the ID of the announcement to delete
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            deleteModal.show();
            
            // Close any open action menus
            if (activeActionMenu) {
                activeActionMenu.classList.remove('show');
                activeActionMenu = null;
            }
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if (!announcementIdToDelete) return;

            fetch("delete_announcement.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id: announcementIdToDelete }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Reload the page after successful deletion
                    }
                })
                .catch(error => console.error("Error deleting announcement:", error));
        });

        document.querySelectorAll('.announcement-item').forEach(item => {
            item.addEventListener('click', function (event) {
                // Prevent modal trigger if the click is on the header or action menu
                if (event.target.closest('.announcement-header') || event.target.closest('.action-menu')) {
                    return;
                }

                const id = this.getAttribute('data-id');
                fetch(`get_announcement.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error(data.error);
                            return;
                        }

                        // Populate modal with announcement data
                        document.getElementById('announcementModalLabel').textContent = data.title;
                        const announcementImage = document.getElementById('announcementImage');
                        if (data.image_path) {
                            announcementImage.src = data.image_path;
                            announcementImage.style.display = 'block';
                        } else {
                            announcementImage.style.display = 'none';
                        }
                        document.getElementById('announcementContent').textContent = data.content;
                        document.getElementById('announcementGenre').textContent = data.genre;
                        document.getElementById('announcementType').textContent = data.type === 'event' ? 'Event' : 'View-only';

                        // Format dates
                        const createdDate = new Date(data.created_at);
                        const activeUntilDate = data.active_until ? new Date(data.active_until) : null;
                        const registrationUntilDate = data.registration_open_until ? new Date(data.registration_open_until) : null;
                        
                        // Set date displays
                        document.getElementById('announcementCreated').textContent = formatDate(createdDate);
                        document.getElementById('announcementActiveUntil').textContent = activeUntilDate
                            ? formatDate(activeUntilDate)
                            : 'No expiration';
                        
                        // Handle event-specific information
                        const eventInfoSection = document.getElementById('eventInfoSection');
                        const registrationUntilField = document.getElementById('registrationUntilField');
                        
                        if (data.type === 'event') {
                            // Show registration until date for events
                            registrationUntilField.style.display = 'flex';
                            document.getElementById('announcementRegistrationUntil').textContent = registrationUntilDate
                                ? formatDate(registrationUntilDate)
                                : 'Not specified';
                            
                            // Show event information section
                            eventInfoSection.style.display = 'block';
                            document.getElementById('maxParticipants').textContent = data.max_participants || 'Unlimited';
                            
                            const registered = data.registered_participants || 0;
                            document.getElementById('registeredParticipants').textContent = registered;
                            
                            if (data.max_participants) {
                                const occupancyRate = ((registered / data.max_participants) * 100).toFixed(1);
                                document.getElementById('occupancyRate').textContent = `${occupancyRate}%`;
                            } else {
                                document.getElementById('occupancyRate').textContent = 'N/A';
                            }
                        } else {
                            // Hide event-specific fields for view-only announcements
                            registrationUntilField.style.display = 'none';
                            eventInfoSection.style.display = 'none';
                        }

                        // Show the modal
                        const modal = new bootstrap.Modal(document.getElementById('announcementModal'));
                        modal.show();
                        
                        // Helper function to format dates consistently
                        function formatDate(dateObj) {
                            return dateObj.toLocaleDateString('en-US', {
                                month: 'long',
                                day: 'numeric',
                                year: 'numeric',
                            });
                        }
                    })
                    .catch(error => console.error('Error fetching announcement:', error));
            });
        });

        // Close action menus when clicking outside
        document.addEventListener('click', function(event) {
            if (activeActionMenu && !event.target.closest('.action-toggle') && !event.target.closest('.action-menu')) {
                activeActionMenu.classList.remove('show');
                activeActionMenu = null;
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>