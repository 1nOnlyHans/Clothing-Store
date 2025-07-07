-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 07, 2025 at 12:59 PM
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
-- Database: `clothing_store_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `category_description`) VALUES
(1, 'Tops', 'From casual basics to trendy statement pieces, our Tops collection has everything you need to mix, match, and layer for any season.'),
(2, 'Bottoms', 'Find your perfect fit with our range of stylish bottoms — from comfy jeans to versatile trousers, all designed to keep you looking effortlessly cool.'),
(3, 'Jackets', 'Stay warm and stylish all year round with our Jackets collection — layering pieces that add personality and comfort to any outfit.');

-- --------------------------------------------------------

--
-- Table structure for table `deliver_message`
--

CREATE TABLE `deliver_message` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deliver_message`
--

INSERT INTO `deliver_message` (`id`, `user_id`, `order_id`, `message`, `created_at`) VALUES
(8, 17, 11, 'Your order is on the way', '2025-07-07 17:16:33');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('Gcash','COD','','') NOT NULL,
  `payment_status` enum('Pending','Paid','Failed') NOT NULL DEFAULT 'Pending',
  `order_status` enum('Processing','To Ship','Delivered','Cancelled') NOT NULL DEFAULT 'Processing',
  `gcash_number` varchar(25) NOT NULL,
  `shipping_address` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `total_amount`, `payment_method`, `payment_status`, `order_status`, `gcash_number`, `shipping_address`, `created_at`, `updated_at`) VALUES
(11, 17, 'ORD-20250707-97999', 46100.00, 'COD', 'Pending', 'Delivered', '', 'Area 51', '2025-07-07 17:15:07', '2025-07-07 17:17:24'),
(12, 17, 'ORD-20250707-35269', 500.00, 'COD', 'Pending', 'Cancelled', '', 'Area 51', '2025-07-07 17:15:23', '2025-07-07 17:17:53'),
(13, 17, 'ORD-20250707-73885', 500.00, 'COD', 'Pending', 'Cancelled', '', 'qweqeq', '2025-07-07 18:42:56', '2025-07-07 18:54:39'),
(14, 17, 'ORD-20250707-27208', 8000.00, 'Gcash', 'Paid', 'Processing', '09123456789', 'qweeqweqeq', '2025-07-07 18:43:25', '2025-07-07 18:43:25');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `variant_id`, `quantity`, `unit_price`, `total_price`) VALUES
(13, 11, 1, 2, 35, 300.00, 10500.00),
(14, 11, 1, 3, 5, 320.00, 1600.00),
(15, 11, 2, 5, 34, 500.00, 17000.00),
(16, 11, 3, 1, 34, 500.00, 17000.00),
(17, 12, 2, 5, 1, 500.00, 500.00),
(18, 13, 2, 5, 1, 500.00, 500.00),
(19, 14, 2, 5, 16, 500.00, 8000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, 'Oversized White T-shirt', 'Your everyday essential. Made from soft, breathable cotton, this oversized white tee pairs effortlessly with jeans, skirts, or shorts for a relaxed, timeless look.', '1751879188_image.png', '2025-07-07 09:06:28', '2025-07-07 09:06:28'),
(2, 2, 'Pants Men', 'Kick off your potential with these pants.', '1751879285_image2.png', '2025-07-07 09:08:05', '2025-07-07 09:08:24'),
(3, 3, 'Leather Jacket', 'Men&#039;s jacket, bomber style in leather-effect fabric.', '1751879340_image3.png', '2025-07-07 09:09:00', '2025-07-07 09:09:00');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` enum('S','M','L','XL','XXL','4XL') NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `production_cost` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image` text NOT NULL,
  `status` enum('Available','Unavailable','Out of Stock','') NOT NULL DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `size`, `color`, `price`, `production_cost`, `stock`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 'S', 'Black', 500.00, 300.00, 16, '1751879390_image3.png', 'Available', '2025-07-07 09:09:50', '2025-07-07 09:16:33'),
(2, 1, 'S', 'white', 300.00, 200.00, 15, '1751879426_image.png', 'Available', '2025-07-07 09:10:26', '2025-07-07 09:16:33'),
(3, 1, 'M', 'white', 320.00, 200.00, 45, '1751880287_image.png', 'Available', '2025-07-07 09:10:57', '2025-07-07 09:24:47'),
(4, 1, 'L', 'white', 325.00, 250.00, 50, '1751879554_image.png', 'Available', '2025-07-07 09:12:34', '2025-07-07 09:12:34'),
(5, 2, 'L', 'gray', 500.00, 450.00, 16, '1751879577_image2.png', 'Available', '2025-07-07 09:12:57', '2025-07-07 09:16:33');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `total_items` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `total_production_cost` decimal(10,2) NOT NULL,
  `profit` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `order_id`, `total_items`, `total_amount`, `total_production_cost`, `profit`, `created_at`) VALUES
(7, 11, 108, 46100.00, 33500.00, 12600.00, '2025-07-07 17:16:33');

-- --------------------------------------------------------

--
-- Table structure for table `sales_items`
--

CREATE TABLE `sales_items` (
  `id` int(11) NOT NULL,
  `sales_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_amount` int(11) NOT NULL,
  `total_production_cost` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_items`
--

INSERT INTO `sales_items` (`id`, `sales_id`, `product_id`, `variant_id`, `quantity`, `total_amount`, `total_production_cost`, `created_at`) VALUES
(9, 7, 1, 2, 35, 10500, 7000.00, '2025-07-07 17:16:33'),
(10, 7, 1, 3, 5, 1600, 1000.00, '2025-07-07 17:16:33'),
(11, 7, 2, 5, 34, 17000, 15300.00, '2025-07-07 17:16:33'),
(12, 7, 3, 1, 34, 17000, 10200.00, '2025-07-07 17:16:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Staff','User','') NOT NULL DEFAULT 'User',
  `profile_img` text NOT NULL DEFAULT 'default.png',
  `status` enum('Active','Inactive','','') NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password`, `role`, `profile_img`, `status`, `created_at`) VALUES
(1, 'Admin', '', 'Admin@gmail.com', '$2y$10$2XWliDhA0OHg/1vEAn.WF.DmMkYXCugpaMwZ1Qn/Mg3MA3UILJbxe', 'Admin', 'default.png', 'Active', '2025-07-02 21:22:31'),
(17, 'John', 'Doe', 'johndoe@gmail.com', '$2y$10$H/g1Rw8MqFmAKehpdJJlhetEBKUQj61NdglRuScm7kOLttq20gZnK', 'User', 'default.png', 'Active', '2025-07-07 16:56:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cart_product_id` (`product_id`),
  ADD KEY `fk_cart_user_id` (`user_id`),
  ADD KEY `fk_cart_variant_id` (`variant_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deliver_message`
--
ALTER TABLE `deliver_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_deliver_user_id` (`user_id`),
  ADD KEY `fk_deliver_order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `orders_ibfk_1` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_variant_id` (`variant_id`),
  ADD KEY `fk_order_product_id` (`product_id`),
  ADD KEY `fk_order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_productvariant_id` (`product_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sales_order_id` (`order_id`);

--
-- Indexes for table `sales_items`
--
ALTER TABLE `sales_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sales_id` (`sales_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `deliver_message`
--
ALTER TABLE `deliver_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sales_items`
--
ALTER TABLE `sales_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_variant_id` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `deliver_message`
--
ALTER TABLE `deliver_message`
  ADD CONSTRAINT `fk_deliver_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_deliver_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_variant_id` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_productvariant_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `fk_sales_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sales_items`
--
ALTER TABLE `sales_items`
  ADD CONSTRAINT `fk_sales_id` FOREIGN KEY (`sales_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
