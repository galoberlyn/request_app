-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2017 at 08:23 AM
-- Server version: 5.7.14
-- PHP Version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `scis_requisition_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `delivered_items`
--

CREATE TABLE `delivered_items` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `rs_item_no` int(11) NOT NULL COMMENT 'id nang rs_slip',
  `quantity` int(11) NOT NULL COMMENT 'total qty of items references quantity on items',
  `qty_delivered` int(11) NOT NULL COMMENT 'quantity delivered - quantity not delivered',
  `date` timestamp NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='mga delivered items para ma trace yung partial items deliver';

--
-- Dumping data for table `delivered_items`
--

INSERT INTO `delivered_items` (`id`, `item_name`, `rs_item_no`, `quantity`, `qty_delivered`, `date`, `created_at`, `updated_at`) VALUES
(5, '32', 5, 32, 0, '2017-12-14 07:11:55', '2017-12-14 07:11:55', '2017-12-14 07:11:55'),
(6, '32', 10, 32, 0, '2017-12-14 07:15:57', '2017-12-14 07:15:57', '2017-12-14 07:15:57'),
(7, '2', 11, 2, 0, '2017-12-14 07:26:32', '2017-12-14 07:26:32', '2017-12-14 07:26:32'),
(8, '23', 12, 1, 0, '2017-12-14 07:36:06', '2017-12-14 07:36:06', '2017-12-14 07:36:06');

-- --------------------------------------------------------

--
-- Table structure for table `itemsnotpo`
--

CREATE TABLE `itemsnotpo` (
  `id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date_accomplished` date DEFAULT NULL,
  `request_slip_no` int(11) NOT NULL COMMENT 'request_slip.id',
  `amount` double DEFAULT '0',
  `itemStatus` enum('Pending','Canceled','Delivered') NOT NULL DEFAULT 'Pending',
  `remarks` varchar(255) DEFAULT 'None',
  `supplier` double DEFAULT NULL COMMENT 'unit cost na to',
  `serial_number` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `qty_delivered_nopo` int(11) NOT NULL DEFAULT '0' COMMENT 'quantity delivered on items'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `itemsnotpo`
--

INSERT INTO `itemsnotpo` (`id`, `quantity`, `description`, `date_accomplished`, `request_slip_no`, `amount`, `itemStatus`, `remarks`, `supplier`, `serial_number`, `model`, `qty_delivered_nopo`) VALUES
(3, 32, '32', NULL, 5, 0, 'Pending', 'None', 23, NULL, NULL, 0),
(4, 32, '32', NULL, 10, 0, 'Pending', 'None', 23, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `itemspo`
--

CREATE TABLE `itemspo` (
  `iditemspo` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `unitprice` float DEFAULT '0',
  `amount` double DEFAULT '0',
  `poid` int(11) NOT NULL,
  `itemspostatus` enum('Pending','Canceled','Delivered') NOT NULL DEFAULT 'Pending',
  `date_complete` date DEFAULT NULL,
  `supplier_po` double DEFAULT NULL COMMENT 'unit cost na to',
  `serial_number` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `qty_delivered_po` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Items for PO';

--
-- Dumping data for table `itemspo`
--

INSERT INTO `itemspo` (`iditemspo`, `quantity`, `description`, `remarks`, `Location`, `unitprice`, `amount`, `poid`, `itemspostatus`, `date_complete`, `supplier_po`, `serial_number`, `model`, `qty_delivered_po`) VALUES
(1, 2, '2', '3', '123', 6, 6, 1, 'Pending', '2017-12-16', 2, '123', '123', 0);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order`
--

CREATE TABLE `purchase_order` (
  `id` int(11) NOT NULL,
  `po_no` varchar(11) DEFAULT NULL,
  `date_of_po` varchar(255) DEFAULT NULL,
  `supplier` varchar(45) DEFAULT NULL,
  `totalamt` double DEFAULT NULL,
  `request_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `purchase_order`
--

INSERT INTO `purchase_order` (`id`, `po_no`, `date_of_po`, `supplier`, `totalamt`, `request_id`) VALUES
(1, NULL, NULL, 'okinamlifesty', 12, 11);

-- --------------------------------------------------------

--
-- Table structure for table `request_slip`
--

CREATE TABLE `request_slip` (
  `id` int(11) NOT NULL,
  `rs_no` varchar(200) NOT NULL,
  `requested_by` varchar(255) NOT NULL COMMENT 'users.id',
  `date_needed` varchar(255) DEFAULT NULL,
  `time_needed` varchar(25) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `purpose` text COMMENT 'another word for reason',
  `status` varchar(255) NOT NULL COMMENT 'pending/cancelled/forPO/delivered/in-progrees/completed',
  `type` enum('ItemsNoPO','PO','Service') NOT NULL COMMENT 'The category',
  `ConcernedOffice` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `request_slip`
--

INSERT INTO `request_slip` (`id`, `rs_no`, `requested_by`, `date_needed`, `time_needed`, `created_at`, `updated_at`, `purpose`, `status`, `type`, `ConcernedOffice`) VALUES
(5, 'CE_21', 'SCIS Admin', 'asap', '213', '2017-12-14 07:11:54', '2017-12-14 07:11:54', '213', 'Pending', 'ItemsNoPO', '22'),
(10, 'CE_tangina naman', 'SCIS Admin', 'asap', '213', '2017-12-14 07:15:57', '2017-12-14 07:15:57', '213', 'Pending', 'ItemsNoPO', '22'),
(11, 'PBE_okinamlifesty', 'SCIS Admin', 'asap', '21', '2017-12-14 07:26:32', '2017-12-14 07:26:32', 'okinamlifesty', 'Pending', 'PO', NULL),
(12, 'SRV_wew', 'SCIS Admin', 'asap', '11: PM', '2017-12-14 07:36:06', '2017-12-14 07:36:06', '23', 'Pending', 'Service', 'wew');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `idServices` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `remarks` text,
  `requestID` int(11) NOT NULL,
  `date_completed` date DEFAULT NULL,
  `service_provider` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`idServices`, `description`, `status`, `remarks`, `requestID`, `date_completed`, `service_provider`) VALUES
(1, '23', 'Pending', NULL, 12, NULL, '232');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$t5NRVlyvnUbvggQ6xW25sO/sGxJtW4KfdVYe/Gx6U8s8DID5HgvRS', '2017-08-07 03:10:15', '2017-05-06 08:40:23'),
(2, 'jl', '$2y$10$NWfnEz.GRJgvqSbjqbMiFeqOESUtuXMwKtuiRF9slber5fegNnwGO', '2017-05-07 09:58:34', '2017-05-07 09:58:34'),
(3, 'galo', '$2y$10$nhKC2Zdt3vnO2gcdCihewuUG5yF9Qt7z7PBYXAR9dJddqGqO5TUq2', '2017-07-09 15:13:22', '2017-07-06 01:36:13');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'users.id',
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`id`, `user_id`, `firstname`, `lastname`, `created_at`, `updated_at`) VALUES
(1, 1, 'SCIS', 'Admin', '2017-05-25 02:41:57', '2017-05-06 08:40:23'),
(2, 2, 'JL', 'Black', '2017-05-11 01:34:34', '2017-05-07 09:58:34'),
(3, 3, 'Galo Berlyn', 'Garlejo', '2017-07-06 01:36:13', '2017-07-06 01:36:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `delivered_items`
--
ALTER TABLE `delivered_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rs_item_no` (`rs_item_no`);

--
-- Indexes for table `itemsnotpo`
--
ALTER TABLE `itemsnotpo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_slip_no` (`request_slip_no`);

--
-- Indexes for table `itemspo`
--
ALTER TABLE `itemspo`
  ADD PRIMARY KEY (`iditemspo`),
  ADD KEY `POIDFK_idx` (`poid`),
  ADD KEY `iditemspo` (`iditemspo`,`quantity`,`description`,`remarks`,`Location`,`unitprice`,`amount`,`poid`,`itemspostatus`),
  ADD KEY `iditemspo_2` (`iditemspo`,`quantity`,`description`,`remarks`,`Location`,`unitprice`,`amount`,`poid`,`itemspostatus`);

--
-- Indexes for table `purchase_order`
--
ALTER TABLE `purchase_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `request_slip`
--
ALTER TABLE `request_slip`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rs_no_UNIQUE` (`rs_no`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`idServices`),
  ADD KEY `ServicesFKRequest_idx` (`requestID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `delivered_items`
--
ALTER TABLE `delivered_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `itemsnotpo`
--
ALTER TABLE `itemsnotpo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `itemspo`
--
ALTER TABLE `itemspo`
  MODIFY `iditemspo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `purchase_order`
--
ALTER TABLE `purchase_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `request_slip`
--
ALTER TABLE `request_slip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `idServices` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `itemsnotpo`
--
ALTER TABLE `itemsnotpo`
  ADD CONSTRAINT `request_slipFK` FOREIGN KEY (`request_slip_no`) REFERENCES `request_slip` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `itemspo`
--
ALTER TABLE `itemspo`
  ADD CONSTRAINT `PoFKID` FOREIGN KEY (`poid`) REFERENCES `purchase_order` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `purchase_order`
--
ALTER TABLE `purchase_order`
  ADD CONSTRAINT `fk_reqid_reqslip` FOREIGN KEY (`request_id`) REFERENCES `request_slip` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `ServicesFKRequest` FOREIGN KEY (`requestID`) REFERENCES `request_slip` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_details`
--
ALTER TABLE `user_details`
  ADD CONSTRAINT `user_details_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
