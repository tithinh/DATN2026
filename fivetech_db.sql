-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th2 24, 2026 lúc 03:30 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `fivetech_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `variant_id` int(10) UNSIGNED DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `session_id`, `product_id`, `variant_id`, `quantity`, `created_at`, `updated_at`) VALUES
(7, 4, NULL, 9, 3, 1, '2026-02-23 03:31:14', '2026-02-23 03:31:14'),
(8, 4, NULL, 9, 3, 1, '2026-02-23 03:31:15', '2026-02-23 03:31:15'),
(9, 4, NULL, 9, 3, 1, '2026-02-23 03:31:17', '2026-02-23 03:31:17'),
(10, 4, NULL, 9, 3, 1, '2026-02-23 03:31:18', '2026-02-23 03:31:18');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `slug`, `parent_id`, `description`, `icon`, `sort_order`, `is_active`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 'Cáp sạc', 'cap-sac', NULL, 'Các loại cáp sạc điện thoại', 'bolt', 1, 1, 1, '2026-01-28 17:05:48', '2026-01-28 17:05:48'),
(2, 'Pin dự phòng', 'pin-du-phong', NULL, 'Pin sạc dự phòng dung lượng cao', 'battery', 2, 1, 1, '2026-01-28 17:05:48', '2026-01-28 17:05:48'),
(3, 'Tai nghe', 'tai-nghe', NULL, 'Tai nghe bluetooth, tai nghe có dây', 'headphones', 3, 1, 1, '2026-01-28 17:05:48', '2026-01-28 17:05:48'),
(4, 'Ốp lưng', 'op-lung', NULL, 'Ốp lưng bảo vệ điện thoại', 'mobile', 4, 1, 0, '2026-01-28 17:05:48', '2026-01-28 17:05:48'),
(5, 'Củ sạc', 'cu-sac', NULL, 'Củ sạc nhanh PD, GaN', 'plug', 5, 1, 0, '2026-01-28 17:05:48', '2026-01-28 17:05:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `content` text NOT NULL,
  `rating` tinyint(3) UNSIGNED DEFAULT NULL CHECK (`rating` between 1 and 5),
  `status` enum('pending','approved','spam') DEFAULT 'pending',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `comments`
--

INSERT INTO `comments` (`comment_id`, `product_id`, `user_id`, `parent_id`, `content`, `rating`, `status`, `updated_at`, `created_at`) VALUES
(3, 9, 4, NULL, 'ádasdasd', 2, 'pending', '2026-02-23 02:21:53', '2026-02-23 02:21:53'),
(4, 9, 4, NULL, 'ádasd', 3, 'pending', '2026-02-23 02:22:03', '2026-02-23 02:22:03'),
(5, 9, 4, 3, 'ádasd', NULL, 'approved', '2026-02-23 02:22:13', '2026-02-23 02:22:13'),
(6, 9, 4, NULL, 'ád', 3, 'pending', '2026-02-23 02:33:06', '2026-02-23 02:33:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_28_164733_create_personal_access_tokens_table', 2),
(5, '2026_01_28_172245_create_carts_table', 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `order_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `promo_id` int(10) UNSIGNED DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `discount_amount` decimal(12,2) DEFAULT 0.00,
  `final_amount` decimal(12,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','canceled','returned') DEFAULT 'pending',
  `payment_method` enum('cod','bank','momo','vnpay','paypal') DEFAULT 'cod',
  `shipping_address` text NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `variant_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `price_at_purchase` decimal(12,2) NOT NULL,
  `rating` tinyint(3) UNSIGNED DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'auth_token', '6a50475ecd251e7d555e16a6d62d2eefff758823bcca7fde126460bffb421826', '[\"*\"]', NULL, NULL, '2026-02-04 10:24:10', '2026-02-04 10:24:10'),
(2, 'App\\Models\\User', 2, 'auth_token', 'b8013621663ed7dac9c127cd343bdd0e3f73d0c1c4effb04e1928c3a0d169360', '[\"*\"]', NULL, NULL, '2026-02-04 10:35:55', '2026-02-04 10:35:55'),
(3, 'App\\Models\\User', 4, 'auth_token', 'ae306769d84ecf40ca9864b626f9854df4d5cfbed6ada4efed566f088276406b', '[\"*\"]', NULL, NULL, '2026-02-05 09:38:19', '2026-02-05 09:38:19'),
(4, 'App\\Models\\User', 4, 'auth_token', '16ad82b94347bf93908bcb2b1566b210571535a9ceff09de59f941a25dd610b2', '[\"*\"]', NULL, NULL, '2026-02-06 07:26:33', '2026-02-06 07:26:33'),
(5, 'App\\Models\\User', 4, 'auth_token', '3f4ca03e20e50d6d08db9c8927abd199e5e0cc76b8a0fa95696908edefc3e517', '[\"*\"]', NULL, NULL, '2026-02-06 07:35:06', '2026-02-06 07:35:06'),
(6, 'App\\Models\\User', 4, 'auth_token', '7059a461050ce013ce94e94dccd051047391b1aa6ecd53aecc2c974382af1520', '[\"*\"]', NULL, NULL, '2026-02-06 07:37:44', '2026-02-06 07:37:44'),
(7, 'App\\Models\\User', 4, 'auth_token', '5c307eb2857118e879aca9bb0f7dd2517abfb48a8ee063bfcee2fe4d2316f009', '[\"*\"]', NULL, NULL, '2026-02-06 07:39:55', '2026-02-06 07:39:55'),
(8, 'App\\Models\\User', 4, 'auth_token', 'a86b749372d2ad0bb2ce4926df49546f5ee8562cb2610af6fe0092e999e242f0', '[\"*\"]', NULL, NULL, '2026-02-06 07:40:25', '2026-02-06 07:40:25'),
(9, 'App\\Models\\User', 4, 'auth_token', '63b4327790ae3a1206fe16e9c4fbb9e1edf55cac9c4e36f185c52c39bb48d881', '[\"*\"]', NULL, NULL, '2026-02-06 07:52:05', '2026-02-06 07:52:05'),
(10, 'App\\Models\\User', 4, 'auth_token', '32a11f534ad3f5b405365f2fc645dec0f220a38e54c1c934de69283b47f40b4f', '[\"*\"]', NULL, NULL, '2026-02-06 08:01:09', '2026-02-06 08:01:09'),
(11, 'App\\Models\\User', 4, 'auth_token', '7f5b8526013ffc728b4dba1e818e426738a1391b6a11de28ce7b07a4f2ad4566', '[\"*\"]', NULL, NULL, '2026-02-06 08:07:36', '2026-02-06 08:07:36'),
(12, 'App\\Models\\User', 4, 'auth_token', '8cf999f3125c230c342162639e3ec37a035f43a7828fcfcdbd3cc746321eb84f', '[\"*\"]', NULL, NULL, '2026-02-06 08:08:49', '2026-02-06 08:08:49'),
(13, 'App\\Models\\User', 4, 'auth_token', 'c5c72ea461611883b461c7b0c82b84fd512d033a936aeda0581fa5441cf6e778', '[\"*\"]', NULL, NULL, '2026-02-06 08:09:49', '2026-02-06 08:09:49'),
(14, 'App\\Models\\User', 4, 'auth_token', 'fcf837394d8a4f2f7a9162c1d0ed57eb6b9739bd29d380fa9751f8c17ade436f', '[\"*\"]', NULL, NULL, '2026-02-08 10:28:10', '2026-02-08 10:28:10'),
(15, 'App\\Models\\User', 4, 'auth_token', 'a2124553eb1a079225a61c8eafccb31a41ac3181b6b4b18ba249a8316f6459e5', '[\"*\"]', NULL, NULL, '2026-02-08 10:28:41', '2026-02-08 10:28:41'),
(16, 'App\\Models\\User', 5, 'auth_token', '46f958170950b5f216b992b5e72dc7da3ec7e10a1b402804540ab92421fbe989', '[\"*\"]', NULL, NULL, '2026-02-11 10:22:38', '2026-02-11 10:22:38'),
(17, 'App\\Models\\User', 5, 'auth_token', '2ae59ffebe87e0bb25d2518905605c6e8f3301b52cc6371254339aab9b863a6d', '[\"*\"]', '2026-02-11 11:48:09', NULL, '2026-02-11 10:36:01', '2026-02-11 11:48:09'),
(18, 'App\\Models\\User', 4, 'auth_token', 'b637a83bd7b167fa6b146c9bbe2aa92e5d634f703b42645b263bb38aa1ba2331', '[\"*\"]', '2026-02-23 03:31:33', NULL, '2026-02-12 07:54:22', '2026-02-23 03:31:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `short_desc` varchar(500) DEFAULT NULL,
  `base_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_price` decimal(12,2) DEFAULT NULL,
  `stock_total` int(10) UNSIGNED DEFAULT 0,
  `likes_count` int(10) UNSIGNED DEFAULT 0,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `is_visible` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`product_id`, `name`, `slug`, `description`, `short_desc`, `base_price`, `discount_price`, `stock_total`, `likes_count`, `category_id`, `is_visible`, `is_featured`, `created_at`, `updated_at`) VALUES
(9, 'Cáp sạc Lightning Anker PowerLine', 'cap-sac-lightning-anker-powerline', 'Cáp sạc Lightning chính hãng Anker PowerLine, lõi Kevlar siêu bền, hỗ trợ sạc nhanh và truyền dữ liệu ổn định.', 'Cáp sạc Lightning Anker siêu bền', 290000.00, 249000.00, 120, 15, 1, 1, 1, '2026-01-28 17:06:39', '2026-01-28 17:06:39'),
(10, 'Cáp sạc Type-C Baseus 60W', 'cap-sac-type-c-baseus-60w', 'Cáp sạc Type-C Baseus hỗ trợ sạc nhanh Power Delivery 60W, phù hợp laptop và điện thoại.', 'Cáp Type-C sạc nhanh 60W', 250000.00, 199000.00, 150, 9, 1, 1, 0, '2026-01-28 17:06:39', '2026-01-28 17:06:39'),
(11, 'Pin dự phòng Xiaomi 10000mAh', 'pin-du-phong-xiaomi-10000mah', 'Pin dự phòng Xiaomi dung lượng 10000mAh, hỗ trợ sạc nhanh 18W, thiết kế gọn nhẹ.', 'Pin dự phòng Xiaomi 10000mAh', 450000.00, 399000.00, 80, 22, 2, 1, 1, '2026-01-28 17:06:39', '2026-01-28 17:06:39'),
(12, 'Pin dự phòng Anker PowerCore 20000mAh', 'pin-du-phong-anker-20000mah', 'Pin dự phòng Anker PowerCore dung lượng lớn 20000mAh, sạc nhanh và an toàn.', 'Pin dự phòng Anker 20000mAh', 950000.00, NULL, 60, 11, 2, 1, 0, '2026-01-28 17:06:39', '2026-01-28 17:06:39'),
(13, 'Tai nghe Bluetooth AirPods Pro 2', 'tai-nghe-airpods-pro-2', 'AirPods Pro 2 với chống ồn chủ động, âm thanh cao cấp và chip H2 mới.', 'AirPods Pro 2 chống ồn', 6200000.00, 5890000.00, 40, 30, 3, 1, 1, '2026-01-28 17:06:39', '2026-01-28 17:06:39'),
(14, 'Tai nghe Sony WF-1000XM5', 'tai-nghe-sony-wf-1000xm5', 'Tai nghe true wireless Sony WF-1000XM5 với công nghệ chống ồn hàng đầu.', 'Tai nghe Sony chống ồn cao cấp', 6900000.00, NULL, 35, 18, 3, 1, 0, '2026-01-28 17:06:39', '2026-01-28 17:06:39'),
(15, 'Ốp lưng iPhone 15 Pro Silicon', 'op-lung-iphone-15-pro-silicon', 'Ốp lưng silicon mềm cho iPhone 15 Pro, chống sốc và chống trầy hiệu quả.', 'Ốp lưng iPhone 15 Pro', 290000.00, 199000.00, 200, 12, 4, 1, 0, '2026-01-28 17:06:39', '2026-01-28 17:06:39'),
(16, 'Ốp lưng Samsung S24 Ultra trong suốt', 'op-lung-samsung-s24-ultra', 'Ốp lưng trong suốt cho Samsung S24 Ultra, thiết kế mỏng nhẹ.', 'Ốp lưng S24 Ultra', 250000.00, NULL, 180, 7, 4, 1, 0, '2026-01-28 17:06:39', '2026-01-28 17:06:39');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_variants`
--

CREATE TABLE `product_variants` (
  `variant_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `storage_size` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price_extra` decimal(10,2) DEFAULT 0.00,
  `sku` varchar(100) DEFAULT NULL,
  `stock` int(10) UNSIGNED DEFAULT 0,
  `image_urls` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_variants`
--

INSERT INTO `product_variants` (`variant_id`, `product_id`, `color`, `storage_size`, `name`, `price_extra`, `sku`, `stock`, `image_urls`, `created_at`, `updated_at`) VALUES
(1, 9, 'Trắng', NULL, 'Cáp 1m - Trắng', 0.00, 'ANKER-LIGHT-1M-WHITE', 40, '[\"variants/cap-anker-1m-trang.jpg\"]', '2026-02-04 16:51:26', '2026-02-23 18:52:18'),
(2, 9, 'Đen', NULL, 'Cáp 1m - Đen', 0.00, 'ANKER-LIGHT-1M-BLACK', 40, NULL, '2026-02-04 16:51:26', '2026-02-04 16:51:26'),
(3, 9, 'Đen', NULL, 'Cáp 2m - Đen', 20000.00, 'ANKER-LIGHT-2M-BLACK', 30, NULL, '2026-02-04 16:51:26', '2026-02-04 16:51:26'),
(4, 10, 'Đen', NULL, 'Cáp 1m - Đen', 0.00, 'BASEUS-C-1M-BLACK', 50, NULL, '2026-02-04 16:51:34', '2026-02-04 16:51:34'),
(5, 10, 'Đen', NULL, 'Cáp 2m - Đen', 20000.00, 'BASEUS-C-2M-BLACK', 40, NULL, '2026-02-04 16:51:34', '2026-02-04 16:51:34'),
(6, 10, 'Đỏ', NULL, 'Cáp 2m - Đỏ', 20000.00, 'BASEUS-C-2M-RED', 40, NULL, '2026-02-04 16:51:34', '2026-02-04 16:51:34'),
(7, 11, 'Đen', '10000mAh', '10000mAh - Đen', 0.00, 'XIAOMI-10000-BLACK', 40, NULL, '2026-02-04 16:51:44', '2026-02-04 16:51:44'),
(8, 11, 'Trắng', '10000mAh', '10000mAh - Trắng', 0.00, 'XIAOMI-10000-WHITE', 40, NULL, '2026-02-04 16:51:44', '2026-02-04 16:51:44'),
(9, 12, 'Đen', '20000mAh', '20000mAh - Đen', 0.00, 'ANKER-20000-BLACK', 30, NULL, '2026-02-04 16:51:50', '2026-02-04 16:51:50'),
(10, 12, 'Xanh', '20000mAh', '20000mAh - Xanh', 0.00, 'ANKER-20000-BLUE', 30, NULL, '2026-02-04 16:51:50', '2026-02-04 16:51:50'),
(11, 13, 'Trắng', NULL, 'Lightning', 0.00, 'AIRPODS-PRO2-LIGHTNING', 20, NULL, '2026-02-04 16:51:59', '2026-02-04 16:51:59'),
(12, 13, 'Trắng', NULL, 'USB-C', 100000.00, 'AIRPODS-PRO2-USBC', 15, NULL, '2026-02-04 16:51:59', '2026-02-04 16:51:59'),
(13, 14, 'Đen', NULL, 'Màu Đen', 0.00, 'SONY-XM5-BLACK', 20, NULL, '2026-02-04 16:52:09', '2026-02-04 16:52:09'),
(14, 14, 'Bạc', NULL, 'Màu Bạc', 0.00, 'SONY-XM5-SILVER', 15, NULL, '2026-02-04 16:52:09', '2026-02-04 16:52:09'),
(15, 15, 'Đen', NULL, 'Màu Đen', 0.00, 'CASE-IP15-BLACK', 70, NULL, '2026-02-04 16:52:27', '2026-02-04 16:52:27'),
(16, 15, 'Xanh', NULL, 'Màu Xanh', 0.00, 'CASE-IP15-BLUE', 70, NULL, '2026-02-04 16:52:27', '2026-02-04 16:52:27'),
(17, 15, 'Hồng', NULL, 'Màu Hồng', 0.00, 'CASE-IP15-PINK', 60, NULL, '2026-02-04 16:52:27', '2026-02-04 16:52:27'),
(18, 16, 'Trong suốt', NULL, 'Trong suốt', 0.00, 'CASE-S24-CLEAR', 90, NULL, '2026-02-04 16:52:35', '2026-02-04 16:52:35'),
(19, 16, 'Đen', NULL, 'Màu Đen', 0.00, 'CASE-S24-BLACK', 90, NULL, '2026-02-04 16:52:35', '2026-02-04 16:52:35');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotions`
--

CREATE TABLE `promotions` (
  `promo_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `promo_type` enum('percentage','fixed','free_shipping') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(12,2) DEFAULT 0.00,
  `max_uses` int(10) UNSIGNED DEFAULT NULL,
  `used_count` int(10) UNSIGNED DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('DN76JMdPrtXpLSxgHYjBgsE5Nq909ncs1sbLBksx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid2JOSktSaVV3TkZiNG1BaWF2N2dObHdTUkIwcG42eXlmcGcxWGNnMyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9zYW5jdHVtL2NzcmYtY29va2llIjtzOjU6InJvdXRlIjtzOjE5OiJzYW5jdHVtLmNzcmYtY29va2llIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771874438),
('uG17wB3TnyaKRAb8W6X0sxZqgfM2REefcdZblc0F', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSFdYUXNkcnU3SWZSbjlubTJmUkxHcWtXWlJhUEhQSFRGOTFrMUtidiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9zYW5jdHVtL2NzcmYtY29va2llIjtzOjU6InJvdXRlIjtzOjE5OiJzYW5jdHVtLmNzcmYtY29va2llIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771926441),
('WlcCJiHxOV8jcOdDDeeQzwNpz9T9QW2cXtX4X9xS', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRGJTQ2hlMGFvSkVPU3NjTE9mV3JBamlRNm4zT0NLYzVsSEpjMzgwSCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9zYW5jdHVtL2NzcmYtY29va2llIjtzOjU6InJvdXRlIjtzOjE5OiJzYW5jdHVtLmNzcmYtY29va2llIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771929915);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` enum('user','admin','super_admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `phone`, `address`, `is_active`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`) VALUES
(1, 'ádas ádas', '0901123456', NULL, 1, 'taitran@gmail.com', NULL, '$2y$12$ygY8vCKBx2tP/SLl8zFYk.AJzAKcwepumhxNuDthiqkwffHpLq81q', NULL, '2026-02-04 10:24:09', '2026-02-04 10:24:09', 'user'),
(2, 'ấ ád', '12345789', NULL, 1, 'tai@gmail.com', NULL, '$2y$12$5FimWRjaTQf1tgznYjeae.WZHkmSRubkS.ILlRpoQCyWG89zLc7/.', NULL, '2026-02-04 10:35:55', '2026-02-04 10:35:55', 'user'),
(3, 'Test User', '0123456789', NULL, 1, 'test@example.com', NULL, '$2y$12$t9zCWhecMtfjSrxoc0Jd4ep22cJEjfDX0rrrCptQZevtp2cAT/hBC', NULL, '2026-02-05 09:17:14', '2026-02-05 10:16:04', 'user'),
(4, 'Tài Trần', '1230001234', NULL, 1, 'tai1@gmail.com', NULL, '$2y$12$7TYAZsWEAc6npBnGSTNhkefjPz0tipjgvmtHOOLJUXRkvEkZY4M2K', NULL, '2026-02-05 09:38:18', '2026-02-05 09:38:18', 'user'),
(5, 'nguyen a', '1234567890', NULL, 1, 'ab@gmail.com', NULL, '$2y$12$qLug.E2EP0yHtyKiTcrip.V62LO4VgrDHyUzXbdpJTnSiYU/ZLSKS', NULL, '2026-02-11 10:22:38', '2026-02-11 10:22:38', 'user'),
(6, 'Khách vãng lai', NULL, NULL, 1, 'guest@fivetech.vn', NULL, 'guest_password_hash', NULL, '2026-02-20 17:14:39', '2026-02-20 17:14:39', 'user'),
(8, 'Administrator', '0123456789', 'Ho Chi Minh City', 1, 'admin@gmail.com', '2026-02-24 12:08:28', '$2y$10$wH9gQ3PqVxJZkC2vXlE8wOZ5y6uQvYQz0kG8f5nGQ7xQ7l3zGk8pK', NULL, '2026-02-24 12:08:28', '2026-02-24 12:08:28', 'admin');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wishlists`
--

CREATE TABLE `wishlists` (
  `wishlist_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Chỉ mục cho bảng `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`session_id`,`product_id`,`variant_id`),
  ADD KEY `idx_cart_user` (`user_id`),
  ADD KEY `idx_cart_session` (`session_id`),
  ADD KEY `idx_cart_product` (`product_id`),
  ADD KEY ` fk_cart_variant` (`variant_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Chỉ mục cho bảng `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `promo_id` (`promo_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `variant_id` (`variant_id`),
  ADD KEY `idx_order` (`order_id`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_category` (`category_id`);

--
-- Chỉ mục cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`variant_id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_sku` (`sku`);

--
-- Chỉ mục cho bảng `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`promo_id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`);

--
-- Chỉ mục cho bảng `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Chỉ mục cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD UNIQUE KEY `unique_wish` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `variant_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `promotions`
--
ALTER TABLE `promotions`
  MODIFY `promo_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `wishlist_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT ` fk_cart_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`),
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`comment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`promo_id`) REFERENCES `promotions` (`promo_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `wishlists_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
