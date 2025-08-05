<?php
$sql = "SELECT 
            wp.id, 
            CONCAT(u.last_name, ', ', u.first_name) AS full_name, 
            wp.address, 
            wp.contact_no, 
            wp.nature_of_work, 
            wp.service_provider, 
            IF(wp.service_provider = 'Others', wp.other_service_provider, 'N/A') AS other_service_provider, 
            wp.utility_type, 
            IF(wp.utility_type = 'Others', wp.other_utility_type, 'N/A') AS other_utility_type,
            wp.date_of_work, 
            wp.created_at,
            wp.status,
            wp.approved_by,
            wp.rejection_reason,
            wp.pickup_schedule,
            wp.time_approved,
            wp.rejected_by,
            wp.time_rejected 
        FROM work_permit_utilities wp
        JOIN users u ON wp.user_id = u.id";
