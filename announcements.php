<?php
session_start();
include 'db.php';

$sql = "UPDATE announcements
        SET is_active = 0
        WHERE active_until < NOW() AND is_active = 1";
$conn->query($sql);

// Genre filter logic
$genreFilter = isset($_GET['genre']) ? $_GET['genre'] : '';

// Pagination logic
$limit = 12; // Number of announcements per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Modify the SQL query to include LIMIT and OFFSET
$sql = "SELECT id, title, content, genre, image_path, created_at, active_until, registration_open_until, type 
        FROM announcements 
        WHERE is_active = 1";
if (!empty($genreFilter)) {
    $sql .= " AND genre = ?";
}
$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if (!empty($genreFilter)) {
    $stmt->bind_param("sii", $genreFilter, $limit, $offset);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

// Get the total number of announcements for pagination
$countSql = "SELECT COUNT(*) AS total FROM announcements WHERE is_active = 1";
if (!empty($genreFilter)) {
    $countSql .= " AND genre = ?";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param("s", $genreFilter);
} else {
    $countStmt = $conn->prepare($countSql);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalAnnouncements = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalAnnouncements / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/index_head.php'; ?>
<link rel="stylesheet" href="announcements.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<body>
<?php include 'includes/index_header.php'; ?>

<div class="container py-5">

    <h1 class="text-center mb-4">All Announcements</h1>
    <div class="text-center mb-4">
        <form method="GET" action="announcements.php" class="d-inline-block">
            <label for="genreFilter" class="me-2">Filter by Genre:</label>
            <select id="genreFilter" name="genre" class="form-control d-inline-block w-auto">
                <option value="">All</option>
                <option value="Work and Employment" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Work and Employment') ? 'selected' : ''; ?>>Work and Employment</option>
                <option value="Healthcare and Safety" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Healthcare and Safety') ? 'selected' : ''; ?>>Healthcare and Safety</option>
                <option value="Animals" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Animals') ? 'selected' : ''; ?>>Animals</option>
                <option value="Safety" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Safety') ? 'selected' : ''; ?>>Safety</option>
                <option value="Emergency" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Emergency') ? 'selected' : ''; ?>>Emergency</option>
                <option value="Holidays and Events" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Holidays and Events') ? 'selected' : ''; ?>>Holidays and Events</option>
                <option value="Education" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Education') ? 'selected' : ''; ?>>Education</option>
                <option value="Transportation and Traffic" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Transportation and Traffic') ? 'selected' : ''; ?>>Transportation and Traffic</option>
                <option value="Government and Public Affairs" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Government and Public Affairs') ? 'selected' : ''; ?>>Government and Public Affairs</option>
                <option value="Social and Community" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Social and Community') ? 'selected' : ''; ?>>Social and Community</option>
            </select>
            <button type="submit" class="btn btn-primary ms-2">Filter</button>
        </form>
    </div>

    <!-- Announcement Cards -->
    <div class="row justify-content-center">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <a href="#" class="card h-100 shadow-sm announcement-card" data-id="<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                    <?php if (!empty($row['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Announcement Image" class="card-img-top announcement-image" style="height:150px; object-fit:cover;">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars(substr($row['content'], 0, 100)) . '...'; ?></p>
                        <span class="badge genre-badge mb-2"><?php echo htmlspecialchars($row['genre']); ?></span>
                        
                        <?php if ($row['type'] === 'event' && isset($row['registration_open_until'])): ?>
                            <?php
                            $now = new DateTime();
                            $registrationOpenUntil = new DateTime($row['registration_open_until']);
                            ?>
                            <?php if ($now->format('Y-m-d') <= $registrationOpenUntil->format('Y-m-d')): ?>
                                <span class="badge bg-success mb-2 pre-register-badge">Pre-Register Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary mb-2 pre-register-badge">Pre-Registration Closed</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <p class="mb-1"><i class="bi bi-calendar-event"></i> <strong>Posted on:</strong> <?php echo date('F j, Y', strtotime($row['created_at'])); ?></p>
                        
                        <?php if (!empty($row['active_until'])): ?>
                            <p class="mb-2"><i class="bi bi-clock"></i> <strong>Active Until:</strong> <?php echo date('F j, Y', strtotime($row['active_until'])); ?></p>
                        <?php endif; ?>

                        <?php if ($row['type'] === 'event'): ?>
                            <p class="mb-2"><i class="bi bi-calendar-check"></i> <strong>Registration Open Until:</strong> 
                                <?php echo isset($row['registration_open_until']) && $row['registration_open_until'] 
                                    ? date('F j, Y', strtotime($row['registration_open_until'])) 
                                    : 'No deadline'; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&genre=<?php echo urlencode($genreFilter); ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&genre=<?php echo urlencode($genreFilter); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&genre=<?php echo urlencode($genreFilter); ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- Announcement Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl"> <!-- Larger modal size -->
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="announcementModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex">
        <!-- Left Section: Image -->
        <div class="modal-image-container flex-shrink-0 me-4" style="width: 50%;">
          <img id="announcementImage" src="" alt="Announcement Image" class="img-fluid rounded" style="display:none; object-fit: cover; width: 100%; height: 100%;">
        </div>

        <!-- Right Section: Details -->
        <div class="modal-details flex-grow-1">
          <!-- Feedback Alerts -->
          <div id="feedback" class="mb-3"></div>

          <!-- Announcement Content -->
          <p id="announcementContent" class="mb-3"></p>
          <span id="announcementGenre" class="badge genre-badge mb-3"></span> <!-- Styled genre badge -->
          <span id="preRegisterBadge" class="badge bg-success mb-3" style="display:none;">Pre-Register Active</span>
          <p><i class="bi bi-calendar-event"></i> <strong>Posted on:</strong> <span id="announcementCreated"></span></p>
          <p id="announcementActiveUntilContainer" style="display:none;"><i class="bi bi-clock"></i> <strong>Active Until:</strong> <span id="announcementActiveUntil"></span></p>

          <!-- Event Details -->
          <div id="eventDetails" style="display:none;">
            <p><i class="bi bi-people"></i> <strong>Max Participants:</strong> <span id="maxParticipants"></span></p>
            <p><i class="bi bi-person-check"></i> <strong>Registered Participants:</strong> <span id="registeredParticipants"></span></p>
            <p><i class="bi bi-calendar-check"></i> <strong>Registration Open Until:</strong> <span id="registrationOpenUntil"></span></p>

            <button id="preRegisterBtn" class="btn btn-primary mt-3" disabled>Pre-Register</button>
          </div>
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
<script>
document.querySelectorAll('.announcement-card').forEach(card => {
    card.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent default anchor behavior
        const id = this.getAttribute('data-id');

        // Set the announcement ID in the modal
        document.getElementById('announcementModal').setAttribute('data-id', id);

        // Reset modal elements
        document.getElementById('announcementModalLabel').textContent = '';
        document.getElementById('announcementImage').style.display = 'none';
        document.getElementById('announcementContent').textContent = '';
        document.getElementById('announcementGenre').textContent = '';
        document.getElementById('announcementActiveUntilContainer').style.display = 'none';
        document.getElementById('announcementActiveUntil').textContent = '';
        document.getElementById('eventDetails').style.display = 'none';
        document.getElementById('registrationOpenUntil').textContent = '';
        document.getElementById('preRegisterBadge').style.display = 'none';
        const preRegisterBtn = document.getElementById('preRegisterBtn');
        preRegisterBtn.disabled = true;
        preRegisterBtn.textContent = 'Pre-Register';

        // Fetch announcement details
        fetch('get_announcement.php?id=' + id)
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
                }
                document.getElementById('announcementContent').textContent = data.content;
                document.getElementById('announcementGenre').textContent = data.genre;

                // Format and display dates
                const createdDate = new Date(data.created_at);
                const activeUntilDate = data.active_until ? new Date(data.active_until) : null;
                document.getElementById('announcementCreated').textContent = createdDate.toLocaleDateString('en-US', {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                });
                if (activeUntilDate) {
                    document.getElementById('announcementActiveUntilContainer').style.display = 'block';
                    document.getElementById('announcementActiveUntil').textContent = activeUntilDate.toLocaleDateString('en-US', {
                        month: 'long',
                        day: 'numeric',
                        year: 'numeric'
                    });
                }

                // Handle event-specific details
                if (data.type === 'event') {
                    document.getElementById('eventDetails').style.display = 'block';
                    document.getElementById('maxParticipants').textContent = data.max_participants;
                    document.getElementById('registeredParticipants').textContent = data.registered_participants;

                    const registrationOpenUntil = data.registration_open_until ? new Date(data.registration_open_until) : null;
                    const now = new Date();

                    if (registrationOpenUntil) {
                        const registrationDate = new Date(registrationOpenUntil);

                        // Compare only the date parts (ignore time)
                        if (now.toISOString().split('T')[0] <= registrationDate.toISOString().split('T')[0]) {
                            document.getElementById('preRegisterBadge').style.display = 'inline-block';
                        } else {
                            document.getElementById('preRegisterBadge').style.display = 'none';
                        }

                        document.getElementById('registrationOpenUntil').textContent = registrationDate.toLocaleDateString('en-US', {
                            month: 'long',
                            day: 'numeric',
                            year: 'numeric',
                        });
                    } else {
                        document.getElementById('registrationOpenUntil').textContent = 'No deadline';
                        document.getElementById('preRegisterBadge').style.display = 'none';
                    }

                    // Enable or disable the "Pre-Register" button
                    const preRegisterBtn = document.getElementById('preRegisterBtn');
                    if (registrationOpenUntil) {
                        const registrationDate = new Date(registrationOpenUntil);

                        // Compare only the date parts (ignore time)
                        if (now.toISOString().split('T')[0] <= registrationDate.toISOString().split('T')[0]) {
                            preRegisterBtn.disabled = false;
                            preRegisterBtn.textContent = 'Pre-Register';
                        } else {
                            preRegisterBtn.disabled = true;
                            preRegisterBtn.textContent = 'Registration Closed';
                        }
                    } else {
                        preRegisterBtn.disabled = true;
                        preRegisterBtn.textContent = 'Registration Closed';
                    }
                } else {
                    document.getElementById('eventDetails').style.display = 'none';
                    document.getElementById('preRegisterBadge').style.display = 'none';
                }

                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('announcementModal'));
                modal.show();
            })
            .catch(error => {
                console.error(error);
                const feedback = document.getElementById('feedback');
                feedback.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> Failed to load announcement details. Please try again later.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            });
    });
});

document.getElementById('preRegisterBtn').addEventListener('click', function () {
    const announcementId = document.getElementById('announcementModal').getAttribute('data-id');

    // Send a request to the backend to handle pre-registration
    fetch('pre_register.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: announcementId }),
    })
        .then(response => response.json())
        .then(data => {
            const feedback = document.getElementById('feedback');
            if (data.success) {
                feedback.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i> You have successfully pre-registered for this event!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            } else {
                let errorMessage = data.error;

                // Handle specific error messages
                if (data.error === 'You must be logged in to pre-register for this event.') {
                    errorMessage = 'Please log in to pre-register for this event.';
                }

                feedback.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> ${errorMessage}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const feedback = document.getElementById('feedback');
            feedback.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> An error occurred while trying to pre-register. Please try again later.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
        });
});

function updatePreRegistrationBadges() {
    fetch('check_registration_status.php')
        .then(response => response.json())
        .then(data => {
            console.log('API Response:', data); // Debugging: Log the API response
            data.forEach(item => {
                const card = document.querySelector(`.announcement-card[data-id="${item.id}"]`);
                if (card) {
                    const badge = card.querySelector('.pre-register-badge');
                    if (badge) { // Ensure the badge exists
                        if (item.is_active === true) { // Explicitly check for true
                            badge.textContent = 'Pre-Register Active';
                            badge.classList.remove('bg-secondary');
                            badge.classList.add('bg-success');
                        } else {
                            badge.textContent = 'Pre-Registration Closed';
                            badge.classList.remove('bg-success');
                            badge.classList.add('bg-secondary');
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error updating badges:', error));
}

// Poll the API every 30 seconds (adjust as needed)
setInterval(updatePreRegistrationBadges, 30000);

// Call it once immediately to update on page load
updatePreRegistrationBadges();

// Clear feedback message when the modal is closed
const announcementModal = document.getElementById('announcementModal');
announcementModal.addEventListener('hidden.bs.modal', function () {
    const feedback = document.getElementById('feedback');
    feedback.innerHTML = ''; // Clear the feedback message
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'includes/footer.php'; ?>
</body>
</html>