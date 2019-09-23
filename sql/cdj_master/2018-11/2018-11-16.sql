ALTER TABLE `question_user_chose`
ADD COLUMN `project_item_id`  int(11) UNSIGNED NULL DEFAULT 0 COMMENT '项目问卷id' AFTER `project_id`;