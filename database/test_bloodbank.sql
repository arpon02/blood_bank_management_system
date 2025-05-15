-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2025 at 11:01 PM
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
-- Database: `test_bloodbank`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_list`
--

CREATE TABLE `admin_list` (
  `name` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_list`
--

INSERT INTO `admin_list` (`name`, `password`) VALUES
('', ''),
('admin1', 'admin1'),
('admin2', 'admin2');

-- --------------------------------------------------------

--
-- Table structure for table `blood_stock`
--

CREATE TABLE `blood_stock` (
  `id` int(11) NOT NULL,
  `blood_grp` varchar(5) NOT NULL,
  `rh` varchar(5) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blood_stock`
--

INSERT INTO `blood_stock` (`id`, `blood_grp`, `rh`, `qty`) VALUES
(9, 'A', '-', 0),
(10, 'B', '-', 0),
(11, 'O', '+', 2),
(12, 'AB', '-', 1),
(13, 'AB', '+', 1);

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `record_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role` varchar(10) DEFAULT NULL,
  `status` varchar(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `receive_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`record_id`, `user_id`, `role`, `status`, `date`, `receiver_id`, `receive_date`) VALUES
(33, 5, 'donation', 'unavailable', '2025-05-14', 1, '2025-05-14'),
(34, 7, 'donation', 'unavailable', '2025-05-14', 4, '2025-05-14'),
(35, 6, 'donation', 'available', '2025-05-14', NULL, NULL),
(36, 20, 'donation', 'unavailable', '2025-05-14', 13, '2025-05-14'),
(37, 4, 'donation', 'available', '2025-05-14', NULL, NULL),
(38, 8, 'donation', 'available', '2025-05-14', NULL, NULL),
(39, 6, 'donation', 'available', '2025-05-14', NULL, NULL),
(40, 1, 'donation', 'available', '2025-05-12', NULL, NULL),
(41, 3, 'donation', 'unavailable', '2025-05-10', 8, '2025-05-10'),
(42, 9, 'donation', 'available', '2025-05-08', NULL, NULL),
(43, 12, 'donation', 'unavailable', '2025-04-15', 15, '2025-04-15'),
(44, 15, 'donation', 'available', '2025-04-20', NULL, NULL),
(45, 18, 'donation', 'unavailable', '2025-04-25', 2, '2025-04-25'),
(46, 2, 'donation', 'available', '2025-04-30', NULL, NULL),
(47, 11, 'donation', 'unavailable', '2025-05-02', 7, '2025-05-02'),
(48, 14, 'donation', 'available', '2025-05-05', NULL, NULL),
(49, 16, 'donation', 'unavailable', '2025-03-20', 10, '2025-03-20'),
(50, 19, 'donation', 'available', '2025-03-25', NULL, NULL),
(51, 4, 'donation', 'unavailable', '2025-04-01', 6, '2025-04-01'),
(52, 7, 'donation', 'available', '2025-04-05', NULL, NULL),
(53, 10, 'donation', 'unavailable', '2025-04-10', 3, '2025-04-10'),
(54, 13, 'donation', 'available', '2025-04-12', NULL, NULL),
(55, 17, 'donation', 'unavailable', '2025-05-01', 9, '2025-05-01'),
(56, 20, 'donation', 'available', '2025-05-03', NULL, NULL),
(57, 5, 'donation', 'unavailable', '2025-05-07', 11, '2025-05-07'),
(58, 8, 'donation', 'available', '2025-05-09', NULL, NULL),
(59, 6, 'donation', 'unavailable', '2025-05-11', 14, '2025-05-11'),
(60, 1, 'donation', 'available', '2025-05-14', NULL, NULL),
(61, 9, 'donation', 'available', '2025-05-14', NULL, NULL),
(62, 16, 'donation', 'available', '2025-05-14', NULL, NULL),
(63, 3, 'donation', 'available', '2025-05-14', NULL, NULL),
(64, 14, 'donation', 'available', '2025-05-14', NULL, NULL),
(65, 18, 'donation', 'available', '2025-05-14', NULL, NULL),
(66, 2, 'donation', 'available', '2025-05-14', NULL, NULL),
(67, 15, 'donation', 'available', '2025-05-14', NULL, NULL),
(73, 1, 'donation', 'available', '2025-05-13', NULL, NULL),
(74, 3, 'donation', 'available', '2025-05-13', NULL, NULL),
(75, 2, 'donation', 'available', '2025-05-13', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `request_blood`
--

CREATE TABLE `request_blood` (
  `request_id` int(11) NOT NULL,
  `blood_group` varchar(3) NOT NULL,
  `rh_factor` varchar(5) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `request_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request_blood`
--

INSERT INTO `request_blood` (`request_id`, `blood_group`, `rh_factor`, `name`, `phone`, `request_date`) VALUES
(1, 'A', '+', 'Abdul Karim', '01712345678', '2025-05-14'),
(2, 'O', '-', 'Nasima Akter', '01819876543', '2025-05-14'),
(3, 'B', '+', 'Mohammed Hossain', '01917654321', '2025-05-14'),
(4, 'AB', '+', 'Farida Begum', '01558901234', '2025-05-14'),
(5, 'A', '-', 'Rafiqul Islam', '01612345678', '2025-05-14'),
(6, 'B', '+', 'Shahnaz Parvin', '01789012345', '2025-05-14'),
(7, 'O', '+', 'Kamal Uddin', '01911122334', '2025-05-14'),
(8, 'AB', '-', 'Jasmine Sultana', '01845678901', '2025-05-14'),
(9, 'A', '+', 'Mizanur Rahman', '01756789012', '2025-05-14'),
(10, 'B', '-', 'Tahmina Khatun', '01634567890', '2025-05-14'),
(11, 'O', '+', 'Zahirul Alam', '01923456789', '2025-05-13'),
(12, 'A', '+', 'Rashida Chowdhury', '01567890123', '2025-05-13'),
(13, 'B', '+', 'Anwar Hossain', '01789654321', '2025-05-13'),
(14, 'AB', '+', 'Salma Begum', '01912876543', '2025-05-13'),
(15, 'O', '-', 'Masud Khan', '01551234567', '2025-05-13'),
(16, 'A', '+', 'Nahid Hassan', '01715432109', '2025-05-14'),
(17, 'B', '-', 'Shabnam Faria', '01891234567', '2025-05-14'),
(18, 'O', '+', 'Imran Ahmed', '01677889900', '2025-05-14'),
(19, 'AB', '-', 'Romana Rashid', '01919876543', '2025-05-14'),
(20, 'A', '+', 'Kamrul Hasan', '01558899001', '2025-05-14');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `blood_group` varchar(3) NOT NULL,
  `rh_factor` varchar(5) NOT NULL,
  `is_emergency_donor` varchar(3) DEFAULT NULL,
  `last_donated` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `blood_group`, `rh_factor`, `is_emergency_donor`, `last_donated`, `address`, `phone`, `email`, `password`) VALUES
(1, 'Alice Johnson', 'A', '+', 'yes', '2025-05-03', '123 Maple Street, Springfield', '01952648594', 'alice.johnson@example.com', '123'),
(2, 'Bob Smith', 'O', '-', 'yes', '2025-05-04', '456 Oak Avenue, Rivertown', '016425489615', 'bob.smith@example.com', '456'),
(3, 'Clara Davis', 'B', '+', 'yes', NULL, '789 Pine Lane, Lakeview', '01254569858', 'clara.davis@example.com', '@g123'),
(4, 'David Wilson', 'AB', '-', 'no', '2025-05-14', '321 Birch Blvd, Hillcrest', '0195621458', 'david.wilson@example.com', '123'),
(5, 'Ella Thompson', 'A', '-', 'yes', '2025-05-14', '654 Cedar Road, Brookfield', '01845297852', 'ella.thompson@example.com', '123'),
(6, 'Frank Martinez', 'O', '+', NULL, '2025-05-14', '987 Elm Street, Grandville', '01845965235', 'frank.martinez@example.com', '987525'),
(7, 'Grace Lee', 'B', '-', NULL, '2025-05-14', '159 Spruce Drive, Meadowvale', '01478536985', 'grace.lee@example.com', 'hsr#122'),
(8, 'Henry Brown', 'AB', '+', NULL, '2025-05-14', '753 Aspen Court, Clearview', '01785236985', 'henry.brown@example.com', '123'),
(9, 'Rahul Islam', 'A', '+', 'yes', '2025-05-01', '42/3 Dhanmondi, Dhaka', '01711234567', 'rahul.islam@example.com', 'dh123'),
(10, 'Farida Rahman', 'B', '-', 'yes', '2025-05-10', '15/A Gulshan-2, Dhaka', '01845678901', 'farida.r@example.com', 'fr456'),
(11, 'Kamal Hassan', 'O', '+', 'yes', NULL, '78 Banani DOHS, Dhaka', '01912345678', 'kamal.h@example.com', 'kh789'),
(12, 'Nusrat Jahan', 'AB', '+', 'yes', '2025-05-12', '25 Uttara Sector-4, Dhaka', '01556789012', 'nusrat.j@example.com', 'nj321'),
(13, 'Ashraf Ali', 'A', '-', 'no', '2025-05-07', '92/4 Mohakhali DOHS, Dhaka', '01789012345', 'ashraf.a@example.com', 'aa567'),
(14, 'Sabrina Khan', 'B', '+', 'yes', NULL, '63 Bashundhara R/A, Dhaka', '01534567890', 'sabrina.k@example.com', 'sk890'),
(15, 'Mohammad Hasan', 'O', '-', 'yes', '2025-05-13', '112 Mirpur DOHS, Dhaka', '01890123456', 'm.hasan@example.com', 'mh432'),
(16, 'Tahmina Akter', 'A', '+', 'no', NULL, '45/2 Niketon, Dhaka', '01767890123', 'tahmina.a@example.com', 'ta765'),
(17, 'Zafar Ahmed', 'AB', '-', 'yes', '2025-05-11', '33 Baridhara, Dhaka', '01923456789', 'zafar.ahmed@example.com', 'za999'),
(18, 'Lubna Haque', 'B', '+', 'yes', '2025-05-09', '88 Cantonment, Dhaka', '01678901234', 'lubna.h@example.com', 'lh111'),
(19, 'Imran Rashid', 'O', '+', 'no', NULL, '156 Tejgaon, Dhaka', '01512345678', 'imran.r@example.com', 'ir222'),
(20, 'Nasreen Begum', 'A', '-', 'yes', '2025-05-14', '27 Khilgaon, Dhaka', '01899999999', 'nasreen.b@example.com', 'nb333');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blood_stock`
--
ALTER TABLE `blood_stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `records`
--
ALTER TABLE `records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_receiver` (`receiver_id`);

--
-- Indexes for table `request_blood`
--
ALTER TABLE `request_blood`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blood_stock`
--
ALTER TABLE `blood_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `records`
--
ALTER TABLE `records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `request_blood`
--
ALTER TABLE `request_blood`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `records`
--
ALTER TABLE `records`
  ADD CONSTRAINT `fk_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
