-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 10, 2020 at 01:33 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `company_location`
--

CREATE TABLE `company_location` (
  `company_location_id` int(11) NOT NULL,
  `company_location_company_id` int(11) NOT NULL,
  `company_location_is_head_office` tinyint(1) NOT NULL DEFAULT 1,
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

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE `file` (
  `file_id` int(11) UNSIGNED NOT NULL,
  `file_type` char(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'img',
  `file_url` char(155) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_original_url` char(155) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `file_height` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `file_width` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `file_section_name` char(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_section_id` int(11) UNSIGNED NOT NULL,
  `file_post_time` datetime NOT NULL DEFAULT current_timestamp(),
  `file_status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
  `inventory_item_id` int(11) NOT NULL,
  `inventory_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `inventory_log_time` datetime NOT NULL DEFAULT current_timestamp(),
  `inventory_log_note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `inventory_warehouse_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `item_id` int(11) NOT NULL,
  `item_item_category_id` int(11) NOT NULL,
  `item_item_style_id` int(11) NOT NULL,
  `item_sku` char(50) NOT NULL,
  `item_weight` decimal(6,2) NOT NULL,
  `item_l` decimal(6,4) NOT NULL,
  `item_w` decimal(6,4) NOT NULL,
  `item_h` decimal(6,4) NOT NULL,
  `item_description` char(255) NOT NULL DEFAULT '',
  `item_price` int(11) NOT NULL,
  `item_image` char(150) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `item_category`
--

CREATE TABLE `item_category` (
  `item_category_id` int(11) NOT NULL,
  `item_category_title` char(255) NOT NULL,
  `item_category_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_sku` char(255) NOT NULL,
  `product_name` char(255) NOT NULL,
  `product_product_category_id` int(11) NOT NULL,
  `product_item_style_id` int(11) NOT NULL,
  `product_weight` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `product_price` int(11) NOT NULL DEFAULT 0,
  `product_w` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `product_h` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `product_l` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `product_expected_count` int(11) NOT NULL DEFAULT 0,
  `product_img_0` char(150) NOT NULL DEFAULT '',
  `product_img_1` char(150) NOT NULL DEFAULT '',
  `product_img_2` char(150) NOT NULL DEFAULT '',
  `product_img_3` char(150) NOT NULL DEFAULT '',
  `product_img_4` char(150) NOT NULL DEFAULT '',
  `product_des` text NOT NULL,
  `product_feature_1` char(255) NOT NULL DEFAULT '',
  `product_feature_2` char(255) NOT NULL DEFAULT '',
  `product_feature_3` char(255) NOT NULL DEFAULT '',
  `product_feature_4` char(255) NOT NULL DEFAULT '',
  `product_feature_5` char(255) NOT NULL DEFAULT '',
  `product_is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `product_category_id` int(11) NOT NULL,
  `product_category_title` char(255) NOT NULL,
  `product_category_des` char(255) NOT NULL,
  `product_category_img` char(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`product_category_id`, `product_category_title`, `product_category_des`, `product_category_img`) VALUES
(1, '123fsdfscccc', 'fff', '/upload/thumbnail/1_15979721575f3f1ebd2a0e65.81153888.png'),
(2, '222132', 'sdfsdf', '/upload/thumbnail/1_15979730305f3f22267e03b0.33647895.png'),
(3, '123', '3123dfsfsdf', '/upload/thumbnail/1_15979800495f3f3d91861da6.34904843.png');

-- --------------------------------------------------------

--
-- Table structure for table `product_relation`
--

CREATE TABLE `product_relation` (
  `product_relation_id` int(11) NOT NULL,
  `product_relation_product_id` int(11) NOT NULL,
  `product_relation_item_id` int(11) NOT NULL,
  `product_relation_item_count` smallint(5) UNSIGNED NOT NULL DEFAULT 0
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
  `register_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT ' 0:删除  1:等待审核 2:审核中 3:审核通过 4:拒绝'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_reference_user_id` int(11) NOT NULL DEFAULT 0 COMMENT '总公司的SALES ID',
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
  `user_register_time` datetime NOT NULL DEFAULT current_timestamp(),
  `user_last_login_time` datetime NOT NULL DEFAULT current_timestamp(),
  `user_status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `warehouse_description` text DEFAULT NULL,
  `warehouse_longitude` float NOT NULL DEFAULT 0,
  `warehouse_latitude` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_manager`
--

CREATE TABLE `warehouse_manager` (
  `warehouse_manager_id` int(11) NOT NULL,
  `warehouse_manager_warehouse_id` int(11) NOT NULL,
  `warehouse_manager_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `product_product_category_id` (`product_product_category_id`),
  ADD KEY `product_item_style_id` (`product_item_style_id`),
  ADD KEY `product_sku` (`product_sku`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`product_category_id`);

--
-- Indexes for table `product_relation`
--
ALTER TABLE `product_relation`
  ADD PRIMARY KEY (`product_relation_id`),
  ADD KEY `product_relation_product_id` (`product_relation_product_id`),
  ADD KEY `product_relation_product_id_2` (`product_relation_product_id`,`product_relation_item_id`);

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
-- Indexes for table `warehouse_manager`
--
ALTER TABLE `warehouse_manager`
  ADD PRIMARY KEY (`warehouse_manager_id`),
  ADD KEY `warehouse_manager_warehouse_id` (`warehouse_manager_warehouse_id`),
  ADD KEY `warehouse_manager_user_id` (`warehouse_manager_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_location`
--
ALTER TABLE `company_location`
  MODIFY `company_location_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
  MODIFY `file_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_log`
--
ALTER TABLE `inventory_log`
  MODIFY `inventory_log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_warehouse`
--
ALTER TABLE `inventory_warehouse`
  MODIFY `inventory_warehouse_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_warehouse_log`
--
ALTER TABLE `inventory_warehouse_log`
  MODIFY `inventory_warehouse_log_id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_category`
--
ALTER TABLE `product_category`
  MODIFY `product_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_relation`
--
ALTER TABLE `product_relation`
  MODIFY `product_relation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `register_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_category`
--
ALTER TABLE `user_category`
  MODIFY `user_category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warehouse`
--
ALTER TABLE `warehouse`
  MODIFY `warehouse_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warehouse_manager`
--
ALTER TABLE `warehouse_manager`
  MODIFY `warehouse_manager_id` int(11) NOT NULL AUTO_INCREMENT;

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
