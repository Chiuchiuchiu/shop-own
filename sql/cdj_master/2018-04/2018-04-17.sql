-- ---------------------------- --
-- project_butler_manage_lists 表增加新字段
-- ---------------------------- --
ALTER TABLE `project_butler_manage_lists`
ADD COLUMN `project_house_id` int(0) DEFAULT 0 AFTER `id`,
ADD COLUMN `project_region_id` int(0) DEFAULT 0 AFTER `project_house_id`,
ADD COLUMN `butler_id` int(0) DEFAULT 0 AFTER `project_region_id`,
ADD COLUMN `status` tinyint(2) DEFAULT 1 AFTER `butler_id`;

ALTER TABLE `project_butler_manage_lists`
  ADD INDEX `project_idx`(`project_house_id`, `project_region_id`),
  ADD INDEX `butler_idx`(`butler_id`);

ALTER TABLE `project_butler_manage_lists`
  ADD COLUMN `house_parent_id` int(0) DEFAULT 0 AFTER `project_region_id`;



-- ----------------------------
-- Table structure for property_managementarea_report_ext
-- ----------------------------
DROP TABLE IF EXISTS `property_managementarea_report_ext`;
CREATE TABLE `property_managementarea_report_ext`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pbml_id` int(11) DEFAULT NULL COMMENT '关联project_butler_manage_lists ID',
  `auth_house_day_count` int(11) DEFAULT NULL COMMENT '日认证房产数',
  `auth_parking_day_count` int(11) DEFAULT NULL COMMENT '日认证车位数',
  `auth_all_house_sum` int(11) DEFAULT 0 COMMENT '所有已认证房产数',
  `auth_all_parking_sum` int(11) DEFAULT 0 COMMENT '所有已认证车位数',
  `bill_house_day_amount` decimal(12, 2) DEFAULT NULL COMMENT '日缴房产费',
  `bill_parking_day_amount` decimal(12, 2) DEFAULT NULL COMMENT '日缴车位管理位',
  `bill_all_house_amount` decimal(12, 2) DEFAULT NULL COMMENT '所有已缴的房产费用',
  `bill_all_parking_amount` decimal(12, 2) DEFAULT NULL COMMENT '所有已缴的车位管理费用',
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pbml_indx`(`pbml_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;