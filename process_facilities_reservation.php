<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to make a reservation.']);
    exit();
}

// Fetch user information
$username = $_SESSION['username'];
$user_query = $conn->query("SELECT * FROM users WHERE username = '$username'");
$user = $user_query->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch form data
    $user_id = $user['id'];
    $facility_type = $_POST['facility_type'];
    $with_aircon = isset($_POST['with_aircon']) ? 1 : 0;
    $rooftop_option = isset($_POST['rooftop_option']) ? 1 : 0;
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $sound_system = isset($_POST['sound_system']) ? 1 : 0;
    $projector = isset($_POST['projector']) ? 1 : 0;
    $lifetime_table = isset($_POST['lifetime_table']) ? (int)$_POST['lifetime_table'] : 0;
    $lifetime_chair = isset($_POST['lifetime_chair']) ? (int)$_POST['lifetime_chair'] : 0;
    $long_table = isset($_POST['long_table']) ? (int)$_POST['long_table'] : 0;
    $monoblock_chair = isset($_POST['monoblock_chair']) ? (int)$_POST['monoblock_chair'] : 0;
    $group_over_50 = isset($_POST['group_over_50']) ? 1 : 0;


    // Calculate total hours
    $start = new DateTime($start_time);
    $end = new DateTime($end_time);
    $hours = ceil(($end->getTimestamp() - $start->getTimestamp()) / 3600);

    // Validate hours
    if (($facility_type === 'Multi Purpose Hall' || $facility_type === 'Community Center') && $hours < 4) {
        echo json_encode(['success' => false, 'message' => 'Reservations for Multi Purpose Hall and Community Center must be at least 4 hours.']);
        exit();
    }

    if ($hours < 1) {
        echo json_encode(['success' => false, 'message' => 'Reservations must be at least 1 hour for all facilities.']);
        exit();
    }

    // Fetch facility limits from the database
    $limits = [];
    $limits_query = $conn->query("SELECT facility_name, max_quantity FROM facility_limits");
    if ($limits_query) {
        while ($row = $limits_query->fetch_assoc()) {
            $limits[$row['facility_name']] = (int)$row['max_quantity'];
        }
    }

    // Validate requested quantities against limits
    $facility_errors = [];
    if (isset($limits['Life-time Table']) && $lifetime_table > $limits['Life-time Table']) {
        $facility_errors[] = 'Life-time Table (max: ' . $limits['Life-time Table'] . ')';
    }
    if (isset($limits['Life-time Chair']) && $lifetime_chair > $limits['Life-time Chair']) {
        $facility_errors[] = 'Life-time Chair (max: ' . $limits['Life-time Chair'] . ')';
    }
    if (isset($limits['Long Table']) && $long_table > $limits['Long Table']) {
        $facility_errors[] = 'Long Table (max: ' . $limits['Long Table'] . ')';
    }
    if (isset($limits['Monoblock Chair']) && $monoblock_chair > $limits['Monoblock Chair']) {
        $facility_errors[] = 'Monoblock Chair (max: ' . $limits['Monoblock Chair'] . ')';
    }
    if (!empty($facility_errors)) {
        echo json_encode(['success' => false, 'message' => 'You cannot reserve more than the available quantity for: ' . implode(', ', $facility_errors)]);
        exit();
    }

    // Calculate total cost
    $total_cost = 0;
    $is_mandatory_charges_applicable = 1;

    if ($facility_type === 'Session Hall') {
        $total_cost = $hours * 600; // 600 Php/hour
        $is_mandatory_charges_applicable = 0;
    } elseif ($facility_type === 'Conference Room') {
        $total_cost = $hours * 400; // 400 Php/hour
        $is_mandatory_charges_applicable = 0;
    } elseif ($facility_type === 'Small Meeting Room') {
        $total_cost = $hours * 200; // 200 Php/hour
        $is_mandatory_charges_applicable = 0;
    } else {
        // For facilities with mandatory charges
        if ($facility_type === 'Multi Purpose Hall') {
            $base_cost = $with_aircon ? 5000 : 3500;
            $extra_hour_cost = $with_aircon ? 1000 : 700;
        } elseif ($facility_type === 'Community Center') {
            $base_cost = $with_aircon ? 4000 : 3000;
            $extra_hour_cost = $with_aircon ? 800 : 600;
        }

        $extra_hours = $hours > 4 ? $hours - 4 : 0;
        $total_cost += $base_cost + ($extra_hours * $extra_hour_cost);

        if ($facility_type === 'Community Center' && $rooftop_option) {
            $total_cost += $hours * 600; // Rooftop cost per hour
        }

        // Add mandatory charges
        $total_cost += 1000; // Cash bond
        $total_cost += 250; // Security/Parking Assistance
        if ($group_over_50) $total_cost += 250; // Group over 50 guests
        $total_cost += 250; // Caretaker/Cleaning Post Event
        $total_cost += 100; // Sound system setup operator
    }

    // Add additional costs
    if ($sound_system) $total_cost += 1000;
    if ($projector) $total_cost += 1500;
    $total_cost += $lifetime_table * 150;
    $total_cost += $lifetime_chair * 50;
    $total_cost += $long_table * 200;
    $total_cost += $monoblock_chair * 10;

    // Insert reservation into the database
    $stmt = $conn->prepare("INSERT INTO facilities_reservations (user_id, facility_type, with_aircon, rooftop_option, start_time, end_time, sound_system, projector, lifetime_table, lifetime_chair, long_table, monoblock_chair, group_over_50, total_cost, is_mandatory_charges_applicable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississiiiiiiiii", $user_id, $facility_type, $with_aircon, $rooftop_option, $start_time, $end_time, $sound_system, $projector, $lifetime_table, $lifetime_chair, $long_table, $monoblock_chair, $group_over_50, $total_cost, $is_mandatory_charges_applicable);

    if ($stmt->execute()) {
        $reservation_id = $stmt->insert_id; // Get the inserted reservation ID
        $control_number = 'FAC-' . str_pad($reservation_id, 3, '0', STR_PAD_LEFT); // Add prefix and pad with zeros
        echo json_encode(['success' => true, 'reservation_id' => $control_number]); // Return the formatted control number
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save reservation.']);
    }

    $stmt->close();
    $conn->close();
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}
?>