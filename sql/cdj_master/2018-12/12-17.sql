-- ----------------
-- author HQM
--  pm_order 增加索引
-- ----------------
ALTER TABLE `pm_order`
ADD INDEX `pay_typeidx`(`pay_type`),
ADD INDEX `status_idx`(`status`);