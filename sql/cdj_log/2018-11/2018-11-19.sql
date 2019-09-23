-- ----------------------------
-- Table structure for upload_file_log
-- ----------------------------
DROP TABLE IF EXISTS `upload_file_log`;
CREATE TABLE `upload_file_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `save_path` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `url_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;