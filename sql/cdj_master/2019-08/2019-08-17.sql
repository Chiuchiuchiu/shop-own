ALTER TABLE `banner`
ADD COLUMN `projects`  text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '可视项目，多个用逗号分隔' AFTER `status`;
ADD COLUMN `sort`  int(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序，0-100，数字越大越靠前' AFTER `projects`;


