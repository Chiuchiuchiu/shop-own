-- ----------------------------
-- Table structure for `quest_cate`
-- ----------------------------
DROP TABLE IF EXISTS `quest_cate`;
CREATE TABLE `quest_cate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '标题说明',
  `parent_id` int(11) DEFAULT '0' COMMENT '父级ID',
  `title` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '标题说明',
  `site` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '标题说明',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `question`
-- ----------------------------
DROP TABLE IF EXISTS `question`;
CREATE TABLE `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '规则标题',
  `site` text COLLATE utf8_unicode_ci,
  `content` text COLLATE utf8_unicode_ci,
  `category_id` int(11) NOT NULL DEFAULT '0' COMMENT '游戏ID',
  `question_project_id` int(11) DEFAULT '0' COMMENT '问卷项目ID',
  `type_isp` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态0单选1多选',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reyes` (`type_isp`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for `question_answer`
-- ----------------------------
DROP TABLE IF EXISTS `question_answer`;
CREATE TABLE `question_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `surname` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telephone` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `butler_id` int(10) DEFAULT '0' COMMENT '用户表ID',
  `question_project_id` int(10) DEFAULT '0' COMMENT '用户表ID',
  `project_house_id` int(10) DEFAULT '0' COMMENT '用户表ID',
  `member_id` int(10) DEFAULT '0' COMMENT '用户表ID',
  `member_house_id` int(10) DEFAULT '0' COMMENT '用户表ID',
  `project_region_id` int(10) DEFAULT '0' COMMENT '用户表ID',
  `question_score` int(10) DEFAULT '0',
  `is_loyal` int(11) DEFAULT '0',
  `score_json` text COLLATE utf8_unicode_ci,
  `ancestor_name` text COLLATE utf8_unicode_ci,
  `status` tinyint(1) DEFAULT '0' COMMENT '是否完成0未完成1完成，2废弃',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reyes` (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for `question_answer_items`
-- ----------------------------
DROP TABLE IF EXISTS `question_answer_items`;
CREATE TABLE `question_answer_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_answer_id` int(10) DEFAULT NULL COMMENT '答卷ID',
  `question_id` int(10) DEFAULT NULL COMMENT '答卷ID',
  `question_project_id` int(10) DEFAULT NULL COMMENT '答卷ID',
  `project_region_id` int(10) DEFAULT NULL COMMENT '答卷ID',
  `project_house_id` int(10) DEFAULT NULL COMMENT '答卷ID',
  `type_isp` int(10) DEFAULT NULL COMMENT '类型',
  `replys` int(10) DEFAULT NULL COMMENT '回答内容',
  `site` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '标题说明',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=234 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `question_project`
-- ----------------------------
DROP TABLE IF EXISTS `question_project`;
CREATE TABLE `question_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '规则标题',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `site` text COLLATE utf8_unicode_ci,
  `content` text COLLATE utf8_unicode_ci,
  `type_isp` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态0单选1多选',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reyes` (`type_isp`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for `question_project_butler`
-- ----------------------------
DROP TABLE IF EXISTS `question_project_butler`;
CREATE TABLE `question_project_butler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `question_project_id` int(10) DEFAULT '0',
  `butler_id` int(10) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reyes` (`question_project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;