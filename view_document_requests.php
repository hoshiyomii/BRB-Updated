<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

function generateControlNo($documentType, $id) {
    $prefixes = [
        'repair_and_construction' => 'RC',
        'work_permit_utilities' => 'WPU',
        'certificate_of_residency' => 'CR',
        'certificate_of_indigency' => 'CI',
        'new_business_permit' => 'NBP',
        'clearance_major_construction' => 'CMC',
    ];

    $prefix = isset($prefixes[$documentType]) ? $prefixes[$documentType] : 'DOC';
    return $prefix . '-' . str_pad($id, 3, '0', STR_PAD_LEFT); // Format as PREFIX-001
}

// Get the selected document type for filtering
$document_type = isset($_GET['document_type']) ? $_GET['document_type'] : 'all';

if ($document_type === 'all') {
    $result = null;
} else {
    if ($document_type === 'repair_and_construction') {
        $sql = "SELECT 
                    rc.id, 
                    u.last_name, 
                    u.first_name, 
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
                    rc.time_rejected
                FROM repair_and_construction rc
                JOIN users u ON rc.user_id = u.id
                ORDER BY rc.created_at DESC";
    } elseif ($document_type === 'work_permit_utilities') {
        $sql = "SELECT 
                    wp.id, 
                    u.last_name, 
                    u.first_name, 
                    wp.address, 
                    wp.contact_no, 
                    wp.nature_of_work, 
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
                    wp.time_rejected 
                FROM work_permit_utilities wp
                JOIN users u ON wp.user_id = u.id
                ORDER BY wp.created_at DESC";
    } elseif ($document_type === 'certificate_of_residency') {
        $sql = "SELECT 
                    cr.id, 
                    u.last_name, 
                    u.first_name, 
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
                    cr.time_rejected 
                FROM certificate_of_residency cr
                JOIN users u ON cr.user_id = u.id
                ORDER BY cr.created_at DESC";
    } elseif ($document_type === 'certificate_of_indigency') {
        $sql = "SELECT 
                    ci.id, 
                    u.last_name, 
                    u.first_name, 
                    CONCAT(u.house_number, ' ', u.street) AS address, 
                    ci.occupancy, 
                    ci.income AS monthly_income, 
                    ci.created_at, 
                    ci.status,
                    ci.approved_by,
                    ci.rejection_reason,
                    ci.pickup_schedule,
                    ci.time_approved,
                    ci.rejected_by,
                    ci.time_rejected
                FROM certificate_of_indigency ci
                JOIN users u ON ci.user_id = u.id
                ORDER BY ci.created_at DESC";
    } elseif ($document_type === 'new_business_permit') {
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
                    nbp.time_rejected 
                FROM new_business_permit nbp
                ORDER BY nbp.created_at DESC";
    } elseif ($document_type === 'clearance_major_construction') {
        $sql = "SELECT 
                    cmc.id, 
                    u.last_name, 
                    u.first_name, 
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
                    cmc.time_rejected
                FROM clearance_major_construction cmc
                JOIN users u ON cmc.user_id = u.id
                ORDER BY cmc.created_at DESC";
    } else {
        $sql = "SELECT * FROM $document_type ORDER BY created_at DESC";
    }
    $result = $conn->query($sql);

    if (!$result) {
        die("Error fetching document requests: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Document Requests</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="view_document_requests.css">
</head>
<body>
    <div class="container mt-5">
        <h1>View Document Requests</h1>
        <div class="mb-3">
            <label for="document_type">Filter by Document Type:</label>
            <select id="document_type" class="form-control" onchange="filterRequests()">
                <option value="all" <?php if ($document_type === 'all') echo 'selected'; ?>>Select Document Type</option>
                <option value="repair_and_construction" <?php if ($document_type === 'repair_and_construction') echo 'selected'; ?>>Repair and Construction</option>
                <option value="work_permit_utilities" <?php if ($document_type === 'work_permit_utilities') echo 'selected'; ?>>Work Permit for Utilities</option>
                <option value="certificate_of_residency" <?php if ($document_type === 'certificate_of_residency') echo 'selected'; ?>>Certificate of Residency</option>
                <option value="certificate_of_indigency" <?php if ($document_type === 'certificate_of_indigency') echo 'selected'; ?>>Certificate of Indigency</option>
                <option value="new_business_permit" <?php if ($document_type === 'new_business_permit') echo 'selected'; ?>>New Business Permit</option>
                <option value="clearance_major_construction" <?php if ($document_type === 'clearance_major_construction') echo 'selected'; ?>>Clearance for Major Construction</option>
            </select>
        </div>
        <?php if ($document_type === 'all'): ?>
            <p>Please select a document type to view the requests.</p>
        <?php elseif ($document_type === 'repair_and_construction' && $result && $result->num_rows > 0): ?>
            <h2>Repair and Construction Requests</h2>
            <table class="table table-bordered table-striped mt-3">
                <thead class="thead-dark">
                    <tr>
                        <th>Control No.</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Contractor Name</th>
                        <th>Contractor Contact</th>
                        <th>Nature of Activity</th>
                        <th>Construction Address</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr style="
                            <?php 
                                if ($row['status'] === 'approved') {
                                    echo 'background-color: #d4edda;'; // Light Green for Approved
                                } elseif ($row['status'] === 'rejected') {
                                    echo 'background-color: #f8d7da;'; // Light Red for Declined
                                } else {
                                    echo 'background-color: #fff3cd;'; // Light Yellow for Pending
                                }
                            ?>
                        ">
                            <td><?php echo generateControlNo($document_type, $row['id']); ?></td> <!-- Generate Control No. -->
                            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['contractor_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['contractor_contact']); ?></td>
                            <td><?php echo htmlspecialchars($row['activity_nature']); ?></td>
                            <td><?php echo htmlspecialchars($row['construction_address']); ?></td>
                            <td><?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <!-- Print Button -->
                                    <button class="btn btn-primary btn-sm" onclick="printDocument(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Print" <?php echo $row['status'] !== 'approved' ? 'disabled' : ''; ?>>
                                        <i class="bi bi-printer"></i>
                                    </button>

                                    <!-- Approve Button -->
                                    <button class="btn btn-success btn-sm" onclick="showApproveModal(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Approve" <?php echo $row['status'] !== 'pending' ? 'disabled' : ''; ?>>
                                        <i class="bi bi-check-lg"></i>
                                    </button>

                                    <!-- Decline Button -->
                                    <button class="btn btn-danger btn-sm" onclick="showDeclineModal(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Decline" <?php echo $row['status'] !== 'pending' ? 'disabled' : ''; ?>>
                                        <i class="bi bi-x-lg"></i>
                                    </button>

                                    <!-- Details Button -->
                                    <button class="btn btn-info btn-sm" onclick="toggleDetails(<?php echo $row['id']; ?>)" title="Details">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                </div>
                                <div id="details-<?php echo $row['id']; ?>" class="details-box" style="display: none; margin-top: 10px;">
                                    <?php if ($row['status'] === 'approved'): ?>
                                        <p><strong>Scheduled Pickup:</strong> <?php echo htmlspecialchars($row['pickup_schedule']); ?></p>
                                        <p><strong>Approved by:</strong> <?php echo htmlspecialchars($row['approved_by']); ?></p>
                                        <p><strong>Time Approved:</strong> <?php echo htmlspecialchars($row['time_approved']); ?></p>
                                    <?php elseif ($row['status'] === 'rejected'): ?>
                                        <p><strong>Rejected because:</strong> <?php echo htmlspecialchars($row['rejection_reason']); ?></p>
                                        <p><strong>Rejected by:</strong> <?php echo htmlspecialchars($row['rejected_by']); ?></p>
                                        <p><strong>Time Rejected:</strong> <?php echo htmlspecialchars($row['time_rejected']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php elseif ($document_type === 'work_permit_utilities' && $result && $result->num_rows > 0): ?>
            <h2>Work Permit for Utilities Requests</h2>
            <table class="table table-bordered table-striped mt-3">
                <thead class="thead-dark">
                    <tr>
                        <th>Control No.</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Address</th>
                        <th>Contact Number</th>
                        <th>Nature of Work</th>
                        <th>Service Provider</th>
                        <th>Other Service Provider</th>
                        <th>Utility Type</th>
                        <th>Other Utility Type</th>
                        <th>Date of Work</th>
                        <th>Date Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr style="
                            <?php 
                                if ($row['status'] === 'approved') {
                                    echo 'background-color: #d4edda;'; // Light Green for Approved
                                } elseif ($row['status'] === 'rejected') {
                                    echo 'background-color: #f8d7da;'; // Light Red for Declined
                                } else {
                                    echo 'background-color: #fff3cd;'; // Light Yellow for Pending
                                }
                            ?>
                        ">
                            <td><?php echo generateControlNo($document_type, $row['id']); ?></td> <!-- Generate Control No. -->
                            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['nature_of_work']); ?></td>
                            <td><?php echo htmlspecialchars($row['service_provider']); ?></td>
                            <td><?php echo htmlspecialchars($row['other_service_provider']); ?></td>
                            <td><?php echo htmlspecialchars($row['utility_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['other_utility_type']); ?></td>
                            <td><?php echo date("F j, Y", strtotime($row['date_of_work'])); ?></td>
                            <td><?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <!-- Print Button -->
                                    <button class="btn btn-primary btn-sm" onclick="printDocument(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Print" <?php echo $row['status'] !== 'approved' ? 'disabled' : ''; ?>>
                                        <i class="bi bi-printer"></i>
                                    </button>

                                    <!-- Approve Button -->
                                    <button class="btn btn-success btn-sm" onclick="showApproveModal(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Approve" <?php echo $row['status'] !== 'pending' ? 'disabled' : ''; ?>>
                                        <i class="bi bi-check-lg"></i>
                                    </button>

                                    <!-- Decline Button -->
                                    <button class="btn btn-danger btn-sm" onclick="showDeclineModal(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Decline" <?php echo $row['status'] !== 'pending' ? 'disabled' : ''; ?>>
                                        <i class="bi bi-x-lg"></i>
                                    </button>

                                    <!-- Details Button -->
                                    <button class="btn btn-info btn-sm" onclick="toggleDetails(<?php echo $row['id']; ?>)" title="Details">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                </div>
                                <div id="details-<?php echo $row['id']; ?>" class="details-box" style="display: none; margin-top: 10px;">
                                    <?php if ($row['status'] === 'approved'): ?>
                                        <p><strong>Scheduled Pickup:</strong> <?php echo htmlspecialchars($row['pickup_schedule']); ?></p>
                                        <p><strong>Approved by:</strong> <?php echo htmlspecialchars($row['approved_by']); ?></p>
                                        <p><strong>Time Approved:</strong> <?php echo htmlspecialchars($row['time_approved']); ?></p>
                                    <?php elseif ($row['status'] === 'rejected'): ?>
                                        <p><strong>Rejected because:</strong> <?php echo htmlspecialchars($row['rejection_reason']); ?></p>
                                        <p><strong>Rejected by:</strong> <?php echo htmlspecialchars($row['rejected_by']); ?></p>
                                        <p><strong>Time Rejected:</strong> <?php echo htmlspecialchars($row['time_rejected']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php elseif ($document_type === 'certificate_of_residency' && $result && $result->num_rows > 0): ?>
            <h2>Certificate of Residency Requests</h2>
    <table class="table table-bordered table-striped mt-3">
        <thead class="thead-dark">
            <tr>
                <th>Control No.</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Address</th>
                <th>Birthdate</th>
                <th>Resident Since</th>
                <th>ID Image</th>
                <th>Created At</th>
                <th>Action</th> <!-- New column for Print button -->
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr style="
                            <?php 
                                if ($row['status'] === 'approved') {
                                    echo 'background-color: #d4edda;'; // Light Green for Approved
                                } elseif ($row['status'] === 'rejected') {
                                    echo 'background-color: #f8d7da;'; // Light Red for Declined
                                } else {
                                    echo 'background-color: #fff3cd;'; // Light Yellow for Pending
                                }
                            ?>
                ">
                    <td><?php echo generateControlNo($document_type, $row['id']); ?></td> <!-- Generate Control No. -->
                    <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                    <td><?php echo date("F j, Y", strtotime($row['birthdate'])); ?></td>
                    <td><?php echo date("F j, Y", strtotime($row['resident_since'])); ?></td>
                    <td>
                        <?php if (!empty($row['id_image'])): ?>
                            <button class="btn btn-info btn-sm" onclick="displayImage('<?php echo htmlspecialchars($row['id_image']); ?>')">View ID</button>
                        <?php else: ?>
                            No ID Image
                        <?php endif; ?>
                    </td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></td>
                    <td>
                        <div class="action-buttons">
                            <!-- Print Button -->
                            <button class="btn btn-primary btn-sm" onclick="printDocument(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Print" <?php echo $row['status'] !== 'approved' ? 'disabled' : ''; ?>>
                                <i class="bi bi-printer"></i>
                            </button>

                            <!-- Approve Button -->
                            <button class="btn btn-success btn-sm" onclick="showApproveModal(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Approve" <?php echo $row['status'] !== 'pending' ? 'disabled' : ''; ?>>
                                <i class="bi bi-check-lg"></i>
                            </button>

                            <!-- Decline Button -->
                            <button class="btn btn-danger btn-sm" onclick="showDeclineModal(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Decline" <?php echo $row['status'] !== 'pending' ? 'disabled' : ''; ?>>
                                <i class="bi bi-x-lg"></i>
                            </button>

                            <!-- Details Button -->
                            <button class="btn btn-info btn-sm" onclick="toggleDetails(<?php echo $row['id']; ?>)" title="Details">
                                <i class="bi bi-info-circle"></i>
                            </button>
                        </div>
                        <div id="details-<?php echo $row['id']; ?>" class="details-box" style="display: none; margin-top: 10px;">
                            <?php if ($row['status'] === 'approved'): ?>
                                <p><strong>Scheduled Pickup:</strong> <?php echo htmlspecialchars($row['pickup_schedule']); ?></p>
                                <p><strong>Approved by:</strong> <?php echo htmlspecialchars($row['approved_by']); ?></p>
                                <p><strong>Time Approved:</strong> <?php echo htmlspecialchars($row['time_approved']); ?></p>
                            <?php elseif ($row['status'] === 'rejected'): ?>
                                <p><strong>Rejected because:</strong> <?php echo htmlspecialchars($row['rejection_reason']); ?></p>
                                <p><strong>Rejected by:</strong> <?php echo htmlspecialchars($row['rejected_by']); ?></p>
                                <p><strong>Time Rejected:</strong> <?php echo htmlspecialchars($row['time_rejected']); ?></p>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
        <?php elseif ($document_type === 'certificate_of_indigency' && $result && $result->num_rows > 0): ?>
    <h2>Certificate of Indigency Requests</h2>
    <table class="table table-bordered table-striped mt-3">
        <thead class="thead-dark">
            <tr>
                <th>Control No.</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Address</th>
                <th>Occupancy</th>
                <th>Monthly Income</th>
                <th>Created At</th>
                <th>Action</th> <!-- New column for Print button -->
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr style="
                            <?php 
                                if ($row['status'] === 'approved') {
                                    echo 'background-color: #d4edda;'; // Light Green for Approved
                                } elseif ($row['status'] === 'rejected') {
                                    echo 'background-color: #f8d7da;'; // Light Red for Declined
                                } else {
                                    echo 'background-color: #fff3cd;'; // Light Yellow for Pending
                                }
                            ?>
                        ">
                    <td><?php echo generateControlNo($document_type, $row['id']); ?></td> <!-- Generate Control No. -->
                    <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                    <td><?php echo htmlspecialchars($row['occupancy']); ?></td>
                    <td><?php echo htmlspecialchars($row['monthly_income']); ?></td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></td>
                    <td>
                        <div class="action-buttons">
                            <!-- Print Button -->
                            <button class="btn btn-primary btn-sm" onclick="printDocument(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Print" <?php echo $row['status'] !== 'approved' ? 'disabled' : ''; ?>>
                                <i class="bi bi-printer"></i>
                            </button>

                            <!-- Approve Button -->
                            <button class="btn btn-success btn-sm" onclick="showApproveModal(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Approve" <?php echo $row['status'] !== 'pending' ? 'disabled' : ''; ?>>
                                <i class="bi bi-check-lg"></i>
                            </button>

                            <!-- Decline Button -->
                            <button class="btn btn-danger btn-sm" onclick="showDeclineModal(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Decline" <?php echo $row['status'] !== 'pending' ? 'disabled' : ''; ?>>
                                <i class="bi bi-x-lg"></i>
                            </button>

                            <!-- Details Button -->
                            <button class="btn btn-info btn-sm" onclick="toggleDetails(<?php echo $row['id']; ?>)" title="Details">
                                <i class="bi bi-info-circle"></i>
                            </button>
                        </div>
                        <div id="details-<?php echo $row['id']; ?>" class="details-box" style="display: none; margin-top: 10px;">
                            <?php if ($row['status'] === 'approved'): ?>
                                <p><strong>Scheduled Pickup:</strong> <?php echo htmlspecialchars($row['pickup_schedule']); ?></p>
                                <p><strong>Approved by:</strong> <?php echo htmlspecialchars($row['approved_by']); ?></p>
                                <p><strong>Time Approved:</strong> <?php echo htmlspecialchars($row['time_approved']); ?></p>
                            <?php elseif ($row['status'] === 'rejected'): ?>
                                <p><strong>Rejected because:</strong> <?php echo htmlspecialchars($row['rejection_reason']); ?></p>
                                <p><strong>Rejected by:</strong> <?php echo htmlspecialchars($row['rejected_by']); ?></p>
                                <p><strong>Time Rejected:</strong> <?php echo htmlspecialchars($row['time_rejected']); ?></p>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
        <?php elseif ($document_type === 'new_business_permit' && $result && $result->num_rows > 0): ?>
    <h2>New Business Permit Requests</h2>
    <table class="table table-bordered table-striped mt-3">
        <thead class="thead-dark">
            <tr>
                <th>Control No.</th>
                <th>Owner</th>
                <th>Co-Owner</th>
                <th>Location</th>
                <th>Business Name</th>
                <th>Nature of Business</th>
                <th>Business Type</th>
                <th>Created At</th>
                <th>Action</th> <!-- New column for Print button -->
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr style="
                            <?php 
                                if ($row['status'] === 'approved') {
                                    echo 'background-color: #d4edda;'; // Light Green for Approved
                                } elseif ($row['status'] === 'rejected') {
                                    echo 'background-color: #f8d7da;'; // Light Red for Declined
                                } else {
                                    echo 'background-color: #fff3cd;'; // Light Yellow for Pending
                                }
                            ?>
                        ">
                    <td><?php echo generateControlNo($document_type, $row['id']); ?></td> <!-- Generate Control No. -->
                    <td><?php echo htmlspecialchars($row['owner']); ?></td>
                    <td><?php echo htmlspecialchars($row['co_owner']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td><?php echo htmlspecialchars($row['business_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['nature_of_business']); ?></td>
                    <td><?php echo htmlspecialchars($row['business_type']); ?></td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></td>
                    <td>
                        <div class="action-buttons">
                            <!-- Print Button -->
                            <button class="btn btn-primary btn-sm" onclick="printDocument(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Print" <?php echo $row['status'] !== 'approved' ? 'disabled' : ''; ?>>
                                <i class="bi bi-printer"></i>
                            </button>

                            <!-- Approve Button -->
                            <button class="btn btn-success btn-sm" onclick="showApproveModal(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Approve" <?php echo $row['status'] !== 'pending' ? 'disabled' : ''; ?>>
                                <i class="bi bi-check-lg"></i>
                            </button>

                            <!-- Decline Button -->
                            <button class="btn btn-danger btn-sm" onclick="showDeclineModal(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Decline" <?php echo $row['status'] !== 'pending' ? 'disabled' : ''; ?>>
                                <i class="bi bi-x-lg"></i>
                            </button>

                            <!-- Details Button -->
                            <button class="btn btn-info btn-sm" onclick="toggleDetails(<?php echo $row['id']; ?>)" title="Details">
                                <i class="bi bi-info-circle"></i>
                            </button>
                        </div>
                        <div id="details-<?php echo $row['id']; ?>" class="details-box" style="display: none; margin-top: 10px;">
                            <?php if ($row['status'] === 'approved'): ?>
                                <p><strong>Scheduled Pickup:</strong> <?php echo htmlspecialchars($row['pickup_schedule']); ?></p>
                                <p><strong>Approved by:</strong> <?php echo htmlspecialchars($row['approved_by']); ?></p>
                                <p><strong>Time Approved:</strong> <?php echo htmlspecialchars($row['time_approved']); ?></p>
                            <?php elseif ($row['status'] === 'rejected'): ?>
                                <p><strong>Rejected because:</strong> <?php echo htmlspecialchars($row['rejection_reason']); ?></p>
                                <p><strong>Rejected by:</strong> <?php echo htmlspecialchars($row['rejected_by']); ?></p>
                                <p><strong>Time Rejected:</strong> <?php echo htmlspecialchars($row['time_rejected']); ?></p>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php elseif ($document_type === 'clearance_major_construction' && $result && $result->num_rows > 0): ?>
    <h2>Clearance for Major Construction Requests</h2>
    <table class="table table-bordered table-striped mt-3">
        <thead class="thead-dark">
            <tr>
                <th>Control No.</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Construction Schedule</th>
                <th>Contractor</th>
                <th>Construction Address</th>
                <th>Infrastructures to be Built</th>
                <th>Created At</th>
                <th>Action</th> <!-- New column for Approve/Decline buttons -->
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr style="
                            <?php 
                                if ($row['status'] === 'approved') {
                                    echo 'background-color: #d4edda;'; // Light Green for Approved
                                } elseif ($row['status'] === 'rejected') {
                                    echo 'background-color: #f8d7da;'; // Light Red for Declined
                                } else {
                                    echo 'background-color: #fff3cd;'; // Light Yellow for Pending
                                }
                            ?>
                        ">
                    <td><?php echo generateControlNo($document_type, $row['id']); ?></td> <!-- Generate Control No. -->
                    <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($row['construction_schedule'])); ?></td>
                    <td><?php echo htmlspecialchars($row['contractor']); ?></td>
                    <td><?php echo htmlspecialchars($row['construction_address']); ?></td>
                    <td><?php echo htmlspecialchars($row['infrastructures']); ?></td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></td>
                    <td>
                        <div class="action-buttons">
                            <!-- Print Button -->
                            <button class="btn btn-primary btn-sm" onclick="printDocument(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Print" <?php echo $row['status'] !== 'approved' ? 'disabled' : ''; ?>>
                                <i class="bi bi-printer"></i>
                            </button>

                            <!-- Approve Button -->
                            <button class="btn btn-success btn-sm" onclick="showApproveModal(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Approve" <?php echo $row['status'] !== 'pending' ? 'disabled' : ''; ?>>
                                <i class="bi bi-check-lg"></i>
                            </button>

                            <!-- Decline Button -->
                            <button class="btn btn-danger btn-sm" onclick="showDeclineModal(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Decline" <?php echo $row['status'] !== 'pending' ? 'disabled' : ''; ?>>
                                <i class="bi bi-x-lg"></i>
                            </button>

                            <!-- Details Button -->
                            <button class="btn btn-info btn-sm" onclick="toggleDetails(<?php echo $row['id']; ?>)" title="Details">
                                <i class="bi bi-info-circle"></i>
                            </button>
                        </div>
                        <div id="details-<?php echo $row['id']; ?>" class="details-box" style="display: none; margin-top: 10px;">
                            <?php if ($row['status'] === 'approved'): ?>
                                <p><strong>Scheduled Pickup:</strong> <?php echo htmlspecialchars($row['pickup_schedule']); ?></p>
                                <p><strong>Approved by:</strong> <?php echo htmlspecialchars($row['approved_by']); ?></p>
                                <p><strong>Time Approved:</strong> <?php echo htmlspecialchars($row['time_approved']); ?></p>
                            <?php elseif ($row['status'] === 'rejected'): ?>
                                <p><strong>Rejected because:</strong> <?php echo htmlspecialchars($row['rejection_reason']); ?></p>
                                <p><strong>Rejected by:</strong> <?php echo htmlspecialchars($row['rejected_by']); ?></p>
                                <p><strong>Time Rejected:</strong> <?php echo htmlspecialchars($row['time_rejected']); ?></p>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php elseif ($result && is_object($result) && $result->num_rows > 0): ?>
    <table class="table table-bordered table-striped mt-3">
        <thead class="thead-dark">
            <tr>
                <?php
                // Dynamically fetch column names
                $columns = array_keys($result->fetch_assoc());
                foreach ($columns as $column) {
                    echo "<th>" . htmlspecialchars($column) . "</th>";
                }
                $result->data_seek(0); // Reset result pointer
                ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <?php foreach ($row as $column => $value): ?>
                        <?php if ($column === 'id_image' && !empty($value)): ?>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="displayImage('<?php echo htmlspecialchars($value); ?>')">Display Image</button>
                            </td>
                        <?php else: ?>
                            <td><?php echo htmlspecialchars($value); ?></td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No records found for the selected document type.</p>
<?php endif; ?>
        <a href="admin_dashboard.php" class="btn btn-primary">Back to Admin Dashboard</a>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approve Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this request?</p>
                    <p><strong>Details:</strong></p>
                    <ul id="approveDetails"></ul>
                    <div class="form-group">
                        <label for="pickupSchedule">Schedule Pickup Date and Time:</label>
                        <input type="datetime-local" id="pickupSchedule" class="form-control">
                    </div>
                    <div class="form-group mt-3">
                        <label for="approveConfirmation">Type "I APPROVE" to confirm:</label>
                        <input type="text" id="approveConfirmation" class="form-control" placeholder="I APPROVE">
                    </div>
                    <p class="mt-3"><strong>Requester Details:</strong></p>
                    <ul id="requesterDetails" class="list-unstyled">
                        <!-- Requester details will be dynamically populated here -->
                    </ul>
                    <p class="text-info mt-3">
                        Please contact the requester to discuss the pickup schedule or any necessary corrections.
                    </p>
                    <p id="approveFeedback" class="mt-3 text-center"></p> <!-- Feedback message -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmApprove">Approve</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Decline Modal -->
    <div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="declineModalLabel">Decline Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to decline this request?</p>
                    <p><strong>Details:</strong></p>
                    <ul id="declineDetails"></ul>
                    <div class="form-group">
                        <label for="declineReason">Reason for declining:</label>
                        <textarea id="declineReason" class="form-control" rows="3" placeholder="Enter reason"></textarea>
                    </div>
                    <p class="mt-3"><strong>Requester Details:</strong></p>
                    <ul id="requesterDetailsDecline" class="list-unstyled">
                        <!-- Requester details will be dynamically populated here -->
                    </ul>
                    <p class="text-info mt-3">
                        Please contact the requester to discuss the reasons for rejection or necessary corrections.
                    </p>
                    <p id="declineFeedback" class="mt-3 text-center"></p> <!-- Feedback message -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDecline">Decline</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterRequests() {
            var document_type = document.getElementById('document_type').value;
            window.location.href = 'view_document_requests.php?document_type=' + document_type;
        }

        function displayImage(imagePath) {
            // Open the image in a new tab
            window.open(imagePath, '_blank');
        }

        function printDocument(id, type) {
            window.open('generate_document.php?id=' + id + '&type=' + type, '_blank');
        }

        function generateControlNo(documentType, id) {
            const prefixes = {
                'repair_and_construction': 'RC',
                'work_permit_utilities': 'WPU',
                'certificate_of_residency': 'CR',
                'certificate_of_indigency': 'CI',
                'new_business_permit': 'NBP',
                'clearance_major_construction': 'CMC',
            };

            const prefix = prefixes[documentType] || 'DOC';
            return `${prefix}-${String(id).padStart(3, '0')}`; // Format as PREFIX-001
        }

        function updateStatus(id, status, table, reason = null, feedbackElement, modalId, pickupSchedule = null) {
            fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id, status, table, reason, pickup_schedule: pickupSchedule }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        feedbackElement.textContent = `Document request ${status} successfully.`;
                        feedbackElement.className = 'text-success'; // Add success styling
                        setTimeout(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                            modal.hide(); // Close the modal
                            location.reload(); // Reload the page to reflect changes
                        }, 2000); // Close modal after 2 seconds
                    } else {
                        feedbackElement.textContent = `Failed to ${status} document request: ${data.error}`;
                        feedbackElement.className = 'text-danger'; // Add error styling
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    feedbackElement.textContent = 'An error occurred while updating the status.';
                    feedbackElement.className = 'text-danger'; // Add error styling
                });
        }

        let currentRequestId = null;
        let currentDocumentType = null;

        function showApproveModal(id, type) {
            currentRequestId = id;
            currentDocumentType = type;

            // Generate the Control Number
            const controlNo = generateControlNo(type, id);

            // Fetch requester details (Full Name, Contact Number, Email)
            fetch(`get_requester_details.php?id=${id}&type=${type}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('approveDetails').innerHTML = `
                            <li><strong>Control No.:</strong> ${controlNo}</li>
                            <li><strong>Document Type:</strong> ${type}</li>
                        `;
                        document.getElementById('requesterDetails').innerHTML = `
                            <li><strong>Full Name:</strong> ${data.full_name}</li>
                            <li><strong>Contact Number:</strong> ${data.contact_number}</li>
                            <li><strong>Email:</strong> ${data.email}</li>
                        `;
                    } else {
                        console.error('Failed to fetch requester details:', data.error);
                    }
                })
                .catch(error => console.error('Error fetching requester details:', error));

            // Show the modal
            const approveModal = new bootstrap.Modal(document.getElementById('approveModal'));
            approveModal.show();
        }

        function showDeclineModal(id, type) {
            currentRequestId = id;
            currentDocumentType = type;

            // Generate the Control Number
            const controlNo = generateControlNo(type, id);

            // Fetch requester details (Full Name, Contact Number, Email)
            fetch(`get_requester_details.php?id=${id}&type=${type}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('declineDetails').innerHTML = `
                            <li><strong>Control No.:</strong> ${controlNo}</li>
                            <li><strong>Document Type:</strong> ${type}</li>
                        `;
                        document.getElementById('requesterDetailsDecline').innerHTML = `
                            <li><strong>Full Name:</strong> ${data.full_name}</li>
                            <li><strong>Contact Number:</strong> ${data.contact_number}</li>
                            <li><strong>Email:</strong> ${data.email}</li>
                        `;
                    } else {
                        console.error('Failed to fetch requester details:', data.error);
                    }
                })
                .catch(error => console.error('Error fetching requester details:', error));

            // Show the modal
            const declineModal = new bootstrap.Modal(document.getElementById('declineModal'));
            declineModal.show();
        }

        function toggleDetails(id) {
            const detailsBox = document.getElementById(`details-${id}`);
            detailsBox.style.display = detailsBox.style.display === 'none' ? 'block' : 'none';
        }

        document.getElementById('confirmApprove').addEventListener('click', () => {
            const confirmation = document.getElementById('approveConfirmation').value;
            const pickupSchedule = document.getElementById('pickupSchedule').value;
            const feedback = document.getElementById('approveFeedback');

            if (confirmation === 'I APPROVE' && pickupSchedule) {
                updateStatus(currentRequestId, 'approved', currentDocumentType, null, feedback, 'approveModal', pickupSchedule);
            } else if (!pickupSchedule) {
                feedback.textContent = 'You must select a pickup date and time.';
                feedback.className = 'text-danger'; // Add error styling
            } else {
                feedback.textContent = 'You must type "I APPROVE" to confirm.';
                feedback.className = 'text-danger'; // Add error styling
            }
        });

        document.getElementById('confirmDecline').addEventListener('click', () => {
            const reason = document.getElementById('declineReason').value.trim();
            const feedback = document.getElementById('declineFeedback');
            if (reason) {
                updateStatus(currentRequestId, 'rejected', currentDocumentType, reason, feedback, 'declineModal');
            } else {
                feedback.textContent = 'You must provide a reason for declining.';
                feedback.className = 'text-danger'; // Add error styling
            }
        });

    </script>

    <!-- Popper.js (required for Bootstrap) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>