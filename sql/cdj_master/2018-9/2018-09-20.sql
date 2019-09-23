-- ----------------------------
-- Table structure for wechat_invoice_card
-- ----------------------------
DROP TABLE IF EXISTS `wechat_invoice_card`;
CREATE TABLE `wechat_invoice_card`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pm_order_number` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `card_id` varchar(0) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '微信卡券模板编号',
  `status` enum('1','2','3') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '1' COMMENT '接收授权；完成授权；其他',
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `order_number_idx`(`pm_order_number`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '微信电子发票卡券' ROW_FORMAT = Compact;