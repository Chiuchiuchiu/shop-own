ALTER TABLE `shop`
ADD COLUMN `password`  varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' AFTER `mobile`;