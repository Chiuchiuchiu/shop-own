CREATE TABLE `min_autumn_red_pack` (
`id`  int(11) UNSIGNED NULL AUTO_INCREMENT ,
`project_id`  int(10) UNSIGNED NULL DEFAULT 0 ,
`house_id`  int(10) UNSIGNED NULL DEFAULT 0 ,
`member_id`  int(10) UNSIGNED NULL DEFAULT 0 ,
`wechat_mch_id`  char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '关联微信红包记录id',
`sure_name`  varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '业主姓名',
`answer`  varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '业主提交的答案，key为题目id，json',
`amount`  int(10) UNSIGNED NULL DEFAULT 0 COMMENT '领用金额（单位：分）' ,
`status`  tinyint(1) UNSIGNED NULL DEFAULT 4 COMMENT '状态：1未答题2已答（合格）3已答（不合格）4作废' ,
`created_at`  int(11) UNSIGNED NULL ,
`updated_at`  int(11) UNSIGNED NULL ,
PRIMARY KEY (`id`),
INDEX `member_id` (`member_id`) USING BTREE ,
INDEX `house_id` (`house_id`) USING BTREE ,
INDEX `project_id` (`project_id`) USING BTREE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='中秋答题记录'
ROW_FORMAT=COMPACT;


CREATE TABLE `min_autumn_question` (
`id`  int(11) UNSIGNED NULL AUTO_INCREMENT ,
`title`  varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '题目' ,
`answer`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '答案，json' ,
`answer_true`  int(2) NULL DEFAULT '-1' COMMENT '正确答案，json的key' ,
`status`  tinyint(1) UNSIGNED NULL COMMENT '状态：1正常2作废' ,
`created_at`  int(11) UNSIGNED NULL DEFAULT 0 ,
`updated_at`  int(11) UNSIGNED NULL DEFAULT 0 ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='中秋题目'
ROW_FORMAT=COMPACT
;

