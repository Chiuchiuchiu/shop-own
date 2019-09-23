ALTER TABLE `house`
DROP INDEX `orderby`,
ADD INDEX `reskind_deepest_idx`(`reskind`, `deepest_node`);