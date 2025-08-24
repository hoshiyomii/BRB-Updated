<?php
include '../db.php'; // Include database connection

// Get the request ID from the query parameter
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the request details
$sql = "SELECT 
            cr.id, 
            CONCAT(u.last_name, ', ', u.first_name) AS full_name, 
            CONCAT(u.house_number, ' ', u.street) AS address, 
            u.birthdate, 
            cr.resident_since, 
            cr.id_image, 
            cr.created_at,
            cr.status,
            cr.approved_by,
            cr.rejection_reason,
            cr.pickup_schedule,
            cr.time_approved,
            cr.rejected_by,
            cr.time_rejected,
            cr.pickup_name 
        FROM certificate_of_residency cr
        JOIN users u ON cr.user_id = u.id
        WHERE cr.id = $request_id";

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
        case 'certificate_of_residency':
            return 'COR';
        default:
            return 'DOC';
    }
}

// Generate the control number
$document_type = 'certificate_of_residency';
$prefix = getDocumentPrefix($document_type);
$controlNumber = sprintf('%s-%03d', $prefix, $request['id']);
?>

<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Certificate of Residency Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <p><i class="fas fa-hashtag"></i> <strong>Control Number:</strong> <?php echo htmlspecialchars($controlNumber); ?></p>
    <p><i class="fas fa-user"></i> <strong>Full Name:</strong> <?php echo htmlspecialchars($request['full_name']); ?></p>
    <p><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> <?php echo htmlspecialchars($request['address']); ?></p>
    <p><i class="fas fa-birthday-cake"></i> <strong>Birthdate:</strong> <?php echo date("F j, Y", strtotime($request['birthdate'])); ?></p>
    <p><i class="fas fa-calendar-alt"></i> <strong>Resident Since:</strong> <?php echo htmlspecialchars($request['resident_since']); ?></p>
    <p><i class="fas fa-id-card"></i> <strong>ID Image:</strong> 
        <a href="<?php echo htmlspecialchars($request['id_image']); ?>" target="_blank">
            <i class="fas fa-eye"></i> View ID Image
        </a>
    </p>
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
