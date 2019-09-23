ALTER TABLE `project_service_category`
DROP COLUMN `order_by`,
ADD COLUMN `order_by`  int(3) UNSIGNED NOT NULL COMMENT '排序，数字越大越靠前' AFTER `status`;

