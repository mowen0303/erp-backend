-- phpMyAdmin SQL Dump
-- version 4.9.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 11, 2020 at 02:22 PM
-- Server version: 5.7.30
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
(1, 'Woodworth Cabinetry - De-valor International Co. Ltd', 'Canada', 'Felix', '9056708787', '2018', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `company_location`
--

CREATE TABLE `company_location` (
  `company_location_id` int(11) NOT NULL,
  `company_location_company_id` int(11) NOT NULL,
  `company_location_is_head_office` tinyint(1) NOT NULL DEFAULT '1',
  `company_location_country` char(50) NOT NULL,
  `company_location_province` char(50) NOT NULL,
  `company_location_city` char(100) NOT NULL,
  `company_location_address` char(255) NOT NULL,
  `company_location_post_code` char(6) NOT NULL,
  `company_location_phone` char(15) NOT NULL DEFAULT '',
  `company_location_email` char(150) NOT NULL DEFAULT '',
  `company_location_fax` char(15) NOT NULL DEFAULT '',
  `company_location_website` char(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `company_location`
--

INSERT INTO `company_location` (`company_location_id`, `company_location_company_id`, `company_location_is_head_office`, `company_location_country`, `company_location_province`, `company_location_city`, `company_location_address`, `company_location_post_code`, `company_location_phone`, `company_location_email`, `company_location_fax`, `company_location_website`) VALUES
(1, 1, 1, 'Canada', 'On', 'Mississauga', '854 westport cres.', 'L5T1N5', '9056708787', 'info@woodworthcabinetry.com', '', 'woodworthcabinetry.com');

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
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
  `inventory_item_id` int(11) NOT NULL,
  `inventory_count` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `inventory_item_id`, `inventory_count`) VALUES
(1, 18, 211),
(2, 13, 23),
(3, 9, 2);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_log`
--

CREATE TABLE `inventory_log` (
  `inventory_log_id` int(11) NOT NULL,
  `inventory_log_warehouse_id` int(11) NOT NULL,
  `inventory_log_operator_id` int(11) NOT NULL,
  `inventory_log_deliver_id` int(11) NOT NULL,
  `inventory_log_type` char(10) NOT NULL COMMENT '1:in, 0:out',
  `inventory_log_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `inventory_log_note` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `inventory_log`
--

INSERT INTO `inventory_log` (`inventory_log_id`, `inventory_log_warehouse_id`, `inventory_log_operator_id`, `inventory_log_deliver_id`, `inventory_log_type`, `inventory_log_time`, `inventory_log_note`) VALUES
(1, 1, 1, 2, 'in', '2020-05-05 16:35:59', 'GOOD'),
(2, 1, 1, 0, 'in', '2020-05-07 14:01:38', '');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_warehouse`
--

CREATE TABLE `inventory_warehouse` (
  `inventory_warehouse_id` int(11) NOT NULL,
  `inventory_warehouse_warehouse_id` int(11) NOT NULL,
  `inventory_warehouse_item_id` int(11) NOT NULL,
  `inventory_warehouse_aisle` char(10) NOT NULL DEFAULT '',
  `inventory_warehouse_column` char(10) NOT NULL DEFAULT '',
  `inventory_warehouse_count` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `inventory_warehouse`
--

INSERT INTO `inventory_warehouse` (`inventory_warehouse_id`, `inventory_warehouse_warehouse_id`, `inventory_warehouse_item_id`, `inventory_warehouse_aisle`, `inventory_warehouse_column`, `inventory_warehouse_count`) VALUES
(1, 1, 18, 'A', '1', 211),
(2, 1, 9, '12', '23', 2),
(3, 1, 13, 'e', '3', 23);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_warehouse_log`
--

CREATE TABLE `inventory_warehouse_log` (
  `inventory_warehouse_log_id` int(11) NOT NULL,
  `inventory_warehouse_log_inventory_log_id` int(11) NOT NULL,
  `inventory_warehouse_log_item_id` int(11) NOT NULL,
  `inventory_warehouse_log_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `inventory_warehouse_log`
--

INSERT INTO `inventory_warehouse_log` (`inventory_warehouse_log_id`, `inventory_warehouse_log_inventory_log_id`, `inventory_warehouse_log_item_id`, `inventory_warehouse_log_count`) VALUES
(1, 1, 18, 90),
(2, 2, 13, 23),
(3, 2, 9, 2),
(4, 2, 18, 121);

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `item_id` int(11) NOT NULL,
  `item_item_category_id` int(11) NOT NULL,
  `item_item_style_id` int(11) NOT NULL,
  `item_sku` char(50) NOT NULL,
  `item_name` char(150) NOT NULL DEFAULT '',
  `item_l` decimal(6,4) NOT NULL,
  `item_w` decimal(6,4) NOT NULL,
  `item_h` decimal(6,4) NOT NULL,
  `item_description` char(255) NOT NULL DEFAULT '',
  `item_price` decimal(8,2) NOT NULL,
  `item_image` char(150) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`item_id`, `item_item_category_id`, `item_item_style_id`, `item_sku`, `item_name`, `item_l`, `item_w`, `item_h`, `item_description`, `item_price`, `item_image`) VALUES
(4, 17, 1, 'B09-F-SW', '', 0.0000, 0.0000, 0.0000, '', 120.00, ''),
(5, 16, 8, 'B09FD-BOX', '', 0.0000, 0.0000, 0.0000, '', 120.00, ''),
(6, 16, 8, 'B09SR-BOX', '', 0.0000, 0.0000, 0.0000, '', 120.00, ''),
(7, 17, 3, 'B12-F-CC', '', 0.0000, 0.0000, 0.0000, '', 120.00, ''),
(8, 17, 2, 'B12-F-SG', '', 0.0000, 0.0000, 0.0000, '', 120.00, ''),
(9, 16, 8, 'B12-BOX', '', 0.0000, 0.0000, 0.0000, '', 120.00, ''),
(10, 17, 1, 'W1230-F-SW', '', 0.0000, 0.0000, 0.0000, '', 120.00, ''),
(11, 16, 8, 'W1230-BOX', '', 0.0000, 0.0000, 0.0000, '', 120.00, ''),
(12, 20, 1, 'WR1818-SW', '', 0.0000, 0.0000, 0.0000, '', 120.00, ''),
(13, 20, 1, 'SGH30-SW', '', 0.0000, 0.0000, 0.0000, '', 120.00, ''),
(14, 20, 1, 'RHA363021-SW', '', 0.0000, 0.0000, 0.0000, '', 120.00, ''),
(15, 19, 1, 'WP1230-SW', '', 0.0000, 0.0000, 0.0000, '', 120.00, ''),
(16, 19, 1, 'DWR3-SW', '', 0.0000, 0.0000, 0.0000, '', 120.00, ''),
(17, 19, 1, 'BP4896-SW', '', 0.0000, 0.0000, 0.0000, '', 120.00, ''),
(18, 21, 1, 'SD-1-SW', '', 0.0000, 0.0000, 0.0000, '', 0.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `item_category`
--

CREATE TABLE `item_category` (
  `item_category_id` int(11) NOT NULL,
  `item_category_title` char(255) NOT NULL,
  `item_category_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `item_category`
--

INSERT INTO `item_category` (`item_category_id`, `item_category_title`, `item_category_description`) VALUES
(16, 'BOX', '柜体'),
(17, 'Door&Frame', ''),
(18, 'Hardware', 'Hinge, Slider, Handle, Nob'),
(19, 'Accessory', 'Molding, Filler, Dummy Door, Panel, Valance, Stand Post'),
(20, 'Storage & Organization & Hood & Specialty Cabinet', 'Wine Rack, Open Shelf'),
(21, 'Sample', '');

-- --------------------------------------------------------

--
-- Table structure for table `item_style`
--

CREATE TABLE `item_style` (
  `item_style_id` int(11) NOT NULL,
  `item_style_title` char(255) NOT NULL,
  `item_style_description` text NOT NULL,
  `item_style_image_cover` char(150) NOT NULL DEFAULT '',
  `item_style_image_description` char(150) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `item_style`
--

INSERT INTO `item_style` (`item_style_id`, `item_style_title`, `item_style_description`, `item_style_image_cover`, `item_style_image_description`) VALUES
(1, 'SW', 'Shaker White', '', ''),
(2, 'SG', 'Shaker Grey', '', ''),
(3, 'CC', 'Concord Chestnut', '', ''),
(4, 'WC', 'Willow Grey', '', ''),
(6, 'BOX', '柜体', '', ''),
(7, 'Hardware', 'hinge, slider, handle, nob', '', ''),
(8, 'NO-STYLE', '', '', '');

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
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_reference_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '总公司的SALES ID',
  `user_user_category_id` int(11) NOT NULL,
  `user_company_location_id` int(11) NOT NULL,
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

INSERT INTO `user` (`user_id`, `user_reference_user_id`, `user_user_category_id`, `user_company_location_id`, `user_last_name`, `user_first_name`, `user_email`, `user_avatar`, `user_pwd`, `user_role`, `user_phone`, `user_fax`, `user_address`, `user_business_hour`, `user_register_time`, `user_last_login_time`, `user_status`) VALUES
(1, 0, 1, 1, 'Admin', 'Super', 'superadmin', '/upload/thumbnail/1_15820820845e4ca8242e0d82.15518426.jpg', '01ff38c60e01bba644901f783753adf8', 'President', '9056708787', '', '', '', '2020-02-13 18:02:45', '2020-05-10 22:12:45', 1),
(2, 0, 2, 1, 'Alex', 'Jin', 'Alexander.jin@de-valor.ca', '/upload/head-default.png', '670b14728ad9902aecba32e22fa4f6bd', 'Manager', '6478356087', '', '', '', '2020-03-26 10:13:00', '2020-03-26 10:13:00', 1),
(3, 0, 4, 1, 'Penny', 'Penny', 'penny@de-valor.ca', '/upload/head-default.png', '670b14728ad9902aecba32e22fa4f6bd', 'Others', '6476187588', '', '', '', '2020-03-27 08:26:37', '2020-05-08 13:27:24', 1);

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
(1, 'Super Admin', '{\"SYSTEM_SETTING\":2,\"USER\":126,\"COMPANY\":62,\"DEALER_APPLICATION\":6,\"ITEM\":30,\"WAREHOUSE\":30,\"INVENTORY\":14,\"INVENTORY_LOG\":2}', 1),
(2, 'Vice President', '{\"SYSTEM_SETTING\":0,\"USER\":62,\"COMPANY\":62,\"DEALER_APPLICATION\":52}', 2),
(3, 'General Manager', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 3),
(4, 'General Accountant', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":62,\"DEALER_APPLICATION\":6,\"ITEM\":30}', 3),
(5, 'Sales Manager', '{\"SYSTEM_SETTING\":0,\"USER\":62,\"COMPANY\":62,\"DEALER_APPLICATION\":6}', 4),
(6, 'Warehouse Manager', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 4),
(7, 'Accountant', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 4),
(8, 'Sales', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 5),
(9, 'Warehouse Supervisor', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 5),
(10, 'Dealer', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 6),
(11, 'Storekeeper', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 6),
(12, 'Assembler', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 6),
(13, 'Deliveryman', '{\"SYSTEM_SETTING\":0,\"USER\":0,\"COMPANY\":0,\"DEALER_APPLICATION\":0}', 6);

-- --------------------------------------------------------

--
-- Table structure for table `warehouse`
--

CREATE TABLE `warehouse` (
  `warehouse_id` int(11) NOT NULL,
  `warehouse_address` char(255) NOT NULL DEFAULT '',
  `warehouse_city` char(50) NOT NULL DEFAULT '',
  `warehouse_province` char(50) NOT NULL DEFAULT '''''',
  `warehouse_country` char(50) NOT NULL DEFAULT '',
  `warehouse_post_code` char(10) NOT NULL DEFAULT '',
  `warehouse_phone` char(15) NOT NULL DEFAULT '',
  `warehouse_fax` char(15) NOT NULL DEFAULT '',
  `warehouse_description` text,
  `warehouse_longitude` float NOT NULL DEFAULT '0',
  `warehouse_latitude` float NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `warehouse`
--

INSERT INTO `warehouse` (`warehouse_id`, `warehouse_address`, `warehouse_city`, `warehouse_province`, `warehouse_country`, `warehouse_post_code`, `warehouse_phone`, `warehouse_fax`, `warehouse_description`, `warehouse_longitude`, `warehouse_latitude`) VALUES
(1, '41 Copperstone Crescent', 'Richmond Hill', 'ON', 'Canada', 'L4S 2C5', '6477734213', '', '', 1, 1),
(2, '854 Westport Crescent', 'Mississauga', 'ON', 'Canada', 'L5T 1N5', '9056708787', '', '', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `company_location`
--
ALTER TABLE `company_location`
  ADD PRIMARY KEY (`company_location_id`),
  ADD KEY `company_location_company_idx` (`company_location_company_id`) USING BTREE;

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `image_section_name_id` (`file_section_name`,`file_section_id`) USING BTREE;

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD UNIQUE KEY `inventory_item_id` (`inventory_item_id`);

--
-- Indexes for table `inventory_log`
--
ALTER TABLE `inventory_log`
  ADD PRIMARY KEY (`inventory_log_id`),
  ADD KEY `inventory_log_operator_id` (`inventory_log_operator_id`),
  ADD KEY `inventory_log_deliver_id` (`inventory_log_deliver_id`),
  ADD KEY `inventory_log_warehouse_id` (`inventory_log_warehouse_id`);

--
-- Indexes for table `inventory_warehouse`
--
ALTER TABLE `inventory_warehouse`
  ADD PRIMARY KEY (`inventory_warehouse_id`),
  ADD KEY `inventory_map_warehouse_id` (`inventory_warehouse_warehouse_id`,`inventory_warehouse_item_id`),
  ADD KEY `inventory_map_item_id` (`inventory_warehouse_item_id`);

--
-- Indexes for table `inventory_warehouse_log`
--
ALTER TABLE `inventory_warehouse_log`
  ADD PRIMARY KEY (`inventory_warehouse_log_id`);

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
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `user_category_id_idx` (`user_user_category_id`),
  ADD KEY `user_company_location_idx` (`user_company_location_id`) USING BTREE;

--
-- Indexes for table `user_category`
--
ALTER TABLE `user_category`
  ADD PRIMARY KEY (`user_category_id`);

--
-- Indexes for table `warehouse`
--
ALTER TABLE `warehouse`
  ADD PRIMARY KEY (`warehouse_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `company_location`
--
ALTER TABLE `company_location`
  MODIFY `company_location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
  MODIFY `file_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventory_log`
--
ALTER TABLE `inventory_log`
  MODIFY `inventory_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory_warehouse`
--
ALTER TABLE `inventory_warehouse`
  MODIFY `inventory_warehouse_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventory_warehouse_log`
--
ALTER TABLE `inventory_warehouse_log`
  MODIFY `inventory_warehouse_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `item_category`
--
ALTER TABLE `item_category`
  MODIFY `item_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `item_style`
--
ALTER TABLE `item_style`
  MODIFY `item_style_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_category`
--
ALTER TABLE `user_category`
  MODIFY `user_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `warehouse`
--
ALTER TABLE `warehouse`
  MODIFY `warehouse_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `company_location`
--
ALTER TABLE `company_location`
  ADD CONSTRAINT `company_location_ibfk_1` FOREIGN KEY (`company_location_company_id`) REFERENCES `company` (`company_id`);

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`inventory_item_id`) REFERENCES `item` (`item_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

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
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_category_id` FOREIGN KEY (`user_user_category_id`) REFERENCES `user_category` (`user_category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_store_id` FOREIGN KEY (`user_company_location_id`) REFERENCES `company_location` (`company_location_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
