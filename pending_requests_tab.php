<div class="tab-pane fade" id="pending-requests" role="tabpanel" aria-labelledby="pending-requests-tab">
    <h3 class="mt-4">Request History</h3>
    <?php
    // Fetch total number of pending requests
    $totalRequestsQuery = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM (
            SELECT id FROM certificate_of_indigency WHERE user_id = ?
            UNION ALL
            SELECT id FROM certificate_of_residency WHERE user_id = ?
            UNION ALL
            SELECT id FROM clearance_major_construction WHERE user_id = ?
            UNION ALL
            SELECT id FROM new_business_permit WHERE user_id = ?
            UNION ALL
            SELECT id FROM repair_and_construction WHERE user_id = ?
            UNION ALL
            SELECT id FROM work_permit_utilities WHERE user_id = ?
        ) AS combined_requests
    ");
    $totalRequestsQuery->bind_param("iiiiii", $userId, $userId, $userId, $userId, $userId, $userId);
    $totalRequestsQuery->execute();
    $totalRequestsResult = $totalRequestsQuery->get_result();
    $totalRequests = $totalRequestsResult->fetch_assoc()['total'];

    // Calculate total pages
    $totalRequestsPages = ceil($totalRequests / $itemsPerPage);
    ?>

    <?php if ($pendingRequests->num_rows > 0): ?>
        <ul class="list-group">
            <?php while ($request = $pendingRequests->fetch_assoc()): ?>
                <li class="list-group-item">
                    <strong>Control Number:</strong> <?php echo htmlspecialchars(generateControlNumber($request['document_type'], $request['id'])); ?><br>
                    <strong>Document Type:</strong> <?php echo htmlspecialchars($request['document_type']); ?><br>
                    <strong>Request Date:</strong> <?php echo htmlspecialchars($request['created_at']); ?><br>
                    <strong>Status:</strong> 
                    <?php 
                        if ($request['status'] === 'approved') {
                            echo '<span class="text-success">Approved</span>';
                            echo '<br><strong>Pickup Schedule:</strong> ' . htmlspecialchars($request['pickup_schedule']);
                        } elseif ($request['status'] === 'rejected') {
                            echo '<span class="text-danger">Declined</span>';
                            echo '<br><strong>Reason for Rejection:</strong> ' . htmlspecialchars($request['rejection_reason']);
                        } elseif ($request['status'] === 'picked_up') { // New Status
                            echo '<span class="text-info">Picked Up</span>';
                        } else {
                            echo '<span class="text-warning">Pending</span>';
                        }
                    ?>
                </li>
            <?php endwhile; ?>
        </ul>
        <!-- Pagination Controls -->
        <nav>
            <ul class="pagination justify-content-center mt-3">
                <li class="page-item <?php if ($pendingRequestsPage <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?activeTab=pending-requests&pendingRequestsPage=<?php echo $pendingRequestsPage - 1; ?>">Previous</a>
                </li>
                <li class="page-item disabled">
                    <span class="page-link">Page <?php echo $pendingRequestsPage; ?> of <?php echo $totalRequestsPages; ?></span>
                </li>
                <li class="page-item <?php if ($pendingRequestsPage >= $totalRequestsPages) echo 'disabled'; ?>">
                    <a class="page-link" href="?activeTab=pending-requests&pendingRequestsPage=<?php echo $pendingRequestsPage + 1; ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php else: ?>
        <p>No requests found.</p>
    <?php endif; ?>
</div>