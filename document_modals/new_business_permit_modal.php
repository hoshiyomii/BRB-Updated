<?php
include '../db.php'; // Include database connection

// Get the request ID from the query parameter
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the request details
$sql = "SELECT 
            nbp.id, 
            nbp.owner, 
            IFNULL(nbp.co_owner, 'N/A') AS co_owner, 
            nbp.location, 
            nbp.business_name, 
            nbp.nature_of_business, 
            nbp.business_type, 
            nbp.created_at,
            nbp.status,
            nbp.approved_by,
            nbp.rejection_reason,
            nbp.pickup_schedule,
            nbp.time_approved,
            nbp.rejected_by,
            nbp.time_rejected,
            nbp.pickup_name 
        FROM new_business_permit nbp
        WHERE nbp.id = $request_id";

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
        case 'new_business_permit':
            return 'NBP';
        default:
            return 'DOC'; // Default prefix if no match
    }
}

// Generate the control number
$document_type = 'new_business_permit';
$prefix = getDocumentPrefix($document_type);
$controlNumber = sprintf('%s-%03d', $prefix, $request['id']);
?>

<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">New Business Permit Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <p><i class="fas fa-hashtag"></i> <strong>Control Number:</strong> <?php echo htmlspecialchars($controlNumber); ?></p>
    <p><i class="fas fa-user"></i> <strong>Owner:</strong> <?php echo htmlspecialchars($request['owner']); ?></p>
    <p><i class="fas fa-user-friends"></i> <strong>Co-Owner:</strong> <?php echo htmlspecialchars($request['co_owner']); ?></p>
    <p><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> <?php echo htmlspecialchars($request['location']); ?></p>
    <p><i class="fas fa-building"></i> <strong>Business Name:</strong> <?php echo htmlspecialchars($request['business_name']); ?></p>
    <p><i class="fas fa-briefcase"></i> <strong>Nature of Business:</strong> <?php echo htmlspecialchars($request['nature_of_business']); ?></p>
    <p><i class="fas fa-industry"></i> <strong>Business Type:</strong> <?php echo htmlspecialchars($request['business_type']); ?></p>
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
