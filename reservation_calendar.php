<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Include database connection

// Get approved reservations from both sports venues and facilities
$sportsQuery = "SELECT 
                    'Sports Venue' as type, 
                    id, 
                    venue_type as facility_name,
                    start_time, 
                    end_time, 
                    total_cost, 
                    approved_by, 
                    time_approved,
                    user_id
                FROM 
                    reservations 
                WHERE 
                    status = 'approved'
                ORDER BY 
                    start_time";

$sportsResult = $conn->query($sportsQuery);

$facilitiesQuery = "SELECT 
                       'Facility' as type,
                       id,
                       facility_type as facility_name,
                       start_time,
                       end_time,
                       total_cost, 
                       approved_by, 
                       time_approved,
                       user_id
                    FROM 
                       facilities_reservations 
                    WHERE 
                       status = 'approved'
                    ORDER BY 
                       start_time";

$facilitiesResult = $conn->query($facilitiesQuery);

// Combine results into one array
$reservations = [];

// Process sports venue results
if ($sportsResult) {
    while ($row = $sportsResult->fetch_assoc()) {
        // Get user details
        $userQuery = "SELECT first_name, last_name FROM users WHERE id = " . $row['user_id'];
        $userResult = $conn->query($userQuery);
        if ($userResult && $userResult->num_rows > 0) {
            $user = $userResult->fetch_assoc();
            $row['reserved_by'] = $user['first_name'] . ' ' . $user['last_name'];
        } else {
            $row['reserved_by'] = 'Unknown';
        }
        $reservations[] = $row;
    }
}

// Process facilities results
if ($facilitiesResult) {
    while ($row = $facilitiesResult->fetch_assoc()) {
        // Get user details
        $userQuery = "SELECT first_name, last_name FROM users WHERE id = " . $row['user_id'];
        $userResult = $conn->query($userQuery);
        if ($userResult && $userResult->num_rows > 0) {
            $user = $userResult->fetch_assoc();
            $row['reserved_by'] = $user['first_name'] . ' ' . $user['last_name'];
        } else {
            $row['reserved_by'] = 'Unknown';
        }
        $reservations[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Calendar</title>
    <?php include 'includes/admin_head.php'; ?>
    <link href="admin_dashboard.css" rel="stylesheet">
    <link href="reservation_calendar_modal.css" rel="stylesheet">
    
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css" rel="stylesheet">
    <!-- Bootstrap Modal CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        #calendar {
            width: 100%;
            margin: 0 auto;
        }
        
        /* Improved color contrast for better readability */
        .fc-event.sports-venue {
            background-color: #3050c0;
            border-color: #3050c0;
            color: #ffffff;
            font-weight: 600;
            cursor: pointer;
        }
        
        .fc-event.facility {
            background-color: #00994d;
            border-color: #00994d;
            color: #ffffff;
            font-weight: 600;
            cursor: pointer;
        }
        
        /* Event title styling to improve readability */
        .fc-event-title {
            padding: 2px 4px;
            text-shadow: 0px 0px 2px rgba(0, 0, 0, 0.5);
        }
        
        .fc-event-time {
            font-weight: bold;
            text-shadow: 0px 0px 2px rgba(0, 0, 0, 0.5);
        }
        
        .legend {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }
        
        .legend-color {
            width: 15px;
            height: 15px;
            margin-right: 5px;
            border-radius: 3px;
        }
        
        .sports-venue-color {
            background-color: #3050c0;
        }
        
        .facility-color {
            background-color: #00994d;
        }
        
    /* Modal styling moved to reservation_calendar_modal.css */
    </style>
</head>
<body>
    <?php include 'includes/admin_navbar.php'; ?>

    <div class="container mt-5">
        <h1>Reservations Calendar</h1>
        <p>This calendar shows all approved reservations for facilities and sports venues.</p>
        
        <div class="legend">
            <div class="legend-item">
                <div class="legend-color sports-venue-color"></div>
                <div>Sports Venue (Court A, Court B)</div>
            </div>
            <div class="legend-item">
                <div class="legend-color facility-color"></div>
                <div>Facility (Meeting Room, Function Hall)</div>
            </div>
        </div>
        
        <div id="calendar"></div>
    </div>
    
    <!-- Reservation Details Modal (Updated UI) -->
    <div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content modal-animation">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservationModalLabel">Reservation Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-section">
                        <div class="detail-row">
                            <span class="detail-icon">
                                <i class="fas fa-hashtag"></i>
                            </span>
                            <div class="detail-label">Control Number:</div>
                            <div class="detail-value" id="modalControlNo"></div>
                        </div>
                        <div class="detail-row">
                            <span class="detail-icon">
                                <i class="fas fa-user"></i>
                            </span>
                            <div class="detail-label">Full Name:</div>
                            <div class="detail-value" id="modalFullName"></div>
                        </div>
                        <div class="detail-row">
                            <span class="detail-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <div class="detail-label">Venue/Facility:</div>
                            <div class="detail-value" id="modalVenueType"></div>
                        </div>
                        <div class="detail-row">
                            <span class="detail-icon">
                                <i class="fas fa-money-bill-wave text-primary"></i>
                            </span>
                            <div class="detail-label text-primary">Total Cost:</div>
                            <div class="detail-value text-primary fw-bold" id="modalTotalCost"></div>
                        </div>
                        <div class="detail-row">
                            <span class="detail-icon">
                                <i class="fas fa-hourglass-start"></i>
                            </span>
                            <div class="detail-label">Start Time:</div>
                            <div class="detail-value" id="modalStartTime"></div>
                        </div>
                        <div class="detail-row">
                            <span class="detail-icon">
                                <i class="fas fa-hourglass-end"></i>
                            </span>
                            <div class="detail-label">End Time:</div>
                            <div class="detail-value" id="modalEndTime"></div>
                        </div>
                        <div class="detail-row">
                            <span class="detail-icon">
                                <i class="fas fa-user-check text-success"></i>
                            </span>
                            <div class="detail-label">Approved by:</div>
                            <div class="detail-value approved-value" id="modalApprovedBy"></div>
                        </div>
                        <div class="detail-row">
                            <span class="detail-icon">
                                <i class="fas fa-clock text-success"></i>
                            </span>
                            <div class="detail-label">Time Approved:</div>
                            <div class="detail-value approved-value" id="modalTimeApproved"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Prepare events data from PHP
            const events = [
                <?php if (count($reservations) > 0): ?>
                <?php foreach ($reservations as $reservation): ?>
                {
                    id: '<?= $reservation['id'] ?>',
                    title: '<?= $reservation['facility_name'] ?>',
                    start: '<?= $reservation['start_time'] ?>',
                    end: '<?= $reservation['end_time'] ?>',
                    className: '<?= strtolower(str_replace(' ', '-', $reservation['type'])) ?>',
                    extendedProps: {
                        type: '<?= $reservation['type'] ?>',
                        facilityName: '<?= $reservation['facility_name'] ?>',
                        reservedBy: '<?= $reservation['reserved_by'] ?>',
                        cost: '<?= number_format($reservation['total_cost'], 2) ?> PHP',
                        approvedBy: '<?= $reservation['approved_by'] ?>',
                        approvalTime: '<?= date("F d, Y h:i A", strtotime($reservation['time_approved'])) ?>',
                        startTime: '<?= date("h:i A", strtotime($reservation['start_time'])) ?>',
                        endTime: '<?= date("h:i A", strtotime($reservation['end_time'])) ?>',
                        date: '<?= date("F d, Y", strtotime($reservation['start_time'])) ?>'
                    }
                },
                <?php endforeach; ?>
                <?php endif; ?>
                // Add a dummy event to prevent empty array error
                {
                    title: 'Calendar initialized',
                    start: new Date(),
                    display: 'none'
                }
            ];
            
            // Get modal elements
            const reservationModal = new bootstrap.Modal(document.getElementById('reservationModal'));
            
            // Initialize calendar
            try {
                const calendarEl = document.getElementById('calendar');
                
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                    },
                    events: events,
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    },
                    eventClick: function(info) {
                        try {
                            // Ignore the dummy event
                            if (info.event.display === 'none') return;

                            // Set modal header color based on event type
                            const modalHeader = document.querySelector('.modal-header');
                            if (modalHeader) {
                                modalHeader.className = 'modal-header';
                                if (info.event.classNames.includes('sports-venue')) {
                                    modalHeader.classList.add('sports-venue');
                                } else if (info.event.classNames.includes('facility')) {
                                    modalHeader.classList.add('facility');
                                }
                            }

                            // Helper to get element by id
                            const get = id => document.getElementById(id);
                            // Control Number: RSV-XXX (use event id, pad to 3 digits)
                            if (get('modalControlNo')) get('modalControlNo').textContent =
                                info.event.id ? `RSV-${String(info.event.id).padStart(3, '0')}` : '';
                            // Full Name
                            if (get('modalFullName')) get('modalFullName').textContent = info.event.extendedProps.reservedBy || '';
                            // Venue/Facility
                            if (get('modalVenueType')) get('modalVenueType').textContent = info.event.extendedProps.facilityName || '';
                            // Total Cost
                            if (get('modalTotalCost')) get('modalTotalCost').textContent = info.event.extendedProps.cost || '';
                            // Start Time
                            if (get('modalStartTime')) get('modalStartTime').textContent = info.event.extendedProps.startTime || '';
                            // End Time
                            if (get('modalEndTime')) get('modalEndTime').textContent = info.event.extendedProps.endTime || '';
                            // Approved By
                            if (get('modalApprovedBy')) get('modalApprovedBy').textContent = info.event.extendedProps.approvedBy || '';
                            // Time Approved
                            if (get('modalTimeApproved')) get('modalTimeApproved').textContent = info.event.extendedProps.approvalTime || '';

                            // Show the modal
                            reservationModal.show();
                        } catch (err) {
                            console.error("Error showing event details:", err);
                        }
                    },
                    // Make calendar responsive
                    height: 'auto',
                    contentHeight: 'auto',
                    // Improve mobile display
                    windowResize: function(view) {
                        if(window.innerWidth < 768) {
                            calendar.changeView('listMonth');
                        } else {
                            calendar.changeView('dayGridMonth');
                        }
                    }
                });
                
                // Initial responsive view check
                if(window.innerWidth < 768) {
                    calendar.changeView('listMonth');
                }
                
                calendar.render();
            } catch (err) {
                document.getElementById('calendar').innerHTML = 
                    '<div class="alert alert-danger">Error initializing calendar. Please try refreshing the page.</div>';
            }
        });
    </script>
</body>
</html>
