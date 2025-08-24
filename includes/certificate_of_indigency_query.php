<?php  
        $sql = "SELECT 
                    ci.id, 
                    CONCAT(u.last_name, ', ', u.first_name) AS full_name, 
                    CONCAT(u.house_number, ' ', u.street) AS address, 
                    ci.occupancy, 
                    ci.purpose, 
                    ci.created_at, 
                    ci.status,
                    ci.approved_by,
                    ci.rejection_reason,
                    ci.pickup_schedule,
                    ci.time_approved,
                    ci.rejected_by,
                    ci.time_rejected
                FROM certificate_of_indigency ci
                JOIN users u ON ci.user_id = u.id";