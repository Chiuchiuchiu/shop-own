CREATE TABLE `wechat_cash_coupon` (
  `id` int(10) UNSIGNED NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(6,2) NOT NULL,
  `pm_order_id` int(10) UNSIGNED NOT NULL,
  `remark` varchar(200) NOT NULL,
  `result` varchar(500) NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  `completed_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `wechat_cash_coupon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pm_order_id` (`pm_order_id`);
ALTER TABLE `wechat_cash_coupon`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `wechat_red_pack` ADD `status` TINYINT UNSIGNED NOT NULL AFTER `result`;
ALTER TABLE `wechat_red_pack` ADD `number` CHAR(20) NOT NULL AFTER `id`;