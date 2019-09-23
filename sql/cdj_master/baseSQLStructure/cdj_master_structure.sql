/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50553
 Source Host           : localhost:3306
 Source Schema         : cdj_master

 Target Server Type    : MySQL
 Target Server Version : 50553
 File Encoding         : 65001

 Date: 05/01/2018 10:38:45
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for activities_collect_order
-- ----------------------------
DROP TABLE IF EXISTS `activities_collect_order`;
CREATE TABLE `activities_collect_order`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `member_house_id` int(11) NOT NULL,
  `house_id` int(11) DEFAULT NULL,
  `project_house_id` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL,
  `tel` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uni_house_id`(`house_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for activities_log
-- ----------------------------
DROP TABLE IF EXISTS `activities_log`;
CREATE TABLE `activities_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `collect_status` tinyint(2) DEFAULT 0,
  `collect_time` int(11) DEFAULT NULL,
  `project_house_id` int(11) DEFAULT NULL,
  `nick_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pay_time` int(11) DEFAULT NULL,
  `pay_status` tinyint(2) DEFAULT 0,
  `identification_status` tinyint(2) DEFAULT 0,
  `identification_time` int(11) DEFAULT NULL,
  `headimg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ac_order_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `index_ni_n_p`(`nick_name`, `name`, `phone`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `show_type` tinyint(3) UNSIGNED NOT NULL,
  `category_id` smallint(5) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `pic` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `author` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `post_at` int(11) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  `updated_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `categroy_id`(`category_id`) USING BTREE,
  INDEX `project_id`(`project_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 31 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for article_category
-- ----------------------------
DROP TABLE IF EXISTS `article_category`;
CREATE TABLE `article_category`  (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `project_id`(`project_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for auth_house_notification_member
-- ----------------------------
DROP TABLE IF EXISTS `auth_house_notification_member`;
CREATE TABLE `auth_house_notification_member`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `house_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_houseId_memberId`(`house_id`, `member_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for butler
-- ----------------------------
DROP TABLE IF EXISTS `butler`;
CREATE TABLE `butler`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group` tinyint(2) DEFAULT 1,
  `project_house_id` int(11) UNSIGNED NOT NULL,
  `wechat_open_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `wechat_user_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `headimg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `regions` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `project_house_id`(`project_house_id`) USING BTREE,
  INDEX `wechat_user_id`(`wechat_user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 23 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for butler_auth
-- ----------------------------
DROP TABLE IF EXISTS `butler_auth`;
CREATE TABLE `butler_auth`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_house_id` int(10) UNSIGNED NOT NULL,
  `account` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `region` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  `used_at` int(10) UNSIGNED NOT NULL,
  `used_to` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `account`(`account`) USING BTREE,
  INDEX `project_house_id`(`project_house_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for butler_election_activity
-- ----------------------------
DROP TABLE IF EXISTS `butler_election_activity`;
CREATE TABLE `butler_election_activity`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` tinyint(2) DEFAULT 1,
  `group` tinyint(2) NOT NULL DEFAULT 1,
  `project_house_id` int(11) NOT NULL,
  `butler_id` int(11) DEFAULT NULL,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `head_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `introduce` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `number` int(11) DEFAULT 0,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 39 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for butler_labels
-- ----------------------------
DROP TABLE IF EXISTS `butler_labels`;
CREATE TABLE `butler_labels`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `butler_id` int(11) DEFAULT NULL,
  `in_labels_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 61 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for butler_region
-- ----------------------------
DROP TABLE IF EXISTS `butler_region`;
CREATE TABLE `butler_region`  (
  `house_id` int(10) UNSIGNED NOT NULL,
  `butler_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`house_id`, `butler_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`  (
  `id` char(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `data` blob,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for feedback
-- ----------------------------
DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `house_id` int(10) UNSIGNED NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tel` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pics` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for feedback_message
-- ----------------------------
DROP TABLE IF EXISTS `feedback_message`;
CREATE TABLE `feedback_message`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `repair_id` int(10) UNSIGNED NOT NULL,
  `sender_type` tinyint(3) UNSIGNED NOT NULL,
  `sender_id` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `create_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `repair_id`(`repair_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for fpzz_feedback
-- ----------------------------
DROP TABLE IF EXISTS `fpzz_feedback`;
CREATE TABLE `fpzz_feedback`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `fpzz_result_id` int(11) DEFAULT NULL,
  `status` tinyint(2) DEFAULT 0,
  `ip` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_member_id`(`member_id`) USING BTREE,
  INDEX `idx_fpzz_result_id`(`member_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for fpzz_log
-- ----------------------------
DROP TABLE IF EXISTS `fpzz_log`;
CREATE TABLE `fpzz_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `fp_cached_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pm_order_fpzz_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for house
-- ----------------------------
DROP TABLE IF EXISTS `house`;
CREATE TABLE `house`  (
  `house_id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL,
  `project_house_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `house_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ancestor_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `house_alias_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '房子别名',
  `reskind` tinyint(4) UNSIGNED NOT NULL,
  `room_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `room_status_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `belong_floor` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `level` int(10) UNSIGNED NOT NULL,
  `deepest_node` int(10) UNSIGNED NOT NULL,
  `show_status` tinyint(3) UNSIGNED NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `updated_at` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`house_id`) USING BTREE,
  INDEX `parent`(`parent_id`) USING BTREE,
  INDEX `project_house_id`(`project_house_id`) USING BTREE,
  INDEX `orderby`(`ordering`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for house_bill_outline
-- ----------------------------
DROP TABLE IF EXISTS `house_bill_outline`;
CREATE TABLE `house_bill_outline`  (
  `house_id` int(10) UNSIGNED NOT NULL,
  `bill_count` smallint(5) UNSIGNED NOT NULL,
  `total_amount` decimal(10, 2) UNSIGNED NOT NULL,
  `process_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '处理状态：0：未处理；1：已处理',
  `aggregate_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '序列化保存客户物业服务费详情',
  `updated_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`house_id`) USING BTREE,
  INDEX `bill_count`(`bill_count`) USING BTREE,
  INDEX `bill_count_2`(`bill_count`) USING BTREE,
  INDEX `process_status`(`process_status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for house_ext
-- ----------------------------
DROP TABLE IF EXISTS `house_ext`;
CREATE TABLE `house_ext`  (
  `house_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL COMMENT '客户ID',
  `birth_day` int(11) DEFAULT NULL COMMENT '生日',
  `charge_area` decimal(10, 4) DEFAULT NULL COMMENT '计费面积',
  `id_number` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '身份证',
  `hurry_phone` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '紧急电话',
  `link_man` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '联系人',
  `customer_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '业主名字',
  `mobile_phone` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '联系电话',
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`house_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for house_relevance
-- ----------------------------
DROP TABLE IF EXISTS `house_relevance`;
CREATE TABLE `house_relevance`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `house_id` int(11) DEFAULT NULL,
  `with_house_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for individual_labels
-- ----------------------------
DROP TABLE IF EXISTS `individual_labels`;
CREATE TABLE `individual_labels`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group` tinyint(2) DEFAULT 0,
  `status` tinyint(2) DEFAULT 1,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `class` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 34 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for manager
-- ----------------------------
DROP TABLE IF EXISTS `manager`;
CREATE TABLE `manager`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `real_name` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '用户名',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '登录密码',
  `group_id` int(11) NOT NULL COMMENT '用户组',
  `need_change_pw` tinyint(4) UNSIGNED NOT NULL DEFAULT 1,
  `state` tinyint(4) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `email`(`email`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for manager_group
-- ----------------------------
DROP TABLE IF EXISTS `manager_group`;
CREATE TABLE `manager_group`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '分组名',
  `permission` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '分组权限',
  `state` tinyint(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '权限分组' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for manager_login_log
-- ----------------------------
DROP TABLE IF EXISTS `manager_login_log`;
CREATE TABLE `manager_login_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `manager_id` int(10) UNSIGNED NOT NULL,
  `time` int(10) UNSIGNED NOT NULL,
  `ip` char(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `manager_id`(`manager_id`, `time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 306 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for member
-- ----------------------------
DROP TABLE IF EXISTS `member`;
CREATE TABLE `member`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wechat_open_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `headimg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `wechat_open_id`(`wechat_open_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for member_car
-- ----------------------------
DROP TABLE IF EXISTS `member_car`;
CREATE TABLE `member_car`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `plate_number` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for member_ext
-- ----------------------------
DROP TABLE IF EXISTS `member_ext`;
CREATE TABLE `member_ext`  (
  `member_id` int(11) NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`member_id`) USING BTREE,
  INDEX `ind_member_id`(`member_id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for member_house
-- ----------------------------
DROP TABLE IF EXISTS `member_house`;
CREATE TABLE `member_house`  (
  `member_id` int(11) NOT NULL,
  `house_id` int(11) NOT NULL,
  `group` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `identity` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`member_id`, `house_id`) USING BTREE,
  INDEX `house_id`(`house_id`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for member_house_log
-- ----------------------------
DROP TABLE IF EXISTS `member_house_log`;
CREATE TABLE `member_house_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(10) UNSIGNED NOT NULL,
  `house_id` int(10) UNSIGNED NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `action` tinyint(3) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  `operator` tinyint(3) UNSIGNED NOT NULL,
  `operator_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for member_house_post_log
-- ----------------------------
DROP TABLE IF EXISTS `member_house_post_log`;
CREATE TABLE `member_house_post_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `raw` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `member_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `project_id`(`project_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 88 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for member_house_review
-- ----------------------------
DROP TABLE IF EXISTS `member_house_review`;
CREATE TABLE `member_house_review`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `butler_id` int(11) DEFAULT NULL,
  `house_id` int(11) NOT NULL,
  `group` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `identity` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `customer_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `house_id`(`house_id`) USING BTREE,
  INDEX `idx_member_id`(`member_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for member_promotion_code
-- ----------------------------
DROP TABLE IF EXISTS `member_promotion_code`;
CREATE TABLE `member_promotion_code`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `house_id` int(11) DEFAULT NULL,
  `amount` decimal(10, 2) DEFAULT 0.00,
  `promotion_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `promotion_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `xg_product_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint(2) DEFAULT 0,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for member_vote
-- ----------------------------
DROP TABLE IF EXISTS `member_vote`;
CREATE TABLE `member_vote`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group` tinyint(2) DEFAULT NULL,
  `member_id` int(11) NOT NULL,
  `number` int(11) DEFAULT 0,
  `bsa_id` int(11) NOT NULL,
  `vote_time` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `ind_member_id`(`member_id`) USING BTREE,
  INDEX `ind_vote_time`(`vote_time`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for mobile_order
-- ----------------------------
DROP TABLE IF EXISTS `mobile_order`;
CREATE TABLE `mobile_order`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `number` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `member_id` int(11) UNSIGNED NOT NULL,
  `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `recharge_type` tinyint(10) UNSIGNED NOT NULL,
  `amount` decimal(10, 2) UNSIGNED NOT NULL,
  `pay_type` tinyint(3) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `send_status` tinyint(3) UNSIGNED NOT NULL,
  `payed_at` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `send_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `numero`(`number`) USING BTREE,
  INDEX `member_id`(`member_id`) USING BTREE,
  INDEX `house_id`(`mobile`) USING BTREE,
  INDEX `project_house_id`(`recharge_type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for mobile_order_log
-- ----------------------------
DROP TABLE IF EXISTS `mobile_order_log`;
CREATE TABLE `mobile_order_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` decimal(10, 2) DEFAULT NULL,
  `number` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for parking_order
-- ----------------------------
DROP TABLE IF EXISTS `parking_order`;
CREATE TABLE `parking_order`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_house_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `number` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `plate_number` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `calc_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(2) NOT NULL,
  `type` tinyint(2) DEFAULT NULL,
  `pay_type` tinyint(2) DEFAULT NULL,
  `amount` decimal(10, 2) NOT NULL,
  `receivable` decimal(10, 2) DEFAULT NULL,
  `disc` decimal(10, 2) DEFAULT NULL,
  `
quantity` tinyint(4) DEFAULT 1,
  `transaction_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `expire_date` int(11) DEFAULT NULL,
  `effect_date` int(11) DEFAULT NULL,
  `m_id` int(11) DEFAULT NULL,
  `payed_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `send_at` int(11) DEFAULT NULL,
  `send_status` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_member_id`(`member_id`) USING BTREE,
  INDEX `idx_number`(`number`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for pm_christmas_bill_item
-- ----------------------------
DROP TABLE IF EXISTS `pm_christmas_bill_item`;
CREATE TABLE `pm_christmas_bill_item`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `house_id` int(11) NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `customer_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_house_id`(`house_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pm_manager
-- ----------------------------
DROP TABLE IF EXISTS `pm_manager`;
CREATE TABLE `pm_manager`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `project_house_id` int(10) UNSIGNED NOT NULL,
  `real_name` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '用户名',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '登录密码',
  `group_id` int(11) NOT NULL COMMENT '用户组',
  `need_change_pw` tinyint(4) UNSIGNED NOT NULL DEFAULT 1,
  `state` tinyint(4) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `email`(`name`) USING BTREE,
  INDEX `project_house_id`(`project_house_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for pm_manager_group
-- ----------------------------
DROP TABLE IF EXISTS `pm_manager_group`;
CREATE TABLE `pm_manager_group`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_house_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '分组名',
  `permission` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '分组权限',
  `state` tinyint(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `project_house_id`(`project_house_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '权限分组' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for pm_manager_login_log
-- ----------------------------
DROP TABLE IF EXISTS `pm_manager_login_log`;
CREATE TABLE `pm_manager_login_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `manager_id` int(10) UNSIGNED NOT NULL,
  `time` int(10) UNSIGNED NOT NULL,
  `ip` char(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `manager_id`(`manager_id`, `time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 219 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for pm_order
-- ----------------------------
DROP TABLE IF EXISTS `pm_order`;
CREATE TABLE `pm_order`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `number` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `member_id` int(11) UNSIGNED NOT NULL,
  `house_id` int(10) UNSIGNED NOT NULL,
  `project_house_id` int(10) UNSIGNED NOT NULL,
  `total_amount` decimal(10, 2) UNSIGNED NOT NULL,
  `pay_type` tinyint(3) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `bill_type` tinyint(2) UNSIGNED DEFAULT 1,
  `discount_status` tinyint(2) DEFAULT 0,
  `payed_at` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `refund_at` int(11) DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `numero`(`number`) USING BTREE,
  INDEX `member_id`(`member_id`) USING BTREE,
  INDEX `house_id`(`house_id`) USING BTREE,
  INDEX `project_house_id`(`project_house_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 70 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for pm_order_auditing
-- ----------------------------
DROP TABLE IF EXISTS `pm_order_auditing`;
CREATE TABLE `pm_order_auditing`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` char(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `pm_order_ids` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  `updated_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for pm_order_auditing_log
-- ----------------------------
DROP TABLE IF EXISTS `pm_order_auditing_log`;
CREATE TABLE `pm_order_auditing_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pm_order_auditing_id` int(11) NOT NULL,
  `manager_id` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  `message` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pm_order_auditing_id`(`pm_order_auditing_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for pm_order_discounts
-- ----------------------------
DROP TABLE IF EXISTS `pm_order_discounts`;
CREATE TABLE `pm_order_discounts`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pm_order_id` int(11) DEFAULT NULL,
  `red_pack_status` tinyint(2) DEFAULT 0,
  `discounts_amount` decimal(10, 2) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_pm_order_id`(`pm_order_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for pm_order_fpzz
-- ----------------------------
DROP TABLE IF EXISTS `pm_order_fpzz`;
CREATE TABLE `pm_order_fpzz`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pm_order_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `house_id` int(11) DEFAULT NULL,
  `project_house_id` int(11) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `category` tinyint(2) DEFAULT 1,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `register_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  `total_amount` decimal(10, 2) DEFAULT NULL,
  `house_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `show_status` tinyint(2) UNSIGNED DEFAULT 1,
  `status` tinyint(2) DEFAULT 4,
  `remarks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `ind_project_house_id`(`project_house_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pm_order_fpzz_item
-- ----------------------------
DROP TABLE IF EXISTS `pm_order_fpzz_item`;
CREATE TABLE `pm_order_fpzz_item`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pm_order_id` int(11) DEFAULT NULL,
  `pm_order_fpzz_id` int(11) DEFAULT NULL,
  `spmc` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `spbm` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ggxh` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sl` decimal(25, 15) DEFAULT NULL,
  `slv` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dw` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dj` decimal(25, 15) DEFAULT NULL,
  `je` decimal(10, 2) DEFAULT NULL,
  `origin_amount` decimal(10, 2) DEFAULT NULL,
  `se` decimal(10, 2) DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `ind_pm_order_fpzz_id`(`pm_order_fpzz_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 407 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pm_order_fpzz_pdf
-- ----------------------------
DROP TABLE IF EXISTS `pm_order_fpzz_pdf`;
CREATE TABLE `pm_order_fpzz_pdf`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pm_order_id` int(11) DEFAULT NULL,
  `kprq` int(11) DEFAULT NULL,
  `fphm` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fpdm` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `xfsh` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gfsh` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gfmc` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `xfmc` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jehj` decimal(10, 2) DEFAULT NULL,
  `sehj` decimal(10, 2) DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fpid` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `index_fpid`(`fpid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pm_order_fpzz_result
-- ----------------------------
DROP TABLE IF EXISTS `pm_order_fpzz_result`;
CREATE TABLE `pm_order_fpzz_result`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pm_order_fpzz_id` int(11) DEFAULT NULL,
  `project_house_id` int(11) DEFAULT NULL,
  `result_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint(2) DEFAULT 0,
  `pm_order_id` int(11) DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `item_ids` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jehj` decimal(10, 2) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_result_id`(`result_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 203 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pm_order_fpzz_spmc
-- ----------------------------
DROP TABLE IF EXISTS `pm_order_fpzz_spmc`;
CREATE TABLE `pm_order_fpzz_spmc`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `spmc` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `as_spmc` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uni_spmc`(`spmc`) USING BTREE,
  INDEX `ind_spmc`(`spmc`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 31 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pm_order_item
-- ----------------------------
DROP TABLE IF EXISTS `pm_order_item`;
CREATE TABLE `pm_order_item`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pm_order_id` int(10) UNSIGNED NOT NULL,
  `charge_item_id` int(10) UNSIGNED NOT NULL,
  `charge_item_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contract_no` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `bill_date` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `amount` decimal(10, 2) UNSIGNED NOT NULL,
  `status` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `bill_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `bankBillNo` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `completed_at` int(10) UNSIGNED NOT NULL,
  `m_id` int(11) DEFAULT NULL,
  `price` decimal(10, 4) DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  `usage_amount` decimal(10, 4) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pm_order_id`(`pm_order_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 172753 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for prepay_pm_order
-- ----------------------------
DROP TABLE IF EXISTS `prepay_pm_order`;
CREATE TABLE `prepay_pm_order`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `number` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `member_id` int(11) UNSIGNED NOT NULL,
  `house_id` int(10) UNSIGNED NOT NULL,
  `project_house_id` int(10) UNSIGNED NOT NULL,
  `num` tinyint(3) UNSIGNED NOT NULL,
  `total_amount` decimal(10, 2) UNSIGNED NOT NULL,
  `balance` decimal(10, 2) UNSIGNED NOT NULL,
  `pay_type` tinyint(3) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `payed_at` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `numero`(`number`) USING BTREE,
  INDEX `member_id`(`member_id`) USING BTREE,
  INDEX `house_id`(`house_id`) USING BTREE,
  INDEX `project_house_id`(`project_house_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 47 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for project
-- ----------------------------
DROP TABLE IF EXISTS `project`;
CREATE TABLE `project`  (
  `house_id` int(11) NOT NULL,
  `project_region_id` int(11) DEFAULT 0,
  `house_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `area` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `logo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `icon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `sync_count` int(11) DEFAULT NULL,
  `url_key` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`house_id`) USING BTREE,
  UNIQUE INDEX `url_key`(`url_key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for project_butler_manage_lists
-- ----------------------------
DROP TABLE IF EXISTS `project_butler_manage_lists`;
CREATE TABLE `project_butler_manage_lists`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `butler_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `area_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bill_house_day_count` int(11) DEFAULT NULL,
  `bill_house_total_count` int(11) DEFAULT NULL,
  `auth_count` int(11) DEFAULT NULL COMMENT '当日认证数',
  `auth_amount` int(11) DEFAULT NULL COMMENT '认证总数',
  `bill_day_amount` decimal(12, 2) DEFAULT 0.00,
  `bill_total_amount` decimal(12, 2) DEFAULT 0.00,
  `house_amount` int(11) DEFAULT NULL COMMENT '房子总数',
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for project_fpzz_account
-- ----------------------------
DROP TABLE IF EXISTS `project_fpzz_account`;
CREATE TABLE `project_fpzz_account`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_house_id` int(11) DEFAULT NULL,
  `appid` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prikey` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kpr_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  `status` tinyint(2) DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for project_house_structure
-- ----------------------------
DROP TABLE IF EXISTS `project_house_structure`;
CREATE TABLE `project_house_structure`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_house_id` int(10) UNSIGNED NOT NULL,
  `parent_reskind` tinyint(4) NOT NULL,
  `name` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `reskind` tinyint(3) UNSIGNED NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `group` tinyint(3) UNSIGNED NOT NULL,
  `ordering` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `project_house_id`(`project_house_id`) USING BTREE,
  INDEX `group`(`group`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 477 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for project_parking_one_to_one
-- ----------------------------
DROP TABLE IF EXISTS `project_parking_one_to_one`;
CREATE TABLE `project_parking_one_to_one`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_house_id` int(11) NOT NULL,
  `app_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `app_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `parking_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_project_house_id`(`project_house_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for project_region
-- ----------------------------
DROP TABLE IF EXISTS `project_region`;
CREATE TABLE `project_region`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` tinyint(2) DEFAULT 1,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  `created_at` int(11) DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for property_announcement
-- ----------------------------
DROP TABLE IF EXISTS `property_announcement`;
CREATE TABLE `property_announcement`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `project_house_id` int(10) UNSIGNED NOT NULL,
  `pic` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `author` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED DEFAULT NULL,
  `updated_at` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `project_id`(`project_house_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for repair
-- ----------------------------
DROP TABLE IF EXISTS `repair`;
CREATE TABLE `repair`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `flow_style_id` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `house_id` int(10) UNSIGNED DEFAULT NULL,
  `member_id` int(11) NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tel` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pics` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` smallint(5) UNSIGNED NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` int(11) NOT NULL,
  `new_window_id` int(11) DEFAULT NULL,
  `site` tinyint(2) DEFAULT 0,
  `reception_user_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reception_user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `order_user_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `order_user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `project_house_id` int(11) DEFAULT NULL,
  `qy_weixin_notify` tinyint(2) DEFAULT 1,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `submit_member_id`(`member_id`) USING BTREE,
  INDEX `idx_house_id`(`house_id`) USING BTREE,
  INDEX `idx_project_house_id`(`project_house_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 47 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for repair_cancel
-- ----------------------------
DROP TABLE IF EXISTS `repair_cancel`;
CREATE TABLE `repair_cancel`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `repair_id` int(11) NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for repair_customer_evaluation
-- ----------------------------
DROP TABLE IF EXISTS `repair_customer_evaluation`;
CREATE TABLE `repair_customer_evaluation`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `repair_id` int(11) NOT NULL,
  `satisfaction` tinyint(2) NOT NULL,
  `timeliness` tinyint(2) NOT NULL,
  `customer_idea` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`, `repair_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for repair_message
-- ----------------------------
DROP TABLE IF EXISTS `repair_message`;
CREATE TABLE `repair_message`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `repair_id` int(10) UNSIGNED NOT NULL,
  `sender_type` tinyint(3) UNSIGNED NOT NULL,
  `sender_id` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `create_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `repair_id`(`repair_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for repair_response
-- ----------------------------
DROP TABLE IF EXISTS `repair_response`;
CREATE TABLE `repair_response`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `repair_id` int(11) NOT NULL,
  `services_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `error_msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `response_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `flow_id` int(11) DEFAULT NULL,
  `business_id` int(11) DEFAULT NULL,
  `service_state` tinyint(4) DEFAULT 2,
  `level_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for security_per_labels
-- ----------------------------
DROP TABLE IF EXISTS `security_per_labels`;
CREATE TABLE `security_per_labels`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bea_id` int(11) DEFAULT NULL,
  `in_labels_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 36 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for sys_switch
-- ----------------------------
DROP TABLE IF EXISTS `sys_switch`;
CREATE TABLE `sys_switch`  (
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `true_member_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `false_member_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for vote
-- ----------------------------
DROP TABLE IF EXISTS `vote`;
CREATE TABLE `vote`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` tinyint(2) DEFAULT NULL,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `votenum` int(11) DEFAULT 0,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for wechats_config_public
-- ----------------------------
DROP TABLE IF EXISTS `wechats_config_public`;
CREATE TABLE `wechats_config_public`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `public_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '公众号名称',
  `public_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '公众号原始id',
  `wechat` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '微信号',
  `interface_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '接口地址',
  `headface_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '公众号头像',
  `area` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '地区',
  `addon_config` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '插件配置',
  `addon_status` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '插件状态',
  `token` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Token',
  `is_use` tinyint(2) DEFAULT 0 COMMENT '是否为当前公众号',
  `type_id` tinyint(4) DEFAULT 0 COMMENT '公众号类型',
  `app_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'AppID',
  `app_secret` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'AppSecret',
  `project_id` int(11) NOT NULL COMMENT '楼盘项目ID',
  `group_id` int(10) UNSIGNED DEFAULT 0 COMMENT '等级',
  `encodingaeskey` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'EncodingAESKey',
  `tips_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '提示关注公众号的文章地址',
  `domain` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '自定义域名',
  `is_bind` tinyint(2) DEFAULT 0 COMMENT '是否为微信开放平台绑定账号',
  `cTime` int(10) DEFAULT NULL COMMENT '增加时间',
  `authorizer_refresh_token` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '一键绑定的refresh_token',
  `project_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `all_users_total` int(11) DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uni_project_id`(`project_id`) USING BTREE,
  INDEX `token`(`token`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xg_request_log
-- ----------------------------
DROP TABLE IF EXISTS `xg_request_log`;
CREATE TABLE `xg_request_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `project_house_id` int(11) DEFAULT NULL,
  `type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- View structure for v_pm_order_and_pm_order_discounts
-- ----------------------------
DROP VIEW IF EXISTS `v_pm_order_and_pm_order_discounts`;
CREATE ALGORITHM = UNDEFINED DEFINER = `root`@`localhost` SQL SECURITY DEFINER VIEW `v_pm_order_and_pm_order_discounts` AS select `p`.`id` AS `id`,`p`.`number` AS `number`,`p`.`member_id` AS `member_id`,`p`.`house_id` AS `house_id`,`p`.`project_house_id` AS `project_house_id`,`p`.`total_amount` AS `total_amount`,`p`.`pay_type` AS `pay_type`,`p`.`status` AS `status`,`p`.`bill_type` AS `bill_type`,`p`.`discount_status` AS `discount_status`,`p`.`payed_at` AS `payed_at`,`p`.`created_at` AS `created_at`,`p`.`refund_at` AS `refund_at`,`pd`.`pm_order_id` AS `pm_order_id`,`pd`.`discounts_amount` AS `discounts_amount` from (`pm_order_discounts` `pd` left join `pm_order` `p` on((`pd`.`pm_order_id` = `p`.`id`))) where ((`p`.`status` = 2) and (`p`.`discount_status` = 1));

SET FOREIGN_KEY_CHECKS = 1;
