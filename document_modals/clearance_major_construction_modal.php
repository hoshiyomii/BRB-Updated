<?php
include '../db.php'; // Include database connection

// Get the request ID from the query parameter
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the request details
$sql = "SELECT 
            cmc.id, 
            CONCAT(u.last_name, ', ', u.first_name) AS full_name, 
            cmc.schedule AS construction_schedule, 
            cmc.contractor, 
            cmc.construction_address, 
            cmc.infrastructures, 
            cmc.created_at,
            cmc.status,
            cmc.approved_by,
            cmc.rejection_reason,
            cmc.pickup_schedule,
            cmc.time_approved,
            cmc.rejected_by,
            cmc.time_rejected,
            cmc.pickup_name
        FROM clearance_major_construction cmc
        JOIN users u ON cmc.user_id = u.id
        WHERE cmc.id = $request_id";

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
        case 'clearance_major_construction':
            return 'CMC';
        default:
            return 'DOC'; // Default prefix if no match
    }
}

// Generate the control number
$document_type = 'clearance_major_construction';
$prefix = getDocumentPrefix($document_type);
$controlNumber = sprintf('%s-%03d', $prefix, $request['id']);
?>

<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Clearance for Major Construction Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <p><i class="fas fa-hashtag"></i> <strong>Control Number:</strong> <?php echo htmlspecialchars($controlNumber); ?></p>
    <p><i class="fas fa-user"></i> <strong>Full Name:</strong> <?php echo htmlspecialchars($request['full_name']); ?></p>
    <p><i class="fas fa-calendar-alt"></i> <strong>Construction Schedule:</strong> <?php echo htmlspecialchars($request['construction_schedule']); ?></p>
    <p><i class="fas fa-building"></i> <strong>Contractor:</strong> <?php echo htmlspecialchars($request['contractor']); ?></p>
    <p><i class="fas fa-map-marker-alt"></i> <strong>Construction Address:</strong> <?php echo htmlspecialchars($request['construction_address']); ?></p>
    <p><i class="fas fa-tools"></i> <strong>Infrastructures:</strong> <?php echo htmlspecialchars($request['infrastructures']); ?></p>
    <p><i class="fas fa-user-tag"></i> <strong>Will be picked up by:</strong> <?php echo htmlspecialchars($request['pickup_name'] ?? 'N/A'); ?></p>
    <p><i class="fas fa-calendar-alt"></i> <strong>Created At:</strong> <?php echo date("F j, Y, g:i a", strtotime($request['created_at'])); ?></p>
    <p><i class="fas fa-info-circle"></i> <strong>Status:</strong> <?php echo formatStatus($request['status']); ?></p>

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
