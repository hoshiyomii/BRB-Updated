<?php   
        $sql = "SELECT 
                    cr.id, 
                    CONCAT(u.last_name, ', ', u.first_name) AS full_name, 
                    CONCAT(u.house_number, ' ', u.street) AS address, 
                    u.birthdate, 
                    cr.resident_since, 
                    cr.id_image, 
                    cr.created_at,
                    cr.status,
                    cr.approved_by,
                    cr.rejection_reason,
                    cr.pickup_schedule,
                    cr.time_approved,
                    cr.rejected_by,
                    cr.time_rejected 
                FROM certificate_of_residency cr
                JOIN users u ON cr.user_id = u.id";