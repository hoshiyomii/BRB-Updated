<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch all document requests
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'created_at';
$sql = "SELECT document_requests.*, users.first_name, users.last_name, users.email FROM document_requests JOIN users ON document_requests.user_id = users.id ORDER BY $sort_by DESC";
$result = $conn->query($sql);

// Check for errors
if (!$result) {
    die("Error fetching document requests: " . $conn->error);
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
            <label for="sort_by">Sort by:</label>
            <select id="sort_by" class="form-control" onchange="sortRequests()">
                <option value="created_at" <?php if ($sort_by == 'created_at') echo 'selected'; ?>>Date Submitted</option>
                <option value="document_type" <?php if ($sort_by == 'document_type') echo 'selected'; ?>>Document Type</option>
            </select>
        </div>
        <table class="table table-bordered table-striped mt-3">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Document Type</th>
                    <th>Details</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['document_type']); ?></td>
                        <td>
                            <?php
                            $details = [];
                            if ($row['contractor']) $details[] = 'Contractor: ' . htmlspecialchars($row['contractor']);
                            if ($row['schedule']) $details[] = 'Schedule: ' . htmlspecialchars($row['schedule']);
                            if ($row['type']) $details[] = 'Type: ' . htmlspecialchars($row['type']);
                            if ($row['utility_type']) $details[] = 'Utility Type: ' . htmlspecialchars($row['utility_type']);
                            if ($row['company']) $details[] = 'Company: ' . htmlspecialchars($row['company']);
                            if ($row['id_image']) $details[] = 'ID Image: <a href="' . htmlspecialchars($row['id_image']) . '">View</a>';
                            if ($row['occupancy']) $details[] = 'Occupancy: ' . htmlspecialchars($row['occupancy']);
                            if ($row['monthly_salary']) $details[] = 'Monthly Salary: ' . htmlspecialchars($row['monthly_salary']);
                            if ($row['clearance_image']) $details[] = 'Clearance Image: <a href="' . htmlspecialchars($row['clearance_image']) . '">View</a>';
                            if ($row['ownership_type']) $details[] = 'Ownership Type: ' . htmlspecialchars($row['ownership_type']);
                            if ($row['business_name']) $details[] = 'Business Name: ' . htmlspecialchars($row['business_name']);
                            if ($row['business_type']) $details[] = 'Business Type: ' . htmlspecialchars($row['business_type']);
                            echo implode('<br>', $details);
                            ?>
                        </td>
                        <td><?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="admin_dashboard.php" class="btn btn-primary">Back to Admin Dashboard</a>
    </div>

    <script>
        function sortRequests() {
            var sort_by = document.getElementById('sort_by').value;
            window.location.href = 'view_document_requests.php?sort_by=' + sort_by;
        }
    </script>
</body>
</html>