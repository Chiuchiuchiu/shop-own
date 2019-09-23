-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2016-12-26 08:18:04
-- 服务器版本： 5.6.15
-- PHP Version: 7.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cdj_event`
--

-- --------------------------------------------------------

--
-- 表的结构 `prepay_lottery_gift`
--

CREATE TABLE `prepay_lottery_gift` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL,
  `stock` int(10) UNSIGNED NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `prepay_lottery_gift`
--

INSERT INTO `prepay_lottery_gift` (`id`, `name`, `amount`, `stock`, `status`) VALUES
(1, '谢谢参与', 120, 0, 1),
(2, '清风抽纸（盒）', 470, 0, 1),
(3, '金龙鱼1.8L（瓶）', 60, 0, 1),
(4, '福临门油粘米5kg（袋）', 50, 0, 1),
(5, '乐扣保鲜套装（套）', 25, 0, 1),
(6, '小米5S手机（台）', 1, 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `prepay_lottery_result`
--

CREATE TABLE `prepay_lottery_result` (
  `id` int(10) UNSIGNED NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `gift_id` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  `gave_at` int(10) UNSIGNED NOT NULL,
  `manager` varchar(20) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `prepay_lottery_result`
--

INSERT INTO `prepay_lottery_result` (`id`, `member_id`, `gift_id`, `created_at`, `gave_at`, `manager`) VALUES
(1, 1, 2, 1481856370, 0, ''),
(2, 1, 2, 1481856376, 0, ''),
(3, 1, 2, 1481856382, 0, ''),
(4, 1, 2, 1481856387, 0, ''),
(5, 1, 2, 1481856404, 0, ''),
(6, 1, 2, 1481856410, 0, ''),
(7, 1, 4, 1481856423, 0, ''),
(8, 1, 3, 1481856428, 0, ''),
(9, 1, 5, 1481856434, 0, ''),
(10, 1, 5, 1481856439, 0, ''),
(11, 1, 4, 1481856445, 0, ''),
(12, 1, 5, 1481856450, 0, ''),
(13, 1, 5, 1481856469, 0, ''),
(14, 1, 5, 1481856478, 0, ''),
(15, 1, 5, 1481856484, 0, ''),
(16, 1, 5, 1481856489, 0, ''),
(17, 1, 5, 1481856494, 0, ''),
(18, 1, 2, 1481856499, 0, ''),
(19, 1, 2, 1481856504, 0, ''),
(20, 1, 2, 1481856531, 0, ''),
(21, 1, 2, 1482032605, 0, ''),
(22, 1, 2, 1482032611, 0, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `prepay_lottery_gift`
--
ALTER TABLE `prepay_lottery_gift`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prepay_lottery_result`
--
ALTER TABLE `prepay_lottery_result`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `prepay_lottery_gift`
--
ALTER TABLE `prepay_lottery_gift`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- 使用表AUTO_INCREMENT `prepay_lottery_result`
--
ALTER TABLE `prepay_lottery_result`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
