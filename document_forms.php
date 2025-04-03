<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: user_login.php");
    exit();
}

include 'db.php';

$document_type = isset($_GET['type']) ? $_GET['type'] : '';

$document_titles = [
    'repair_and_construction' => 'Repair and Construction',
    'work_permit_utilities' => 'Work Permit For Utilities',
    'certificate_of_residency' => 'Certificate of Residency',
    'certificate_of_indigency' => 'Certificate of Indigency',
    'business_clearance' => 'Business Clearance',
    'new_business_permit' => 'New Business Permit',
    'clearance_major_construction' => 'Clearance for Major Construction',
];

if (!array_key_exists($document_type, $document_titles)) {
    die('Invalid document type.');
}

$document_title = $document_titles[$document_type];

// Fetch user information
$username = $_SESSION['username'];
$user_query = $conn->query("SELECT * FROM users WHERE username = '$username'");
$user = $user_query->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission
    $user_id = $user['id'];
    $contractor = isset($_POST['contractor']) ? $_POST['contractor'] : null;
    $schedule = isset($_POST['schedule']) ? $_POST['schedule'] : null;
    $type = isset($_POST['type']) ? $_POST['type'] : null;
    $utility_type = isset($_POST['utility_type']) ? $_POST['utility_type'] : null;
    $company = isset($_POST['company']) ? $_POST['company'] : null;
    $id_image = isset($_FILES['id_image']['name']) ? 'uploads/' . basename($_FILES['id_image']['name']) : null;
    $occupancy = isset($_POST['occupancy']) ? $_POST['occupancy'] : null;
    $monthly_salary = isset($_POST['monthly_salary']) ? $_POST['monthly_salary'] : null;
    $clearance_image = isset($_FILES['clearance_image']['name']) ? 'uploads/' . basename($_FILES['clearance_image']['name']) : null;
    $ownership_type = isset($_POST['ownership_type']) ? $_POST['ownership_type'] : null;
    $business_name = isset($_POST['business_name']) ? $_POST['business_name'] : null;
    $business_type = isset($_POST['business_type']) ? $_POST['business_type'] : null;

    // Handle file uploads
    if ($id_image) {
        move_uploaded_file($_FILES['id_image']['tmp_name'], $id_image);
    }
    if ($clearance_image) {
        move_uploaded_file($_FILES['clearance_image']['tmp_name'], $clearance_image);
    }

    $stmt = $conn->prepare("INSERT INTO document_requests (user_id, document_type, contractor, schedule, type, utility_type, company, id_image, occupancy, monthly_salary, clearance_image, ownership_type, business_name, business_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssssssss", $user_id, $document_type, $contractor, $schedule, $type, $utility_type, $company, $id_image, $occupancy, $monthly_salary, $clearance_image, $ownership_type, $business_name, $business_type);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Request submitted successfully!'); window.location.href = 'index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($document_title); ?> Request Form</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1><?php echo htmlspecialchars($document_title); ?> Request Form</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['street'] . ' ' . $user['house_number']); ?>" readonly>
            </div>

            <?php if ($document_type === 'repair_and_construction'): ?>
                <div class="form-group">
                    <label for="contractor">Contractor</label>
                    <input type="text" class="form-control" id="contractor" name="contractor" required>
                </div>
                <div class="form-group">
                    <label for="schedule">Schedule</label>
                    <input type="datetime-local" class="form-control" id="schedule" name="schedule" required>
                </div>
                <div class="form-group">
                    <label for="type">Type</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="Renovation">Renovation</option>
                        <option value="Repair">Repair</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
            <?php elseif ($document_type === 'work_permit_utilities'): ?>
                <div class="form-group">
                    <label for="utility_type">Utility Type</label>
                    <select class="form-control" id="utility_type" name="utility_type" required>
                        <option value="Water">Water</option>
                        <option value="Electricity">Electricity</option>
                        <option value="Internet">Internet</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="company">Company</label>
                    <select class="form-control" id="company" name="company" required>
                        <option value="Meralco">Meralco</option>
                        <option value="Maynilad">Maynilad</option>
                        <option value="PLDT">PLDT</option>
                        <option value="Converge">Converge</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
            <?php elseif ($document_type === 'certificate_of_residency'): ?>
                <div class="form-group">
                    <label for="id_image">Attach ID Image</label>
                    <input type="file" class="form-control" id="id_image" name="id_image" required>
                </div>
            <?php elseif ($document_type === 'certificate_of_indigency'): ?>
                <div class="form-group">
                    <label for="occupancy">Occupancy</label>
                    <input type="text" class="form-control" id="occupancy" name="occupancy" required>
                </div>
                <div class="form-group">
                    <label for="monthly_salary">Monthly Salary</label>
                    <input type="number" class="form-control" id="monthly_salary" name="monthly_salary" required>
                </div>
            <?php elseif ($document_type === 'business_clearance'): ?>
                <div class="form-group">
                    <label for="clearance_image">Attach Existing Clearance Image</label>
                    <input type="file" class="form-control" id="clearance_image" name="clearance_image" required>
                </div>
            <?php elseif ($document_type === 'new_business_permit'): ?>
                <div class="form-group">
                    <label for="ownership_type">Ownership Type</label>
                    <select class="form-control" id="ownership_type" name="ownership_type" required>
                        <option value="Solo">Solo</option>
                        <option value="Shared">Shared</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="business_name">Business Name</label>
                    <input type="text" class="form-control" id="business_name" name="business_name" required>
                </div>
                <div class="form-group">
                    <label for="business_type">Business Type</label>
                    <input type="text" class="form-control" id="business_type" name="business_type" required>
                </div>
            <?php elseif ($document_type === 'clearance_major_construction'): ?>
                <div class="form-group">
                    <label for="schedule">Schedule</label>
                    <input type="datetime-local" class="form-control" id="schedule" name="schedule" required>
                </div>
                <div class="form-group">
                    <label for="contractor">Contractor</label>
                    <input type="text" class="form-control" id="contractor" name="contractor" required>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary mt-3">Submit Request</button>
        </form>
        <a href="index.php" class="btn btn-secondary mt-3">Back</a>
    </div>
</body>
</html>