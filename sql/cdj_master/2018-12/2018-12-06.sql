-- -------------------
--
-- -------------------
ALTER TABLE `pm_order_fpzz`
  ADD COLUMN `request_number` tinyint(0) DEFAULT 0 AFTER `remarks`,
  ADD INDEX `stat_index`(`status`, `type`, `request_number`);