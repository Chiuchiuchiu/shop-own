CREATE TABLE `operation_log_201907` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`member_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 ,
`path_info`  varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '请求路径' ,
`referrer`  varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '上一个页面' ,
`ip`  varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' ,
`created_at`  int(11) UNSIGNED NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`)
)
;

