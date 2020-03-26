-- phpMyAdmin SQL Dump
-- version 4.9.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 25, 2020 at 11:47 PM
-- Server version: 5.7.29
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `woodwort_erp`
--

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `company_id` int(11) NOT NULL,
  `company_name` char(150) NOT NULL,
  `company_country` char(50) NOT NULL,
  `company_owner_name` char(150) NOT NULL,
  `company_business_number` char(24) NOT NULL,
  `company_startup_year` char(4) NOT NULL DEFAULT '',
  `company_type` char(100) NOT NULL DEFAULT '',
  `company_role` char(100) NOT NULL DEFAULT '',
  `company_license_file` char(150) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`company_id`, `company_name`, `company_country`, `company_owner_name`, `company_business_number`, `company_startup_year`, `company_type`, `company_role`, `company_license_file`) VALUES
(1, 'De-Valor International Co. Ltd', 'Canada', 'Test', '000000', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE `file` (
  `file_id` int(11) UNSIGNED NOT NULL,
  `file_type` char(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'img',
  `file_url` char(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_original_url` char(155) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `file_height` int(5) UNSIGNED NOT NULL DEFAULT '0',
  `file_width` int(5) UNSIGNED NOT NULL DEFAULT '0',
  `file_section_name` char(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_section_id` int(11) UNSIGNED NOT NULL,
  `file_post_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `file_status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `item_id` int(11) NOT NULL,
  `item_item_category_id` int(11) NOT NULL,
  `item_item_style_id` int(11) NOT NULL,
  `item_sku` char(50) NOT NULL,
  `item_w` decimal(6,2) NOT NULL,
  `item_h` decimal(6,2) NOT NULL,
  `item_d` decimal(6,2) NOT NULL,
  `item_description` char(255) NOT NULL DEFAULT '',
  `item_price` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item_category`
--

CREATE TABLE `item_category` (
  `item_category_id` int(11) NOT NULL,
  `item_category_title` char(255) NOT NULL,
  `item_category_image` char(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item_style`
--

CREATE TABLE `item_style` (
  `item_style_id` int(11) NOT NULL,
  `item_style_title` char(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `log_login`
--

CREATE TABLE `log_login` (
  `log_login_id` int(11) NOT NULL,
  `log_login_user_id` int(11) NOT NULL,
  `log_login_ip` char(24) NOT NULL,
  `log_login_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `register_id` int(11) NOT NULL,
  `register_company_name` char(255) NOT NULL DEFAULT '',
  `register_company_number` char(255) NOT NULL DEFAULT '',
  `register_company_owner_first_name` char(255) NOT NULL DEFAULT '',
  `register_company_owner_last_name` char(255) NOT NULL DEFAULT '',
  `register_company_address` char(255) NOT NULL DEFAULT '',
  `register_company_city` char(255) NOT NULL DEFAULT '',
  `register_company_province` char(255) NOT NULL DEFAULT '',
  `register_company_postcode` char(255) NOT NULL DEFAULT '',
  `register_company_country` char(255) NOT NULL DEFAULT '',
  `register_company_phone` char(255) NOT NULL DEFAULT '',
  `register_company_fax` char(255) NOT NULL DEFAULT '',
  `register_company_email` char(255) NOT NULL DEFAULT '',
  `register_company_website` char(255) NOT NULL DEFAULT '',
  `register_first_name` char(255) NOT NULL DEFAULT '',
  `register_last_name` char(255) NOT NULL DEFAULT '',
  `register_role` char(255) NOT NULL DEFAULT '',
  `register_phone` char(255) NOT NULL DEFAULT '',
  `register_email` char(255) NOT NULL DEFAULT '',
  `register_businessCardFile` char(255) NOT NULL DEFAULT '',
  `register_driverLicenseFile` char(255) NOT NULL DEFAULT '',
  `register_company_start_year` char(255) NOT NULL DEFAULT '',
  `register_company_type` char(255) NOT NULL DEFAULT '',
  `register_company_role` char(255) NOT NULL DEFAULT '',
  `register_businessLicenseFile` char(255) NOT NULL DEFAULT '',
  `register_refer_name` char(255) NOT NULL DEFAULT '',
  `register_refer_media` char(255) NOT NULL DEFAULT '',
  `register_reject_reason` text NOT NULL,
  `register_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT ' 0:删除  1:等待审核 2:审核中 3:审核通过 4:拒绝'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `store`
--

CREATE TABLE `store` (
  `store_id` int(11) NOT NULL,
  `store_company_id` int(11) NOT NULL,
  `store_is_head_office` tinyint(1) NOT NULL DEFAULT '1',
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
  `user_reference_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '总公司的SALES ID',
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
  `user_register_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_last_login_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_reference_user_id`, `user_user_category_id`, `user_store_id`, `user_last_name`, `user_first_name`, `user_email`, `user_avatar`, `user_pwd`, `user_role`, `user_phone`, `user_fax`, `user_address`, `user_business_hour`, `user_register_time`, `user_last_login_time`, `user_status`) VALUES
(1, 0, 1, 1, 'Test', 'Test', 'superadmin', '/upload/thumbnail/1_15820820845e4ca8242e0d82.15518426.jpg', '96e79218965eb72c92a549dd5a330112', 'President', '6477734213', '', '41', '', '2020-02-13 18:02:45', '2020-03-25 23:45:45', 1);

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
(1, 'Super Admin', '{\"SYSTEM_SETTING\":2,\"USER\":62,\"COMPANY\":62,\"DEALER_APPLICATION\":6}', 1),
(2, 'Vice President', '{\"SYSTEM_SETTING\":0,\"USER\":62,\"COMPANY\":62,\"DEALER_APPLICATION\":52}', 2),
(3, 'General Manager', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 3),
(4, 'General Accountant', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 3),
(5, 'Sales Manager', '{\"SYSTEM_SETTING\":0,\"USER\":62,\"COMPANY\":62,\"DEALER_APPLICATION\":6}', 4),
(6, 'Warehouse Manager', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 4),
(7, 'Accountant', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 4),
(8, 'Sales', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 5),
(9, 'Warehouse Supervisor', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 5),
(10, 'Dealer', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 6),
(11, 'Storekeeper', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 6),
(12, 'Assembler', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 6),
(13, 'Deliveryman', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 6);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `image_section_name_id` (`file_section_name`,`file_section_id`) USING BTREE;

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`item_id`),
  ADD UNIQUE KEY `item_sku` (`item_sku`),
  ADD KEY `item_category_id` (`item_item_category_id`),
  ADD KEY `item_style_id` (`item_item_style_id`);

--
-- Indexes for table `item_category`
--
ALTER TABLE `item_category`
  ADD PRIMARY KEY (`item_category_id`);

--
-- Indexes for table `item_style`
--
ALTER TABLE `item_style`
  ADD PRIMARY KEY (`item_style_id`);

--
-- Indexes for table `log_login`
--
ALTER TABLE `log_login`
  ADD PRIMARY KEY (`log_login_id`),
  ADD KEY `log_login_user_id` (`log_login_user_id`);

--
-- Indexes for table `register`
--
ALTER TABLE `register`
  ADD PRIMARY KEY (`register_id`);

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
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
  MODIFY `file_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_category`
--
ALTER TABLE `item_category`
  MODIFY `item_category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_style`
--
ALTER TABLE `item_style`
  MODIFY `item_style_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_login`
--
ALTER TABLE `log_login`
  MODIFY `log_login_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `register_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `user_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`item_item_category_id`) REFERENCES `item_category` (`item_category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `item_ibfk_2` FOREIGN KEY (`item_item_style_id`) REFERENCES `item_style` (`item_style_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

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
