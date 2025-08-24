<?php
include '../db.php'; // Include database connection

// Get the request ID from the query parameter
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the request details
$sql = "SELECT 
            wp.id, 
            CONCAT(u.last_name, ', ', u.first_name) AS full_name, 
            wp.address, 
            wp.contact_no, 
            wp.service_provider, 
            IF(wp.service_provider = 'Others', wp.other_service_provider, 'N/A') AS other_service_provider, 
            wp.utility_type, 
            IF(wp.utility_type = 'Others', wp.other_utility_type, 'N/A') AS other_utility_type,
            wp.date_of_work, 
            wp.created_at,
            wp.status,
            wp.approved_by,
            wp.rejection_reason,
            wp.pickup_schedule,
            wp.time_approved,
            wp.rejected_by,
            wp.time_rejected,
            wp.pickup_name 
        FROM work_permit_utilities wp
        JOIN users u ON wp.user_id = u.id
        WHERE wp.id = $request_id";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    die("Error fetching request details or no record found.");
}

$request = $result->fetch_assoc();

// Function to format status with color coding
function formatStatus($status) {
    $status = ucfirst($status); // Capitalize the first letter
    switch ($status) {
        case 'Approved':
            return "<span class='badge badge-success'>$status</span>";
        case 'Rejected':
            return "<span class='badge badge-danger'>$status</span>";
        case 'Pending':
            return "<span class='badge badge-warning'>$status</span>";
        default:
            return "<span class='badge badge-secondary'>$status</span>";
    }
}

// Function to get the document prefix
function getDocumentPrefix($documentType) {
    switch ($documentType) {
        case 'work_permit_utilities':
            return 'WPU';
        default:
            return 'DOC'; // Default prefix if no match
    }
}

// Generate the control number
$document_type = 'work_permit_utilities';
$prefix = getDocumentPrefix($document_type);
$controlNumber = sprintf('%s-%03d', $prefix, $request['id']);
?>

<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Work Permit for Utilities Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <p><i class="fas fa-hashtag"></i> <strong>Control Number:</strong> <?php echo htmlspecialchars($controlNumber); ?></p>
    <p><i class="fas fa-user"></i> <strong>Full Name:</strong> <?php echo htmlspecialchars($request['full_name']); ?></p>
    <p><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> <?php echo htmlspecialchars($request['address']); ?></p>
    <p><i class="fas fa-phone"></i> <strong>Contact Number:</strong> <?php echo htmlspecialchars($request['contact_no']); ?></p>
    <p><i class="fas fa-industry"></i> <strong>Service Provider:</strong> <?php echo htmlspecialchars($request['service_provider']); ?></p>
    <p><i class="fas fa-industry"></i> <strong>Other Service Provider:</strong> <?php echo htmlspecialchars($request['other_service_provider']); ?></p>
    <p><i class="fas fa-tools"></i> <strong>Utility Type:</strong> <?php echo htmlspecialchars($request['utility_type']); ?></p>
    <p><i class="fas fa-tools"></i> <strong>Other Utility Type:</strong> <?php echo htmlspecialchars($request['other_utility_type']); ?></p>
    <p><i class="fas fa-calendar-alt"></i> <strong>Date of Work:</strong> <?php echo date("F j, Y", strtotime($request['date_of_work'])); ?></p>
    <p><i class="fas fa-calendar-alt"></i> <strong>Created At:</strong> <?php echo date("F j, Y, g:i a", strtotime($request['created_at'])); ?></p>
    <p><i class="fas fa-info-circle"></i> <strong>Status:</strong> <?php echo formatStatus($request['status']); ?></p>
    <p><i class="fas fa-user-tag"></i> <strong>Will be picked up by:</strong> <?php echo htmlspecialchars($request['pickup_name'] ?? 'N/A'); ?></p>

    <?php if ($request['status'] === 'approved'): ?>
        <p><i class="fas fa-user-check"></i> <strong>Approved By:</strong> <?php echo htmlspecialchars($request['approved_by']); ?></p>
        <p><i class="fas fa-clock"></i> <strong>Time Approved:</strong> <?php echo date("F j, Y, g:i a", strtotime($request['time_approved'])); ?></p>
        <p><i class="fas fa-calendar-check"></i> <strong>Pickup Schedule:</strong> <?php echo htmlspecialchars($request['pickup_schedule']); ?></p>
    <?php elseif ($request['status'] === 'rejected'): ?>
        <p><i class="fas fa-user-times"></i> <strong>Rejected By:</strong> <?php echo htmlspecialchars($request['rejected_by']); ?></p>
        <p><i class="fas fa-clock"></i> <strong>Time Rejected:</strong> <?php echo date("F j, Y, g:i a", strtotime($request['time_rejected'])); ?></p>
        <p><i class="fas fa-ban"></i> <strong>Rejection Reason:</strong> <?php echo htmlspecialchars($request['rejection_reason']); ?></p>
    <?php endif; ?>
</div>
