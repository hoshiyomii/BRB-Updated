<div class="tab-pane fade" id="pending-facilities" role="tabpanel" aria-labelledby="pending-facilities-tab">
    <h3 class="mt-4">Facilities Reservations</h3>
    <?php
    // Pagination logic for facilities reservations
    $facilitiesPage = isset($_GET['facilitiesPage']) ? (int)$_GET['facilitiesPage'] : 1;
    $facilitiesOffset = ($facilitiesPage - 1) * $itemsPerPage;

    // Fetch total number of facilities reservations
    $totalFacilitiesQuery = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM facilities_reservations 
        WHERE user_id = ?
    ");
    $totalFacilitiesQuery->bind_param("i", $userId);
    $totalFacilitiesQuery->execute();
    $totalFacilitiesResult = $totalFacilitiesQuery->get_result();
    $totalFacilities = $totalFacilitiesResult->fetch_assoc()['total'];

    // Calculate total pages
    $totalFacilitiesPages = ceil($totalFacilities / $itemsPerPage);

    // Fetch facilities reservations for the current page
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

    <?php if ($facilitiesReservations->num_rows > 0): ?>
        <ul class="list-group">
            <?php while ($reservation = $facilitiesReservations->fetch_assoc()): ?>
                <li class="list-group-item">
                    <strong>Control Number:</strong> <?php echo 'FAC-' . str_pad($reservation['id'], 3, '0', STR_PAD_LEFT); ?><br>
                    <strong>Facility Type:</strong> <?php echo htmlspecialchars($reservation['facility_type']); ?><br>
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
                <li class="page-item <?php if ($facilitiesPage <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?activeTab=pending-facilities&facilitiesPage=<?php echo $facilitiesPage - 1; ?>">Previous</a>
                </li>
                <li class="page-item disabled">
                    <span class="page-link">Page <?php echo $facilitiesPage; ?> of <?php echo $totalFacilitiesPages; ?></span>
                </li>
                <li class="page-item <?php if ($facilitiesPage >= $totalFacilitiesPages) echo 'disabled'; ?>">
                    <a class="page-link" href="?activeTab=pending-facilities&facilitiesPage=<?php echo $facilitiesPage + 1; ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php else: ?>
        <p>No facilities reservations found.</p>
    <?php endif; ?>
</div>