-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 17, 2025 at 11:51 AM
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertCategory` (IN `p_category_name` VARCHAR(100))   BEGIN  
    INSERT INTO categories (category_name) VALUES (p_category_name);  
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertUser` (IN `p_username` VARCHAR(100), IN `p_role` VARCHAR(50), IN `p_address` VARCHAR(255), IN `p_phonenumber` VARCHAR(15), IN `p_email` VARCHAR(100), IN `p_password` VARCHAR(64))   INSERT INTO users (username, role, address, phonenumber, email, password)
      VALUES (p_username, p_role, p_address, p_phonenumber, p_email,p_password)$$

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
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
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
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `category` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `price`, `quantity`, `category`, `img_path`) VALUES
(13, 'Arany gyűrű', '300.00', 3, 'Gyűrű', '../backend/uploads/6800dc8ba2651.jpg'),
(14, 'Arany gyűrű', '300.00', 3, 'Gyűrű', '../backend/uploads/6800dc8e21153.jpg'),
(15, 'Arany gyűrű', '300.00', 3, 'Gyűrű', '../backend/uploads/6800dc8edd17b.jpg'),
(16, 'Arany gyűrű', '300.00', 3, 'Gyűrű', '../backend/uploads/6800dc8f850f4.jpg'),
(17, 'Arany gyűrű', '300.00', 3, 'Gyűrű', '../backend/uploads/6800dc916e52e.jpg'),
(18, 'Arany gyűrű', '300.00', 3, 'Gyűrű', '../backend/uploads/6800dc91e38a2.jpg'),
(19, 'Arany gyűrű', '300.00', 3, 'Gyűrű', '../backend/uploads/6800dc9287d72.jpg'),
(20, 'Arany gyűrű', '300.00', 3, 'Gyűrű', '../backend/uploads/6800dc934118f.jpg'),
(21, 'Gyűrű', '4.00', 3, 'Gyűrű', '../backend/uploads/6800de064f8ff.jpg');

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
  `password` varchar(64) NOT NULL,
  `Name` varchar(64) DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `role`, `created_at`, `address`, `phonenumber`, `email`, `password`, `Name`, `deleted_at`) VALUES
(2, 'Gipsz Jakab', 'Customer', '2024-11-25 10:16:53', 'Lehel sor 2', '06202369229', 'gipszjakab1976@gmail.com', '', NULL, NULL),
(3, 'Hologram Ákos', 'Customer', '2024-11-25 10:17:50', 'Lehel sor 3', '06202369220', 'Hmi@gmail.com', '', NULL, NULL),
(4, 'Kökényesi MC István', 'customer', '2025-01-09 09:05:45', NULL, NULL, 'kalanyoskornel1976@gmail.com', '$2y$10$mssAPBR5r9cRS2MmhAwu9.8qtGsWJ2xNiaLz3f/CRQ9ctNOp/WXRe', NULL, NULL),
(31, 'admin', 'admin', '2025-03-03 12:29:16', NULL, NULL, 'admin@gmail.com', '$2y$10$KN9v8nX/C9zcUuh5B.d.9exqMa3q3vTGCl8StHRvCyE2Hu1qv/nia', NULL, NULL),
(32, '123', 'customer', '2025-04-06 19:02:16', NULL, NULL, '31231231@gmail.com', '$2y$10$nrPjyrFjOqSyAMgf2ICiIOBzY44SLDEyRh.iWZM4bnrv3UO9IEGRC', NULL, NULL),
(33, '123123121', 'customer', '2025-04-16 08:45:46', NULL, NULL, 'szekelyarmin121@gmail.com', '$2y$10$h6kByJ5V33QYrElhz1BInOO7n/FMX49w8v9YnChi5CuVi97odNEgW', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `username` (`username`,`phonenumber`,`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
