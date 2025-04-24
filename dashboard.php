<?php
session_start();
include 'db.php';

function generateControlNumber($documentType, $id) {
    $prefix = '';
    switch ($documentType) {
        case 'Certificate of Indigency':
            $prefix = 'COI';
            break;
        case 'Certificate of Residency':
            $prefix = 'COR';
            break;
        case 'Clearance for Major Construction':
            $prefix = 'CMC';
            break;
        case 'New Business Permit':
            $prefix = 'NBP';
            break;
        case 'Repair and Construction':
            $prefix = 'RC';
            break;
        case 'Work Permit for Utilities':
            $prefix = 'WPU';
            break;
    }
    return $prefix . '-' . str_pad($id, 3, '0', STR_PAD_LEFT);
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: user_login.php');
    exit();
}

// Fetch user information
$username = $_SESSION['username'];
$userQuery = $conn->prepare("SELECT * FROM users WHERE username = ?");
$userQuery->bind_param("s", $username);
$userQuery->execute();
$userResult = $userQuery->get_result();
$userData = $userResult->fetch_assoc();

if (!$userData) {
    echo "User not found.";
    exit();
}

$userId = $userData['id']; // Get the user ID

// Fetch pending requests from individual tables
$pendingRequests = [];

// Certificate of Indigency
$indigencyQuery = $conn->prepare("SELECT id, 'Certificate of Indigency' AS document_type, created_at, status, pickup_schedule, rejection_reason FROM certificate_of_indigency WHERE user_id = ?");
$indigencyQuery->bind_param("i", $userId);
$indigencyQuery->execute();
$pendingRequests = array_merge($pendingRequests, $indigencyQuery->get_result()->fetch_all(MYSQLI_ASSOC));

// Certificate of Residency
$residencyQuery = $conn->prepare("SELECT id, 'Certificate of Residency' AS document_type, created_at, status, pickup_schedule, rejection_reason FROM certificate_of_residency WHERE user_id = ?");
$residencyQuery->bind_param("i", $userId);
$residencyQuery->execute();
$pendingRequests = array_merge($pendingRequests, $residencyQuery->get_result()->fetch_all(MYSQLI_ASSOC));

// Clearance for Major Construction
$constructionQuery = $conn->prepare("SELECT id, 'Clearance for Major Construction' AS document_type, created_at, status, pickup_schedule, rejection_reason FROM clearance_major_construction WHERE user_id = ?");
$constructionQuery->bind_param("i", $userId);
$constructionQuery->execute();
$pendingRequests = array_merge($pendingRequests, $constructionQuery->get_result()->fetch_all(MYSQLI_ASSOC));

// New Business Permit
$businessPermitQuery = $conn->prepare("SELECT id, 'New Business Permit' AS document_type, created_at, status, pickup_schedule, rejection_reason FROM new_business_permit WHERE user_id = ?");
$businessPermitQuery->bind_param("i", $userId);
$businessPermitQuery->execute();
$pendingRequests = array_merge($pendingRequests, $businessPermitQuery->get_result()->fetch_all(MYSQLI_ASSOC));

// Repair and Construction
$repairQuery = $conn->prepare("SELECT id, 'Repair and Construction' AS document_type, created_at, status, pickup_schedule, rejection_reason FROM repair_and_construction WHERE user_id = ?");
$repairQuery->bind_param("i", $userId);
$repairQuery->execute();
$pendingRequests = array_merge($pendingRequests, $repairQuery->get_result()->fetch_all(MYSQLI_ASSOC));

// Work Permit for Utilities
$workPermitQuery = $conn->prepare("SELECT id, 'Work Permit for Utilities' AS document_type, created_at, status, pickup_schedule, rejection_reason FROM work_permit_utilities WHERE user_id = ?");
$workPermitQuery->bind_param("i", $userId);
$workPermitQuery->execute();
$pendingRequests = array_merge($pendingRequests, $workPermitQuery->get_result()->fetch_all(MYSQLI_ASSOC));

// Fetch joined events
$joinedEventsQuery = $conn->prepare("SELECT r.*, a.title AS event_name, a.created_at AS event_date 
                                     FROM registrations r 
                                     JOIN announcements a ON r.announcement_id = a.id 
                                     WHERE r.user_id = ?");
$joinedEventsQuery->bind_param("i", $userId);
$joinedEventsQuery->execute();
$joinedEvents = $joinedEventsQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

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


    <style>
        
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        .content-wrapper {
            flex: 1;
            min-height: 60vh;
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
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0 wow fadeIn" data-wow-delay="0.1s">
        <a href="index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h1 class="m-0 text-primary">Blue Ridge B</h1>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.php" class="nav-item nav-link">Home</a>
                <a href="about.php" class="nav-item nav-link">About</a>
                <a href="service.php" class="nav-item nav-link">Service</a>
                <a href="contact.php" class="nav-item nav-link">Contact</a>
                <a href="dashboard.php" class="nav-item nav-link active">Dashboard</a>
                <a href="logout.php" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block">Log-Out<i class="fa fa-arrow-right ms-3"></i></a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

        <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">User Dashboard</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb text-uppercase mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="#">Manage your personal information, requests, and events.</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Content Wrapper Start -->
    <div class="content-wrapper">
        <div class="container mt-5">
            <h1 class="text-center">User Dashboard</h1>
            <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Profile</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-requests-tab" data-bs-toggle="tab" data-bs-target="#pending-requests" type="button" role="tab" aria-controls="pending-requests" aria-selected="false">Pending Requests</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="joined-events-tab" data-bs-toggle="tab" data-bs-target="#joined-events" type="button" role="tab" aria-controls="joined-events" aria-selected="false">Joined Events</button>
                </li>
            </ul>
            <div class="tab-content" id="dashboardTabsContent">
                <!-- Profile Tab -->
                <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <h3 class="mt-4">Profile Information</h3>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($userData['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($userData['street'] . ', ' . $userData['house_number']); ?></p>
                    <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($userData['phone_number']); ?></p>
                </div>

                <!-- Pending Requests Tab -->
                <div class="tab-pane fade" id="pending-requests" role="tabpanel" aria-labelledby="pending-requests-tab">
                    <h3 class="mt-4">Request History</h3>
                    <?php if (count($pendingRequests) > 0): ?>
                        <ul class="list-group">
                            <?php foreach ($pendingRequests as $request): ?>
                                <li class="list-group-item">
                                    <strong>Control Number:</strong> <?php echo htmlspecialchars(generateControlNumber($request['document_type'], $request['id'])); ?><br>
                                    <strong>Document Type:</strong> <?php echo htmlspecialchars($request['document_type']); ?><br>
                                    <strong>Request Date:</strong> <?php echo htmlspecialchars($request['created_at']); ?><br>
                                    <strong>Status:</strong> 
                                    <?php 
                                        if ($request['status'] === 'approved') {
                                            echo '<span class="text-success">Approved</span>';
                                            echo '<br><strong>Pickup Schedule:</strong> ' . htmlspecialchars($request['pickup_schedule']);
                                        } elseif ($request['status'] === 'rejected') {
                                            echo '<span class="text-danger">Declined</span>';
                                            echo '<br><strong>Reason for Rejection:</strong> ' . htmlspecialchars($request['rejection_reason']);
                                        } else {
                                            echo '<span class="text-warning">Pending</span>';
                                        }
                                    ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No requests found.</p>
                    <?php endif; ?>
                </div>

                <!-- Joined Events Tab -->
                <div class="tab-pane fade" id="joined-events" role="tabpanel" aria-labelledby="joined-events-tab">
                    <h3 class="mt-4">Joined Events</h3>
                    <?php if ($joinedEvents->num_rows > 0): ?>
                        <ul class="list-group">
                            <?php while ($event = $joinedEvents->fetch_assoc()): ?>
                                <li class="list-group-item">
                                    <strong>Event Name:</strong> <?php echo htmlspecialchars($event['event_name']); ?><br>
                                    <strong>Event Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p>No joined events.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Wrapper End -->

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