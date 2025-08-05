<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch admin details
$adminUsername = $_SESSION['admin'];
$query = $conn->prepare("SELECT * FROM admins WHERE username = ?");
$query->bind_param("s", $adminUsername);
$query->execute();
$result = $query->get_result();
$adminData = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['current_password'])) {
    // Handle profile update
    $firstName = $_POST['first_name'];
    $middleName = $_POST['middle_name'];
    $lastName = $_POST['last_name'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_new_password'];

    // Fetch the current hashed password from the database
    $passwordQuery = $conn->prepare("SELECT password FROM admins WHERE username = ?");
    $passwordQuery->bind_param("s", $adminUsername);
    $passwordQuery->execute();
    $passwordResult = $passwordQuery->get_result();
    $adminPasswordData = $passwordResult->fetch_assoc();

    if (!$currentPassword) {
        $errorMessage = "Current password is required!";
    } elseif (!$adminPasswordData || !password_verify($currentPassword, $adminPasswordData['password'])) {
        $errorMessage = "Current password is incorrect!";
    } elseif (!empty($newPassword) || !empty($confirmPassword)) {
        // If new password fields are not empty, validate and update the password
        if ($newPassword !== $confirmPassword) {
            $errorMessage = "New passwords do not match!";
        } else {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        }
    } else {
        // Keep the current password if new password fields are empty
        $hashedPassword = $adminPasswordData['password'];
    }

    if (!isset($errorMessage)) {
        // Generate new username based on last name
        $newUsername = "admin-" . strtolower($lastName);

        // Update admin details
        $updateQuery = $conn->prepare("UPDATE admins SET first_name = ?, middle_name = ?, last_name = ?, username = ?, password = ? WHERE username = ?");
        $updateQuery->bind_param("ssssss", $firstName, $middleName, $lastName, $newUsername, $hashedPassword, $adminUsername);

        if ($updateQuery->execute()) {
            $_SESSION['admin'] = $newUsername; // Update session with new username
            $successMessage = "Profile updated successfully!";
            
            // Redirect to the same page to refresh the displayed data
            header("Location: admin_profile.php?success=1");
            exit();
        } else {
            $errorMessage = "Error updating profile: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/admin_head.php'; ?>
<link href= "admin_dashboard.css" rel="stylesheet">
<body>
<?php include 'includes/admin_navbar.php'; ?>

<div class="container mt-5">
    <h1>Admin Profile</h1>
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert alert-success">Profile updated successfully!</div>
    <?php elseif (isset($errorMessage)): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="row g-3">
            <div class="col-12 col-sm-6">
                <label>Username:</label>
                <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars("admin-" . strtolower($adminData['last_name'])); ?>" readonly>
                <small class="form-text text-muted">
                    The username is automatically generated based on your last name: <strong>admin-[lastname]</strong>. It cannot be edited manually.
                </small>
            </div>
            <div class="col-12 col-sm-6">
                <label>First Name:</label>
                <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($adminData['first_name']); ?>" required>
            </div>
            <div class="col-12 col-sm-6">
                <label>Middle Name:</label>
                <input type="text" class="form-control" name="middle_name" value="<?php echo htmlspecialchars($adminData['middle_name']); ?>">
            </div>
            <div class="col-12 col-sm-6">
                <label>Last Name:</label>
                <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($adminData['last_name']); ?>" required>
            </div>
            <div class="col-12">
                <h4 class="mt-4">Security</h4>
            </div>
            <div class="col-12 col-sm-6">
                <label>New Password:</label>
                <input type="password" class="form-control" name="new_password" minlength="8" maxlength="32" placeholder="Enter new password">
            </div>
            <div class="col-12 col-sm-6">
                <label>Confirm New Password:</label>
                <input type="password" class="form-control" name="confirm_new_password" minlength="8" maxlength="32" placeholder="Re-enter new password">
            </div>
            <div class="col-12 col-sm-6">
                <label>Current Password:</label>
                <input type="password" class="form-control" name="current_password" minlength="8" maxlength="32" placeholder="Enter current password" required>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" type="submit">Save Changes</button>
            </div>
        </div>
    </form>
</div>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center modal-outline modal-glow">
            <div class="modal-header border-0 justify-content-center">
                <!-- Optional: Add a title or leave empty -->
            </div>
            <div class="modal-body">
                <!-- Warning Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#ffc107" class="mb-4" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                </svg>
                <div class="mb-3 fs-5 text-secondary">Are you sure you want to log out?</div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary me-2 px-4" data-bs-dismiss="modal">Cancel</button>
                <a href="logout.php" class="btn btn-danger px-4">Yes, Log-out</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>