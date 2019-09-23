-- ----------------------------
-- Table structure for project_red_envelope
-- ----------------------------
DROP TABLE IF EXISTS `project_red_envelope`;
CREATE TABLE `project_red_envelope` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_house_id` int(11) NOT NULL,
  `stock` int(10) NOT NULL DEFAULT '0',
  `property_id` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `wechat_red_pack`
  ADD COLUMN `house_id`  int NULL DEFAULT 0 AFTER `pm_order_id`;