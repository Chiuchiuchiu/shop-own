ALTER TABLE `question_answer` ADD `chose_ancestor_name` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '待调研表地址' AFTER `ancestor_name`;


-- ----------------------- --
--
-- ----------------------- --
ALTER TABLE `pm_order_item`
ADD INDEX `con_status_idx`(`contract_no`, `status`);