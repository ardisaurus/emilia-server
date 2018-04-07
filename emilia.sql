-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 07, 2018 at 06:29 PM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 5.6.33

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
('bk803', '', '0192023a7bbd73250516f069df18b500', '', 0),
('go956', '', '0192023a7bbd73250516f069df18b500', '', 0),
('ue025', 'Front door', '0192023a7bbd73250516f069df18b500', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `hst_id` int(11) NOT NULL,
  `hst_date` date NOT NULL,
  `hst_time` time NOT NULL,
  `hst_dvc_id` varchar(60) NOT NULL,
  `hst_email` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`hst_id`, `hst_date`, `hst_time`, `hst_dvc_id`, `hst_email`) VALUES
(1, '2018-04-05', '06:00:00', 'bk803', 'onodera@haru.com'),
(3, '2018-04-05', '06:00:00', 'bk803', 'onodera@haru.com'),
(4, '2018-04-07', '05:53:18', 'ue025', 'onodera@haru.com');

-- --------------------------------------------------------

--
-- Table structure for table `ownership`
--

CREATE TABLE `ownership` (
  `own_id` int(11) NOT NULL,
  `own_email` varchar(100) NOT NULL,
  `own_dvc_id` varchar(60) NOT NULL,
  `own_level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ownership`
--

INSERT INTO `ownership` (`own_id`, `own_email`, `own_dvc_id`, `own_level`) VALUES
(5, 'onodera@haru.com', 'ue025', 0);

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
('ichijo@raku.com', 'Ichojo Raku', '0192023a7bbd73250516f069df18b500', '1995-01-01', 0, 1),
('onodera@haru.com', 'Onodera Haru', '0192023a7bbd73250516f069df18b500', '2003-12-20', 0, 1),
('onodera@kosaki.com', 'Onoderan Kosaki', '0192023a7bbd73250516f069df18b500', '1996-11-19', 1, 1);

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
  MODIFY `hst_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ownership`
--
ALTER TABLE `ownership`
  MODIFY `own_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
