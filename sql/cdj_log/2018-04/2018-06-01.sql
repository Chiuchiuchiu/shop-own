ALTER TABLE `pm_order_to_butler_error_log`
ADD INDEX `pmo_status_idx`(`pm_order_id`, `status`);