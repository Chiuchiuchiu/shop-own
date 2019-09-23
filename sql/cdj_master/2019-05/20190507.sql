-- 问卷调查-开发商、业委会、居委会人员信息表 --
CREATE TABLE `question_member_develop` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`company`  varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分公司' ,
`project`  varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '项目名' ,
`member_type`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1开发商2业委会3居委会' ,
`name`  varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '业主姓名' ,
`phone`  varchar(20) NOT NULL ,
`number`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '通知次数',
`send_status`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1成功2失败',
`season`  int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '季度，哪一次的答题活动添加的',
`year` int(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '年份',
`created_at`  int(11) NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='问卷调查-开发商、业委会、居委会人员信息表'
ROW_FORMAT=COMPACT;

-- 问卷调查-开发商、业委会、居委会人员答题列表 --
CREATE TABLE `question_answer_items_develop` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(10) DEFAULT NULL COMMENT '答卷ID',
  `develop_id` int(10) unsigned DEFAULT NULL COMMENT '开发商、业委会、居委会人员id',
  `score` int(10) unsigned DEFAULT NULL COMMENT '得分',
  `site` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '详细意见',
  `created_at` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='问卷调查-开发商、业委会、居委会人员答题详细信息'
ROW_FORMAT=COMPACT;

-- 阿里大于短信发送log --
CREATE TABLE `alidayu_msg_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(20) NOT NULL DEFAULT '',
  `result` varchar(250) NOT NULL DEFAULT '',
  `status` tinyint(10) unsigned DEFAULT '0' COMMENT '1发送成功2发送失败',
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='阿里大于短信发送log';

-- 添加职位字段 --
ALTER TABLE `question_member_develop`
ADD COLUMN `job`  varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '职位' AFTER `phone`;