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
    <link href= "admin_dashboard.css" rel="stylesheet">
<body>
    <?php include 'includes/admin_navbar.php'; ?>

    <main class="container mt-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Manage Announcements</h2>
            
            <div>
                <!-- Add Announcement Button -->
                <button class="btn btn-primary me-2" onclick="location.href='add_announcement.php'">Add Announcement</button>
                <!-- Filter Dropdown -->
                <select id="announcementFilter" class="form-select w-auto d-inline-block" onchange="filterAnnouncements(this.value)">
                    <option value="active" <?php echo $filter === 'active' ? 'selected' : ''; ?>>Active Announcements</option>
                    <option value="archived" <?php echo $filter === 'archived' ? 'selected' : ''; ?>>Archived Announcements</option>
                </select>
            </div>
        </div>

        <!-- Announcements Container -->
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm announcement-card" data-id="<?php echo $row['id']; ?>" style="cursor: pointer;">
                        <!-- Top Section: Title and Dropdown -->
                        <div class="card-header d-flex justify-content-between align-items-center bg-light" style="cursor: default;">
                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle" type="button" id="actionMenu<?php echo $row['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots"></i> <!-- Triple-dot icon -->
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="actionMenu<?php echo $row['id']; ?>">
                                    <li>
                                        <a class="dropdown-item" href="edit_announcement.php?id=<?php echo $row['id']; ?>">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <button class="dropdown-item text-danger" onclick="showDeleteConfirmation(<?php echo $row['id']; ?>)">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- Main Content -->
                        <div class="row g-0">
                            <!-- Left Side: Image -->
                            <div class="col-md-4">
                                <img src="<?php echo htmlspecialchars($row['image_path'] ?? 'uploads/default.jpg'); ?>" 
                                     alt="Announcement Image" 
                                     class="img-fluid rounded-start" 
                                     style="height: 100%; object-fit: cover;">
                            </div>
                            <!-- Right Side: Information -->
                            <div class="col-md-8">
                                <div class="card-body">
                                    <span class="badge genre-badge mb-2" data-genre="<?php echo htmlspecialchars($row['genre']); ?>">
                                        <?php echo htmlspecialchars($row['genre']); ?>
                                    </span>
                                    <p class="card-text"><?php echo htmlspecialchars(substr($row['content'], 0, 100)) . '...'; ?></p>
                                    <p class="card-text"><i class="bi bi-calendar-event"></i> <strong>Posted on:</strong> <?php echo date('F j, Y', strtotime($row['created_at'])); ?></p>
                                    <p class="card-text"><i class="bi bi-clock"></i> <strong>Active Until:</strong> <?php echo $row['active_until'] ? date('F j, Y', strtotime($row['active_until'])) : 'No expiration'; ?></p>
                                    <p class="card-text"><i class="bi bi-calendar-check"></i> <strong>Registration Open Until:</strong> 
                                        <?php echo isset($row['registration_open_until']) && $row['registration_open_until'] 
                                            ? date('F j, Y, g:i A', strtotime($row['registration_open_until'])) 
                                            : 'No deadline'; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination Controls -->
        <nav>
            <ul class="pagination justify-content-center mt-4">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?filter=<?php echo $filter; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </main>

    <!-- Announcement Modal -->
    <div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="announcementModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="announcementImage" src="" alt="Announcement Image" class="img-fluid rounded mb-3" style="display:none; object-fit: cover; width: 100%; height: auto;">
                    <p id="announcementContent"></p>
                    <span id="announcementGenre" class="badge genre-badge mb-3"></span>
                    <p><i class="bi bi-calendar-event"></i> <strong>Posted on:</strong> <span id="announcementCreated"></span></p>
                    <p><i class="bi bi-clock"></i> <strong>Active Until:</strong> <span id="announcementActiveUntil"></span></p>
                </div>
            </div>
        </div>
    </div>

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

        function filterAnnouncements(filter) {
            window.location.href = `admin_dashboard.php?filter=${filter}`;
        }

        function showDeleteConfirmation(id) {
            announcementIdToDelete = id; // Store the ID of the announcement to delete
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            deleteModal.show();
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

        document.querySelectorAll('.announcement-card').forEach(card => {
            card.addEventListener('click', function (event) {
                // Prevent modal trigger if the click is on the card-header or dropdown menu
                if (event.target.closest('.card-header') || event.target.closest('.dropdown-menu')) {
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

                        const createdDate = new Date(data.created_at);
                        const activeUntilDate = data.active_until ? new Date(data.active_until) : null;
                        document.getElementById('announcementCreated').textContent = createdDate.toLocaleDateString('en-US', {
                            month: 'long',
                            day: 'numeric',
                            year: 'numeric',
                        });
                        document.getElementById('announcementActiveUntil').textContent = activeUntilDate
                            ? activeUntilDate.toLocaleDateString('en-US', {
                                  month: 'long',
                                  day: 'numeric',
                                  year: 'numeric',
                              })
                            : 'No expiration';

                        // Show the modal
                        const modal = new bootstrap.Modal(document.getElementById('announcementModal'));
                        modal.show();
                    })
                    .catch(error => console.error('Error fetching announcement:', error));
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>