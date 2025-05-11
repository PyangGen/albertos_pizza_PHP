-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2025 at 03:09 PM
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
-- Database: `restaurant`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `image` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `catName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `total_price` varchar(255) NOT NULL,
  `size` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `itemName`, `price`, `image`, `quantity`, `catName`, `email`, `total_price`, `size`) VALUES
(1, 'French Fries', 760, 'fries.jpg', 1, 'Appetizer', 'asna@gmail.com', '760', NULL),
(2, 'BBQ Chicken Pizza', 1000, 'bbq-pizza.jpg', 1, 'Pizza', 'zidnan@gmail.com', '1000', NULL),
(3, 'Strawberry Mocktail', 550, 'strawberry-drink.png', 2, 'Beverage', 'zidnan@gmail.com', '1100', NULL),
(166, 'Pizza Bianca', 120, 'PIZZABIANCA_3ddc317e-0d34-40c0-bfe8-ef786e979378_800x.webp', 1, 'Pizza', 'jhon@gmail.com', '120', '9'),
(167, 'Pizza Bianca', 120, 'PIZZABIANCA_3ddc317e-0d34-40c0-bfe8-ef786e979378_800x.webp', 1, 'Pizza', 'admin@gmail.com', '120', '9'),
(179, 'Alfredo', 150, 'alfredo-sp.png', 1, 'Pizza', 'marites@gmail.com', '150', '9'),
(180, 'IL Supremo!', 210, 'ILSUPREMO_c5e4cdd1-1e78-4b12-a2c3-b891924987af_800x.webp', 1, 'Pizza', 'marites@gmail.com', '210', '11'),
(182, 'IL Supremo!', 170, 'ILSUPREMO_c5e4cdd1-1e78-4b12-a2c3-b891924987af_800x.webp', 1, 'Pizza', 'almafepepana@gmail.com', '170', '9'),
(183, 'IL Supremo!', 210, 'ILSUPREMO_c5e4cdd1-1e78-4b12-a2c3-b891924987af_800x.webp', 1, 'Pizza', 'almafepepana@gmail.com', '210', '11');

-- --------------------------------------------------------

--
-- Table structure for table `gcash_images`
--

CREATE TABLE `gcash_images` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gcash_images`
--

INSERT INTO `gcash_images` (`id`, `image_path`, `uploaded_at`) VALUES
(38, '681756d93479c.jpg', '2025-05-04 12:00:25'),
(39, '6817574f14ce4.jpg', '2025-05-04 12:02:23'),
(40, '681757559a854.jpg', '2025-05-04 12:02:29'),
(41, '68180e9d712fd.png', '2025-05-05 01:04:29');

-- --------------------------------------------------------

--
-- Table structure for table `menucategory`
--

CREATE TABLE `menucategory` (
  `catId` int(11) NOT NULL,
  `catName` varchar(255) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `min_time` int(11) DEFAULT NULL COMMENT 'Minimum estimated time in minutes',
  `max_time` int(11) DEFAULT NULL COMMENT 'Maximum estimated time in minutes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menucategory`
--

INSERT INTO `menucategory` (`catId`, `catName`, `dateCreated`, `min_time`, `max_time`) VALUES
(14, 'Pizza', '2025-05-04 12:05:30', 15, 20),
(15, 'Shakes', '2025-05-04 12:24:17', 5, 10),
(16, 'Tea', '2025-05-04 12:33:07', NULL, NULL),
(17, 'Halo-Halo', '2025-05-04 12:34:54', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menuitem`
--

CREATE TABLE `menuitem` (
  `itemId` int(11) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `catName` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `status` enum('Available','Unavailable','','') NOT NULL DEFAULT 'Available',
  `description` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedDate` datetime NOT NULL,
  `is_popular` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menuitem`
--

INSERT INTO `menuitem` (`itemId`, `itemName`, `catName`, `price`, `status`, `description`, `image`, `dateCreated`, `updatedDate`, `is_popular`) VALUES
(43, 'Mushroom & Truffle Pizza', 'Pizza', '', 'Available', 'Truffle, porcini, shiitake, and button mushrooms with mozzarella on signature dough, drizzled with truffle oil.', '3MUSHROOM_TRUFFLE_800x.webp', '2025-05-04 12:05:51', '2025-05-04 20:05:51', 1),
(44, 'Pizza Bianca', 'Pizza', '', 'Available', 'Ricotta, mozzarella, and parmesan topped with fresh arugula on our classic dough.', 'PIZZABIANCA_3ddc317e-0d34-40c0-bfe8-ef786e979378_800x.webp', '2025-05-04 12:07:21', '2025-05-04 20:07:21', 1),
(45, 'IL Supremo!', 'Pizza', '', 'Available', 'A hearty mix of meats, veggies, and cheeses loaded on one flavorful pizza.', 'ILSUPREMO_c5e4cdd1-1e78-4b12-a2c3-b891924987af_800x.webp', '2025-05-04 12:08:25', '2025-05-04 20:08:25', 1),
(46, 'Alfredo', 'Pizza', '', 'Available', 'A creamy and savory delight topped with rich Alfredo sauce, mozzarella, and tender chicken slices.', 'alfredo-sp.png', '2025-05-04 12:12:31', '2025-05-04 20:12:31', 0),
(47, 'BBQ Chicken Pizza', 'Pizza', '', 'Available', 'Sweet and smoky barbecue sauce meets juicy chicken and cheese for a bold, satisfying bite.', 'bbq-chicken-sp.png', '2025-05-04 12:14:55', '2025-05-04 20:14:55', 0),
(48, 'Cheese Pizza', 'Pizza', '', 'Available', 'A classic favorite loaded with gooey, melted cheese over a perfectly crisp crust.', 'cheese.png', '2025-05-04 12:15:36', '2025-05-04 20:15:36', 0),
(49, 'Classic Chicken Pizza', 'Pizza', '', 'Available', ' Simple yet flavorful, featuring seasoned chicken, cheese, and Alberto’s signature sauce.', 'classic-chicken.png', '2025-05-04 12:16:12', '2025-05-04 20:16:12', 0),
(50, 'Giant Pizza', 'Pizza', '', 'Available', 'A massive feast packed with assorted premium toppings, perfect for sharing and satisfying big cravings.', 'giant-sp.png', '2025-05-04 12:16:47', '2025-05-04 20:16:47', 0),
(51, 'Hawaiian Pizza', 'Pizza', '', 'Available', 'A tropical twist of sweet pineapple and savory ham on a cheesy, golden crust.', 'hawaiian-sp.png', '2025-05-04 12:17:18', '2025-05-04 20:17:18', 1),
(52, 'Pepperoni Pizza ', 'Pizza', '', 'Available', 'A timeless classic layered with spicy, crispy pepperoni and bubbling cheese.', 'pepperoni-and-beef-sp.png', '2025-05-04 12:17:55', '2025-05-04 20:17:55', 1),
(53, 'Zesty Ham and Cheddar Pizza ', 'Pizza', '', 'Available', 'A sharp, flavorful combo of cheddar cheese and zesty ham for a tangy, hearty slice.', 'zesty-ham-and-cheddar-sp.png', '2025-05-04 12:18:32', '2025-05-04 20:18:32', 0),
(54, 'Frutas Milk Shakes', 'Shakes', '', 'Available', 'Creamy milkshakes blended with pure, real fruits for a naturally refreshing and delicious treat.', 'VISMIN-MilkShakes-2024.jpg', '2025-05-04 12:28:01', '2025-05-04 20:28:01', 0),
(55, 'Choco Shakes', 'Shakes', '', 'Available', 'Rich and creamy choco shakes made with real chocolate and blended to smooth perfection for a sweet, indulgent treat.', 'VISMIN-ChocoOreoMocha-2024.jpg', '2025-05-04 12:29:19', '2025-05-04 20:29:19', 0),
(56, 'Mango Graham Shake', 'Shakes', '', 'Available', 'A tropical blend of ripe mangoes and crushed grahams, layered with creamy milk for a sweet and satisfying shake.', 'VISMIN-MangoGraham-2024.jpg', '2025-05-04 12:30:36', '2025-05-04 20:30:36', 0),
(57, 'Calamansi Shake', 'Shakes', '', 'Available', ' zesty, refreshing blend of fresh calamansi juice and ice for a tangy tropical cool-down.', 'VISMIN-Cucumber-Juice-2024.jpg', '2025-05-04 12:32:46', '2025-05-04 20:32:46', 0),
(58, 'Milk Tea', 'Tea', '', 'Unavailable', 'Classic milk tea brewed with rich black tea and creamy milk, served chilled for a smooth and refreshing sip.', 'VISMIN-MilkTeaPlus-2024.jpg', '2025-05-04 12:34:20', '2025-05-04 20:34:20', 0),
(59, 'Halo-halo Special', 'Halo-Halo', '', 'Available', 'A colorful blend of shaved ice, sweet beans, jellies, fruits, leche flan, and ube, topped with creamy ice cream for a classic Filipino delight.', 'VISMIN-SundaeHalo2x-2024.jpg', '2025-05-04 12:37:53', '2025-05-04 20:37:53', 0);

-- --------------------------------------------------------

--
-- Table structure for table `menuitem_sizes`
--

CREATE TABLE `menuitem_sizes` (
  `id` int(11) NOT NULL,
  `itemId` int(11) NOT NULL,
  `size` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menuitem_sizes`
--

INSERT INTO `menuitem_sizes` (`id`, `itemId`, `size`, `price`) VALUES
(28, 46, '9', 150.00),
(29, 47, '9', 160.00),
(30, 47, '11\"', 190.00),
(31, 48, '9', 160.00),
(32, 49, '9', 180.00),
(33, 50, '9', 160.00),
(34, 51, '9', 150.00),
(35, 53, '9', 150.00),
(38, 43, '11\'', 200.00),
(39, 44, '9', 120.00),
(40, 45, '9', 170.00),
(41, 45, '11', 210.00),
(42, 55, '16oz', 60.00),
(43, 57, 'bottle', 40.00),
(44, 58, '16oz', 70.00),
(45, 59, '16oz', 75.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `city` varchar(100) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `street` varchar(255) NOT NULL,
  `pmode` enum('COD','Gcash','Pick_up','') NOT NULL DEFAULT 'COD',
  `image` varchar(255) DEFAULT NULL,
  `payment_status` enum('Pending','Successful','Rejected','') NOT NULL DEFAULT 'Pending',
  `sub_total` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_status` enum('Pending','Completed','Cancelled','Processing','On the way') NOT NULL DEFAULT 'Pending',
  `cancel_reason` varchar(255) DEFAULT NULL,
  `note` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `email`, `firstName`, `lastName`, `phone`, `city`, `barangay`, `street`, `pmode`, `image`, `payment_status`, `sub_total`, `grand_total`, `order_date`, `order_status`, `cancel_reason`, `note`) VALUES
(56, 'zidnan@gmail.com', 'Mohamed', 'Muhadh', '0000000000', '', '', '', '', NULL, 'Successful', 1150.00, 1150.00, '2024-08-11 18:04:16', 'Completed', '', ''),
(57, 'jhon@gmail.com', 'Jhon', 'Paul', '7777777777', '', '', '', '', NULL, 'Rejected', 5720.00, 5720.00, '2024-08-08 18:05:26', 'Completed', '', ''),
(76, 'jhon@gmail.com', 'fdfds', 'fds', 'fdfs', '', '', '', 'Pick_up', NULL, 'Rejected', 2875.00, 2875.00, '2025-05-04 09:48:17', 'Cancelled', 'gfdgf', 'fdsdf'),
(86, 'daniel@gmail.com', 'Daniel', 'Padilla', '978787878', '', '', '', 'Gcash', 'VISMIN-SundaeHalo2x-2024.jpg', 'Successful', 210.00, 340.00, '2025-05-05 01:03:22', 'Completed', '', 'please'),
(87, 'daniel@gmail.com', 'ivan', 'sestual', '097777773', '', '', '', 'COD', '', 'Pending', 170.00, 220.00, '2025-05-05 02:34:37', 'Cancelled', 'way inyong pizza', 'way lami'),
(88, 'daniel@gmail.com', 'Jade', 'Alipan', '099797979', '', '', '', 'COD', '', 'Pending', 170.00, 220.00, '2025-05-05 02:40:20', 'Pending', NULL, 'sasdasdadasd'),
(89, 'daniel@gmail.com', 'Jhon', 'Cuadra', '979787777', '', '', '', 'Gcash', '68180e9d712fd (2).png', 'Pending', 210.00, 260.00, '2025-05-05 03:27:47', 'Cancelled', 'dsa', 'wala'),
(90, 'daniel@gmail.com', 'teter', 'tretre', '587999999', 'Mandaue City, 6014', 'Ibabao-Estancia', 'gfgfd', 'Pick_up', '', 'Pending', 120.00, 120.00, '2025-05-06 16:06:41', 'Cancelled', 'fdsfsd', 'rere'),
(91, 'marites@gmail.com', 'Marites', 'Quivedo', '987878787', 'Mandaue City, 6014', 'Banilad', 'maguikay', 'Gcash', 'download (5).jpg', 'Pending', 380.00, 430.00, '2025-05-07 03:46:03', 'Cancelled', 'wala lang', 'please lang');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `size` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `itemName`, `image`, `quantity`, `price`, `total_price`, `size`) VALUES
(161, 86, 'IL Supremo!', 'ILSUPREMO_c5e4cdd1-1e78-4b12-a2c3-b891924987af_800x.webp', 1, 210, 210.00, '11'),
(162, 87, 'IL Supremo!', 'ILSUPREMO_c5e4cdd1-1e78-4b12-a2c3-b891924987af_800x.webp', 1, 170, 170.00, '9'),
(163, 88, 'IL Supremo!', 'ILSUPREMO_c5e4cdd1-1e78-4b12-a2c3-b891924987af_800x.webp', 1, 170, 170.00, '9'),
(164, 89, 'IL Supremo!', 'ILSUPREMO_c5e4cdd1-1e78-4b12-a2c3-b891924987af_800x.webp', 1, 210, 210.00, '11'),
(165, 90, 'Pizza Bianca', 'PIZZABIANCA_3ddc317e-0d34-40c0-bfe8-ef786e979378_800x.webp', 1, 120, 120.00, '9'),
(166, 91, 'IL Supremo!', 'ILSUPREMO_c5e4cdd1-1e78-4b12-a2c3-b891924987af_800x.webp', 1, 170, 170.00, '9'),
(167, 91, 'IL Supremo!', 'ILSUPREMO_c5e4cdd1-1e78-4b12-a2c3-b891924987af_800x.webp', 1, 210, 210.00, '11');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `reset_token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact` varchar(10) NOT NULL,
  `noOfGuests` int(50) NOT NULL,
  `reservedTime` time NOT NULL,
  `reservedDate` date NOT NULL,
  `reservedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','On Process','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `reservation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`email`, `name`, `contact`, `noOfGuests`, `reservedTime`, `reservedDate`, `reservedAt`, `status`, `reservation_id`) VALUES
('asna@gmail.com', 'Asna Assalam', '0000000000', 6, '12:00:00', '2024-07-31', '2024-07-29 15:35:05', 'Completed', 1),
('zidnan@gmail.com', 'Zidnan', '1111111111', 5, '10:00:07', '2024-08-11', '2024-08-10 18:14:55', '', 2),
('preethi@gmail.com', 'Preethi Suresh', '5555555', 2, '06:30:59', '2024-08-10', '2024-08-03 18:15:54', '', 3),
('jhon@gmail.com', 'Jhon Paul', '334455', 9, '20:45:59', '2024-08-09', '2024-08-05 18:16:38', 'Cancelled', 4),
('hfhf@gmail.com', 'hfh', 'hgf', 4, '00:00:00', '2025-05-24', '2025-05-04 14:28:29', 'Pending', 18),
('josh@gmail.com', 'Josh', '0984878782', 4, '00:00:08', '2025-05-05', '2025-05-05 00:20:28', 'Pending', 19),
('josh@gmail.com', 'Joseph Cadenass', '0959997989', 4, '00:00:11', '2025-05-07', '2025-05-05 02:27:01', 'Pending', 20),
('josh@gmail.com', 'Joseph Cadenass', '0959997989', 4, '00:00:10', '2025-05-07', '2025-05-05 02:41:15', 'Pending', 21);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `review_text` text DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `review_date` date DEFAULT current_timestamp(),
  `status` enum('approved','pending','rejected') DEFAULT 'pending',
  `response` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `email`, `order_id`, `rating`, `review_text`, `video_path`, `review_date`, `status`, `response`) VALUES
(37, 'daniel@gmail.com', 90, 4, 'dsd', 'uploads/reviews/681a3b584bdfc_Screen Recording 2025-05-07 002843.mp4', '2025-05-07', 'approved', NULL),
(38, 'marites@gmail.com', 91, 2, 'lami', 'uploads/reviews/681ad79b4d1c5_Screen Recording 2025-05-07 002750.mp4', '2025-05-07', 'approved', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `role` enum('superadmin','admin','delivery boy','waiter') NOT NULL,
  `password` varchar(255) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_image` varchar(255) NOT NULL DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `firstName`, `lastName`, `email`, `contact`, `role`, `password`, `createdAt`, `updatedAt`, `profile_image`) VALUES
(2, 'Akshaya', 'Rohit', 'ak@gmail.com', '8877669955', 'superadmin', 'AkRohit', '2024-08-02 19:45:36', '2024-08-10 15:30:48', 'user-girl.png'),
(3, 'Ravi', 'Kumar', 'ravi@gmail.com', '9876543210', 'delivery boy', 'ravi123', '2024-08-02 19:46:10', '2024-08-02 19:46:10', 'default.jpg'),
(5, 'Demo', 'Admin', 'admin@gmail.com', '0000000000', 'admin', 'admin2024', '2024-08-04 06:51:20', '2025-05-05 00:34:03', 'AI Generated Model.jpg'),
(7, 'Pyangg', 'Generalao', 'pyang@gmail.com', '0997799', 'delivery boy', '123123123', '2025-05-03 08:29:34', '2025-05-03 08:33:57', 'default.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `email` varchar(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `contact` varchar(10) NOT NULL,
  `password` varchar(20) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) NOT NULL DEFAULT 'default.jpg',
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`email`, `firstName`, `lastName`, `contact`, `password`, `dateCreated`, `profile_image`, `reset_token`, `token_expiry`, `otp`, `otp_expiry`) VALUES
('1233444@gmail.com', 'congtvbvbvbvbvbb', 'cvcvcvbvbvhhhhhh', '988888888', 'Pyang#1234', '2025-05-08 05:26:59', 'default.jpg', NULL, NULL, NULL, NULL),
('almafepepana@gmail.com', 'mace', 'lassy', '978787888', 'Mace#1234', '2025-05-07 15:32:45', 'AI Generated Model.jpg', NULL, NULL, NULL, NULL),
('almafepepani.g@gmail.com', 'alma', 'peps', '978787888', 'Pyang#1234', '2025-05-08 02:10:45', 'default.jpg', NULL, NULL, NULL, NULL),
('almafepepania@gmail.com', '  434', '4343', '998788877', '123123', '2025-05-05 03:38:55', 'default.jpg', NULL, NULL, NULL, NULL),
('asna@gmail.com', 'Asna', 'Assalam', '3333333333', 'AsnaA', '2024-07-26 12:50:46', 'user-girl.png', NULL, NULL, NULL, NULL),
('daniel@gmail.com', 'daniell', 'padilla', '0978787333', 'daniel', '2025-05-05 00:30:38', '681757559a854.jpg', NULL, NULL, NULL, NULL),
('fdfdf@gmail.com', 'fdfs', 'fsdfds', '654878977', 'Jassy@1234', '2025-05-07 14:55:09', 'default.jpg', NULL, NULL, NULL, NULL),
('jhon@gmail.com', 'Jhon', 'Paul', '4444444444', 'JhonP', '2024-08-10 15:37:56', 'default.jpg', NULL, NULL, NULL, NULL),
('marites@gmail.com', 'marites', 'Quivedo', '989787866', 'marites', '2025-05-07 03:43:59', 'default.jpg', NULL, NULL, NULL, NULL),
('pepania@gmail.com', 'rewew', 'fsf', '434343333', 'pyangpepania', '2025-05-05 03:40:32', 'default.jpg', NULL, NULL, NULL, NULL),
('preethi@gmail.com', 'Preethi', 'Suresh', '2222222222', 'Preethi123', '2024-08-10 15:36:50', 'default.jpg', NULL, NULL, NULL, NULL),
('zidnan@gmail.com', 'Zidnan', 'Ahamad', '1111111111', 'Zidnan123', '2024-07-30 12:45:21', 'user-boy.jpg', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gcash_images`
--
ALTER TABLE `gcash_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menucategory`
--
ALTER TABLE `menucategory`
  ADD PRIMARY KEY (`catId`),
  ADD UNIQUE KEY `catName` (`catName`);

--
-- Indexes for table `menuitem`
--
ALTER TABLE `menuitem`
  ADD PRIMARY KEY (`itemId`);

--
-- Indexes for table `menuitem_sizes`
--
ALTER TABLE `menuitem_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `itemId` (`itemId`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `itemId` (`itemName`) USING BTREE;

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `email` (`email`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=184;

--
-- AUTO_INCREMENT for table `gcash_images`
--
ALTER TABLE `gcash_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `menucategory`
--
ALTER TABLE `menucategory`
  MODIFY `catId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `menuitem`
--
ALTER TABLE `menuitem`
  MODIFY `itemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `menuitem_sizes`
--
ALTER TABLE `menuitem_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menuitem_sizes`
--
ALTER TABLE `menuitem_sizes`
  ADD CONSTRAINT `menuitem_sizes_ibfk_1` FOREIGN KEY (`itemId`) REFERENCES `menuitem` (`itemId`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
