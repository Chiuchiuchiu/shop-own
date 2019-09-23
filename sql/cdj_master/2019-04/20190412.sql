ALTER TABLE `house_relevance`
ADD COLUMN `parent_ids`  varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '上级id，直到项目。多个用逗号区分' AFTER `with_house_id`;