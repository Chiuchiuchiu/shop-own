-- 添加管家上门抄表字段
ALTER TABLE `meter_log`
MODIFY COLUMN `butler_id`  int(10) UNSIGNED NULL DEFAULT 0 COMMENT '审核人id' AFTER `house_id`,
ADD COLUMN `butler_updated_at`  int(11) UNSIGNED NULL DEFAULT 0 COMMENT '管家更新时间' AFTER `updated_at`;
ADD COLUMN `visit_butler_id`  int(10) UNSIGNED NULL DEFAULT 0 COMMENT '上门抄表的管家id' AFTER `butler_id`;

-- 统一抄表状态
ALTER TABLE `meter_log`
MODIFY COLUMN `status`  tinyint(1) NULL DEFAULT 0 COMMENT '是否完成2待审核3无效4已审批5已上报' AFTER `meter_id`;

ALTER TABLE `meter_house`
MODIFY COLUMN `status`  tinyint(1) NULL DEFAULT 0 COMMENT '是否完成2待审核3无效4已审批5已上报' AFTER `meter_id`;



