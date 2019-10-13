/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : shop

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-10-08 18:05:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for shop
-- ----------------------------
DROP TABLE IF EXISTS `shop`;
CREATE TABLE `shop` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL COMMENT '商铺名',
  `logo` varchar(255) DEFAULT NULL,
  `inventory_type` tinyint(4) DEFAULT '0' COMMENT '减库存  1 拍下减库存  2 付款减库存',
  `status` tinyint(4) DEFAULT NULL,
  `service_end_time` int(11) DEFAULT NULL COMMENT '服务到期时间',
  `shop_type` int(11) DEFAULT '1' COMMENT '店铺类型',
  `mobile` varchar(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `reg_status` tinyint(4) DEFAULT '0' COMMENT '注册状态。-3.提交申请；-2.发送验证码；-1.店铺信息；0.待审核；1.注册成功 ',
  `balance` decimal(10,2) DEFAULT '0.00' COMMENT '店铺总余额',
  `lock_balance` decimal(10,2) DEFAULT '0.00' COMMENT '锁定余额',
  `available_balance` decimal(10,2) DEFAULT '0.00' COMMENT '可用余额',
  `unpaid_time` int(8) DEFAULT '30' COMMENT '拍下未付款时间自动取消',
  `is_since` int(8) DEFAULT '0' COMMENT '是否开启上门自提  0 关闭 1 开启',
  `created_at` bigint(20) DEFAULT NULL,
  `updated_at` bigint(20) DEFAULT NULL,
  `deleted_at` bigint(20) DEFAULT NULL,
  `platform_commission` decimal(11,4) DEFAULT '0.0000' COMMENT '平台提成比例',
  `amount` decimal(11,2) DEFAULT '0.00' COMMENT '店铺金额',
  `amount_locked` decimal(11,2) DEFAULT '0.00' COMMENT '店铺冻结金额',
  `amount_wait` decimal(11,2) DEFAULT '0.00' COMMENT '待结算金额',
  `total_amount` decimal(32,2) DEFAULT '0.00' COMMENT '交易总金额',
  `sort` int(11) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `project_ids` varchar(256) DEFAULT NULL COMMENT '所属项目IDS',
  `service_type` int(11) unsigned DEFAULT '3' COMMENT '订单服务类型：物流订单，到店服务，上门服务',
  `is_business` int(11) unsigned NOT NULL DEFAULT '2' COMMENT '0:测试；1：平台；2：商家',
  `icon_name` varchar(64) DEFAULT NULL COMMENT 'ICON名称',
  `cate_id` bigint(32) unsigned DEFAULT NULL COMMENT '系统分类ID',
  `is_vip` int(11) unsigned DEFAULT '0' COMMENT '0非VIP店铺不能DIY;1为VIP店铺能DIY首页',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shop
-- ----------------------------
INSERT INTO `shop` VALUES ('1', '海外购', '', '2', '1', null, '1', '13800138000', '123@qq.com', '', '0', '0.00', '0.00', '0.00', '30', '0', '1560418220', '1569209901', null, '0.1000', '52.23', '0.00', '0.10', '52.23', '255', null, '7', '1', null, null, '0');
INSERT INTO `shop` VALUES ('2', '甘稻夫测试', '', '2', '1', null, '1', '13800138001', '123@qq.com', '', '0', '0.00', '0.00', '0.00', '30', '0', '1560418260', '1561455119', null, '0.1000', '0.39', '0.00', '0.15', '0.00', '255', null, '3', '1', null, null, '0');
INSERT INTO `shop` VALUES ('3', '甘稻夫旗舰店', 'https://shop.51homemoney.com//Public/Uploads/cfcd208495d565ef66e7dff9f98764da/2019-08-14/5d5372154eb16.png', null, '1', null, '1', '18038738863', null, '', '1', '0.00', '0.00', '0.00', '30', '0', '1561167963', '1568484003', null, '0.1000', '4240.17', '0.00', '0.22', '4222.35', '1', '', '1', '2', '好米到家', '1', '0');
INSERT INTO `shop` VALUES ('4', '润兴水超市', 'https://shop.51homemoney.com//Public/Uploads/cfcd208495d565ef66e7dff9f98764da/2019-08-15/5d54bf7dbe0f7.png', '1', '1', null, '1', '13527844726', null, '', '1', '0.00', '0.00', '0.00', '30', '0', '1564971490', '1569520802', null, '0.0500', '229.91', '0.00', '0.00', '229.91', '2', ',78874,84244,', '6', '2', '好水到家', '1', '0');
INSERT INTO `shop` VALUES ('5', '广州星驰汽车服务', 'https://shop.51homemoney.com//Public/Uploads/cfcd208495d565ef66e7dff9f98764da/2019-08-17/5d577b7fafd7d.png', null, '1', null, '1', '17676114772', null, '汽车服务与美容', '1', '0.00', '0.00', '0.00', '30', '0', '1566014385', '1568253601', null, '0.1000', '0.00', '0.00', '0.00', '0.00', '3', ',84244,', '2', '2', '汽车服务', '5', '0');
INSERT INTO `shop` VALUES ('6', '全优加早教中心', 'https://shop.51homemoney.com//Public/Uploads/cfcd208495d565ef66e7dff9f98764da/2019-08-21/5d5cdf8649838.jpg', null, '1', null, '1', '13802909250', null, '全优加早教', '1', '0.00', '0.00', '0.00', '30', '0', '1566354701', '1568253612', null, '0.1000', '0.00', '0.00', '0.00', '0.00', '4', ',84244,', '2', '2', '全优加早教', '3', '0');
INSERT INTO `shop` VALUES ('7', '壹手海淘', 'https://shop.51homemoney.com//Public/Uploads/cfcd208495d565ef66e7dff9f98764da/2019-09-16/5d7f54cad2bd2.png', '2', '1', null, '1', '13802764803', null, '主营英国产品，是多个英国品牌授权的中国总代理。', '1', '0.00', '0.00', '0.00', '30', '0', '1566955976', '1570471203', null, '0.1000', '413.70', '0.00', '59.10', '413.70', '2', '', '1', '2', '海淘到家', '1', '0');
INSERT INTO `shop` VALUES ('8', '帮妮洗衣店', 'https://shop.51homemoney.com//Public/Uploads/cfcd208495d565ef66e7dff9f98764da/2019-09-12/5d79e95ab9124.jpg', '1', '1', null, '1', '18122361757', null, '洗衣、缝补、奢侈品皮具护理', '0', '0.00', '0.00', '0.00', '30', '0', '1568272068', null, null, '0.1000', '0.00', '0.00', '0.00', '0.00', '255', null, '4', '2', '洗衣服务', '2', '0');
INSERT INTO `shop` VALUES ('9', '清风纸业专营店', 'https://shop.51homemoney.com//Public/Uploads/cfcd208495d565ef66e7dff9f98764da/2019-09-16/5d7f4352bd8ed.png', '1', '1', null, '1', '13631330617', null, '清风纸巾批发', '1', '0.00', '0.00', '0.00', '30', '0', '1568274147', '1569293112', null, '0.1000', '0.00', '0.00', '0.00', '0.00', '3', '', '1', '2', '清风纸业', '1', '0');
INSERT INTO `shop` VALUES ('10', '德麦森贝壳除醛涂料店', 'https://shop.51homemoney.com//Public/Uploads/cfcd208495d565ef66e7dff9f98764da/2019-09-18/5d81d8f276b00.jpg', '1', '1', null, '1', '13925196973', null, '装修环保涂料，防潮防霉', '1', '0.00', '0.00', '0.00', '30', '0', '1568274929', '1569402425', null, '0.1500', '0.00', '0.00', '0.00', '0.00', '4', ',78874,83542,84244,172752,522719,53751,93719,117582,128802,196496,230975,270774,286855,308671,399360,461292,468497,472801,509052,549871,663706,177056,211998,236762,261314,286856,', '7', '2', '环保涂漆', '6', '0');
