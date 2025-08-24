<?php
session_start();
include 'db.php';

$message = "";
$icon = "";
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE verify_token=? AND is_verified=0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        // Mark as verified
        $stmt2 = $conn->prepare("UPDATE users SET is_verified=1, verify_token=NULL WHERE verify_token=?");
        $stmt2->bind_param("s", $token);
        $stmt2->execute();
        $message = "Your email has been verified! You can now log in.";
        $icon = "fa-check-circle text-success";
    } else {
        $message = "Invalid or expired verification link.";
        $icon = "fa-times-circle text-danger";
    }
} else {
    $message = "No verification token provided.";
    $icon = "fa-exclamation-circle text-warning";
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; ?>
<link rel="stylesheet" href="user_login.css">

<body>
    <?php include 'includes/index_header.php'; ?>

    <!-- Hero Section -->
    <div class="container-fluid hero-landing p-0" style="position:relative; min-height:100vh;">
        <div class="row g-0 align-items-center min-vh-100 justify-content-center">
            <div class="col-lg-5 col-md-7 col-11 mx-auto">
                <div class="login-card bg-white rounded-4 shadow p-5 my-5 text-center">
                    <i class="fa <?php echo $icon; ?> fa-4x mb-4"></i>
                    <h2 class="text-center mb-4">Verification Status</h2>
                    <p class="lead"><?php echo htmlspecialchars($message); ?></p>
                    <a href="login.php" class="btn btn-primary w-100 py-3 mt-4">To Log-in</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>