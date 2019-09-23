CREATE TABLE `auth_private_log` (
`project_id`  int(10) UNSIGNED NOT NULL DEFAULT 0 ,
`house_id`  int(10) UNSIGNED NOT NULL DEFAULT 0 ,
`member_id`  int(10) UNSIGNED NOT NULL DEFAULT 0 ,
`created_at`  int(11) UNSIGNED NOT NULL DEFAULT 0
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='记录内部认证房产'
ROW_FORMAT=COMPACT
;
