-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
-- Host: localhost:3306
-- Generation Time: Nov 25, 2024 at 10:23 AM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Character set settings
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Database: `webshop`

-- --------------------------------------------------------
-- Table structure for `categories`
CREATE TABLE `categories` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_name` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- Table structure for `orders`
CREATE TABLE `orders` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `product_id` INT(11) NOT NULL,
    `order_quantity` INT(11) NOT NULL,
    `order_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` VARCHAR(50) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- Table structure for `product`
CREATE TABLE `product` (
    `product_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_name` VARCHAR(100) NOT NULL,
    `product_type` VARCHAR(50) DEFAULT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `product_quantity` INT(11) NOT NULL,
    `category_id` INT(11) DEFAULT NULL,
    PRIMARY KEY (`product_id`),
    UNIQUE KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- Table structure for `users`
CREATE TABLE `users` (
    `user_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(100) NOT NULL,
    `role` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `address` VARCHAR(255) DEFAULT NULL,
    `phonenumber` VARCHAR(15) DEFAULT NULL,
    `email` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Sample Data for `users`
INSERT INTO `users` (`user_id`, `username`, `role`, `created_at`, `address`, `phonenumber`, `email`) VALUES
(2, 'Gipsz Jakab', 'Customer', '2024-11-25 10:16:53', 'Lehel sor 2', '06202369229', 'gipszjakab1976@gmail.com'),
(3, 'Hologram Ákos', 'Customer', '2024-11-25 10:17:50', 'Lehel sor 3', '06202369220', 'Hmi@gmail.com');

-- --------------------------------------------------------
-- Stored Procedures
DELIMITER $$

-- Procedure to Delete an Order
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteOrder` (IN `p_order_id` INT)
BEGIN
    DELETE FROM `orders`
    WHERE `id` = p_order_id;
END$$

-- Procedure to Get Orders by User
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetOrdersByUser` (IN `p_user_id` INT)
BEGIN
    SELECT 
        o.id AS order_id,
        u.username,
        p.product_name,
        o.order_quantity,
        o.order_date,
        o.status
    FROM `orders` o
    JOIN `users` u ON o.user_id = u.user_id
    JOIN `product` p ON o.product_id = p.product_id
    WHERE o.user_id = p_user_id;
END$$

-- Procedure to Get Products by Category
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProductsByCategory` (IN `p_category_id` INT)
BEGIN
    SELECT 
        product_id, 
        product_name, 
        price, 
        product_quantity
    FROM `product`
    WHERE category_id = p_category_id;
END$$

-- Procedure to Insert a New User
CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertUser` (
    IN `p_username` VARCHAR(100), 
    IN `p_role` VARCHAR(50), 
    IN `p_address` VARCHAR(255), 
    IN `p_phonenumber` VARCHAR(15), 
    IN `p_email` VARCHAR(100)
)
BEGIN
    INSERT INTO `users` (username, role, address, phonenumber, email)
    VALUES (p_username, p_role, p_address, p_phonenumber, p_email);
END$$

-- Procedure to Place an Order
CREATE DEFINER=`root`@`localhost` PROCEDURE `PlaceOrder` (
    IN `p_user_id` INT, 
    IN `p_product_id` INT, 
    IN `p_quantity` INT
)
BEGIN
    DECLARE v_product_quantity INT;

    -- Check product availability
    SELECT product_quantity INTO v_product_quantity
    FROM `product`
    WHERE product_id = p_product_id;

    IF v_product_quantity >= p_quantity THEN
        -- Place the order
        INSERT INTO `orders` (user_id, product_id, order_quantity, status)
        VALUES (p_user_id, p_product_id, p_quantity, 'Processing');

        -- Update product stock
        UPDATE `product`
        SET product_quantity = product_quantity - p_quantity
        WHERE product_id = p_product_id;
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Insufficient stock to place the order';
    END IF;
END$$

-- Procedure to Update Product Price
CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateProductPrice` (
    IN `p_product_id` INT, 
    IN `p_new_price` DECIMAL(10,2)
)
BEGIN
    UPDATE `product`
    SET price = p_new_price
    WHERE product_id = p_product_id;
END$$

DELIMITER ;

-- Commit changes
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
