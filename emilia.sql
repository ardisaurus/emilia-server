-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 15, 2018 at 03:00 PM
-- Server version: 10.1.32-MariaDB
-- PHP Version: 5.6.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `emilia`
--

-- --------------------------------------------------------

--
-- Table structure for table `device`
--

CREATE TABLE `device` (
  `dvc_id` varchar(60) NOT NULL,
  `dvc_name` varchar(100) NOT NULL,
  `dvc_password` text NOT NULL,
  `dvc_password_sc` text NOT NULL,
  `dvc_status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `device`
--

INSERT INTO `device` (`dvc_id`, `dvc_name`, `dvc_password`, `dvc_password_sc`, `dvc_status`) VALUES
('bk803', '', 'abeac07d3c28c1bef9e730002c753ed4', '0192023a7bbd73250516f069df18b500', 1),
('go956', '', 'abeac07d3c28c1bef9e730002c753ed4', '', 0),
('ue025', '', 'abeac07d3c28c1bef9e730002c753ed4', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `hst_id` int(11) NOT NULL,
  `hst_date` date NOT NULL,
  `hst_time` time NOT NULL,
  `hst_dvc_id` varchar(60) NOT NULL,
  `hst_email` varchar(60) NOT NULL,
  `hst_status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`hst_id`, `hst_date`, `hst_time`, `hst_dvc_id`, `hst_email`, `hst_status`) VALUES
(1, '2018-07-22', '15:49:20', 'bk803', 'onodera@nanako.com', 1),
(2, '2018-07-22', '15:49:24', 'bk803', 'onodera@nanako.com', 0),
(3, '2018-07-22', '16:06:55', 'go956', 'onodera@haru.com', 1),
(4, '2018-07-22', '16:06:58', 'go956', 'onodera@haru.com', 0),
(5, '2018-07-23', '19:57:20', 'bk803', 'onodera@haru.com', 1),
(6, '2018-07-23', '19:57:50', 'bk803', 'onodera@haru.com', 0),
(7, '2018-07-23', '19:58:27', 'bk803', 'onodera@haru.com', 1),
(8, '2018-08-02', '16:54:11', 'bk803', 'onodera@haru.com', 0),
(9, '2018-08-14', '08:07:11', 'bk803', 'onodera@haru.com', 1),
(10, '2018-08-14', '08:07:36', 'bk803', 'onodera@haru.com', 0),
(11, '2018-08-14', '08:10:00', 'bk803', 'onodera@haru.com', 1),
(12, '2018-08-14', '08:10:23', 'bk803', 'onodera@haru.com', 0),
(13, '2018-08-14', '08:10:50', 'bk803', 'onodera@haru.com', 1),
(14, '2018-08-14', '20:18:45', 'bk803', 'onodera@haru.com', 0),
(15, '2018-08-14', '20:20:19', 'bk803', 'onodera@haru.com', 1),
(16, '2018-08-14', '20:22:10', 'bk803', 'onodera@haru.com', 0),
(17, '2018-08-14', '20:22:31', 'bk803', 'onodera@haru.com', 1),
(18, '2018-08-14', '20:22:46', 'bk803', 'onodera@haru.com', 0),
(19, '2018-08-14', '20:36:46', 'bk803', 'onodera@haru.com', 1),
(20, '2018-08-14', '20:36:50', 'bk803', 'onodera@haru.com', 0),
(21, '2018-08-14', '20:37:06', 'bk803', 'onodera@haru.com', 1),
(22, '2018-08-14', '20:41:32', 'bk803', 'onodera@haru.com', 0),
(23, '2018-08-14', '20:41:52', 'bk803', 'onodera@haru.com', 1),
(24, '2018-08-14', '20:42:08', 'bk803', 'onodera@haru.com', 0),
(25, '2018-08-14', '21:05:08', 'bk803', 'onodera@haru.com', 1),
(26, '2018-08-14', '21:05:19', 'bk803', 'onodera@haru.com', 0),
(27, '2018-08-14', '21:54:03', 'bk803', 'onodera@haru.com', 1),
(28, '2018-08-14', '22:00:20', 'bk803', 'onodera@haru.com', 0),
(29, '2018-08-14', '22:16:13', 'bk803', 'onodera@haru.com', 1),
(30, '2018-08-14', '22:16:13', 'bk803', 'onodera@haru.com', 1),
(31, '2018-08-14', '22:16:19', 'bk803', 'onodera@haru.com', 0),
(32, '2018-08-14', '22:43:59', 'bk803', 'onodera@haru.com', 1),
(33, '2018-08-14', '22:44:02', 'bk803', 'onodera@haru.com', 0),
(34, '2018-08-14', '22:44:02', 'bk803', 'onodera@haru.com', 0),
(35, '2018-08-15', '08:59:39', 'bk803', 'onodera@haru.com', 1),
(40, '2018-08-15', '19:58:29', 'ue025', 'onodera@haru.com', 1),
(41, '2018-08-15', '19:58:35', 'ue025', 'onodera@haru.com', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ownership`
--

CREATE TABLE `ownership` (
  `own_id` int(11) NOT NULL,
  `own_email` varchar(100) NOT NULL,
  `own_dvc_id` varchar(60) NOT NULL,
  `own_level` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ownership`
--

INSERT INTO `ownership` (`own_id`, `own_email`, `own_dvc_id`, `own_level`) VALUES
(2, 'onodera@haru.com', 'bk803', 0),
(4, 'onodera@haru.com', 'go956', 0),
(5, 'onodera@nanako.com', 'bk803', 1),
(12, 'onodera@haru.com', 'ue025', 0);

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `session_id` varchar(10) NOT NULL,
  `public_key` varchar(60) NOT NULL,
  `private_key` varchar(60) NOT NULL,
  `modulo` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `token_email`
--

CREATE TABLE `token_email` (
  `email` varchar(100) NOT NULL,
  `token` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `email` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `dob` date NOT NULL,
  `level` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`email`, `name`, `password`, `dob`, `level`, `active`) VALUES
('onodera@haru.com', 'Onoderan Haru', '0192023a7bbd73250516f069df18b500', '1996-11-19', 0, 1),
('onodera@kosaki.com', 'Onoderan Kosaki', '0192023a7bbd73250516f069df18b500', '1996-11-19', 1, 1),
('onodera@nanako.com', 'Onoderan Haru', '0192023a7bbd73250516f069df18b500', '1996-11-19', 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `device`
--
ALTER TABLE `device`
  ADD PRIMARY KEY (`dvc_id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`hst_id`);

--
-- Indexes for table `ownership`
--
ALTER TABLE `ownership`
  ADD PRIMARY KEY (`own_id`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `token_email`
--
ALTER TABLE `token_email`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `hst_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `ownership`
--
ALTER TABLE `ownership`
  MODIFY `own_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
