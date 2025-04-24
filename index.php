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
        WHERE is_active = 1 
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Blue Ridge B-Home</title>
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

    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <!-- Initialize Carousel -->
    <script>
    $(document).ready(function(){
        $(".owl-carousel").owlCarousel({
            loop:true,
            margin:10,
            nav:true,
            items:1
        });
    });
    </script>

    <!-- Custom Styles for Compact Announcements -->
    <style>
        .announcement-wrapper {
            display: flex;
            justify-content: center;
        }

        .announcement-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            max-width: 1200px; /* Adjust this value as needed */
        }

        .announcement-item {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }

        .announcement-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .announcement-item h4 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .announcement-item p {
            font-size: 14px;
            margin-bottom: 15px;
        }

        .announcement-item a {
            font-size: 16px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border-radius: 30px;
            font-weight: bold;
            text-decoration: none;
        }

        .announcement-image {
            max-width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        /* Custom styles for carousel height */
        .carousel-container {
            max-height: 600px; /* Adjust this value as needed */
            overflow: hidden;
        }

        .carousel-container img {
            max-height: 600px; /* Adjust this value as needed */
            object-fit: cover;
        }
    </style>

</head>

<body>
    <!-- Topbar Start -->
    <div class="container-fluid bg-light p-0 wow fadeIn" data-wow-delay="0.1s">
        <div class="row gx-0 d-none d-lg-flex">
            <div class="col-lg-7 px-5 text-start">
                <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                    <small class="fa fa-map-marker-alt text-primary me-2"></small>
                    <small> 5 Moonlight Loop, Project 4, Quezon City, 1800 Metro Manila</small>
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
                <a href="index.php" class="nav-item nav-link active">Home</a>
                <a href="about.php" class="nav-item nav-link">About</a>
                <a href="service.php" class="nav-item nav-link">Service</a>
                <a href="contact.php" class="nav-item nav-link">Contact</a>
                
                <?php if (isset($_SESSION["username"])): ?>
                    <a href="dashboard.php" class="nav-item nav-link">Dashboard</a>
                <?php endif; ?>
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
    <div class="container-fluid header bg-primary p-0 mb-5">
        <div class="row g-0 align-items-center flex-column-reverse flex-lg-row">
            <div class="col-lg-6 p-5 wow fadeIn" data-wow-delay="0.1s">
                <h1 class="display-4 text-white mb-5">About Barangay Blue Ridge B</h1>
                <div class="row g-4">
                    <div class="col-sm-4">
                        <div class="border-start border-light ps-4">
                            <h2 class="text-white mb-1" data-toggle="counter-up">1,071</h2>
                            <p class="text-light mb-0">Resident</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="border-start border-light ps-4">
                            <h2 class="text-white mb-1" data-toggle="counter-up">52</h2>
                            <p class="text-light mb-0">Brgy Staff</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="border-start border-light ps-4">
                            <h2 class="text-white mb-1" data-toggle="counter-up">1,123</h2>
                            <p class="text-light mb-0">Total Citizens</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 wow fadeIn carousel-container" data-wow-delay="0.5s">
                <div class="owl-carousel header-carousel">
                    <div class="owl-carousel-item position-relative">
                        <img class="img-fluid" src="http://localhost/barangay/img/index-3.png" alt="">
                        <div class="owl-carousel-text">
                           
                        </div>
                    </div>
                    <div class="owl-carousel-item position-relative">
                        <img class="img-fluid" src="http://localhost/barangay/img/index-2.png" alt="">
                        <div class="owl-carousel-text">
                            
                        </div>
                    </div>
                    <div class="owl-carousel-item position-relative">
                        <img class="img-fluid" src="http://localhost/barangay/img/index-1.png" alt="">
                        <div class="owl-carousel-text">
                            <h1 class="display-1 text-white mb-0"></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <h1 align="center">Announcements</h1>

    <div class="announcement-wrapper">
        
        <div class="announcement-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="announcement-item">
                    <!-- Optional: Add an image at the top of the card -->
                    <?php if (!empty($row['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Announcement Image" class="announcement-image">
                    <?php endif; ?>

                    <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                    <p><?php echo htmlspecialchars(substr($row['content'], 0, 100)) . '...'; ?></p>
                    <p><strong>Genre:</strong> <?php echo htmlspecialchars($row['genre']); ?></p> <!-- Display the genre -->
                    <a href="view_announcement.php?id=<?php echo $row['id']; ?>">Click Here</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    
    <!-- dito ung sort-->


    <div class="text-center mb-4">
        <form method="GET" action="index.php" class="d-inline-block">
            <label for="genreFilter" class="me-2">Filter by Genre:</label>
            <select id="genreFilter" name="genre" class="form-control d-inline-block w-auto">
                <option value="">All</option>
                <option value="Work and Employment" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Work and Employment') ? 'selected' : ''; ?>>Work and Employment</option>
                <option value="Healthcare and Safety" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Healthcare and Safety') ? 'selected' : ''; ?>>Healthcare and Safety</option>
                <option value="Animals" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Animals') ? 'selected' : ''; ?>>Animals</option>
                <option value="Safety" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Safety') ? 'selected' : ''; ?>>Safety</option>
                <option value="Emergency" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Emergency') ? 'selected' : ''; ?>>Emergency</option>
                <option value="Holidays and Events" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Holidays and Events') ? 'selected' : ''; ?>>Holidays and Events</option>
                <option value="Education" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Education') ? 'selected' : ''; ?>>Education</option>
                <option value="Transportation and Traffic" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Transportation and Traffic') ? 'selected' : ''; ?>>Transportation and Traffic</option>
                <option value="Government and Public Affairs" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Government and Public Affairs') ? 'selected' : ''; ?>>Government and Public Affairs</option>
                <option value="Social and Community" <?php echo (isset($_GET['genre']) && $_GET['genre'] === 'Social and Community') ? 'selected' : ''; ?>>Social and Community</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

        <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <div class="d-flex flex-column">
                        <img class="img-fluid rounded w-75 align-self-end" src="img/about-1.png" alt="">
                        <img class="img-fluid rounded w-50 bg-white pt-3 pe-3" src="img/about-2.png" alt="" style="margin-top: -25%;">
                    </div>
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
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
    <!-- About End -->

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
                        <h4 class="mb-2" style="font-size: 18px;">New Business Permit</h4>
                        <p class="mb-3" style="font-size: 14px;">Issuing clearances for businesses to operate legally and meet local requirements.</p>
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
