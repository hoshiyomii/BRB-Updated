<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // User is already logged in, redirect to dashboard or homepage
    header("Location: dashboard.php"); // or index.php
    exit();
}
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, email, is_verified FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['is_verified']) {
            // Store user details in the session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email']; // 

            echo "success";
        } else {
            // User is not verified, show the modal
            echo "unverified";
        }
    } else {
        echo "invalid_credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/head.php'; ?>
<link rel="stylesheet" href="user_login.css">
<body>

<?php include 'includes/index_header.php'; ?>
    <!-- Navbar End -->


    <!-- Page Header End -->
    <!-- Hero-style login background -->
    <div class="container-fluid hero-landing p-0" style="position:relative; min-height:100vh;">
        <div class="row g-0 align-items-center min-vh-100 justify-content-center">
            <div class="col-lg-5 col-md-7 col-11 mx-auto">
                <div class="login-card bg-white rounded-4 shadow p-5 my-5">
                    <h2 class="text-center mb-4">Log-in</h2>
                    <form id="loginForm" action="process_user_login.php" method="POST">
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
                        <div class="text-center">
                            <a href="register_user.php">Don't have an account? Create here.</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Unverified User Modal -->
<div class="modal fade" id="unverifiedUserModal" tabindex="-1" aria-labelledby="unverifiedUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="unverifiedUserModalLabel">Email Verification Required</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>An email has already been sent to <strong id="userEmail"></strong>. Please check your inbox and click the verification link to activate your account.</p>
        <p>If you did not receive the email, you can resend the verification token.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="resendVerificationBtn">Resend Verification Email</button>
      </div>
    </div>
  </div>
</div>

    <script src="user_login.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const resendVerificationBtn = document.getElementById('resendVerificationBtn');

    // Show the modal if the user is unverified
    const isUnverified = <?php echo isset($_GET['unverified']) && $_GET['unverified'] === 'true' ? 'true' : 'false'; ?>;
    console.log('Is Unverified:', isUnverified); // Debugging log
    if (isUnverified) {
        const userEmail = '<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>';
        console.log('User Email:', userEmail); // Debugging log
        document.getElementById('userEmail').textContent = userEmail;

        // Trigger the modal
        const unverifiedUserModal = new bootstrap.Modal(document.getElementById('unverifiedUserModal'));
        unverifiedUserModal.show();
    }

    // Resend verification token
    resendVerificationBtn.addEventListener('click', function () {
        const email = '<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>';
        console.log('Resending verification for email:', email); // Debugging log
        fetch('resend_verification.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Verification email has been resent successfully.');
            } else {
                alert('Failed to resend verification email. Please try again later.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred. Please try again later.');
        });
    });
});
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>
