<?php
session_start();
include 'db.php';


$id = $_GET['id'];
$sql = "SELECT * FROM announcements WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$announcement = $result->fetch_assoc();

if (!$announcement) {
    die("Announcement not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($announcement['title']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="pre-register.js" defer></script>

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
        <button class="btn btn-secondary mb-3" onclick="window.history.back()">Back</button>
        <div class="card">
            <div class="card-body">
                <h1 class="card-title"><?php echo htmlspecialchars($announcement['title']); ?></h1>
                
                <?php if (!empty($announcement['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($announcement['image_path']); ?>" alt="Announcement Image" class="img-fluid mb-3">
                <?php endif; ?>

                <p class="card-text"><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>

                <!-- Display Genre -->
                <p><strong>Genre:</strong> <?php echo htmlspecialchars($announcement['genre']); ?></p>

                <!-- Display Created At Timestamp -->
                <p><strong>Posted on:</strong> <?php echo date("F j, Y, g:i a", strtotime($announcement['created_at'])); ?></p>

                <!-- Display Active Until -->
                <p><strong>Active Until:</strong> 
                    <?php echo $announcement['active_until'] ? date("F j, Y, g:i a", strtotime($announcement['active_until'])) : 'No expiration'; ?>
                </p>

                <?php if ($announcement['type'] === 'event'): ?>
                    <p><strong>Max Participants:</strong> <?php echo $announcement['max_participants']; ?></p>
                    <p><strong>Registered Participants:</strong> <span id="count"><?php echo $announcement['registered_participants']; ?></span></p>
                    
                    <?php if ($announcement['registered_participants'] < $announcement['max_participants']): ?>
                        <button id="preRegisterBtn" class="btn btn-primary" data-id="<?php echo $announcement['id']; ?>">Pre-Register</button>
                    <?php else: ?>
                        <p><strong>Registration Full</strong></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
