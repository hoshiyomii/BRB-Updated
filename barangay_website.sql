-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2025 at 12:09 PM
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
  `admin_level` enum('1','2') NOT NULL DEFAULT '1',
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `admin_level`, `first_name`, `middle_name`, `last_name`) VALUES
(1, 'admin-testcase', '$2y$10$up7q6Mn1k/fV9vuW1q.k3.s5T4dW7uJ7gAgPhkHBVZOXCCa/Z3W8e', '1', 'Testing', 'Cases', 'testcase'),
(2, 'superadmin', '$2y$10$LllWRZhb9pUhFYpi/jjSX.JwGyf1PiI8UH0set6PFNaQeQZXkf2Gm', '2', '', NULL, ''),
(3, 'admin3', '$2y$10$XyB0fyhnXH0QcUU5BM7mU..72/VCh1eXQGh0qWVxyWReQS4OKri9u', '1', '', NULL, ''),
(4, 'admin-yomi', '$2y$10$MOHfWhP8JoO/hkNkGgXEceoUGGPvqbtEI5U4zl06LJehfkdpZuCUe', '2', 'Hoshi', 'Miyos', 'Yomi'),
(5, 'admin-alpha', '$2y$10$0YycI91A5b1PO4BxYQfXJONxM1QMZdhm2txPc3phQpuSd0c8qr.0K', '2', '', NULL, ''),
(6, 'admin-beta', '$2y$10$189GC5t9uBJqvl5RDZcp7elxPHLHZN7xQluCI/bNlFOLtaMSN.Z1K', '2', '', NULL, ''),
(7, 'admin-charlie', '$2y$10$PdWrZW6ybrN0Pmf5hSAk4.ydb3Qlg2Y16t5AyqXkDR0djWftsM4Km', '2', '', NULL, ''),
(8, 'admin-delta', '$2y$10$xfwymP1FaIFJZt5AVUsNkuVMPiu0ZaNfLFFjWvWS5.vK6gMhrtNIa', '2', '', NULL, ''),
(9, 'admin-golf', '$2y$10$Rw/vsW/g4o2/03BXeqRofuDZmRc5b0vrRhx5HWNER37gohXnN/Xeu', '2', '', NULL, '');

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
  `genre` varchar(255) NOT NULL,
  `active_until` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `registration_open_until` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `type`, `max_participants`, `created_at`, `registered_participants`, `image_path`, `genre`, `active_until`, `is_active`, `registration_open_until`) VALUES
(28, 'Testing Announcement', 'No time frame, ', 'view-only', 0, '2025-05-14 01:59:30', 0, 'uploads/default.jpg', 'Animals', NULL, 1, NULL),
(29, '214241', 'dssdffewrwertwet', 'view-only', 0, '2025-05-23 14:12:41', 0, 'uploads/maxresdefault.jpg', 'Animals', NULL, 1, NULL),
(30, 'ffew', '5353', 'view-only', 0, '2025-05-23 14:12:57', 0, 'uploads/maxresdefault.jpg', 'Work and Employment', NULL, 1, NULL),
(31, 'qwxxzccz', 'fefe', 'view-only', 0, '2025-05-23 14:13:12', 0, 'uploads/default.jpg', 'Government and Public Affairs', NULL, 1, NULL),
(32, '561654', '24424dsfdfsdfs', 'view-only', 0, '2025-05-23 14:13:37', 0, 'uploads/512x512bb.jpg', 'Holidays and Events', NULL, 1, NULL),
(33, 'cvxcvxcvxcvxcxv', 'erertertertert', 'view-only', 0, '2025-05-23 14:15:24', 0, 'uploads/670e1fc607d825c1a783f4308043be28.jpg', 'Work and Employment', NULL, 1, NULL),
(35, 'Suisei', 'WEWEEWEWWEWEWE', 'view-only', 0, '2025-05-23 14:16:32', 0, 'uploads/1285514.png', 'Healthcare and Safety', NULL, 1, NULL),
(36, 'xccvxcvx', 'eerr', 'view-only', 0, '2025-05-23 14:16:37', 0, 'uploads/default.jpg', 'Work and Employment', NULL, 1, NULL),
(37, 'dsffxc', 'fcxvcvxcv', 'view-only', 0, '2025-05-23 17:17:58', 0, 'uploads/default.jpg', 'Work and Employment', NULL, 1, NULL),
(43, 'Launching New Website', 'The Barangay Blue Ridge B is getting a new website for its visitors and residents! Said website contains access to announcements that can be viewed freely anytime and anywhere with internet access, request documents and Item/Facility reservations remotely, and contact the barangay through their phones and computers. The Barangay Blue Ridge B is getting a new website for its visitors and residents! Said website contains access to announcements that can be viewed freely anytime and anywhere with internet access, request documents and Item/Facility reservations remotely, and contact the barangay through their phones and computers.', 'view-only', NULL, '2025-05-26 11:28:24', 0, 'uploads/maxresdefault.jpg', 'Work and Employment', NULL, 1, NULL),
(44, 'Testing after admin revamps', 'Bla bla bla ble ble ble blu blu blu ah blublublublublublublublublublublublublublublublublublublublublublublu', 'view-only', NULL, '2025-05-27 12:32:01', 0, 'uploads/default.jpg', 'Work and Employment', NULL, 0, NULL),
(46, 'Testing after announcement revamp', 'Awooga awooga', 'view-only', NULL, '2025-05-31 13:52:11', 0, 'uploads/Shrimps.png', 'Social and Community', NULL, 1, NULL),
(47, 'Testing after announcement revamps', 'Awooga awoogas', 'view-only', NULL, '2025-05-31 14:07:39', 0, 'uploads/Falador.png', 'Healthcare and Safety', NULL, 1, NULL),
(56, 'Edited Announcement Testings', 'Amogus dfgdfdfgdfgdfg', 'event', 5, '2025-06-03 08:10:17', 2, 'uploads/default.jpg', 'Work and Employment', NULL, 1, '2025-06-25'),
(59, 'Testing for fit', 'The Barangay Blue Ridge B is getting a new website for its visitors and residents! Said website contains access to announcements that can be viewed freely anytime and anywhere with internet access, request documents and Item/Facility reservations remotely, and contact the barangay through their phones and computers. The Barangay Blue Ridge B is getting a new website for its visitors and residents! Said website contains access to announcements that can be viewed freely anytime and anywhere with internet access, request documents and Item/Facility reservations remotely, and contact the barangay through their phones and computers.', 'event', 2424, '2025-06-04 11:15:29', 2, 'uploads/default.jpg', 'Work and Employment', NULL, 1, '2025-06-18'),
(62, 'Testing for archive system client side', 'Testing the announcement module here ', 'view-only', NULL, '2025-06-04 11:27:29', 0, 'uploads/default.jpg', 'Work and Employment', '2025-06-04 19:29:00', 0, NULL),
(63, 'weqtw90tu890', '23958923589diosfjiosdjfio woejrtiowe ioewklngjko s 89wut 89w hsfh s9ufhesu89 huw89h 789h sdu8fh wuh u89h78 hsiufh 8sgf8 7sgf78sfbwubf 8f 8ry78w huhyfbuysf8 8s7yf78 sfw4 2 235 235 sdfsf wt 2t 2t we3t wtew sdfds fs df wfqw2f', 'event', 5, '2025-06-04 11:35:18', 0, 'uploads/default.jpg', 'Work and Employment', NULL, 1, '2025-06-03');

-- --------------------------------------------------------

--
-- Table structure for table `barangay_officials`
--

CREATE TABLE `barangay_officials` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(50) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `term_start` date DEFAULT NULL,
  `term_end` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificate_of_indigency`
--

CREATE TABLE `certificate_of_indigency` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `occupancy` varchar(255) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected','picked_up') NOT NULL DEFAULT 'pending',
  `approved_by` varchar(255) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `pickup_schedule` datetime DEFAULT NULL,
  `time_approved` datetime DEFAULT NULL,
  `rejected_by` varchar(255) DEFAULT NULL,
  `time_rejected` datetime DEFAULT NULL,
  `print_count` int(11) DEFAULT 0,
  `pickup_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificate_of_indigency`
--

INSERT INTO `certificate_of_indigency` (`id`, `user_id`, `occupancy`, `purpose`, `created_at`, `status`, `approved_by`, `rejection_reason`, `pickup_schedule`, `time_approved`, `rejected_by`, `time_rejected`, `print_count`, `pickup_name`) VALUES
(1, 2, 'Programmer', 'Amogus testing purpose', '2025-04-16 14:00:20', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(3, 2, 'Gajillionaresds', '', '2025-04-24 16:28:11', 'approved', 'admin', NULL, '2025-05-16 00:09:00', '2025-04-25 04:09:30', NULL, NULL, 0, NULL),
(4, 18, 'Programmerss', 'Amogus testing purposes', '2025-05-29 13:52:44', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(5, 21, 'Programmer', 'Amogus testing purpose', '2025-06-05 05:03:41', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, ''),
(6, 21, 'Programmer', 'Amogus testing purpose', '2025-06-05 05:03:41', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, ''),
(7, 24, 'qwer', 'Among US?!', '2025-06-08 02:34:20', 'approved', 'admin', NULL, '2025-06-10 10:34:00', '2025-06-08 10:34:44', NULL, NULL, 0, 'Tst on person pick up');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected','picked_up') NOT NULL DEFAULT 'pending',
  `approved_by` varchar(255) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `pickup_schedule` datetime DEFAULT NULL,
  `time_approved` datetime DEFAULT NULL,
  `rejected_by` varchar(255) DEFAULT NULL,
  `time_rejected` datetime DEFAULT NULL,
  `print_count` int(11) DEFAULT 0,
  `pickup_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificate_of_residency`
--

INSERT INTO `certificate_of_residency` (`id`, `user_id`, `resident_since`, `date`, `id_image`, `created_at`, `status`, `approved_by`, `rejection_reason`, `pickup_schedule`, `time_approved`, `rejected_by`, `time_rejected`, `print_count`, `pickup_name`) VALUES
(1, 2, '2025-01-15', '2025-04-16', 'uploads/670e1fc607d825c1a783f4308043be28.jpg', '2025-04-16 13:21:19', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(3, 21, '2025-06-05', '2025-06-05', 'uploads/Raw_anglerfish.png', '2025-06-05 04:24:46', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, ''),
(4, 24, '2024-12-05', '2025-06-08', 'uploads/68747470733a2f2f6d656469612e74656e6f722e636f6d2f696b79313544506b6c457341414141642f7375697365692d7369702e676966.gif', '2025-06-08 02:33:18', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'asd');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected','picked_up') NOT NULL DEFAULT 'pending',
  `approved_by` varchar(255) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `pickup_schedule` datetime DEFAULT NULL,
  `time_approved` datetime DEFAULT NULL,
  `rejected_by` varchar(255) DEFAULT NULL,
  `time_rejected` datetime DEFAULT NULL,
  `print_count` int(11) DEFAULT 0,
  `pickup_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clearance_major_construction`
--

INSERT INTO `clearance_major_construction` (`id`, `user_id`, `schedule`, `contractor`, `construction_address`, `infrastructures`, `created_at`, `status`, `approved_by`, `rejection_reason`, `pickup_schedule`, `time_approved`, `rejected_by`, `time_rejected`, `print_count`, `pickup_name`) VALUES
(1, 2, '2025-04-04 23:28:00', 'Testing', 'Testing Street', '', '2025-04-16 15:27:04', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(3, 2, '2025-04-25 14:43:00', 'The Construction Company', 'Road 1 Dona Petra Tumana', 'House, asd', '2025-04-24 16:41:07', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(4, 2, '2025-04-25 14:43:00', 'The Construction Company', 'Road 1 Dona Petra Tumana', 'Housessss24s', '2025-04-24 16:41:09', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(5, 21, '2025-06-19 15:40:00', 'qwe', 'Road 1 Dona Petra Tumana', 'Housess', '2025-06-05 04:37:50', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, ''),
(6, 24, '2025-06-16 10:47:00', 'The Construction Company', 'Road 1 Dona Petra Tumana', 'Housess', '2025-06-08 02:47:50', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'YOmi WOirss');

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
-- Table structure for table `facilities_reservations`
--

CREATE TABLE `facilities_reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `facility_type` enum('Multi Purpose Hall','Community Center','Session Hall','Conference Room','Small Meeting Room') NOT NULL,
  `is_mandatory_charges_applicable` tinyint(1) DEFAULT 1,
  `with_aircon` tinyint(1) DEFAULT 0,
  `rooftop_option` tinyint(1) DEFAULT 0,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `sound_system` tinyint(1) DEFAULT 0,
  `projector` tinyint(1) DEFAULT 0,
  `lifetime_table` int(11) DEFAULT 0,
  `lifetime_chair` int(11) DEFAULT 0,
  `long_table` int(11) DEFAULT 0,
  `monoblock_chair` int(11) DEFAULT 0,
  `security_parking` tinyint(1) DEFAULT 0,
  `group_over_50` tinyint(1) DEFAULT 0,
  `caretaker` tinyint(1) DEFAULT 0,
  `sound_operator` tinyint(1) DEFAULT 0,
  `total_cost` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` varchar(255) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `time_approved` datetime DEFAULT NULL,
  `time_rejected` datetime DEFAULT NULL,
  `rejected_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facilities_reservations`
--

INSERT INTO `facilities_reservations` (`id`, `user_id`, `facility_type`, `is_mandatory_charges_applicable`, `with_aircon`, `rooftop_option`, `start_time`, `end_time`, `sound_system`, `projector`, `lifetime_table`, `lifetime_chair`, `long_table`, `monoblock_chair`, `security_parking`, `group_over_50`, `caretaker`, `sound_operator`, `total_cost`, `created_at`, `status`, `approved_by`, `rejection_reason`, `time_approved`, `time_rejected`, `rejected_by`) VALUES
(1, 24, 'Small Meeting Room', 0, 0, 0, '2025-06-13 21:00:00', '2025-06-13 23:00:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 400.00, '2025-06-07 12:14:08', 'approved', 'admin', NULL, '2025-06-07 17:14:14', NULL, NULL),
(2, 24, 'Multi Purpose Hall', 1, 1, 0, '2025-06-13 19:00:00', '2025-06-13 23:00:00', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 7600.00, '2025-06-07 12:38:19', 'rejected', NULL, 'asd', NULL, '2025-06-07 17:14:25', 'admin'),
(3, 24, 'Multi Purpose Hall', 1, 1, 0, '2025-06-13 19:00:00', '2025-06-13 23:00:00', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 9100.00, '2025-06-07 12:38:30', 'rejected', NULL, 'asd', NULL, '2025-06-08 03:44:58', 'admin'),
(4, 24, 'Multi Purpose Hall', 1, 1, 0, '2025-06-13 19:00:00', '2025-06-13 23:00:00', 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 6750.00, '2025-06-07 12:38:44', 'pending', NULL, NULL, NULL, NULL, NULL),
(5, 24, 'Multi Purpose Hall', 1, 1, 0, '2025-06-13 19:00:00', '2025-06-13 23:00:00', 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 6800.00, '2025-06-07 12:39:26', 'pending', NULL, NULL, NULL, NULL, NULL),
(6, 24, 'Multi Purpose Hall', 1, 1, 0, '2025-06-13 19:00:00', '2025-06-13 23:00:00', 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 7000.00, '2025-06-07 12:39:32', 'pending', NULL, NULL, NULL, NULL, NULL),
(7, 24, 'Multi Purpose Hall', 1, 1, 0, '2025-06-13 19:00:00', '2025-06-13 23:00:00', 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 7010.00, '2025-06-07 12:39:42', 'pending', NULL, NULL, NULL, NULL, NULL),
(8, 24, 'Multi Purpose Hall', 1, 1, 0, '2025-06-13 19:00:00', '2025-06-13 23:00:00', 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 9510.00, '2025-06-07 12:39:57', 'pending', NULL, NULL, NULL, NULL, NULL),
(9, 24, 'Multi Purpose Hall', 1, 1, 0, '2025-06-13 19:00:00', '2025-06-13 23:00:00', 1, 1, 1, 1, 1, 1, 0, 1, 0, 0, 9760.00, '2025-06-07 12:40:09', 'pending', NULL, NULL, NULL, NULL, NULL),
(10, 24, 'Community Center', 1, 1, 1, '2025-06-13 16:00:00', '2025-06-13 20:00:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 8000.00, '2025-06-07 12:59:35', 'pending', NULL, NULL, NULL, NULL, NULL),
(11, 24, 'Session Hall', 0, 0, 0, '2025-06-13 16:00:00', '2025-06-13 20:00:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2400.00, '2025-06-07 12:59:51', 'pending', NULL, NULL, NULL, NULL, NULL),
(12, 24, 'Session Hall', 0, 0, 0, '2025-06-13 16:00:00', '2025-06-13 20:00:00', 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 2550.00, '2025-06-07 13:00:00', 'pending', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `admin_name` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`id`, `document_id`, `document_type`, `action`, `admin_name`, `timestamp`) VALUES
(1, 4, 'clearance_major_construction', 'Updated infrastructures', NULL, '2025-05-29 22:32:23'),
(2, 4, 'clearance_major_construction', 'Updated infrastructures', NULL, '2025-05-29 22:32:46'),
(3, 4, 'clearance_major_construction', 'Updated infrastructures', NULL, '2025-05-29 22:35:43'),
(4, 4, 'clearance_major_construction', 'Updated infrastructures', NULL, '2025-05-29 22:39:36'),
(5, 4, 'clearance_major_construction', 'Updated infrastructures', NULL, '2025-05-29 22:41:41'),
(6, 4, 'clearance_major_construction', 'Updated infrastructures', 'hoshiyomii', '2025-05-29 23:18:09'),
(7, 4, 'clearance_major_construction', 'Updated infrastructures', 'hoshiyomii', '2025-05-29 23:21:35'),
(8, 15, 'repair_and_construction', 'Printed document (Count: 1)', 'hoshiyomii', '2025-05-29 23:31:09'),
(9, 1, 'repair_and_construction', 'Updated contractor_contact', 'hoshiyomii', '2025-05-29 23:32:27'),
(10, 1, 'repair_and_construction', 'Updated contractor_contact', 'Unknown Admin', '2025-05-29 23:33:04'),
(11, 1, 'repair_and_construction', 'Updated contractor_contact', 'admin', '2025-05-29 23:36:40'),
(12, 1, 'certificate_of_indigency', 'Updated purpose', 'admin', '2025-05-30 18:48:21'),
(13, 26, 'repair_and_construction', 'Updated construction_address', 'admin', '2025-06-05 15:08:59'),
(14, 4, 'repair_and_construction', 'Printed document (Count: 1)', 'testcase', '2025-06-08 10:55:11'),
(15, 4, 'repair_and_construction', 'Printed document (Count: 2)', 'testcase', '2025-06-08 10:55:42'),
(16, 12, 'repair_and_construction', 'Printed document (Count: 1)', 'testcase', '2025-06-08 11:55:08'),
(17, 12, 'repair_and_construction', 'Printed document (Count: 2)', 'Unknown Admin', '2025-06-08 11:55:30'),
(18, 17, 'repair_and_construction', 'Printed document (Count: 1)', 'Unknown Admin', '2025-06-08 12:00:55'),
(19, 12, 'repair_and_construction', 'Printed document (Count: 3)', 'admin', '2025-06-08 12:01:24'),
(20, 12, 'repair_and_construction', 'Printed document (Count: 4)', 'admin', '2025-06-08 12:01:29'),
(21, 12, 'repair_and_construction', 'Printed document (Count: 5)', 'admin', '2025-06-08 12:01:35');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected','picked_up') NOT NULL DEFAULT 'pending',
  `approved_by` varchar(255) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `pickup_schedule` datetime DEFAULT NULL,
  `time_approved` datetime DEFAULT NULL,
  `rejected_by` varchar(255) DEFAULT NULL,
  `time_rejected` datetime DEFAULT NULL,
  `print_count` int(11) DEFAULT 0,
  `pickup_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `new_business_permit`
--

INSERT INTO `new_business_permit` (`id`, `user_id`, `owner`, `location`, `business_name`, `nature_of_business`, `business_type`, `co_owner`, `created_at`, `status`, `approved_by`, `rejection_reason`, `pickup_schedule`, `time_approved`, `rejected_by`, `time_rejected`, `print_count`, `pickup_name`) VALUES
(1, 2, 'Jane Doe', 'Amogus st.s', 'Amog', 'Sari-sari store', 'Shared', 'Crewmmate', '2025-04-16 14:08:56', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(2, 24, 'test case', 'Union Lane wqeqew', 'Amogstore', 'Sussy baka', 'Solo', NULL, '2025-06-05 05:36:31', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'sdsadads asrwrqr'),
(3, 24, 'test case', 'Union Lane wqeqew', 'Amog', 'Sussy baka Store', 'Shared', 'The crewmate?!', '2025-06-08 02:35:54', 'rejected', NULL, 'crewmate ', NULL, NULL, 'admin', '2025-06-08 10:36:35', 0, 'The imposter is sus');

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
(24, 56, 18, '', '', '2025-06-04 10:59:10'),
(27, 59, 18, '', '', '2025-06-04 11:15:36'),
(28, 59, 24, '', '', '2025-06-08 01:56:46'),
(29, 56, 24, '', '', '2025-06-08 01:56:55');

-- --------------------------------------------------------

--
-- Table structure for table `repair_and_construction`
--

CREATE TABLE `repair_and_construction` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_of_request` date NOT NULL DEFAULT curdate(),
  `homeowner_name` varchar(255) NOT NULL,
  `contractor_name` varchar(255) NOT NULL,
  `contractor_contact` varchar(15) NOT NULL,
  `activity_nature` enum('Repairs','Minor Construction','Construction','Demolition') NOT NULL,
  `construction_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected','picked_up') NOT NULL DEFAULT 'pending',
  `approved_by` varchar(255) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `pickup_schedule` datetime DEFAULT NULL,
  `time_approved` datetime DEFAULT NULL,
  `rejected_by` varchar(255) DEFAULT NULL,
  `time_rejected` datetime DEFAULT NULL,
  `print_count` int(11) DEFAULT 0,
  `pickup_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repair_and_construction`
--

INSERT INTO `repair_and_construction` (`id`, `user_id`, `date_of_request`, `homeowner_name`, `contractor_name`, `contractor_contact`, `activity_nature`, `construction_address`, `created_at`, `status`, `approved_by`, `rejection_reason`, `pickup_schedule`, `time_approved`, `rejected_by`, `time_rejected`, `print_count`, `pickup_name`) VALUES
(1, 2, '2025-04-17', 'Fasdasdiko', 'Testing', '234023040042', 'Repairs', 'Amog', '2025-04-15 14:26:23', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(2, 2, '2025-04-16', '', 'Awooga', '4124253235532', 'Repairs', NULL, '2025-04-16 16:12:56', 'rejected', NULL, 'Sad', NULL, NULL, 'admin', '2025-04-24 16:41:52', 0, NULL),
(3, 2, '2025-04-16', 'Jane Doe', 'Awooga asdads', '412425323553223', 'Minor Construction', NULL, '2025-04-16 16:17:29', 'rejected', 'admin', 'Testing Reject', NULL, NULL, 'admin', '2025-04-24 16:34:51', 0, NULL),
(4, 1, '2025-04-19', 'John Doe', 'Testing', '03588357344', 'Demolition', 'Testing Street awooga', '2025-04-19 04:00:53', 'picked_up', 'admin', 'sdads', '2025-04-30 15:10:00', '2025-04-24 16:05:24', NULL, NULL, 2, NULL),
(5, 9, '2025-04-24', 'Test Case_2', 'Awooga Awooga', '92492492024', 'Repairs', 'Union Lane 2323 Lot.423', '2025-04-24 14:42:59', 'rejected', NULL, 'Hindi lahat ay pinapayagan', NULL, NULL, 'admin', '2025-04-24 16:43:39', 0, NULL),
(6, 2, '2025-04-24', 'Jane Doe', 'Awooga', '412425323553223', 'Construction', 'Testing Street', '2025-04-24 16:24:07', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(7, 2, '2025-04-24', 'Jane Doe', 'weqweq', '24224', 'Repairs', 'wqeqew', '2025-04-24 16:28:21', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(8, 2, '2025-04-24', 'Jane Doe', 'weqweq', '24224', 'Repairs', 'wqeqew', '2025-04-24 16:29:46', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(9, 2, '2025-04-24', 'Jane Doe', 'asdsdwe', '4124253235532', 'Minor Construction', 'Testing Street', '2025-04-24 16:38:49', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(10, 2, '2025-04-24', 'Jane Doe', 'asdsdwe', '4124253235532', 'Minor Construction', 'Testing Street2324', '2025-04-24 16:40:17', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(11, 10, '2025-04-24', 'Test Case_3', 'Testing case contractor', '23505824', 'Repairs', 'Test_case address testing cons', '2025-04-24 16:46:17', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(12, 10, '2025-04-24', 'Test Case_3', 'Awooga asdads', '4124253235532', 'Minor Construction', 'Testing Street awoogaas asd', '2025-04-24 16:47:21', 'approved', 'admin', NULL, '2025-06-20 07:23:00', '2025-06-05 16:20:48', NULL, NULL, 5, NULL),
(13, 10, '2025-04-24', 'Test Case_3', 'Testing', '4124253235532', 'Repairs', 'Testing Street awooga removed cont num redundant', '2025-04-24 17:02:47', 'approved', 'admin', NULL, '2025-04-12 16:13:00', '2025-04-25 04:07:33', NULL, NULL, 0, NULL),
(14, 18, '2025-05-28', 'Hoshi Yomi', 'Awooga', '4124253235532', 'Minor Construction', 'Testing Street awoogaaeweq', '2025-05-28 10:27:18', 'approved', NULL, NULL, '2025-06-11 07:48:00', '2025-05-29 15:44:38', NULL, NULL, 0, NULL),
(15, 18, '2025-05-29', 'Hoshi Yomi', 'Awooga', '4124253235532', 'Minor Construction', 'Oflsd Street, 0424 lot 32', '2025-05-29 07:56:24', 'approved', 'admin', NULL, '2025-05-31 19:00:00', '2025-05-29 15:56:39', NULL, NULL, 1, NULL),
(16, 18, '2025-05-29', 'Hoshi Yomi', 'Alien', '230592395823582', 'Construction', 'zib zib zib', '2025-05-29 08:04:11', 'rejected', NULL, 'bla bla bla ble ble ble blu blu blu ablaubulabuuablua', NULL, NULL, 'admin', '2025-05-29 16:04:34', 0, NULL),
(17, 18, '2025-05-30', 'Hoshi Yomi', 'Awooga asdads', '2340230400', 'Minor Construction', 'Oflsd Street, 0424 lot 32', '2025-05-30 10:39:53', 'approved', 'admin', NULL, '2025-06-06 10:44:00', '2025-05-30 18:40:27', NULL, NULL, 1, NULL),
(18, 18, '2025-05-30', 'Hoshi Yomi', 'Awooga asdads', '230592395823582', 'Minor Construction', 'Amog', '2025-05-30 10:42:13', 'rejected', NULL, 'sadads', NULL, NULL, 'admin', '2025-05-30 18:42:27', 0, NULL),
(19, 18, '2025-05-30', 'Hoshi Yomi', 'Awooga asdads', '2340230494', 'Repairs', 'Road 1 Dona Petra Tumana', '2025-05-30 15:31:14', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'YOmi WOir'),
(20, 18, '2025-05-30', 'Hoshi Yomi', 'Wiiwoowiiwoo', '02492492454', 'Repairs', 'Construction wherer!??!?!', '2025-05-30 15:52:00', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'YOmi WOirss'),
(21, 24, '2025-06-05', 'test case', 'Awooga asdads', '23402304', 'Repairs', 'Testing without includes feedback mdoal', '2025-06-05 02:17:51', 'rejected', NULL, 'asd', NULL, NULL, 'admin', '2025-06-05 15:07:14', 0, 'sdsadads asrwrqr'),
(22, 21, '2025-06-05', 'Miyom Mimoy', 'Testing', '03588357344', 'Repairs', 'Oflsd Street, 0424 lot 32', '2025-06-05 04:23:22', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Tst on person pick up'),
(23, 21, '2025-06-05', 'Miyom Mimoy', 'Awooga', '23402304', 'Repairs', 'Road 1 Dona Petra Tumana', '2025-06-05 06:33:07', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, ''),
(24, 21, '2025-06-05', 'Miyom Mimoy', 'asddsa', 'ads', 'Repairs', 'asd', '2025-06-05 06:53:56', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, ''),
(25, 21, '2025-06-05', 'Miyom Mimoy', 'Awooga asdads', '4124253235532', 'Repairs', 'Amog', '2025-06-05 07:01:15', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'N/A'),
(26, 21, '2025-06-05', 'Miyom Mimoy', '1525', '512534', 'Repairs', 'test during ', '2025-06-05 07:02:13', 'rejected', NULL, 'Amog', NULL, NULL, 'admin', '2025-06-05 16:21:03', 0, 'YOmi WOir'),
(27, 24, '2025-06-08', 'test case', 'Awooga asdads', '09247247248', 'Repairs', 'Amog', '2025-06-08 02:22:12', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'YOmi WOir'),
(28, 24, '2025-06-08', 'test case', '24dgd', '01921289573', 'Repairs', 'Road 1 Dona Petra Tumana', '2025-06-08 03:50:09', 'picked_up', 'admin', NULL, '2025-06-09 11:50:00', '2025-06-08 11:50:30', NULL, NULL, 0, 'asddfgdfg');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `venue_type` enum('Court A','Court B') NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `is_big_group` tinyint(1) DEFAULT 0,
  `security_option` tinyint(1) DEFAULT 0,
  `caretaker_option` tinyint(1) DEFAULT 0,
  `power_supply_hours` int(11) DEFAULT 0,
  `total_cost` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` varchar(255) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `time_approved` datetime DEFAULT NULL,
  `rejected_by` varchar(255) DEFAULT NULL,
  `time_rejected` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `venue_type`, `start_time`, `end_time`, `is_big_group`, `security_option`, `caretaker_option`, `power_supply_hours`, `total_cost`, `created_at`, `status`, `approved_by`, `rejection_reason`, `time_approved`, `rejected_by`, `time_rejected`) VALUES
(10, 24, 'Court A', '2025-06-07 12:00:00', '2025-06-07 14:00:00', 0, 0, 0, 0, 200.00, '2025-06-07 03:25:43', 'pending', NULL, NULL, NULL, NULL, NULL),
(11, 24, 'Court A', '2025-06-07 12:00:00', '2025-06-07 14:00:00', 0, 0, 0, 0, 200.00, '2025-06-07 03:28:02', 'pending', NULL, NULL, NULL, NULL, NULL),
(12, 24, 'Court A', '2025-06-07 14:00:00', '2025-06-07 18:00:00', 1, 0, 0, 0, 5400.00, '2025-06-07 03:31:20', 'pending', NULL, NULL, NULL, NULL, NULL),
(13, 24, 'Court A', '2025-06-07 13:00:00', '2025-06-07 14:00:00', 0, 0, 0, 0, 100.00, '2025-06-07 03:33:18', 'rejected', NULL, 'Among Us', NULL, 'admin', '2025-06-07 18:39:23'),
(14, 24, 'Court A', '2025-06-07 14:00:00', '2025-06-07 19:00:00', 1, 1, 1, 0, 7000.00, '2025-06-07 03:36:07', 'approved', 'admin', NULL, '2025-06-07 18:39:11', NULL, NULL),
(15, 24, 'Court A', '2025-06-13 10:00:00', '2025-06-13 18:00:00', 1, 0, 0, 0, 9800.00, '2025-06-07 10:05:54', 'approved', 'admin', NULL, '2025-06-07 18:07:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `street` varchar(255) NOT NULL,
  `lot_block` varchar(100) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `house_number` varchar(50) NOT NULL,
  `date_registered` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) DEFAULT 0,
  `verify_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `first_name`, `middle_name`, `last_name`, `gender`, `phone_number`, `email`, `birthdate`, `street`, `lot_block`, `blood_type`, `house_number`, `date_registered`, `is_verified`, `verify_token`) VALUES
(1, 'user', '$2y$10$Ue1SPKQnFG4ZpI9BEVbfdOm72jxkwZzX8kjMVDSk.p/Vhc6H8Na92', 'John', NULL, 'Doe', 'Male', '09123456789', 'user@gmail.com', '2025-02-01', 'asd', NULL, NULL, 'wqeqew', '2025-02-11 11:17:35', 0, NULL),
(2, 'user2', '$2y$10$Mggs9pMcCTsH.q9tVV44I.Hn8.k5Kgl/LO9os6lBKK.GSe5nBKffO', 'Jane', NULL, 'Doe', 'Female', '091234567892', 'user2@gmail.com', '2025-02-05', 'asd', NULL, NULL, '8', '2025-02-11 11:53:42', 0, NULL),
(3, 'bryan', '$2y$10$IaCL/9OMrYV1.B8iWFQqlOIrlh5YN0LllFfYmrJFG4CL9UVowPZBO', 'bryan', NULL, 'last', 'Male', '09217327039', 'bryan@gmail.com', '2025-02-05', 'asd', NULL, NULL, '32', '2025-02-11 14:32:25', 0, NULL),
(4, 'Laoke', '$2y$10$nUYpUdZjpZbC/v602M0R5OG3HEYPoSNWUKeJiRS3KPMl5SkT2qIDK', 'Bryan', NULL, 'Laoke', 'Male', '09217327039', 'laoke@email.com', '2025-01-29', '32', NULL, NULL, '3434', '2025-02-12 11:33:51', 0, NULL),
(5, 'Bryan.laoke', '$2y$10$VRLPTjmISYVkOet69sBnwONpu9IcQQDepO6mz/yxbNOiomuGwd4Sa', 'Bryan', NULL, 'Laoke', 'Male', '09123456789', 'bryan.laoke@email.com', '2004-06-07', 'Piling Santos', NULL, NULL, '23', '2025-02-12 13:34:34', 0, NULL),
(7, 'Charmaine_ferrer', '$2y$10$U2RgASH843ykGemvLyt6DOvwhfu3tFZZrnZ6F/8fl7J67VEGcZYFa', 'Charmaine', NULL, 'Ferrer', 'Female', '0932234345', 'charmaine_ferrer@gmail.com', '2008-10-03', 'Testing Street', NULL, NULL, '32', '2025-04-23 00:21:11', 0, NULL),
(8, 'Test_Case', '$2y$10$dqHOn1hTcouF7m5dAJ7fAOSAG90T329S9FG2O3yvunp.1Nk1Je2u2', 'Test', NULL, 'Case', 'Female', '02484822323', 'test_case@gmail.com', '2025-03-05', 'Piling Santos', NULL, NULL, 'wqeqew23. Lot.42', '2025-04-23 00:44:05', 0, NULL),
(9, 'Test_Case2', '$2y$10$RKRUx0Cf7veaA5vdyjeH3O2VMgj7bY5sBzWk3e9x0pvYwfJkHgVxK', 'Test', NULL, 'Case_2', 'Female', '092173270392', 'test_case2@gmail.com', '2025-04-19', 'Union Lane', NULL, NULL, '2323 Lot.423', '2025-04-24 09:33:09', 0, NULL),
(10, 'Test_Case3', '$2y$10$fnpwRL0CXiVuagIj7xCYwOG5M8bJWHlg7uGB9GrKC6jcr51XiA.G2', 'Test', NULL, 'Case_3', 'Female', '023232342', 'test_case3@gmail.com', '2025-04-02', 'Starline Drive', NULL, NULL, '2323', '2025-04-24 16:45:29', 0, NULL),
(18, 'hoshiyomii', '', 'Hoshi', 'Users', 'Yomi', 'Male', '09123456789', 'hoshiyomi08@gmail.com', '2004-11-07', 'Twin Peaks Drive', '422424', 'A+', 'qweew', '2025-05-20 10:08:45', 1, NULL),
(21, 'miyomishi', '$2y$10$kV/rTPJpn/6JUfFH6UheQe4Oeoeq63mn4zfhUqzMIqsQHqwNQnSZe', 'Miyom', 'Yomi', 'Mimoy', 'Male', '09123456789', 'mhyrrcs@gmail.com', '2010-11-17', 'Starline Drive', '240, Lots03', 'A-', '2424', '2025-05-26 11:57:57', 1, NULL),
(23, 'Miyoshi', '$2y$10$VTUnOuhIJ4Ipt8wsUoCqOORpaQInIozCACGM32uEKoY0dy6HAFPR.', 'Bryan', 'James', 'Laoke', 'Male', '0912345674560', 'chimikunhd@gmail.com', '2008-02-28', 'Twin Peaks Drive', '422424', 'A+', '242', '2025-06-04 12:20:04', 1, NULL),
(24, 'testcase', '$2y$10$S.hajHBo0YcpK0U7sr5w6ez0SGgDrQWeG8GNpGmJPuIj1XE.7H2bu', 'test', 'test', 'case', 'Male', '09123456789', 'gecitot313@jeanssi.com', '2010-02-02', 'Union Lane', '422424', 'A+', 'wqeqew', '2025-06-05 01:37:49', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_audit_logs`
--

CREATE TABLE `user_audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action_type` varchar(64) NOT NULL,
  `action_details` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_audit_logs`
--

INSERT INTO `user_audit_logs` (`id`, `user_id`, `action_type`, `action_details`, `created_at`) VALUES
(4, 21, 'profile_update', 'Changed fields: first_name', '2025-05-26 21:11:40'),
(5, 18, 'profile_update', 'Changed fields: birthdate', '2025-06-05 11:14:25'),
(6, 18, 'profile_update', 'Changed fields: ', '2025-06-05 12:01:44'),
(7, 18, 'profile_update', 'Changed fields: ', '2025-06-05 12:01:59'),
(8, 18, 'profile_update', 'Changed fields: ', '2025-06-05 12:02:44'),
(9, 21, 'profile_update', 'Changed fields: ', '2025-06-05 12:06:31'),
(10, 21, 'profile_update', 'Changed fields: password', '2025-06-05 12:10:04'),
(11, 21, 'profile_update', 'Changed fields: username', '2025-06-05 12:10:19'),
(12, 21, 'profile_update', 'Changed fields: first_name', '2025-06-05 12:10:27'),
(13, 21, 'profile_update', 'Changed fields: first_name, middle_name', '2025-06-05 12:10:35'),
(14, 21, 'profile_update', 'Changed fields: birthdate', '2025-06-05 14:32:43'),
(15, 21, 'profile_update', 'Changed fields: birthdate', '2025-06-05 14:57:16'),
(16, 21, 'profile_update', 'Changed fields: birthdate', '2025-06-05 15:01:53');

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
  `other_utility_type` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected','picked_up') NOT NULL DEFAULT 'pending',
  `approved_by` varchar(255) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `pickup_schedule` datetime DEFAULT NULL,
  `time_approved` datetime DEFAULT NULL,
  `rejected_by` varchar(255) DEFAULT NULL,
  `time_rejected` datetime DEFAULT NULL,
  `print_count` int(11) DEFAULT 0,
  `pickup_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `work_permit_utilities`
--

INSERT INTO `work_permit_utilities` (`id`, `user_id`, `date_of_request`, `date_of_work`, `contact_no`, `address`, `service_provider`, `other_service_provider`, `nature_of_work`, `created_at`, `utility_type`, `other_utility_type`, `status`, `approved_by`, `rejection_reason`, `pickup_schedule`, `time_approved`, `rejected_by`, `time_rejected`, `print_count`, `pickup_name`) VALUES
(1, 2, '2025-04-16', '2025-04-10', '123123123', 'asd 8', 'Others', 'Test2', 'Repair/Maintenance', '2025-04-16 10:56:11', 'Others', 'Test', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(2, 1, '2025-04-19', '2025-05-03', '2509824892480', 'asd wqeqew', 'Globe', NULL, 'Repair/Maintenance', '2025-04-19 06:57:26', 'Internet', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(3, 9, '2025-04-24', '2025-05-10', '024848248', 'Union Lane 2323 Lot.423', 'CIGNAL', NULL, 'Permanent Disconnection', '2025-04-24 10:19:22', 'Internet', NULL, 'approved', 'admin', NULL, '2025-04-24 00:15:00', '2025-04-24 16:13:12', NULL, NULL, 0, NULL),
(4, 2, '2025-04-24', '2025-05-07', '2509824892480', 'asd 8', 'Meralco', NULL, 'New installation', '2025-04-24 16:42:04', 'Water', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(5, 2, '2025-04-24', '2025-05-07', '2509824892480', 'asd 8', 'Meralco', NULL, 'New installation', '2025-04-24 16:43:12', 'Water', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(6, 10, '2025-04-24', '2025-05-09', '2509824892480', 'Starline Drive 2323', 'CIGNAL', NULL, 'Reconnection', '2025-04-24 16:46:56', 'Internet', NULL, 'approved', 'admin', NULL, '2025-04-15 00:10:00', '2025-04-25 04:08:22', NULL, NULL, 0, NULL),
(7, 10, '2025-04-24', '2025-05-02', '2509824892480', 'Starline Drive 2323', 'Meralco', NULL, 'New installation', '2025-04-24 16:53:59', 'Water', NULL, 'rejected', NULL, 'Testing', NULL, NULL, 'admin', '2025-04-25 04:08:09', 0, NULL),
(8, 2, '2025-04-25', '2025-05-01', '2509824892480', 'asd 8', 'Meralco', NULL, 'New installation', '2025-04-25 03:18:55', 'Water', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(9, 18, '2025-05-29', '2025-06-28', '2509824892480', 'Twin Peaks Drive qweew', 'Others', 'Others Ert ert ', 'Repair/Maintenance', '2025-05-29 13:25:03', 'Others', 'Otherssadasdaiods', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(10, 21, '2025-06-05', '2025-06-20', '2509824892480', 'Amogus', 'Meralco', NULL, 'Repair/Maintenance', '2025-06-05 04:24:22', 'Water', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Tst on person pick up'),
(11, 24, '2025-06-08', '2025-06-16', '09124274128', 'qweqwdddfdgdfgfgdfgdfgfgfgfgfg', 'Meralco', NULL, 'New installation', '2025-06-08 02:29:14', 'Water', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'asd');

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
-- Indexes for table `barangay_officials`
--
ALTER TABLE `barangay_officials`
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
-- Indexes for table `facilities_reservations`
--
ALTER TABLE `facilities_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
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
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
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
-- Indexes for table `user_audit_logs`
--
ALTER TABLE `user_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `barangay_officials`
--
ALTER TABLE `barangay_officials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificate_of_indigency`
--
ALTER TABLE `certificate_of_indigency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `certificate_of_residency`
--
ALTER TABLE `certificate_of_residency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `clearance_major_construction`
--
ALTER TABLE `clearance_major_construction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `document_requests`
--
ALTER TABLE `document_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `facilities_reservations`
--
ALTER TABLE `facilities_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `new_business_permit`
--
ALTER TABLE `new_business_permit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `repair_and_construction`
--
ALTER TABLE `repair_and_construction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_audit_logs`
--
ALTER TABLE `user_audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `work_permit_utilities`
--
ALTER TABLE `work_permit_utilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
-- Constraints for table `facilities_reservations`
--
ALTER TABLE `facilities_reservations`
  ADD CONSTRAINT `facilities_reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_audit_logs`
--
ALTER TABLE `user_audit_logs`
  ADD CONSTRAINT `user_audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `work_permit_utilities`
--
ALTER TABLE `work_permit_utilities`
  ADD CONSTRAINT `work_permit_utilities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
