-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 13, 2025 at 05:27 AM
-- Server version: 12.0.2-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `trackdocu`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_departments`
--

CREATE TABLE `tbl_departments` (
  `id` int(100) NOT NULL,
  `department_name` varchar(100) DEFAULT NULL,
  `date_created` timestamp NOT NULL,
  `date_updated` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_departments`
--

INSERT INTO `tbl_departments` (`id`, `department_name`, `date_created`, `date_updated`) VALUES
(1, 'Mayor\'s Office', '2025-10-08 01:38:50', '2025-10-08 01:38:50'),
(2, 'Department of Agriculture', '2025-10-08 02:20:20', '2025-10-08 02:20:20'),
(3, 'Department of Accounting ', '2025-10-08 02:20:41', '2025-10-08 02:20:41'),
(4, 'Department of Engineering', '2025-10-08 13:00:11', '2025-10-08 13:00:11'),
(5, 'Department of Human Recources', '2025-10-08 13:03:05', '2025-10-08 13:03:05'),
(6, 'Department of the Interior and Local Government', '2025-10-08 13:03:33', '2025-10-08 13:03:33'),
(7, 'Management Information System', '2025-10-08 13:04:03', '2025-10-08 13:04:03'),
(8, 'Department of Social Welfare and Development', '2025-10-08 13:04:40', '2025-10-08 13:04:40'),
(9, 'Regular Employees', '2025-10-08 13:05:01', '2025-10-08 13:05:01');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_fileaudittrails`
--

CREATE TABLE `tbl_fileaudittrails` (
  `id` int(255) NOT NULL,
  `file_id` int(255) DEFAULT NULL,
  `user_id` int(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `user_department_id` int(50) DEFAULT NULL,
  `usertype_id` int(11) DEFAULT NULL,
  `folder_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `to_department_id` int(10) DEFAULT NULL,
  `to_usertype_id` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `time_stamp` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_fileaudittrails`
--

INSERT INTO `tbl_fileaudittrails` (`id`, `file_id`, `user_id`, `status`, `user_department_id`, `usertype_id`, `folder_id`, `action_type`, `to_department_id`, `to_usertype_id`, `remarks`, `time_stamp`) VALUES
(176, 89, 12, 'Pending', 9, 4, 1, 'Uploaded', 2, 3, NULL, '2025-10-11 11:03:33'),
(177, 89, 9, 'Viewed', 2, 3, 1, 'Under Review', 0, 0, NULL, '2025-10-11 11:08:01'),
(178, 89, 9, 'Forwarded', 2, 3, 1, 'Forwarded', 2, 2, NULL, '2025-10-11 11:08:09'),
(179, 89, 8, 'Viewed', 2, 2, 1, 'Under Review', 0, 0, NULL, '2025-10-11 11:10:35'),
(180, 89, 8, 'Approved', 2, 2, 1, 'Under Review', 1, 1, NULL, '2025-10-11 11:10:37'),
(182, 89, 3, 'Viewed', 1, 1, 1, 'Under Review', 0, 0, NULL, '2025-10-11 11:15:11'),
(183, 89, 3, 'Viewed', 1, 1, 1, 'Under Review', 0, 0, NULL, '2025-10-11 11:16:00'),
(184, 89, 3, 'Completed', 1, 1, 1, 'Final Approval', NULL, NULL, 'Final Approval', '2025-10-11 11:16:04'),
(185, 90, 12, 'Pending', 9, 4, 2, 'Uploaded', 2, 3, NULL, '2025-10-11 12:08:13');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_files`
--

CREATE TABLE `tbl_files` (
  `id` int(255) NOT NULL,
  `filename` varchar(100) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `user_id` int(50) DEFAULT NULL,
  `is_complete` int(2) DEFAULT NULL,
  `date_uploaded` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_files`
--

INSERT INTO `tbl_files` (`id`, `filename`, `file_path`, `user_id`, `is_complete`, `date_uploaded`) VALUES
(89, 'test.pdf', '../uploads/agriculture_1760180613_test.pdf', 12, NULL, '2025-10-11 11:03:33'),
(90, 'form.pdf', '../uploads/agriculture_1760184493_form.pdf', 12, NULL, '2025-10-11 12:08:13');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_folders`
--

CREATE TABLE `tbl_folders` (
  `id` int(11) NOT NULL,
  `folder_name` varchar(50) NOT NULL,
  `date_created` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_folders`
--

INSERT INTO `tbl_folders` (`id`, `folder_name`, `date_created`) VALUES
(1, 'Agri Folder', '2025-10-08 12:25:28'),
(2, 'Acct Folder', '2025-10-08 12:25:43');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id` int(255) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `department_id` int(10) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `usertype_id` int(10) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT NULL,
  `date_updated` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id`, `firstname`, `lastname`, `email`, `department_id`, `username`, `password`, `usertype_id`, `status`, `last_login`, `date_created`, `date_updated`) VALUES
(3, 'jordan', 'zapanta', 'jordan@gmail.com', 1, 'jordan', '123', 1, 'active', NULL, '2025-10-07 19:51:53', NULL),
(7, 'Deparment Admin', 'Accounting', 'dept@gmail.com', 3, 'test3', '123', 2, 'active', NULL, '2025-10-08 05:52:35', NULL),
(8, 'Department Admin', 'Agri', 'Dept@gmail.com', 2, 'test2', '123', 2, 'active', NULL, '2025-10-08 05:53:36', NULL),
(9, 'test agri', 'Agri', 'argi@gmail.com', 2, 'empagri', '123', 3, 'active', NULL, '2025-10-08 05:54:26', NULL),
(10, 'dept employee', 'acct', 'acttemp@gmail.com', 3, 'empacct', '123', 3, 'active', NULL, '2025-10-08 05:55:34', NULL),
(12, 'regular', 'Zapanta', 'Regular@gmail.com', 9, 'regular', '123', 4, 'active', NULL, '2025-10-08 07:13:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_usertype`
--

CREATE TABLE `tbl_usertype` (
  `id` int(10) NOT NULL,
  `usertype` varchar(50) DEFAULT NULL,
  `date_created` timestamp NOT NULL,
  `date_updated` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_usertype`
--

INSERT INTO `tbl_usertype` (`id`, `usertype`, `date_created`, `date_updated`) VALUES
(1, 'Municipal Admin', '2025-10-08 01:50:04', '2025-10-08 01:50:04'),
(2, 'Department Head', '2025-10-09 01:54:15', '2025-10-09 01:54:15'),
(3, 'Department Staff', '2025-10-09 13:07:13', '2025-10-09 13:07:13'),
(4, 'Regular Employee', '2025-10-08 13:05:47', '2025-10-08 13:05:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_departments`
--
ALTER TABLE `tbl_departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_fileaudittrails`
--
ALTER TABLE `tbl_fileaudittrails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_files`
--
ALTER TABLE `tbl_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_folders`
--
ALTER TABLE `tbl_folders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_usertype`
--
ALTER TABLE `tbl_usertype`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_departments`
--
ALTER TABLE `tbl_departments`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_fileaudittrails`
--
ALTER TABLE `tbl_fileaudittrails`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- AUTO_INCREMENT for table `tbl_files`
--
ALTER TABLE `tbl_files`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `tbl_folders`
--
ALTER TABLE `tbl_folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_usertype`
--
ALTER TABLE `tbl_usertype`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
