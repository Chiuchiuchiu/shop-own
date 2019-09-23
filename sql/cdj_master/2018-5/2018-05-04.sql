ALTER TABLE `project`
ADD COLUMN `pay_type` tinyint(2) NULL DEFAULT 1 AFTER `sync_count`;

ALTER TABLE `pm_order_item`
  ADD COLUMN `charge_detail_id_list` varchar(255) DEFAULT 0 AFTER `contract_no`;

ALTER TABLE `pm_order_fpzz_item`
  ADD COLUMN `contract_no` text AFTER `pm_order_fpzz_id`,
  ADD COLUMN `charge_detail_id_list` varchar(255) AFTER `contract_no`;

ALTER TABLE `pm_order_fpzz_result`
  ADD COLUMN `send_window_status` tinyint(2) DEFAULT 0 AFTER `status`;

-- ----------------------------
-- Table structure for project_pay_config
-- ----------------------------
DROP TABLE IF EXISTS `project_pay_config`;
CREATE TABLE `project_pay_config`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_house_id` int(11) DEFAULT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mch_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `project_house_id_idx`(`project_house_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;