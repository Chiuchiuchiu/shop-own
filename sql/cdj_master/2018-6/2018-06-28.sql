ALTER TABLE `butler_region`
ADD COLUMN `butler_auth_id` int(0) DEFAULT 0 AFTER `room_status`,
ADD INDEX `butler_auth_id_idx`(`butler_auth_id`);