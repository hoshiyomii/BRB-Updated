<?php
session_start();
include 'db.php';

// Archive announcements where the current date exceeds active_until
$updateQuery = "UPDATE announcements 
                SET is_active = 0 
                WHERE is_active = 1 AND active_until IS NOT NULL AND active_until < NOW()";
$conn->query($updateQuery);

// Fetch announcements with image, genre, and created_at support
$genreFilter = isset($_GET['genre']) ? $_GET['genre'] : '';
$sql = "SELECT id, title, content, genre, image_path, created_at, active_until 
        FROM announcements 
        WHERE is_active = 1";

// Add a condition for genre filtering if a genre is selected
if (!empty($genreFilter)) {
    $sql .= " AND genre = ?";
}

// Append the ORDER BY clause
$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

// Bind the genre parameter if a filter is applied
if (!empty($genreFilter)) {
    $stmt->bind_param("s", $genreFilter);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/index_head.php'; ?>
<link href="index.css" rel="stylesheet">
<link href="about-section.css" rel="stylesheet">

<body>
    <?php include 'includes/index_header.php'; ?>
    
    <!-- Main content container -->
    <div class="main-container">

    <!-- Hero Section -->
    <div class="content-section" id="hero">
        <div class="hero-container">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="hero-text-wrapper">
                    <div class="hero-text">
                        <h1>The <span class="highlight">community</span> you've been looking for.</h1>
                        <p class="hero-subtitle">A society built to accommodate for everyone and their needs.</p>
                        <p class="hero-description">
                            Blue Ridge B is a welcoming neighborhood in Quezon City, Metro Manila. Part of the Blue Ridge subdivision, it offers a peaceful residential area with easy access to major roads, schools, and businesses.
                        </p>
                        <div class="hero-buttons">
                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <a href="register_user.php" class="hero-btn primary">Register Now</a>
                            <?php endif; ?>
                            <a href="service.php" class="hero-btn secondary">Services</a>
                        </div>
                        <div class="hero-features">
                            <span class="hero-feature"><i class="fa fa-users"></i>Safe Community</span>
                            <span class="hero-feature"><i class="fa fa-cogs"></i>Active Services</span>
                            <span class="hero-feature"><i class="fa fa-smile"></i>Friendly Staff</span>
                        </div>
                        <div class="community-info">
                            <p>Join over 1,700+ residents that is part of our ever-growing community</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="scroll-indicator">
                <a href="#announcements">
                    <i class="fa fa-chevron-down animate__animated animate__bounce animate__infinite"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="content-section" id="announcements">
        <div class="announcements-container">
            <h1 class="announcements-heading">ANNOUNCEMENTS</h1>
            
            <div class="announcements-layout">
                <!-- Left Side - Dynamic Announcement Text -->
                <div class="announcement-text">
                    <?php
                    // Reset result pointer and get the first announcement for text display
                    $result->data_seek(0);
                    $featuredAnnouncement = $result->fetch_assoc();
                    if ($featuredAnnouncement): 
                    ?>
                    <h2 class="announcement-title"><?php echo htmlspecialchars($featuredAnnouncement['title']); ?></h2>
                    
                    <div class="announcement-tags">
                        <?php if(!empty($featuredAnnouncement['genre'])): ?>
                            <span class="announcement-tag"><?php echo htmlspecialchars($featuredAnnouncement['genre']); ?></span>
                        <?php endif; ?>
                        <?php 
                        // Display creation date as a tag
                        if(!empty($featuredAnnouncement['created_at'])): 
                            $date = new DateTime($featuredAnnouncement['created_at']);
                        ?>
                            <span class="announcement-tag"><?php echo $date->format('M d, Y'); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <p class="announcement-body">
                        <?php echo nl2br(htmlspecialchars($featuredAnnouncement['content'])); ?>
                    </p>
                    
                    <a href="announcements.php" class="view-all-btn">VIEW ALL</a>
                    <?php else: ?>
                    <h2 class="announcement-title">No Announcements</h2>
                    <p class="announcement-body">There are no active announcements at this time.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Right Side - Image Carousel -->
                <div class="carousel-container">
                    <div class="announcement-carousel">
                        <?php
                        // Reset result pointer for carousel
                        $result->data_seek(0);
                        $count = 0;
                        
                        while ($row = $result->fetch_assoc()):
                            if ($count >= 7) break; // Limit to 7 announcements
                            $active = ($count === 0) ? 'active' : '';
                            $bg = !empty($row['image_path']) ? htmlspecialchars($row['image_path']) : 'img/placeholder-image.jpg';
                        ?>
                        <div class="carousel-slide <?php echo $active; ?>" data-index="<?php echo $count; ?>" 
                             data-title="<?php echo htmlspecialchars($row['title']); ?>"
                             data-content="<?php echo htmlspecialchars($row['content']); ?>"
                             data-genre="<?php echo htmlspecialchars($row['genre']); ?>"
                             data-date="<?php echo htmlspecialchars($row['created_at']); ?>">
                            <img src="<?php echo $bg; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        </div>
                        <?php
                            $count++;
                        endwhile;
                        ?>
                    </div>
                    
                    <div class="carousel-navigation">
                        <button type="button" class="carousel-prev" aria-label="Previous slide"><i class="fa fa-chevron-left"></i></button>
                        <div class="carousel-dots">
                            <?php for($i = 0; $i < $count; $i++): ?>
                            <button type="button" class="carousel-dot <?php echo $i === 0 ? 'active' : ''; ?>" 
                                    data-index="<?php echo $i; ?>"
                                    aria-label="Go to slide <?php echo $i + 1; ?>"></button>
                            <?php endfor; ?>
                        </div>
                        <button type="button" class="carousel-next" aria-label="Next slide"><i class="fa fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
        <!-- About Start -->
    <div class="content-section" id="about">
        <div class="about-container">
            <div class="about-overlay"></div>
            <div class="about-content">
                <div class="about-text-wrapper">
                    <div class="about-heading">
                        <span class="about-badge">About Us</span>
                        <h2>Know About Us!</h2>
                    </div>
                    <div class="about-description">
                        <p>Blue Ridge B is a welcoming neighborhood in Quezon City, Metro Manila. Part of the Blue Ridge subdivision, it offers a peaceful residential area with easy access to major roads, schools, and businesses. Known for its strong sense of community, Blue Ridge B is an ideal place to live, work, and enjoy a convenient lifestyle in the heart of the city.</p>
                        <ul class="about-features">
                            <li><i class="far fa-check-circle"></i>Quality health care</li>
                            <li><i class="far fa-check-circle"></i>Quality Services</li>
                            <li><i class="far fa-check-circle"></i>Strong Community</li>
                            <li><i class="far fa-check-circle"></i>Prime Location</li>
                        </ul>
                        <a class="about-btn" href="about.php">Read More</a>
                    </div>
                </div>
                <div class="about-image-wrapper">
                    <div class="about-image-container">
                        <img class="about-image-main" src="img/about-1.png" alt="Blue Ridge B Community">
                        <img class="about-image-secondary" src="img/about-2.png" alt="Community Services">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

        <!-- Service Start -->
    <div class="content-section" id="services">
        <div class="container-xxl py-5">
            <div class="container">
                <!-- Documents Section -->
                <div class="text-center mx-auto mb-5" style="max-width: 600px;">
                    <p class="d-inline-block border rounded-pill py-1 px-4">Services</p>
                    <h1>Available Documents</h1>
                </div>
                <div class="row g-4">
                    <!-- Repair and Construction -->
                    <div class="col-lg-4 col-md-6">
                        <div class="service-item bg-light rounded h-100 p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 50px; height: 50px;">
                                <i class="fa fa-tools text-primary fs-5"></i>
                            </div>
                            <h4 class="mb-2" style="font-size: 18px;">Repair and Construction</h4>
                            <p class="mb-3" style="font-size: 14px;">Assistance with repair and construction permits for residential and commercial properties.</p>
                            <a class="view-popup" href="view_document.php?type=repair_and_construction" style="font-size: 16px; padding: 12px 25px; background-color: #007bff; color: white; border-radius: 30px; font-weight: bold; text-decoration: none;">View</a>
                        </div>
                    </div>
                    <!-- Work Permit for Utilities -->
                    <div class="col-lg-4 col-md-6">
                        <div class="service-item bg-light rounded h-100 p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 50px; height: 50px;">
                                <i class="fa fa-file-alt text-primary fs-5"></i>
                            </div>
                            <h4 class="mb-2" style="font-size: 18px;">Work Permit for Utilities</h4>
                            <p class="mb-3" style="font-size: 14px;">Facilitating permits for utility installations such as water, electricity, and internet services.</p>
                            <a class="view-popup" href="view_document.php?type=work_permit_utilities" style="font-size: 16px; padding: 12px 25px; background-color: #007bff; color: white; border-radius: 30px; font-weight: bold; text-decoration: none;">View</a>
                        </div>
                    </div>
                    <!-- Certificate of Residency -->
                    <div class="col-lg-4 col-md-6">
                        <div class="service-item bg-light rounded h-100 p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 50px; height: 50px;">
                                <i class="fa fa-home text-primary fs-5"></i>
                            </div>
                            <h4 class="mb-2" style="font-size: 18px;">Certificate of Residency</h4>
                            <p class="mb-3" style="font-size: 14px;">Official document verifying residency for legal, employment, or educational purposes.</p>
                            <a class="view-popup" href="view_document.php?type=certificate_of_residency" style="font-size: 16px; padding: 12px 25px; background-color: #007bff; color: white; border-radius: 30px; font-weight: bold; text-decoration: none;">View</a>
                        </div>
                    </div>
                    <!-- Certificate of Indigency -->
                    <div class="col-lg-4 col-md-6">
                        <div class="service-item bg-light rounded h-100 p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 50px; height: 50px;">
                                <i class="fa fa-hand-holding-heart text-primary fs-5"></i>
                            </div>
                            <h4 class="mb-2" style="font-size: 18px;">Certificate of Indigency</h4>
                            <p class="mb-3" style="font-size: 14px;">Document certifying financial hardship for access to government assistance programs.</p>
                            <a class="view-popup" href="view_document.php?type=certificate_of_indigency" style="font-size: 16px; padding: 12px 25px; background-color: #007bff; color: white; border-radius: 30px; font-weight: bold; text-decoration: none;">View</a>
                        </div>
                    </div>
                    <!-- New Business Permit -->
                    <div class="col-lg-4 col-md-6">
                        <div class="service-item bg-light rounded h-100 p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 50px; height: 50px;">
                                <i class="fa fa-briefcase text-primary fs-5"></i>
                            </div>
                            <h4 class="mb-2" style="font-size: 18px;">New Business Permit</h4>
                            <p class="mb-3" style="font-size: 14px;">Assistance in acquiring permits for starting businesses across various industries.</p>
                            <a class="view-popup" href="view_document.php?type=new_business_permit" style="font-size: 16px; padding: 12px 25px; background-color: #007bff; color: white; border-radius: 30px; font-weight: bold; text-decoration: none;">View</a>
                        </div>
                    </div>
                    <!-- Clearance for Major Construction -->
                    <div class="col-lg-4 col-md-6">
                        <div class="service-item bg-light rounded h-100 p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 50px; height: 50px;">
                                <i class="fa fa-building text-primary fs-5"></i>
                            </div>
                            <h4 class="mb-2" style="font-size: 18px;">Clearance for Major Construction</h4>
                            <p class="mb-3" style="font-size: 14px;">Providing clearances for large-scale construction projects, including compliance with local regulations.</p>
                            <a class="view-popup" href="view_document.php?type=clearance_major_construction" style="font-size: 16px; padding: 12px 25px; background-color: #007bff; color: white; border-radius: 30px; font-weight: bold; text-decoration: none;">View</a>
                        </div>
                    </div>
                </div>

                <!-- Reservations Section -->
                <div class="text-center mx-auto mt-5 mb-5" style="max-width: 600px;">
                    <p class="d-inline-block border rounded-pill py-1 px-4">Services</p>
                    <h1>Available Reservations</h1>
                </div>
                <div class="row g-4">
                    <!-- Sports Venue -->
                    <div class="col-lg-4 col-md-6">
                        <div class="service-item bg-light rounded h-100 p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 50px; height: 50px;">
                                <i class="fa fa-calendar-alt text-primary fs-5"></i>
                            </div>
                            <h4 class="mb-2" style="font-size: 18px;">Sports Venue</h4>
                            <p class="mb-3" style="font-size: 14px;">Reserve sports venues for activities such as basketball, volleyball, and other events.</p>
                            <a class="view-popup" href="reservation_form.php" style="font-size: 16px; padding: 12px 25px; background-color: #007bff; color: white; border-radius: 30px; font-weight: bold; text-decoration: none;">View</a>
                        </div>
                    </div>
                    <!-- Facilities Reservation -->
                    <div class="col-lg-4 col-md-6">
                        <div class="service-item bg-light rounded h-100 p-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 50px; height: 50px;">
                                <i class="fa fa-building text-primary fs-5"></i>
                            </div>
                            <h4 class="mb-2" style="font-size: 18px;">Facilities Reservation</h4>
                            <p class="mb-3" style="font-size: 14px;">Reserve facilities like the Multi-Purpose Hall or Community Center for events such as weddings, seminars, and gatherings.</p>
                            <a class="view-popup" href="facilities_reservation_form.php" style="font-size: 16px; padding: 12px 25px; background-color: #007bff; color: white; border-radius: 30px; font-weight: bold; text-decoration: none;">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-section wow fadeIn" id="footer-section">
        <?php include 'includes/footer.php'; ?>
    </div>

</div>

<div class="custom-modal-bg"></div>
<?php include 'includes/logout_modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/wow/wow.min.js"></script>
    <script src="announcement-carousel.js"></script>
    
    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    const headerOffset = 80;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: "smooth"
                    });
                }
            });
        });
    </script><script>
    new WOW().init();
</script>

<script>
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});
</script>

<script>
// Direct event binding for carousel buttons when document is loaded
document.addEventListener('DOMContentLoaded', function() {
    const nextBtn = document.querySelector('.carousel-next');
    const prevBtn = document.querySelector('.carousel-prev');
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            console.log('Next button clicked from inline script');
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            console.log('Prev button clicked from inline script');
        });
    }
    
    // Direct keyboard controls
    document.addEventListener('keydown', function(e) {
        if (e.key === "ArrowLeft") {
            console.log("Left arrow pressed");
            document.querySelector('.carousel-prev')?.click();
        } else if (e.key === "ArrowRight") {
            console.log("Right arrow pressed");
            document.querySelector('.carousel-next')?.click();
        }
    });
});
</script>

</body>
</html>
