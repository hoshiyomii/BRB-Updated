<?php
session_start();
include 'db.php';

// Fetch announcements with image and created_at support
$sql = "SELECT id, title, content, image_path, created_at FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Blue Ridge B-Service</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet"> 

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>


    <!-- Topbar Start -->
    <div class="container-fluid bg-light p-0 wow fadeIn" data-wow-delay="0.1s">
        <div class="row gx-0 d-none d-lg-flex">
            <div class="col-lg-7 px-5 text-start">
                <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                    <small class="fa fa-map-marker-alt text-primary me-2"></small>
                    <small>5 Moonlight Loop, Project 4, Quezon City, 1800 Metro Manila</small>
                </div>
                <div class="h-100 d-inline-flex align-items-center py-3">
                    <small class="far fa-clock text-primary me-2"></small>
                    <small>Mon - Fri : 08.00 AM - 05.00 PM</small>
                </div>
            </div>
            <div class="col-lg-5 px-5 text-end">
                <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                    <small class="fa fa-phone-alt text-primary me-2"></small>
                    <small>0917 182 2282</small>
                </div>
                
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->


        <!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0 wow fadeIn" data-wow-delay="0.1s">
        <a href="index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h1 class="m-0 text-primary"><i class=></i>Blue Ridge B</h1>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.php" class="nav-item nav-link">Home</a>
                <a href="about.php" class="nav-item nav-link">About</a>
                <a href="service.php" class="nav-item nav-link active">Service</a>
                <a href="contact.php" class="nav-item nav-link">Contact</a>
                    <?php if (isset($_SESSION["username"])): ?>
                        <!-- <p>Hello, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</p> -->
                        <a href="logout.php" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block">Log-Out<i class="fa fa-arrow-right ms-3"></i></a>
                    <?php else: ?>
                        <a href="user_login.php" class="nav-item nav-link">Login</a>
                        <!-- <a href="register_user.php"><button>Register</button></a> -->
                        <a href="register_user.php" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block">Register<i class="fa fa-arrow-right ms-3"></i></a>
                    <?php endif; ?>
                
                <!-- <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Log In</a>
                </div>
            </div>
                
            <a href="appointment.html" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block">Register<i class="fa fa-arrow-right ms-3"></i></a> -->
        </div>
    </nav>
    <!-- Navbar End -->


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
            <div class="text-center mx-auto mb-5" style="max-width: 600px;">
                <p class="d-inline-block border rounded-pill py-1 px-4">Services</p>
                <h1>Available Documents</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="service-item bg-light rounded h-100 p-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 50px; height: 50px;">
                            <i class="fa fa-x-ray text-primary fs-5"></i>
                        </div>
                        <h4 class="mb-2" style="font-size: 18px;">Repair and Construction</h4>
                        <p class="mb-3" style="font-size: 14px;">Providing repair and construction services for various facilities.</p>
                        <a class="view-popup" href="view_document.php?type=repair_and_construction" style="font-size: 16px; padding: 12px 25px; background-color: #007bff; color: white; border-radius: 30px; font-weight: bold; text-decoration: none;">View</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-item bg-light rounded h-100 p-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 50px; height: 50px;">
                            <i class="fa fa-x-ray text-primary fs-5"></i>
                        </div>
                        <h4 class="mb-2" style="font-size: 18px;">Work Permit For Utilities</h4>
                        <p class="mb-3" style="font-size: 14px;">Assistance in obtaining permits for utility installations and maintenance.</p>
                        <a class="view-popup" href="view_document.php?type=work_permit_utilities" style="font-size: 16px; padding: 12px 25px; background-color: #007bff; color: white; border-radius: 30px; font-weight: bold; text-decoration: none;">View</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-item bg-light rounded h-100 p-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 50px; height: 50px;">
                            <i class="fa fa-x-ray text-primary fs-5"></i>
                        </div>
                        <h4 class="mb-2" style="font-size: 18px;">Certificate of Residency</h4>
                        <p class="mb-3" style="font-size: 14px;">Issuing certificates verifying residency for individuals in need.</p>
                        <a class="view-popup" href="view_document.php?type=certificate_of_residency" style="font-size: 16px; padding: 12px 25px; background-color: #007bff; color: white; border-radius: 30px; font-weight: bold; text-decoration: none;">View</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-item bg-light rounded h-100 p-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 50px; height: 50px;">
                            <i class="fa fa-x-ray text-primary fs-5"></i>
                        </div>
                        <h4 class="mb-2" style="font-size: 18px;">Certificate of Indigency</h4>
                        <p class="mb-3" style="font-size: 14px;">Providing certificates for individuals who are unable to afford basic necessities.</p>
                        <a class="view-popup" href="view_document.php?type=certificate_of_indigency" style="font-size: 16px; padding: 12px 25px; background-color: #007bff; color: white; border-radius: 30px; font-weight: bold; text-decoration: none;">View</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-item bg-light rounded h-100 p-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 50px; height: 50px;">
                            <i class="fa fa-x-ray text-primary fs-5"></i>
                        </div>
                        <h4 class="mb-2" style="font-size: 18px;">Business Clearance</h4>
                        <p class="mb-3" style="font-size: 14px;">Issuing clearances for businesses to operate legally and meet local requirements.</p>
                        <a class="view-popup" href="view_document.php?type=business_clearance" style="font-size: 16px; padding: 12px 25px; background-color: #007bff; color: white; border-radius: 30px; font-weight: bold; text-decoration: none;">View</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-item bg-light rounded h-100 p-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 50px; height: 50px;">
                            <i class="fa fa-x-ray text-primary fs-5"></i>
                        </div>
                        <h4 class="mb-2" style="font-size: 18px;">New Business Permit</h4>
                        <p class="mb-3" style="font-size: 14px;">Assistance in acquiring new business permits for healthcare-related establishments.</p>
                        <a class="view-popup" href="view_document.php?type=new_business_permit" style="font-size: 16px; padding: 12px 25px; background-color: #007bff; color: white; border-radius: 30px; font-weight: bold; text-decoration: none;">View</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-item bg-light rounded h-100 p-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 50px; height: 50px;">
                            <i class="fa fa-x-ray text-primary fs-5"></i>
                        </div>
                        <h4 class="mb-2" style="font-size: 18px;">Clearance for Major Construction</h4>
                        <p class="mb-3" style="font-size: 14px;">Providing clearances for large-scale construction projects and developments.</p>
                        <a class="view-popup" href="view_document.php?type=clearance_major_construction" style="font-size: 16px; padding: 12px 25px; background-color: #007bff; color: white; border-radius: 30px; font-weight: bold; text-decoration: none;">View</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<!-- Service End -->

    <div class="container-fluid bg-dark text-light footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-light mb-4">Address</h5>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>5 Moonlight Loop, Project 4, Quezon City, 1800 Metro Manila
                    </p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>0917 182 2282</p>
                    <div class="d-flex pt-2">
                       
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-light mb-4">Services</h5>
                    <a class="btn btn-link" href="">Repair and Construction</a>
                    <a class="btn btn-link" href="">Work Permit For Utilities</a>
                    <a class="btn btn-link" href="">Certificate of Residency</a>
                    <a class="btn btn-link" href="">Reservation Form</a>
                    <a class="btn btn-link" href="">Certificate of Indigency</a>
                    <a class="btn btn-link" href="">Business Clearance</a>
                    <a class="btn btn-link" href="">New Business Permit</a>
                    <a class="btn btn-link" href="">Clearance for Major Construction</a>
                    
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-light mb-4">Quick Links</h5>
                    <a class="btn btn-link" href="">About Us</a>
                    <a class="btn btn-link" href="">Contact Us</a>
                    <a class="btn btn-link" href="">Our Services</a>
                </div>

        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="#">Your Site Name</a>, All Right Reserved.
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>