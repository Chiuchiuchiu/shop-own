ALTER TABLE `butler_auth`
ADD COLUMN `group` tinyint(4) DEFAULT 0 AFTER `status`;