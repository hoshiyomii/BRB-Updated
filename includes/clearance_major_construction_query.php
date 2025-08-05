<?php
        $sql = "SELECT 
                    cmc.id, 
                    CONCAT(u.last_name, ', ', u.first_name) AS full_name, 
                    cmc.schedule AS construction_schedule, 
                    cmc.contractor, 
                    cmc.construction_address, 
                    cmc.infrastructures, 
                    cmc.created_at,
                    cmc.status,
                    cmc.approved_by,
                    cmc.rejection_reason,
                    cmc.pickup_schedule,
                    cmc.time_approved,
                    cmc.rejected_by,
                    cmc.time_rejected
                FROM clearance_major_construction cmc
                JOIN users u ON cmc.user_id = u.id";