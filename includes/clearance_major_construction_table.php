<?php
?>
<h2>Clearance for Major Construction Requests</h2>
<table class="table table-bordered table-striped mt-3">
    <thead class="thead-dark">
        <tr>
            <th>Control No.</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Construction Schedule</th>
            <th>Contractor</th>
            <th>Construction Address</th>
            <th>Infrastructures to be Built</th>
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
                <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                <td><?php echo date("F j, Y, g:i a", strtotime($row['construction_schedule'])); ?></td>
                <td><?php echo htmlspecialchars($row['contractor']); ?></td>
                <td><?php echo htmlspecialchars($row['construction_address']); ?></td>
                <td><?php echo htmlspecialchars($row['infrastructures']); ?></td>
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