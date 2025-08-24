<div class="tab-pane fade" id="pending-sports-venue" role="tabpanel" aria-labelledby="pending-sports-venue-tab">
    <h3 class="mt-4">Sports Venue Reservations</h3>
    <?php
    // Pagination logic for sports venue reservations
    $pendingReservationsPage = isset($_GET['pendingReservationsPage']) ? (int)$_GET['pendingReservationsPage'] : 1;
    $pendingReservationsOffset = ($pendingReservationsPage - 1) * $itemsPerPage;

    // Fetch total number of sports venue reservations
    $totalReservationsQuery = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM reservations 
        WHERE user_id = ?
    ");
    $totalReservationsQuery->bind_param("i", $userId);
    $totalReservationsQuery->execute();
    $totalReservationsResult = $totalReservationsQuery->get_result();
    $totalReservations = $totalReservationsResult->fetch_assoc()['total'];

    // Calculate total pages
    $totalReservationsPages = ceil($totalReservations / $itemsPerPage);

    // Fetch sports venue reservations for the current page
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
    ?>

    <?php if ($pendingReservations->num_rows > 0): ?>
        <ul class="list-group">
            <?php while ($reservation = $pendingReservations->fetch_assoc()): ?>
                <li class="list-group-item">
                    <strong>Control Number:</strong> <?php echo 'RSV-' . str_pad($reservation['id'], 3, '0', STR_PAD_LEFT); ?><br>
                    <strong>Facility:</strong> <?php echo htmlspecialchars($reservation['venue_type']); ?><br>
                    <strong>Start and End Time:</strong> <?php echo date("F j, Y, g:i a", strtotime($reservation['start_time'])); ?> to <?php echo date("F j, Y, g:i a", strtotime($reservation['end_time'])); ?><br>
                    <strong>Request Date:</strong> <?php echo htmlspecialchars($reservation['created_at']); ?><br>
                    <strong>Total Cost:</strong> <?php echo number_format($reservation['total_cost'], 2); ?> Php<br>
                    <strong>Status:</strong> <span class="<?php echo $reservation['status'] === 'approved' ? 'text-success' : ($reservation['status'] === 'rejected' ? 'text-danger' : 'text-warning'); ?>">
                        <?php echo ucfirst($reservation['status']); ?>
                    </span>
                </li>
            <?php endwhile; ?>
        </ul>
        <!-- Pagination Controls -->
        <nav>
            <ul class="pagination justify-content-center mt-3">
                <li class="page-item <?php if ($pendingReservationsPage <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?activeTab=pending-sports-venue&pendingReservationsPage=<?php echo $pendingReservationsPage - 1; ?>">Previous</a>
                </li>
                <li class="page-item disabled">
                    <span class="page-link">Page <?php echo $pendingReservationsPage; ?> of <?php echo $totalReservationsPages; ?></span>
                </li>
                <li class="page-item <?php if ($pendingReservationsPage >= $totalReservationsPages) echo 'disabled'; ?>">
                    <a class="page-link" href="?activeTab=pending-sports-venue&pendingReservationsPage=<?php echo $pendingReservationsPage + 1; ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php else: ?>
        <p>No sports venue reservations found.</p>
    <?php endif; ?>
</div>