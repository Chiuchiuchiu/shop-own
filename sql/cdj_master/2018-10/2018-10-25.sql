ALTER TABLE `pm_order_newwindow_pdf`
ADD COLUMN `tax_amount` decimal(10, 2) DEFAULT 0 COMMENT '发票税额' AFTER `member_id`,
ADD COLUMN `not_tax_amount` decimal(10, 2) DEFAULT 0 COMMENT '发票不含税金额' AFTER `tax_amount`,
ADD COLUMN `fpjym` varchar(50) DEFAULT '' COMMENT '发票校验码' AFTER `not_tax_amount`;