ALTER TABLE `member_vote`
ADD COLUMN `house_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '业主房产id' AFTER `group`;

