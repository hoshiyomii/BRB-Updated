<?php
session_start();
include 'db.php';

// Fetch announcements with image and created_at support
$sql = "SELECT id, title, content, image_path, created_at FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'includes/index_head.php'; ?>

<body>
<?php include 'includes/index_header.php'; ?>

    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Service</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb text-uppercase mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="#">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">Services</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->


    <!-- Service Start -->
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
    <!-- Service End -->

    <?php include 'includes/footer.php'; ?>

    <?php include 'includes/logout_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
</body>
</html>