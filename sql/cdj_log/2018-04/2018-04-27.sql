ALTER TABLE `visit_house_owner_notify_log`
ADD COLUMN `visit_house_owner_id` int(0) AFTER `id`,
ADD INDEX `visit_house_owner_idx`(`visit_house_owner_id`);