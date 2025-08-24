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
    <link href= "admin_dashboard.css" rel="stylesheet">

<body>
    <?php include 'includes/admin_navbar.php'; ?>

    <div class="container mt-5">
        <h1>View Reservations</h1>

        <!-- Filters side by side -->
        <div class="d-flex gap-3 mb-3">
            <div class="form-group">
                <label for="statusFilter">Filter by Status:</label>
                <select id="statusFilter" class="form-control" onchange="filterReservations()">
                    <option value="all" <?php if ($statusFilter === 'all') echo 'selected'; ?>>All</option>
                    <option value="pending" <?php if ($statusFilter === 'pending') echo 'selected'; ?>>Pending</option>
                    <option value="approved" <?php if ($statusFilter === 'approved') echo 'selected'; ?>>Approved</option>
                    <option value="rejected" <?php if ($statusFilter === 'rejected') echo 'selected'; ?>>Rejected</option>
                </select>
            </div>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <table class="table table-bordered table-striped mt-3">
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
                            <td class="status-<?php echo htmlspecialchars($row['status']); ?>">
                                <?php 
                                    if ($row['status'] === 'approved') {
                                        echo '<span class="status-approved" style="color: green;">Approved</span>';
                                    } elseif ($row['status'] === 'rejected') {
                                        echo '<span class="status-rejected" style="color: red;">Rejected</span>';
                                    } elseif ($row['status'] === 'pending') {
                                        echo '<span class="status-pending" style="color: orange;">Pending</span>';
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Pagination Controls -->
            <nav>
                <ul class="pagination justify-content-center mt-4">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?status=<?php echo $statusFilter; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php else: ?>
            <p>No reservations found for the selected status.</p>
        <?php endif; ?>
    </div>

    <!-- Reservation Details Modal -->
    <div class="modal fade" id="reservationDetailsModal" tabindex="-1" role="dialog" aria-labelledby="reservationDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservationDetailsModalLabel">Reservation Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><i class="fa fa-hashtag"></i> <strong>Control No.:</strong> <span id="modalControlNo"></span></p>
                    <p><i class="fa fa-user"></i> <strong>Full Name:</strong> <span id="modalFullName"></span></p>
                    <p><i class="fa fa-phone"></i> <strong>Contact Number:</strong> <span id="modalContactNumber"></span></p>
                    <p><i class="fa fa-map-marker"></i> <strong>Venue Type:</strong> <span id="modalVenueType"></span></p>
                    <p><i class="fa fa-money" style="color: darkblue;"></i> <strong style="color: darkblue;">Total Cost:</strong> <span id="modalTotalCost" style="color: darkblue;"></span></p>
                    <p><i class="fa fa-clock"></i> <strong>Start Time:</strong> <span id="modalStartTime"></span></p>
                    <p><i class="fa fa-clock"></i> <strong>End Time:</strong> <span id="modalEndTime"></span></p>
                    <p><i class="fa fa-info-circle"></i> <strong>Status:</strong> <span id="modalStatus"></span></p>
                    <p id="modalApprovedBy" style="display: none;"><i class="fa fa-user-check"></i> <strong>Approved by:</strong> <span></span></p>
                    <p id="modalTimeApproved" style="display: none;"><i class="fa fa-clock"></i> <strong>Time Approved:</strong> <span></span></p>
                    <p id="modalRejectedBy" style="display: none;"><i class="fa fa-user-times"></i> <strong>Rejected by:</strong> <span></span></p>
                    <p id="modalRejectionReason" style="display: none;"><i class="fa fa-comment"></i> <strong>Rejection Reason:</strong> <span></span></p>
                    <p id="modalTimeRejected" style="display: none;"><i class="fa fa-clock"></i> <strong>Time Rejected:</strong> <span></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="openApproveModal()">Approve</button>
                    <button type="button" class="btn btn-danger" onclick="openRejectModal()">Reject</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="approveModalLabel">Approve Reservation</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <button type="submit" class="btn btn-success btn-block mt-3">Confirm Approval</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Reservation</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rejectForm">
                        <input type="hidden" id="rejectReservationId" name="id">
                        <div class="form-group">
                            <label for="rejectionReason">Rejection Reason:</label>
                            <textarea id="rejectionReason" name="rejection_reason" class="form-control" rows="3" required></textarea>
                        </div>
                        <div id="rejectFeedback" class="alert mt-3" style="display: none;"></div>
                        <button type="submit" class="btn btn-danger btn-block mt-3">Confirm Rejection</button>
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

                // Show or hide buttons and labels based on status
                const approveButton = document.querySelector('.btn-success');
                const rejectButton = document.querySelector('.btn-danger');

                if (status === 'Pending') {
                    approveButton.style.display = 'inline-block';
                    rejectButton.style.display = 'inline-block';

                    document.getElementById('modalApprovedBy').style.display = 'none';
                    document.getElementById('modalTimeApproved').style.display = 'none';
                    document.getElementById('modalRejectedBy').style.display = 'none';
                    document.getElementById('modalRejectionReason').style.display = 'none';
                    document.getElementById('modalTimeRejected').style.display = 'none';
                } else if (status === 'Approved') {
                    approveButton.style.display = 'none';
                    rejectButton.style.display = 'none';

                    // Update content before displaying
                    document.getElementById('modalApprovedBy').innerHTML = `<i class="fa fa-user-check"></i> <strong>Approved by:</strong> ${row.dataset.approvedBy || 'N/A'}`;
                    document.getElementById('modalTimeApproved').innerHTML = `<i class="fa fa-clock"></i> <strong>Time Approved:</strong> ${row.dataset.timeApproved ? new Date(row.dataset.timeApproved).toLocaleString() : 'N/A'}`;

                    document.getElementById('modalApprovedBy').style.display = 'block';
                    document.getElementById('modalTimeApproved').style.display = 'block';

                    document.getElementById('modalRejectedBy').style.display = 'none';
                    document.getElementById('modalRejectionReason').style.display = 'none';
                    document.getElementById('modalTimeRejected').style.display = 'none';
                } else if (status === 'Rejected') {
                    approveButton.style.display = 'none';
                    rejectButton.style.display = 'none';

                    // Update content before displaying
                    document.getElementById('modalRejectedBy').innerHTML = `<i class="fa fa-user-times"></i> <strong>Rejected by:</strong> ${row.dataset.rejectedBy || 'N/A'}`;
                    document.getElementById('modalRejectionReason').innerHTML = `<i class="fa fa-comment"></i> <strong>Rejection Reason:</strong> ${row.dataset.rejectionReason || 'N/A'}`;
                    document.getElementById('modalTimeRejected').innerHTML = `<i class="fa fa-clock"></i> <strong>Time Rejected:</strong> ${row.dataset.timeRejected ? new Date(row.dataset.timeRejected).toLocaleString() : 'N/A'}`;

                    document.getElementById('modalRejectedBy').style.display = 'block';
                    document.getElementById('modalRejectionReason').style.display = 'block';
                    document.getElementById('modalTimeRejected').style.display = 'block';

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
    </script>
</body>
</html>