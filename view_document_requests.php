<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Include database connection

// Get the selected document type and status for filtering
$document_type = isset($_GET['document_type']) ? $_GET['document_type'] : 'all';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Pagination logic
$limit = 8; // Number of requests per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query logic
if ($document_type !== 'all') {
    $queryFile = "includes/{$document_type}_query.php";
    if (file_exists($queryFile)) {
        include $queryFile; // Include the query file

        // Add status condition to the query
        if ($statusFilter !== 'all') {
            if (strpos($sql, 'WHERE') !== false) {
                $sql .= " AND status = '$statusFilter'";
            } else {
                $sql .= " WHERE status = '$statusFilter'";
            }
        }

        $sql .= " LIMIT $limit OFFSET $offset"; // Add pagination to the query
    } else {
        die("Error: Query file for document type '{$document_type}' not found.");
    }

    $result = $conn->query($sql);

    if (!$result) {
        die("Error fetching document requests: " . $conn->error);
    }

    // Get total number of requests for pagination
    $countSql = "SELECT COUNT(*) AS total FROM {$document_type}";
    if ($statusFilter !== 'all') {
        if (strpos($countSql, 'WHERE') !== false) {
            $countSql .= " AND status = '$statusFilter'";
        } else {
            $countSql .= " WHERE status = '$statusFilter'";
        }
    }
    $countResult = $conn->query($countSql);

    if (!$countResult) {
        die("Error fetching total requests: " . $conn->error);
    }

    $totalRequests = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRequests / $limit);
} else {
    $result = null;
    $totalPages = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
    <?php include 'includes/admin_head.php'; ?>
    <link href="view_document_requests.css" rel="stylesheet">
    <link href="admin_dashboard.css" rel="stylesheet">
    <link href="history_modal.css" rel="stylesheet">
    <link href="modern_buttons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


<body>
    <?php include 'includes/admin_navbar.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="document-container">
                    <div class="header-actions">
                        <h2 class="header-title">Document Requests</h2>
                    </div>

                    <!-- Filters -->
                    <div class="filter-container">
                        <div class="d-flex align-items-center me-4">
                            <label for="document_type" class="filter-label me-2">Document Type:</label>
                            <select id="document_type" class="form-select filter-select" onchange="filterRequests()">
                                <option value="all" <?php if ($document_type === 'all') echo 'selected'; ?>>Select Document Type</option>
                                <option value="repair_and_construction" <?php if ($document_type === 'repair_and_construction') echo 'selected'; ?>>Repair and Construction</option>
                                <option value="work_permit_utilities" <?php if ($document_type === 'work_permit_utilities') echo 'selected'; ?>>Work Permit for Utilities</option>
                                <option value="certificate_of_residency" <?php if ($document_type === 'certificate_of_residency') echo 'selected'; ?>>Certificate of Residency</option>
                                <option value="certificate_of_indigency" <?php if ($document_type === 'certificate_of_indigency') echo 'selected'; ?>>Certificate of Indigency</option>
                                <option value="new_business_permit" <?php if ($document_type === 'new_business_permit') echo 'selected'; ?>>New Business Permit</option>
                                <option value="clearance_major_construction" <?php if ($document_type === 'clearance_major_construction') echo 'selected'; ?>>Clearance for Major Construction</option>
                </select>
                        </div>

                        <div class="d-flex align-items-center">
                            <label for="statusFilter" class="filter-label me-2">Status:</label>
                            <select id="statusFilter" class="form-select filter-select" onchange="filterRequests()">
                                <option value="all" <?php if ($statusFilter === 'all') echo 'selected'; ?>>All</option>
                                <option value="pending" <?php if ($statusFilter === 'pending') echo 'selected'; ?>>Pending</option>
                                <option value="approved" <?php if ($statusFilter === 'approved') echo 'selected'; ?>>Approved</option>
                                <option value="rejected" <?php if ($statusFilter === 'rejected') echo 'selected'; ?>>Rejected</option>
                                <option value="picked_up" <?php if ($statusFilter === 'picked_up') echo 'selected'; ?>>Picked Up</option>
                            </select>
                        </div>
                    </div>

                    <?php if ($document_type === 'all'): ?>
                        <div class="empty-state">
                            <i class="fas fa-file-alt empty-state-icon"></i>
                            <p class="empty-state-text">Please select a document type to view the requests.</p>
                        </div>
                    <?php elseif ($result && $result->num_rows > 0): ?>
                        <div class="table-container">
                            <table class="document-table">
                                <thead>
                                    <tr>
                                        <th>Control No.</th>
                                        <th>Full Name</th>
                                        <th>Created At</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <?php 
                                            // Get the prefix for the current document type
                                            $prefix = getDocumentPrefix($document_type);

                                            // Format the control number with the prefix and zero-padded ID
                                            $controlNumber = sprintf('%s-%03d', $prefix, $row['id']);
                                            // Removed status class from row
                                        ?>
                                        <tr class="clickable-row" 
                                            data-id="<?php echo $row['id']; ?>" 
                                            data-document-type="<?php echo $document_type; ?>" 
                                            data-status="<?php echo $row['status']; ?>" 
                                            data-full-name="<?php echo htmlspecialchars($row['full_name'] ?? $row['owner']); ?>">
                                            <td><?php echo $controlNumber; ?></td>
                                            <td>
                                                <?php 
                                                if ($document_type === 'new_business_permit') {
                                                    echo htmlspecialchars($row['owner']); // Use 'owner' for new_business_permit
                                } else {
                                    echo htmlspecialchars($row['full_name']); // Use 'full_name' for other document types
                                }
                                ?>
                            </td>
                                            <td><?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $row['status']; ?>">
                                                    <?php 
                                                    if ($row['status'] === 'picked_up') {
                                                        echo 'Picked&nbsp;Up'; // Non-breaking space to keep on one line
                                                    } else {
                                                        echo ucfirst($row['status']);
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Controls -->
                        <div class="pagination-container">
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?document_type=<?php echo $document_type; ?>&status=<?php echo $statusFilter; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                    <?php endfor; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-search empty-state-icon"></i>
                            <p class="empty-state-text">No records found for the selected document type and status.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>    <!-- Approve Modal -->
    <div class="modal fade document-detail-modal" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-animation">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approve Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-section">
                        <form id="approveForm">
                            <input type="hidden" id="approveRequestId" name="id">
                            <input type="hidden" id="approveDocumentType" name="document_type">
                            <div class="detail-row">
                                <div class="detail-label">
                                    <i class="fas fa-calendar-alt me-2"></i> Pickup Date:
                                </div>
                                <div class="detail-value">
                                    <input type="datetime-local" id="pickupDate" name="pickup_date" class="form-control" required>
                                    <small class="text-muted">You cannot select dates from the past</small>
                                </div>
                            </div>
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i> Confirm Approval
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

<!-- Reject Modal -->
    <div class="modal fade document-detail-modal" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-animation">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-section">
                        <form id="rejectForm">
                            <input type="hidden" id="rejectRequestId" name="id">
                            <input type="hidden" id="rejectDocumentType" name="document_type">
                            <div class="detail-row">
                                <div class="detail-label">
                                    <i class="fas fa-times-circle me-2"></i> Rejection Reason:
                                </div>
                                <div class="detail-value">
                                    <textarea id="rejectionReason" name="rejection_reason" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-ban me-2"></i> Confirm Rejection
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Request Details -->
    <div class="modal fade document-detail-modal" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="requestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-animation" id="modal-content">
                <!-- Modal content will be loaded dynamically -->
                <!-- This modal's content is populated via JavaScript -->
                <div class="modal-header">
                    <h5 class="modal-title" id="requestModalLabel">Document Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be inserted here dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Modal -->
    <div class="modal fade document-detail-modal" id="actionsModal" tabindex="-1" role="dialog" aria-labelledby="actionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-animation">
                <div class="modal-header">
                    <h5 class="modal-title" id="actionsModalLabel">Document Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-row">
                        <span class="detail-icon">
                            <i class="fas fa-hashtag"></i>
                        </span>
                        <div class="detail-label">Control Number:</div>
                        <div class="detail-value" id="modalControlNumber"></div>
                    </div>
                    <div class="detail-row">
                        <span class="detail-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        <div class="detail-label">Full Name:</div>
                        <div class="detail-value" id="modalFullName"></div>
                    </div>
                    <div class="detail-row">
                        <span class="detail-icon">
                            <i class="fas fa-info-circle"></i>
                        </span>
                        <div class="detail-label">Status:</div>
                        <div class="detail-value" id="modalStatus"></div>
                    </div>
                    
                    <div class="actions-section">
                        <h5><i class="fas fa-tools me-2"></i>Actions</h5>
                        <p class="text-muted small">Select an action to perform on this request</p>
                        <div id="modalActions">
                            <!-- Action buttons will be dynamically added here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- History Modal -->
    <div class="modal fade document-detail-modal" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-animation">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyModalLabel">Request History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-section">
                        <div class="d-flex align-items-center mb-3">
                            <div class="detail-icon">
                                <i class="fas fa-history"></i>
                            </div>
                            <div class="fs-5 fw-bold">Activity Log</div>
                        </div>
                        <p class="text-muted small mb-3">This log shows all actions performed on this request.</p>
                        <div>
                            <ul id="historyList" class="list-group">
                                <!-- History items will be dynamically loaded here -->
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Picked Up Modal -->
    <div class="modal fade document-detail-modal" id="pickedUpModal" tabindex="-1" role="dialog" aria-labelledby="pickedUpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-animation">
                <div class="modal-header">
                    <h5 class="modal-title" id="pickedUpModalLabel">Mark as Picked Up</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-section">
                        <form id="pickedUpForm">
                            <input type="hidden" id="pickedUpRequestId" name="id">
                            <input type="hidden" id="pickedUpDocumentType" name="document_type">
                            
                            <div class="detail-row">
                                <div class="detail-label">
                                    <i class="fas fa-question-circle me-2"></i> Confirmation:
                                </div>
                                <div class="detail-value">
                                    Are you sure you want to mark this request as picked up?
                                </div>
                            </div>
                            
                            <div class="detail-row">
                                <div class="detail-label">
                                    <i class="fas fa-check-square me-2"></i> Verification:
                                </div>
                                <div class="detail-value">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="confirmPickedUp" required>
                                        <label class="form-check-label" for="confirmPickedUp">
                                            I confirm that this request has been picked up.
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i> Confirm
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
<div class="modal fade document-detail-modal" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-animation">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Log Out</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <!-- Warning Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#ffc107" class="mb-4" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                </svg>
                <div class="mb-3 fs-5 text-secondary">Are you sure you want to log out?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="logout.php" class="btn btn-danger">Yes, Log-out</a>
            </div>
        </div>
    </div>
</div>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function filterRequests() {
            var document_type = document.getElementById('document_type').value;
            var status = document.getElementById('statusFilter').value;
            window.location.href = 'view_document_requests.php?document_type=' + document_type + '&status=' + status;
        }

        function openRequestModal(requestId, documentType) {
            $.get(`document_modals/${documentType}_modal.php`, { id: requestId }, function(data) {
                $('#modal-content').html(data);
                $('#requestModal').modal('show');
            });
        }

        document.getElementById('approveForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const requestId = document.getElementById('approveRequestId').value;
            const documentType = document.getElementById('approveDocumentType').value;
            const pickupDate = document.getElementById('pickupDate').value;

            // Clear any previous feedback
            const existingFeedback = document.querySelector('#approveForm .alert');
            if (existingFeedback) {
                existingFeedback.remove();
            }

            if (!pickupDate) {
                const feedbackContainer = document.createElement('div');
                feedbackContainer.className = 'alert alert-warning mt-3';
                feedbackContainer.innerHTML = 'Please select a pickup date and time.';
                document.getElementById('approveForm').appendChild(feedbackContainer);
                return;
            }

            // Double-check that the selected date is not in the past
            const selectedDate = new Date(pickupDate);
            const now = new Date();
            
            if (selectedDate < now) {
                const feedbackContainer = document.createElement('div');
                feedbackContainer.className = 'alert alert-warning mt-3';
                feedbackContainer.innerHTML = 'Please select a future date and time. The selected date is in the past.';
                document.getElementById('approveForm').appendChild(feedbackContainer);
                return;
            }

            $.post('approve_request.php', { id: requestId, document_type: documentType, pickup_date: pickupDate }, function (response) {
                const feedbackContainer = document.createElement('div');
                feedbackContainer.className = response.success ? 'alert alert-success mt-3' : 'alert alert-danger mt-3';
                feedbackContainer.innerHTML = response.success ? 'Request approved successfully!' : `Error approving request: ${response.message}`;
                document.getElementById('approveForm').appendChild(feedbackContainer);

                if (response.success) {
                    setTimeout(() => location.reload(), 2000); // Reload after 2 seconds
                }
            }, 'json');
        });

        function openApproveModal(requestId, documentType) {
            document.getElementById('approveRequestId').value = requestId;
            document.getElementById('approveDocumentType').value = documentType;
            
            // Set minimum date to current date and time
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            const currentDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            document.getElementById('pickupDate').min = currentDateTime;
            
            $('#approveModal').modal('show');
        }

        function openRejectModal(requestId, documentType) {
            document.getElementById('rejectRequestId').value = requestId;
            document.getElementById('rejectDocumentType').value = documentType;
            $('#rejectModal').modal('show');
        }

        document.getElementById('rejectForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const requestId = document.getElementById('rejectRequestId').value;
            const documentType = document.getElementById('rejectDocumentType').value;
            const rejectionReason = document.getElementById('rejectionReason').value;

            if (!rejectionReason) {
                const feedbackContainer = document.createElement('div');
                feedbackContainer.className = 'alert alert-warning mt-3';
                feedbackContainer.innerHTML = 'Please provide a reason for rejection.';
                document.getElementById('rejectForm').appendChild(feedbackContainer);
                return;
            }

            $.post('reject_request.php', { id: requestId, document_type: documentType, rejection_reason: rejectionReason }, function (response) {
                const feedbackContainer = document.createElement('div');
                feedbackContainer.className = response.success ? 'alert alert-success mt-3' : 'alert alert-danger mt-3';
                feedbackContainer.innerHTML = response.success ? 'Request rejected successfully!' : `Error rejecting request: ${response.message}`;
                document.getElementById('rejectForm').appendChild(feedbackContainer);

                if (response.success) {
                    setTimeout(() => location.reload(), 2000); // Reload after 2 seconds
                }
            }, 'json');
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Add click event listener to all rows
            document.querySelectorAll('.clickable-row').forEach(function (row) {
                row.addEventListener('click', function () {
                    // Add visual feedback when row is clicked
                    document.querySelectorAll('.clickable-row').forEach(r => r.classList.remove('active-row'));
                    this.classList.add('active-row');
                    
                    const requestId = this.getAttribute('data-id');
                    const documentType = this.getAttribute('data-document-type');
                    const status = this.getAttribute('data-status');
                    const fullName = this.getAttribute('data-full-name');

                    // Generate the control number with prefix
                    const prefixMap = {
                        certificate_of_indigency: 'CI',
                        certificate_of_residency: 'COR',
                        clearance_major_construction: 'CMC',
                        new_business_permit: 'NBP',
                        repair_and_construction: 'RC',
                        work_permit_utilities: 'WPU',
                        default: 'DOC'
                    };

                    const prefix = prefixMap[documentType] || prefixMap.default;
                    const controlNumber = `${prefix}-${String(requestId).padStart(3, '0')}`;

                    // Set the control number in the modal
                    document.getElementById('modalControlNumber').textContent = controlNumber;

                    // Populate modal fields
                    document.getElementById('modalFullName').textContent = fullName;
                    document.getElementById('modalStatus').textContent = status.charAt(0).toUpperCase() + status.slice(1);

                    // Populate action buttons
                    const modalActions = document.getElementById('modalActions');
                    modalActions.innerHTML = ''; // Clear previous buttons

                    // Hide all action buttons for "picked_up" status
                    if (status === 'picked_up') {
                        $('#actionsModal').modal('show'); // Show the modal without buttons
                        return;
                    }

                    // Add View button
                    const viewButton = document.createElement('button');
                    viewButton.className = 'action-btn btn-view';
                    viewButton.innerHTML = '<i class="fas fa-eye"></i> View';
                    viewButton.onclick = function () {
                        $('#actionsModal').modal('hide'); // Close Actions Modal
                        openRequestModal(requestId, documentType);
                    };
                    modalActions.appendChild(viewButton);

                    // Add Approve button if status is pending
                    if (status === 'pending') {
                        const approveButton = document.createElement('button');
                        approveButton.className = 'action-btn btn-approve';
                        approveButton.innerHTML = '<i class="fas fa-check"></i> Approve';
                        approveButton.onclick = function () {
                            $('#actionsModal').modal('hide'); // Close Actions Modal
                            openApproveModal(requestId, documentType);
                        };
                        modalActions.appendChild(approveButton);

                        // Add Reject button
                        const rejectButton = document.createElement('button');
                        rejectButton.className = 'action-btn btn-reject';
                        rejectButton.innerHTML = '<i class="fas fa-times"></i> Reject';
                        rejectButton.onclick = function () {
                            $('#actionsModal').modal('hide'); // Close Actions Modal
                            openRejectModal(requestId, documentType);
                        };
                        modalActions.appendChild(rejectButton);
                    }

                    // Add Print button if status is approved
                    if (status === 'approved') {
                        const printButton = document.createElement('button');
                        printButton.className = 'btn btn-primary btn-sm';
                        printButton.innerHTML = '<i class="fas fa-print"></i> Print';
                        printButton.onclick = function () {
                            $.post('log_print.php', { id: requestId, document_type: documentType }, function (response) {
                                if (response.success) {
                                    // Open the print view in a new tab
                                    window.open(`generate_document.php?id=${requestId}&type=${documentType}`, '_blank');
                                } else {
                                    // Display feedback as a Bootstrap warning
                                    const warning = document.createElement('div');
                                    warning.className = 'alert alert-warning mt-3';
                                    warning.innerHTML = response.message;
                                    modalActions.appendChild(warning);
                                }
                            }, 'json');
                        };
                        modalActions.appendChild(printButton);
                    }

                    // Add History button
                    const historyButton = document.createElement('button');
                    historyButton.className = 'action-btn btn-history';
                    historyButton.innerHTML = '<i class="fas fa-history"></i> History';
                    historyButton.onclick = function () {
                        $('#actionsModal').modal('hide'); // Close Actions Modal
                        openHistoryModal(requestId, documentType);
                    };
                    modalActions.appendChild(historyButton);

                    // Show the modal
                    $('#actionsModal').modal('show');
                    
                    // Clear active row when modal is closed
                    document.getElementById('actionsModal').addEventListener('hidden.bs.modal', function () {
                        document.querySelectorAll('.clickable-row').forEach(r => r.classList.remove('active-row'));
                    });

                    // Add Edit button if status is not approved or rejected
                    if (status !== 'approved' && status !== 'rejected') {
                        const editButton = document.createElement('button');
                        editButton.className = 'action-btn btn-edit';
                        editButton.innerHTML = '<i class="fas fa-edit"></i> Edit';
                        editButton.onclick = function () {
                            $('#actionsModal').modal('hide'); // Close Actions Modal
                            $('#modal-content').load(`document_modals/edit_${documentType}_modal.php?id=${requestId}`, function () {
                                $('#requestModal').modal('show'); // Open the Edit Modal
                            });
                        };
                        modalActions.appendChild(editButton);
                    }

                    // Add Picked Up button if status is approved
                    if (status === 'approved') {
                        const pickedUpButton = document.createElement('button');
                        pickedUpButton.className = 'action-btn btn-approve';
                        pickedUpButton.innerHTML = '<i class="fas fa-box"></i> Already Picked Up?';
                        pickedUpButton.onclick = function () {
                            $('#actionsModal').modal('hide'); // Close Actions Modal
                            openPickedUpModal(requestId, documentType);
                        };
                        modalActions.appendChild(pickedUpButton);
                    }
                });
            });
        });

        function openHistoryModal(requestId, documentType) {
            // Show the modal first for better user experience
            $('#historyModal').modal('show');
            
            // Update the modal title to include the document type
            document.getElementById('historyModalLabel').textContent = "Request History";
            
            // Show loading indicator
            const historyList = document.getElementById('historyList');
            historyList.innerHTML = `
                <li class="list-group-item text-center border-0 py-5">
                    <div class="spinner-container">
                        <div class="history-pulse">
                            <i class="fas fa-history text-primary mb-3" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <p class="text-muted mt-3">Loading history...</p>
                </li>
            `;
            
            $.get('fetch_history.php', { id: requestId, document_type: documentType }, function (data) {
                historyList.innerHTML = ''; // Clear previous history

                if (data.error) {
                    console.error('Error fetching history:', data.error);
                    historyList.innerHTML = '<li class="list-group-item text-danger"><i class="fas fa-exclamation-circle me-2"></i> Error fetching history: ' + data.error + '</li>';
                    return;
                }

                if (data.length === 0) {
                    historyList.innerHTML = '<li class="list-group-item text-center py-5 border-0">' +
                        '<div class="empty-state-container">' +
                        '<i class="fas fa-history text-secondary mb-3" style="font-size: 3rem; opacity: 0.5;"></i>' +
                        '<h5 class="mb-2">No History Available</h5>' +
                        '<p class="mb-0 text-muted">This request has no recorded actions yet.</p>' +
                        '<p class="text-muted small mt-2">The history will be recorded when actions like approvals, rejections, or edits are performed on this request.</p>' +
                        '</div>' +
                        '</li>';
                    return;
                }

                // Loop through items with some delay for animation effect
                data.forEach((item, index) => {
                    setTimeout(() => {
                        // Format the timestamp to be more readable
                        const date = new Date(item.timestamp);
                        const formattedDate = date.toLocaleDateString('en-US', {
                            day: 'numeric', 
                            month: 'short', 
                            year: 'numeric'
                        });
                        
                        const formattedTime = date.toLocaleTimeString('en-US', {
                            hour: '2-digit', 
                            minute: '2-digit'
                        });
                        
                        // Get action icon and class based on the action type
                        let actionIcon = 'fa-check-circle';
                        let actionClass = 'text-success';
                        let actionBorderClass = 'action-approve';
                        
                        if (item.action.toLowerCase().includes('reject')) {
                            actionIcon = 'fa-times-circle';
                            actionClass = 'text-danger';
                            actionBorderClass = 'action-reject';
                        } else if (item.action.toLowerCase().includes('pick')) {
                            actionIcon = 'fa-box';
                            actionClass = 'text-primary';
                            actionBorderClass = 'action-pickup';
                        } else if (item.action.toLowerCase().includes('edit')) {
                            actionIcon = 'fa-edit';
                            actionClass = 'text-warning';
                            actionBorderClass = 'action-edit';
                        } else if (item.action.toLowerCase().includes('request')) {
                            actionIcon = 'fa-file-alt';
                            actionClass = 'text-info';
                            actionBorderClass = 'action-request';
                        }
                        
                        const listItem = document.createElement('li');
                        listItem.className = `list-group-item ${actionBorderClass} history-item-animation`;
                        listItem.innerHTML = `
                            <div class="d-flex align-items-start">
                                <div class="history-icon">
                                    <i class="fas ${actionIcon} ${actionClass}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold">${item.action}</span>
                                        <span class="badge bg-light text-dark">${formattedDate} at ${formattedTime}</span>
                                    </div>
                                    <p class="mb-0 text-muted">by <strong>${item.admin_name}</strong></p>
                                </div>
                            </div>
                        `;
                        historyList.appendChild(listItem);
                    }, index * 100); // Stagger the appearance of each item by 100ms
                });
            }, 'json').fail(function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
                historyList.innerHTML = '<li class="list-group-item text-center py-5 border-0">' +
                    '<div class="empty-state-container">' +
                    '<i class="fas fa-exclamation-circle text-danger mb-3" style="font-size: 3rem;"></i>' +
                    '<h5 class="mb-2">Unable to Load History</h5>' +
                    '<p class="mb-0">There was a problem retrieving the history data.</p>' +
                    '<div class="mt-3">' +
                    '<button class="btn btn-outline-primary btn-sm" onclick="openHistoryModal(' + requestId + ', \'' + documentType + '\')">' +
                    '<i class="fas fa-sync-alt me-2"></i> Try Again</button>' +
                    '</div>' +
                    '</div>' +
                    '</li>';
            });
        }

        document.getElementById('pickedUpForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const requestId = document.getElementById('pickedUpRequestId').value;
            const documentType = document.getElementById('pickedUpDocumentType').value;

            $.post('mark_picked_up.php', { id: requestId, document_type: documentType }, function (response) {
                const feedbackContainer = document.createElement('div');
                feedbackContainer.className = response.success ? 'alert alert-success mt-3' : 'alert alert-danger mt-3';
                feedbackContainer.innerHTML = response.success ? 'Request marked as picked up!' : `Error: ${response.message}`;
                document.getElementById('pickedUpForm').appendChild(feedbackContainer);

                if (response.success) {
                    setTimeout(() => location.reload(), 2000); // Reload after 2 seconds
                }
            }, 'json');
        });

        function openPickedUpModal(requestId, documentType) {
            document.getElementById('pickedUpRequestId').value = requestId;
            document.getElementById('pickedUpDocumentType').value = documentType;
            $('#pickedUpModal').modal('show');
        }
    </script>

</body>
</html>

<?php
function getDocumentPrefix($documentType) {
    switch ($documentType) {
        case 'certificate_of_indigency':
            return 'CI';
        case 'certificate_of_residency':
            return 'COR';
        case 'clearance_major_construction':
            return 'CMC';
        case 'new_business_permit':
            return 'NBP';
        case 'repair_and_construction':
            return 'RC';
        case 'work_permit_utilities':
            return 'WPU';
        default:
            return 'DOC'; // Default prefix if no match
    }
}
?>