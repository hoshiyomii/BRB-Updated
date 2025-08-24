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
$sql = "SELECT facilities_reservations.id, facilities_reservations.facility_type, facilities_reservations.start_time, facilities_reservations.end_time, facilities_reservations.status, facilities_reservations.total_cost, facilities_reservations.approved_by, facilities_reservations.time_approved, facilities_reservations.rejected_by, facilities_reservations.rejection_reason, facilities_reservations.time_rejected, facilities_reservations.with_aircon, facilities_reservations.rooftop_option, facilities_reservations.sound_system, facilities_reservations.projector, facilities_reservations.group_over_50, facilities_reservations.lifetime_table, facilities_reservations.lifetime_chair, facilities_reservations.long_table, facilities_reservations.monoblock_chair, users.first_name, users.last_name, users.phone_number 
        FROM facilities_reservations 
        JOIN users ON facilities_reservations.user_id = users.id";
if ($statusFilter !== 'all') {
    $sql .= " WHERE facilities_reservations.status = '$statusFilter'";
}
$sql .= " LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

if (!$result) {
    die("Error fetching reservations: " . $conn->error);
}

// Get total number of reservations for pagination
$countSql = "SELECT COUNT(*) AS total FROM facilities_reservations";
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
        <h1>View Facilities Reservations</h1>

        <!-- Filters -->
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
                        <th>Facility Type</th>
                        <th>Start and End Time</th>
                        <th>Status</th>
<<<<<<< HEAD
                        <th>Action</th>
=======
>>>>>>> upstream/master
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="clickable-row" 
                            data-id="<?php echo $row['id']; ?>" 
                            data-full-name="<?php echo $row['first_name'] . ' ' . $row['last_name']; ?>" 
                            data-phone-number="<?php echo $row['phone_number']; ?>" 
                            data-facility-type="<?php echo $row['facility_type']; ?>" 
                            data-total-cost="<?php echo $row['total_cost']; ?>" 
                            data-start-time="<?php echo $row['start_time']; ?>" 
                            data-end-time="<?php echo $row['end_time']; ?>" 
                            data-status="<?php echo $row['status']; ?>" 
                            data-approved-by="<?php echo isset($row['approved_by']) ? $row['approved_by'] : 'N/A'; ?>" 
                            data-time-approved="<?php echo isset($row['time_approved']) ? $row['time_approved'] : 'N/A'; ?>" 
                            data-rejected-by="<?php echo isset($row['rejected_by']) ? $row['rejected_by'] : 'N/A'; ?>" 
                            data-rejection-reason="<?php echo isset($row['rejection_reason']) ? $row['rejection_reason'] : 'N/A'; ?>" 
                            data-time-rejected="<?php echo isset($row['time_rejected']) ? $row['time_rejected'] : 'N/A'; ?>" 
                            data-with-aircon="<?php echo $row['with_aircon']; ?>" 
                            data-rooftop-option="<?php echo $row['rooftop_option']; ?>" 
                            data-sound-system="<?php echo $row['sound_system']; ?>" 
                            data-projector="<?php echo $row['projector']; ?>" 
                            data-group-over-50="<?php echo $row['group_over_50']; ?>" 
                            data-lifetime-table="<?php echo $row['lifetime_table']; ?>" 
                            data-lifetime-chair="<?php echo $row['lifetime_chair']; ?>" 
                            data-long-table="<?php echo $row['long_table']; ?>" 
                            data-monoblock-chair="<?php echo $row['monoblock_chair']; ?>">
                            <td>FAC-<?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                            <td><?php echo $row['phone_number']; ?></td>
                            <td><?php echo number_format($row['total_cost'], 2); ?> Php</td>
                            <td><?php echo $row['facility_type']; ?></td>
                            <td><?php echo date("F j, Y, g:i a", strtotime($row['start_time'])); ?> - <?php echo date("F j, Y, g:i a", strtotime($row['end_time'])); ?></td>
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
<<<<<<< HEAD
                            <td>
                                <button class="btn btn-primary btn-sm edit-reservation-btn" data-toggle="modal" data-target="#editReservationModal" data-id="<?php echo $row['id']; ?>">Edit</button>
                            </td>
=======
>>>>>>> upstream/master
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
<<<<<<< HEAD
            <!-- Edit Reservation Modal -->
            <div class="modal fade" id="editReservationModal" tabindex="-1" role="dialog" aria-labelledby="editReservationModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="editReservationForm" method="POST" action="update_reservation.php">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editReservationModalLabel">Edit Reservation</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" id="editReservationId">
                                <div class="form-group">
                                    <label for="editFacilityType">Facility Type</label>
                                    <input type="text" class="form-control" name="facility_type" id="editFacilityType" required>
                                </div>
                                <div class="form-group">
                                    <label for="editStartTime">Start Time</label>
                                    <input type="datetime-local" class="form-control" name="start_time" id="editStartTime" required>
                                </div>
                                <div class="form-group">
                                    <label for="editEndTime">End Time</label>
                                    <input type="datetime-local" class="form-control" name="end_time" id="editEndTime" required>
                                </div>
                                <div class="form-group">
                                    <label for="editTotalCost">Total Cost</label>
                                    <input type="number" step="0.01" class="form-control" name="total_cost" id="editTotalCost" required>
                                </div>
                                <!-- Add more fields as needed -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
=======
>>>>>>> upstream/master

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
                    <p><i class="fa fa-map-marker"></i> <strong>Facility Type:</strong> <span id="modalFacilityType"></span></p>
                    <p><i class="fa fa-money" style="color: darkblue;"></i> <strong style="color: darkblue;">Total Cost:</strong> <span id="modalTotalCost" style="color: darkblue;"></span></p>
                    <p><i class="fa fa-clock"></i> <strong>Start Time:</strong> <span id="modalStartTime"></span></p>
                    <p><i class="fa fa-clock"></i> <strong>End Time:</strong> <span id="modalEndTime"></span></p>
                    <p><i class="fa fa-plus"></i> <strong>Extras:</strong> <span id="modalExtras"></span></p>
                    <p id="modalLifetimeTable" style="display: none;"><i class="fa fa-table"></i> <strong>Lifetime Table:</strong> <span></span></p>
                    <p id="modalLifetimeChair" style="display: none;"><i class="fa fa-chair"></i> <strong>Lifetime Chair:</strong> <span></span></p>
                    <p id="modalLongTable" style="display: none;"><i class="fa fa-table"></i> <strong>Long Table:</strong> <span></span></p>
                    <p id="modalMonoblockChair" style="display: none;"><i class="fa fa-chair"></i> <strong>Monoblock Chair:</strong> <span></span></p>
                    <p><i class="fa fa-info-circle"></i> <strong>Status:</strong> <span id="modalStatus"></span></p>
                    <p id="modalApprovedBy" style="display: none;"><i class="fa fa-user-check"></i> <strong>Approved by:</strong> <span></span></p>
                    <p id="modalTimeApproved" style="display: none;"><i class="fa fa-clock"></i> <strong>Time Approved:</strong> <span></span></p>
                    <p id="modalRejectedBy" style="display: none;"><i class="fa fa-user-times"></i> <strong>Rejected by:</strong> <span></span></p>
                    <p id="modalRejectionReason" style="display: none;"><i class="fa fa-comment"></i> <strong>Rejection Reason:</strong> <span></span></p>
                    <p id="modalTimeRejected" style="display: none;"><i class="fa fa-clock"></i> <strong>Time Rejected:</strong> <span></span></p>
                </div>
                <div class="modal-footer" id="modalFooter">
<<<<<<< HEAD
                    <button type="button" class="btn btn-primary" id="editDetailsBtn">Edit</button>
                    <button type="button" class="btn btn-success" onclick="openApproveModal()">Approve</button>
                    <button type="button" class="btn btn-danger" onclick="openRejectModal()">Reject</button>
                </div>

                <!-- Editable form, hidden by default -->
                <form id="editDetailsForm" style="display:none;">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editModalId">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control" id="editModalFullName" name="full_name" readonly>
                        </div>
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" class="form-control" id="editModalContactNumber" name="contact_number">
                        </div>
                        <div class="form-group">
                            <label>Facility Type</label>
                            <input type="text" class="form-control" id="editModalFacilityType" name="facility_type">
                        </div>
                        <div class="form-group">
                            <label>Total Cost</label>
                            <input type="number" class="form-control" id="editModalTotalCost" name="total_cost" step="0.01">
                        </div>
                        <div class="form-group">
                            <label>Start Time</label>
                            <input type="datetime-local" class="form-control" id="editModalStartTime" name="start_time">
                        </div>
                        <div class="form-group">
                            <label>End Time</label>
                            <input type="datetime-local" class="form-control" id="editModalEndTime" name="end_time">
                        </div>
                        <div class="form-group">
                            <label>Extras (comma separated)</label>
                            <input type="text" class="form-control" id="editModalExtras" name="extras">
                        </div>
                        <div class="form-group">
                            <label>Lifetime Table</label>
                            <input type="number" class="form-control" id="editModalLifetimeTable" name="lifetime_table" min="0">
                        </div>
                        <div class="form-group">
                            <label>Lifetime Chair</label>
                            <input type="number" class="form-control" id="editModalLifetimeChair" name="lifetime_chair" min="0">
                        </div>
                        <div class="form-group">
                            <label>Long Table</label>
                            <input type="number" class="form-control" id="editModalLongTable" name="long_table" min="0">
                        </div>
                        <div class="form-group">
                            <label>Monoblock Chair</label>
                            <input type="number" class="form-control" id="editModalMonoblockChair" name="monoblock_chair" min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="cancelEditBtn">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
</script>
<script>
// Show edit form in modal
document.addEventListener('DOMContentLoaded', function() {
    var editBtn = document.getElementById('editDetailsBtn');
    var editForm = document.getElementById('editDetailsForm');
    var modalBody = document.querySelector('#reservationDetailsModal .modal-body');
    var modalFooter = document.getElementById('modalFooter');
    var cancelEditBtn = document.getElementById('cancelEditBtn');

    if(editBtn) {
        editBtn.addEventListener('click', function() {
            // Hide details, show form
            modalBody.style.display = 'none';
            editForm.style.display = 'block';
            modalFooter.style.display = 'none';
            // Fill form fields with current values
            document.getElementById('editModalId').value = document.getElementById('modalControlNo').textContent.replace('FAC-','');
            document.getElementById('editModalFullName').value = document.getElementById('modalFullName').textContent;
            document.getElementById('editModalContactNumber').value = document.getElementById('modalContactNumber').textContent;
            document.getElementById('editModalFacilityType').value = document.getElementById('modalFacilityType').textContent;
            document.getElementById('editModalTotalCost').value = document.getElementById('modalTotalCost').textContent.replace(' Php','');
            // Convert to datetime-local format
            function toDatetimeLocal(dt) {
                if(!dt) return '';
                var d = new Date(dt);
                d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
                return d.toISOString().slice(0,16);
            }
            document.getElementById('editModalStartTime').value = toDatetimeLocal(document.getElementById('modalStartTime').textContent);
            document.getElementById('editModalEndTime').value = toDatetimeLocal(document.getElementById('modalEndTime').textContent);
            document.getElementById('editModalExtras').value = document.getElementById('modalExtras').textContent;
            document.getElementById('editModalLifetimeTable').value = document.querySelector('#modalLifetimeTable span').textContent || 0;
            document.getElementById('editModalLifetimeChair').value = document.querySelector('#modalLifetimeChair span').textContent || 0;
            document.getElementById('editModalLongTable').value = document.querySelector('#modalLongTable span').textContent || 0;
            document.getElementById('editModalMonoblockChair').value = document.querySelector('#modalMonoblockChair span').textContent || 0;
        });
    }
    if(cancelEditBtn) {
        cancelEditBtn.addEventListener('click', function(e) {
            e.preventDefault();
            editForm.style.display = 'none';
            modalBody.style.display = 'block';
            modalFooter.style.display = 'flex';
        });
    }
    // On form submit, send AJAX to update_reservation.php
    if(editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(editForm);
            fetch('update_reservation.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Reservation updated successfully!');
                    location.reload();
                } else {
                    alert('Update failed: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(() => alert('Update failed.'));
        });
    }
});
</script>
=======
                    <button type="button" class="btn btn-success" onclick="openApproveModal()">Approve</button>
                    <button type="button" class="btn btn-danger" onclick="openRejectModal()">Reject</button>
                </div>
>>>>>>> upstream/master
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

    <script>
        function filterReservations() {
            const status = document.getElementById('statusFilter').value;
            window.location.href = `view_facilities_reservations.php?status=${status}`;
        }

        document.querySelectorAll('.clickable-row').forEach(row => {
            row.addEventListener('click', () => {
                const reservationId = row.dataset.id;

                // Ensure reservation ID is set correctly
                if (!reservationId) {
                    alert('Reservation ID is missing.');
                    return;
                }

                // Populate hidden input fields for Approve and Reject modals
                document.getElementById('approveReservationId').value = reservationId;
                document.getElementById('rejectReservationId').value = reservationId;

                // Populate modal fields
                document.getElementById('modalControlNo').textContent = `FAC-${reservationId.padStart(3, '0')}`;
                document.getElementById('modalFullName').textContent = row.dataset.fullName;
                document.getElementById('modalContactNumber').textContent = row.dataset.phoneNumber;

                // Facility Type with additional options
                let facilityType = row.dataset.facilityType;
                if (row.dataset.withAircon === '1') facilityType += ' (With Aircon)';
                if (row.dataset.rooftopOption === '1') facilityType += ' (With Rooftop)';
                document.getElementById('modalFacilityType').textContent = facilityType;

                // Total Cost
                document.getElementById('modalTotalCost').textContent = `${parseFloat(row.dataset.totalCost).toFixed(2)} Php`;

                document.getElementById('modalStartTime').textContent = new Date(row.dataset.startTime).toLocaleString();
                document.getElementById('modalEndTime').textContent = new Date(row.dataset.endTime).toLocaleString();

                // Extras
                const extras = [];
                if (row.dataset.soundSystem === '1') extras.push('Sound System');
                if (row.dataset.projector === '1') extras.push('Projector');
                if (row.dataset.groupOver50 === '1') extras.push('More than 50 Pax');
                document.getElementById('modalExtras').textContent = extras.length > 0 ? extras.join(', ') : 'None';

                // Display additional items only if their value is not 0
                const lifetimeTable = document.getElementById('modalLifetimeTable');
                if (parseInt(row.dataset.lifetimeTable) > 0) {
                    lifetimeTable.style.display = 'block';
                    lifetimeTable.querySelector('span').textContent = row.dataset.lifetimeTable;
                } else {
                    lifetimeTable.style.display = 'none';
                }

                const lifetimeChair = document.getElementById('modalLifetimeChair');
                if (parseInt(row.dataset.lifetimeChair) > 0) {
                    lifetimeChair.style.display = 'block';
                    lifetimeChair.querySelector('span').textContent = row.dataset.lifetimeChair;
                } else {
                    lifetimeChair.style.display = 'none';
                }

                const longTable = document.getElementById('modalLongTable');
                if (parseInt(row.dataset.longTable) > 0) {
                    longTable.style.display = 'block';
                    longTable.querySelector('span').textContent = row.dataset.longTable;
                } else {
                    longTable.style.display = 'none';
                }

                const monoblockChair = document.getElementById('modalMonoblockChair');
                if (parseInt(row.dataset.monoblockChair) > 0) {
                    monoblockChair.style.display = 'block';
                    monoblockChair.querySelector('span').textContent = row.dataset.monoblockChair;
                } else {
                    monoblockChair.style.display = 'none';
                }

                // Status
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
                const modalFooter = document.getElementById('modalFooter');
                if (status === 'Pending') {
                    modalFooter.style.display = 'flex';
                } else {
                    modalFooter.style.display = 'none';
                }

                const approvedBy = document.getElementById('modalApprovedBy');
                const timeApproved = document.getElementById('modalTimeApproved');
                const rejectedBy = document.getElementById('modalRejectedBy');
                const rejectionReason = document.getElementById('modalRejectionReason');
                const timeRejected = document.getElementById('modalTimeRejected');

                if (status === 'Approved') {
                    approvedBy.style.display = 'block';
                    approvedBy.querySelector('span').textContent = row.dataset.approvedBy || 'N/A';

                    timeApproved.style.display = 'block';
                    timeApproved.querySelector('span').textContent = row.dataset.timeApproved
                        ? new Date(row.dataset.timeApproved).toLocaleString()
                        : 'N/A';

                    rejectedBy.style.display = 'none';
                    rejectionReason.style.display = 'none';
                    timeRejected.style.display = 'none';
                } else if (status === 'Rejected') {
                    rejectedBy.style.display = 'block';
                    rejectedBy.querySelector('span').textContent = row.dataset.rejectedBy || 'N/A';

                    rejectionReason.style.display = 'block';
                    rejectionReason.querySelector('span').textContent = row.dataset.rejectionReason || 'N/A';

                    timeRejected.style.display = 'block';
                    timeRejected.querySelector('span').textContent = row.dataset.timeRejected
                        ? new Date(row.dataset.timeRejected).toLocaleString()
                        : 'N/A';

                    approvedBy.style.display = 'none';
                    timeApproved.style.display = 'none';
                } else {
                    approvedBy.style.display = 'none';
                    timeApproved.style.display = 'none';
                    rejectedBy.style.display = 'none';
                    rejectionReason.style.display = 'none';
                    timeRejected.style.display = 'none';
                }

                // Show the modal using Bootstrap's native API
                const reservationDetailsModal = new bootstrap.Modal(document.getElementById('reservationDetailsModal'));
                reservationDetailsModal.show();
            });
        });

        function openApproveModal() {
            const reservationId = document.getElementById('approveReservationId').value;

            // Ensure reservation ID is set before opening the modal
            if (!reservationId) {
                alert('Reservation ID is missing.');
                return;
            }

            // Hide the Details Modal and show the Approve Modal
            const reservationDetailsModal = bootstrap.Modal.getInstance(document.getElementById('reservationDetailsModal'));
            reservationDetailsModal.hide();

            const approveModal = new bootstrap.Modal(document.getElementById('approveModal'));
            approveModal.show();
        }

        function openRejectModal() {
            const reservationId = document.getElementById('rejectReservationId').value;

            // Ensure reservation ID is set before opening the modal
            if (!reservationId) {
                alert('Reservation ID is missing.');
                return;
            }

            // Hide the Details Modal and show the Reject Modal
            const reservationDetailsModal = bootstrap.Modal.getInstance(document.getElementById('reservationDetailsModal'));
            reservationDetailsModal.hide();

            const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
            rejectModal.show();
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

            // Send POST request to approve the reservation
            $.post('approve_facilities_reservation.php', { id: reservationId }, function (response) {
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

            // Send POST request to reject the reservation
            $.post('reject_facilities_reservation.php', { id: reservationId, rejection_reason: rejectionReason }, function (response) {
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

    <!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>