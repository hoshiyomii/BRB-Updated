<?php
session_start();

$document_type = isset($_GET['type']) ? $_GET['type'] : '';

$requirements = [
    'repair_and_construction' => [
        'title' => 'Repair and Construction',
        'requirements' => [
            'Proof of Identity',
            'Proof of Ownership',
            'Construction Plan',
        ],
    ],
    'work_permit_utilities' => [
        'title' => 'Work Permit For Utilities',
        'requirements' => [
            'Proof of Identity',
            'Utility Plan',
            'Approval from Local Authorities',
        ],
    ],
    'certificate_of_residency' => [
        'title' => 'Certificate of Residency',
        'requirements' => [
            'Proof of Identity',
            'Proof of Residency',
            'Completed Application Form',
        ],
    ],
    'certificate_of_indigency' => [
        'title' => 'Certificate of Indigency',
        'requirements' => [
            'Proof of Identity',
            'Proof of Income',
            'Completed Application Form',
        ],
    ],
    'business_clearance' => [
        'title' => 'Business Clearance',
        'requirements' => [
            'Proof of Identity',
            'Business Plan',
            'Approval from Local Authorities',
        ],
    ],
    'new_business_permit' => [
        'title' => 'New Business Permit',
        'requirements' => [
            'Proof of Identity',
            'Business Plan',
            'Approval from Local Authorities',
        ],
    ],
    'clearance_major_construction' => [
        'title' => 'Clearance for Major Construction',
        'requirements' => [
            'Proof of Identity',
            'Construction Plan',
            'Approval from Local Authorities',
        ],
    ],
];

if (!array_key_exists($document_type, $requirements)) {
    die('Invalid document type.');
}

$document = $requirements[$document_type];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($document['title']); ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

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
    <div class="container mt-5">
        <h1><?php echo htmlspecialchars($document['title']); ?></h1>
        <h3>Requirements:</h3>
        <ul>
            <?php foreach ($document['requirements'] as $requirement): ?>
                <li><?php echo htmlspecialchars($requirement); ?></li>
            <?php endforeach; ?>
        </ul>
        <a href="document_forms.php?type=<?php echo urlencode($document_type); ?>" class="btn btn-primary">Proceed to Request Form</a>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </div>
</body>
</html>