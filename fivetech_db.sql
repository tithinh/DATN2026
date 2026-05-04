-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 27, 2026 lúc 04:08 PM
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
-- Cấu trúc bảng cho bảng `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('super_admin','admin','manager','staff') DEFAULT 'admin',
  `is_active` tinyint(1) DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `email`, `password_hash`, `full_name`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$12$g8fi6hRXvnzTLbpSA56qp.MRCeH2JM6zzDJ.Y4z8p0DxszqYk.EUC', 'Super Admin', 'admin', 1, NULL, '2026-02-24 15:47:08', '2026-04-22 11:24:04'),
(4, 'testadmin', 'test@test.com', '$2y$12$6L.5Txq3CDRd2thY5G835OkWymbLy2Qu8geTlAS/eKIdDpT2M18lq', 'Test Admin', 'admin', 1, NULL, '2026-02-24 08:57:20', '2026-04-22 11:27:25');

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
(53, 1, NULL, 10, 4, 47, '2026-03-18 00:37:11', '2026-03-18 00:37:42'),
(60, 1, NULL, 9, 1, 2, '2026-04-13 03:57:57', '2026-04-13 03:58:03'),
(64, NULL, NULL, 9, 1, 1, '2026-04-13 05:37:41', '2026-04-13 05:37:41'),
(83, 32, NULL, 9, 1, 1, '2026-04-24 04:36:59', '2026-04-24 04:36:59');

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
(6, 9, 4, NULL, 'ád', 3, 'pending', '2026-02-23 02:33:06', '2026-02-23 02:33:06'),
(7, 15, 4, NULL, 'abc', 3, 'pending', '2026-02-26 02:38:03', '2026-02-26 02:38:03'),
(8, 10, 32, NULL, 'ok', 5, 'pending', '2026-04-22 10:38:42', '2026-04-22 10:06:06'),
(9, 10, 32, NULL, 'ok', 5, 'approved', '2026-04-22 10:37:00', '2026-04-22 10:06:07'),
(10, 10, 32, NULL, 'abc', NULL, 'pending', '2026-04-22 11:39:22', '2026-04-22 11:39:22'),
(11, 10, 32, NULL, 'abc', 5, 'approved', '2026-04-22 11:40:02', '2026-04-22 11:39:45'),
(12, 10, 32, NULL, 'abc', 5, 'approved', '2026-04-22 11:40:01', '2026-04-22 11:39:46'),
(13, 10, 32, NULL, 'abc', NULL, 'approved', '2026-04-22 11:55:44', '2026-04-22 11:55:27'),
(14, 9, 32, NULL, 'abc', NULL, 'approved', '2026-04-22 12:15:31', '2026-04-22 12:15:20'),
(15, 10, 32, 13, 'ok', NULL, 'approved', '2026-04-24 03:31:43', '2026-04-24 03:31:43'),
(16, 9, 1, 14, 'ok', NULL, 'approved', '2026-04-24 03:32:05', '2026-04-24 03:32:05');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comment_user`
--

CREATE TABLE `comment_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `comment_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `status` enum('pending','replied','spam') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `subject`, `content`, `status`, `created_at`, `updated_at`) VALUES
(1, 'thịnh', 'tithinh.ntt@gmail.com', '0869603267', 'warranty', 'Tôi muốn bảo hành', 'pending', '2026-03-11 23:07:23', '2026-03-11 23:07:23');

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
(5, '2026_01_28_172245_create_carts_table', 3),
(6, '2026_02_25_000001_add_session_id_to_cart_items_table', 4),
(7, '2026_02_25_205542_update_orders_table_add_customer_fields', 5),
(8, '2026_02_25_205543_add_customer_district_to_orders_table', 6),
(9, '2026_02_25_200000_create_news_table', 7),
(10, '2026_02_25_200001_create_contacts_table', 7),
(11, '2026_02_25_200002_add_social_columns_to_users_table', 8),
(13, '2026_02_26_000001_add_order_code_to_orders_table', 9),
(14, '2026_03_09_000001_add_product_id_to_order_items_table', 10),
(15, '2026_03_12_062239_update_orders_status_enum', 10),
(16, '2026_03_20_113144_add_session_id_to_cart_items_table', 11),
(17, '2026_03_27_102152_update_payment_method_enum_in_orders_table', 11),
(21, '2026_03_27_104536_update_admin_role_enum_in_admins_table', 12),
(22, '2026_03_27_131702_create_user_addresses_table', 12),
(23, '2026_03_30_000001_add_price_to_product_variants_table', 12);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `news`
--

CREATE TABLE `news` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'Tin tức',
  `author` varchar(255) NOT NULL DEFAULT 'Admin',
  `views` int(11) NOT NULL DEFAULT 0,
  `status` enum('published','draft') NOT NULL DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `news`
--

INSERT INTO `news` (`id`, `title`, `slug`, `excerpt`, `content`, `image`, `category`, `author`, `views`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Top 10 ốp lưng iPhone 15 Pro Max đáng mua nhất năm 2026', 'top-10-op-lung-iphone-15-pro-max-dang-mua-nhat-nam-2026', 'Khám phá những mẫu ốp lưng đẹp, bền, bảo vệ tốt nhất cho iPhone 15 Pro Max với đa dạng phong cách từ trong suốt, da cao cấp đến chống sốc quân sự.', 'Nội dung chi tiết về top 10', 'https://images.unsplash.com/photo-1512054502232-10a0a035d672?w=800&h=450&fit=crop', 'Đánh giá', 'Nguyễn Văn An', 2504, 'published', '2026-02-26 04:22:14', '2026-04-13 03:39:43'),
(2, 'Cách chọn tai nghe Bluetooth phù hợp với nhu cầu sử dụng', 'cach-chon-tai-nghe-bluetooth', 'Hướng dẫn chi tiết giúp bạn chọn tai nghe không dây phù hợp nhất với lifestyle của mình.', 'Hướng dẫn chi tiết cách chọn tai nghe Bluetooth...', 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&h=250&fit=crop', 'Hướng dẫn', 'Trần Thị B', 1800, 'published', '2026-02-26 04:22:14', '2026-02-26 04:22:14'),
(3, 'Apple ra mắt chuẩn sạc nhanh mới cho iPhone 16 Series', 'apple-sac-nhanh-iphone-16', 'Tìm hiểu về công nghệ sạc nhanh mới nhất từ Apple hỗ trợ lên đến 50W.', 'Apple vừa công bố...', 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=400&h=250&fit=crop', 'Tin tức', 'Lê Văn C', 1501, 'published', '2026-02-26 04:22:14', '2026-02-26 04:27:20'),
(4, 'So sánh pin dự phòng Anker vs Xiaomi: Đâu là lựa chọn tốt?', 'so-sanh-pin-du-phong-anker-xiaomi', 'Đánh giá chi tiết hai thương hiệu pin sạc dự phòng phổ biến nhất hiện nay.', 'So sánh chi tiết...', 'https://images.unsplash.com/photo-1585338107529-13afc5f02586?w=400&h=250&fit=crop', 'So sánh', 'Phạm Văn D', 1200, 'published', '2026-02-26 04:22:14', '2026-02-26 04:22:14'),
(5, '5 mẹo sử dụng sạc không dây hiệu quả nhất', 'meo-su-dung-sac-khong-day', 'Những mẹo đơn giản giúp bạn sạc không dây nhanh hơn và an toàn hơn.', 'Mẹo sử dụng sạc không dây...', 'https://images.unsplash.com/photo-1628815113969-0487917f26eb?w=400&h=250&fit=crop', 'Mẹo hay', 'Nguyễn Thị E', 900, 'published', '2026-02-26 04:22:14', '2026-02-26 04:22:14'),
(6, 'Review dây đeo Apple Watch Ultra: Có đáng tiền?', 'review-day-deo-apple-watch-ultra', 'Đánh giá chi tiết dây đeo chính hãng Apple sau 3 tháng sử dụng.', 'Đánh giá dây đeo Apple Watch Ultra...', 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=400&h=250&fit=crop', 'Đánh giá', 'Trần Văn F', 801, 'published', '2026-02-26 04:22:14', '2026-02-26 04:27:03'),
(7, 'JBL ra mắt loa Bluetooth Flip 7 với pin 15 giờ', 'jbl-flip-7-ra-mat', 'Phiên bản nâng cấp của dòng loa di động bán chạy nhất thế giới.', 'JBL vừa giới thiệu...', 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=400&h=250&fit=crop', 'Tin tức', 'Lê Thị G', 751, 'published', '2026-02-26 04:22:14', '2026-02-26 04:27:30'),
(8, 'Hướng dẫn sạc iPhone đúng cách để kéo dài tuổi thọ pin', 'huong-dan-sac-iphone-dung-cach', 'Những sai lầm khi sạc iPhone có thể làm giảm tuổi thọ pin nhanh chóng.', 'Hướng dẫn chi tiết...', 'https://images.unsplash.com/photo-1512054502232-10a0a035d672?w=400&h=250&fit=crop', 'Hướng dẫn', 'Nguyễn Văn H', 2000, 'published', '2026-02-26 04:22:14', '2026-02-26 04:22:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `order_id` int(10) UNSIGNED NOT NULL,
  `order_code` varchar(50) DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `promo_id` int(10) UNSIGNED DEFAULT NULL,
  `total_amount` decimal(12,2) DEFAULT NULL,
  `discount_amount` decimal(12,2) DEFAULT 0.00,
  `final_amount` decimal(12,2) DEFAULT NULL,
  `status` enum('pending','confirmed','processing','shipping','delivered','completed','cancelled') DEFAULT 'pending',
  `payment_method` enum('cod','bank','momo','vnpay','paypal','vietqr') DEFAULT 'cod',
  `payment_status` varchar(50) NOT NULL DEFAULT 'pending',
  `shipping_address` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `shipping_method` varchar(50) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `customer_district` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`order_id`, `order_code`, `user_id`, `promo_id`, `total_amount`, `discount_amount`, `final_amount`, `status`, `payment_method`, `payment_status`, `shipping_address`, `note`, `created_at`, `updated_at`, `shipping_method`, `customer_name`, `customer_phone`, `customer_email`, `customer_address`, `customer_district`, `deleted_at`) VALUES
(5, NULL, 4, NULL, 0.00, 0.00, NULL, 'cancelled', 'cod', 'pending', NULL, NULL, '2026-02-25 14:22:11', '2026-04-13 08:18:10', 'standard', 'Tài Trần', '123000', 'tai1@gmail.com', 'LA', NULL, NULL),
(6, NULL, 4, NULL, NULL, 0.00, NULL, 'pending', 'cod', 'pending', NULL, NULL, '2026-02-25 14:37:39', '2026-02-26 15:01:17', 'standard', 'Tài Trần', '123000', 'tai1@gmail.com', 'LA', NULL, NULL),
(7, NULL, NULL, NULL, NULL, 0.00, NULL, 'pending', 'cod', 'pending', NULL, NULL, '2026-02-26 10:48:40', '2026-02-26 10:48:40', 'standard', 'Tài Trần', '123000', 'tai1@gmail.com', 'LA', NULL, NULL),
(8, NULL, 4, NULL, NULL, 0.00, NULL, 'pending', 'cod', 'pending', NULL, NULL, '2026-02-26 11:10:16', '2026-02-26 18:11:15', 'standard', 'Tài Trần', '123000', 'tai1@gmail.com', 'LA', NULL, NULL),
(9, 'FT-20260226-0001', NULL, NULL, 0.00, 0.00, NULL, 'pending', 'cod', 'pending', NULL, NULL, '2026-02-26 11:32:43', '2026-02-26 11:32:43', 'standard', 'Tài Trần', '123000', 'tai1@gmail.com', 'LA', NULL, NULL),
(10, 'FT-20260309-0001', 1, NULL, 648000.00, 0.00, 648000.00, 'pending', 'cod', 'pending', '2000 le lai', NULL, '2026-03-09 05:34:53', '2026-03-09 05:34:53', NULL, 'adsd', '0919190190', 'tithinh.ntt@gmail.com', '2000 le lai', NULL, NULL),
(11, 'FT-20260309-0002', 1, NULL, 897000.00, 0.00, 897000.00, 'shipping', 'cod', 'pending', '190 lê lai', NULL, '2026-03-09 11:29:51', '2026-03-11 23:27:34', NULL, 'test 1', '0912035768', 'tithinh.ntt@gmail.com', '190 lê lai', NULL, NULL),
(12, 'FT-20260312-0001', 1, NULL, 598000.00, 0.00, 598000.00, 'shipping', 'cod', 'pending', NULL, NULL, '2026-03-11 23:33:49', '2026-03-12 06:38:25', NULL, 'Khách vãng lai', NULL, '', NULL, NULL, NULL),
(13, 'FT-20260312-0002', 1, NULL, 598000.00, 0.00, 598000.00, 'pending', 'cod', 'pending', NULL, NULL, '2026-03-12 06:43:04', '2026-03-12 06:43:04', NULL, 'Khách vãng lai', NULL, '', NULL, NULL, NULL),
(14, 'FT-20260318-0001', 1, NULL, 4700000.00, 0.00, 4700000.00, 'pending', 'cod', 'pending', '190 le lai', NULL, '2026-03-18 00:43:55', '2026-03-18 00:43:55', NULL, 'tien thinh', '0123435454', 'tithinh.ntt@gmail.com', '190 le lai', NULL, NULL),
(15, 'FT-20260318-0002', 1, NULL, 1349000.00, 0.00, 1349000.00, 'completed', 'cod', 'pending', NULL, NULL, '2026-03-18 00:49:31', '2026-03-18 00:54:45', NULL, 'Khách vãng lai', NULL, '', NULL, NULL, NULL),
(16, 'FT-20260412-0001', 31, NULL, 538000.00, 0.00, 538000.00, 'completed', 'cod', 'pending', 'Long An', NULL, '2026-04-12 08:48:14', '2026-04-22 10:05:30', NULL, 'Tai Tran', '0901107798', 'taihuulol01@gmail.com', 'Long An', NULL, NULL),
(17, 'FT-20260413-0001', 4, NULL, 19079000.00, 0.00, 19079000.00, 'pending', 'cod', 'pending', 'LA122222222222222', NULL, '2026-04-13 06:06:15', '2026-04-13 06:06:15', NULL, 'Tài Trần', '12300023232', 'tai1@gmail.com', 'LA122222222222222', NULL, NULL),
(18, 'FT-20260413-0002', 4, NULL, 538000.00, 0.00, 538000.00, 'pending', 'cod', 'pending', '12 HCM city', NULL, '2026-04-13 06:16:26', '2026-04-13 06:16:26', NULL, 'Tài Trần', '0902215443', 'tai1@gmail.com', '12 HCM city', NULL, NULL),
(19, 'FT-20260413-0003', 4, NULL, 259000.00, 0.00, 289000.00, 'pending', 'cod', 'pending', 'LA1', NULL, '2026-04-13 06:21:17', '2026-04-13 06:21:17', NULL, 'Tài Trần', '0902215443', 'tai1@gmail.com', 'LA1', NULL, NULL),
(20, 'FT-20260413-0004', 4, NULL, 110000.00, 0.00, 140000.00, 'shipping', 'vietqr', 'pending', 'LA1', NULL, '2026-04-13 06:22:38', '2026-04-13 06:23:08', NULL, 'Tài Trần', '0902215443', 'tai1@gmail.com', 'LA1', NULL, NULL),
(22, 'FT-20260413-0006', 4, NULL, 110000.00, 0.00, 140000.00, 'shipping', 'cod', 'pending', 'LA1', NULL, '2026-04-13 07:25:56', '2026-04-13 07:29:18', NULL, 'Tài Trần', '0902215443', 'tai1@gmail.com', 'LA1', NULL, NULL),
(23, 'FT-20260413-0007', 4, NULL, 259000.00, 0.00, 289000.00, 'pending', 'cod', 'pending', 'LA1', NULL, '2026-04-13 08:02:54', '2026-04-13 08:02:54', NULL, 'Tài Trần', '0902215443', 'tai1@gmail.com', 'LA1', NULL, NULL),
(24, 'FT-20260413-0008', 32, NULL, 259000.00, 0.00, 289000.00, 'cancelled', 'cod', 'pending', 'abc', NULL, '2026-04-13 08:04:00', '2026-04-13 08:04:15', NULL, 'Tài Trần', '0901106764', 'taithps39900@gmail.com', 'abc', NULL, '2026-04-13 08:04:15'),
(30, 'FT-20260413-0009', 32, NULL, 399000.00, 0.00, 399000.00, 'pending', 'cod', 'pending', 'abc', NULL, '2026-04-13 08:28:46', '2026-04-13 08:28:46', NULL, 'Tài Trần', '0901106764', 'taithps39900@gmail.com', 'abc', NULL, NULL),
(31, 'FT-20260413-0010', 32, NULL, 11780000.00, 0.00, 11780000.00, 'pending', 'vietqr', 'paid', 'abc', NULL, '2026-04-13 09:05:19', '2026-04-13 09:22:06', NULL, 'Tài Trần', '0901106764', 'taithps39900@gmail.com', 'abc', NULL, NULL),
(32, 'FT-20260416-0001', 32, NULL, 259000.00, 0.00, 289000.00, 'confirmed', 'cod', 'paid', 'abc', NULL, '2026-04-16 02:44:31', '2026-04-16 02:44:31', NULL, 'Tài Trần', '0901106764', 'taithps39900@gmail.com', 'abc', NULL, NULL),
(33, 'FT-20260416-0002', 32, NULL, 5890000.00, 0.00, 5890000.00, 'pending', 'cod', 'paid', 'abc', NULL, '2026-04-16 02:55:32', '2026-04-16 02:55:32', NULL, 'Tài Trần', '0901106764', 'taithps39900@gmail.com', 'abc', NULL, NULL),
(34, 'FT-20260416-0003', 32, NULL, 950000.00, 0.00, 950000.00, 'pending', 'vietqr', 'paid', 'abc', NULL, '2026-04-16 03:01:20', '2026-04-16 03:01:31', NULL, 'Tài Trần', '0901106764', 'taithps39900@gmail.com', 'abc', NULL, NULL),
(35, 'FT-20260416-0004', 32, NULL, 350000.00, 0.00, 350000.00, 'completed', 'cod', 'paid', 'abc', NULL, '2026-04-16 03:42:59', '2026-04-22 10:05:48', NULL, 'Tài Trần', '0901106764', 'taithps39900@gmail.com', 'abc', NULL, NULL),
(36, 'FT-20260423-0001', 32, NULL, 259000.00, 0.00, 289000.00, 'pending', 'vietqr', 'paid', 'abc', NULL, '2026-04-22 23:39:00', '2026-04-22 23:39:23', NULL, 'Tài Trần', '0901106764', 'taithps39900@gmail.com', 'abc', NULL, NULL),
(37, 'FT-20260424-0001', 32, NULL, 259000.00, 0.00, 289000.00, 'pending', 'cod', 'paid', 'abc', NULL, '2026-04-24 04:28:19', '2026-04-24 04:28:19', NULL, 'Tài Trần', '0901106764', 'taithps39900@gmail.com', 'abc', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED DEFAULT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `variant_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `price_at_purchase` decimal(12,2) DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `variant_id`, `quantity`, `price_at_purchase`, `rating`, `comment`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 8, NULL, 1, 1, NULL, NULL, NULL, '2026-02-25 14:22:11', '2026-02-25 14:22:11', NULL),
(2, 8, NULL, 1, 1, NULL, NULL, NULL, '2026-02-25 14:37:39', '2026-02-25 14:37:39', NULL),
(3, NULL, NULL, 11, 1, NULL, NULL, NULL, '2026-02-26 10:48:40', '2026-02-26 10:48:40', NULL),
(4, NULL, NULL, 3, 1, NULL, NULL, NULL, '2026-02-26 10:48:40', '2026-02-26 10:48:40', NULL),
(5, NULL, NULL, 4, 1, NULL, NULL, NULL, '2026-02-26 10:48:40', '2026-02-26 10:48:40', NULL),
(6, NULL, NULL, 11, 1, NULL, NULL, NULL, '2026-02-26 11:10:16', '2026-02-26 11:10:16', NULL),
(7, NULL, NULL, 1, 1, NULL, NULL, NULL, '2026-02-26 11:32:43', '2026-02-26 11:32:43', NULL),
(8, 10, 9, 1, 1, 249000.00, NULL, NULL, '2026-03-09 05:34:53', '2026-03-09 05:34:53', NULL),
(9, 10, 11, 7, 1, 399000.00, NULL, NULL, '2026-03-09 05:34:53', '2026-03-09 05:34:53', NULL),
(10, 11, 9, 1, 1, 249000.00, NULL, NULL, '2026-03-09 11:29:51', '2026-03-09 11:29:51', NULL),
(11, 11, 11, 7, 1, 399000.00, NULL, NULL, '2026-03-09 11:29:51', '2026-03-09 11:29:51', NULL),
(12, 11, 9, 1, 1, 249000.00, NULL, NULL, '2026-03-09 11:29:51', '2026-03-09 11:29:51', NULL),
(13, 12, 10, 4, 1, 199000.00, NULL, NULL, '2026-03-11 23:33:49', '2026-03-11 23:33:49', NULL),
(14, 12, 11, 7, 1, 399000.00, NULL, NULL, '2026-03-11 23:33:49', '2026-03-11 23:33:49', NULL),
(15, 13, 10, 4, 1, 199000.00, NULL, NULL, '2026-03-12 06:43:04', '2026-03-12 06:43:04', NULL),
(16, 13, 11, 7, 1, 399000.00, NULL, NULL, '2026-03-12 06:43:04', '2026-03-12 06:43:04', NULL),
(17, 14, 10, 4, 47, 100000.00, NULL, NULL, '2026-03-18 00:43:55', '2026-03-18 00:43:55', NULL),
(18, 15, 11, 7, 1, 399000.00, NULL, NULL, '2026-03-18 00:49:31', '2026-03-18 00:49:31', NULL),
(19, 15, 12, 9, 1, 950000.00, NULL, NULL, '2026-03-18 00:49:31', '2026-03-18 00:49:31', NULL),
(20, 16, 9, 3, 2, 269000.00, NULL, NULL, '2026-04-12 08:48:14', '2026-04-12 08:48:14', NULL),
(21, 17, 14, 13, 1, 6900000.00, NULL, NULL, '2026-04-13 06:06:15', '2026-04-13 06:06:15', NULL),
(22, 17, 13, 11, 2, 5890000.00, NULL, NULL, '2026-04-13 06:06:15', '2026-04-13 06:06:15', NULL),
(23, 17, 11, 7, 1, 399000.00, NULL, NULL, '2026-04-13 06:06:15', '2026-04-13 06:06:15', NULL),
(24, 18, 9, 3, 2, 269000.00, NULL, NULL, '2026-04-13 06:16:27', '2026-04-13 06:16:27', NULL),
(25, 19, 9, 1, 1, 259000.00, NULL, NULL, '2026-04-13 06:21:17', '2026-04-13 06:21:17', NULL),
(26, 20, 10, 4, 1, 110000.00, NULL, NULL, '2026-04-13 06:22:38', '2026-04-13 06:22:38', NULL),
(30, 22, 10, 4, 1, 110000.00, NULL, NULL, '2026-04-13 07:25:56', '2026-04-13 07:25:56', NULL),
(31, 23, 9, 1, 1, 259000.00, NULL, NULL, '2026-04-13 08:02:54', '2026-04-13 08:02:54', NULL),
(32, 24, 9, 1, 1, 259000.00, NULL, NULL, '2026-04-13 08:04:00', '2026-04-13 08:04:15', '2026-04-13 08:04:15'),
(33, 30, 11, 7, 1, 399000.00, NULL, NULL, '2026-04-13 08:28:46', '2026-04-13 08:28:46', NULL),
(34, 31, 13, 11, 2, 5890000.00, NULL, NULL, '2026-04-13 09:05:19', '2026-04-13 09:05:19', NULL),
(35, 32, 9, 1, 1, 259000.00, NULL, NULL, '2026-04-16 02:44:31', '2026-04-16 02:44:31', NULL),
(36, 33, 13, 11, 1, 5890000.00, NULL, NULL, '2026-04-16 02:55:32', '2026-04-16 02:55:32', NULL),
(37, 34, 12, 9, 1, 950000.00, NULL, NULL, '2026-04-16 03:01:20', '2026-04-16 03:01:20', NULL),
(38, 35, 10, 4, 1, 110000.00, NULL, NULL, '2026-04-16 03:42:59', '2026-04-16 03:42:59', NULL),
(39, 35, 10, 6, 2, 120000.00, NULL, NULL, '2026-04-16 03:42:59', '2026-04-16 03:42:59', NULL),
(40, 36, 9, 1, 1, 259000.00, NULL, NULL, '2026-04-22 23:39:00', '2026-04-22 23:39:00', NULL),
(41, 37, 9, 1, 1, 259000.00, NULL, NULL, '2026-04-24 04:28:19', '2026-04-24 04:28:19', NULL);

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
(18, 'App\\Models\\User', 4, 'auth_token', 'b637a83bd7b167fa6b146c9bbe2aa92e5d634f703b42645b263bb38aa1ba2331', '[\"*\"]', '2026-02-23 03:31:33', NULL, '2026-02-12 07:54:22', '2026-02-23 03:31:33'),
(19, 'App\\Models\\User', 4, 'auth_token', '86367e1d92adbf76ce24747729370051883a55aad79ba38a2b0b375f23e09de7', '[\"*\"]', '2026-02-25 10:04:12', NULL, '2026-02-24 09:44:14', '2026-02-25 10:04:12'),
(21, 'App\\Models\\User', 4, 'auth_token', '829cf5fe10945b122eadcfcb7730d3f96ce4ace9456e99935d5bc07329c3ae28', '[\"*\"]', '2026-02-25 11:24:55', NULL, '2026-02-25 11:11:50', '2026-02-25 11:24:55'),
(22, 'App\\Models\\User', 4, 'auth_token', '5530a3e1a30405efa94ba39230b901be9751e7d975e6450d6eb4c47725cc011e', '[\"*\"]', '2026-02-26 03:41:36', NULL, '2026-02-25 11:57:18', '2026-02-26 03:41:36'),
(23, 'App\\Models\\User', 4, 'auth_token', '5fe0373a48664d36735035aa23e63cf72cb9f2976b7765c2052ef750365af416', '[\"*\"]', '2026-02-26 07:34:32', NULL, '2026-02-26 03:50:19', '2026-02-26 07:34:32'),
(24, 'App\\Models\\User', 4, 'auth_token', '932b1a447f5dd9cd9d9cf8f95898f9582320a12a40718497f761e98e590bd49f', '[\"*\"]', '2026-02-26 11:35:20', NULL, '2026-02-26 07:58:50', '2026-02-26 11:35:20'),
(25, 'App\\Models\\User', 26, 'auth_token', '73d184b2433d150eadb315c6a6c94bdf98beb6b9fd0c02701b90c6c1db49b989', '[\"*\"]', '2026-03-09 05:45:58', NULL, '2026-03-09 05:45:51', '2026-03-09 05:45:58'),
(27, 'App\\Models\\User', 28, 'auth_token', 'e0d4d45a751c9d52b0fc889a32272ac3ddb206f74299b9bd8aeb589c8cc1f022', '[\"*\"]', '2026-03-09 11:19:56', NULL, '2026-03-09 11:19:16', '2026-03-09 11:19:56'),
(28, 'App\\Models\\User', 28, 'auth_token', '827ff3ef0c4908056d6aceae47c9864487597e689488e4195713d7b92ce7a62f', '[\"*\"]', '2026-03-09 11:21:31', NULL, '2026-03-09 11:21:06', '2026-03-09 11:21:31'),
(29, 'App\\Models\\Admin', 1, 'admin-token', 'db69043c258dea553d59ead3b1d968a5676bc79bf1b72f7d6c0d35d098b5ae29', '[\"*\"]', '2026-03-29 06:52:16', NULL, '2026-03-09 11:29:07', '2026-03-29 06:52:16'),
(30, 'App\\Models\\Admin', 1, 'test', 'bc81d653e5724845f122b9dc088eed75e17ba06456f6e46123e4e2697c7de88c', '[\"*\"]', '2026-03-09 12:39:58', NULL, '2026-03-09 12:24:07', '2026-03-09 12:39:58'),
(31, 'App\\Models\\User', 29, 'auth_token', '33471674e5d8756f04cb3070d0a01788bfd505782a2427ed200fbf8e94dd76dd', '[\"*\"]', '2026-03-11 22:57:39', NULL, '2026-03-11 22:57:33', '2026-03-11 22:57:39'),
(32, 'App\\Models\\User', 29, 'auth_token', '436567af356f8d2f3595a30cdc989320131e246d31d88bddd18a7cd3efa77ada', '[\"*\"]', '2026-03-18 00:26:10', NULL, '2026-03-11 22:57:58', '2026-03-18 00:26:10'),
(33, 'App\\Models\\User', 30, 'auth_token', '571f2cd63caaa8f15c2dc78b81b9528c3721d3d9f44a6583a31a0a0dc94faa93', '[\"*\"]', '2026-03-18 00:56:43', NULL, '2026-03-18 00:48:04', '2026-03-18 00:56:43'),
(34, 'App\\Models\\User', 26, 'auth_token', 'd9d21c45293c4ca3fb9bee68ca82fcab373d43f91106341cf8de72760d9d7f04', '[\"*\"]', '2026-03-29 06:52:33', NULL, '2026-03-29 06:33:01', '2026-03-29 06:52:33'),
(35, 'App\\Models\\User', 31, 'auth_token', '6a63f6625a6b9a6d283f37e92d57cb8341aefe954584d4594422e486fe49292a', '[\"*\"]', '2026-04-12 09:30:22', NULL, '2026-04-12 08:36:52', '2026-04-12 09:30:22'),
(36, 'App\\Models\\Admin', 1, 'admin-token', '4883a3df7f58218962d0cc8a22c7ad2b23602abe19c39368024610b91f6d5e56', '[\"*\"]', '2026-04-22 23:42:23', NULL, '2026-04-12 08:43:06', '2026-04-22 23:42:23'),
(37, 'App\\Models\\User', 32, 'auth_token', 'ffc232217dbd10966132a2eea37c9c38ac6d7d6c750a983f61cf6cc34086a79e', '[\"*\"]', '2026-04-13 03:26:24', NULL, '2026-04-12 09:36:59', '2026-04-13 03:26:24'),
(38, 'App\\Models\\User', 4, 'auth_token', 'ada9bacf8aa69815602748ce215be8a27bddf7c9ba907951c9b02251e5941989', '[\"*\"]', '2026-04-13 03:27:03', NULL, '2026-04-13 03:26:53', '2026-04-13 03:27:03'),
(39, 'App\\Models\\User', 32, 'auth_token', '24c67c4313103349790f347e3dcea53efc5dcb088deeaba03e81447ee37ec645', '[\"*\"]', '2026-04-13 05:48:16', NULL, '2026-04-13 03:59:26', '2026-04-13 05:48:16'),
(40, 'App\\Models\\User', 4, 'auth_token', '19150722cc6db6951458089e9a3761d0037a90f8ca26c07c3999e7a66f2c7654', '[\"*\"]', '2026-04-13 08:03:01', NULL, '2026-04-13 05:48:32', '2026-04-13 08:03:01'),
(41, 'App\\Models\\User', 32, 'auth_token', '16b9b0fd855b7cd69adc71aec6c032cf0c791440b4c94f9060421198c4c8701b', '[\"*\"]', '2026-04-22 23:48:32', NULL, '2026-04-13 08:03:14', '2026-04-22 23:48:32'),
(42, 'App\\Models\\Admin', 1, 'admin-token', '041b35189f0991fb59cb8333eba4beba6681e420a749058878108ad6e0c2a6f0', '[\"*\"]', '2026-04-23 11:58:43', NULL, '2026-04-23 11:58:40', '2026-04-23 11:58:43'),
(43, 'App\\Models\\Admin', 1, 'admin-token', 'c77011d07f2ceae7c6a92c1ed276017f641db5f451946776a21dfd90f5e515c6', '[\"*\"]', '2026-04-23 12:06:02', NULL, '2026-04-23 12:05:58', '2026-04-23 12:06:02'),
(44, 'App\\Models\\Admin', 1, 'admin-token', 'ea508417440e28da2a3e28511d4da9af1f73350a70bbb25ed95e612f08c9cc65', '[\"*\"]', '2026-04-23 12:07:32', NULL, '2026-04-23 12:07:07', '2026-04-23 12:07:32'),
(45, 'App\\Models\\Admin', 1, 'admin-token', '0257edef80f02421503ae5eb6f1caf8fe5e9418b568747ce1d4b2eed901b08a9', '[\"*\"]', '2026-04-24 04:32:10', NULL, '2026-04-24 02:43:36', '2026-04-24 04:32:10'),
(46, 'App\\Models\\User', 32, 'auth_token', 'a60089b18d71e2162577d01c812ae0a331fa08f8cae2c0cbfb8de53d451f64ab', '[\"*\"]', '2026-04-24 04:58:24', NULL, '2026-04-24 03:27:08', '2026-04-24 04:58:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
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

INSERT INTO `products` (`product_id`, `name`, `thumbnail`, `slug`, `description`, `short_desc`, `base_price`, `discount_price`, `stock_total`, `likes_count`, `category_id`, `is_visible`, `is_featured`, `created_at`, `updated_at`) VALUES
(9, 'Cáp sạc Lightning Anker PowerLine', NULL, 'cap-sac-lightning-anker-powerline', 'Cáp sạc Lightning chính hãng Anker PowerLine, lõi Kevlar siêu bền, hỗ trợ sạc nhanh và truyền dữ liệu ổn định.', 'Cáp sạc Lightning Anker siêu bền', 290000.00, 249000.00, 108, 15, 1, 1, 1, '2026-01-28 17:06:39', '2026-04-12 22:56:21'),
(10, 'Cáp sạc Type-C Baseus 60W', 'products/z0c9mPxVywLM1eR1E5OPTdSOkX317uNXEUg1JkIS.webp', 'cap-sac-type-c-baseus-60w', 'Cáp sạc Type-C Baseus hỗ trợ sạc nhanh Power Delivery 60W, phù hợp laptop và điện thoại.', 'Cáp Type-C sạc nhanh 60W', 250000.00, 100000.00, 85, 9, 1, 1, 0, '2026-01-28 17:06:39', '2026-04-12 22:58:42'),
(11, 'Pin dự phòng Xiaomi 10000mAh', 'products/KcnuYVsygPKJQdIbyhQbXDsgTt3Qt9l0aeqgTvjr.png', 'pin-du-phong-xiaomi-10000mah', 'Pin dự phòng Xiaomi dung lượng 10000mAh, hỗ trợ sạc nhanh 18W, thiết kế gọn nhẹ.', 'Pin dự phòng Xiaomi 10000mAh', 450000.00, 399000.00, 76, 22, 2, 1, 1, '2026-01-28 17:06:39', '2026-04-12 23:01:01'),
(12, 'Pin dự phòng Anker PowerCore 20000mAh', 'products/Kx99CfwRwuUgg0fZBMmZ9QFfwiQD7ytGO0cslmp7.png', 'pin-du-phong-anker-20000mah', 'Pin dự phòng Anker PowerCore dung lượng lớn 20000mAh, sạc nhanh và an toàn.', 'Pin dự phòng Anker 20000mAh', 950000.00, NULL, 59, 11, 2, 1, 0, '2026-01-28 17:06:39', '2026-04-12 23:20:07'),
(13, 'Tai nghe Bluetooth AirPods Pro 2', 'products/7DXn9QfklWXJoXui1njGSG4OCXQFoiawF0MDW8wT.png', 'tai-nghe-airpods-pro-2', 'AirPods Pro 2 với chống ồn chủ động, âm thanh cao cấp và chip H2 mới.', 'AirPods Pro 2 chống ồn', 6200000.00, 5890000.00, 35, 30, 3, 1, 1, '2026-01-28 17:06:39', '2026-04-12 23:24:14'),
(14, 'Tai nghe Sony WF-1000XM5', 'products/ZhFqXjC5aQylTixN9tJU9uauVoTOEaiAXIu2IgO1.png', 'tai-nghe-sony-wf-1000xm5', 'Tai nghe true wireless Sony WF-1000XM5 với công nghệ chống ồn hàng đầu.', 'Tai nghe Sony chống ồn cao cấp', 6900000.00, NULL, 35, 18, 3, 1, 0, '2026-01-28 17:06:39', '2026-04-12 23:26:23'),
(15, 'Ốp lưng iPhone 15 Pro Silicon', 'products/UJHl1tcXihuuA3gt0BXzCwEZDeOjixuuq92KDCjn.jpg', 'op-lung-iphone-15-pro-silicon', 'Ốp lưng silicon mềm cho iPhone 15 Pro, chống sốc và chống trầy hiệu quả.', 'Ốp lưng iPhone 15 Pro', 290000.00, 199000.00, 200, 12, 4, 1, 0, '2026-01-28 17:06:39', '2026-04-12 23:33:30'),
(16, 'Ốp lưng Samsung S24 Ultra trong suốt', 'products/ClVmL0Gfp6EB6aicx1dzA6T3TVCJGZTDCUI4HLOA.webp', 'op-lung-samsung-s24-ultra', 'Ốp lưng trong suốt cho Samsung S24 Ultra, thiết kế mỏng nhẹ.', 'Ốp lưng S24 Ultra', 250000.00, NULL, 180, 7, 4, 1, 0, '2026-01-28 17:06:39', '2026-04-12 23:35:54');

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
  `price` decimal(15,2) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `stock` int(10) UNSIGNED DEFAULT 0,
  `image_urls` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_variants`
--

INSERT INTO `product_variants` (`variant_id`, `product_id`, `color`, `storage_size`, `name`, `price_extra`, `price`, `sku`, `stock`, `image_urls`, `created_at`, `updated_at`) VALUES
(1, 9, 'Trắng', NULL, NULL, 10000.00, NULL, NULL, 35, '[\"variants/cap-anker-1m-trang.jpg\"]', '2026-02-04 16:51:26', '2026-04-24 04:28:19'),
(2, 9, 'Đen', NULL, NULL, 10000.00, NULL, NULL, 40, '\"[\\\"variants\\\\\\/lOGbJ2qtlIszreT3lZ9YFy9y6rCCzS2FdjjQmQXu.png\\\"]\"', '2026-02-04 16:51:26', '2026-04-12 22:44:12'),
(3, 9, 'Đen 2m', NULL, NULL, 20000.00, NULL, NULL, 26, '[\"variants/cap-anker-1m-den.jpg\"]', '2026-02-04 16:51:26', '2026-04-13 07:26:43'),
(4, 10, 'Đen', NULL, NULL, 10000.00, NULL, NULL, 2, '\"[\\\"variants\\\\\\/vIbXDnGmJyUTTZKXFZ6KpjOtsHq0dHsBwxc4EGp5.webp\\\"]\"', '2026-02-04 16:51:34', '2026-04-16 03:42:59'),
(5, 10, 'Đen 1m5', NULL, NULL, 20000.00, NULL, NULL, 40, '\"[\\\"variants\\\\\\/pNz2PO3uYMggOqlqy4BIImrdXEJ5Q76RQlY1TO0O.webp\\\"]\"', '2026-02-04 16:51:34', '2026-04-12 22:48:59'),
(6, 10, 'Đỏ', NULL, NULL, 20000.00, NULL, NULL, 38, '\"[\\\"variants\\\\\\/JcNPFAZVNHu9W1dF6IS91aguVXUMXpA1uWUVcSA8.png\\\"]\"', '2026-02-04 16:51:34', '2026-04-16 03:42:59'),
(7, 11, 'Đen', '10000mAh', NULL, 0.00, NULL, NULL, 34, '\"[\\\"variants\\\\\\/J7NYdgSYPBUhyRJQcKpRLZWjpDvrjtqef2fjhwjq.png\\\"]\"', '2026-02-04 16:51:44', '2026-04-13 08:28:46'),
(8, 11, 'Trắng', '10000mAh', NULL, 0.00, NULL, NULL, 40, '\"[\\\"variants\\\\\\/Mv47gjrGEE7KWdCIlU4geeY2G0lyeKDCIv6nxik5.png\\\"]\"', '2026-02-04 16:51:44', '2026-04-12 23:01:01'),
(9, 12, 'Đen', '20000mAh', NULL, 0.00, NULL, NULL, 28, '\"[\\\"variants\\\\\\/dwMBTXwQ6yRcXrI3iW7qGyBHmMJGiE0oXowZRbx8.png\\\"]\"', '2026-02-04 16:51:50', '2026-04-16 03:01:20'),
(10, 12, 'Xanh', '20000mAh', NULL, 0.00, NULL, NULL, 30, '\"[\\\"variants\\\\\\/xmdQok78XrYmifHDyqWi47AgXuliulYwUHI2KPk6.png\\\"]\"', '2026-02-04 16:51:50', '2026-04-12 23:20:07'),
(11, 13, 'Trắng', NULL, NULL, 0.00, NULL, NULL, 15, '\"[\\\"variants\\\\\\/l9Lf3jyT5c4vy4GWshYi0mpUWUBTE5A0VoP4sw7y.png\\\"]\"', '2026-02-04 16:51:59', '2026-04-16 02:55:32'),
(12, 13, 'Đen', NULL, NULL, 100000.00, NULL, NULL, 15, '\"[\\\"variants\\\\\\/gAEMEREsZcPdBIUTDDL3upJpCAogvq9q1RNWMCTN.webp\\\"]\"', '2026-02-04 16:51:59', '2026-04-12 23:24:14'),
(13, 14, 'Đen', NULL, NULL, 0.00, NULL, NULL, 19, '\"[\\\"variants\\\\\\/BDw0Bxtr4TCvNjQz7fg7TpmXk79vStqg8UhIm4uW.png\\\"]\"', '2026-02-04 16:52:09', '2026-04-13 06:06:15'),
(14, 14, 'Bạc', NULL, NULL, 0.00, NULL, NULL, 15, '\"[\\\"variants\\\\\\/4JowI6NaRzF6Vkw1vRhkpRyzhwwTpekyNcI80BYI.webp\\\"]\"', '2026-02-04 16:52:09', '2026-04-12 23:26:23'),
(15, 15, 'Đen', NULL, NULL, 0.00, NULL, NULL, 70, '\"[\\\"variants\\\\\\/hP4jy1neghVf5hjNL4Yl5yonM5ajiCjS2Z4ZEDZ9.jpg\\\"]\"', '2026-02-04 16:52:27', '2026-04-12 23:33:30'),
(16, 15, 'Xanh', NULL, NULL, 0.00, NULL, NULL, 70, '\"[\\\"variants\\\\\\/qukcyNo00mNQLzJa2I0R24ZzUydBHk11AKyhgpO6.webp\\\"]\"', '2026-02-04 16:52:27', '2026-04-12 23:33:30'),
(17, 15, 'Hồng', NULL, NULL, 0.00, NULL, NULL, 60, '\"[\\\"variants\\\\\\/O5rDS9U2dfxcgnwbokVfosshSf84ZB5nAgvXbodr.webp\\\"]\"', '2026-02-04 16:52:27', '2026-04-12 23:33:30'),
(18, 16, 'Trong suốt', NULL, NULL, 0.00, NULL, NULL, 90, '\"[\\\"variants\\\\\\/3uYqwNEUjRxTClXmwFXxuGNK59EEvaqaOzYRxE0u.webp\\\"]\"', '2026-02-04 16:52:35', '2026-04-12 23:35:54'),
(19, 16, 'Đen', NULL, NULL, 0.00, NULL, NULL, 90, '\"[\\\"variants\\\\\\/SVFCyU08zquQUV2o98R4cUABTB7yMnMw4GIy1MrT.webp\\\"]\"', '2026-02-04 16:52:35', '2026-04-12 23:35:54');

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

--
-- Đang đổ dữ liệu cho bảng `promotions`
--

INSERT INTO `promotions` (`promo_id`, `code`, `promo_type`, `discount_value`, `min_order_amount`, `max_uses`, `used_count`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'SALE20', 'percentage', 20.00, 200000.00, 5, 0, '2026-04-20', '2026-04-30', 1, '2026-03-11 23:12:58', '2026-04-24 04:27:31');

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
('3HmxZqQXSfPgDxvJkLVZXu0rLIBErIXB1eOuVdAD', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 OPR/130.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiU0FIWEVSTkZpRllWY2NQb1U4akdzMGdtbHMydFQwcExxNzRDT3U4USI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hcGkvdjEvcHJvbW90aW9ucy9hdmFpbGFibGU/c3VidG90YWw9MjU5MDAwIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjU6InN0YXRlIjtzOjQwOiJSY2gxTG01Wkx0elluRW1ualZzY2ZFcERiSXlmV05sV0lWNWhnVGIyIjt9', 1777031904);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `facebook_id` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` enum('user','admin','super_admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `phone`, `address`, `avatar`, `birthday`, `gender`, `google_id`, `facebook_id`, `is_active`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`) VALUES
(1, 'Khách vãng lai', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', NULL, NULL, NULL, '2026-02-20 17:14:39', '2026-02-20 17:14:39', 'user'),
(2, 'ấ ád', '12345789', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'tai@gmail.com', NULL, '$2y$12$5FimWRjaTQf1tgznYjeae.WZHkmSRubkS.ILlRpoQCyWG89zLc7/.', NULL, '2026-02-04 10:35:55', '2026-02-04 10:35:55', 'user'),
(3, 'Test User', '0123456789', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'test@example.com', NULL, '$2y$12$t9zCWhecMtfjSrxoc0Jd4ep22cJEjfDX0rrrCptQZevtp2cAT/hBC', NULL, '2026-02-05 09:17:14', '2026-02-05 10:16:04', 'user'),
(4, 'Tài Trần', '0902215443', 'LA1', NULL, '2005-12-12', 'male', NULL, NULL, 1, 'tai1@gmail.com', NULL, '$2y$12$7TYAZsWEAc6npBnGSTNhkefjPz0tipjgvmtHOOLJUXRkvEkZY4M2K', NULL, '2026-02-05 09:38:18', '2026-04-13 06:15:45', 'user'),
(5, 'nguyen a', '1234567890', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'ab@gmail.com', NULL, '$2y$12$qLug.E2EP0yHtyKiTcrip.V62LO4VgrDHyUzXbdpJTnSiYU/ZLSKS', NULL, '2026-02-11 10:22:38', '2026-02-11 10:22:38', 'user'),
(19, 'Tài Trần', '123000', 'LA', NULL, NULL, NULL, NULL, NULL, 0, 'guest_DvX69mP6@fivetech.vn', NULL, '$2y$12$hHmQ2REQzdrnoE2vygtc.eIXdDONrOVcNg8Aeh7Hd9r2McIf53VlO', NULL, '2026-02-25 14:22:11', '2026-02-25 14:22:11', 'user'),
(22, 'Tài Trần', '123000', 'LA', NULL, NULL, NULL, NULL, NULL, 0, 'guest_GdZLGk47@fivetech.vn', NULL, '$2y$12$4ct3HYoZwNlxqoKjZyBDve7cyokFC/CNN/wwAtIxUDS51FpZuuQBu', NULL, '2026-02-25 14:37:39', '2026-02-25 14:37:39', 'user'),
(23, 'Tài Trần', '123000', 'LA', NULL, NULL, NULL, NULL, NULL, 0, 'guest_ToDRiKLu@fivetech.vn', NULL, '$2y$12$2siPzZOruH.xdSnffDZbHuv46Y01TQ2hGFkDtCdb2G.hoKX.X3F8.', NULL, '2026-02-26 10:48:39', '2026-02-26 10:48:39', 'user'),
(24, 'Tài Trần', '123000', 'LA', NULL, NULL, NULL, NULL, NULL, 0, 'guest_EmU6a1Jc@fivetech.vn', NULL, '$2y$12$dyqNDXPxnDz.DmsXcZX3zeF5GFoJMxhoGXqKqDFuXNSEnOaXdIl7.', NULL, '2026-02-26 11:10:16', '2026-02-26 11:10:16', 'user'),
(25, 'Tài Trần', '123000', 'LA', NULL, NULL, NULL, NULL, NULL, 0, 'guest_MKce5LGe@fivetech.vn', NULL, '$2y$12$gwfAITMAI9KS.l0SXU3Oa.2icwY67NwpdohQmqZLaNoEa3y4.yhv.', NULL, '2026-02-26 11:32:43', '2026-02-26 11:32:43', 'user'),
(26, 'tithinh nguyen', '0869603267', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocLJre3lbQCgr6LThYlikoB01e8SrBpneSiHQaM0fDnWVT0q1SX0=s96-c', NULL, NULL, '117886623940499227332', NULL, 1, 'tithinh.ntt@gmail.com', NULL, '$2y$12$vbDgLKQXxv9wazI3MkLILu.FON4IPIcnvmNWu5EbiMNZvLCOGsSb.', NULL, '2026-03-09 05:45:51', '2026-03-29 06:33:01', 'super_admin'),
(27, NULL, '0123456789', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'admin@gmail.com', NULL, '$2y$12$QgTn4FS06oEP5oCen24wR.n95gA/fzc1jSxBeXBWl0fm3VUFGgJ.W', NULL, '2026-03-09 05:50:16', '2026-03-09 05:50:16', 'admin'),
(28, 'tithinh A', '0869603267', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'abc@gmail.com', NULL, '$2y$12$k0.E9v5Nl4Nzkn/PDoUA5e.XBCVOfGrlb4VmbXWTVYH0MpqCIiMku', NULL, '2026-03-09 11:18:38', '2026-03-09 11:18:38', 'admin'),
(29, 'tien nguyen', '0869603268', 'action-btn delete', NULL, '2000-02-05', 'male', NULL, NULL, 1, 'thinh0989667669@gmail.com', NULL, '$2y$12$ysr3u5mwtVpC.D4qMdWP4e2KlAJ1CKsCuDQgVdOztzZ0tHgp46qiS', NULL, '2026-03-11 22:57:33', '2026-03-12 06:42:35', 'user'),
(30, 'thinh nguyen', '0869603267', '40 Lý Thường Kiệt', NULL, '2000-02-05', 'male', NULL, NULL, 1, 'thinh@gmail.com', NULL, '$2y$12$V9CREsG4E/RtU9NioNe4nehjbNX7zZyvuDaiEfmEXyvzRCEY/i7W2', NULL, '2026-03-18 00:48:04', '2026-03-18 00:48:56', 'user'),
(31, 'Tai Tran', '0901107798', 'Long An', 'https://lh3.googleusercontent.com/a/ACg8ocIPDCPt-a5QIfkuBv5TOaCYGHSvDoPvNZ03hp7jCiLrjGbBmT0P=s96-c', '2005-03-11', 'male', '106872877244329613350', NULL, 1, 'taihuulol01@gmail.com', NULL, '$2y$12$TgmO3HEkCZg2Xa4wLQalAunih8qLUbILHF3o5vMiu8tS6LGqq2DF2', NULL, '2026-04-12 08:36:52', '2026-04-12 08:41:47', 'user'),
(32, 'Tài Trần', '0901106764', 'abc', 'https://lh3.googleusercontent.com/a/ACg8ocKatvql3tjVPFXgPghATque-9zUhV_Re1bjKEVOK6kH1aOkFQ=s96-c', '2005-12-12', 'male', '107787290576646875126', NULL, 1, 'taithps39900@gmail.com', NULL, '$2y$12$yeEIrL21YtYMPk5GgcQ.muNW2nx8cZU4DiBhruNG04yiW61B.NiZ2', NULL, '2026-04-12 09:36:59', '2026-04-13 03:25:40', 'user');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(500) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wishlists`
--

CREATE TABLE `wishlists` (
  `wishlist_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `wishlists`
--

INSERT INTO `wishlists` (`wishlist_id`, `user_id`, `product_id`, `added_at`, `created_at`, `updated_at`) VALUES
(1, 4, 9, '2026-02-25 18:18:19', '2026-02-25 18:18:19', '2026-02-25 18:18:27'),
(6, NULL, 12, '2026-03-29 13:33:20', '2026-03-29 06:33:20', '2026-03-29 06:33:20');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

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
-- Chỉ mục cho bảng `comment_user`
--
ALTER TABLE `comment_user`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

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
-- Chỉ mục cho bảng `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `news_slug_unique` (`slug`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `orders_order_code_unique` (`order_code`),
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
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_google_id_unique` (`google_id`),
  ADD UNIQUE KEY `users_facebook_id_unique` (`facebook_id`);

--
-- Chỉ mục cho bảng `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_addresses_user_id_foreign` (`user_id`);

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
-- AUTO_INCREMENT cho bảng `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `comment_user`
--
ALTER TABLE `comment_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `news`
--
ALTER TABLE `news`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

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
  MODIFY `promo_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT cho bảng `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `wishlist_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- Các ràng buộc cho bảng `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

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
