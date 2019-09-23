-- ----------------------------
-- 添加接口记录操作
-- ----------------------------
DROP TABLE IF EXISTS `api_log`;
CREATE TABLE `api_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `controller` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `action` varchar(20) NOT NULL DEFAULT '',
  `params` varchar(255) NOT NULL DEFAULT '',
  `result` text,
  `accesstoken` varchar(32) DEFAULT '',
  `ip` varchar(12) NOT NULL DEFAULT '',
  `created_at` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='接口记录表';