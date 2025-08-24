
<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'db_connection.php';
$sql = "SELECT id, username, first_name, middle_name, last_name, gender, phone_number, email, birthdate, street, lot_block, blood_type, house_number, date_registered, is_verified FROM users ORDER BY date_registered DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'includes/admin_head.php'; ?>
    <link href="admin_dashboard.css" rel="stylesheet">
<body>
    <?php include 'includes/admin_navbar.php'; ?>

    <main class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Barangay User Accounts</h2>
        </div>
        <div class="table-responsive animate__animated animate__fadeIn">
            <table class="table table-bordered table-hover align-middle bg-white shadow-sm">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Birthdate</th>
                        <th>Street</th>
                        <th>Lot/Block</th>
                        <th>Blood Type</th>
                        <th>House #</th>
                        <th>Date Registered</th>
                        <th>Verified?</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['first_name']) ?></td>
                            <td><?= htmlspecialchars($row['middle_name']) ?></td>
                            <td><?= htmlspecialchars($row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['gender']) ?></td>
                            <td><?= htmlspecialchars($row['phone_number']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['birthdate']) ?></td>
                            <td><?= htmlspecialchars($row['street']) ?></td>
                            <td><?= htmlspecialchars($row['lot_block']) ?></td>
                            <td><?= htmlspecialchars($row['blood_type']) ?></td>
                            <td><?= htmlspecialchars($row['house_number']) ?></td>
                            <td><?= htmlspecialchars($row['date_registered']) ?></td>
                            <td>
                                <?php if ($row['is_verified']): ?>
                                    <span class="badge bg-success">Yes</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">No</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="15" class="text-center">No users found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
