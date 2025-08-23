-- SQL script to update the blood_type column to accommodate "Unknown" value
ALTER TABLE `users` MODIFY `blood_type` varchar(10) DEFAULT NULL;

-- If you need to rollback this change
-- ALTER TABLE `users` MODIFY `blood_type` varchar(5) DEFAULT NULL;
