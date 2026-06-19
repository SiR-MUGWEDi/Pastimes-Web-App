-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2026 at 10:30 PM
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
-- Database: `clothingstore`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblclothes`
--

CREATE TABLE `tblclothes` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `image_path` varchar(255) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'available',
  `condition` enum('new','excellent','good','fair') DEFAULT 'good'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblclothes`
--

INSERT INTO `tblclothes` (`item_id`, `item_name`, `brand`, `description`, `price`, `quantity`, `image_path`, `seller_id`, `status`, `condition`) VALUES
(1, 'Levi\'s 501 Jeans', 'Levi\'s', 'Classic blue jeans, excellent condition', 350.00, 0, 'images/item1.jpg', NULL, 'available', 'good'),
(2, 'Nike Air Max', 'Nike', 'Size 42, worn twice', 800.00, 1, 'images/item2.jpg', NULL, 'available', 'good'),
(3, 'Zara Blazer', 'Zara', 'Black formal blazer, size M', 450.00, 1, 'images/item3.jpg', NULL, 'available', 'good'),
(4, 'Adidas Hoodie', 'Adidas', 'Grey pullover hoodie, size L', 300.00, -6, 'images/item4.jpg', NULL, 'available', 'good'),
(5, 'Polo Ralph Lauren Shirt', 'Polo', 'Blue striped shirt, size S', 250.00, -5, 'images/item5.jpg', NULL, 'available', 'good'),
(21, 'Nike shoe', 'Nike', 'size 5, good condition', 450.00, -3, 'images/OIP.webp', 18, 'available', 'good'),
(22, 'H&M BLack blazer', 'H&M', 'good quality, Size-M, Good condition', 300.00, 0, 'images/item_1781810582_725.webp', 1, 'pending', 'excellent'),
(23, 'H&M white blazer', 'H&M', 'Size-M', 300.00, -1, 'images/item_1781810940_988.webp', 1, 'available', 'good');

-- --------------------------------------------------------

--
-- Table structure for table `tblorder`
--

CREATE TABLE `tblorder` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(20) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblorder`
--

INSERT INTO `tblorder` (`order_id`, `user_id`, `order_number`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 18, 'ORD-1781809286-814', 1050.00, 'pending', '2026-06-18 19:01:26', '2026-06-18 19:01:26'),
(2, 18, 'ORD-1781809657-871', 1600.00, 'pending', '2026-06-18 19:07:37', '2026-06-18 19:07:37'),
(3, 18, 'ORD-1781809859-153', 750.00, 'pending', '2026-06-18 19:10:59', '2026-06-18 19:10:59'),
(4, 1, 'ORD-1781810307-378', 1250.00, 'pending', '2026-06-18 19:18:27', '2026-06-18 19:18:27'),
(5, 1, 'ORD-1781810849-265', 800.00, 'pending', '2026-06-18 19:27:29', '2026-06-18 19:27:29'),
(6, 19, 'ORD-1781811106-108', 600.00, 'pending', '2026-06-18 19:31:46', '2026-06-18 19:31:46');

-- --------------------------------------------------------

--
-- Table structure for table `tblorderitem`
--

CREATE TABLE `tblorderitem` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_time` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblorderitem`
--

INSERT INTO `tblorderitem` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price_at_time`, `subtotal`) VALUES
(1, 1, 4, 2, 300.00, 600.00),
(2, 1, 21, 1, 450.00, 450.00),
(3, 2, 1, 1, 350.00, 350.00),
(4, 2, 4, 1, 300.00, 300.00),
(5, 2, 5, 2, 250.00, 500.00),
(6, 2, 21, 1, 450.00, 450.00),
(7, 3, 4, 1, 300.00, 300.00),
(8, 3, 21, 1, 450.00, 450.00),
(9, 4, 4, 1, 300.00, 300.00),
(10, 4, 5, 2, 250.00, 500.00),
(11, 4, 21, 1, 450.00, 450.00),
(12, 5, 4, 1, 300.00, 300.00),
(13, 5, 5, 2, 250.00, 500.00),
(14, 6, 4, 1, 300.00, 300.00),
(15, 6, 23, 1, 300.00, 300.00);

-- --------------------------------------------------------

--
-- Table structure for table `tblorders`
--

CREATE TABLE `tblorders` (
  `order_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblorders`
--

INSERT INTO `tblorders` (`order_id`, `user_id`, `total`, `order_date`) VALUES
(NULL, 12, 1100.00, '2026-05-04 19:04:46'),
(NULL, 7, 450.00, '2026-05-04 19:15:44'),
(NULL, 17, 1100.00, '2026-06-18 08:29:08');

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_verified` tinyint(4) DEFAULT 0,
  `is_admin` tinyint(4) DEFAULT 0,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`user_id`, `full_name`, `email`, `username`, `password_hash`, `is_verified`, `is_admin`, `registration_date`) VALUES
(1, 'Administrator', 'admin@pastimes.com', 'admin', '$2y$10$IlJqQOtHFkUp4b7ZkcbEBOTWztImPXuf/l1iKdGPy4KW7NbgnKPVe', 1, 1, '2026-05-03 16:29:33'),
(16, 'TT', 'thabo@example.com', 'Tet', '009892bd28c78935e2b7bfe87bd2635894f900754a449a611c7ecb07138f424d', 1, 0, '2026-06-18 08:21:45'),
(18, 'Vee', 'V@example.com', 'VV', '$2y$10$0tzF8bH8XEfUIngpUOyQSeVmn/ruHVG8GeS.S9npAjjrJYnr0Q.US', 1, 0, '2026-06-18 18:18:39'),
(19, 'momo', 'mo@example.com', 'mo', '$2y$10$1QNUc/J/L2tuqSesIu.KoOHhKfXAjKJJnGgZCsJDR22GTc3Seepj6', 1, 0, '2026-06-18 19:16:33'),
(20, 'VM', 'Vee@example.com', 'VEE', '$2y$10$TYtJC.udOejPWzlFZ.mtRO1CMDJad5HhW0ImrdTeQx0wQUwniOFt6', 1, 0, '2026-06-18 19:25:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblclothes`
--
ALTER TABLE `tblclothes`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `tblorder`
--
ALTER TABLE `tblorder`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tblorderitem`
--
ALTER TABLE `tblorderitem`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblclothes`
--
ALTER TABLE `tblclothes`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tblorder`
--
ALTER TABLE `tblorder`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblorderitem`
--
ALTER TABLE `tblorderitem`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblclothes`
--
ALTER TABLE `tblclothes`
  ADD CONSTRAINT `tblclothes_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `tbluser` (`user_id`);

--
-- Constraints for table `tblorder`
--
ALTER TABLE `tblorder`
  ADD CONSTRAINT `tblorder_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbluser` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tblorderitem`
--
ALTER TABLE `tblorderitem`
  ADD CONSTRAINT `tblorderitem_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `tblorder` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblorderitem_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tblclothes` (`item_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
