-- ----------------
-- 增加客户端区分 pm_order_fpzz
-- ----------------
ALTER TABLE `pm_order_fpzz`
ADD COLUMN `client_type` tinyint(2) DEFAULT 1 AFTER `request_number`;