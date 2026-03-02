-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 20, 2026 at 11:02 AM
-- Server version: 8.0.45-0ubuntu0.22.04.1
-- PHP Version: 8.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rposystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `rpos_admin`
--

CREATE TABLE `rpos_admin` (
  `admin_id` varchar(200) NOT NULL,
  `admin_name` varchar(200) NOT NULL,
  `admin_email` varchar(200) NOT NULL,
  `admin_password` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rpos_admin`
--

INSERT INTO `rpos_admin` (`admin_id`, `admin_name`, `admin_email`, `admin_password`) VALUES
('10e0b6dc958adfb5b094d8935a13aeadbe783c25', 'System Admin', 'admin@mail.com', 'e0ec30792753894eb3ed855f5ddccd408a506697');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_customers`
--

CREATE TABLE `rpos_customers` (
  `customer_id` varchar(200) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `customer_phoneno` varchar(200) NOT NULL,
  `customer_email` varchar(200) NOT NULL,
  `customer_password` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rpos_customers`
--

INSERT INTO `rpos_customers` (`customer_id`, `customer_name`, `customer_phoneno`, `customer_email`, `customer_password`, `created_at`) VALUES
('06549ea58afd', 'Ana J. Browne', '4589698780', 'anaj@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 12:39:48.523820'),
('0f6e4296f645', 'AIshimwe', '0899u8y87t6r5y', 'desire@gmail.com', 'ad616acbc075d922cbd257c66b1c2cc15f7ea071', '2026-02-19 07:48:07.720533'),
('1', 'Billy S. Burke', '7540001240', 'billyb9@gmail.com', '92ac0281d4695ec3710f690e029a6320ca7e5244', '2025-11-23 15:30:36.490784'),
('141', 'Irene Murindahabi', '780000000000', 'irene@mail.com', '92ac0281d4695ec3710f690e029a6320ca7e5244', '2026-01-21 10:38:33.710385'),
('142', 'NTwali Joshua', '780000000000', 'ntwali@mail.co', '92ac0281d4695ec3710f690e029a6320ca7e5244', '2025-11-26 17:48:30.419425'),
('143', 'Van Laurent', '7899999999999999', 'van@gmail.com', '92ac0281d4695ec3710f690e029a6320ca7e5244', '2025-11-26 17:52:25.989147'),
('144', 'kigali Rwanda', '780000000', 'iraguhayves@gmail.com', '92ac0281d4695ec3710f690e029a6320ca7e5244', '2026-01-06 09:10:09.708086'),
('24fa99d7bcd9', 'kalisa', '078000000000', 'kalisa@mail.com', '4028a0e356acc947fcd2bfbf00cef11e128d484a', '2026-01-06 08:53:36.522207'),
('27e4a5bc74c2', 'Tammy R. Polley', '4589654780', 'tammy@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 12:37:47.049438'),
('29c759d624f9', 'Trina L. Crowder', '5896321002', 'trina@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 13:16:18.927595'),
('3', 'Beatriz M. Matthews', '1247778460', 'matthews@gmail.com', '92ac0281d4695ec3710f690e029a6320ca7e5244', '2025-11-22 11:12:47.957692'),
('35135b319ce3', 'Christine Moore', '7412569692', 'christine@mail.com', '7186ebfb69adb98029cce10975245bf1e6c44194', '2025-11-22 10:56:47.520294'),
('3859d26cd9a5', 'Louise R. Holloman', '7856321000', 'holloman@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 12:38:12.149280'),
('4', 'Kevin Johnson', '1478546500', 'kevin@gmail.com', '92ac0281d4695ec3710f690e029a6320ca7e5244', '2025-11-22 18:21:19.164767'),
('5', 'Dwayne Scott', '2671249780', 'scottdway@gmail.com', '92ac0281d4695ec3710f690e029a6320ca7e5244', '2025-11-25 16:30:46.512847'),
('6', 'Bruno Denn', '1245554780', 'denbru@gmail.com', '92ac0281d4695ec3710f690e029a6320ca7e5244', '2025-11-22 18:25:41.408128'),
('74aed29b4b66', 'M KALISA', '0785988488', 'kalisa@gmail.com', 'ad616acbc075d922cbd257c66b1c2cc15f7ea071', '2026-02-18 08:56:39.452697'),
('7c8f2100d552', 'Melody E. Hance', '3210145550', 'melody@mail.com', 'a69681bcf334ae130217fea4505fd3c994f5683f', '2022-09-03 13:16:23.996068'),
('8', 'Andrew Stuartt', '2457778450', 'andrew@gmail.com', '92ac0281d4695ec3710f690e029a6320ca7e5244', '2025-11-22 11:12:20.491797'),
('94803228dab8', 'test', '088888888888', 'tet@test.com', 'ad616acbc075d922cbd257c66b1c2cc15f7ea071', '2026-02-18 08:30:35.807570'),
('9c7fcc067bda', 'Delbert G. Campbell', '7850001256', 'delbert@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 12:38:56.944364'),
('9ec5327a5809', 'muli', '078850065', 'muli@gmail', 'ad616acbc075d922cbd257c66b1c2cc15f7ea071', '2026-01-06 09:36:11.590566'),
('9f6378b79283', 'William C. Gallup', '7145665870', 'william@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 12:39:26.507932'),
('d7c2db8f6cbf', 'Victor A. Pierson', '1458887896', 'victor@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 12:37:21.568155'),
('e636fa332bbe', 'kalisa', '0788888888888', 'umuisa@gmail.com', 'ad616acbc075d922cbd257c66b1c2cc15f7ea071', '2026-02-18 08:30:08.806511'),
('e711dcc579d9', 'Julie R. Martin', '3245557896', 'julie@mail.com', '55c3b5386c486feb662a0785f340938f518d547f', '2022-09-03 12:38:33.397498'),
('fe6bb69bdd29', 'Brian S. Boucher', '1020302055', 'brians@mail.com', 'a69681bcf334ae130217fea4505fd3c994f5683f', '2022-09-03 13:16:29.591980');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_orders`
--

CREATE TABLE `rpos_orders` (
  `order_id` varchar(200) NOT NULL,
  `order_code` varchar(200) NOT NULL,
  `customer_id` varchar(200) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `prod_id` varchar(200) NOT NULL,
  `prod_name` varchar(200) NOT NULL,
  `prod_price` varchar(200) NOT NULL,
  `prod_qty` varchar(200) NOT NULL,
  `order_status` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  `Done_by` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rpos_orders`
--

INSERT INTO `rpos_orders` (`order_id`, `order_code`, `customer_id`, `customer_name`, `prod_id`, `prod_name`, `prod_price`, `prod_qty`, `order_status`, `created_at`, `Done_by`) VALUES
('2QJN50HCDK8VX96SBRZI', 'PCHG-8320', '0f6e4296f645', 'AIshimwe', 'w7x8', 'BEEF COURSE', '10000', '3', 'Paid', '2026-02-19 07:51:38.000000', 'admin@mail.com'),
('DBFT-0172-696f6460c3665', 'BWHD-1326', '3', 'Beatriz M. Matthews', 'a1b2', 'BR.CHEVRE', '3000', '1', 'Cancelled', '2026-02-19 13:08:48.589671', 'admin@mail.com'),
('DBFT-0172-696f6460c4edb', 'BWHD-1326', '3', 'Beatriz M. Matthews', 'a1b3', 'BURGERS', '7000', '1', 'Cancelled', '2026-02-19 13:08:48.589671', 'admin@mail.com'),
('DXEI-9843-6970be6c37584', 'LING-7816', '35135b319ce3', 'Christine Moore', 'a1b2', 'BR.CHEVRE', '3000', '3', 'Pending', '2026-01-21 11:54:20.227966', 'admin@mail.com'),
('EUCI-0349-696f6aedb4484', 'CIAM-4073', 'fe6bb69bdd29', 'Brian S. Boucher', 'a1b2', 'BR.CHEVRE', '3000', '3', 'Pending', '2026-01-20 11:45:49.738738', 'admin@mail.com'),
('EUZG-9635-6995795c5ac39', 'QZMC-1746', '6', 'Bruno Denn', '06ba89c8c6', 'GOLDEN SHERRYs', '15000', '2', 'Paid', '2026-02-18 08:36:37.132516', 'admin@mail.com'),
('EUZG-9635-6995795c5bf18', 'QZMC-1746', '6', 'Bruno Denn', '3bc27065fd', 'konyage', '30000', '1', 'Paid', '2026-02-18 08:36:37.132516', 'admin@mail.com'),
('EUZG-9635-6995795c5d587', 'QZMC-1746', '6', 'Bruno Denn', '805fdbe631', 'Sminoff', '50000', '1', 'Paid', '2026-02-18 08:36:37.132516', 'admin@mail.com'),
('HQIF-3015-6970a8755902e', 'YFKV-9372', '35135b319ce3', 'Christine Moore', 'a1b2', 'BR.CHEVRE', '3000', '3', 'Pending', '2026-01-21 10:20:37.364892', 'admin@mail.com'),
('JMBN-4316-6996c0a69d5d2', 'PCHG-8320', '0f6e4296f645', 'AIshimwe', '06ba89c8c6', 'GOLDEN SHERRYs', '15000', '1', 'Paid', '2026-02-19 07:50:58.899777', 'admin@mail.com'),
('JMBN-4316-6996c0a6a05b6', 'PCHG-8320', '0f6e4296f645', 'AIshimwe', 'a3b4', 'SPECIAL OMOLET', '10000', '1', 'Paid', '2026-02-19 07:50:58.899777', 'admin@mail.com'),
('JMBN-4316-6996c0a6a3dd8', 'PCHG-8320', '0f6e4296f645', 'AIshimwe', 'a5b6', 'ROAST POTATOES', '4000', '1', 'Paid', '2026-02-19 07:50:58.899777', 'admin@mail.com'),
('JMBN-4316-6996c0a6a759a', 'PCHG-8320', '0f6e4296f645', 'AIshimwe', 'a6b7', 'AFRICAN TEA', '4000', '1', 'Paid', '2026-02-19 07:50:58.899777', 'admin@mail.com'),
('MI3LE5TS6QC9KUG712FH', 'QZMC-1746', '6', 'Bruno Denn', 'w7x8', 'BEEF COURSE', '10000', '1', 'Paid', '2026-02-18 08:37:49.000000', 'admin@mail.com'),
('MPWA-4203-6977666b2ed91', 'HJMQ-3702', '3', 'Beatriz M. Matthews', '06ba89c8c6', 'GOLDEN SHERRYs', '15000', '1', 'Pending', '2026-01-26 13:04:43.192243', 'admin@mail.com'),
('MWCQ-1648-6970aca9af351', 'DHZT-3058', '141', 'Irene Murindahabi', 'a1b2', 'BR.CHEVRE', '3000', '1', 'Pending', '2026-01-21 10:38:33.717879', 'admin@mail.com'),
('ONZM-2638-69807b5f7ef49', 'TDLR-8452', '6', 'Bruno Denn', '06ba89c8c6', 'GOLDEN SHERRYs', '15000', '1', 'Pending', '2026-02-02 10:24:31.520140', 'admin@mail.com'),
('SIWG-0591-6970e72b3f12f', 'HMXY-6103', '6', 'Bruno Denn', '06ba89c8c6', 'GOLDEN SHERRY', '15000', '1', 'Pending', '2026-01-21 14:48:11.258771', 'admin@mail.com'),
('XRKU5QVBP73F1W9ESDJN', 'SYAP-6238', '74aed29b4b66', 'M KALISA', '3bc27065fd', 'konyage', '30000', '1', 'Paid', '2026-02-18 09:06:01.000000', 'admin@mail.com'),
('ZIOL-5879-69957fce784b2', 'SYAP-6238', '74aed29b4b66', 'M KALISA', 'c9d0', 'POISSONS(10,000)', '10000', '1', 'Paid', '2026-02-18 09:05:07.227414', 'admin@mail.com'),
('ZIOL-5879-69957fce7972c', 'SYAP-6238', '74aed29b4b66', 'M KALISA', 'y3z4', 'PLAIN RICE', '3000', '1', 'Paid', '2026-02-18 09:05:07.227414', 'admin@mail.com'),
('ZIOL-5879-69957fce7a7e3', 'SYAP-6238', '74aed29b4b66', 'M KALISA', 'y9z0', 'FISH COURSE', '7000', '1', 'Paid', '2026-02-18 09:05:07.227414', 'admin@mail.com');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_pass_resets`
--

CREATE TABLE `rpos_pass_resets` (
  `reset_id` int NOT NULL,
  `reset_code` varchar(200) NOT NULL,
  `reset_token` varchar(200) NOT NULL,
  `reset_email` varchar(200) NOT NULL,
  `reset_status` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rpos_pass_resets`
--

INSERT INTO `rpos_pass_resets` (`reset_id`, `reset_code`, `reset_token`, `reset_email`, `reset_status`, `created_at`) VALUES
(1, '63KU9QDGSO', '4ac4cee0a94e82a2aedc311617aa437e218bdf68', 'sysadmin@icofee.org', 'Pending', '2020-08-17 15:20:14.318643');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_payments`
--

CREATE TABLE `rpos_payments` (
  `pay_id` varchar(200) NOT NULL,
  `pay_code` varchar(200) NOT NULL,
  `order_code` varchar(200) NOT NULL,
  `customer_id` varchar(200) NOT NULL,
  `pay_amt` varchar(200) NOT NULL,
  `pay_method` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  `Done_by` varchar(100) NOT NULL,
  `Tip` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rpos_payments`
--

INSERT INTO `rpos_payments` (`pay_id`, `pay_code`, `order_code`, `customer_id`, `pay_amt`, `pay_method`, `created_at`, `Done_by`, `Tip`) VALUES
('0bf592', '9UMWLG4BF8', 'EJKA-4501', '35135b319ce3', '8', 'Cash', '2022-09-04 16:31:54.525284', '', ''),
('123547', 'EQDMNSU7VA', 'OWCE-0379', '29c759d624f9', '150000', 'Cash', '2025-11-22 09:11:21.418051', '', ''),
('239991', 'UFHOSGP71Y', 'QZMC-1746', '6', '120000', 'Cash', '2026-02-18 08:37:49.389967', 'admin@mail.com', '0'),
('2fb54a', 'QMBJ2IS5CN', 'IDCW-0794', '143', '16000', 'Pay Later', '2025-11-26 17:52:38.507942', '', ''),
('312086', 'FQPOAXCRZY', 'XOWP-6247', '142', '35000', 'Cash', '2026-01-06 12:45:37.158024', 'admin@mail.com', '0'),
('42ed3e', 'FK9GNW38HM', 'DJZB-8673', '5', '1011', 'Cash', '2025-12-30 09:55:35.057524', 'admin@mail.com', '1000'),
('4423d7', 'QWERT0YUZ1', 'JFMB-0731', '35135b319ce3', '11', 'Cash', '2022-09-04 16:37:03.655834', '', ''),
('442865', '146XLFSC9V', 'INHG-0875', '9c7fcc067bda', '10', 'Paypal', '2022-09-04 16:35:22.470600', '', ''),
('488b24', 'OH73XT6RLN', 'JOQF-2843', '06549ea58afd', '23000', 'Cash', '2025-11-22 10:22:38.479682', '', ''),
('5d74e9', 'Y5ACGOBT68', 'XSRJ-2097', '9ec5327a5809', '2008', 'Cash', '2026-01-06 09:52:30.319965', 'admin@mail.com', '0'),
('65891b', 'MF2TVJA1PY', 'ZPXD-6951', 'e711dcc579d9', '16', 'Cash', '2022-09-03 13:12:46.959558', '', ''),
('66c199', '4UBEV158WZ', 'AWDO-9146', '142', '23000', 'Pay Later', '2025-11-26 17:48:46.783775', '', ''),
('75ae21', '1QIKVO69SA', 'IUSP-9453', 'fe6bb69bdd29', '10', 'Cash', '2022-09-03 11:50:40.496625', '', ''),
('7bf5f7', 'XY89PRSQ1C', 'HTRF-9716', '3', '23000', 'Cash', '2025-11-22 11:13:00.476990', '', ''),
('7e1989', 'KLTF3YZHJP', 'QOEH-8613', '29c759d624f9', '9', 'Cash', '2022-09-03 12:02:32.926529', '', ''),
('948e1b', 'G3L87O5DEC', 'NMAI-5628', 'fe6bb69bdd29', '8000', 'Cash', '2025-11-26 20:54:56.571683', '', ''),
('968488', '5E31DQ2NCG', 'COXP-6018', '7c8f2100d552', '22', 'Cash', '2022-09-03 12:17:44.639979', '', ''),
('977ace', 'ONTJ3M796C', 'XYZE-5379', 'fe6bb69bdd29', '16000', 'Cash', '2025-12-18 08:05:59.474749', 'admin@mail.com', ''),
('984539', 'LSBNK1WRFU', 'FNAB-9142', '35135b319ce3', '18', 'Paypal', '2022-09-04 16:32:14.852482', '', ''),
('9ea0bf', '23QDFCUNWV', 'MHQD-3927', '8', '23000', 'Pay Later', '2025-11-24 17:11:15.952352', '', ''),
('9fcee7', 'AZSUNOKEI7', 'OTEV-8532', '3859d26cd9a5', '15', 'Cash', '2022-09-03 13:13:38.855058', '', ''),
('a47ae3', 'ROZC3DS8FM', 'TLHJ-6712', '052498065442', '300', 'Cash', '2025-11-22 09:21:59.302530', '', ''),
('a63daa', '25HMCOBG7T', 'LAIB-8374', '9ec5327a5809', '8500', 'Mobile Money', '2026-01-06 09:44:44.127681', 'admin@mail.com', '2000'),
('a85d1e', 'ZWE5G23URM', 'WSDH-0625', '4', '8000', 'Cash', '2025-12-01 11:13:04.658455', 'cashier@mail.com', ''),
('b10406', '6W2F3GHZQ7', 'PCHG-8320', '0f6e4296f645', '63000', 'Cash', '2026-02-19 07:51:38.458762', 'admin@mail.com', '0'),
('bc0a16', 'AL76IXPS3N', 'UXHN-6341', 'e636fa332bbe', '20500', 'Mobile Money', '2026-01-06 13:14:26.909144', 'admin@mail.com', '2000'),
('c24090', '65X9LEA2BY', 'MFBG-3782', '8', '8000', 'Pay Later', '2025-11-22 18:43:24.962238', '', ''),
('c2eff5', 'E6F5UVJNPD', 'KNWS-4935', '1', '8000', 'Cash', '2025-11-25 16:34:19.444842', '', ''),
('c76315', '9HB3ZJIDW8', 'TDSM-5872', '35135b319ce3', '23942', 'Cash', '2026-01-05 11:20:59.795182', 'admin@mail.com', '900'),
('c81d2e', 'WERGFCXZSR', 'AEHM-0653', '06549ea58afd', '8', 'Cash', '2022-09-03 13:26:00.331494', '', ''),
('c927e6', 'DEIUVTR682', 'MPZR-1035', '4', '8000', 'Cash', '2025-11-22 18:21:59.210885', '', ''),
('c9df4b', 'DPVNAZEUTW', 'SYAP-6238', '74aed29b4b66', '50000', 'Cash', '2026-02-18 09:06:01.602760', 'admin@mail.com', '0'),
('ce9c98', 'J9FU3NOLWC', 'SHPN-4135', '052498065442', '16000', 'Cash', '2025-11-22 08:34:16.774054', '', ''),
('d925da', 'CHWG7ABQVZ', 'IORZ-3298', '27e4a5bc74c2', '24000', 'Cash', '2025-11-22 10:27:46.586286', '', ''),
('e35af6', 'WKJBS814UI', 'BUZE-7036', '6', '25000', 'Cash', '2026-01-06 09:16:28.964535', 'admin@mail.com', '2000'),
('e46e29', 'QMCGSNER3T', 'ONSY-2465', '57b7541814ed', '12', 'Cash', '2022-09-03 08:35:50.172062', '', ''),
('e61505', 'K7B1M3NIHE', 'JGAK-0196', '5', '23000', 'Cash', '2025-11-25 16:31:05.232701', '', ''),
('e678eb', 'LCDBP9RFI1', 'IDCW-0794', '143', '16000', 'Cash', '2025-12-10 12:27:00.185988', 'admin@mail.com', ''),
('f15842', 'VJPHLXSM8R', 'GKEP-0167', 'fe6bb69bdd29', '8000', 'Cash', '2025-12-01 10:54:09.552858', 'admin@mail.com', ''),
('f3b0a6', 'UBDS8TACXE', 'OYDP-8432', '35135b319ce3', '15000', 'Cash', '2025-12-30 09:52:46.097667', 'admin@mail.com', '500'),
('fa54a3', 'KOC5EHTRFN', 'OBXS-3982', '6', '703', 'Cash', '2025-12-30 10:28:23.336581', 'cashier@mail.com', '700');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_products`
--

CREATE TABLE `rpos_products` (
  `prod_id` varchar(200) NOT NULL,
  `prod_code` varchar(200) NOT NULL,
  `prod_name` varchar(200) NOT NULL,
  `prod_img` varchar(200) NOT NULL,
  `prod_desc` longtext NOT NULL,
  `prod_price` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rpos_products`
--

INSERT INTO `rpos_products` (`prod_id`, `prod_code`, `prod_name`, `prod_img`, `prod_desc`, `prod_price`, `created_at`) VALUES
('06ba89c8c6', 'P000002', 'GOLDEN SHERRYs', '', 'test', '15000', '2026-01-23 10:05:15.357590'),
('3bc27065fd', 'P000003', 'konyage', '', 'test', '30000', '2026-01-21 14:17:55.407351'),
('805fdbe631', 'LUMK-9538', 'Sminoff', '', 'test', '50000', '2026-02-18 08:32:34.134957'),
('a1b2', 'PROD-1001', 'BR.CHEVRE', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('a1b3', 'PROD-1066', 'BURGERS', 'default.jpg', 'test', '7000', '2026-01-16 07:30:46.910463'),
('a2b4', 'PROD-1092', 'BUFFET COMP1', 'default.jpg', 'test', '16000', '2026-01-16 07:30:46.910463'),
('a3b4', 'PROD-1027', 'SPECIAL OMOLET', 'default.jpg', 'test', '10000', '2026-01-16 07:30:46.910463'),
('a5b6', 'PROD-1053', 'ROAST POTATOES', 'default.jpg', 'test', '4000', '2026-01-16 07:30:46.910463'),
('a6b7', 'PROD-1079', 'AFRICAN TEA', 'default.jpg', 'test', '4000', '2026-01-16 07:30:46.910463'),
('a7b8', 'PROD-1014', 'POISSONS (28,000)', 'default.jpg', 'test', '28000', '2026-01-16 07:30:46.910463'),
('a9b0', 'PROD-1040', 'VEGETABLE SOUP', 'default.jpg', 'test', '8000', '2026-01-16 07:30:46.910463'),
('c1d2', 'PROD-1041', 'BREAKFAST', 'default.jpg', 'test', '8000', '2026-01-16 07:30:46.910463'),
('c2d4', 'PROD-1067', 'PASTA', 'default.jpg', 'test', '8000', '2026-01-16 07:30:46.910463'),
('c3d4', 'PROD-1002', 'BR.BOEUF No', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('c3d5', 'PROD-1093', 'BUFFET COMPLETE2', 'default.jpg', 'test', '15000', '2026-01-16 07:30:46.910463'),
('c5d6', 'PROD-1028', 'SPECIAL OMOLE+ MEAT', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('c7d8', 'PROD-1054', 'KAWUNGA', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('c8d9', 'PROD-1080', 'FRESH JUICE2', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('c9d0', 'PROD-1015', 'POISSONS(10,000)', 'default.jpg', 'test', '10000', '2026-01-16 07:30:46.910463'),
('e0f1', 'PROD-1081', 'tea', 'default.jpg', 'test', '4000', '2026-01-16 07:30:46.910463'),
('e1f2', 'PROD-1016', 'POISSONS (18,000)', 'default.jpg', 'test', '18000', '2026-01-16 07:30:46.910463'),
('e3f4', 'PROD-1042', 'TAKE AWAY', 'default.jpg', 'test', '1000', '2026-01-16 07:30:46.910463'),
('e4f5', 'PROD-1068', '2 PIZZA', 'default.jpg', 'test', '9000', '2026-01-16 07:30:46.910463'),
('e5f6', 'PROD-1003', 'BOILED BOEUF', 'default.jpg', 'test', '10000', '2026-01-16 07:30:46.910463'),
('e5f7', 'PROD-1094', 'BUFFET COMPLETE3', 'default.jpg', 'test', '14000', '2026-01-16 07:30:46.910463'),
('e7f8', 'PROD-1029', 'CHICKEN WINGS', 'default.jpg', 'test', '10000', '2026-01-16 07:30:46.910463'),
('e9f0', 'PROD-1055', 'PILAU RICE', 'default.jpg', 'test', '8000', '2026-01-16 07:30:46.910463'),
('g1h2', 'PROD-1056', 'ROASTED BANANA', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('g2h3', 'PROD-1082', 'casava ugali', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('g3h4', 'PROD-1017', 'POISSONS(30,000)', 'default.jpg', 'test', '30000', '2026-01-16 07:30:46.910463'),
('g5h6', 'PROD-1043', 'FISH FINGER', 'default.jpg', 'test', '8000', '2026-01-16 07:30:46.910463'),
('g6h7', 'PROD-1069', 'PIZZA 4 SEASON', 'default.jpg', 'test', '10000', '2026-01-16 07:30:46.910463'),
('g7h8', 'PROD-1004', 'BR.BOEUF', 'default.jpg', 'test', '4000', '2026-01-16 07:30:46.910463'),
('g7h9', 'PROD-1095', 'BUFFET COMPLET', 'default.jpg', 'test', '12000', '2026-01-16 07:30:46.910463'),
('g9h0', 'PROD-1030', 'AFRICAN TEA', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('i1j2', 'PROD-1031', 'AFRICAN COFFEE', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('i3j4', 'PROD-1057', 'UMUZUZU', 'default.jpg', 'test', '5000', '2026-01-16 07:30:46.910463'),
('i4j5', 'PROD-1083', 'chicken soup', 'default.jpg', 'test', '4000', '2026-01-16 07:30:46.910463'),
('i5j6', 'PROD-1018', 'POISSONS', 'default.jpg', 'test', '22000', '2026-01-16 07:30:46.910463'),
('i7j8', 'PROD-1044', 'CHIEF SALADE', 'default.jpg', 'test', '5000', '2026-01-16 07:30:46.910463'),
('i8j9', 'PROD-1070', 'SPECIALS CRF', 'default.jpg', 'test', '1500', '2026-01-16 07:30:46.910463'),
('i9j0', 'PROD-1005', 'BR.MMDJIMA', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('i9j1', 'PROD-1096', 'BUFFET COMPLET', 'default.jpg', 'test', '10000', '2026-01-16 07:30:46.910463'),
('k0l1', 'PROD-1071', 'FRUITS SALADE', 'default.jpg', 'test', '5000', '2026-01-16 07:30:46.910463'),
('k1l2', 'PROD-1006', 'BR POULET', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('k3l4', 'PROD-1032', 'GINGER TEA', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('k5l6', 'PROD-1058', 'RATATOUILLE', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('k6l7', 'PROD-1084', 'SAUCE PROVINCAIL', 'default.jpg', 'test', '1500', '2026-01-16 07:30:46.910463'),
('k7l8', 'PROD-1019', 'POISSONS', 'default.jpg', 'test', '25000', '2026-01-16 07:30:46.910463'),
('k9l0', 'PROD-1045', 'AVOCAT &VINEGAR', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('m1n2', 'PROD-1046', 'COLESLAW SALAD', 'default.jpg', 'test', '3500', '2026-01-16 07:30:46.910463'),
('m2n3', 'PROD-1072', 'PANCAKE', 'default.jpg', 'test', '2500', '2026-01-16 07:30:46.910463'),
('m3n4', 'PROD-1007', 'BR ZINGALO', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('m5n6', 'PROD-1033', 'POMME SAUTE', 'default.jpg', 'test', '4000', '2026-01-16 07:30:46.910463'),
('m7n8', 'PROD-1059', 'BEANS', 'default.jpg', 'test', '5000', '2026-01-16 07:30:46.910463'),
('m8n9', 'PROD-1085', 'SNACKS', 'default.jpg', 'test', '2000', '2026-01-16 07:30:46.910463'),
('m9n0', 'PROD-1020', 'POISSONS', 'default.jpg', 'test', '35000', '2026-01-16 07:30:46.910463'),
('o0p1', 'PROD-1086', 'SALADE SIMPLE', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('o1p2', 'PROD-1021', 'FILLET DE CAPITAINE', 'default.jpg', 'test', '11000', '2026-01-16 07:30:46.910463'),
('o3p4', 'PROD-1047', 'GREEN SALADE', 'default.jpg', 'test', '4000', '2026-01-16 07:30:46.910463'),
('o4p5', 'PROD-1073', 'PANCAKE + HONEY', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('o5p6', 'PROD-1008', 'BR.CAPITAINE', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('o7p8', 'PROD-1034', 'AFRICA BLACK TEA', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('o9p0', 'PROD-1060', 'GREEN BEANS', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('q1r2', 'PROD-1061', 'PEAS', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('q2r3', 'PROD-1087', 'CREPE AUX SUCRE', 'default.jpg', 'test', '2500', '2026-01-16 07:30:46.910463'),
('q3r4', 'PROD-1022', 'STEAKS', 'default.jpg', 'test', '13000', '2026-01-16 07:30:46.910463'),
('q5r6', 'PROD-1048', 'SALADE SAMPLE', 'default.jpg', 'test', '4000', '2026-01-16 07:30:46.910463'),
('q6r7', 'PROD-1074', 'HOT WATER', 'default.jpg', 'test', '1500', '2026-01-16 07:30:46.910463'),
('q7r8', 'PROD-1009', 'BR DE CHAIR', 'default.jpg', 'test', '16000', '2026-01-16 07:30:46.910463'),
('q9r0', 'PROD-1035', 'HOT CHOCOLATE', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('s1t2', 'PROD-1036', 'BLACK COFFEE', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('s3t4', 'PROD-1062', 'ISOGI', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('s4t5', 'PROD-1088', 'POULET CASSEROLE', 'default.jpg', 'test', '30000', '2026-01-16 07:30:46.910463'),
('s5t6', 'PROD-1023', 'FRITES', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('s7t8', 'PROD-1049', 'SALADE SAMPLE', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('s8t9', 'PROD-1075', 'VEGETABLE SOUP', 'default.jpg', 'test', '4000', '2026-01-16 07:30:46.910463'),
('s9t0', 'PROD-1010', 'PORTION DE Poulet', 'default.jpg', 'test', '12000', '2026-01-16 07:30:46.910463'),
('u0v1', 'PROD-1076', 'MUSHROOM SOUP', 'default.jpg', 'test', '4000', '2026-01-16 07:30:46.910463'),
('u1v2', 'PROD-1011', 'BOILED CHICKEN', 'default.jpg', 'test', '30000', '2026-01-16 07:30:46.910463'),
('u3v4', 'PROD-1037', 'NESCAFE', 'default.jpg', 'test', '10000', '2026-01-16 07:30:46.910463'),
('u5v6', 'PROD-1063', 'KACUMBARI', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('u6v7', 'PROD-1089', 'IHENE YOSE', 'default.jpg', 'test', '150000', '2026-01-16 07:30:46.910463'),
('u7v8', 'PROD-1024', 'POMME DE TERRE', 'default.jpg', 'test', '2000', '2026-01-16 07:30:46.910463'),
('u9v0', 'PROD-1050', 'CROQUE MONSIEUR', 'default.jpg', 'test', '5000', '2026-01-16 07:30:46.910463'),
('w1x2', 'PROD-1051', 'CROQUE MADAME', 'default.jpg', 'test', '5000', '2026-01-16 07:30:46.910463'),
('w2x3', 'PROD-1077', 'SPAGHETTI BOLSE', 'default.jpg', 'test', '8000', '2026-01-16 07:30:46.910463'),
('w3x4', 'PROD-1012', 'POULET DE CAMP', 'default.jpg', 'test', '16000', '2026-01-16 07:30:46.910463'),
('w5x6', 'PROD-1038', 'PEPAS', 'default.jpg', 'test', '6000', '2026-01-16 07:30:46.910463'),
('w7x8', 'PROD-1064', 'BEEF COURSE', 'default.jpg', 'test', '10000', '2026-01-16 07:30:46.910463'),
('w8x9', 'PROD-1090', 'BREADS', 'default.jpg', 'test', '2000', '2026-01-16 07:30:46.910463'),
('w9x0', 'PROD-1025', 'AGATOGO', 'default.jpg', 'test', '8000', '2026-01-16 07:30:46.910463'),
('y0z1', 'PROD-1091', 'VRGETABLE OMLETE', 'default.jpg', 'test', '4000', '2026-01-16 07:30:46.910463'),
('y1z2', 'PROD-1026', 'IGITOKI Isahani', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('y3z4', 'PROD-1052', 'PLAIN RICE', 'default.jpg', 'test', '3000', '2026-01-16 07:30:46.910463'),
('y4z5', 'PROD-1078', 'EMBALAGE', 'default.jpg', 'test', '1000', '2026-01-16 07:30:46.910463'),
('y5z6', 'PROD-1013', 'POISSONS (15,000)', 'default.jpg', 'test', '15000', '2026-01-16 07:30:46.910463'),
('y7z8', 'PROD-1039', 'REPAS+viande', 'default.jpg', 'test', '10000', '2026-01-16 07:30:46.910463'),
('y9z0', 'PROD-1065', 'FISH COURSE', 'default.jpg', 'test', '7000', '2026-01-16 07:30:46.910463');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_staff`
--

CREATE TABLE `rpos_staff` (
  `staff_id` int NOT NULL,
  `staff_name` varchar(200) NOT NULL,
  `staff_number` varchar(200) NOT NULL,
  `staff_email` varchar(200) NOT NULL,
  `staff_password` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rpos_staff`
--

INSERT INTO `rpos_staff` (`staff_id`, `staff_name`, `staff_number`, `staff_email`, `staff_password`, `created_at`) VALUES
(2, 'Cashier Trevor', 'QEUY-9042', 'cashier@mail.com', '903b21879b4a60fc9103c3334e4f6f62cf6c3a2d', '2022-09-04 16:11:30.581882');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_versement`
--

CREATE TABLE `rpos_versement` (
  `id` int NOT NULL,
  `amt` varchar(100) NOT NULL,
  `who` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rpos_versement`
--

INSERT INTO `rpos_versement` (`id`, `amt`, `who`, `created_at`) VALUES
(1, '200000', 'Boss', '2026-01-06 10:11:07'),
(2, '20000', 'Boss', '2026-01-06 10:16:53'),
(3, '500000', 'boss', '2026-01-06 11:56:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rpos_admin`
--
ALTER TABLE `rpos_admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `rpos_customers`
--
ALTER TABLE `rpos_customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `rpos_orders`
--
ALTER TABLE `rpos_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `CustomerOrder` (`customer_id`),
  ADD KEY `ProductOrder` (`prod_id`);

--
-- Indexes for table `rpos_pass_resets`
--
ALTER TABLE `rpos_pass_resets`
  ADD PRIMARY KEY (`reset_id`);

--
-- Indexes for table `rpos_payments`
--
ALTER TABLE `rpos_payments`
  ADD PRIMARY KEY (`pay_id`),
  ADD KEY `order` (`order_code`);

--
-- Indexes for table `rpos_products`
--
ALTER TABLE `rpos_products`
  ADD PRIMARY KEY (`prod_id`);

--
-- Indexes for table `rpos_staff`
--
ALTER TABLE `rpos_staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `rpos_versement`
--
ALTER TABLE `rpos_versement`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rpos_pass_resets`
--
ALTER TABLE `rpos_pass_resets`
  MODIFY `reset_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rpos_staff`
--
ALTER TABLE `rpos_staff`
  MODIFY `staff_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rpos_versement`
--
ALTER TABLE `rpos_versement`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rpos_orders`
--
ALTER TABLE `rpos_orders`
  ADD CONSTRAINT `CustomerOrder` FOREIGN KEY (`customer_id`) REFERENCES `rpos_customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ProductOrder` FOREIGN KEY (`prod_id`) REFERENCES `rpos_products` (`prod_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
