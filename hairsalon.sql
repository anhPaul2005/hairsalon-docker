-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th12 09, 2025 lúc 01:21 PM
-- Phiên bản máy phục vụ: 9.1.0
-- Phiên bản PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `hairsalon`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) DEFAULT NULL,
  `book_date` date DEFAULT NULL,
  `book_time` time DEFAULT NULL,
  `stylist` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reject_reason` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `bookings`
--

INSERT INTO `bookings` (`id`, `customer_name`, `book_date`, `book_time`, `stylist`, `status`, `created_at`, `reject_reason`, `phone`) VALUES
(1, 'Khách Demo', '2025-11-06', '12:20:00', 'Ngẫu nhiên', 'confirmed', '2025-11-23 04:19:51', NULL, NULL),
(2, 'Khách Demo', '2025-11-24', '10:47:00', 'Tuấn Anh', 'confirmed', '2025-11-23 04:21:41', NULL, NULL),
(3, 'Khách Demo', '2025-11-24', '09:28:00', 'Tuấn Anh', 'confirmed', '2025-11-23 05:23:46', NULL, NULL),
(4, 'Khách Demo', '2025-11-24', '10:46:00', 'Ngẫu nhiên', 'confirmed', '2025-11-23 05:43:38', NULL, NULL),
(5, 'Khách Demo', '2025-11-24', '13:47:00', 'Khang', 'rejected', '2025-11-23 05:48:09', 'Sorry', NULL),
(6, 'tuananh123', '2025-11-24', '12:56:00', 'Khang', 'confirmed', '2025-11-23 05:56:39', NULL, NULL),
(7, 'tuananh123', '2025-11-26', '18:01:00', 'Tuấn Anh', 'rejected', '2025-11-23 06:02:03', 'Quán đóng cửa', NULL),
(8, 'Khách Demo', '2025-11-25', '08:00:00', 'Ngẫu nhiên', 'confirmed', '2025-11-23 16:12:25', NULL, '0986046133'),
(9, 'Khách Demo', '2025-12-12', '12:35:00', 'Trần Thanh Bò', 'confirmed', '2025-12-04 03:34:00', NULL, '0986046133');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `items` text,
  `total_price` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) DEFAULT 'pending',
  `reject_reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `price` int NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `description`) VALUES
(5, 'Volcanic Clay', 400000, 'https://classic.vn/wp-content/uploads/2016/10/volcanic-clay-v4-2019-810x800.jpg', NULL),
(6, 'Oxy Deep Shampoo 500ml', 340000, 'https://shop.rohto.com.vn/media/catalog/product/1/_/1_4_.jpg?optimize=medium&fit=bounds&height=&width=&canvas=:', NULL),
(7, 'Sữa rửa mặt Oxy Perfect Wash', 129000, 'https://encrypted-tbn2.gstatic.com/shopping?q=tbn:ANd9GcSnNCbhV5z1koy6fXbLW2_KSNu8YvLsp2NLy6sU73hPM9VQqZwrFH83PZ5YxwRuXBtADKgQudosmI8UMmYykyD_CxlgHvDGynA5Yb0yGJLXltvX8NJGij6KMU_ZSLMQOMMkybhAGEw&usqp=CAc', NULL),
(23, 'Mặt nạ đất sét L’Oreal Pure Clay', 250000, 'https://media.hcdn.vn/wysiwyg/HaNguyen/mat-na-dat-set-l-oreal-50g-8.png', NULL),
(24, 'Sữa tắm Oxy Cool Blue', 105000, 'https://cdn.famitaa.net/storage/uploads/noidung/oxy-perfect-cool-wash-rohto-mentholatum-100g-kem-rua-mat_00442.jpg', NULL),
(25, 'Sữa tắm cho nam Romano', 125000, 'https://www.lottemart.vn/media/catalog/product/cache/0x0/8/9/8935212811002-4-1.jpg.webp', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(50) DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `comment` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `customer_name`, `rating`, `comment`) VALUES
(1, 'Nguyễn Văn A', 5, 'Cắt rất đẹp, thợ nhiệt tình!'),
(2, 'Admin', 5, '123123');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `stylists`
--

DROP TABLE IF EXISTS `stylists`;
CREATE TABLE IF NOT EXISTS `stylists` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `experience` varchar(50) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `stylists`
--

INSERT INTO `stylists` (`id`, `name`, `experience`, `avatar`) VALUES
(6, 'Lê Đức', '1 năm', NULL),
(4, 'Tuấn Anh', '1 năm', NULL),
(5, 'Hoàng Lực', '1 năm', NULL),
(7, 'Hoàng Dương', '1 năm', NULL),
(8, 'Thanh Huy', '1 năm', NULL),
(9, 'Khánh Đậu', '1 năm', NULL),
(10, 'Hữu Khang', '1 năm', NULL),
(11, 'Mè Thái Huy', '1 năm', NULL),
(12, 'Minh Khánh', '1 năm', NULL),
(13, 'Trần Thanh Bò', '1 năm', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(10) DEFAULT 'user',
  `fullname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `is_verified` tinyint DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `fullname`, `verification_token`, `is_verified`) VALUES
(1, 'admin', '123', 'admin@salon.com', 'admin', NULL, NULL, 1),
(2, 'demo', '123', 'demo@gmail.com', 'user', NULL, NULL, 1),
(8, 'hoangluc123', '@Nhl123.', 'nguyenhoangluc9@gmail.com', 'user', 'Nguyễn Hoàng Lực', 'dc35432178dc7ec87d69d94f17c42fc0', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
