-- 订单表添加费用类型字段
ALTER TABLE `pm_order`
ADD COLUMN `charge_type`  tinyint(1) UNSIGNED NOT NULL DEFAULT 1 AFTER `discount_status`;


