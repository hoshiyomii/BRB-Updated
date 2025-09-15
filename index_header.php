<!-- index_header.php -->
<!-- Topbar Start -->
<!-- Topbar End -->

<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top p-0 wow fadeIn navbar-index" data-wow-delay="0.1s">
    <a href="index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <img src="img/new_logo.png" alt="BRB Logo" style="height:48px; width:auto; margin-right:12px;">
        <h1 class="m-0 text-primary">Blue Ridge B</h1>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="mailto:cjlimpin23@gmail.com" class="nav-item nav-link" target="_blank">
                <i class="fas fa-envelope"></i>
            </a>
            <a href="index.php" class="nav-item nav-link">
                <i class="fas fa-home"></i>
            </a>
            <a href="about.php" class="nav-item nav-link">About</a>
            <a href="service.php" class="nav-item nav-link">Service</a>
            <a href="announcements.php" class="nav-item nav-link">Announcements</a>
            <?php if (isset($_SESSION["username"])): ?>
                <span class="nav-item nav-link" style="font-weight:bold; color:#0d6efd;">
                    Welcome,
                    <a href="dashboard.php" style="font-weight:bold; color:#0d6efd; display:inline;">
                        <?php echo isset($_SESSION["name"]) ? htmlspecialchars($_SESSION["name"]) : htmlspecialchars($_SESSION["username"]); ?>
                    </a>
                </span>
                <button type="button"
                    class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block"
                    data-bs-toggle="modal"
                    data-bs-target="#logoutModal">
                    Log-Out<i class="fa fa-arrow-right ms-3"></i>
                </button>
            <?php else: ?>
                <a href="user_login.php" class="nav-item nav-link">Login</a>
                <a href="register_user.php" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block">
                    Register<i class="fa fa-arrow-right ms-3"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
