--删除uid，改为关联question_project的主键
ALTER TABLE `question_item`
CHANGE COLUMN `uid` `question_id`  int(10) UNSIGNED NULL DEFAULT 0 COMMENT '问卷调查id' AFTER `id`;
