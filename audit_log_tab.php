<div class="tab-pane fade" id="audit-log" role="tabpanel" aria-labelledby="audit-log-tab">
    <h3 class="mt-4">Action History</h3>
    <?php if ($auditLogs->num_rows > 0): ?>
        <ul class="list-group">
            <?php while ($log = $auditLogs->fetch_assoc()): ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $log['action_type']))); ?></strong><br>
                    <?php echo nl2br(htmlspecialchars($log['action_details'])); ?><br>
                    <small class="text-muted"><?php echo htmlspecialchars($log['created_at']); ?></small>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No actions recorded yet.</p>
    <?php endif; ?>
    <nav>
        <ul class="pagination justify-content-center mt-3">
            <li class="page-item <?php if ($auditLogPage <= 1) echo 'disabled'; ?>">
                <a class="page-link" href="?activeTab=audit-log&auditLogPage=<?php echo $auditLogPage - 1; ?>">Previous</a>
            </li>
            <li class="page-item <?php if ($auditLogs->num_rows < $itemsPerPage) echo 'disabled'; ?>">
                <a class="page-link" href="?activeTab=audit-log&auditLogPage=<?php echo $auditLogPage + 1; ?>">Next</a>
            </li>
        </ul>
    </nav>
</div>