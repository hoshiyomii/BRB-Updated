<div class="tab-pane fade" id="pending-sports-venue" role="tabpanel" aria-labelledby="pending-sports-venue-tab"><div class="tab-pane fade" id="pending-reservations" role="tabpanel" aria-labelledby="pending-reservations-tab">
    <h3 class="mt-4">Pending Sports Venue Reservations</h3>
    <?php if ($pendingReservations->num_rows > 0): ?>
        <ul class="list-group">
            <?php while ($reservation = $pendingReservations->fetch_assoc()): ?>
                <li class="list-group-item">on Type:</label>
                    <strong>Control Number:</strong> <?php echo 'RSV-' . str_pad($reservation['id'], 3, '0', STR_PAD_LEFT); ?><br>ct-sm" id="reservationTypeDropdown" onchange="showReservationsDropdown(this.value)">
                    <strong>Facility:</strong> <?php echo htmlspecialchars($reservation['venue_type']); ?><br>
                    <strong>Start and End Time:</strong> <?php echo date("F j, Y, g:i a", strtotime($reservation['start_time'])); ?> to <?php echo date("F j, Y, g:i a", strtotime($reservation['end_time'])); ?><br>
                    <strong>Request Date:</strong> <?php echo htmlspecialchars($reservation['created_at']); ?><br>
                    <strong>Total Cost:</strong> <?php echo number_format($reservation['total_cost'], 2); ?> Php<br>
                    <strong>Status:</strong> <span class="text-warning">Pending</span>
                </li>
            <?php endwhile; ?>nueReservations">
        </ul>rvations->num_rows > 0): ?>
    <?php else: ?>ul class="list-group">
        <p>No pending sports venue reservations.</p>ation = $pendingReservations->fetch_assoc()): ?>
    <?php endif; ?>       <li class="list-group-item">
    <!-- Pagination Controls -->echo 'RSV-' . str_pad($reservation['id'], 3, '0', STR_PAD_LEFT); ?><br>
    <nav>type']); ?><br>
        <ul class="pagination justify-content-center mt-3">, Y, g:i a", strtotime($reservation['end_time'])); ?><br>
            <li class="page-item <?php if ($pendingReservationsPage <= 1) echo 'disabled'; ?>">   <strong>Request Date:</strong> <?php echo htmlspecialchars($reservation['created_at']); ?><br>
                <a class="page-link" href="?activeTab=pending-sports-venue&pendingReservationsPage=<?php echo $pendingReservationsPage - 1; ?>">Previous</a><br>
            </li>
            <li class="page-item <?php if ($pendingReservations->num_rows < $itemsPerPage) echo 'disabled'; ?>">/li>
                <a class="page-link" href="?activeTab=pending-sports-venue&pendingReservationsPage=<?php echo $pendingReservationsPage + 1; ?>">Next</a>?php endwhile; ?>
            </li>ul>
        </ul>: ?>
    </nav></p>
</div>
    <!-- Facilities Reservations -->
    <div id="facilitiesReservations" style="display: none;">
        <?php
        // Fetch pending facilities reservations with pagination
        $facilitiesPage = isset($_GET['facilitiesPage']) ? (int)$_GET['facilitiesPage'] : 1;
        $facilitiesOffset = ($facilitiesPage - 1) * $itemsPerPage;

        $facilitiesQuery = $conn->prepare("
            SELECT id, facility_type, start_time, end_time, created_at, total_cost, status 
            FROM facilities_reservations 
            WHERE user_id = ? AND status = 'pending' 
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        $facilitiesQuery->bind_param("iii", $userId, $itemsPerPage, $facilitiesOffset);
        $facilitiesQuery->execute();
        $facilitiesReservations = $facilitiesQuery->get_result();
        ?>

        <?php if ($facilitiesReservations->num_rows > 0): ?>
            <ul class="list-group">
                <?php while ($reservation = $facilitiesReservations->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <strong>Control Number:</strong> <?php echo 'FAC-' . str_pad($reservation['id'], 3, '0', STR_PAD_LEFT); ?><br>
                        <strong>Facility Type:</strong> <?php echo htmlspecialchars($reservation['facility_type']); ?><br>
                        <strong>Start and End Time:</strong> <?php echo date("F j, Y, g:i a", strtotime($reservation['start_time'])); ?> to <?php echo date("F j, Y, g:i a", strtotime($reservation['end_time'])); ?><br>
                        <strong>Request Date:</strong> <?php echo htmlspecialchars($reservation['created_at']); ?><br>
                        <strong>Total Cost:</strong> <?php echo number_format($reservation['total_cost'], 2); ?> Php<br>
                        <strong>Status:</strong> <span class="text-warning">Pending</span>
                    </li>
                <?php endwhile; ?>
            </ul>
            <!-- Pagination Controls -->
            <nav>
                <ul class="pagination justify-content-center mt-3">
                    <li class="page-item <?php if ($facilitiesPage <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="?activeTab=pending-reservations&facilitiesPage=<?php echo $facilitiesPage - 1; ?>">Previous</a>
                    </li>
                    <li class="page-item <?php if ($facilitiesReservations->num_rows < $itemsPerPage) echo 'disabled'; ?>">
                        <a class="page-link" href="?activeTab=pending-reservations&facilitiesPage=<?php echo $facilitiesPage + 1; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php else: ?>
            <p>No pending facilities reservations.</p>
        <?php endif; ?>
    </div>
</div>