ALTER TABLE `operation_log_201907`
DROP COLUMN `project_id`,
ADD COLUMN `project_id`  int(11) UNSIGNED NOT NULL AFTER `referrer`,
ADD INDEX `member_id` (`member_id`) USING BTREE ,
ROW_FORMAT=COMPACT;

ALTER TABLE `operation_log_201908`
DROP COLUMN `project_id`,
ADD COLUMN `project_id`  int(11) UNSIGNED NOT NULL AFTER `referrer`,
ADD INDEX `member_id` (`member_id`) USING BTREE ,
ROW_FORMAT=COMPACT;