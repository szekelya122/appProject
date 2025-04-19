-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 19, 2025 at 08:12 PM
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddToCart` (IN `p_user_id` BIGINT, IN `p_product_id` BIGINT, IN `p_quantity` INT)   BEGIN
    DECLARE v_existing_quantity INT;
    
    -- Check if the item already exists in the cart
    SELECT quantity INTO v_existing_quantity
    FROM cart
    WHERE user_id = p_user_id AND product_id = p_product_id;

    IF v_existing_quantity IS NOT NULL THEN
        -- Update quantity if item exists
        UPDATE cart
        SET quantity = quantity + p_quantity
        WHERE user_id = p_user_id AND product_id = p_product_id;
    ELSE
        -- Insert new item into the cart
        INSERT INTO cart (user_id, product_id, quantity)
        VALUES (p_user_id, p_product_id, p_quantity);
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ClearCart` (IN `p_user_id` BIGINT)   BEGIN
    DELETE FROM cart
    WHERE user_id = p_user_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteCategory` (IN `p_category_id` INT)   BEGIN  
    DELETE FROM categories WHERE id = p_category_id;  
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteOrder` (IN `p_order_id` INT)   BEGIN
      DELETE FROM orders
      WHERE id = p_order_id;
  END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteUser` (IN `p_user_id` INT)   BEGIN  
    DELETE FROM users WHERE user_id = p_user_id;  
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllCategories` ()   BEGIN
    SELECT 
        id AS category_id, 
        category_name 
    FROM 
        categories;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllProducts` ()   BEGIN
    SELECT 
        p.product_id, 
        p.product_name, 
        p.product_type, 
        p.price, 
        p.product_quantity, 
        c.category_name
    FROM 
        product p
    LEFT JOIN 
        categories c 
    ON 
        p.category_id = c.id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCategoryById` (IN `id` INT)   SELECT * FROM categories WHERE id = categories.id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetOrderById` (IN `orderid` INT)   SELECT * FROM orders WHERE orderid = orders.id$$

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProductById` (IN `id` INT)   SELECT * FROM product WHERE id = product.id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProductDetails` (IN `p_product_id` INT)   BEGIN
    SELECT 
        p.product_id, 
        p.product_name, 
        p.product_type, 
        p.price, 
        p.product_quantity, 
        c.category_name 
    FROM 
        product p
    LEFT JOIN 
        categories c 
    ON 
        p.category_id = c.id
    WHERE 
        p.product_id = p_product_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProductsByCategory` (IN `p_category_id` INT)   BEGIN
      SELECT product_id, product_name, price, product_quantity
      FROM product
      WHERE category_id = p_category_id;
  END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserById` (IN `id` INT)   SELECT * FROM users WHERE id = users.user_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserLastFiveOrders` (IN `user_id_in` INT)   BEGIN
    SELECT
        o.id AS order_id,
        o.order_date,
        o.status,
        oi.quantity,
        oi.price AS ordered_price,
        p.name AS product_name
    FROM
        orders o
    JOIN
        order_items oi ON o.id = oi.order_id
    JOIN
        product p ON oi.product_id = p.id
    WHERE
        o.user_id = user_id_in
    ORDER BY
        o.order_date DESC
    LIMIT 5;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertCategory` (IN `p_name` VARCHAR(100))   BEGIN  
    INSERT INTO categories (name) VALUES (p_name);  
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertUser` (IN `p_username` VARCHAR(100), IN `p_role` VARCHAR(50), IN `p_address` VARCHAR(255), IN `p_phonenumber` VARCHAR(15), IN `p_email` VARCHAR(100), IN `p_password` VARCHAR(64))   INSERT INTO users (username, role, address, phonenumber, email, password)
      VALUES (p_username, p_role, p_address, p_phonenumber, p_email,p_password)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `PlaceOrder` (IN `p_user_id ` INT, IN `p_order_date` DATETIME, IN `p_total_amount` DECIMAL(10,2), IN `p_shipping_name` VARCHAR(64), IN `p_shipping_address` VARCHAR(255), IN `p_shipping_city` VARCHAR(255), IN `p_shipping_zip` VARCHAR(64), IN `p_shipping_country` VARCHAR(255))   INSERT INTO orders (user_id, order_date, total_amount, shipping_name, shipping_address, shipping_city, shipping_zip, shipping_country)
    VALUES (p_user_id, p_order_date, p_total_amount, p_shipping_name, p_shipping_address, p_shipping_city, p_shipping_zip, p_shipping_country)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RemoveFromCart` (IN `p_cart_id` BIGINT)   BEGIN
    DELETE FROM cart
    WHERE id = p_cart_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateCategory` (IN `p_category_id` INT, IN `p_new_name` VARCHAR(100))   BEGIN  
    UPDATE categories  
    SET category_name = p_new_name  
    WHERE id = p_category_id;  
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateOrderStatus` (IN `p_order_id` INT, IN `p_new_status` VARCHAR(50))   BEGIN  
    UPDATE orders  
    SET status = p_new_status  
    WHERE id = p_order_id;  
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateProductPrice` (IN `p_product_id` INT, IN `p_new_price` DECIMAL(10,2))   BEGIN
      UPDATE product
      SET price = p_new_price
      WHERE product_id = p_product_id;
  END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateProductStock` (IN `p_product_id` INT, IN `p_new_quantity` INT)   BEGIN  
    UPDATE product  
    SET quantity = p_new_quantity  
    WHERE id = p_product_id;  
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ViewCart` (IN `p_user_id` BIGINT)   BEGIN
    SELECT 
        c.id AS cart_id,
        p.name AS product_name,
        p.price AS unit_price,
        c.quantity,
        (p.price * c.quantity) AS total_price,
        c.added_at
    FROM cart c
    JOIN product p ON c.product_id = p.id
    WHERE c.user_id = p_user_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Gyűrű'),
(3, 'Karóra'),
(2, 'Nyaklánc');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `total_amount` int(10) DEFAULT NULL,
  `shipping_name` varchar(64) NOT NULL,
  `shipping_address` varchar(255) NOT NULL,
  `shipping_city` varchar(255) NOT NULL,
  `shipping_zip` varchar(64) NOT NULL,
  `shipping_country` varchar(255) NOT NULL,
  `price` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_date`, `status`, `total_amount`, `shipping_name`, `shipping_address`, `shipping_city`, `shipping_zip`, `shipping_country`, `price`) VALUES
(21, 38, '2025-04-19 16:02:38', NULL, 30, 'Székely Ármin', 'Árpád utca 44', 'Dombóvár', '7200', 'Hungary', NULL),
(22, 38, '2025-04-19 16:03:01', NULL, 29, 'Székely Ármin', 'Árpád utca 44', 'Dombóvár', '7200', 'Hungary', NULL),
(23, 38, '2025-04-19 16:07:49', NULL, 500, 'Székely Ármin', 'Árpád utca 44', 'Dombóvár', '7200', 'Hungary', NULL),
(24, 38, '2025-04-19 16:56:32', NULL, 500, 'Székely Ármin', 'Árpád utca 44', 'Dombóvár', '7200', 'Hungary', NULL),
(25, 38, '2025-04-19 18:01:45', 'pending', NULL, 'Székely Ármin', 'Árpád utca 44', 'Dombóvár', '7200', 'Hungary', 40),
(26, 38, '2025-04-19 18:38:13', 'pending', NULL, 'Székely Ármin', 'Árpád utca 44', 'Dombóvár', '7200', 'Hungary', NULL),
(27, 39, '2025-04-19 18:51:57', 'pending', NULL, 'Teszt', 'Teszt', 'teszt', '123', 'Teszt', 40),
(28, 39, '2025-04-19 18:52:26', 'pending', NULL, 'Teszt', 'Teszt', 'teszt', '123', 'Teszt', NULL),
(29, 39, '2025-04-19 18:54:51', 'pending', NULL, 'Teszt', 'Teszt', 'teszt', '123', 'Teszt', NULL),
(30, 39, '2025-04-19 19:06:24', 'pending', NULL, 'Teszt', 'Teszt', 'teszt', '123', 'Teszt', NULL),
(31, 38, '2025-04-19 20:06:16', 'pending', NULL, 'Székely Ármin', 'Árpád utca 44', 'Dombóvár', '7200', 'Hungary', 500),
(32, 38, '2025-04-19 20:11:06', 'pending', NULL, 'Székely Ármin', 'Árpád utca 44', 'Dombóvár', '7200', 'Hungary', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) UNSIGNED DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 21, 8, 1, '30.00'),
(2, 22, 13, 1, '29.00'),
(3, 23, 9, 1, '500.00'),
(4, 24, 9, 1, '500.00'),
(5, 25, 10, 1, '40.00'),
(6, 27, 10, 1, '40.00'),
(7, 31, 9, 1, '500.00');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `img_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `price`, `quantity`, `category_id`, `img_path`, `description`) VALUES
(8, 'Gyűrű', '30.00', 0, 1, '../backend/uploads/6803c8678dd39.jpg', NULL),
(9, 'Gyűrű 2', '500.00', 40, 1, '../backend/uploads/6803c876553cf.jpg', NULL),
(10, 'Gyűrű 3', '40.00', 26, 1, '../backend/uploads/6803c8838e980.jpg', NULL),
(11, 'Nyaklánc', '399.00', 1, 2, '../backend/uploads/6803c8981baf7.jpg', NULL),
(12, 'Nyaklánc 2', '59.00', 2, 2, '../backend/uploads/6803c8ad5e0f3.jpg', NULL),
(13, 'Nyaklánc 3', '29.00', 40, 2, '../backend/uploads/6803c8caa1fb3.jpg', NULL),
(14, 'Gyűrű321321', '3.00', 16, 1, '../backend/uploads/6803d17fe326f.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `address` varchar(255) DEFAULT NULL,
  `phonenumber` varchar(15) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(64) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `city` varchar(200) DEFAULT NULL,
  `zipcode` varchar(16) DEFAULT NULL,
  `country` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `role`, `created_at`, `address`, `phonenumber`, `email`, `password`, `name`, `city`, `zipcode`, `country`) VALUES
(38, 'admin', 'admin', '2025-04-19 15:56:45', 'Árpád utca 44', NULL, 'admin@admin.com', '$2y$10$JfeZU85EiXXfZCB2xojQpezylYp0OKonXRxQE78U9K3sVDV7JkueK', 'Székely Ármin', 'Dombóvár', '7200', 'Hungary'),
(39, 'teszt', 'customer', '2025-04-19 18:51:06', 'Teszt', NULL, 'teszt@teszt.com', '$2y$10$ABk6mWAtSCrFaFgG8yfOAuAJERLyNrPM6sSNiBpSbVm6tgN0d4p92', 'Teszt', 'teszt', '123', 'Teszt');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cart_user` (`user_id`),
  ADD KEY `fk_cart_product` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_items_order` (`order_id`),
  ADD KEY `fk_order_item_product` (`product_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`id`),
  ADD KEY `fk_product_category` (`category_id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `username` (`username`,`phonenumber`,`email`),
  ADD KEY `user_id_2` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_item_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
