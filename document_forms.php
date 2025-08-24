<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Calculate the user's age
$birthdate = new DateTime($user['birthdate']);
$today = new DateTime();
$age = $today->diff($birthdate)->y;

// Determine if the pickup checkbox should be checked and disabled
$pickupChecked = ($age >= 13 && $age <= 17) ? 'checked' : '';
$pickupDisabled = ($age >= 13 && $age <= 17) ? 'disabled' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $user['id'];

    if ($document_type === 'repair_and_construction') {
        $construction_address = $_POST['construction_address'];
        $date_of_request = date('Y-m-d'); // Automatically set to the current date
        $homeowner_name = $user['first_name'] . ' ' . $user['last_name']; // Default to requester's name
        $contractor_name = $_POST['contractor_name'];
        $contractor_contact = $_POST['contractor_contact'];
        $activity_nature = $_POST['activity_nature'];
        $pickup_name = isset($_POST['pickup_name']) ? $_POST['pickup_name'] : null;

        $stmt = $conn->prepare("INSERT INTO repair_and_construction (user_id, construction_address, date_of_request, homeowner_name, contractor_name, contractor_contact, activity_nature, pickup_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $user_id, $construction_address, $date_of_request, $homeowner_name, $contractor_name, $contractor_contact, $activity_nature, $pickup_name);
        $stmt->execute();

        $control_number = "RC-" . str_pad($stmt->insert_id, 3, "0", STR_PAD_LEFT);
        $stmt->close();

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'control_number' => $control_number]);
        exit();
    }

    if ($document_type === 'work_permit_utilities') {
        $date_of_request = date('Y-m-d');
        $date_of_work = $_POST['date_of_work'];
        $contact_no = $_POST['contact_no'];
        $address = $_POST['address'];
        $service_provider = $_POST['service_provider'];
        $other_service_provider = $service_provider === 'Others' ? $_POST['other_service_provider'] : null;
        $utility_type = $_POST['utility_type'];
        $other_utility_type = $utility_type === 'Others' ? $_POST['other_utility_type'] : null;
        $nature_of_work = $_POST['nature_of_work'];
        $pickup_name = isset($_POST['pickup_name']) ? $_POST['pickup_name'] : null;

        // Server-side validation for Date of Work
        $today = new DateTime();
        $oneWeekFromNow = (clone $today)->modify('+7 days');
        $dateOfWork = new DateTime($date_of_work);

        if ($dateOfWork < $oneWeekFromNow) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Date of work must be at least 1 week in the future.']);
            exit();
        }

        // Server-side validation for Contact Number
        if (!preg_match('/^\d{11}$/', $contact_no)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Contact number must be exactly 11 digits and contain only numbers.']);
            exit();
        }

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO work_permit_utilities (user_id, date_of_request, date_of_work, contact_no, address, service_provider, other_service_provider, utility_type, other_utility_type, nature_of_work, pickup_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssssss", $user_id, $date_of_request, $date_of_work, $contact_no, $address, $service_provider, $other_service_provider, $utility_type, $other_utility_type, $nature_of_work, $pickup_name);
        $stmt->execute();

        $control_number = "WPU-" . str_pad($stmt->insert_id, 3, "0", STR_PAD_LEFT);
        $stmt->close();

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'control_number' => $control_number]);
        exit();
    }

    if ($document_type === 'certificate_of_residency') {
        $resident_since = $_POST['resident_since'];
        $date = date('Y-m-d');
        $id_image = 'uploads/' . basename($_FILES['id_image']['name']);
        move_uploaded_file($_FILES['id_image']['tmp_name'], $id_image);
        $pickup_name = isset($_POST['pickup_name']) ? $_POST['pickup_name'] : null;

        $stmt = $conn->prepare("INSERT INTO certificate_of_residency (user_id, resident_since, date, id_image, pickup_name) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $resident_since, $date, $id_image, $pickup_name);
        $stmt->execute();

        $control_number = "CR-" . str_pad($stmt->insert_id, 3, "0", STR_PAD_LEFT);
        $stmt->close();

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'control_number' => $control_number]);
        exit();
    }

    if ($document_type === 'certificate_of_indigency') {
        $occupancy = $_POST['occupancy'];
        $purpose = $_POST['purpose']; // Replace income with purpose
        $pickup_name = isset($_POST['pickup_name']) ? $_POST['pickup_name'] : null;

        $stmt = $conn->prepare("INSERT INTO certificate_of_indigency (user_id, occupancy, purpose, pickup_name) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $conn->error]);
            exit();
        }

        $stmt->bind_param("isss", $user_id, $occupancy, $purpose, $pickup_name); // Replace income with purpose
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'error' => 'Execution error: ' . $stmt->error]);
            exit();
        }

        $control_number = "CI-" . str_pad($stmt->insert_id, 3, "0", STR_PAD_LEFT);
        $stmt->close();

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'control_number' => $control_number]);
        exit();
    }

    if ($document_type === 'new_business_permit') {
        $owner = $user['first_name'] . ' ' . $user['last_name'];
        $location = $user['street'] . ' ' . $user['house_number']; // Use the autofilled address field
        $business_name = $_POST['business_name'];
        $nature_of_business = $_POST['nature_of_business'];
        $business_type = $_POST['business_type'];
        $co_owner = $business_type === 'Shared' ? $_POST['co_owner'] : null;
        $pickup_name = isset($_POST['pickup_name']) ? $_POST['pickup_name'] : null;

        $stmt = $conn->prepare("INSERT INTO new_business_permit (user_id, owner, location, business_name, nature_of_business, business_type, co_owner, pickup_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $conn->error]);
            exit();
        }

        $stmt->bind_param("isssssss", $user_id, $owner, $location, $business_name, $nature_of_business, $business_type, $co_owner, $pickup_name);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'error' => 'Execution error: ' . $stmt->error]);
            exit();
        }

        $control_number = "NBP-" . str_pad($stmt->insert_id, 3, "0", STR_PAD_LEFT);
        $stmt->close();

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'control_number' => $control_number]);
        exit();
    }

    if ($document_type === 'clearance_major_construction') {
        $schedule = $_POST['schedule'];
        $contractor = $_POST['contractor'];
        $construction_address = $_POST['construction_address'];
        $infrastructures = $_POST['infrastructures'];
        $pickup_name = isset($_POST['pickup_name']) ? $_POST['pickup_name'] : null;

        // Server-side validation for Scheduled Date
        $today = new DateTime();
        $oneWeekFromNow = (clone $today)->modify('+7 days');
        $scheduledDate = new DateTime($schedule);

        if ($scheduledDate < $oneWeekFromNow) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'The scheduled date must be at least 1 week from now.']);
            exit();
        }

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO clearance_major_construction (user_id, schedule, contractor, construction_address, infrastructures, pickup_name) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user_id, $schedule, $contractor, $construction_address, $infrastructures, $pickup_name);
        $stmt->execute();

        $control_number = "CMC-" . str_pad($stmt->insert_id, 3, "0", STR_PAD_LEFT);
        $stmt->close();

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'control_number' => $control_number]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/index_head.php'; ?>
<link href="document_forms.css" rel="stylesheet">
<body>
<?php include 'includes/index_header.php'; ?>

<div class="container-fluid hero-landing p-0">
    <div class="row g-0 align-items-center min-vh-100 justify-content-center">
        <div class="col-lg-9 col-md-11 col-12 mx-auto">
            <div class="row gy-4 gx-2">
                <!-- Form Box -->
                <div class="col-lg-7">
                    <div class="register-card p-4">
                        <h2 class="text-center mb-4"><?php echo htmlspecialchars($document_title); ?> Request Form</h2>
                        <form id="documentForm" action="" method="POST" enctype="multipart/form-data">
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

                            <!-- Include the specific form for the document type -->
                            <?php include "form_includes/{$document_type}_form.php"; ?>

                            <!-- Checkbox for pickup -->
                            <div class="form-group mt-3">
                                <label>
                                    <input type="checkbox" id="pickupCheckbox" name="pickup" value="yes" <?php echo $pickupChecked; ?> <?php echo $pickupDisabled; ?>>
                                    Other people will pick this up on my behalf (check if yes)
                                </label>
                            </div>
                            <div class="form-group mt-3" id="pickupNameGroup" style="display: <?php echo ($pickupChecked ? 'block' : 'none'); ?>;">
                                <label for="pickupName">Name of Person Picking Up</label>
                                <input type="text" class="form-control" id="pickupName" name="pickup_name" required>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Submit Request</button>
                        </form>
                    </div>
                </div>

                <!-- Guidelines Box -->
                <div class="col-lg-5">
                    <div class="guidelines-box p-4 d-flex flex-column justify-content-center">
                        <h3 class="mb-3 text-center">Guidelines</h3>
                        <ul class="guideline-list">
                            <li><i class="fa fa-check-circle"></i> Fill out all required fields.</li>
                            <li><i class="fa fa-check-circle"></i> Ensure the information provided is accurate.</li>
                            <li><i class="fa fa-check-circle"></i> Attach all necessary documents if applicable.</li>
                            <li><i class="fa fa-check-circle"></i> Submit the form to proceed with your request.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    document.getElementById('pickupCheckbox').addEventListener('change', function () {
        const pickupNameField = document.getElementById('pickupName');
        
        if (this.checked) {
            pickupNameField.value = ''; // Clear the field for user input
            pickupNameField.removeAttribute('readonly'); // Allow editing
        } else {
            pickupNameField.value = 'N/A'; // Set default value
            pickupNameField.setAttribute('readonly', 'readonly'); // Prevent editing
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const pickupCheckbox = document.getElementById('pickupCheckbox');
        const pickupNameField = document.getElementById('pickupName');

        // Set default value to "N/A" if the checkbox is not checked on page load
        if (!pickupCheckbox.checked) {
            pickupNameField.value = 'N/A';
            pickupNameField.setAttribute('readonly', 'readonly');
        }
    });

    document.getElementById('pickupCheckbox').addEventListener('change', function () {
        const pickupNameGroup = document.getElementById('pickupNameGroup');
        pickupNameGroup.style.display = this.checked ? 'block' : 'none';
    });

    document.addEventListener('DOMContentLoaded', function () {
        const pickupCheckbox = document.getElementById('pickupCheckbox');
        const pickupNameGroup = document.getElementById('pickupNameGroup');

        // Ensure the pickup name field is always visible for minors
        pickupNameGroup.style.display = pickupCheckbox.checked ? 'block' : 'none';

        // Prevent changes to the checkbox if it is disabled
        pickupCheckbox.addEventListener('change', function () {
            if (pickupCheckbox.disabled) {
                pickupCheckbox.checked = true; // Reset to checked if disabled
            }
            pickupNameGroup.style.display = pickupCheckbox.checked ? 'block' : 'none';
        }); 
    });

    document.addEventListener('DOMContentLoaded', function () {
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        const successModalElement = document.getElementById('successModal');

        // Redirect to dashboard when the success modal is closed
        successModalElement.addEventListener('hidden.bs.modal', function () {
            window.location.href = 'dashboard.php';
        });

        const form = document.querySelector('form');
        const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
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
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    controlNumber.textContent = data.control_number;
                    successModal.show();
                } else {
                    alert(data.error || 'An error occurred while submitting your request.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>