-- 常用电话表
DROP TABLE IF EXISTS `project_service_phone`;
CREATE TABLE `project_service_phone` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`project_house_id`  int(11) UNSIGNED NULL COMMENT '项目id，0为通用' ,
`category_id`  tinyint(2) UNSIGNED NULL COMMENT '分类id，0为通用' ,
`name`  varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '管理处名字' ,
`telphone`  varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '联系电话' ,
`address`  varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '通讯地址' ,
`status`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态：0禁用1正常' ,
`created_at`  int(11) UNSIGNED NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`),
UNIQUE INDEX `id` (`id`) USING BTREE ,
INDEX `project_house_id` (`project_house_id`) USING BTREE
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='常用电话表' ROW_FORMAT=COMPACT;


-- 常用电话分类表
DROP TABLE IF EXISTS `project_service_category`;
CREATE TABLE `project_service_category` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`project_house_id`  int(11) UNSIGNED NULL COMMENT '分类id，0为通用' ,
`name`  varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '常用电话分类名' ,
`status`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态：0禁用1正常' ,
PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='常用电话分类表' ROW_FORMAT=COMPACT;





