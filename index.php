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
<link href= "index.css" rel="stylesheet">

<body>
    <?php include 'includes/index_header.php'; ?>

<div class="snap-container">

    <!-- Hero Section -->
    <div class="snap-section" id="hero">
        <div class="container-fluid hero-landing p-0" style="position:relative;">
            <div class="row g-0 align-items-center min-vh-60">
                <div class="col-lg-7 p-5 wow fadeInLeft" data-wow-delay="0.1s" style="margin-top:70px;">
                    <h1 class="display-3 fw-bold text-white mb-3">Welcome to Barangay Blue Ridge B</h1>
                    <p class="lead text-light mb-4">
                        Blue Ridge B is a welcoming neighborhood in Quezon City, Metro Manila. Part of the Blue Ridge subdivision, it offers a peaceful residential area with easy access to major roads, schools, and businesses. Known for its strong sense of community, Blue Ridge B is an ideal place to live, work, and enjoy a convenient lifestyle in the heart of the city.
                    </p>
                    <div class="hero-buttons mb-4">
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <a href="register_user.php" class="btn btn-primary btn-lg rounded-pill px-5 me-2 mb-3">Register Now</a> <!-- Added bottom margin -->
                        <?php endif; ?>
                        <a href="announcements.php" class="btn btn-outline-light btn-lg rounded-pill px-5 mb-3">View Announcements</a> <!-- Added bottom margin -->
                    </div>
                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <span class="badge bg-success fs-6"><i class="fa fa-users me-2"></i>Safe Community</span>
                        <span class="badge bg-info text-dark fs-6"><i class="fa fa-cogs me-2"></i>Active Services</span>
                        <span class="badge bg-warning text-dark fs-6"><i class="fa fa-smile me-2"></i>Friendly Staff</span>
                    </div>
                </div>
            </div>
            <div class="scroll-down-indicator position-absolute bottom-0 start-50 translate-middle-x mb-3">
                <a href="#announcements" style="text-decoration:none;">
                    <i class="fa fa-chevron-down fa-2x text-white animate__animated animate__bounce animate__infinite"></i>
                </a>
            </div>
        </div>

    </div>

    <div class="snap-section" id="announcements">
        <h1 class="text-center mb-5">Recent Announcements</h1>
        <div class="announcements-carousel-wrapper mx-auto">
            <div id="announcementsCarousel" class="carousel slide carousel-fade">
              <div class="carousel-inner">
                <?php
                $count = 0;
                // Reset result pointer if needed
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()):
                    if ($count >= 7) break;
                    $active = ($count === 0) ? 'active' : '';
                    $bg = !empty($row['image_path']) ? "background-image: url('".htmlspecialchars($row['image_path'])."');" : "background-color: #1f458b;";
                ?>
                  <div class="carousel-item <?php echo $active; ?>">
                    <div class="announcement-slide d-flex align-items-center" style="<?php echo $bg; ?>">
                      <div class="announcement-overlay"></div>
                      <div class="announcement-info-hero">
                        <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($row['genre']); ?></span>
                        <h2 class="mb-2"><?php echo htmlspecialchars($row['title']); ?></h2>
                        <p class="mb-3"><?php echo htmlspecialchars(substr($row['content'], 0, 120)) . '...'; ?></p>
                      </div>
                    </div>
                  </div>
                <?php
                    $count++;
                endwhile;
                ?>
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#announcementsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#announcementsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
                <span class="visually-hidden">Next</span>
              </button>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="announcements.php" class="btn btn-primary btn-lg px-5">View All Announcements</a>
        </div>
    </div>
    
        <!-- About Start -->
    <div class="snap-section " id="about">
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row g-5">
                    <div class="col-lg-6 " data-wow-delay="0.1s">
                        <div class="d-flex flex-column">
                            <img class="img-fluid rounded w-75 align-self-end" src="img/about-1.png" alt="">
                            <img class="img-fluid rounded w-50 bg-white pt-3 pe-3" src="img/about-2.png" alt="" style="margin-top: -25%;">
                        </div>
                    </div>
                    <div class="col-lg-6 " data-wow-delay="0.5s">
                        <p class="d-inline-block border rounded-pill py-1 px-4">About Us</p>
                        <h1 class="mb-4"> Know About Us!</h1>
                        <p>Blue Ridge B is a welcoming neighborhood in Quezon City, Metro Manila. Part of the Blue Ridge subdivision, it offers a peaceful residential area with easy access to major roads, schools, and businesses. Known for its strong sense of community, Blue Ridge B is an ideal place to live, work, and enjoy a convenient lifestyle in the heart of the city.</p>
                        <p><i class="far fa-check-circle text-primary me-3"></i>Quality health care</p>
                        <p><i class="far fa-check-circle text-primary me-3"></i>Quality Services</p>
                        <a class="btn btn-primary rounded-pill py-3 px-5 mt-3" href="about.php">Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

        <!-- Service Start -->
    <div class="snap-section" id="services">
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

    <div class="snap-section wow fadeIn" id="footer-section">
        <?php include 'includes/footer.php'; ?>
    </div>

</div>

<div class="custom-modal-bg"></div>
<?php include 'includes/logout_modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/wow/wow.min.js"></script>

<script>
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
document.addEventListener('keydown', function(e) {
    if (e.key === "ArrowLeft") {
        document.querySelector('#announcementsCarousel .carousel-control-prev')?.click();
    } else if (e.key === "ArrowRight") {
        document.querySelector('#announcementsCarousel .carousel-control-next')?.click();
    }
});
</script>

</body>
</html>
