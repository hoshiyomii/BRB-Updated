// Add active class to current page
document.addEventListener('DOMContentLoaded', function() {
    // Get current path
    const path = window.location.pathname;
    const page = path.split("/").pop();
    
    // Find all nav links
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Check each link against current page
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === page || (page === '' && href === 'admin_dashboard.php')) {
            link.classList.add('active');
        }
    });
});
