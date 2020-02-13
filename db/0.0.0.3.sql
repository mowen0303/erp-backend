-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 14, 2020 at 12:04 AM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.2.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `erp`
--
CREATE DATABASE IF NOT EXISTS `erp` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `erp`;

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `company_id` int(11) NOT NULL,
  `company_name` char(150) NOT NULL,
  `company_country` char(50) NOT NULL,
  `company_owner_name` char(150) NOT NULL,
  `company_business_number` char(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`company_id`, `company_name`, `company_country`, `company_owner_name`, `company_business_number`) VALUES
(1, 'De-Valor International Co. Ltd', 'Canada', 'Test', '000000');

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

CREATE TABLE `image` (
  `image_id` int(11) UNSIGNED NOT NULL,
  `image_url` char(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_original_url` char(155) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `image_height` int(5) UNSIGNED NOT NULL,
  `image_width` int(5) UNSIGNED NOT NULL,
  `image_section_name` char(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_section_id` int(11) UNSIGNED NOT NULL,
  `image_post_time` int(10) UNSIGNED NOT NULL,
  `image_status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_login`
--

CREATE TABLE `log_login` (
  `log_login_id` int(11) NOT NULL,
  `log_login_user_id` int(11) NOT NULL,
  `log_login_ip` char(24) NOT NULL,
  `log_login_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `store`
--

CREATE TABLE `store` (
  `store_id` int(11) NOT NULL,
  `store_company_id` int(11) NOT NULL,
  `store_is_head_office` tinyint(1) NOT NULL DEFAULT 1,
  `store_country` char(50) NOT NULL,
  `store_province` char(50) NOT NULL,
  `store_city` char(100) NOT NULL,
  `store_address` char(255) NOT NULL,
  `store_post_code` char(6) NOT NULL,
  `store_phone` char(15) NOT NULL DEFAULT '',
  `store_email` char(150) NOT NULL DEFAULT '',
  `store_fax` char(15) NOT NULL DEFAULT '',
  `store_website` char(15) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `store`
--

INSERT INTO `store` (`store_id`, `store_company_id`, `store_is_head_office`, `store_country`, `store_province`, `store_city`, `store_address`, `store_post_code`, `store_phone`, `store_email`, `store_fax`, `store_website`) VALUES
(1, 1, 1, 'Canada', 'ON', 'Test', '20 kally st.', '000000', '000000', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_reference_user_id` int(11) NOT NULL DEFAULT 0 COMMENT '总公司的SALES ID',
  `user_user_category_id` int(11) NOT NULL,
  `user_store_id` int(11) NOT NULL,
  `user_last_name` char(30) NOT NULL,
  `user_first_name` char(30) NOT NULL,
  `user_email` char(100) NOT NULL,
  `user_avatar` char(255) NOT NULL DEFAULT '/upload/head-default.png',
  `user_pwd` char(64) NOT NULL,
  `user_role` char(30) NOT NULL,
  `user_phone` char(15) NOT NULL,
  `user_fax` char(15) NOT NULL DEFAULT '',
  `user_address` char(255) NOT NULL DEFAULT '',
  `user_business_hour` char(255) NOT NULL DEFAULT '',
  `user_register_time` datetime NOT NULL DEFAULT current_timestamp(),
  `user_last_login_time` datetime NOT NULL DEFAULT current_timestamp(),
  `user_status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_reference_user_id`, `user_user_category_id`, `user_store_id`, `user_last_name`, `user_first_name`, `user_email`, `user_avatar`, `user_pwd`, `user_role`, `user_phone`, `user_fax`, `user_address`, `user_business_hour`, `user_register_time`, `user_last_login_time`, `user_status`) VALUES
(1, 0, 1, 1, 'Test', 'Test', 'superadmin', '/upload/head-default.png', '96e79218965eb72c92a549dd5a330112', 'test', '6477734213', '', '41', '', '2020-02-13 18:02:45', '2020-02-13 18:03:15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_category`
--

CREATE TABLE `user_category` (
  `user_category_id` int(11) NOT NULL,
  `user_category_title` char(40) NOT NULL,
  `user_category_authority` text NOT NULL,
  `user_category_level` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_category`
--

INSERT INTO `user_category` (`user_category_id`, `user_category_title`, `user_category_authority`, `user_category_level`) VALUES
(1, 'Super Admin', '{\"SYSTEM_SETTING\":2,\"USER\":62,\"COMPANY\":62}', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `image_section_name_id` (`image_section_name`,`image_section_id`) USING BTREE;

--
-- Indexes for table `log_login`
--
ALTER TABLE `log_login`
  ADD PRIMARY KEY (`log_login_id`),
  ADD KEY `log_login_user_id` (`log_login_user_id`);

--
-- Indexes for table `store`
--
ALTER TABLE `store`
  ADD PRIMARY KEY (`store_id`),
  ADD KEY `store_company_id` (`store_company_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `user_category_id_idx` (`user_user_category_id`),
  ADD KEY `user_store_id_idx` (`user_store_id`);

--
-- Indexes for table `user_category`
--
ALTER TABLE `user_category`
  ADD PRIMARY KEY (`user_category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `image_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_login`
--
ALTER TABLE `log_login`
  MODIFY `log_login_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `store`
--
ALTER TABLE `store`
  MODIFY `store_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_category`
--
ALTER TABLE `user_category`
  MODIFY `user_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `log_login`
--
ALTER TABLE `log_login`
  ADD CONSTRAINT `log_login_ibfk_1` FOREIGN KEY (`log_login_user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `store`
--
ALTER TABLE `store`
  ADD CONSTRAINT `store_ibfk_1` FOREIGN KEY (`store_company_id`) REFERENCES `company` (`company_id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_category_id` FOREIGN KEY (`user_user_category_id`) REFERENCES `user_category` (`user_category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_store_id` FOREIGN KEY (`user_store_id`) REFERENCES `store` (`store_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
