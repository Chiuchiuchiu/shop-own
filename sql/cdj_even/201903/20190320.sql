-- 问卷调查送代金券记录表 --
CREATE TABLE `question_red_pack` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`project_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 ,
`house_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 ,
`member_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 ,
`amount`  decimal(6,2) NOT NULL DEFAULT 0 ,
`remark`  varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
`created_at`  int(10) UNSIGNED NOT NULL DEFAULT 0 ,
`update_at`  int(10) UNSIGNED NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='问卷调查送代金券记录表'
ROW_FORMAT=COMPACT;