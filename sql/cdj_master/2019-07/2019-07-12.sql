ALTER TABLE `third_party_view_history`
ADD COLUMN `click_place`  tinyint(2) UNSIGNED NULL DEFAULT 0 COMMENT '点击来源，1banner,2icon' AFTER `member_nickname`;

ALTER TABLE `third_party_view_history`
ADD COLUMN `pic`  varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '图片' AFTER `click_place`;

