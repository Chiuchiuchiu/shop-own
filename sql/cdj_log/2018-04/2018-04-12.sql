-- ----------------------------
-- Table structure for parking_order_notify_error_log
-- ----------------------------
DROP TABLE IF EXISTS `parking_order_notify_error_log`;
CREATE TABLE `parking_order_notify_error_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parking_order_id` int(11) DEFAULT NULL,
  `to_user_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` tinyint(2) DEFAULT 0,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;