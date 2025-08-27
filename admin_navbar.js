document.addEventListener('DOMContentLoaded', function() {
    // Toggle mobile menu
    const navbarToggle = document.querySelector('.admin-navbar-toggle');
    const navbarMenu = document.querySelector('.admin-navbar-menu');
    
    if (navbarToggle && navbarMenu) {
        navbarToggle.addEventListener('click', function() {
            navbarMenu.classList.toggle('show');
            
            // Toggle between bars and X icon
            const icon = this.querySelector('i');
            if (icon) {
                if (navbarMenu.classList.contains('show')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }
    
    // Handle dropdown menus on mobile
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            // Only handle click for mobile view
            if (window.innerWidth <= 991) {
                e.preventDefault();
                const dropdownMenu = this.nextElementSibling;
                dropdownMenu.classList.toggle('show');
            }
        });
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!navbarMenu.contains(e.target) && !navbarToggle.contains(e.target) && navbarMenu.classList.contains('show')) {
            navbarMenu.classList.remove('show');
            
            // Reset icon
            const icon = navbarToggle.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
    });
    
    // Close mobile menu when screen size changes
    window.addEventListener('resize', function() {
        if (window.innerWidth > 991 && navbarMenu.classList.contains('show')) {
            navbarMenu.classList.remove('show');
            
            // Reset icon
            const icon = navbarToggle.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
    });
});
