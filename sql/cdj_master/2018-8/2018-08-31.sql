-- ---------------------- --
-- fpzz_log
-- ---------------------- --
ALTER TABLE `fpzz_log`
ADD COLUMN `pm_order_id` int(0) AFTER `pm_order_fpzz_id`,
ADD INDEX `pm_order_idx`(`pm_order_id`);