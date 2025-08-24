<nav class="navbar navbar-expand-lg navbar-light sticky-top p-0 bg-primary text-white">
    <a href="admin_dashboard.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <img src="img/new_logo.png" alt="BRB Logo" style="height:48px; width:auto; margin-right:12px;">
        <h1 class="m-0">
            <span style="color: white;">Blue Ridge B</span>
            <span>Admin</span>
        </h1>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="user_list.php" class="nav-item nav-link text-white">Resident List</a>
            <a href="admin_dashboard.php" class="nav-item nav-link text-white">Announcements</a>
            <a href="view_registrations.php" class="nav-item nav-link text-white">Registrations</a>
            <a href="view_document_requests.php" class="nav-item nav-link text-white">Document Requests</a>

            <!-- Dropdown for Reservations -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle text-white" id="reservationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Reservations
                </a>
                <ul class="dropdown-menu" aria-labelledby="reservationsDropdown">
                    <li><a href="view_reservations.php" class="dropdown-item">Sports Venue</a></li>
                    <li><a href="view_facilities_reservations.php" class="dropdown-item">Facilities</a></li>
                </ul>
            </div>

            <?php if (isset($_SESSION['admin_level']) && $_SESSION['admin_level'] == '2'): ?>
                <a href="create_admin.php" class="nav-item nav-link text-white">Create Admin</a>
            <?php endif; ?>
            <a href="admin_profile.php" class="nav-item nav-link text-white">Profile</a>
            <button type="button"
                class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block"
                data-bs-toggle="modal"
                data-bs-target="#logoutModal">
                Log-Out<i class="fa fa-arrow-right ms-3"></i>
            </button>
        </div>
    </div>
</nav>
