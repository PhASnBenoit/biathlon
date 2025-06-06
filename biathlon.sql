-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 06, 2025 at 07:15 PM
-- Server version: 10.11.11-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `biathlon`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE `activity` (
  `id_activity` int(11) NOT NULL,
  `state` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `activity`
--

INSERT INTO `activity` (`id_activity`, `state`) VALUES
(1, -1);

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `id_config` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `token` varchar(32) NOT NULL,
  `nom_course` varchar(50) NOT NULL,
  `nb_juges` int(11) NOT NULL DEFAULT 1,
  `max_juges` int(11) NOT NULL DEFAULT 16,
  `dist2T` int(11) NOT NULL DEFAULT 600,
  `distPen` int(11) NOT NULL DEFAULT 100,
  `distSansPen` int(11) NOT NULL DEFAULT 20,
  `dist1T` int(11) NOT NULL DEFAULT 325
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`id_config`, `code`, `token`, `nom_course`, `nb_juges`, `max_juges`, `dist2T`, `distPen`, `distSansPen`, `dist1T`) VALUES
(1, '$2y$10$xC5PSXT0fiiVWgCvmnSLN.289a9vyoiGWHSB/58c62dPn9NnwhrVG', '0', 'Course 1', 1, 1, 650, 80, 20, 325);

-- --------------------------------------------------------

--
-- Table structure for table `race`
--

CREATE TABLE `race` (
  `id_race` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `runnerName` varchar(50) NOT NULL,
  `judgeName` varchar(50) NOT NULL,
  `token` varchar(32) NOT NULL,
  `judgeState` int(11) NOT NULL,
  `t0` double DEFAULT NULL COMMENT 'Temps initial',
  `t1` double DEFAULT NULL COMMENT '1T',
  `t2` double DEFAULT NULL COMMENT 'ST1',
  `t3` double DEFAULT NULL COMMENT 'TP1',
  `t4` double DEFAULT NULL COMMENT '2T',
  `t5` double DEFAULT NULL COMMENT 'ST2',
  `t6` double DEFAULT NULL COMMENT 'TP2',
  `t7` double DEFAULT NULL COMMENT '2T',
  `t8` double DEFAULT NULL COMMENT 'ST3',
  `t9` double DEFAULT NULL COMMENT 'TP3',
  `t10` double DEFAULT NULL COMMENT '1T',
  `totalTime` double DEFAULT 0,
  `seqTirs1` varchar(5) DEFAULT '-/5',
  `seqTirs2` varchar(5) DEFAULT '-/5',
  `seqTirs3` varchar(5) DEFAULT '-/5'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`id_activity`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id_config`);

--
-- Indexes for table `race`
--
ALTER TABLE `race`
  ADD PRIMARY KEY (`id_race`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity`
--
ALTER TABLE `activity`
  MODIFY `id_activity` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `config`
--
ALTER TABLE `config`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `race`
--
ALTER TABLE `race`
  MODIFY `id_race` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
