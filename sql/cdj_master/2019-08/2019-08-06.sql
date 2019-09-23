CREATE TABLE `repair_hold` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`repair_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 ,
`content`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' ,
`status`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 ,
`butler_id`  int(11) NOT NULL DEFAULT 0,
`created_at`  int(11) NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_general_ci
ROW_FORMAT=COMPACT
;

ALTER TABLE `repair_hold`
ADD COLUMN `updated_at`  int(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `created_at`;

ALTER TABLE `repair_hold`
ADD COLUMN `project_id`  int(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `repair_id`;