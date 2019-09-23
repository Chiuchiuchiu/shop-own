ALTER TABLE `question_project`
MODIFY COLUMN `start_date`  date NULL DEFAULT NULL AFTER `title`,
MODIFY COLUMN `end_date`  date NULL DEFAULT NULL AFTER `start_date`;

