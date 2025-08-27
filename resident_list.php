<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Pagination settings
$limit = 10; // Number of residents per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Prepare search and filter conditions
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$verificationFilter = isset($_GET['verification']) ? $_GET['verification'] : 'all';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'date_registered';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Build SQL conditions
$conditions = [];
$params = [];

if (!empty($searchTerm)) {
    $conditions[] = "(first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone_number LIKE ? OR street LIKE ?)";
    $searchPattern = "%$searchTerm%";
    for ($i = 0; $i < 6; $i++) {
        $params[] = $searchPattern;
    }
}

if ($verificationFilter !== 'all') {
    $conditions[] = "is_verified = ?";
    $params[] = ($verificationFilter === 'verified') ? 1 : 0;
}

$whereClause = '';
if (!empty($conditions)) {
    $whereClause = "WHERE " . implode(" AND ", $conditions);
}

// Count total residents
$countSql = "SELECT COUNT(*) as total FROM users $whereClause";
$stmt = $conn->prepare($countSql);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$totalResidents = $stmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalResidents / $limit);

// Fetch residents with pagination and sorting
$sql = "SELECT * FROM users $whereClause ORDER BY $sortBy $sortOrder LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

// Combine all parameters for the final query
$params[] = $limit;
$params[] = $offset;
$types = str_repeat('s', count($params) - 2) . 'ii'; // All previous params are strings, last two are integers
$stmt->bind_param($types, ...$params);
$stmt->execute();
$residents = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/admin_head.php'; ?>
<link href="resident_list.css" rel="stylesheet">
<body>
    <?php include 'includes/admin_navbar.php'; ?>

    <main class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="resident-container">
                    <div class="header-actions">
                        <h2 class="header-title">Resident List</h2>
                        <div class="search-export-container">
                            <div class="search-container">
                                <form action="" method="GET" id="searchForm">
                                    <input type="hidden" name="verification" value="<?php echo $verificationFilter; ?>">
                                    <input type="hidden" name="sort" value="<?php echo $sortBy; ?>">
                                    <input type="hidden" name="order" value="<?php echo $sortOrder; ?>">
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        placeholder="Search residents..." 
                                        name="search" 
                                        id="searchInput"
                                        value="<?php echo htmlspecialchars($searchTerm); ?>"
                                        onkeydown="if(event.key === 'Enter') { this.form.dispatchEvent(new Event('submit')); return false; }"
                                    >
                                    <?php if (!empty($searchTerm)): ?>
                                    <i class="bi bi-x-circle clear-search" id="clearSearch"></i>
                                    <?php endif; ?>
                                    <i class="bi bi-search search-icon" id="searchIcon"></i>
                                </form>
                            </div>
                            <button class="btn export-btn" id="exportCSV">
                                <i class="bi bi-download me-2"></i> Export CSV
                            </button>
                        </div>
                    </div>

                    <div class="filter-container">
                        <span class="filter-label">Filter by:</span>
                        <select class="form-select filter-select" id="verificationFilter">
                            <option value="all" <?php echo $verificationFilter === 'all' ? 'selected' : ''; ?>>All Residents</option>
                            <option value="verified" <?php echo $verificationFilter === 'verified' ? 'selected' : ''; ?>>Verified</option>
                            <option value="unverified" <?php echo $verificationFilter === 'unverified' ? 'selected' : ''; ?>>Unverified</option>
                        </select>
                    </div>

                    <div class="table-container">
                        <?php if ($residents->num_rows > 0): ?>
                            <table class="table resident-table">
                                <thead>
                                    <tr>
                                        <th class="sortable" data-sort="id">ID</th>
                                        <th class="sortable" data-sort="first_name">Name</th>
                                        <th class="sortable" data-sort="gender">Gender</th>
                                        <th class="sortable" data-sort="email">Email</th>
                                        <th class="sortable" data-sort="phone_number">Phone</th>
                                        <th>Address</th>
                                        <th class="sortable" data-sort="date_registered">Registered</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($resident = $residents->fetch_assoc()): ?>
                                        <tr class="clickable-row" data-id="<?php echo $resident['id']; ?>">
                                            <td><?php echo $resident['id']; ?></td>
                                            <td>
                                                <?php 
                                                echo htmlspecialchars($resident['first_name']) . ' ';
                                                if (!empty($resident['middle_name'])) {
                                                    echo htmlspecialchars($resident['middle_name']) . ' ';
                                                }
                                                echo htmlspecialchars($resident['last_name']);
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($resident['gender']); ?></td>
                                            <td><?php echo htmlspecialchars($resident['email']); ?></td>
                                            <td><?php echo htmlspecialchars($resident['phone_number']); ?></td>
                                            <td>
                                                <?php 
                                                echo htmlspecialchars($resident['house_number'] . ' ' . $resident['street']);
                                                if (!empty($resident['lot_block'])) {
                                                    echo ', ' . htmlspecialchars($resident['lot_block']);
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($resident['date_registered'])); ?></td>
                                            <td>
                                                <?php if ($resident['is_verified']): ?>
                                                    <span class="status-badge status-verified">Verified</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-unverified">Unverified</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                            
                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                                <div class="pagination-container">
                                    <div class="page-info">
                                        Showing 
                                        <?php echo $offset + 1; ?> - 
                                        <?php echo min($offset + $limit, $totalResidents); ?> of 
                                        <?php echo $totalResidents; ?>
                                    </div>
                                    <ul class="pagination">
                                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="<?php echo '?page=' . ($page - 1) . '&search=' . urlencode($searchTerm) . '&verification=' . $verificationFilter . '&sort=' . $sortBy . '&order=' . $sortOrder; ?>">
                                                <i class="bi bi-chevron-left"></i>
                                            </a>
                                        </li>
                                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                                <a class="page-link" href="<?php echo '?page=' . $i . '&search=' . urlencode($searchTerm) . '&verification=' . $verificationFilter . '&sort=' . $sortBy . '&order=' . $sortOrder; ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="<?php echo '?page=' . ($page + 1) . '&search=' . urlencode($searchTerm) . '&verification=' . $verificationFilter . '&sort=' . $sortBy . '&order=' . $sortOrder; ?>">
                                                <i class="bi bi-chevron-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                        <?php else: ?>
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="bi bi-people"></i>
                                </div>
                                <h4>No residents found</h4>
                                <p class="empty-state-text">
                                    <?php 
                                    if (!empty($searchTerm)) {
                                        echo "No residents match your search criteria. Try a different search term.";
                                    } else if ($verificationFilter !== 'all') {
                                        echo "No " . strtolower($verificationFilter) . " residents found.";
                                    } else {
                                        echo "No residents have registered yet.";
                                    }
                                    ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Resident Detail Modal -->
    <div class="modal fade resident-detail-modal" id="residentDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Resident Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="profile-container">
                        <div class="profile-placeholder" id="profileInitials"></div>
                    </div>
                    
                    <div class="detail-section">
                        <h5>Personal Information</h5>
                        <div class="detail-row">
                            <div class="detail-label">Full Name</div>
                            <div class="detail-value" id="residentName"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Gender</div>
                            <div class="detail-value" id="residentGender"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Birthdate</div>
                            <div class="detail-value" id="residentBirthdate"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Blood Type</div>
                            <div class="detail-value" id="residentBloodType"></div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h5>Contact Information</h5>
                        <div class="detail-row">
                            <div class="detail-label">Email</div>
                            <div class="detail-value" id="residentEmail"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Phone</div>
                            <div class="detail-value" id="residentPhone"></div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h5>Address Information</h5>
                        <div class="detail-row">
                            <div class="detail-label">House Number</div>
                            <div class="detail-value" id="residentHouseNumber"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Street</div>
                            <div class="detail-value" id="residentStreet"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Lot/Block</div>
                            <div class="detail-value" id="residentLotBlock"></div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h5>Account Information</h5>
                        <div class="detail-row">
                            <div class="detail-label">Username</div>
                            <div class="detail-value" id="residentUsername"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Date Registered</div>
                            <div class="detail-value" id="residentDateRegistered"></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Verification Status</div>
                            <div class="detail-value" id="residentVerificationStatus"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="verifyResidentBtn">Verify Resident</button>
                    <button type="button" class="btn btn-danger" id="deleteResidentBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Confirmation Modal -->
    <div class="modal fade" id="verifyConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Verify Resident</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to verify this resident? This will grant them full access to the barangay services.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmVerifyBtn">Verify</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Resident</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this resident? This action cannot be undone.</p>
                    <p><strong>Warning:</strong> Deleting a resident will remove all their data including document requests, reservations, and other records.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle sorting
            document.querySelectorAll('th.sortable').forEach(header => {
                header.addEventListener('click', function() {
                    const sort = this.getAttribute('data-sort');
                    let order = 'ASC';
                    
                    // If already sorting by this column, toggle the order
                    const currentSort = new URLSearchParams(window.location.search).get('sort');
                    const currentOrder = new URLSearchParams(window.location.search).get('order');
                    
                    if (currentSort === sort && currentOrder === 'ASC') {
                        order = 'DESC';
                    }
                    
                    // Update URL with new sort parameters
                    const url = new URL(window.location);
                    url.searchParams.set('sort', sort);
                    url.searchParams.set('order', order);
                    window.location = url.toString();
                });
            });
            
            // Mark current sort column
            const currentSort = '<?php echo $sortBy; ?>';
            const currentOrder = '<?php echo $sortOrder; ?>';
            if (currentSort) {
                const header = document.querySelector(`th[data-sort="${currentSort}"]`);
                if (header) {
                    header.classList.add(currentOrder === 'ASC' ? 'sort-asc' : 'sort-desc');
                }
            }
            
            // Handle filter change
            document.getElementById('verificationFilter').addEventListener('change', function() {
                const url = new URL(window.location);
                url.searchParams.set('verification', this.value);
                url.searchParams.set('page', 1); // Reset to first page
                window.location = url.toString();
            });
            
            // Handle search form submission
            document.getElementById('searchForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const searchTerm = this.querySelector('input[name="search"]').value;
                const url = new URL(window.location);
                url.searchParams.set('search', searchTerm);
                url.searchParams.set('page', 1); // Reset to first page
                window.location = url.toString();
            });
            
            // Make search icon clickable
            document.getElementById('searchIcon').addEventListener('click', function() {
                document.getElementById('searchForm').dispatchEvent(new Event('submit'));
            });
            
            // Clear search functionality
            const clearSearchBtn = document.getElementById('clearSearch');
            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', function() {
                    document.getElementById('searchInput').value = '';
                    document.getElementById('searchForm').dispatchEvent(new Event('submit'));
                });
            }
            
            // CSV Export
            document.getElementById('exportCSV').addEventListener('click', function() {
                window.location = 'export_residents.php?<?php echo http_build_query(['search' => $searchTerm, 'verification' => $verificationFilter]); ?>';
            });
            
            // Make table rows clickable and handle view resident details
            let selectedResidentId = null;
            
            document.querySelectorAll('.clickable-row').forEach(row => {
                row.addEventListener('click', function() {
                    const residentId = this.getAttribute('data-id');
                    selectedResidentId = residentId;
                    
                    // Add visual feedback when row is clicked
                    document.querySelectorAll('.clickable-row').forEach(r => r.classList.remove('active-row'));
                    this.classList.add('active-row');
                    
                    // Show loading indicator in modal
                    document.getElementById('residentName').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
                    const detailModal = new bootstrap.Modal(document.getElementById('residentDetailModal'));
                    detailModal.show();
                    
                    // Fetch resident details
                    fetch(`fetch_resident.php?id=${residentId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                document.getElementById('residentName').innerHTML = '<i class="fas fa-exclamation-circle text-danger"></i> Error';
                                document.querySelector('.modal-body').innerHTML = `<div class="alert alert-danger">
                                    <strong>Error:</strong> ${data.error}
                                </div>`;
                                document.querySelectorAll('.modal-footer button:not([data-bs-dismiss="modal"])').forEach(btn => btn.style.display = 'none');
                                return;
                            }
                            
                            // Populate modal with resident data
                            document.getElementById('residentName').textContent = `${data.first_name} ${data.middle_name || ''} ${data.last_name}`;
                            document.getElementById('residentGender').textContent = data.gender;
                            document.getElementById('residentBirthdate').textContent = new Date(data.birthdate).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });
                            document.getElementById('residentBloodType').textContent = data.blood_type || 'Not provided';
                            document.getElementById('residentEmail').textContent = data.email;
                            document.getElementById('residentPhone').textContent = data.phone_number;
                            document.getElementById('residentHouseNumber').textContent = data.house_number;
                            document.getElementById('residentStreet').textContent = data.street;
                            document.getElementById('residentLotBlock').textContent = data.lot_block || 'Not provided';
                            document.getElementById('residentUsername').textContent = data.username;
                            document.getElementById('residentDateRegistered').textContent = new Date(data.date_registered).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            
                            // Set verification status
                            const verificationStatus = document.getElementById('residentVerificationStatus');
                            if (data.is_verified == 1) {
                                verificationStatus.textContent = 'Verified';
                                verificationStatus.className = 'detail-value text-success fw-bold';
                                document.getElementById('verifyResidentBtn').style.display = 'none';
                            } else {
                                verificationStatus.textContent = 'Not Verified';
                                verificationStatus.className = 'detail-value text-danger fw-bold';
                                document.getElementById('verifyResidentBtn').style.display = 'block';
                            }
                            
                            // Set profile initials
                            const initials = data.first_name.charAt(0) + data.last_name.charAt(0);
                            document.getElementById('profileInitials').textContent = initials.toUpperCase();
                            
                            // Show/hide delete button
                            document.getElementById('deleteResidentBtn').style.display = 'block';
                            
                            // Show the modal
                            const detailModal = new bootstrap.Modal(document.getElementById('residentDetailModal'));
                            detailModal.show();
                        })
                        .catch(error => {
                            console.error('Error fetching resident details:', error);
                            document.getElementById('residentName').innerHTML = '<i class="fas fa-exclamation-triangle text-danger"></i> Error';
                            document.querySelector('.modal-body').innerHTML = `<div class="alert alert-danger">
                                <strong>Network Error:</strong> Could not fetch resident details. Please check your connection and try again.
                            </div>`;
                            document.querySelectorAll('.modal-footer button:not([data-bs-dismiss="modal"])').forEach(btn => btn.style.display = 'none');
                        });
                });
            });
            
            // Clear active row state when modal is closed
            document.getElementById('residentDetailModal').addEventListener('hidden.bs.modal', function () {
                document.querySelectorAll('.clickable-row').forEach(r => r.classList.remove('active-row'));
                selectedResidentId = null;
            });
            
            // Handle verify button in resident detail modal
            document.getElementById('verifyResidentBtn').addEventListener('click', function() {
                if (!selectedResidentId) return;
                
                const verifyModal = new bootstrap.Modal(document.getElementById('verifyConfirmationModal'));
                verifyModal.show();
                
                // Hide the detail modal
                const detailModal = bootstrap.Modal.getInstance(document.getElementById('residentDetailModal'));
                detailModal.hide();
            });
            
            // Handle delete button in resident detail modal
            document.getElementById('deleteResidentBtn').addEventListener('click', function() {
                if (!selectedResidentId) return;
                
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
                deleteModal.show();
                
                // Hide the detail modal
                const detailModal = bootstrap.Modal.getInstance(document.getElementById('residentDetailModal'));
                detailModal.hide();
            });
            
            // Confirm verification
            document.getElementById('confirmVerifyBtn').addEventListener('click', function() {
                if (!selectedResidentId) return;
                
                // Send verification request
                fetch('verify_resident.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `resident_id=${selectedResidentId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload page to reflect changes
                        window.location.reload();
                    } else {
                        alert('Error verifying resident: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            });
            
            // Confirm deletion
            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                if (!selectedResidentId) return;
                
                // Send delete request
                fetch('delete_resident.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `resident_id=${selectedResidentId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload page to reflect changes
                        window.location.reload();
                    } else {
                        alert('Error deleting resident: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    </script>
</body>
</html>
