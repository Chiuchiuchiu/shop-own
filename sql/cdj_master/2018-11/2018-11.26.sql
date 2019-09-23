-- ------------------
-- 增加项目微信小程序带参二维码
-- ------------------
ALTER TABLE `project`
  ADD COLUMN `mp_qrcode` varchar(255) DEFAULT '' COMMENT '微信小程序二维码' AFTER `icon`;