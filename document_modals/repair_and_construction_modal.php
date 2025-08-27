<?php
include '../db.php'; // Include database connection

// Get the request ID from the query parameter
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the request details
$sql = "SELECT 
            rc.id, 
            CONCAT(u.last_name, ', ', u.first_name) AS full_name, 
            rc.contractor_name, 
            rc.contractor_contact, 
            rc.activity_nature, 
            rc.construction_address,
            rc.created_at,
            rc.status,
            rc.approved_by,
            rc.rejection_reason,
            rc.pickup_schedule,
            rc.time_approved,
            rc.rejected_by,
            rc.time_rejected,
            rc.pickup_name
        FROM repair_and_construction rc
        JOIN users u ON rc.user_id = u.id
        WHERE rc.id = $request_id";

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
            return "<span class='badge bg-success'>$status</span>";
        case 'Rejected':
            return "<span class='badge bg-danger'>$status</span>";
        case 'Pending':
            return "<span class='badge bg-warning text-dark'>$status</span>";
        case 'Picked_up':
        case 'Picked up':
            return "<span class='badge bg-info'>Picked Up</span>";
        default:
            return "<span class='badge bg-secondary'>$status</span>";
    }
}

// Function to get the document prefix
function getDocumentPrefix($documentType) {
    switch ($documentType) {
        case 'repair_and_construction':
            return 'RC';
        default:
            return 'DOC'; // Default prefix if no match
    }
}

// Generate the control number
$document_type = 'repair_and_construction';
$prefix = getDocumentPrefix($document_type);
$controlNumber = sprintf('%s-%03d', $prefix, $request['id']);
?>

<div class="modal-header">
    <h5 class="modal-title">Repair and Construction Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="detail-row">
        <span class="detail-icon">
            <i class="fas fa-hashtag"></i>
        </span>
        <div class="detail-label">Control Number:</div>
        <div class="detail-value"><?php echo htmlspecialchars($controlNumber); ?></div>
    </div>
    <div class="detail-row">
        <span class="detail-icon">
            <i class="fas fa-user"></i>
        </span>
        <div class="detail-label">Full Name:</div>
        <div class="detail-value"><?php echo htmlspecialchars($request['full_name']); ?></div>
    </div>
    <div class="detail-row">
        <span class="detail-icon">
            <i class="fas fa-building"></i>
        </span>
        <div class="detail-label">Contractor Name:</div>
        <div class="detail-value"><?php echo htmlspecialchars($request['contractor_name']); ?></div>
    </div>
    <div class="detail-row">
        <span class="detail-icon">
            <i class="fas fa-phone"></i>
        </span>
        <div class="detail-label">Contractor Contact:</div>
        <div class="detail-value"><?php echo htmlspecialchars($request['contractor_contact']); ?></div>
    </div>
    <div class="detail-row">
        <span class="detail-icon">
            <i class="fas fa-tools"></i>
        </span>
        <div class="detail-label">Nature of Activity:</div>
        <div class="detail-value"><?php echo htmlspecialchars($request['activity_nature']); ?></div>
    </div>
    <div class="detail-row">
        <span class="detail-icon">
            <i class="fas fa-map-marker-alt"></i>
        </span>
        <div class="detail-label">Construction Address:</div>
        <div class="detail-value"><?php echo htmlspecialchars($request['construction_address']); ?></div>
    </div>
    <div class="detail-row">
        <span class="detail-icon">
            <i class="fas fa-calendar-alt"></i>
        </span>
        <div class="detail-label">Created At:</div>
        <div class="detail-value"><?php echo date("F j, Y, g:i a", strtotime($request['created_at'])); ?></div>
    </div>
    <div class="detail-row">
        <span class="detail-icon">
            <i class="fas fa-info-circle"></i>
        </span>
        <div class="detail-label">Status:</div>
        <div class="detail-value"><?php echo formatStatus($request['status']); ?></div>
    </div>
    <div class="detail-row">
        <span class="detail-icon">
            <i class="fas fa-user-tag"></i>
        </span>
        <div class="detail-label">Will be picked up by:</div>
        <div class="detail-value"><?php echo htmlspecialchars($request['pickup_name'] ?? 'N/A'); ?></div>
    </div>

    <?php if ($request['status'] === 'approved'): ?>
        <div class="detail-row">
            <span class="detail-icon">
                <i class="fas fa-user-check"></i>
            </span>
            <div class="detail-label">Approved By:</div>
            <div class="detail-value"><?php echo htmlspecialchars($request['approved_by']); ?></div>
        </div>
        <div class="detail-row">
            <span class="detail-icon">
                <i class="fas fa-clock"></i>
            </span>
            <div class="detail-label">Time Approved:</div>
            <div class="detail-value"><?php echo date("F j, Y, g:i a", strtotime($request['time_approved'])); ?></div>
        </div>
        <div class="detail-row">
            <span class="detail-icon">
                <i class="fas fa-calendar-check"></i>
            </span>
            <div class="detail-label">Pickup Schedule:</div>
            <div class="detail-value"><?php echo htmlspecialchars($request['pickup_schedule']); ?></div>
        </div>
    <?php elseif ($request['status'] === 'rejected'): ?>
        <div class="detail-row">
            <span class="detail-icon">
                <i class="fas fa-user-times"></i>
            </span>
            <div class="detail-label">Rejected By:</div>
            <div class="detail-value"><?php echo htmlspecialchars($request['rejected_by']); ?></div>
        </div>
        <div class="detail-row">
            <span class="detail-icon">
                <i class="fas fa-clock"></i>
            </span>
            <div class="detail-label">Time Rejected:</div>
            <div class="detail-value"><?php echo date("F j, Y, g:i a", strtotime($request['time_rejected'])); ?></div>
        </div>
        <div class="detail-row">
            <span class="detail-icon">
                <i class="fas fa-ban"></i>
            </span>
            <div class="detail-label">Rejection Reason:</div>
            <div class="detail-value"><?php echo htmlspecialchars($request['rejection_reason']); ?></div>
        </div>
    <?php endif; ?>
</div>
