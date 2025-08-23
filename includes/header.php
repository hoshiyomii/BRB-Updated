<!-- header.php -->
<!-- Topbar Start -->
<!-- Topbar End -->

<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0 wow fadeIn" data-wow-delay="0.1s">
    <a href="index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <img src="img/new_logo.png" alt="BRB Logo" style="height:48px; width:auto; margin-right:12px;">
        <h1 class="m-0 text-primary">Blue Ridge B</h1>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="index.php" class="nav-item nav-link">Home</a>
            <a href="about.php" class="nav-item nav-link">About</a>
            <a href="service.php" class="nav-item nav-link">Service</a>
            <a href="contact.php" class="nav-item nav-link">Contact</a>
            <?php if (isset($_SESSION["username"])): ?>
                <a href="dashboard.php" class="nav-item nav-link">Dashboard</a>
                <button type="button"
                    class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block"
                    data-bs-toggle="modal"
                    data-bs-target="#logoutModal">
                    Log-Out<i class="fa fa-arrow-right ms-3"></i>
                </button>
                <!-- Mobile logout button -->
                <div class="d-block d-lg-none">
                    <button type="button"
                        class="btn btn-primary w-100 py-3 mt-2"
                        data-bs-toggle="modal"
                        data-bs-target="#logoutModal">
                        Log-Out<i class="fa fa-arrow-right ms-3"></i>
                    </button>
                </div>
            <?php else: ?>
                <a href="user_login.php" class="nav-item nav-link">Login</a>
                <a href="register_user.php" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block">
                    Register<i class="fa fa-arrow-right ms-3"></i>
                </a>
                <!-- Mobile register button -->
                <div class="d-block d-lg-none">
                    <a href="register_user.php" class="btn btn-primary w-100 py-3 mt-2">
                        Register<i class="fa fa-arrow-right ms-3"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
.navbar {
    border-bottom: 4px solid #0a1e3c !important;
    box-shadow: 0 2px 8px rgba(10,30,60,0.04) !important;
}
</style>

<script>
// Ensure navbar toggle works properly
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap collapse for navbar
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('#navbarCollapse');
    
    if (navbarToggler && navbarCollapse) {
        navbarToggler.addEventListener('click', function() {
            console.log('Navbar toggler clicked'); // Debug log
            const isExpanded = navbarCollapse.classList.contains('show');
            
            if (isExpanded) {
                navbarCollapse.classList.remove('show');
                navbarToggler.setAttribute('aria-expanded', 'false');
            } else {
                navbarCollapse.classList.add('show');
                navbarToggler.setAttribute('aria-expanded', 'true');
            }
            
            // Force visibility
            const navItems = navbarCollapse.querySelectorAll('.nav-item, .nav-link');
            navItems.forEach(item => {
                if (navbarCollapse.classList.contains('show')) {
                    item.style.display = 'block';
                    item.style.visibility = 'visible';
                    item.style.opacity = '1';
                } else {
                    item.style.display = '';
                    item.style.visibility = '';
                    item.style.opacity = '';
                }
            });
        });
        
        // Close navbar when clicking outside
        document.addEventListener('click', function(event) {
            const isClickInsideNav = navbarCollapse.contains(event.target) || navbarToggler.contains(event.target);
            
            if (!isClickInsideNav && navbarCollapse.classList.contains('show')) {
                navbarCollapse.classList.remove('show');
                navbarToggler.setAttribute('aria-expanded', 'false');
                
                // Reset visibility
                const navItems = navbarCollapse.querySelectorAll('.nav-item, .nav-link');
                navItems.forEach(item => {
                    item.style.display = '';
                    item.style.visibility = '';
                    item.style.opacity = '';
                });
            }
        });
    }
});
</script>    
