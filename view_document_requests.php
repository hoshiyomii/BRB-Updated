<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

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
                    rc.homeowner_contact, 
                    rc.contractor_name, 
                    rc.contractor_contact, 
                    rc.activity_nature, 
                    rc.construction_address,
                    rc.created_at 
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
                    wp.created_at 
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
                    cr.created_at 
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
                    ci.created_at 
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
                    nbp.created_at 
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
                    cmc.created_at 
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
                        <th>ID</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Homeowner Contact</th>
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
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['homeowner_contact']); ?></td>
                            <td><?php echo htmlspecialchars($row['contractor_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['contractor_contact']); ?></td>
                            <td><?php echo htmlspecialchars($row['activity_nature']); ?></td>
                            <td><?php echo htmlspecialchars($row['construction_address']); ?></td>
                            <td><?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="printDocument(<?php echo $row['id']; ?>, 'repair_and_construction')">Print</button>
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
                        <th>ID</th>
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
                        <th>Action</th> <!-- New column for Print button -->
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
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
                                <button class="btn btn-primary btn-sm" onclick="printDocument(<?php echo $row['id']; ?>, 'work_permit_utilities')">Print</button>
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
                <th>ID</th>
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
                <tr>
                    <td><?php echo $row['id']; ?></td>
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
                        <button class="btn btn-primary btn-sm" onclick="printDocument(<?php echo $row['id']; ?>, 'certificate_of_residency')">Print</button>
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
                <th>ID</th>
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
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                    <td><?php echo htmlspecialchars($row['occupancy']); ?></td>
                    <td><?php echo htmlspecialchars($row['monthly_income']); ?></td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="printDocument(<?php echo $row['id']; ?>, 'certificate_of_indigency')">Print</button>
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
                <th>ID</th>
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
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['owner']); ?></td>
                    <td><?php echo htmlspecialchars($row['co_owner']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td><?php echo htmlspecialchars($row['business_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['nature_of_business']); ?></td>
                    <td><?php echo htmlspecialchars($row['business_type']); ?></td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="printDocument(<?php echo $row['id']; ?>, 'new_business_permit')">Print</button>
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
                <th>ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Construction Schedule</th>
                <th>Contractor</th>
                <th>Construction Address</th>
                <th>Infrastructures to be Built</th>
                <th>Created At</th>
                <th>Action</th> <!-- New column for Print button -->
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($row['construction_schedule'])); ?></td>
                    <td><?php echo htmlspecialchars($row['contractor']); ?></td>
                    <td><?php echo htmlspecialchars($row['construction_address']); ?></td>
                    <td><?php echo htmlspecialchars($row['infrastructures']); ?></td>
                    <td><?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="printDocument(<?php echo $row['id']; ?>, 'clearance_major_construction')">Print</button>
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
    </script>
</body>
</html>