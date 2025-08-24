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
    $user_id = $user['id'];

    if ($document_type === 'repair_and_construction') {
        $construction_address = $_POST['construction_address'];
        $date_of_request = date('Y-m-d'); // Automatically set to the current date
        $homeowner_name = $user['first_name'] . ' ' . $user['last_name']; // Default to requester's name
        $contractor_name = $_POST['contractor_name'];
        $contractor_contact = $_POST['contractor_contact'];
        $activity_nature = $_POST['activity_nature'];

        $stmt = $conn->prepare("INSERT INTO repair_and_construction (user_id, construction_address, date_of_request, homeowner_name, contractor_name, contractor_contact, activity_nature) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $user_id, $construction_address, $date_of_request, $homeowner_name, $contractor_name, $contractor_contact, $activity_nature);
        $stmt->execute();

        // Generate the control number before closing the statement
        $control_number = "RC-" . str_pad($stmt->insert_id, 3, "0", STR_PAD_LEFT);

        $stmt->close(); // Close the statement after accessing insert_id

        // Return a JSON response
        echo json_encode(['success' => true, 'control_number' => $control_number]);
        exit();
    }

    if ($document_type === 'work_permit_utilities') {
        $date_of_request = date('Y-m-d'); // Automatically set to the current date
        $date_of_work = $_POST['date_of_work'];
        $contact_no = $_POST['contact_no'];
        $address = $_POST['address'];
        $service_provider = $_POST['service_provider'];
        $other_service_provider = $service_provider === 'Others' ? $_POST['other_service_provider'] : null;
        $utility_type = $_POST['utility_type'];
        $other_utility_type = $utility_type === 'Others' ? $_POST['other_utility_type'] : null;
        $nature_of_work = $_POST['nature_of_work'];

        $stmt = $conn->prepare("INSERT INTO work_permit_utilities (user_id, date_of_request, date_of_work, contact_no, address, service_provider, other_service_provider, utility_type, other_utility_type, nature_of_work) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssssss", $user_id, $date_of_request, $date_of_work, $contact_no, $address, $service_provider, $other_service_provider, $utility_type, $other_utility_type, $nature_of_work);
        $stmt->execute();

        $control_number = "WPU-" . str_pad($stmt->insert_id, 3, "0", STR_PAD_LEFT);
        $stmt->close();

        echo json_encode(['success' => true, 'control_number' => $control_number]);
        exit();
    }

    if ($document_type === 'certificate_of_residency') {
        $resident_since = $_POST['resident_since'];
        $date = date('Y-m-d'); // Automatically set to the current date
        $id_image = 'uploads/' . basename($_FILES['id_image']['name']);
        move_uploaded_file($_FILES['id_image']['tmp_name'], $id_image);

        $stmt = $conn->prepare("INSERT INTO certificate_of_residency (user_id, resident_since, date, id_image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $resident_since, $date, $id_image);
        $stmt->execute();

        $control_number = "CR-" . str_pad($stmt->insert_id, 3, "0", STR_PAD_LEFT);
        $stmt->close();

        echo json_encode(['success' => true, 'control_number' => $control_number]);
        exit();
    }

    if ($document_type === 'certificate_of_indigency') {
        $occupancy = $_POST['occupancy'];
        $income = $_POST['income'];

        $stmt = $conn->prepare("INSERT INTO certificate_of_indigency (user_id, occupancy, income) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $occupancy, $income);
        $stmt->execute();

        $control_number = "CI-" . str_pad($stmt->insert_id, 3, "0", STR_PAD_LEFT);
        $stmt->close();

        echo json_encode(['success' => true, 'control_number' => $control_number]);
        exit();
    }

    if ($document_type === 'new_business_permit') {
        $owner = $user['first_name'] . ' ' . $user['last_name'];
        $location = $_POST['location'];
        $business_name = $_POST['business_name'];
        $nature_of_business = $_POST['nature_of_business'];
        $business_type = $_POST['business_type'];
        $co_owner = $business_type === 'Shared' ? $_POST['co_owner'] : null;
        $date = date('Y-m-d'); // Automatically set to the current date
    
        $stmt = $conn->prepare("INSERT INTO new_business_permit (user_id, owner, location, business_name, nature_of_business, business_type, co_owner, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $user_id, $owner, $location, $business_name, $nature_of_business, $business_type, $co_owner, $date);
        $stmt->execute();
        $control_number = "NBP-" . str_pad($stmt->insert_id, 3, "0", STR_PAD_LEFT);
        $stmt->close();

        echo json_encode(['success' => true, 'control_number' => $control_number]);
        exit();
    }

    if ($document_type === 'clearance_major_construction') {
        $schedule = $_POST['schedule'];
        $contractor = $_POST['contractor'];
        $construction_address = $_POST['construction_address'];
        $infrastructures = $_POST['infrastructures'];

        $stmt = $conn->prepare("INSERT INTO clearance_major_construction (user_id, schedule, contractor, construction_address, infrastructures) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $schedule, $contractor, $construction_address, $infrastructures);
        $stmt->execute();
        $control_number = "CMC-" . str_pad($stmt->insert_id, 3, "0", STR_PAD_LEFT);
        $stmt->close();

        echo json_encode(['success' => true, 'control_number' => $control_number]);
        exit();
    }

    // Handle form submission
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

    <style>
        body {
            background-color: #f4f6f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        

        .login-container {
    background-color: white;
    border-radius: 4px; /* Reduced border-radius for smaller border edges */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Reduced box-shadow for a smaller border effect */
    width: 100%;
    max-width: 500px; /* You can adjust the width as needed */
    padding: 10px; /* Reduced padding to make the container smaller */

    

        }
        
        label {
    font-weight: bold;
    font-size: 15px;
    color: #333; /* Darker color for better visibility */
}
     


        .form-group {
            margin-bottom: 10px; /* Reduced margin for more compactness */
        }

        .form-control {
            height: 40px;
            font-size: 14px;
            padding: 10px;
        }

        .form-title {
    text-align: center;
    margin-bottom: 20px;
    font-size: 24px;
    font-weight: bold;
}


        .btn-primary {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: #1877f2;
            border-color: #1877f2;
            border-radius: 5px;
        }

        .btn-primary:hover {
            background-color: #1565c0;
            border-color: #1565c0;
        }

        .link-back {
            text-align: center;
            margin-top: 20px;
        }

        .link-back a {
            text-decoration: none;
            color: #1877f2;
            font-size: 14px;
        }

        .link-back a:hover {
            text-decoration: underline;
        }

        /* Styling for the horizontal fields */
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
            min-width: 220px; /* Ensures the input fields stay on the same row in larger screens */
        }

        .form-row .form-group:last-child {
            margin-right: 0;
        }

        /* For smaller screens */
        @media (max-width: 768px) {
            .login-container {
                width: 90%;
            }

            .form-row {
                flex-direction: column;
            }
        }
    </style>
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
                    <label for="construction_address">Construction Address</label>
                    <input type="text" class="form-control" id="construction_address" name="construction_address" required>
                </div>
                <div class="form-group">
                    <label for="date_of_request">Date of Request</label>
                    <input type="date" class="form-control" id="date_of_request" name="date_of_request" value="<?php echo date('Y-m-d'); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="homeowner_name">Name of Homeowner</label>
                    <input type="text" class="form-control" id="homeowner_name" name="homeowner_name" value="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="contractor_name">Name of Contractor</label>
                    <input type="text" class="form-control" id="contractor_name" name="contractor_name" required>
                </div>
                <div class="form-group">
                    <label for="contractor_contact">Contact Number of Contractor</label>
                    <input type="text" class="form-control" id="contractor_contact" name="contractor_contact" required>
                </div>
                <div class="form-group">
                    <label for="activity_nature">Nature of Activity</label>
                    <select class="form-control" id="activity_nature" name="activity_nature" required>
                        <option value="Repairs">Repairs</option>
                        <option value="Minor Construction">Minor Construction</option>
                        <option value="Construction">Construction</option>
                        <option value="Demolition">Demolition</option>
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
                <div class="form-group" id="other_utility_type_group" style="display: none;">
                    <label for="other_utility_type">Specify Other Utility Type</label>
                    <input type="text" class="form-control" id="other_utility_type" name="other_utility_type">
                </div>
                <script>
                    document.getElementById('utility_type').addEventListener('change', function() {
                        const otherUtilityGroup = document.getElementById('other_utility_type_group');
                        if (this.value === 'Others') {
                            otherUtilityGroup.style.display = 'block';
                        } else {
                            otherUtilityGroup.style.display = 'none';
                            document.getElementById('other_utility_type').value = '';
                        }
                    });
                </script>



                <div class="form-group">
                    <label for="date_of_request">Date of Request</label>
                    <input type="date" class="form-control" id="date_of_request" name="date_of_request" value="<?php echo date('Y-m-d'); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="date_of_work">Date of Work</label>
                    <input type="date" class="form-control" id="date_of_work" name="date_of_work" required>
                </div>
                <div class="form-group">
                    <label for="contact_no">Contact No.</label>
                    <input type="text" class="form-control" id="contact_no" name="contact_no" required>
                </div>
                <div class="form-group">
                    <label for="service_provider">Service Provider</label>
                    <select class="form-control" id="service_provider" name="service_provider" required>
                        <option value="Meralco">Meralco</option>
                        <option value="Globe">Globe</option>
                        <option value="PLDT">PLDT</option>
                        <option value="Sky Cable">Sky Cable</option>
                        <option value="CIGNAL">CIGNAL</option>
                        <option value="Manila Water">Manila Water</option>
                        <option value="Smart">Smart</option>
                        <option value="Bayantel">Bayantel</option>
                        <option value="Destiny">Destiny</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
                <div class="form-group" id="other_service_provider_group" style="display: none;">
                    <label for="other_service_provider">Specify Other Service Provider</label>
                    <input type="text" class="form-control" id="other_service_provider" name="other_service_provider">
                </div>
                <div class="form-group">
                    <label for="nature_of_work">Nature of Work</label>
                    <select class="form-control" id="nature_of_work" name="nature_of_work" required>
                        <option value="New installation">New installation</option>
                        <option value="Repair/Maintenance">Repair/Maintenance</option>
                        <option value="Permanent Disconnection">Permanent Disconnection</option>
                        <option value="Reconnection">Reconnection</option>
                    </select>
                </div>
                <script>
                    document.getElementById('service_provider').addEventListener('change', function() {
                        const otherGroup = document.getElementById('other_service_provider_group');
                        if (this.value === 'Others') {
                            otherGroup.style.display = 'block';
                        } else {
                            otherGroup.style.display = 'none';
                            document.getElementById('other_service_provider').value = '';
                        }
                    });
                </script>



            <?php elseif ($document_type === 'certificate_of_residency'): ?>
                <div class="form-group">
                    <label for="birthdate">Birthdate</label>
                    <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($user['birthdate']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="resident_since">Resident of Blue Ridge B Since</label>
                    <input type="date" class="form-control" id="resident_since" name="resident_since" required>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" readonly>
                </div>
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
                    <label for="income">Income</label>
                    <input type="number" class="form-control" id="income" name="income" required>
                </div>



            <?php elseif ($document_type === 'business_clearance'): ?>
                <div class="form-group">
                    <label for="clearance_image">Attach Existing Clearance Image</label>
                    <input type="file" class="form-control" id="clearance_image" name="clearance_image" required>
                </div>



            <?php elseif ($document_type === 'new_business_permit'): ?>
                <div class="form-group">
                    <label for="owner">Owner</label>
                    <input type="text" class="form-control" id="owner" name="owner" value="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" class="form-control" id="location" name="location" required>
                </div>
                <div class="form-group">
                    <label for="business_name">Name of Business</label>
                    <input type="text" class="form-control" id="business_name" name="business_name" required>
                </div>
                <div class="form-group">
                    <label for="nature_of_business">Nature of Business</label>
                    <input type="text" class="form-control" id="nature_of_business" name="nature_of_business" required>
                </div>
                <div class="form-group">
                    <label for="business_type">Business Type</label>
                    <select class="form-control" id="business_type" name="business_type" required>
                        <option value="Solo">Solo</option>
                        <option value="Shared">Shared</option>
                    </select>
                </div>
                <div class="form-group" id="co_owner_group" style="display: none;">
                    <label for="co_owner">Co-owner</label>
                    <input type="text" class="form-control" id="co_owner" name="co_owner">
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" readonly>
                </div>

                <script>
                    document.getElementById('business_type').addEventListener('change', function() {
                        const coOwnerGroup = document.getElementById('co_owner_group');
                        if (this.value === 'Shared') {
                            coOwnerGroup.style.display = 'block';
                        } else {
                            coOwnerGroup.style.display = 'none';
                            document.getElementById('co_owner').value = '';
                        }
                    });
                </script>

                
            <?php elseif ($document_type === 'clearance_major_construction'): ?>
                <div class="form-group">
                    <label for="schedule">Schedule</label>
                    <input type="datetime-local" class="form-control" id="schedule" name="schedule" required>
                </div>
                <div class="form-group">
                    <label for="contractor">Contractor</label>
                    <input type="text" class="form-control" id="contractor" name="contractor" required>
                </div>
                <div class="form-group">
                    <label for="construction_address">Construction Address</label>
                    <input type="text" class="form-control" id="construction_address" name="construction_address" required>
                </div>
                <div class="form-group">
                    <label for="infrastructures">Infrastructures</label>
                    <input type="text" class="form-control" id="infrastructures" name="infrastructures" required>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary mt-3">Submit Request</button>
        </form>
        <a href="index.php" class="btn btn-secondary mt-3">Back</a>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Your Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Please review the details of your request below:</p>
                    <ul id="requestDetails" class="list-unstyled">
                        <!-- Details will be dynamically populated here -->
                    </ul>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="confirmCheckbox">
                        <label class="form-check-label" for="confirmCheckbox">
                            I understand and wish to proceed. I acknowledge that this request cannot be undone and my personal information will be recorded permanently.
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmSubmitBtn" disabled>Submit Request</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Request Submitted Successfully</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Your request has been successfully received. Please wait for a response from a barangay official or employee.</p>
                    <p><strong>Control Number:</strong> <span id="controlNumber"></span></p>
                    <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form');
        const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        const confirmCheckbox = document.getElementById('confirmCheckbox');
        const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
        const requestDetails = document.getElementById('requestDetails');
        const controlNumber = document.getElementById('controlNumber');

        confirmCheckbox.addEventListener('change', function () {
            confirmSubmitBtn.disabled = !this.checked;
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            requestDetails.innerHTML = `
                <li><strong>Name:</strong> ${document.getElementById('name').value}</li>
                <li><strong>Email:</strong> ${document.getElementById('email').value}</li>
                <li><strong>Address:</strong> ${document.getElementById('address').value}</li>
                <li><strong>Document Type:</strong> <?php echo htmlspecialchars($document_title); ?></li>
            `;

            confirmationModal.show();
        });

        confirmSubmitBtn.addEventListener('click', function () {
            confirmationModal.hide();

            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    controlNumber.textContent = data.control_number;
                    successModal.show();
                } else {
                    alert('An error occurred while submitting your request. Please try again.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>
</html>