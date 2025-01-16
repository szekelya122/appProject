-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 16, 2025 at 08:53 AM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webshop`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteOrder` (IN `p_order_id` INT)   BEGIN
      DELETE FROM orders
      WHERE id = p_order_id;
  END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetOrdersByUser` (IN `p_user_id` INT)   BEGIN
      SELECT 
          o.id AS order_id,
          u.username,
          p.product_name,
          o.order_quantity,
          o.order_date,
          o.status
      FROM orders o
      JOIN users u ON o.user_id = u.user_id
      JOIN product p ON o.product_id = p.product_id
      WHERE o.user_id = p_user_id;
  END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProductsByCategory` (IN `p_category_id` INT)   BEGIN
      SELECT product_id, product_name, price, product_quantity
      FROM product
      WHERE category_id = p_category_id;
  END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertUser` (IN `p_username` VARCHAR(100), IN `p_role` VARCHAR(50), IN `p_address` VARCHAR(255), IN `p_phonenumber` VARCHAR(15), IN `p_email` VARCHAR(100))   BEGIN
      INSERT INTO users (username, role, address, phonenumber, email)
      VALUES (p_username, p_role, p_address, p_phonenumber, p_email);
  END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `PlaceOrder` (IN `p_user_id` INT, IN `p_product_id` INT, IN `p_quantity` INT)   BEGIN
      DECLARE v_product_quantity INT;

      -- Check product availability
      SELECT product_quantity INTO v_product_quantity
      FROM product
      WHERE product_id = p_product_id;

      IF v_product_quantity >= p_quantity THEN
          -- Place the order
          INSERT INTO orders (user_id, product_id, order_quantity, status)
          VALUES (p_user_id, p_product_id, p_quantity, 'Processing');

          -- Update product stock
          UPDATE product
          SET product_quantity = product_quantity - p_quantity
          WHERE product_id = p_product_id;
      ELSE
          SIGNAL SQLSTATE '45000'
          SET MESSAGE_TEXT = 'Insufficient stock to place the order';
      END IF;
  END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateProductPrice` (IN `p_product_id` INT, IN `p_new_price` DECIMAL(10,2))   BEGIN
      UPDATE product
      SET price = p_new_price
      WHERE product_id = p_product_id;
  END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`) VALUES
(1, 'Electronics'),
(2, 'Books'),
(3, 'Clothing'),
(4, 'Home Appliances'),
(5, 'Beauty Products'),
(6, 'Sports'),
(7, 'Toys'),
(8, 'Furniture'),
(9, 'Automotive'),
(10, 'Groceries');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_quantity` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `order_quantity`, `order_date`, `status`) VALUES
(1, 7, 1, 2, '2025-01-16 08:47:54', 'Processing'),
(2, 8, 3, 1, '2025-01-16 08:47:54', 'Shipped'),
(3, 9, 4, 3, '2025-01-16 08:47:54', 'Delivered'),
(4, 10, 5, 1, '2025-01-16 08:47:54', 'Processing'),
(5, 11, 6, 4, '2025-01-16 08:47:54', 'Canceled'),
(6, 12, 7, 2, '2025-01-16 08:47:54', 'Delivered'),
(7, 13, 8, 5, '2025-01-16 08:47:54', 'Processing'),
(8, 14, 9, 1, '2025-01-16 08:47:54', 'Shipped'),
(9, 15, 10, 2, '2025-01-16 08:47:54', 'Processing'),
(10, 16, 2, 1, '2025-01-16 08:47:54', 'Delivered');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_type` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `product_type`, `price`, `product_quantity`, `category_id`) VALUES
(1, 'Smartphone', 'Electronics', '699.99', 50, 1),
(2, 'Laptop', 'Electronics', '1199.99', 30, 1),
(3, 'Fiction Book', 'Books', '19.99', 200, 2),
(4, 'T-Shirt', 'Clothing', '14.99', 150, 3),
(5, 'Microwave', 'Home Appliances', '89.99', 20, 4),
(6, 'Face Cream', 'Beauty Products', '24.99', 100, 5),
(7, 'Basketball', 'Sports', '29.99', 60, 6),
(8, 'Stuffed Bear', 'Toys', '15.99', 80, 7),
(9, 'Dining Table', 'Furniture', '499.99', 10, 8),
(10, 'Car Oil', 'Automotive', '39.99', 25, 9);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `address` varchar(255) DEFAULT NULL,
  `phonenumber` varchar(15) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `role`, `created_at`, `address`, `phonenumber`, `email`, `password`) VALUES
(2, 'Gipsz Jakab', 'Customer', '2024-11-25 10:16:53', 'Lehel sor 2', '06202369229', 'gipszjakab1976@gmail.com', ''),
(3, 'Hologram Ákos', 'Customer', '2024-11-25 10:17:50', 'Lehel sor 3', '06202369220', 'Hmi@gmail.com', ''),
(4, 'Kökényesi MC István', 'customer', '2025-01-09 09:05:45', NULL, NULL, 'kalanyoskornel1976@gmail.com', '$2y$10$mssAPBR5r9cRS2MmhAwu9.8qtGsWJ2xNiaLz3f/CRQ9ctNOp/WXRe'),
(5, 'Armin', 'customer', '2025-01-09 09:20:21', NULL, NULL, 'szekelyarmin121@gmail.com', '$2y$10$TJU2rst3ObMiqCU8m.EC/OrSg7FajMV0DokznKsTrTD4FE428T1Eq'),
(6, 'KukKornél', 'customer', '2025-01-09 09:45:15', NULL, NULL, 'Kurvakornel12@gmail.com', '$2y$10$iegRV2q7owQ4IH3il5rFleBcb4J6vaKuFK.fShKdMzLgi1uzLL5Wa'),
(7, 'Jane Doe', 'customer', '2025-01-16 08:47:54', '123 Main St', '1234567890', 'jane.doe@example.com', '$2y$10$EXAMPLEPASSWORDHASH1'),
(8, 'John Smith', 'customer', '2025-01-16 08:47:54', '456 Elm St', '2345678901', 'john.smith@example.com', '$2y$10$EXAMPLEPASSWORDHASH2'),
(9, 'Alice Brown', 'customer', '2025-01-16 08:47:54', '789 Oak St', '3456789012', 'alice.brown@example.com', '$2y$10$EXAMPLEPASSWORDHASH3'),
(10, 'Bob White', 'customer', '2025-01-16 08:47:54', '321 Pine St', '4567890123', 'bob.white@example.com', '$2y$10$EXAMPLEPASSWORDHASH4'),
(11, 'Charlie Black', 'admin', '2025-01-16 08:47:54', '654 Maple St', '5678901234', 'charlie.black@example.com', '$2y$10$EXAMPLEPASSWORDHASH5'),
(12, 'Dave Green', 'customer', '2025-01-16 08:47:54', '987 Cedar St', '6789012345', 'dave.green@example.com', '$2y$10$EXAMPLEPASSWORDHASH6'),
(13, 'Eve Blue', 'customer', '2025-01-16 08:47:54', '159 Birch St', '7890123456', 'eve.blue@example.com', '$2y$10$EXAMPLEPASSWORDHASH7'),
(14, 'Grace Red', 'customer', '2025-01-16 08:47:54', '753 Walnut St', '8901234567', 'grace.red@example.com', '$2y$10$EXAMPLEPASSWORDHASH8'),
(15, 'Hank Silver', 'admin', '2025-01-16 08:47:54', '951 Chestnut St', '9012345678', 'hank.silver@example.com', '$2y$10$EXAMPLEPASSWORDHASH9'),
(16, 'Ivy Gold', 'customer', '2025-01-16 08:47:54', '147 Spruce St', '0123456789', 'ivy.gold@example.com', '$2y$10$EXAMPLEPASSWORDHASH10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
