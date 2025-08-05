<?php
session_start();
if (isset($_SESSION['admin'])) {
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/index_head.php'; ?>
<link rel="stylesheet" href="user_login.css">
<body>
<?php include 'includes/index_header.php'; ?>

<!-- Hero-style login background -->
<div class="container-fluid hero-landing p-0">
    <div class="row g-0 align-items-center min-vh-100 justify-content-center">
        <div class="col-lg-5 col-md-7 col-11 mx-auto">
            <div class="login-card bg-white rounded-4 shadow p-5 my-5">
                <h2 class="text-center mb-4">Admin Log-in</h2>
                <form id="loginForm" action="process_admin_login.php" method="POST">
                    <div class="mb-3">
                        <input type="text" class="form-control input-dark border-0" placeholder="Username" style="height: 55px;" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control input-dark border-0" placeholder="Password" style="height: 55px;" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary w-100 py-3" type="submit">Log-in</button>
                    </div>
                    <p id="errorMsg" style="color: red;"></p>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="login.js"></script>
</body>
</html>
