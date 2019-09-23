-- 对旧的调研项目补关联调研问卷id
update `cdj`.`question_item` set `question_id` = 3 where id not in (33, 34)