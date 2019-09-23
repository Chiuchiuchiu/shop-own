-- --------------------- --
-- alter table fpzz_log
-- --------------------- --
ALTER TABLE `fpzz_log`
ADD COLUMN `type` varchar(50) AFTER `id`;

-- --------------------- --
-- alter table pm_order_fpzz_pdf ----
-- --------------------- --
ALTER TABLE `pm_order_fpzz_pdf`
ADD COLUMN `status` tinyint(2) DEFAULT 0 AFTER `project_house_id`,
ADD INDEX `gfmc_idx`(`gfmc`);

-- ------------------ --
-- alter table pm_order_fpzz_pdf
-- ------------------ --
ALTER TABLE `pm_order_fpzz_pdf`
  CHANGE COLUMN `fpid` `fpr_id` int(0) DEFAULT NULL AFTER `id`;

-- ------------------ --
--
-- ------------------ --
ALTER TABLE `pm_order_fpzz`
  ADD INDEX `type_created_idx`(`type`, `created_at`);

-- ----------------- --
--
-- ----------------- --
ALTER TABLE `butler_region`
  ADD INDEX `butler_idx`(`butler_id`);

-- -------------------- --
-- pm_order_item 更改索引
-- -------------------- --
ALTER TABLE `pm_order_item`
  DROP INDEX `pm_order_id`,
  ADD INDEX `pm_order_amount_id`(`pm_order_id`, `amount`) USING BTREE;

-- --------------------------- --
--
-- --------------------------- --
ALTER TABLE `pm_order_fpzz_pdf`
  ADD COLUMN `save_path` varchar(255) AFTER `url`;

-- ------------------------------
--
-- ------------------------------
ALTER TABLE `pm_order_fpzz_pdf`
  ADD INDEX `status_idx`(`status`);

-- ------------------------------
--
-- ------------------------------
ALTER TABLE `pm_order_fpzz_pdf`
  ADD COLUMN `processing_note` varchar(255) AFTER `member_id`;