<?php
$sql = "SELECT 
            rc.id, 
            CONCAT(u.last_name, ', ', u.first_name) AS full_name, 
            rc.contractor_name, 
            rc.contractor_contact, 
            rc.activity_nature, 
            rc.construction_address,
            rc.created_at,
            rc.status,
            rc.approved_by,
            rc.rejection_reason,
            rc.pickup_schedule,
            rc.time_approved,
            rc.rejected_by,
            rc.time_rejected
        FROM repair_and_construction rc
        JOIN users u ON rc.user_id = u.id";