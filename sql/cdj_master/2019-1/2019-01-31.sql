-- 顺德博澳城项目->区域7->摩托车车位->6区->4426
-- 认证业主抽到200红包但金额太高无法使用，故迁移到房屋->
-- 顺德博澳城项目->区域7->住宅六区6座->1单元->1901 使用
-- 抽到的业主 member_id:67991
-- house_id:513703迁移到498882
-- 已执行，此处仅作记录

update `cdj`.`auth_house_notification_member` set `house_id`=498882 where `id`=147664;

update `cdj`.`member_promotion_code` set `house_id`=498882 where `id`=72455;

update `cdj_event`.`wechat_red_pack` set `house_id`=498882 where `id`=74165;