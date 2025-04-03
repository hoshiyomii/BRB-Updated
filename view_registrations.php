<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch all events for the dropdown
$events_result = $conn->query("SELECT id, title FROM announcements WHERE type = 'event'");

// Get selected event ID from the form submission
$selected_event_id = isset($_POST['event_id']) ? $_POST['event_id'] : '';

// Fetch registrations with event titles, filtered by selected event if any
$sql = "SELECT registrations.*, announcements.title AS event_title 
        FROM registrations 
        JOIN announcements ON registrations.announcement_id = announcements.id";
if ($selected_event_id) {
    $sql .= " WHERE announcements.id = " . $conn->real_escape_string($selected_event_id);
}
$sql .= " ORDER BY registered_at DESC";
$result = $conn->query($sql);

// Check for errors
if (!$result) {
    die("Error fetching registrations: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Registrations</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">View Registrations</h1>

        <form method="POST" class="mb-3">
            <div class="form-group">
                <label for="event_id">Filter by Event:</label>
                <select name="event_id" id="event_id" class="form-control">
                    <option value="">All Events</option>
                    <?php while ($event = $events_result->fetch_assoc()): ?>
                        <option value="<?php echo $event['id']; ?>" <?php if ($event['id'] == $selected_event_id) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($event['title']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <form method="POST" action="export_registrations.php" class="mb-3">
            <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($selected_event_id); ?>">
            <button type="submit" class="btn btn-success">Export to Excel</button>
        </form>

        <table class="table table-bordered table-striped mt-3">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Event</th>
                    <th>Date Registered</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['event_title']); ?></td>
                        <td><?php echo date("F j, Y, g:i a", strtotime($row['registered_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <p><a href="admin_dashboard.php" class="btn btn-primary">Back to Admin Dashboard</a></p>
    </div>
</body>
</html>
