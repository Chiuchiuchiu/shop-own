-- -----------------
-- 增加小程序用户 open_id
-- -----------------
ALTER TABLE `member`
ADD COLUMN `mp_open_id` varchar(50) DEFAULT '' COMMENT '小程序' AFTER `wechat_open_id`;

-- 添加区分住宅、写字楼的问卷字段
ALTER TABLE `question`
ADD COLUMN `type_id`  tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '写字楼、住宅的问卷，1公共，2住宅，3写字楼' AFTER `site`;
ADD COLUMN `status`  tinyint(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1正常，2作废' AFTER `type_id`;
DROP COLUMN `uid`;

ALTER TABLE `question_project` DROP COLUMN `uid`;
ALTER TABLE `question_answer` DROP COLUMN `uid`;
ALTER TABLE `quest_cate` DROP COLUMN `uid`;

