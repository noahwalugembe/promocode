-- phpMyAdmin SQL Dump
-- version 4.8.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2018 at 08:13 PM
-- Server version: 10.1.31-MariaDB
-- PHP Version: 7.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `safeboda_promo_code`
--

-- --------------------------------------------------------

--
-- Table structure for table `rw_events`
--

CREATE TABLE `rw_events` (
  `id` mediumint(9) NOT NULL,
  `event_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rw_events`
--

INSERT INTO `rw_events` (`id`, `event_id`) VALUES
(1, 'testevent');

-- --------------------------------------------------------

--
-- Table structure for table `rw_promo_code`
--

CREATE TABLE `rw_promo_code` (
  `id` mediumint(9) NOT NULL,
  `event_id` tinyint(4) NOT NULL,
  `code` varchar(255) NOT NULL,
  `unlock_code` varchar(255) NOT NULL,
  `uses_remaining` smallint(6) NOT NULL,
  `amount` varchar(15) DEFAULT NULL,
  `expire_date` varchar(15) DEFAULT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL,
  `venue_lat` varchar(15) DEFAULT NULL,
  `venue_long` varchar(15) DEFAULT NULL,
  `radius` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rw_promo_code`
--

INSERT INTO `rw_promo_code` (`id`, `event_id`, `code`, `unlock_code`, `uses_remaining`, `amount`, `expire_date`, `active`, `venue_lat`, `venue_long`, `radius`) VALUES
(1, 1, 'test', '1', 996, '112', '2015-12-31', 1, '47.117828', '-88.545625', '3333'),
(7, 0, '7UX', 'qqoooo', 33, NULL, NULL, 0, NULL, NULL, NULL),
(8, 0, 'G5D', 'qqoooo', 33, '3444', '1222', 1, '47.117828 ', '-88.545625', NULL),
(39, 0, 'YT6', '1', 1111, '112', '2015-12-31', 1, '47.117828', '-88.545625', '33');

-- --------------------------------------------------------

--
-- Table structure for table `rw_promo_code_redeemed`
--

CREATE TABLE `rw_promo_code_redeemed` (
  `id` mediumint(9) NOT NULL,
  `rw_promo_code_id` mediumint(9) NOT NULL,
  `device_id` varchar(255) NOT NULL,
  `redeemed_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rw_promo_code_redeemed`
--

INSERT INTO `rw_promo_code_redeemed` (`id`, `rw_promo_code_id`, `device_id`, `redeemed_time`) VALUES
(64, 1, '22', '2018-05-21 18:11:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rw_promo_code`
--
ALTER TABLE `rw_promo_code`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rw_promo_code_redeemed`
--
ALTER TABLE `rw_promo_code_redeemed`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rw_promo_code`
--
ALTER TABLE `rw_promo_code`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `rw_promo_code_redeemed`
--
ALTER TABLE `rw_promo_code_redeemed`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
