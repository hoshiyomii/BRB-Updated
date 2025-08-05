<?php
        $sql = "SELECT 
                    nbp.id, 
                    nbp.owner, 
                    IFNULL(nbp.co_owner, 'N/A') AS co_owner, 
                    nbp.location, 
                    nbp.business_name, 
                    nbp.nature_of_business, 
                    nbp.business_type, 
                    nbp.created_at,
                    nbp.status,
                    nbp.approved_by,
                    nbp.rejection_reason,
                    nbp.pickup_schedule,
                    nbp.time_approved,
                    nbp.rejected_by,
                    nbp.time_rejected 
                FROM new_business_permit nbp";