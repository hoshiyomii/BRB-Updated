<div class="tab-pane fade" id="joined-events" role="tabpanel" aria-labelledby="joined-events-tab">
    <h3 class="mt-4">Joined Events</h3>
    <?php
    // Fetch total number of joined events
    $totalEventsQuery = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM registrations r 
        JOIN announcements a ON r.announcement_id = a.id 
        WHERE r.user_id = ?
    ");
    $totalEventsQuery->bind_param("i", $userId);
    $totalEventsQuery->execute();
    $totalEventsResult = $totalEventsQuery->get_result();
    $totalEvents = $totalEventsResult->fetch_assoc()['total'];

    // Calculate total pages
    $totalEventsPages = ceil($totalEvents / $itemsPerPage);
    ?>

    <?php if ($joinedEvents->num_rows > 0): ?>
        <ul class="list-group">
            <?php while ($event = $joinedEvents->fetch_assoc()): ?>
                <li class="list-group-item">
                    <strong>Event Name:</strong> <?php echo htmlspecialchars($event['event_name']); ?><br>
                    <strong>Event Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?>
                </li>
            <?php endwhile; ?>
        </ul>
        <!-- Pagination Controls -->
        <nav>
            <ul class="pagination justify-content-center mt-3">
                <li class="page-item <?php if ($joinedEventsPage <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?activeTab=joined-events&joinedEventsPage=<?php echo $joinedEventsPage - 1; ?>">Previous</a>
                </li>
                <li class="page-item disabled">
                    <span class="page-link">Page <?php echo $joinedEventsPage; ?> of <?php echo $totalEventsPages; ?></span>
                </li>
                <li class="page-item <?php if ($joinedEventsPage >= $totalEventsPages) echo 'disabled'; ?>">
                    <a class="page-link" href="?activeTab=joined-events&joinedEventsPage=<?php echo $joinedEventsPage + 1; ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php else: ?>
        <p>No joined events.</p>
    <?php endif; ?>
</div>