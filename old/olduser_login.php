<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, email FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Store user details in the session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email']; // ✅ Ensure this is stored

        echo "success";
    } else {
        echo "invalid_credentials";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>User Login</title>
    <link rel="stylesheet" href="styles.css">

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
            <h1 class="display-3 text-white mb-3 animated slideInDown">Log-in</h1>
            <nav aria-label="breadcrumb animated slideInDown">
            </nav>
        </div>
    </div>
    <!-- Page Header End -->
    <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
        <div class="bg-light rounded h-100 d-flex align-items-center p-5">
            <form id="loginForm" action="process_user_login.php" method="POST">
                <div class="row g-3">

                    <div class="col-12 col-sm-6">
                        <input type="text" class="form-control border-0" placeholder="Username" style="height: 55px;" id="username" name="username" required >
                    </div>
                    <div class="col-12 col-sm-6">
                        <input type="password" class="form-control border-0" placeholder="Password" style="height: 55px;" id="password" name="password" required>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary w-100 py-3" type="submit">Log-in</button>
                    </div>
                    <p id="errorMsg" style="color: red;"></p>   

                </div>
            </form>
        </div>
    </div>


    <!-- <form id="loginForm" action="process_user_login.php" method="POST">
        <input type="text" id="username" name="username" placeholder="Username" required>
        <input type="password" id="password" name="password" placeholder="Password" required>
        
        <button type="submit">Login</button>
        <p id="errorLogin" style="color: red;"></p>
    </form>
    
    <button onclick="window.location.href='index.php'">Back</button> -->

    <script src="user_login.js"></script>
</body>
</html>
