CREATE TABLE `pm_order_refund_minsheng` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`pm_order_id`  varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' ,
`refund_number`  varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' ,
`status`  int(3) UNSIGNED NOT NULL DEFAULT 0 ,
`amount`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0 ,
`ip`  varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
`created_at`  int(11) UNSIGNED NOT NULL DEFAULT 0 ,
`updated_at`  int(11) UNSIGNED NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_general_ci
ROW_FORMAT=COMPACT
;

