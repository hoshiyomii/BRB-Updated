-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2025 at 04:23 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `barangay_website`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `admin_level` enum('1','2') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `admin_level`) VALUES
(1, 'admin', '$2y$10$xg0wK8auLHFchjHsXQIxzeKYzHMqJ7Zv6YWCRexFZVF90TcpsI4uu', '1'),
(2, 'superadmin', '$2y$10$LllWRZhb9pUhFYpi/jjSX.JwGyf1PiI8UH0set6PFNaQeQZXkf2Gm', '2'),
(3, 'admin3', '$2y$10$XyB0fyhnXH0QcUU5BM7mU..72/VCh1eXQGh0qWVxyWReQS4OKri9u', '1');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` enum('view-only','event') NOT NULL,
  `max_participants` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `registered_participants` int(11) DEFAULT 0,
  `image_path` varchar(255) DEFAULT NULL,
  `genre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `type`, `max_participants`, `created_at`, `registered_participants`, `image_path`, `genre`) VALUES
(16, 'Testing Announcement', 'This announcement is used for the user manual.', 'event', 50, '2025-02-12 13:38:24', 2, 'uploads/user man.jpg', ''),
(17, 'Adding New Announcement', 'Para sa user manual :O', 'event', 50, '2025-02-12 13:46:51', 1, 'uploads/user man.jpg', ''),
(21, 'Test for Genre 4', 'Testing genre', 'view-only', NULL, '2025-04-02 15:22:46', 0, NULL, 'Healthcare and Safety'),
(22, 'Test for Genre 5', 'Genre test', 'view-only', NULL, '2025-04-02 15:22:57', 0, NULL, 'Social and Community');

-- --------------------------------------------------------

--
-- Table structure for table `certificate_of_indigency`
--

CREATE TABLE `certificate_of_indigency` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `occupancy` varchar(255) NOT NULL,
  `income` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificate_of_indigency`
--

INSERT INTO `certificate_of_indigency` (`id`, `user_id`, `occupancy`, `income`, `created_at`) VALUES
(1, 2, 'Programmer', 2.00, '2025-04-16 14:00:20'),
(2, 6, 'Gajillionare', 99999999.99, '2025-04-22 12:10:51');

-- --------------------------------------------------------

--
-- Table structure for table `certificate_of_residency`
--

CREATE TABLE `certificate_of_residency` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `resident_since` date NOT NULL DEFAULT '2025-01-01',
  `date` date NOT NULL,
  `id_image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificate_of_residency`
--

INSERT INTO `certificate_of_residency` (`id`, `user_id`, `resident_since`, `date`, `id_image`, `created_at`) VALUES
(1, 2, '2025-01-01', '2025-04-16', 'uploads/670e1fc607d825c1a783f4308043be28.jpg', '2025-04-16 13:21:19'),
(2, 6, '2025-04-11', '2025-04-22', 'uploads/FwwlkrDX0Ag5Mj7.jpg', '2025-04-22 11:30:51');

-- --------------------------------------------------------

--
-- Table structure for table `clearance_major_construction`
--

CREATE TABLE `clearance_major_construction` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `schedule` datetime NOT NULL,
  `contractor` varchar(255) NOT NULL,
  `construction_address` varchar(255) NOT NULL,
  `infrastructures` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clearance_major_construction`
--

INSERT INTO `clearance_major_construction` (`id`, `user_id`, `schedule`, `contractor`, `construction_address`, `infrastructures`, `created_at`) VALUES
(1, 2, '2025-04-04 23:28:00', 'Testing', 'Testing Street', '', '2025-04-16 15:27:04'),
(2, 6, '2025-05-31 12:08:00', 'The Construction Company', 'Oflsd Street, 0424 lot 32', 'House', '2025-04-22 13:06:03');

-- --------------------------------------------------------

--
-- Table structure for table `document_requests`
--

CREATE TABLE `document_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `contractor` varchar(255) DEFAULT NULL,
  `schedule` datetime DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `utility_type` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `id_image` varchar(255) DEFAULT NULL,
  `occupancy` varchar(255) DEFAULT NULL,
  `monthly_salary` decimal(10,2) DEFAULT NULL,
  `clearance_image` varchar(255) DEFAULT NULL,
  `ownership_type` varchar(255) DEFAULT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `business_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_requests`
--

INSERT INTO `document_requests` (`id`, `user_id`, `document_type`, `contractor`, `schedule`, `type`, `utility_type`, `company`, `id_image`, `occupancy`, `monthly_salary`, `clearance_image`, `ownership_type`, `business_name`, `business_type`, `created_at`) VALUES
(1, 2, 'repair_and_construction', 'qwe', '2025-02-21 09:11:00', 'Renovation', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-12 13:08:48'),
(2, 2, 'clearance_major_construction', '3243er', '2025-02-01 21:17:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-12 13:17:40'),
(3, 5, 'work_permit_utilities', NULL, NULL, NULL, 'Water', 'Maynilad', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-12 13:43:29');

-- --------------------------------------------------------

--
-- Table structure for table `new_business_permit`
--

CREATE TABLE `new_business_permit` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `nature_of_business` varchar(255) NOT NULL,
  `business_type` enum('Solo','Shared') NOT NULL,
  `co_owner` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `new_business_permit`
--

INSERT INTO `new_business_permit` (`id`, `user_id`, `owner`, `location`, `business_name`, `nature_of_business`, `business_type`, `co_owner`, `date`, `created_at`) VALUES
(1, 2, 'Jane Doe', 'Amogus st.', 'Amog', 'Sari-sari store', 'Shared', 'Crewmnmate', '2025-04-16', '2025-04-16 14:08:56');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `announcement_id`, `user_id`, `name`, `email`, `registered_at`) VALUES
(6, 16, 5, 'Bryan.laoke', 'bryan.laoke@email.com', '2025-02-12 13:39:51'),
(7, 17, 6, 'BryanLaoke', 'hoshiyomi08@gmail.com', '2025-04-22 13:14:54'),
(8, 16, 6, 'BryanLaoke', 'hoshiyomi08@gmail.com', '2025-04-22 13:14:58');

-- --------------------------------------------------------

--
-- Table structure for table `repair_and_construction`
--

CREATE TABLE `repair_and_construction` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_of_request` date NOT NULL DEFAULT curdate(),
  `homeowner_name` varchar(255) NOT NULL,
  `homeowner_contact` varchar(15) NOT NULL,
  `contractor_name` varchar(255) NOT NULL,
  `contractor_contact` varchar(15) NOT NULL,
  `activity_nature` enum('Repairs','Minor Construction','Construction','Demolition') NOT NULL,
  `construction_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repair_and_construction`
--

INSERT INTO `repair_and_construction` (`id`, `user_id`, `date_of_request`, `homeowner_name`, `homeowner_contact`, `contractor_name`, `contractor_contact`, `activity_nature`, `construction_address`, `created_at`) VALUES
(1, 2, '2025-04-17', 'Fasdasdiko', '92304934949', 'Testing', '23402304', 'Repairs', NULL, '2025-04-15 14:26:23'),
(2, 2, '2025-04-16', '', 'Testing', 'Awooga', '4124253235532', 'Repairs', NULL, '2025-04-16 16:12:56'),
(3, 2, '2025-04-16', 'Jane Doe', '92304934949', 'Awooga asdads', '412425323553223', 'Minor Construction', NULL, '2025-04-16 16:17:29'),
(4, 1, '2025-04-19', 'John Doe', '34562523', 'Testing', '03588357344', 'Demolition', 'Testing Street awooga', '2025-04-19 04:00:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `street` varchar(255) NOT NULL,
  `house_number` varchar(50) NOT NULL,
  `date_registered` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `first_name`, `last_name`, `gender`, `phone_number`, `email`, `birthdate`, `street`, `house_number`, `date_registered`) VALUES
(1, 'user', '$2y$10$Ue1SPKQnFG4ZpI9BEVbfdOm72jxkwZzX8kjMVDSk.p/Vhc6H8Na92', 'John', 'Doe', 'Male', '09123456789', 'user@gmail.com', '2025-02-01', 'asd', 'wqeqew', '2025-02-11 11:17:35'),
(2, 'user2', '$2y$10$Mggs9pMcCTsH.q9tVV44I.Hn8.k5Kgl/LO9os6lBKK.GSe5nBKffO', 'Jane', 'Doe', 'Female', '091234567892', 'user2@gmail.com', '2025-02-05', 'asd', '8', '2025-02-11 11:53:42'),
(3, 'bryan', '$2y$10$IaCL/9OMrYV1.B8iWFQqlOIrlh5YN0LllFfYmrJFG4CL9UVowPZBO', 'bryan', 'last', 'Male', '09217327039', 'bryan@gmail.com', '2025-02-05', 'asd', '32', '2025-02-11 14:32:25'),
(4, 'Laoke', '$2y$10$nUYpUdZjpZbC/v602M0R5OG3HEYPoSNWUKeJiRS3KPMl5SkT2qIDK', 'Bryan', 'Laoke', 'Male', '09217327039', 'laoke@email.com', '2025-01-29', '32', '3434', '2025-02-12 11:33:51'),
(5, 'Bryan.laoke', '$2y$10$VRLPTjmISYVkOet69sBnwONpu9IcQQDepO6mz/yxbNOiomuGwd4Sa', 'Bryan', 'Laoke', 'Male', '09123456789', 'bryan.laoke@email.com', '2004-06-07', 'Piling Santos', '23', '2025-02-12 13:34:34'),
(6, 'BryanLaoke', '$2y$10$hjjuTSW3M8DlBPAjxWyO.et0uFpoxAPilIpDLlfLKqlWX7fsrruSS', 'Bryan', 'Laoke', 'Male', '021312351234', 'hoshiyomi08@gmail.com', '2004-06-07', 'Testing Street', '2423', '2025-04-22 11:30:28');

-- --------------------------------------------------------

--
-- Table structure for table `work_permit_utilities`
--

CREATE TABLE `work_permit_utilities` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_of_request` date NOT NULL,
  `date_of_work` date NOT NULL,
  `contact_no` varchar(15) NOT NULL,
  `address` varchar(255) NOT NULL,
  `service_provider` enum('Meralco','Globe','PLDT','Sky Cable','CIGNAL','Manila Water','Smart','Bayantel','Destiny','Others') NOT NULL,
  `other_service_provider` varchar(255) DEFAULT NULL,
  `nature_of_work` enum('New installation','Repair/Maintenance','Permanent Disconnection','Reconnection') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `utility_type` enum('Water','Electricity','Internet','Others') NOT NULL,
  `other_utility_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `work_permit_utilities`
--

INSERT INTO `work_permit_utilities` (`id`, `user_id`, `date_of_request`, `date_of_work`, `contact_no`, `address`, `service_provider`, `other_service_provider`, `nature_of_work`, `created_at`, `utility_type`, `other_utility_type`) VALUES
(1, 2, '2025-04-16', '2025-04-10', '123123123', 'asd 8', 'Others', 'Test2', 'Repair/Maintenance', '2025-04-16 10:56:11', 'Others', 'Test'),
(2, 1, '2025-04-19', '2025-05-03', '2509824892480', 'asd wqeqew', 'Globe', NULL, 'Repair/Maintenance', '2025-04-19 06:57:26', 'Internet', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `certificate_of_indigency`
--
ALTER TABLE `certificate_of_indigency`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `certificate_of_residency`
--
ALTER TABLE `certificate_of_residency`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `clearance_major_construction`
--
ALTER TABLE `clearance_major_construction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `document_requests`
--
ALTER TABLE `document_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `new_business_permit`
--
ALTER TABLE `new_business_permit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_event` (`announcement_id`,`user_id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `repair_and_construction`
--
ALTER TABLE `repair_and_construction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `work_permit_utilities`
--
ALTER TABLE `work_permit_utilities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `certificate_of_indigency`
--
ALTER TABLE `certificate_of_indigency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `certificate_of_residency`
--
ALTER TABLE `certificate_of_residency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `clearance_major_construction`
--
ALTER TABLE `clearance_major_construction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `document_requests`
--
ALTER TABLE `document_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `new_business_permit`
--
ALTER TABLE `new_business_permit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `repair_and_construction`
--
ALTER TABLE `repair_and_construction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `work_permit_utilities`
--
ALTER TABLE `work_permit_utilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `certificate_of_indigency`
--
ALTER TABLE `certificate_of_indigency`
  ADD CONSTRAINT `certificate_of_indigency_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `certificate_of_residency`
--
ALTER TABLE `certificate_of_residency`
  ADD CONSTRAINT `certificate_of_residency_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `clearance_major_construction`
--
ALTER TABLE `clearance_major_construction`
  ADD CONSTRAINT `clearance_major_construction_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `new_business_permit`
--
ALTER TABLE `new_business_permit`
  ADD CONSTRAINT `new_business_permit_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `repair_and_construction`
--
ALTER TABLE `repair_and_construction`
  ADD CONSTRAINT `repair_and_construction_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `work_permit_utilities`
--
ALTER TABLE `work_permit_utilities`
  ADD CONSTRAINT `work_permit_utilities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
