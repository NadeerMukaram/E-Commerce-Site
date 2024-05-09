-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2024 at 09:25 AM
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
-- Database: `shop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `password`) VALUES
(1, 'admin', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2'),
(2, 'nzro', '7b52009b64fd0a2a49e6d8a939753077792b0554');

-- --------------------------------------------------------

--
-- Table structure for table `cancelled_orders`
--

CREATE TABLE `cancelled_orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `total_products` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `placed_on` date DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `order_received` date DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_details` text DEFAULT NULL,
  `order_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `address_2` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cancelled_orders`
--

INSERT INTO `cancelled_orders` (`id`, `user_id`, `name`, `number`, `email`, `method`, `address`, `total_products`, `total_price`, `placed_on`, `payment_status`, `order_id`, `order_received`, `product_name`, `product_details`, `order_timestamp`, `address_2`) VALUES
(186, 12, 'Nadeer Mukaram', '0919577816', 'mukaram@gmail.com', 'Cash on Delivery', 'Flat No. Hanna Drive, Near Marine Station, Zamboanga City, Philippines 7000', 1, 6.00, '2024-04-15', 'cancelled', 67477, NULL, 'das', '234eefew', '0000-00-00 00:00:00', ''),
(188, 12, 'Nadeer Mukaram', '0919577816', 'mukaram@gmail.com', 'Cash on Delivery', 'Flat No. Hanna Drive, Near Marine Station, Zamboanga City, Philippines 7000', 1, 6.00, '2024-04-15', 'cancelled', 76981, NULL, 'das', '234eefew', '0000-00-00 00:00:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `quantity` int(10) NOT NULL,
  `image` varchar(100) NOT NULL,
  `details` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `pid`, `name`, `price`, `quantity`, `image`, `details`) VALUES
(208, 10, 3, 'nzro', 12, 1, '309192716-2b7b1825-b2b2-42b6-a5af-5c517af5ef29.jpg', 'sads');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `message` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `number` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `total_products` varchar(1000) NOT NULL,
  `total_price` int(100) NOT NULL,
  `placed_on` date NOT NULL DEFAULT current_timestamp(),
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  `order_id` varchar(5) DEFAULT NULL,
  `order_received` varchar(50) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_details` varchar(1000) DEFAULT NULL,
  `order_timestamp` varchar(50) DEFAULT NULL,
  `address_2` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `number`, `email`, `method`, `address`, `total_products`, `total_price`, `placed_on`, `payment_status`, `order_id`, `order_received`, `product_name`, `product_details`, `order_timestamp`, `address_2`) VALUES
(187, 12, 'Nadeer Mukaram', '0919577816', 'mukaram@gmail.com', 'Cash on Delivery', 'Flat No. Hanna Drive, Near Marine Station, Zamboanga City, Philippines 7000', '1', 6, '2024-04-15', 'completed', '24392', NULL, 'das', '234eefew', '03:07 PM', ''),
(189, 12, 'Nadeer Mukaram', '0919577816', 'mukaram@gmail.com', 'Cash on Delivery', 'Flat No. Hanna Drive, Near Marine Station, Zamboanga City, Philippines 7000', '1', 6, '2024-04-15', 'pending', '36768', NULL, 'das', '234eefew', '03:21 PM', '');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `details` varchar(500) NOT NULL,
  `price` int(10) NOT NULL,
  `image_01` varchar(100) NOT NULL,
  `image_02` varchar(100) NOT NULL,
  `image_03` varchar(100) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `product_size` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `details`, `price`, `image_01`, `image_02`, `image_03`, `quantity`, `category`, `product_size`) VALUES
(3, 'nzro', 'sads', 12, '309192716-2b7b1825-b2b2-42b6-a5af-5c517af5ef29.jpg', '432668251_944398680552838_8046220023492653746_n.jpg', 'a.jpg', 434, 'Smartphone', ''),
(4, 'wewddf', 'wee', 12, '432668251_944398680552838_8046220023492653746_n.jpg', '309192716-2b7b1825-b2b2-42b6-a5af-5c517af5ef29.jpg', '432668251_944398680552838_8046220023492653746_n.jpg', 0, 'Shirt', 'L'),
(5, 'das', '234eefew', 6, 'Screenshot_20231130-012014_Marker-Based AR Flash Cards.jpg', '400220233_313996958224658_6407138245799166849_n.jpg', 'df4nzr3-3ad1d9ff-f2ce-40fc-8048-bd0927cd9543.jpg', 112, 'Watch', NULL),
(6, 'ew', 'awe', 123, 'Screenshot 2024-04-05 014239.png', 'Screenshot 2024-04-05 004032.png', 'Screenshot 2024-04-05 014217.png', 211, 'Watch', NULL),
(7, 'Nzro Shirttdsadasd', 'Best Shirt', 5003, '309192716-2b7b1825-b2b2-42b6-a5af-5c517af5ef29.jpg', 'a.jpg', 'random__104_by_nadzero_deq61pq-fullview.jpg', 499, 'Shirt', 'XL'),
(8, 'Lops', 'Mid Shirt', 20, '280516353-962652f9-0c2b-4960-a13f-992330ddba98.jpg', 'Entamoeba Figure 1.jpg', 'froo.png', 47, 'Shirt', 'S'),
(9, 'yoyoyo', 'ew', 22, '309192716-2b7b1825-b2b2-42b6-a5af-5c517af5ef29.jpg', 'a.jpg', 'abraham-aliya2.jpg', 44, 'Pants', 'XS'),
(10, 'w21weeeweewe', 'we', 123, 'Main.webp', 'Main.webp', 'Main.webp', 123, 'Shirt', 'XS'),
(11, 'nzroasdsadas', '123', 12, 'Main.webp', 'Main.webp', 'Main.webp', 11, 'Mouse', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `number` varchar(50) DEFAULT NULL,
  `number_validation_status` varchar(50) DEFAULT NULL,
  `drive` varchar(255) DEFAULT NULL,
  `landmarks` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `number`, `number_validation_status`, `drive`, `landmarks`, `city`, `country`, `zip_code`, `fullname`) VALUES
(12, 'nzro', 'mukaram@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '09195778166', NULL, 'Hanna Drive', 'Near Marine Station', 'Zamboanga City', 'Philippines', '7000', 'Nadeer Mukaram');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(100) NOT NULL,
  `image` varchar(100) NOT NULL,
  `details` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cancelled_orders`
--
ALTER TABLE `cancelled_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cancelled_orders`
--
ALTER TABLE `cancelled_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=189;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=215;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=190;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
