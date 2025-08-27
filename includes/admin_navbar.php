<?php
// Include CSS for the navbar
echo '<link href="admin_navbar.css" rel="stylesheet">';
?>

<nav class="admin-navbar">
    <a href="admin_dashboard.php" class="admin-navbar-brand">
        <img src="img/new_logo.png" alt="BRB Logo" class="admin-navbar-logo">
        <h1 class="admin-navbar-title">
            <span>Blue Ridge B</span>
            <span>Admin</span>
        </h1>
    </a>
    
    <button type="button" class="admin-navbar-toggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <div class="admin-navbar-menu">
        <ul class="admin-navbar-nav">
            <li class="nav-item">
                <a href="admin_dashboard.php" class="nav-link">Announcements</a>
            </li>
            <li class="nav-item">
                <a href="view_registrations.php" class="nav-link">Registrations</a>
            </li>
            <li class="nav-item">
                <a href="resident_list.php" class="nav-link">Residents</a>
            </li>
            <li class="nav-item">
                <a href="view_document_requests.php" class="nav-link">Document Requests</a>
            </li>
            
            <!-- Dropdown for Reservations -->
            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle">
                    Reservations
                </a>
                <div class="dropdown-menu">
                    <a href="view_reservations.php" class="dropdown-item">Sports Venue</a>
                    <a href="view_facilities_reservations.php" class="dropdown-item">Facilities</a>
                    <a href="reservation_calendar.php" class="dropdown-item">Calendar View</a>
                </div>
            </li>
            
            <?php if (isset($_SESSION['admin_level']) && $_SESSION['admin_level'] == '2'): ?>
                <li class="nav-item">
                    <a href="create_admin.php" class="nav-link">Create Admin</a>
                </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a href="admin_profile.php" class="nav-link">Profile</a>
            </li>
            
            <!-- Mobile Logout Button -->
            <li class="nav-item mobile-logout">
                <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    Log-Out <i class="fas fa-sign-out-alt"></i>
                </a>
            </li>
        </ul>
        
        <!-- Desktop Logout Button -->
        <button type="button" class="logout-button" data-bs-toggle="modal" data-bs-target="#logoutModal">
            Log-Out <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</nav>

<!-- Include JS for the navbar -->
<script src="admin_navbar.js"></script>
<script src="admin_active_page.js"></script>
