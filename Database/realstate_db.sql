-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2025 at 08:38 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `realstate_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `property_id`) VALUES
(3, 4, 1),
(6, 7, 2),
(7, 7, 3);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_text` text DEFAULT NULL,
  `sent_datetime` datetime DEFAULT current_timestamp(),
  `reply_text` text DEFAULT NULL,
  `reply_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message_text`, `sent_datetime`, `reply_text`, `reply_datetime`) VALUES
(1, 4, 3, 'Hy', '2024-05-26 08:40:22', 'Yes ', '2024-05-26 08:41:26'),
(2, 4, 3, 'i want to purchase somethings ', '2024-05-26 08:41:44', 'what', '2024-05-26 08:41:56'),
(3, 4, 3, 'He', '2024-05-26 09:27:25', 'Yes', '2024-05-26 09:27:39'),
(4, 7, 6, 'Hy', '2025-03-11 11:21:15', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `payment_status` enum('Succeeded','Failed') NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `buyer_id`, `property_id`, `amount`, `transaction_id`, `payment_status`, `payment_date`) VALUES
(1, 7, 3, 4000.00, 'ch_3R1MosRrUKhdzOsD1atTgT8H', 'Succeeded', '2025-03-11 07:01:43');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `property_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `property_type` enum('rent','sale') NOT NULL,
  `amenities` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `bedrooms` int(11) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `status` enum('Active','Sold') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `user_id`, `title`, `description`, `location`, `price`, `property_type`, `amenities`, `image`, `bedrooms`, `latitude`, `longitude`, `status`) VALUES
(1, 3, 'Home For Sale', 'This Home is for Sale', 'London', 4999.00, 'sale', 'dafd', 'uploads/images.jpg', 0, 0.00000000, 0.00000000, 'Active'),
(2, 6, 'Karachi Property', 'ds', 'Karachi Korangi', 340000.00, 'sale', 'sddds', 'uploads/1.png', 2, 24.83870000, 67.12090000, 'Sold'),
(3, 6, 'Lahore Property', 'dfa', 'lahore', 4000.00, 'rent', 'dfd', 'uploads/CS201p2.png', 3, 31.52040000, 74.35870000, 'Sold');

-- --------------------------------------------------------

--
-- Table structure for table `reportproperty`
--

CREATE TABLE `reportproperty` (
  `report_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `report_reason` text NOT NULL,
  `report_datetime` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reportproperty`
--

INSERT INTO `reportproperty` (`report_id`, `property_id`, `user_id`, `report_reason`, `report_datetime`) VALUES
(1, 1, 4, 'fds', '2024-05-26 09:16:31'),
(2, 2, 7, 'dsds', '2025-03-11 11:20:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `expertise` varchar(255) DEFAULT NULL,
  `user_type` varchar(50) DEFAULT NULL,
  `status` enum('Pending','Approved') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `phone`, `age`, `bio`, `expertise`, `user_type`, `status`) VALUES
(3, 'seller', '$2y$10$S68SJhc8dr.Nl5JplyC3JOY2GAzEQmHVnYdSAeVxubqk0HMLk2EUK', 'seller@gmail.com', '4512', 23, '0', 'none', 'Seller-Agent', 'Pending'),
(4, 'buyer', '$2y$10$db5YDwvzT0vsA07WC/YLTeWlERUeFJ3rJHWhmPuQ0zqpm19jbhU96', 'buyer@gmail.com', '23221', 23, NULL, NULL, 'Buyer', 'Pending'),
(6, 'testagent', '$2y$10$3fzrpKNVCDZnCp8IyjYtPOl4ECJ2ptnZ8ffXgQ3CzJmSAsRpK2V7q', 'testagent@gmail.com', '03266029050', 23, NULL, NULL, 'Seller-Agent', 'Approved'),
(7, 'testbuyer', '$2y$10$T0Jk6O4ib61C1q7P/9pdbuvj0xLpZWi33PwUchS7LbIEbW3w5f58m', 'testbuyer@gmail.com', '03266029051', 23, NULL, NULL, 'Buyer', 'Approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`property_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reportproperty`
--
ALTER TABLE `reportproperty`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reportproperty`
--
ALTER TABLE `reportproperty`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`);

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reportproperty`
--
ALTER TABLE `reportproperty`
  ADD CONSTRAINT `reportproperty_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`),
  ADD CONSTRAINT `reportproperty_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
