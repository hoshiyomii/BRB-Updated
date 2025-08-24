<?php
// edit_facility_limits.php
include 'db_connection.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['limits'] as $facility => $max_quantity) {
        $facility = mysqli_real_escape_string($conn, $facility);
        $max_quantity = (int)$max_quantity;
        $sql = "UPDATE facility_limits SET max_quantity = $max_quantity WHERE facility_name = '$facility'";
        mysqli_query($conn, $sql);
    }
    $message = "Facility limits updated successfully.";
}

// Fetch current limits
$result = mysqli_query($conn, "SELECT * FROM facility_limits");
$limits = [];
while ($row = mysqli_fetch_assoc($result)) {
    $limits[$row['facility_name']] = $row['max_quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Facility Limits</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Edit Facility Limits</h2>
    <?php if (!empty($message)) echo '<p style="color:green;">' . $message . '</p>'; ?>
    <form method="post">
        <table>
            <tr><th>Facility</th><th>Max Quantity</th></tr>
            <?php foreach ($limits as $facility => $max): ?>
            <tr>
                <td><?php echo htmlspecialchars($facility); ?></td>
                <td><input type="number" name="limits[<?php echo htmlspecialchars($facility); ?>]" value="<?php echo $max; ?>" min="0" required></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <button type="submit">Save Changes</button>
    </form>
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
