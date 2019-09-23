-- ----------------------- ---
-- project_parking_one_to_one 表增加 type 字段
-- ----------------------- --
ALTER TABLE `project_parking_one_to_one`
ADD COLUMN `type` tinyint(2) DEFAULT 1 AFTER `project_house_id`;

-- ------------------------ --
-- parking_order 增加 parkin_type 字段
-- ------------------------ --
ALTER TABLE `parking_order`
  ADD COLUMN `parking_type` tinyint(2) DEFAULT 1 AFTER `pay_type`;

-- ------------------------ --
-- pm_order 增加 house_type 字段
-- ------------------------ --
ALTER TABLE `pm_order`
ADD COLUMN `house_type` tinyint(2) DEFAULT 0 AFTER `project_house_id`;

ALTER TABLE `pm_order`
  DROP INDEX `house_id`,
  ADD INDEX `house_id_type`(`house_id`, `house_type`) USING BTREE;