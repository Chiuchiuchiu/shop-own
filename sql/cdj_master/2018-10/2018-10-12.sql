-- 添加二次核销字段 --
ALTER TABLE `pm_order_item`
ADD COLUMN `second_updated_at`  int(11) UNSIGNED NULL COMMENT '二次核销时间' AFTER `usage_amount`;