<?php
?>
<h2>New Business Permit Requests</h2>
<table class="table table-bordered table-striped mt-3">
    <thead class="thead-dark">
        <tr>
            <th>Control No.</th>
            <th>Owner</th>
            <th>Co-Owner</th>
            <th>Location</th>
            <th>Business Name</th>
            <th>Nature of Business</th>
            <th>Business Type</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr style="
                <?php 
                    if ($row['status'] === 'approved') {
                        echo 'background-color: #d4edda;'; // Light Green for Approved
                    } elseif ($row['status'] === 'rejected') {
                        echo 'background-color: #f8d7da;'; // Light Red for Declined
                    } else {
                        echo 'background-color: #fff3cd;'; // Light Yellow for Pending
                    }
                ?>
            ">
                <td><?php echo generateControlNo($document_type, $row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['owner']); ?></td>
                <td><?php echo htmlspecialchars($row['co_owner']); ?></td>
                <td><?php echo htmlspecialchars($row['location']); ?></td>
                <td><?php echo htmlspecialchars($row['business_name']); ?></td>
                <td><?php echo htmlspecialchars($row['nature_of_business']); ?></td>
                <td><?php echo htmlspecialchars($row['business_type']); ?></td>
                <td><?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-primary btn-sm" onclick="printDocument(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Print" <?php echo $row['status'] !== 'approved' ? 'disabled' : ''; ?>>
                            <i class="bi bi-printer"></i>
                        </button>
                        <button class="btn btn-success btn-sm" onclick="showApproveModal(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Approve" <?php echo $row['status'] !== 'pending' ? 'disabled' : ''; ?>>
                            <i class="bi bi-check-lg"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="showDeclineModal(<?php echo $row['id']; ?>, '<?php echo $document_type; ?>')" title="Decline" <?php echo $row['status'] !== 'pending' ? 'disabled' : ''; ?>>
                            <i class="bi bi-x-lg"></i>
                        </button>
                        <button class="btn btn-info btn-sm" onclick="toggleDetails(<?php echo $row['id']; ?>)" title="Details">
                            <i class="bi bi-info-circle"></i>
                        </button>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>