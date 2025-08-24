<?php
session_start();
include 'db.php';

function generateControlNumber($documentType, $id) {
    $prefix = '';
    switch ($documentType) {
        case 'Certificate of Indigency':
            $prefix = 'CI';
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

// Fetch audit logs for this user
$auditLogsQuery = $conn->prepare("SELECT * FROM user_audit_logs WHERE user_id = ? ORDER BY created_at DESC");
$auditLogsQuery->bind_param("i", $userId);
$auditLogsQuery->execute();
$auditLogs = $auditLogsQuery->get_result();

// Pagination logic
$itemsPerPage = 3;

// Get the current page for each tab
$pendingRequestsPage = isset($_GET['pendingRequestsPage']) ? (int)$_GET['pendingRequestsPage'] : 1;
$joinedEventsPage = isset($_GET['joinedEventsPage']) ? (int)$_GET['joinedEventsPage'] : 1;
$pendingReservationsPage = isset($_GET['pendingReservationsPage']) ? (int)$_GET['pendingReservationsPage'] : 1;
$facilitiesPage = isset($_GET['facilitiesPage']) ? (int)$_GET['facilitiesPage'] : 1;

// Calculate offsets
$pendingRequestsOffset = ($pendingRequestsPage - 1) * $itemsPerPage;
$joinedEventsOffset = ($joinedEventsPage - 1) * $itemsPerPage;
$pendingReservationsOffset = ($pendingReservationsPage - 1) * $itemsPerPage;
$facilitiesOffset = ($facilitiesPage - 1) * $itemsPerPage;

// Fetch pending requests with pagination
$pendingRequestsQuery = $conn->prepare("
    SELECT id, document_type, created_at, status, pickup_schedule, rejection_reason 
    FROM (
        SELECT id, 'Certificate of Indigency' AS document_type, created_at, status, pickup_schedule, rejection_reason FROM certificate_of_indigency WHERE user_id = ?
        UNION ALL
        SELECT id, 'Certificate of Residency' AS document_type, created_at, status, pickup_schedule, rejection_reason FROM certificate_of_residency WHERE user_id = ?
        UNION ALL
        SELECT id, 'Clearance for Major Construction' AS document_type, created_at, status, pickup_schedule, rejection_reason FROM clearance_major_construction WHERE user_id = ?
        UNION ALL
        SELECT id, 'New Business Permit' AS document_type, created_at, status, pickup_schedule, rejection_reason FROM new_business_permit WHERE user_id = ?
        UNION ALL
        SELECT id, 'Repair and Construction' AS document_type, created_at, status, pickup_schedule, rejection_reason FROM repair_and_construction WHERE user_id = ?
        UNION ALL
        SELECT id, 'Work Permit for Utilities' AS document_type, created_at, status, pickup_schedule, rejection_reason FROM work_permit_utilities WHERE user_id = ?
    ) AS combined_requests
    ORDER BY created_at DESC
    LIMIT ? OFFSET ?
");
$pendingRequestsQuery->bind_param("iiiiiiii", $userId, $userId, $userId, $userId, $userId, $userId, $itemsPerPage, $pendingRequestsOffset);
$pendingRequestsQuery->execute();
$pendingRequests = $pendingRequestsQuery->get_result();

// Fetch joined events with pagination
$joinedEventsQuery = $conn->prepare("
    SELECT r.*, a.title AS event_name, a.created_at AS event_date 
    FROM registrations r 
    JOIN announcements a ON r.announcement_id = a.id 
    WHERE r.user_id = ?
    ORDER BY a.created_at DESC
    LIMIT ? OFFSET ?
");
$joinedEventsQuery->bind_param("iii", $userId, $itemsPerPage, $joinedEventsOffset);
$joinedEventsQuery->execute();
$joinedEvents = $joinedEventsQuery->get_result();

// Fetch pending reservations with pagination
$reservationsQuery = $conn->prepare("
    SELECT id, venue_type, start_time, end_time, created_at, total_cost, status 
    FROM reservations 
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT ? OFFSET ?
");
$reservationsQuery->bind_param("iii", $userId, $itemsPerPage, $pendingReservationsOffset);
$reservationsQuery->execute();
$pendingReservations = $reservationsQuery->get_result();

// Pagination logic for facilities reservations
$facilitiesPage = isset($_GET['facilitiesPage']) ? (int)$_GET['facilitiesPage'] : 1;
$facilitiesOffset = ($facilitiesPage - 1) * $itemsPerPage;

// Fetch pending facilities reservations with pagination
$facilitiesQuery = $conn->prepare("
    SELECT id, facility_type, start_time, end_time, created_at, total_cost, status 
    FROM facilities_reservations 
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT ? OFFSET ?
");
$facilitiesQuery->bind_param("iii", $userId, $itemsPerPage, $facilitiesOffset);
$facilitiesQuery->execute();
$facilitiesReservations = $facilitiesQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/index_head.php'; ?>
<link rel="stylesheet" href="dashboard.css">
<body>
<?php include 'includes/index_header.php'; ?>

    <!-- Content Wrapper Start -->
    <div class="content-wrapper">
        <div class="container mt-5">
            <h1 class="text-center">User Dashboard</h1>
            <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Profile</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="audit-log-tab" data-bs-toggle="tab" data-bs-target="#audit-log" type="button" role="tab" aria-controls="audit-log" aria-selected="false">Action History</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-requests-tab" data-bs-toggle="tab" data-bs-target="#pending-requests" type="button" role="tab" aria-controls="pending-requests" aria-selected="false">Pending Requests</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="joined-events-tab" data-bs-toggle="tab" data-bs-target="#joined-events" type="button" role="tab" aria-controls="joined-events" aria-selected="false">Joined Events</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-sports-venue-tab" data-bs-toggle="tab" data-bs-target="#pending-sports-venue" type="button" role="tab" aria-controls="pending-sports-venue" aria-selected="false">Sports Venue Reservations</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-facilities-tab" data-bs-toggle="tab" data-bs-target="#pending-facilities" type="button" role="tab" aria-controls="pending-facilities" aria-selected="false">Facilities Reservations</button>
                </li>
            </ul>
            <div class="tab-content" id="dashboardTabsContent">
                <?php include 'profile_tab.php'; ?>
                <?php include 'audit_log_tab.php'; ?>
                <?php include 'pending_requests_tab.php'; ?>
                <?php include 'joined_events_tab.php'; ?>
                <?php include 'pending_sports_venue_tab.php'; ?>
                <?php include 'pending_facilities_tab.php'; ?>
            </div>
        </div>
    </div>
    <!-- Content Wrapper End -->
    <?php include 'includes/logout_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>

    <?php include 'includes/footer.php'; ?>

    <script>
        function showReservations(type) {
            const sportsVenueSection = document.getElementById('sportsVenueReservations');
            const facilitiesSection = document.getElementById('facilitiesReservations');
            const sportsVenueToggle = document.getElementById('sportsVenueToggle');
            const facilitiesToggle = document.getElementById('facilitiesToggle');

            if (type === 'sportsVenue') {
                sportsVenueSection.style.display = 'block';
                facilitiesSection.style.display = 'none';
                sportsVenueToggle.classList.add('btn-primary');
                sportsVenueToggle.classList.remove('btn-secondary');
                facilitiesToggle.classList.add('btn-secondary');
                facilitiesToggle.classList.remove('btn-primary');
            } else if (type === 'facilities') {
                sportsVenueSection.style.display = 'none';
                facilitiesSection.style.display = 'block';
                facilitiesToggle.classList.add('btn-primary');
                facilitiesToggle.classList.remove('btn-secondary');
                sportsVenueToggle.classList.add('btn-secondary');
                sportsVenueToggle.classList.remove('btn-primary');
            }
        }

        function showReservationsDropdown(value) {
            const sportsVenueSection = document.getElementById('sportsVenueReservations');
            const facilitiesSection = document.getElementById('facilitiesReservations');

            if (value === 'sportsVenue') {
                sportsVenueSection.style.display = 'block';
                facilitiesSection.style.display = 'none';
            } else if (value === 'facilities') {
                sportsVenueSection.style.display = 'none';
                facilitiesSection.style.display = 'block';
            }
        }

        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('click', function () {
                const url = new URL(window.location);
                url.searchParams.set('activeTab', this.getAttribute('data-bs-target').substring(1));
                history.pushState({}, '', url);
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('activeTab');

            if (activeTab) {
                const tabButton = document.querySelector(`[data-bs-target="#${activeTab}"]`);
                const tabContent = document.querySelector(`#${activeTab}`);

                if (tabButton && tabContent) {
                    // Deactivate all tabs
                    document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
                    document.querySelectorAll('.tab-pane').forEach(content => content.classList.remove('show', 'active'));

                    // Activate the selected tab
                    tabButton.classList.add('active');
                    tabContent.classList.add('show', 'active');
                }
            }
        });
    </script>
</body>
</html>

