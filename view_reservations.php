<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Include database connection

// Get the selected status for filtering
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Pagination logic
$limit = 8; // Number of reservations per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query logic
$sql = "SELECT reservations.id, reservations.venue_type, reservations.start_time, reservations.end_time, reservations.status, reservations.total_cost, reservations.approved_by, reservations.time_approved, reservations.rejected_by, reservations.rejection_reason, reservations.time_rejected, users.first_name, users.last_name, users.phone_number 
        FROM reservations 
        JOIN users ON reservations.user_id = users.id";
if ($statusFilter !== 'all') {
    $sql .= " WHERE reservations.status = '$statusFilter'";
}
$sql .= " LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

if (!$result) {
    die("Error fetching reservations: " . $conn->error);
}

// Get total number of reservations for pagination
$countSql = "SELECT COUNT(*) AS total FROM reservations";
if ($statusFilter !== 'all') {
    $countSql .= " WHERE status = '$statusFilter'";
}
$countResult = $conn->query($countSql);

if (!$countResult) {
    die("Error fetching total reservations: " . $conn->error);
}

$totalReservations = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalReservations / $limit);
?>

<!DOCTYPE html>
<html lang="en">
    <?php include 'includes/admin_head.php'; ?>
    <link href="view_document_requests.css" rel="stylesheet">
    <link href="admin_dashboard.css" rel="stylesheet">
    <link href="modern_buttons.css" rel="stylesheet">
    <link href="history_modal.css" rel="stylesheet">
    <style>
    /* Hidden print container */
    #printContainer {
        display: none;
    }
    
    @page {
        margin: 0;
        size: auto;
    }
    
    @media print {
        body * {
            visibility: hidden;
        }
        
        #printContainer, #printContainer * {
            display: block !important;
            visibility: visible !important;
        }
        
        #printContainer {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 9999;
            padding: 20px;
            margin: 0;
        }
        
        /* Style for receipt items in print view */
        #printContainer .receipt-item {
            display: flex !important;
            justify-content: space-between !important;
            margin-bottom: 2px !important;
        }
        
        #printContainer .receipt-item-label,
        #printContainer .receipt-item-value {
            display: inline-block !important;
        }
        
        #printContainer .receipt-header {
            margin-bottom: 20px !important;
            text-align: center !important;
        }
        
        #printContainer .receipt-section {
            margin-bottom: 10px !important;
        }
        
        #printContainer .receipt-total {
            font-weight: bold !important;
            margin-top: 5px !important;
            border-top: 1px solid #ddd !important;
            padding-top: 5px !important;
        }
        
        /* Hide modal elements during print */
        .modal, .modal-dialog, .modal-content, .modal-body {
            display: none;
        }
    }
    </style>

<body>
    <!-- Print Container -->
    <div id="printContainer"></div>
    <?php include 'includes/admin_navbar.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="document-container">
                    <div class="header-actions">
                        <h2 class="header-title">View Reservations</h2>
                        <a href="reservation_calendar.php" class="btn btn-primary">
                            <i class="fas fa-calendar"></i> Calendar View
                        </a>
                    </div>

                    <!-- Filters -->
                    <div class="filter-container">
                        <div class="d-flex align-items-center">
                            <label for="statusFilter" class="filter-label me-2">Status:</label>
                            <select id="statusFilter" class="form-select filter-select" onchange="filterReservations()">
                                <option value="all" <?php if ($statusFilter === 'all') echo 'selected'; ?>>All</option>
                                <option value="pending" <?php if ($statusFilter === 'pending') echo 'selected'; ?>>Pending</option>
                                <option value="approved" <?php if ($statusFilter === 'approved') echo 'selected'; ?>>Approved</option>
                                <option value="rejected" <?php if ($statusFilter === 'rejected') echo 'selected'; ?>>Rejected</option>
                            </select>
                        </div>
                    </div>

                    <?php if ($result && $result->num_rows > 0): ?>
                        <div class="table-container">
                            <table class="document-table">
                                <thead>
                                    <tr>
                                        <th>Control No.</th>
                                        <th>Full Name</th>
                                        <th>Contact Number</th>
                                        <th>Total Cost</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="clickable-row" 
                            data-id="<?php echo $row['id']; ?>" 
                            data-full-name="<?php echo $row['first_name'] . ' ' . $row['last_name']; ?>" 
                            data-phone-number="<?php echo $row['phone_number']; ?>" 
                            data-venue-type="<?php echo $row['venue_type']; ?>" 
                            data-total-cost="<?php echo $row['total_cost']; ?>" 
                            data-start-time="<?php echo $row['start_time']; ?>" 
                            data-end-time="<?php echo $row['end_time']; ?>" 
                            data-status="<?php echo $row['status']; ?>" 
                            data-approved-by="<?php echo isset($row['approved_by']) ? $row['approved_by'] : 'N/A'; ?>" 
                            data-time-approved="<?php echo isset($row['time_approved']) ? $row['time_approved'] : 'N/A'; ?>" 
                            data-rejected-by="<?php echo isset($row['rejected_by']) ? $row['rejected_by'] : 'N/A'; ?>" 
                            data-rejection-reason="<?php echo isset($row['rejection_reason']) ? $row['rejection_reason'] : 'N/A'; ?>" 
                            data-time-rejected="<?php echo isset($row['time_rejected']) ? $row['time_rejected'] : 'N/A'; ?>">
                            <td>RSV-<?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                            <td><?php echo $row['phone_number']; ?></td>
                            <td><?php echo number_format($row['total_cost'], 2); ?> Php</td>
                            <td><?php echo date("F j, Y, g:i a", strtotime($row['start_time'])); ?></td>
                            <td><?php echo date("F j, Y, g:i a", strtotime($row['end_time'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo htmlspecialchars($row['status']); ?>">
                                    <?php echo ucfirst($row['status']); ?>
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
                                            <a class="page-link" href="?status=<?php echo $statusFilter; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-alt empty-state-icon"></i>
                            <p class="empty-state-text">No reservations found for the selected status.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation Details Modal -->
    <div class="modal fade" id="reservationDetailsModal" tabindex="-1" role="dialog" aria-labelledby="reservationDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content modal-animation">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservationDetailsModalLabel">Reservation Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-section">
                        <div class="detail-row">
                            <span class="detail-icon">
                                <i class="fas fa-hashtag"></i>
                            </span>
                            <div class="detail-label">Control Number:</div>
                            <div class="detail-value" id="modalControlNo"></div>
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
                                <i class="fas fa-phone"></i>
                            </span>
                            <div class="detail-label">Contact Number:</div>
                            <div class="detail-value" id="modalContactNumber"></div>
                        </div>
                        <div class="detail-row">
                            <span class="detail-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <div class="detail-label">Venue Type:</div>
                            <div class="detail-value" id="modalVenueType"></div>
                        </div>
                        <div class="detail-row">
                            <span class="detail-icon">
                                <i class="fas fa-money-bill-wave text-primary"></i>
                            </span>
                            <div class="detail-label text-primary">Total Cost:</div>
                            <div class="detail-value text-primary fw-bold" id="modalTotalCost"></div>
                        </div>
                        <div class="detail-row">
                            <span class="detail-icon">
                                <i class="fas fa-hourglass-start"></i>
                            </span>
                            <div class="detail-label">Start Time:</div>
                            <div class="detail-value" id="modalStartTime"></div>
                        </div>
                        <div class="detail-row">
                            <span class="detail-icon">
                                <i class="fas fa-hourglass-end"></i>
                            </span>
                            <div class="detail-label">End Time:</div>
                            <div class="detail-value" id="modalEndTime"></div>
                        </div>
                        <div class="detail-row">
                            <span class="detail-icon">
                                <i class="fas fa-info-circle"></i>
                            </span>
                            <div class="detail-label">Status:</div>
                            <div class="detail-value" id="modalStatus"></div>
                        </div>
                        
                        <!-- Approved details -->
                        <div class="detail-row" id="modalApprovedBy" style="display: none;">
                            <span class="detail-icon">
                                <i class="fas fa-user-check text-success"></i>
                            </span>
                            <div class="detail-label">Approved by:</div>
                            <div class="detail-value approved-value"></div>
                        </div>
                        <div class="detail-row" id="modalTimeApproved" style="display: none;">
                            <span class="detail-icon">
                                <i class="fas fa-clock text-success"></i>
                            </span>
                            <div class="detail-label">Time Approved:</div>
                            <div class="detail-value approved-value"></div>
                        </div>

                        <!-- Print receipt button (only shown for approved reservations) -->
                        <div class="detail-row" id="printReceiptRow" style="display: none;">
                            <div class="w-100 d-flex justify-content-center mt-3">
                                <button type="button" class="action-btn btn-print" id="printButton">
                                    <i class="fas fa-print me-2"></i> Print Receipt
                                </button>
                            </div>
                        </div>
                        
                        <!-- Rejected details -->
                        <div class="detail-row" id="modalRejectedBy" style="display: none;">
                            <span class="detail-icon">
                                <i class="fas fa-user-times text-danger"></i>
                            </span>
                            <div class="detail-label">Rejected by:</div>
                            <div class="detail-value rejected-value"></div>
                        </div>
                        <div class="detail-row" id="modalRejectionReason" style="display: none;">
                            <span class="detail-icon">
                                <i class="fas fa-comment-alt text-danger"></i>
                            </span>
                            <div class="detail-label">Rejection Reason:</div>
                            <div class="detail-value rejected-value"></div>
                        </div>
                        <div class="detail-row" id="modalTimeRejected" style="display: none;">
                            <span class="detail-icon">
                                <i class="fas fa-clock text-danger"></i>
                            </span>
                            <div class="detail-label">Time Rejected:</div>
                            <div class="detail-value rejected-value"></div>
                        </div>
                    </div>
                    
                    <div class="actions-section">
                        <h5><i class="fas fa-tools me-2"></i>Actions</h5>
                        <p class="text-muted small">Select an action to perform on this reservation</p>
                        <div id="modalActions" class="d-flex flex-wrap gap-2 mt-3">
                            <button type="button" class="action-btn btn-approve" onclick="openApproveModal()">
                                <i class="fas fa-check-circle me-2"></i> Approve Reservation
                            </button>
                            <button type="button" class="action-btn btn-reject" onclick="openRejectModal()">
                                <i class="fas fa-times-circle me-2"></i> Reject Reservation
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="action-btn btn-close-modal" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-animation">
                <div class="modal-header">
                    <h5 class="modal-title text-success" id="approveModalLabel"><i class="fas fa-check-circle me-2"></i>Approve Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="approveForm">
                        <input type="hidden" id="approveReservationId" name="id">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="approveCheckbox" required>
                            <label class="form-check-label" for="approveCheckbox">
                                I have thoroughly reviewed the details and received the payment.
                            </label>
                        </div>
                        <div id="approveFeedback" class="alert mt-3" style="display: none;"></div>
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="action-btn btn-approve">
                                <i class="fas fa-check-circle me-2"></i> Confirm Approval
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-animation">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="rejectModalLabel"><i class="fas fa-times-circle me-2"></i>Reject Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rejectForm">
                        <input type="hidden" id="rejectReservationId" name="id">
                        <div class="form-group">
                            <label for="rejectionReason">Rejection Reason:</label>
                            <textarea id="rejectionReason" name="rejection_reason" class="form-control" rows="3" required></textarea>
                        </div>
                        <div id="rejectFeedback" class="alert mt-3" style="display: none;"></div>
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="action-btn btn-reject">
                                <i class="fas fa-times-circle me-2"></i> Confirm Rejection
                            </button>
                        </div>
                    </form>
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

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function filterReservations() {
            var status = document.getElementById('statusFilter').value;
            window.location.href = 'view_reservations.php?status=' + status;
        }

        document.querySelectorAll('.clickable-row').forEach(row => {
            row.addEventListener('click', () => {
                const reservationId = row.dataset.id;

                // Ensure reservation ID is set correctly
                if (!reservationId) {
                    alert('Reservation ID is missing.');
                    return;
                }

                document.getElementById('modalControlNo').textContent = `RSV-${reservationId.padStart(3, '0')}`;
                document.getElementById('approveReservationId').value = reservationId;
                document.getElementById('rejectReservationId').value = reservationId;

                document.getElementById('modalFullName').textContent = row.dataset.fullName;
                document.getElementById('modalContactNumber').textContent = row.dataset.phoneNumber;
                document.getElementById('modalVenueType').textContent = row.dataset.venueType;
                document.getElementById('modalTotalCost').textContent = `${row.dataset.totalCost} Php`;
                document.getElementById('modalStartTime').textContent = new Date(row.dataset.startTime).toLocaleString();
                document.getElementById('modalEndTime').textContent = new Date(row.dataset.endTime).toLocaleString();

                const status = row.dataset.status.charAt(0).toUpperCase() + row.dataset.status.slice(1);
                const modalStatus = document.getElementById('modalStatus');
                modalStatus.textContent = status;

                // Apply color coding to the status
                if (status === 'Pending') {
                    modalStatus.style.color = 'orange';
                } else if (status === 'Approved') {
                    modalStatus.style.color = 'green';
                } else if (status === 'Rejected') {
                    modalStatus.style.color = 'red';
                }

                // Show or hide actions section and detail rows based on status
                const modalActions = document.getElementById('modalActions');
                const actionsSection = document.querySelector('.actions-section');

                if (status === 'Pending') {
                    actionsSection.style.display = 'block';

                    document.getElementById('modalApprovedBy').style.display = 'none';
                    document.getElementById('modalTimeApproved').style.display = 'none';
                    document.getElementById('printReceiptRow').style.display = 'none';
                    document.getElementById('modalRejectedBy').style.display = 'none';
                    document.getElementById('modalRejectionReason').style.display = 'none';
                    document.getElementById('modalTimeRejected').style.display = 'none';
                } else if (status === 'Approved') {
                    actionsSection.style.display = 'none';

                    // Update content before displaying
                    document.querySelector('#modalApprovedBy .approved-value').textContent = row.dataset.approvedBy || 'N/A';
                    document.querySelector('#modalTimeApproved .approved-value').textContent = row.dataset.timeApproved ? new Date(row.dataset.timeApproved).toLocaleString() : 'N/A';

                    document.getElementById('modalApprovedBy').style.display = 'flex';
                    document.getElementById('modalTimeApproved').style.display = 'flex';
                    document.getElementById('printReceiptRow').style.display = 'flex';

                    document.getElementById('modalRejectedBy').style.display = 'none';
                    document.getElementById('modalRejectionReason').style.display = 'none';
                    document.getElementById('modalTimeRejected').style.display = 'none';
                } else if (status === 'Rejected') {
                    actionsSection.style.display = 'none';

                    // Update content before displaying
                    document.querySelector('#modalRejectedBy .rejected-value').textContent = row.dataset.rejectedBy || 'N/A';
                    document.querySelector('#modalRejectionReason .rejected-value').textContent = row.dataset.rejectionReason || 'N/A';
                    document.querySelector('#modalTimeRejected .rejected-value').textContent = row.dataset.timeRejected ? new Date(row.dataset.timeRejected).toLocaleString() : 'N/A';

                    document.getElementById('modalRejectedBy').style.display = 'flex';
                    document.getElementById('modalRejectionReason').style.display = 'flex';
                    document.getElementById('modalTimeRejected').style.display = 'flex';
                    document.getElementById('printReceiptRow').style.display = 'none';

                    document.getElementById('modalApprovedBy').style.display = 'none';
                    document.getElementById('modalTimeApproved').style.display = 'none';
                }

                $('#reservationDetailsModal').modal('show');
            });
        });

        function openApproveModal() {
            const reservationId = document.getElementById('approveReservationId').value;

            // Ensure reservation ID is set before opening the modal
            if (!reservationId) {
                alert('Reservation ID is missing.');
                return;
            }

            $('#reservationDetailsModal').modal('hide'); // Hide Details Modal
            $('#approveModal').modal('show');
        }

        function openRejectModal() {
            const reservationId = document.getElementById('rejectReservationId').value;

            // Ensure reservation ID is set before opening the modal
            if (!reservationId) {
                alert('Reservation ID is missing.');
                return;
            }

            $('#reservationDetailsModal').modal('hide'); // Hide Details Modal
            $('#rejectModal').modal('show');
        }

        document.getElementById('approveForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const reservationId = document.getElementById('approveReservationId').value;
            const approveCheckbox = document.getElementById('approveCheckbox').checked;
            const approveFeedback = document.getElementById('approveFeedback');

            if (!reservationId) {
                approveFeedback.textContent = 'Reservation ID is required.';
                approveFeedback.className = 'alert alert-danger';
                approveFeedback.style.display = 'block';
                return;
            }

            if (!approveCheckbox) {
                approveFeedback.textContent = 'Please confirm that you have reviewed the details and received the payment.';
                approveFeedback.className = 'alert alert-danger';
                approveFeedback.style.display = 'block';
                return;
            }

            $.post('approve_reservation.php', { id: reservationId }, function (response) {
                if (response.success) {
                    approveFeedback.textContent = response.message;
                    approveFeedback.className = 'alert alert-success';
                    approveFeedback.style.display = 'block';

                    setTimeout(() => {
                        location.reload(); // Reload the page after 2 seconds
                    }, 2000);
                } else {
                    approveFeedback.textContent = response.message;
                    approveFeedback.className = 'alert alert-danger';
                    approveFeedback.style.display = 'block';
                }
            }, 'json').fail(function () {
                approveFeedback.textContent = 'Failed to approve reservation. Please try again.';
                approveFeedback.className = 'alert alert-danger';
                approveFeedback.style.display = 'block';
            });
        });

        document.getElementById('rejectForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const reservationId = document.getElementById('rejectReservationId').value;
            const rejectionReason = document.getElementById('rejectionReason').value;
            const rejectFeedback = document.getElementById('rejectFeedback');

            if (!reservationId) {
                rejectFeedback.textContent = 'Reservation ID is required.';
                rejectFeedback.className = 'alert alert-danger';
                rejectFeedback.style.display = 'block';
                return;
            }

            if (!rejectionReason) {
                rejectFeedback.textContent = 'Please provide a reason for rejection.';
                rejectFeedback.className = 'alert alert-danger';
                rejectFeedback.style.display = 'block';
                return;
            }

            $.post('reject_reservation.php', { id: reservationId, rejection_reason: rejectionReason }, function (response) {
                if (response.success) {
                    rejectFeedback.textContent = response.message;
                    rejectFeedback.className = 'alert alert-success';
                    rejectFeedback.style.display = 'block';

                    setTimeout(() => {
                        location.reload(); // Reload the page after 2 seconds
                    }, 2000);
                } else {
                    rejectFeedback.textContent = response.message;
                    rejectFeedback.className = 'alert alert-danger';
                    rejectFeedback.style.display = 'block';
                }
            }, 'json').fail(function () {
                rejectFeedback.textContent = 'Failed to reject reservation. Please try again.';
                rejectFeedback.className = 'alert alert-danger';
                rejectFeedback.style.display = 'block';
            });
        });
        
        // Print receipt functionality
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('printButton').addEventListener('click', function() {
                generateReceiptForPrinting();
                window.print();
            });
        });
        
        function generateReceiptForPrinting() {
            const printContainer = document.getElementById('printContainer');
            printContainer.innerHTML = ''; // Clear previous content
            
            const receiptContent = document.createElement('div');
            receiptContent.classList.add('receipt-content');
            
            // Add header
            const header = document.createElement('div');
            header.classList.add('receipt-header');
            header.innerHTML = `
                <h3>Barangay Reservation Receipt</h3>
                <h4>Sports Venue Reservation</h4>
                <p>Date: ${new Date().toLocaleDateString()}</p>
            `;
            receiptContent.appendChild(header);
            
            // Create reservation details section
            const detailsSection = document.createElement('div');
            detailsSection.classList.add('receipt-section');
            detailsSection.innerHTML = '<h5>Reservation Details</h5>';
            
            // Add details items
            const controlNo = document.getElementById('modalControlNo').textContent;
            addReceiptItem(detailsSection, 'Control Number:', controlNo);
            
            const fullName = document.getElementById('modalFullName').textContent;
            addReceiptItem(detailsSection, 'Full Name:', fullName);
            
            const contactNumber = document.getElementById('modalContactNumber').textContent;
            addReceiptItem(detailsSection, 'Contact Number:', contactNumber);
            
            const venueType = document.getElementById('modalVenueType').textContent;
            addReceiptItem(detailsSection, 'Venue Type:', venueType);
            
            const startTime = document.getElementById('modalStartTime').textContent;
            addReceiptItem(detailsSection, 'Start Time:', startTime);
            
            const endTime = document.getElementById('modalEndTime').textContent;
            addReceiptItem(detailsSection, 'End Time:', endTime);
            
            // Add approval details
            const approvedBy = document.querySelector('#modalApprovedBy .approved-value').textContent;
            addReceiptItem(detailsSection, 'Approved By:', approvedBy);
            
            const timeApproved = document.querySelector('#modalTimeApproved .approved-value').textContent;
            addReceiptItem(detailsSection, 'Time Approved:', timeApproved);
            
            // Add cost details
            const totalCost = document.getElementById('modalTotalCost').textContent;
            addReceiptItem(detailsSection, 'Total Cost:', totalCost, true);
            
            // Add official section
            const officialSection = document.createElement('div');
            officialSection.classList.add('receipt-section', 'mt-5');
            officialSection.innerHTML = `
                <div style="margin-top: 50px; text-align: center;">
                    <div style="border-top: 1px solid #000; display: inline-block; width: 200px; margin-bottom: 5px;"></div>
                    <p>Authorized Signature</p>
                </div>
            `;
            
            // Append all sections to receipt content
            receiptContent.appendChild(detailsSection);
            receiptContent.appendChild(officialSection);
            
            // Add footer
            const footer = document.createElement('div');
            footer.classList.add('receipt-footer', 'mt-5');
            footer.innerHTML = `
                <p style="text-align: center; font-size: 12px; margin-top: 20px;">
                    This is an official receipt for sports venue reservation.<br>
                    Thank you for your reservation!
                </p>
            `;
            receiptContent.appendChild(footer);
            
            // Add the content to the print container
            printContainer.appendChild(receiptContent);
        }
        
        function addReceiptItem(container, label, value, isBold = false) {
            const item = document.createElement('div');
            item.classList.add('receipt-item');
            
            const labelSpan = document.createElement('span');
            labelSpan.classList.add('receipt-item-label');
            labelSpan.textContent = label;
            
            const valueSpan = document.createElement('span');
            valueSpan.classList.add('receipt-item-value');
            valueSpan.textContent = value;
            
            if (isBold) {
                labelSpan.style.fontWeight = 'bold';
                valueSpan.style.fontWeight = 'bold';
                item.classList.add('receipt-total');
            }
            
            item.appendChild(labelSpan);
            item.appendChild(valueSpan);
            container.appendChild(item);
        }
    </script>
</body>
</html>