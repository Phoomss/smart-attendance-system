-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2025 at 06:47 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `attendance_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `attendance_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `departure_time` timestamp NULL DEFAULT NULL,
  `status` enum('on_time','late','absent') DEFAULT 'on_time',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type` enum('ลาป่วย','ลากิจ') NOT NULL,
  `leave_date` date NOT NULL,
  `leave_end_date` date DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_quotas`
--

CREATE TABLE `leave_quotas` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `sick_limit` int(11) DEFAULT 30,
  `personal_limit` int(11) DEFAULT 6,
  `year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `employee_code` varchar(50) DEFAULT NULL,
  `title` varchar(10) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `surname` varchar(100) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `phone` varchar(10) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','employee') DEFAULT 'employee',
  `picture` varchar(255) DEFAULT NULL,
  `access_token` text DEFAULT NULL,
  `refresh_token` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `employee_code`, `title`, `firstname`, `surname`, `name`, `username`, `phone`, `email`, `password`, `role`, `picture`, `access_token`, `refresh_token`, `created_at`) VALUES
(1, 'EMP001', 'Mr.', 'Admin', 'System', NULL, 'admin', NULL, 'admin@admin.com', '$2y$10$LlGHDb589U6y9n0XjwvGBuzlRmdXjy3Sjv0c8le13UG8MK8Ct.JGi', 'admin', NULL, NULL, NULL, '2025-01-17 17:44:41'),
(2, 'EMP002', 'Mr.', 'John', 'Doe', 'John Doe', 'user1', '0812345678', 'user1@example.com', '$2y$10$LlGHDb589U6y9n0XjwvGBuzlRmdXjy3Sjv0c8le13UG8MK8Ct.JGi', 'employee', NULL, NULL, NULL, '2025-01-17 17:45:00'),
(3, 'EMP003', 'Ms.', 'Jane', 'Smith', 'Jane Smith', 'user2', '0812345679', 'user2@example.com', '$2y$10$LlGHDb589U6y9n0XjwvGBuzlRmdXjy3Sjv0c8le13UG8MK8Ct.JGi', 'employee', NULL, NULL, NULL, '2025-01-17 17:45:10'),
(4, 'EMP004', 'Mr.', 'Bob', 'Johnson', 'Bob Johnson', 'user3', '0812345680', 'user3@example.com', '$2y$10$LlGHDb589U6y9n0XjwvGBuzlRmdXjy3Sjv0c8le13UG8MK8Ct.JGi', 'employee', NULL, NULL, NULL, '2025-01-17 17:45:20');

--
-- Dumping data for table `leave_quotas`
--

INSERT INTO `leave_quotas` (`id`, `employee_id`, `sick_limit`, `personal_limit`, `year`) VALUES
(1, 1, 30, 6, 2025),
(2, 2, 30, 6, 2025),
(3, 3, 30, 6, 2025),
(4, 4, 30, 6, 2025);

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`id`, `employee_id`, `attendance_date`, `attendance_time`, `departure_time`, `status`, `created_at`) VALUES
(1, 2, '2025-01-15', '2025-01-15 08:00:00', '2025-01-15 17:00:00', 'on_time', '2025-01-15 08:00:00'),
(2, 3, '2025-01-15', '2025-01-15 08:15:00', '2025-01-15 17:15:00', 'on_time', '2025-01-15 08:15:00'),
(3, 4, '2025-01-15', '2025-01-15 09:05:00', '2025-01-15 18:05:00', 'late', '2025-01-15 09:05:00'),
(4, 2, '2025-01-16', '2025-01-16 07:55:00', '2025-01-16 16:55:00', 'on_time', '2025-01-16 07:55:00'),
(5, 3, '2025-01-16', '2025-01-16 08:30:00', '2025-01-16 17:30:00', 'late', '2025-01-16 08:30:00'),
(6, 4, '2025-01-16', '2025-01-16 08:05:00', '2025-01-16 17:05:00', 'on_time', '2025-01-16 08:05:00');

--
-- Dumping data for table `leaves`
--

INSERT INTO `leaves` (`id`, `employee_id`, `leave_type`, `leave_date`, `leave_end_date`, `reason`, `status`, `created_at`) VALUES
(1, 2, 'ลาป่วย', '2025-01-10', '2025-01-10', 'ปวดหัวตัวร้อน', 'approved', '2025-01-09 20:00:00'),
(2, 3, 'ลากิจ', '2025-01-12', '2025-01-13', 'ไปทำธุระที่ธนาคาร', 'approved', '2025-01-11 10:00:00'),
(3, 4, 'ลาป่วย', '2025-01-17', '2025-01-17', 'เป็นไข้หวัด', 'pending', '2025-01-17 07:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `leave_quotas`
--
ALTER TABLE `leave_quotas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id_year` (`employee_id`,`year`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `employee_code` (`employee_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `leave_quotas`
--
ALTER TABLE `leave_quotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `leaves_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `leave_quotas`
--
ALTER TABLE `leave_quotas`
  ADD CONSTRAINT `leave_quotas_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
