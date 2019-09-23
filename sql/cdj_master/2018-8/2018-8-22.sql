-- ----------------------------
-- Table structure for pm_order_newwindow_pdf
-- ----------------------------
DROP TABLE IF EXISTS `pm_order_newwindow_pdf`;
CREATE TABLE `pm_order_newwindow_pdf`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pm_order_fpzz_id` int(11) DEFAULT NULL COMMENT '关联fpzz_id',
  `member_id` int(11) DEFAULT NULL COMMENT '关联member',
  `bill_num` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '蓝字发票号码',
  `bill_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '蓝字发票代码',
  `bill_pdf_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '发票PDF地址',
  `bill_jpg_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '发票JPG地址',
  `ref_bill_num` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '红字发票号码',
  `ref_bill_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '红字发票代码',
  `ref_bill_pdf_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '红字发票PDF地址',
  `ref_bill_jpg_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '红字发票JPG地址',
  `save_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '保存路径',
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fpzz_id_idx`(`pm_order_fpzz_id`, `member_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '新视窗开具电子发票' ROW_FORMAT = Compact;


-- question_project添加状态并删除deleted_at字段
ALTER TABLE `question_project`
DROP COLUMN `deleted_at`,
ADD COLUMN `status`  tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '问卷状态：1进行中2已结束' AFTER `type_isp`;
