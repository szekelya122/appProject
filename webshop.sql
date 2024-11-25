-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 25, 2024 at 10:23 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `address` varchar(255) DEFAULT NULL,
  `phonenumber` varchar(15) DEFAULT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `role`, `created_at`, `address`, `phonenumber`, `email`) VALUES
(2, 'Gipsz Jakab', 'Customer', '2024-11-25 10:16:53', 'Lehel sor 2', '06202369229', 'gipszjakab1976@gmail.com'),
(3, 'Hologram Ákos', 'Customer', '2024-11-25 10:17:50', 'Lehel sor 3', '06202369220', 'Hmi@gmail.com');

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
