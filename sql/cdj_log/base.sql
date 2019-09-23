/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50553
 Source Host           : localhost:3306
 Source Schema         : cdj_log

 Target Server Type    : MySQL
 Target Server Version : 50553
 File Encoding         : 65001

 Date: 12/04/2018 16:32:13
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for butler_auth_log
-- ----------------------------
DROP TABLE IF EXISTS `butler_auth_log`;
CREATE TABLE `butler_auth_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `ip` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for lxj_log
-- ----------------------------
DROP TABLE IF EXISTS `lxj_log`;
CREATE TABLE `lxj_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `project_house_id` int(11) DEFAULT NULL,
  `ip` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_project_house_id`(`project_house_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for member_phone_auth_log
-- ----------------------------
DROP TABLE IF EXISTS `member_phone_auth_log`;
CREATE TABLE `member_phone_auth_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `ip` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for new_repair_log
-- ----------------------------
DROP TABLE IF EXISTS `new_repair_log`;
CREATE TABLE `new_repair_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `butler_id` int(11) DEFAULT NULL,
  `post_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `ip` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for parking_log
-- ----------------------------
DROP TABLE IF EXISTS `parking_log`;
CREATE TABLE `parking_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `post_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `error_msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `error_code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `plateno` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_member_id`(`member_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

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

-- ----------------------------
-- Table structure for parking_pay_order_log
-- ----------------------------
DROP TABLE IF EXISTS `parking_pay_order_log`;
CREATE TABLE `parking_pay_order_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `msg` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `order_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_order_id`(`order_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for pm_order_to_butler_error_log
-- ----------------------------
DROP TABLE IF EXISTS `pm_order_to_butler_error_log`;
CREATE TABLE `pm_order_to_butler_error_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pm_order_id` int(11) NOT NULL,
  `to_user_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(2) DEFAULT 0,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qy_weixin_notify_log
-- ----------------------------
DROP TABLE IF EXISTS `qy_weixin_notify_log`;
CREATE TABLE `qy_weixin_notify_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `send_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `ip` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for reminder_log
-- ----------------------------
DROP TABLE IF EXISTS `reminder_log`;
CREATE TABLE `reminder_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `send_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '发送状态------0：成功；1：失败',
  `to_wechat_open_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `log_data` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `update_at` int(11) NOT NULL,
  `create_at` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '物业费催缴记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for tcis_fp_log
-- ----------------------------
DROP TABLE IF EXISTS `tcis_fp_log`;
CREATE TABLE `tcis_fp_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pm_order_fpzz_id` int(11) DEFAULT NULL,
  `type` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fp_cached_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pdf_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `post_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `resource` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for upload_excel_file_log
-- ----------------------------
DROP TABLE IF EXISTS `upload_excel_file_log`;
CREATE TABLE `upload_excel_file_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `project_house_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for visit_house_butler_to_butler_log
-- ----------------------------
DROP TABLE IF EXISTS `visit_house_butler_to_butler_log`;
CREATE TABLE `visit_house_butler_to_butler_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_butler_id` int(11) DEFAULT NULL,
  `to_butler_id` int(11) DEFAULT NULL,
  `manage_id` int(11) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for visit_house_owner_notify_log
-- ----------------------------
DROP TABLE IF EXISTS `visit_house_owner_notify_log`;
CREATE TABLE `visit_house_owner_notify_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `butler_qywechat_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for wechat_red_pack_log
-- ----------------------------
DROP TABLE IF EXISTS `wechat_red_pack_log`;
CREATE TABLE `wechat_red_pack_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `amount` decimal(10, 2) DEFAULT NULL,
  `result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
