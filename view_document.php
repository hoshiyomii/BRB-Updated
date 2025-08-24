<?php
session_start();

$document_type = isset($_GET['type']) ? $_GET['type'] : '';

$requirements = [
    'repair_and_construction' => [
        'title' => 'Repair and Construction',
        'definition' => 'This document is required for any repair or construction work within the barangay.',
        'requirements' => [
            'Proof of Identity',
            'Proof of Ownership',
            'Construction Plan',
        ],
    ],
    'work_permit_utilities' => [
        'title' => 'Work Permit For Utilities',
        'definition' => 'This document is required for utility-related work such as water, electricity, or internet installations.',
        'requirements' => [
            'Proof of Identity',
            'Utility Plan',
        ],
    ],
    'certificate_of_residency' => [
        'title' => 'Certificate of Residency',
        'definition' => 'This document certifies that the applicant is a resident of the barangay.',
        'requirements' => [
            'Proof of Identity',
            'Proof of Residency',
        ],
    ],
    'certificate_of_indigency' => [
        'title' => 'Certificate of Indigency',
        'definition' => 'This document certifies that the applicant belongs to the indigent sector of the barangay.',
        'requirements' => [
            'Proof of Identity',
            'Proof of Income',
        ],
    ],
    'business_clearance' => [
        'title' => 'Business Clearance',
        'definition' => 'This document is required for businesses operating within the barangay.',
        'requirements' => [
            'Proof of Identity',
            'Business Plan',
        ],
    ],
    'new_business_permit' => [
        'title' => 'New Business Permit',
        'definition' => 'This document is required to start a new business within the barangay.',
        'requirements' => [
            'Proof of Identity',
            'Business Plan',
        ],
    ],
    'clearance_major_construction' => [
        'title' => 'Clearance for Major Construction',
        'definition' => 'This document is required for major construction projects within the barangay.',
        'requirements' => [
            'Proof of Identity',
            'Construction Plan',
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
<?php include 'includes/index_head.php'; ?>
<link href="view_document.css" rel="stylesheet">
<body>
<?php include 'includes/index_header.php'; ?>

<div class="container-fluid hero-landing">
    <div class="row g-0 align-items-center min-vh-100 justify-content-center">
        <div class="col-lg-4 col-md-6 col-10 mx-auto"> <!-- Adjusted column width -->
            <div class="p-5 bg-white shadow rounded text-center info-box"> <!-- Increased padding -->
                <h2 class="mb-3"><?php echo htmlspecialchars($document['title']); ?></h2>
                <p class="mb-4"><?php echo htmlspecialchars($document['definition']); ?></p>
                <h4 class="mb-3">On-site Requirements</h4> <!-- Updated heading -->
                <ul class="list-unstyled mb-4 requirements-list"> <!-- Added class for styling -->
                    <?php foreach ($document['requirements'] as $requirement): ?>
                        <li><i class="fa fa-check-circle text-primary me-2"></i><?php echo htmlspecialchars($requirement); ?></li>
                    <?php endforeach; ?>
                </ul>
                <div class="d-flex justify-content-center">
                    <a href="document_forms.php?type=<?php echo urlencode($document_type); ?>" class="btn btn-primary me-2">Proceed to Request Form</a>
                    <a href="index.php" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>