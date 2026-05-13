-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table ams_inventory.activity_logs
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ams_inventory.activity_logs: ~24 rows (approximately)
INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
	(1, 3, 'Logout', 'User logged out.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-11 19:24:02', '2026-05-11 19:24:02'),
	(2, 3, 'Login', 'User successfully logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-11 19:54:21', '2026-05-11 19:54:21'),
	(3, 1, 'Updated', 'Updated user account: hakdog@deped.gov.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-11 20:01:04', '2026-05-11 20:01:04'),
	(4, 3, 'Logout', 'User logged out.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-11 20:01:18', '2026-05-11 20:01:18'),
	(5, 6, 'Login', 'User successfully logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-11 20:01:23', '2026-05-11 20:01:23'),
	(6, 6, 'Logout', 'User logged out.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-11 20:01:36', '2026-05-11 20:01:36'),
	(7, 3, 'Login', 'User successfully logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-11 20:01:43', '2026-05-11 20:01:43'),
	(8, 1, 'Updated', 'Updated user account: hakdog@deped.gov.ph', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-11 20:01:55', '2026-05-11 20:01:55'),
	(9, 4, 'Updated', 'Scanner updated asset status: Acer Laptop to Serviceable', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-11 22:47:06', '2026-05-11 22:47:06'),
	(10, 4, 'Updated', 'Scanner updated asset status: Acer Laptop to Serviceable', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-11 22:47:30', '2026-05-11 22:47:30'),
	(11, 4, 'Updated', 'Scanner updated asset status: Acer Laptop to Serviceable', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-11 22:48:00', '2026-05-11 22:48:00'),
	(12, 4, 'Updated', 'Scanner updated asset status: Acer Laptop to Unserviceable', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-11 22:48:32', '2026-05-11 22:48:32'),
	(13, 4, 'Updated', 'Scanner updated asset status: Acer Laptop to Serviceable', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-11 22:48:40', '2026-05-11 22:48:40'),
	(14, 4, 'Updated', 'Scanner updated asset status: Acer Laptop to Serviceable', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-11 23:19:34', '2026-05-11 23:19:34'),
	(15, 3, 'Login', 'User successfully logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-12 00:30:57', '2026-05-12 00:30:57'),
	(16, 3, 'Created', 'User submitted a new RIS request: RIS-2026-05-0024', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-12 00:38:28', '2026-05-12 00:38:28'),
	(17, 4, 'Updated', 'Processed/Updated RIS: RIS-2026-05-0024 (Status: Forwarded to Admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-12 00:40:01', '2026-05-12 00:40:01'),
	(18, 1, 'Updated', 'Processed RIS Request RIS-2026-05-0024 to status: Approved', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-12 00:40:49', '2026-05-12 00:40:49'),
	(19, 4, 'Created', 'Added new supply: Bond paper', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-12 00:43:08', '2026-05-12 00:43:08'),
	(20, 3, 'Created', 'User submitted a new RIS request: RIS-2026-05-0025', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-12 00:44:50', '2026-05-12 00:44:50'),
	(21, 3, 'Updated', 'User modified their pending RIS request: RIS-2026-05-0025', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-12 00:45:50', '2026-05-12 00:45:50'),
	(22, 4, 'Updated', 'Processed/Updated RIS: RIS-2026-05-0025 (Status: Forwarded to Admin)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-12 00:46:33', '2026-05-12 00:46:33'),
	(23, 1, 'Updated', 'Processed RIS Request RIS-2026-05-0025 to status: Approved', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-12 00:46:53', '2026-05-12 00:46:53'),
	(24, 4, 'Login', 'User successfully logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-12 14:21:24', '2026-05-12 14:21:24'),
	(25, 4, 'Login', 'User successfully logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-12 18:14:27', '2026-05-12 18:14:27'),
	(26, 1, 'Login', 'User successfully logged in.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '2026-05-12 18:39:38', '2026-05-12 18:39:38');

-- Dumping structure for table ams_inventory.assets
CREATE TABLE IF NOT EXISTS `assets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `barcode_id` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `article` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `unit_measure` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `supplier` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `unit_value` decimal(10,2) DEFAULT '0.00',
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'Serviceable',
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode_id` (`barcode_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table ams_inventory.assets: ~4 rows (approximately)
INSERT INTO `assets` (`id`, `barcode_id`, `article`, `description`, `unit_measure`, `supplier`, `unit_value`, `image`, `status`) VALUES
	(2, NULL, 'Epson Printer', 'L3210 3-in-1', NULL, '', 12000.00, NULL, 'Serviceable'),
	(3, NULL, 'Acer Laptop', 'TravelMate', NULL, 'ABENSON', 48000.00, NULL, 'Serviceable'),
	(7, 'DEPED-2026-00007', 'Acer Laptop', 'Travelmate', 'Unit', NULL, 48900.00, NULL, 'Serviceable'),
	(9, '2026-02-0003', 'Epson Printer', 'Model L3210', 'Unit', NULL, 10000.00, NULL, 'Serviceable');

-- Dumping structure for table ams_inventory.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ams_inventory.cache: ~0 rows (approximately)

-- Dumping structure for table ams_inventory.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ams_inventory.cache_locks: ~0 rows (approximately)

-- Dumping structure for table ams_inventory.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ams_inventory.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table ams_inventory.generated_barcodes
CREATE TABLE IF NOT EXISTS `generated_barcodes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `item_id` int NOT NULL,
  `item_type` enum('asset','supply') COLLATE utf8mb4_general_ci NOT NULL,
  `barcode_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `article` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table ams_inventory.generated_barcodes: ~5 rows (approximately)
INSERT INTO `generated_barcodes` (`id`, `item_id`, `item_type`, `barcode_code`, `article`, `generated_at`) VALUES
	(3, 9, 'asset', '2026-02-0003', 'Epson Printer', '2026-02-19 06:15:30'),
	(6, 18, 'supply', 'SUP-2026-02-0018', 'Bond paper', '2026-02-20 01:08:32'),
	(8, 21, 'supply', 'SUP-2026-02-0021', 'Ballpen', '2026-02-23 06:44:18'),
	(9, 24, 'supply', 'SUP-2026-02-0024', 'Manila Paper', '2026-02-25 08:04:24'),
	(11, 26, 'supply', 'SUP-2026-02-0026', 'Masking Tape', '2026-02-25 08:08:22');

-- Dumping structure for table ams_inventory.ics_requests
CREATE TABLE IF NOT EXISTS `ics_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ics_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fund_cluster` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sig_received_from_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sig_received_from_pos` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sig_from_date` date DEFAULT NULL,
  `sig_received_by_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sig_received_by_pos` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sig_by_date` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `items_json` text COLLATE utf8mb4_unicode_ci,
  `signed_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ics_requests_ics_no_unique` (`ics_no`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ams_inventory.ics_requests: ~3 rows (approximately)
INSERT INTO `ics_requests` (`id`, `ics_no`, `fund_cluster`, `category`, `sig_received_from_name`, `sig_received_from_pos`, `sig_from_date`, `sig_received_by_name`, `sig_received_by_pos`, `sig_by_date`, `status`, `items_json`, `signed_document`, `created_at`, `updated_at`) VALUES
	(1, 'SPHV-2026-05-0001', NULL, 'High - Valued', 'eqwewq', 'dafa', NULL, 'dsadasdas', 'qwe', NULL, 'Pending', '[{"qty":"1","unit":"unit","desc":"Travelmate","inv_no":"DEPED-2026-00007","est_life":null,"unit_cost":"48900.00","total_cost":"48900.00"}]', '1777820449_signed_ics_1.png', '2026-05-03 05:24:48', '2026-05-03 07:00:49'),
	(4, 'SPLV-2026-05-0001', NULL, 'Low - Valued', 'Jeffrey B. Pagatpat', 'Admin Officer V', NULL, 'AAAA', 'aaaa', NULL, 'Pending', '[{"qty":"1","unit":"unit","desc":"Travelmate","inv_no":"DEPED-2026-00007","est_life":null,"unit_cost":"48900.00","total_cost":"48900.00"}]', NULL, '2026-05-04 00:21:11', '2026-05-04 00:21:11'),
	(5, 'PAR-2026-05-0003', NULL, 'PPE', 'SALVADOR DEYTO JR.', 'ITO', NULL, 'JEFFREY PAGATPAT', 'Administrative Officer V- AMS', NULL, 'Pending', '[{"qty":"1","unit":"unit","desc":"Travelmate","inv_no":"DEPED-2026-00007","est_life":"2026-05-04","unit_cost":"48900.00","total_cost":"49999","transfer_status":"Returned to Inventory"}]', NULL, '2026-05-04 00:23:13', '2026-05-11 23:19:34');

-- Dumping structure for table ams_inventory.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ams_inventory.jobs: ~0 rows (approximately)

-- Dumping structure for table ams_inventory.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ams_inventory.job_batches: ~0 rows (approximately)

-- Dumping structure for table ams_inventory.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ams_inventory.migrations: ~17 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_02_26_015026_create_assets_table', 1),
	(5, '2026_02_26_015812_create_supplies_table', 1),
	(6, '2026_03_05_051247_create_ics_requests_table', 1),
	(7, '2026_03_05_053406_add_profile_fields_to_users_table', 1),
	(8, '2026_03_06_021216_change_stock_avail_column_in_ris_items_table', 2),
	(9, '2026_03_06_022124_add_user_id_to_ris_requests_table', 2),
	(10, '2026_03_25_064906_drop_designation_from_users_table', 3),
	(11, '2026_03_25_072221_add_profile_fields_to_users_table', 4),
	(12, '2026_03_31_024115_remove_username_from_users_table', 5),
	(13, '2026_03_31_051949_remove_employee_id_from_users_table', 6),
	(14, '2026_04_13_020940_create_purchase_orders_table', 7),
	(15, '2026_04_13_020945_create_purchase_order_items_table', 7),
	(16, '2026_04_13_021231_create_purchase_orders_table', 8),
	(17, '2026_04_13_021238_create_purchase_order_items_table', 8),
	(18, '2026_04_14_004114_add_low_stock_threshold_to_supplies_table', 9),
	(19, '2026_04_15_010919_add_image_to_users_table', 10),
	(20, '2026_04_15_011350_add_image_to_users_table', 11),
	(21, '2026_04_15_073552_add_remember_token_to_users_table', 12),
	(22, '2026_04_16_013351_add_delivery_details_to_purchase_orders_table', 13),
	(23, '2026_04_16_015753_add_designations_to_purchase_orders_table', 13),
	(24, '2026_04_20_011012_add_is_delivered_to_purchase_order_items_table', 13),
	(25, '2026_04_20_015200_fix_missing_po_columns_master', 13),
	(26, '2026_04_21_005945_create_system_settings_table', 14),
	(27, '2026_04_30_013423_add_po_type_to_purchase_orders_table', 15),
	(28, '2026_04_30_023114_add_image_to_assets_table', 16),
	(29, '2026_04_30_055939_remove_quantity_from_assets_table', 17),
	(30, '2026_05_03_145954_add_signed_document_to_ics_requests_table', 18);

-- Dumping structure for table ams_inventory.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ams_inventory.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table ams_inventory.purchase_orders
CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `po_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `po_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `po_date` date NOT NULL,
  `procurement_mode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_official` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chief_accountant` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending Delivery',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `place_of_delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_term` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_term` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_official_designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'REGIONAL DIRECTOR',
  `chief_accountant_designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACCOUNTANT II',
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_po_no_unique` (`po_no`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ams_inventory.purchase_orders: ~2 rows (approximately)
INSERT INTO `purchase_orders` (`id`, `po_type`, `entity_name`, `po_no`, `supplier_name`, `supplier_address`, `po_date`, `procurement_mode`, `auth_official`, `chief_accountant`, `total_amount`, `status`, `created_at`, `updated_at`, `place_of_delivery`, `date_of_delivery`, `delivery_term`, `payment_term`, `auth_official_designation`, `chief_accountant_designation`) VALUES
	(9, 'Supply', NULL, '2026-04-0003', 'Skylark Graphics Solution', 'Lakandula Drive, Gogon, Legazpi City', '2026-04-30', 'Small Value Procurement', 'Gilbert T. Sadsad', 'Zer Jethro Rodmell A. Roscuata, CPA', 9330.00, 'Complete', '2026-04-29 17:36:57', '2026-04-29 17:36:57', NULL, NULL, NULL, NULL, 'REGIONAL DIRECTOR', 'ACCOUNTANT II'),
	(10, 'Asset', NULL, '2026-04-0004', 'Skylark Graphics Solution', 'Lakandula Drive, Gogon, Legazpi City', '2026-04-30', 'Negotiated Procurement', 'Gilbert T. Sadsad', 'Zer Jethro Rodmell A. Roscuata, CPA', 55000.00, 'Complete', '2026-04-29 17:57:57', '2026-04-29 17:57:57', NULL, NULL, NULL, NULL, 'REGIONAL DIRECTOR', 'ACCOUNTANT II'),
	(11, 'Supply', NULL, '2026-04-0010', 'Skylark Graphics Solution', 'Lakandula Drive, Gogon, Legazpi City', '2026-05-04', 'Small Value Procurement', 'Gilbert T. Sadsad', 'Zer Jethro Rodmell A. Roscuata, CPA', 3500.00, 'Partial', '2026-05-03 22:53:15', '2026-05-03 22:53:15', NULL, NULL, NULL, NULL, 'REGIONAL DIRECTOR', 'ACCOUNTANT II');

-- Dumping structure for table ams_inventory.purchase_order_items
CREATE TABLE IF NOT EXISTS `purchase_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint unsigned NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int NOT NULL,
  `unit_cost` decimal(15,2) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `is_delivered` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`),
  CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ams_inventory.purchase_order_items: ~6 rows (approximately)
INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `unit`, `description`, `qty`, `unit_cost`, `amount`, `is_delivered`, `created_at`, `updated_at`) VALUES
	(20, 9, 'pc', 'supply 1', 10, 533.00, 5330.00, 1, '2026-04-29 17:36:57', '2026-04-29 17:36:57'),
	(21, 9, 'pc', 'supply 2', 8, 500.00, 4000.00, 1, '2026-04-29 17:36:57', '2026-04-29 17:36:57'),
	(22, 10, 'pc', 'asset 1', 1, 5000.00, 5000.00, 1, '2026-04-29 17:57:57', '2026-04-29 17:57:57'),
	(23, 10, 'pc', 'asset 2', 2, 25000.00, 50000.00, 1, '2026-04-29 17:57:57', '2026-04-29 17:57:57'),
	(24, 11, 'pc', 'ballpen', 100, 10.00, 1000.00, 1, '2026-05-03 22:53:15', '2026-05-03 22:53:15'),
	(25, 11, 'pc', 'bond paper', 500, 5.00, 2500.00, 0, '2026-05-03 22:53:15', '2026-05-03 22:53:15');

-- Dumping structure for table ams_inventory.ris_items
CREATE TABLE IF NOT EXISTS `ris_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ris_id` int NOT NULL,
  `stock_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `unit` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `req_quantity` int DEFAULT NULL,
  `stock_avail` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `issue_quantity` int DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table ams_inventory.ris_items: ~16 rows (approximately)
INSERT INTO `ris_items` (`id`, `ris_id`, `stock_no`, `unit`, `description`, `req_quantity`, `stock_avail`, `issue_quantity`, `remarks`) VALUES
	(5, 1, NULL, 'Ream', 'A4 bond paper', 5, 'n/a', NULL, 'zcxcxzc'),
	(6, 2, 'SUP-2026-02-0024', 'Pc', 'Manila Paper', 5, 'n/a', 5, NULL),
	(7, 3, 'SUP-2026-03-0063', 'Piece', 'TESTING LANGS, TESTING NGANI', 10, 'yes', 0, 'oki'),
	(8, 4, 'SUP-2026-03-0125', 'Piece', 'Pakak, PaPa mo Blue', 2, 'yes', 2, 'zcxcxzc'),
	(9, 5, 'SUP-2026-03-0125', 'Piece', 'AMS Tesing, tesing testing', 1, 'N/A', NULL, NULL),
	(10, 6, 'SUP-2026-03-0125', 'Unit', 'AMS Tesing, tesing testing', 1, 'n/a', NULL, NULL),
	(11, 7, 'SUP-2026-03-0125', 'Piece', 'AMS Testing, testing testing', 30, 'N/A', NULL, NULL),
	(12, 8, 'SUP-2026-03-0026', 'Piece', 'Envelope, mailing, long , 500pcs/ box', 100, 'n/a', 101, NULL),
	(15, 9, 'SUP-2026-03-0002', 'Gallon', 'Alcohol, Ethyl, 1 Gallon , 70% solution', 1, 'N/A', NULL, NULL),
	(16, 9, 'SUP-2026-03-0103', 'Can', 'Air freshener, aerosol 280 ml', 3, 'N/A', NULL, NULL),
	(17, 10, 'SUP-2026-03-0100', 'Piece(s)', 'Aircon Outlet, Wide  universal outlet with ground', 2, 'yes', 2, '₱ 400.00'),
	(18, 10, 'SUP-2026-03-0100', 'Piece(s)', 'Aircon Outlet, Wide  universal outlet with ground', 2, 'no', 0, 'Out of Stock'),
	(19, 10, 'SUP-2026-03-0033', 'Bottle', '336XHigh Yield Black, original HP LaserJet', 8, 'yes', 2, '₱ 13,820.00'),
	(20, 11, 'SUP-2026-03-0125', 'Piece(s)', 'AMS Testing, testing testing', 4, 'no', 0, NULL),
	(23, 14, NULL, 'unit', 'Tricycle', 1, 'N/A', NULL, NULL),
	(24, 15, 'SUP-2026-03-0001', 'Bottle', 'Alcohol, Ethyl, 500ml, 70% solution', 10, 'yes', 5, '₱ 315.00'),
	(26, 16, 'SUP-2026-05-0011', 'Ream', 'Bond paper, A4 short', 5, 'yes', 5, '₱ 1,000.00');

-- Dumping structure for table ams_inventory.ris_requests
CREATE TABLE IF NOT EXISTS `ris_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `ris_no` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `entity_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `division` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `office` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fund_cluster` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rcc` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `purpose` text COLLATE utf8mb4_general_ci,
  `sig_requested_by` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sig_approved_by` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sig_issued_by` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sig_received_by` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `desig_requested` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `desig_approved` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `desig_issued` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `desig_received` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_requested` date DEFAULT NULL,
  `date_approved` date DEFAULT NULL,
  `date_issued` date DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `status` enum('Pending Staff Review','Forwarded to Admin','Approved','Rejected') COLLATE utf8mb4_general_ci DEFAULT 'Pending Staff Review',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table ams_inventory.ris_requests: ~14 rows (approximately)
INSERT INTO `ris_requests` (`id`, `user_id`, `ris_no`, `entity_name`, `division`, `office`, `fund_cluster`, `rcc`, `purpose`, `sig_requested_by`, `sig_approved_by`, `sig_issued_by`, `sig_received_by`, `desig_requested`, `desig_approved`, `desig_issued`, `desig_received`, `date_requested`, `date_approved`, `date_issued`, `date_received`, `status`, `created_at`) VALUES
	(1, NULL, 'RIS-2026-03-0001', 'Department of Education - ROV', 'Asset Management Section', 'Administrative Division', NULL, NULL, 'For Office Use', 'hjhkjg', 'JEFFREY B. PAGATPAT', 'ALDRIN RELLAMA', 'ewqeqw', 'qewqeqwe', 'Admin, Officer V (Supply Officer)', 'AA-VI (Storekeeper II)', 'weqe', NULL, NULL, NULL, NULL, 'Rejected', '2026-03-09 17:25:57'),
	(2, 3, 'RIS-2026-03-0002', 'Department of Education - ROV', 'General Services Unit', 'Administrative Division', NULL, NULL, NULL, 'czxczx', 'JEFFREY B. PAGATPAT', 'ALDRIN RELLAMA', 'ewqeqw', 'fdsafsad', 'Admin, Officer V (Supply Officer)', 'AA-VI (Storekeeper II)', 'fdsfsdfdfs', NULL, '2026-03-17', NULL, NULL, 'Rejected', '2026-03-16 16:40:22'),
	(3, 3, 'RIS-2026-03-0003', 'Department of Education - ROV', 'Asset Management Section', 'Administrative Division', NULL, NULL, NULL, 'Karen', 'JEFFREY B. PAGATPAT', 'ALDRIN RELLAMA', 'Karen', 'Developer', 'Admin, Officer V (Supply Officer)', 'AA-VI (Storekeeper II)', 'Developer', NULL, '2026-04-15', NULL, NULL, 'Approved', '2026-03-17 17:26:44'),
	(4, 3, 'RIS-2026-03-0004', 'Department of Education - ROV', 'Asset Management Section', 'Administrative Division', NULL, NULL, 'fo office only', 'vbncnbc', 'JEFFREY B. PAGATPAT', 'ALDRIN RELLAMA', 'vhjhjjhj', 'fdsafsad', 'Admin, Officer V (Supply Officer)', 'AA-VI (Storekeeper II)', 'fdsfsdfdfs', NULL, '2026-03-18', NULL, NULL, 'Approved', '2026-03-17 21:53:12'),
	(5, 3, 'RIS-2026-04-0001', 'Department of Education - ROV', 'Records Section', 'Administrative Division', NULL, NULL, '', 'adasdasd', 'JEFFREY B. PAGATPAT', 'ALDRIN RELLAMA', 'dasdasd', 'dsa', 'Admin, Officer V (Supply Officer)', 'AA-VI (Storekeeper II)', 'asda', '2026-04-07', NULL, NULL, NULL, 'Pending Staff Review', '2026-04-06 16:48:09'),
	(6, 3, 'RIS-2026-04-0002', 'Department of Education - ROV', 'Learning Resource Management Section', 'Curriculum and Learning Management Division', NULL, NULL, 'debugging', 'qwe', 'JEFFREY B. PAGATPAT', 'ALDRIN RELLAMA', 'qwe', 'ewq', 'Admin, Officer V (Supply Officer)', 'AA-VI (Storekeeper II)', 'ewq', NULL, NULL, NULL, NULL, 'Forwarded to Admin', '2026-04-06 17:24:28'),
	(7, 3, 'RIS-2026-04-0003', 'Department of Education - ROV', 'Asset Management Section', 'Administrative Division', NULL, NULL, 'dfgdfhdh', 'efasdfdasf', 'JEFFREY B. PAGATPAT', 'ALDRIN RELLAMA', 'hdfgd', 'ghnfghdfh', 'Admin, Officer V (Supply Officer)', 'AA-VI (Storekeeper II)', 'dgdfgdf', '2026-04-23', NULL, NULL, NULL, 'Pending Staff Review', '2026-04-22 19:00:11'),
	(8, 3, 'RIS-2026-04-0004', 'Department of Education - ROV', 'Asset Management Section', 'Administrative Division', NULL, NULL, NULL, 'afdda', 'JEFFREY B. PAGATPAT', 'ALDRIN RELLAMA', 'afasd', 'fdafad', 'Admin, Officer V (Supply Officer)', 'AA-VI (Storekeeper II)', 'dasdsad', NULL, '2026-04-27', NULL, NULL, 'Approved', '2026-04-26 17:38:37'),
	(9, 3, 'RIS-2026-04-0005', 'Department of Education - ROV', NULL, 'Curriculum and Learning Management Division', NULL, NULL, '', 'dsfbgnhmj', 'JEFFREY B. PAGATPAT', 'ALDRIN RELLAMA', 'sfdghj,j', 'fsdgmb', 'Admin, Officer V (Supply Officer)', 'AA-VI (Storekeeper II)', 'sgrtbdghbg', '2026-04-27', NULL, NULL, NULL, 'Pending Staff Review', '2026-04-26 23:06:35'),
	(10, 3, 'RIS-2026-05-0006', 'Department of Education - ROV', 'Programs and Projects', 'Education Support Services Division', NULL, NULL, 'kjjhgfd', 'ghjklklkl', 'JEFFREY B. PAGATPAT', 'ALDRIN RELLAMA', 'yttytyt', 'hghjghj', 'Admin, Officer V (Supply Officer)', 'AA-VI (Storekeeper II)', 'tuyfyhf', NULL, '2026-05-12', NULL, NULL, 'Approved', '2026-05-03 21:16:12'),
	(11, 6, 'RIS-2026-05-0014', 'Department of Education - ROV', 'Asset Management Section', 'Administrative Division', NULL, NULL, NULL, 'asdasd', 'JEFFREY B. PAGATPAT', 'ALDRIN RELLAMA', 'sadsad', 'sadsad', 'Admin, Officer V (Supply Officer)', 'AA-VI (Storekeeper II)', 'asdasd', NULL, '2026-05-12', NULL, NULL, 'Approved', '2026-05-03 23:50:21'),
	(14, 3, 'RIS-2026-05-0023', 'Department of Education - ROV', 'Asset Management Section', 'Administrative Division', NULL, NULL, 'for service', 'test', 'JEFFREY B. PAGATPAT', 'ALDRIN RELLAMA', 'test', 'ting', 'Admin, Officer V (Supply Officer)', 'AA-VI (Storekeeper II)', 'ting', '2026-05-12', NULL, NULL, NULL, 'Pending Staff Review', '2026-05-11 16:12:05'),
	(15, 3, 'RIS-2026-05-0024', 'Department of Education - ROV', 'Asset Management Section', 'Administrative Division', NULL, NULL, 'gjhghj', 'test', 'JEFFREY B. PAGATPAT', 'ALDRIN RELLAMA', 'test', 'ting', 'Admin, Officer V (Supply Officer)', 'AA-VI (Storekeeper II)', 'ting', NULL, '2026-05-12', NULL, NULL, 'Approved', '2026-05-12 00:38:28'),
	(16, 3, 'RIS-2026-05-0025', 'Department of Education - ROV', 'Asset Management Section', 'Administrative Division', NULL, NULL, 'hjghj', 'asdasd', 'JEFFREY B. PAGATPAT', 'ALDRIN RELLAMA', 'lex', 'mvb', 'Admin, Officer V (Supply Officer)', 'AA-VI (Storekeeper II)', 'cvnvn', NULL, '2026-05-12', NULL, NULL, 'Approved', '2026-05-12 00:44:50');

-- Dumping structure for table ams_inventory.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ams_inventory.sessions: ~3 rows (approximately)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('7n6zt2YMiM0QME09b3nBY24p2sC8vnLXKx5JFlbZ', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQmg0eVp4SzFaYjVGazU3V0o0WVlzWDZqQnM4S2pDWDVSeHFabDRlcSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hbXMtaW52ZW50b3J5LnRlc3QvaWRsZS1zY3JlZW4iO3M6NToicm91dGUiO047fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6MzQ6Imh0dHA6Ly9hbXMtaW52ZW50b3J5LnRlc3Qvc3VwcGxpZXMiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O30=', 1778640506),
	('hmtvthxSAlWjz3uWuGIMmGCRJwDXkHzxPilofcmA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUjZIMVBDS1Y1SkZQOGtpb2Fvb3lGWEdSSjVkOXF5TldBWnZpeVFISiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9hbXMtaW52ZW50b3J5LnRlc3QvaWRsZS1zY3JlZW4iO3M6NToicm91dGUiO047fX0=', 1778640080),
	('Lt8xksTmJVQKclAQY29G18VzRNoXYF5VClPr4g2G', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibUFRdkdtVkloVFVqSWFscFg5Y0VuNWhPU2ZRenlGaklpSEl4bmpLQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly9hbXMtaW52ZW50b3J5LnRlc3QvYWRtaW4vYWN0aXZpdHktbG9ncyI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1778640031);

-- Dumping structure for table ams_inventory.supplies
CREATE TABLE IF NOT EXISTS `supplies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `barcode_id` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `article` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `supplier` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `unit_measure` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `unit_value` decimal(10,2) DEFAULT '0.00',
  `quantity` int DEFAULT '0',
  `low_stock_threshold` int NOT NULL DEFAULT '10',
  `status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'Available',
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode_id` (`barcode_id`)
) ENGINE=InnoDB AUTO_INCREMENT=167 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table ams_inventory.supplies: ~123 rows (approximately)
INSERT INTO `supplies` (`id`, `barcode_id`, `article`, `description`, `supplier`, `unit_measure`, `unit_value`, `quantity`, `low_stock_threshold`, `status`, `image`) VALUES
	(28, 'SUP-2026-03-0001', 'Alcohol', 'Ethyl, 500ml, 70% solution', NULL, 'Bottle', 63.00, 495, 10, 'Available', NULL),
	(29, 'SUP-2026-03-0002', 'Alcohol', 'Ethyl, 1 Gallon , 70% solution', NULL, 'Gallon', 219.00, 20, 10, 'Available', NULL),
	(31, 'SUP-2026-03-0004', 'Ballpen', 'ordinary, color blue, good quality', NULL, 'Piece', 6.10, 20000, 10, 'Available', NULL),
	(32, 'SUP-2026-03-0005', 'Ballpen', 'ordinary , color blue, good quality', NULL, 'Piece', 6.10, 1200, 10, 'Available', NULL),
	(33, 'SUP-2026-03-0006', 'Battery', 'dry cell, AA, 2 pieces/blister pack', NULL, 'Pack', 43.00, 460, 10, 'Available', NULL),
	(34, 'SUP-2026-03-0007', 'Battery', 'dry cell, AAA, 2 pieces/blister pack', NULL, 'Pack', 52.00, 386, 10, 'Available', NULL),
	(35, 'SUP-2026-03-0008', 'Binder Clip', '(black fold) 19mm, clamping depth 0.20mm, 12pcs/box', NULL, 'Box', 20.00, 170, 10, 'Available', NULL),
	(36, 'SUP-2026-03-0009', 'Binder Clip', '(black fold) 25mm, clamping depth 0.20mm, 12 pcs/box', NULL, 'Box', 19.00, 261, 10, 'Available', NULL),
	(37, 'SUP-2026-03-0010', 'Binder Clip', '(black fold) 32mm, clamping depth 0.20mm, 12 pcs/box', NULL, 'Box', 31.00, 217, 10, 'Available', NULL),
	(38, 'SUP-2026-03-0011', 'Binder Clip', '(black fold ), 50mm, clamping depth 0.33mm, 12pcs/box', NULL, 'Box', 68.00, 200, 10, 'Available', NULL),
	(39, 'SUP-2026-03-0012', 'Binder', 'Ring , plastic 32mm, black color, 10pcs/bundle', NULL, 'Bundle', 48.00, 49, 10, 'Available', NULL),
	(40, 'SUP-2026-03-0013', 'Carbon Film', 'black,100sheets/pack, legal', NULL, 'Pack', 585.00, 20, 10, 'Available', NULL),
	(41, 'SUP-2026-03-0014', 'Cartolina', 'assorted color, 78gsm, 572 x 724mm, 20pcs/pack', NULL, 'Pack', 155.00, 10, 10, 'Available', NULL),
	(42, 'SUP-2026-03-0015', 'Certificate Holder', 'plastic, A4 size, 8.25" x 11.69', NULL, 'Piece', 41.00, 200, 10, 'Available', NULL),
	(43, 'SUP-2026-03-0016', 'Certificate Holder', 'plastic, long, 8.5 x 13"', NULL, 'Piece', 43.00, 200, 10, 'Available', NULL),
	(44, 'SUP-2026-03-0017', 'Certificate Holder', 'plastic, short, 8.5 x 11"', NULL, 'Piece', 39.00, 200, 10, 'Available', NULL),
	(45, 'SUP-2026-03-0018', 'Clear book', '20 transparent  pockets , A4 size', NULL, 'Piece', 35.00, 200, 10, 'Available', NULL),
	(46, 'SUP-2026-03-0019', 'Clear book', '20 transparent pockets, legal size', NULL, 'Piece', 40.00, 200, 10, 'Available', NULL),
	(47, 'SUP-2026-03-0020', 'Clip Paper', 'big, vinyl, plastic coat, 50mm, 100pcs/box', NULL, 'Box', 20.00, 200, 10, 'Available', NULL),
	(48, 'SUP-2026-03-0021', 'Colored Paper', 'short, 8.5 x 11, green, 250sheets/pack', NULL, 'Pack', 199.00, 20, 10, 'Available', NULL),
	(49, 'SUP-2026-03-0022', 'Colored Paper', 'long, assorted, 250sheets/pack', NULL, 'Pack', 240.00, 20, 10, 'Available', NULL),
	(50, 'SUP-2026-03-0023', 'Correction Tape', 'film base type UL 6m min', NULL, 'Piece', 23.00, 500, 10, 'Available', NULL),
	(53, 'SUP-2026-03-0024', 'Data file Box', 'made of chipboard, with close ends', NULL, 'Piece', 121.00, 200, 10, 'Available', NULL),
	(54, 'SUP-2026-03-0025', 'Data File Folder', '127x229mmx400mm, chipboard, blue', NULL, 'Piece', 89.00, 200, 10, 'Available', NULL),
	(55, 'SUP-2026-03-0026', 'Envelope', 'mailing, long , 500pcs/ box', NULL, 'Piece(s)', 387.00, 899, 10, 'Available', NULL),
	(56, 'SUP-2026-03-0027', 'Envelope', 'documentary, A4, 500pcs/box', NULL, 'Piece(s)', 1135.00, 21, 10, 'Available', NULL),
	(57, 'SUP-2026-03-0028', 'T6641 INK Cartridge', 'black', NULL, 'Bottle', 310.00, 100, 10, 'Available', NULL),
	(58, 'SUP-2026-03-0029', 'T6642 INK Cartridge', 'cyan', NULL, 'Bottle', 310.00, 40, 10, 'Available', NULL),
	(59, 'SUP-2026-03-0030', 'T6643 INK Cartridge', 'magenta', NULL, 'Bottle', 40.00, 310, 10, 'Available', NULL),
	(60, 'SUP-2026-03-0031', 'T6644 INK Cartridge', 'yellow', NULL, 'Bottle', 310.00, 40, 10, 'Available', NULL),
	(61, 'SUP-2026-03-0032', 'Folder', 'Ordinary, white, long 100pcs per pack', NULL, 'Pack', 498.00, 50, 10, 'Available', NULL),
	(62, 'SUP-2026-03-0033', '336XHigh Yield Black', 'original HP LaserJet', NULL, 'Bottle', 6910.00, 0, 10, 'Available', NULL),
	(63, 'SUP-2026-03-0034', 'Printer Ink', 'GT 51/53 black, original', NULL, 'Bottle', 399.00, 50, 10, 'Available', NULL),
	(64, 'SUP-2026-03-0035', 'Printer Ink', 'GT 52 cyan, original', NULL, 'Bottle', 399.00, 24, 10, 'Available', NULL),
	(65, 'SUP-2026-03-0036', 'Printer Ink', 'GT 52 magenta, original', NULL, 'Bottle', 399.00, 24, 10, 'Available', NULL),
	(66, 'SUP-2026-03-0037', 'Printer Ink', 'GT 52 yellow, original', NULL, 'Bottle', 399.00, 24, 10, 'Available', NULL),
	(67, 'SUP-2026-03-0038', 'Index Tab', 'Alphabetical A-Z self-adhesive, transparent, 5sets/bx', NULL, 'Box', 90.00, 126, 10, 'Available', NULL),
	(68, 'SUP-2026-03-0039', 'Computer Ink', 'No. 003 black original', NULL, 'Bottle', 310.00, 400, 10, 'Available', NULL),
	(69, 'SUP-2026-03-0040', 'Computer Ink', 'No. 003 cyan, original', NULL, 'Bottle', 319.00, 200, 10, 'Available', NULL),
	(70, 'SUP-2026-03-0041', 'Computer Ink', 'No. 003 yellow, original', NULL, 'Bottle', 319.00, 200, 10, 'Available', NULL),
	(71, 'SUP-2026-03-0042', 'Insecticide', 'Aerosol type, 600 ml min.', NULL, 'Can', 498.00, 32, 10, 'Available', NULL),
	(72, 'SUP-2026-03-0043', 'Interfolded Paper Towel', '175 pulls', NULL, 'Pack', 48.00, 200, 10, 'Available', NULL),
	(73, 'SUP-2026-03-0044', 'Laid Paper', 'Long , Pink , 500 sheets per ream', NULL, 'Ream', 960.00, 20, 10, 'Available', NULL),
	(74, 'SUP-2026-03-0045', 'Laid Paper', 'Long, Yellow, 500 sheets per ream', NULL, 'Ream', 960.00, 20, 10, 'Available', NULL),
	(75, 'SUP-2026-03-0046', 'Laid Paper', 'Long , Light Blue, 500 sheets per ream', NULL, 'Ream', 960.00, 20, 10, 'Available', NULL),
	(76, 'SUP-2026-03-0047', 'Laid Paper', 'Long, Light Green, 500 sheets per ream', NULL, 'Ream', 960.00, 20, 10, 'Available', NULL),
	(77, 'SUP-2026-03-0048', 'Laid Paper', 'Long, Ivory, 500 sheets per ream', NULL, 'Ream', 960.00, 20, 10, 'Available', NULL),
	(78, 'SUP-2026-03-0049', 'Laid Paper', 'Long Cream, 500 sheets per ream', NULL, 'Ream', 960.00, 20, 10, 'Available', NULL),
	(79, 'SUP-2026-03-0050', 'Laid Paper', 'A4, Cream, 500 sheets per ream', NULL, 'Ream', 960.00, 20, 10, 'Available', NULL),
	(80, 'SUP-2026-03-0051', 'Laid Paper', 'A4 Light Green, 500 sheets per ream', NULL, 'Ream', 960.00, 20, 10, 'Available', NULL),
	(81, 'SUP-2026-03-0052', 'Laid Paper', 'A4, Pink , 500 sheets per ream', NULL, 'Ream', 960.00, 20, 10, 'Available', NULL),
	(82, 'SUP-2026-03-0053', 'Laid Paper', 'A4, Yellow , 500 sheets per ream', NULL, 'Ream', 960.00, 20, 10, 'Available', NULL),
	(83, 'SUP-2026-03-0054', 'Laid Paper', 'Short, Light Pink, 500 sheets per ream', NULL, 'Ream', 960.00, 20, 10, 'Available', NULL),
	(84, 'SUP-2026-03-0055', 'Laid Paper', 'Short, Yellow, 500 sheets per ream', NULL, 'Ream', 960.00, 20, 10, 'Available', NULL),
	(85, 'SUP-2026-03-0056', 'Marker', 'Fluorescent, Highlighter, 3 assorted colors per set, good quality', NULL, 'Ream', 105.00, 150, 10, 'Available', NULL),
	(86, 'SUP-2026-03-0057', 'Notepad', 'stick on, 50mmx76mm min (2x3")', NULL, 'Pad', 22.00, 100, 10, 'Available', NULL),
	(87, 'SUP-2026-03-0058', 'Notepad', 'stick on, 76mmx100, (3x4") min', NULL, 'Pad', 33.00, 100, 10, 'Available', NULL),
	(88, 'SUP-2026-03-0059', 'Notepad', 'stick on, 76mmx76mm(3x3")', NULL, 'Pad', 25.00, 100, 10, 'Available', NULL),
	(89, 'SUP-2026-03-0060', 'Notebook Spiral', '152 x 216mm, 60 leaves', NULL, 'Piece', 17.00, 300, 10, 'Available', NULL),
	(90, 'SUP-2026-03-0061', 'Paper Cutter', 'Utility knife, heavy duty', NULL, 'Piece', 89.00, 50, 10, 'Available', NULL),
	(91, 'SUP-2026-03-0062', 'Paper Parchment', '100 sheets/box, 210mmx297mm', NULL, 'Box', 12.00, 300, 10, 'Available', NULL),
	(93, 'SUP-2026-03-0064', 'Pencil', 'lead,  with eraser, wood cased hardness, 12 pc/box, good quality', NULL, 'Piece', 12.00, 300, 10, 'Available', NULL),
	(94, 'SUP-2026-03-0065', 'Pencil Sharpener', 'good quality, heavy duty', NULL, 'Piece', 329.00, 10, 10, 'Available', NULL),
	(95, 'SUP-2026-03-0066', 'Photo Paper', 'long 100 pcs/pack', NULL, 'Pack', 210.00, 20, 10, 'Available', NULL),
	(96, 'SUP-2026-03-0067', 'Push Pin', '0.25" diameter, plastic head, 100 pcs/box', NULL, 'Box', 35.00, 30, 10, 'Available', NULL),
	(97, 'SUP-2026-03-0068', 'PVC  Plastic Cover', 'A4 transparent 200microns', NULL, 'Pack', 515.00, 20, 10, 'Available', NULL),
	(98, 'SUP-2026-03-0069', 'PCV Plastic Cover', 'Long, transparent 200microns', NULL, 'Pack', 570.00, 20, 10, 'Available', NULL),
	(99, 'SUP-2026-03-0070', 'PCV Plastic Cover', 'Short, transparent 200microns', NULL, 'Pack', 490.00, 20, 10, 'Available', NULL),
	(100, 'SUP-2026-03-0071', 'Rags Cloth', 'all cotton, round, 32 pcs/bundle min.', NULL, 'Bundle', 92.00, 100, 10, 'Available', NULL),
	(101, 'SUP-2026-03-0072', 'Record Book', '500 pages, 214mmx278mm min, 55gsm', NULL, 'Book', 225.00, 120, 10, 'Available', NULL),
	(102, 'SUP-2026-03-0073', 'Record Book', '300 pages, 214mmx278mm min, 55gsm', NULL, 'Book', 196.00, 120, 10, 'Available', NULL),
	(103, 'SUP-2026-03-0074', 'Rubber Band', '70mm min lay flat length (No. 18)', NULL, 'Box', 185.00, 32, 10, 'Available', NULL),
	(104, 'SUP-2026-03-0075', 'Pair of Scissors', '5.5 inches minimum symmetrical/asymmetrical', NULL, 'Piece', 55.00, 50, 10, 'Available', NULL),
	(105, 'SUP-2026-03-0076', 'Sign Pen', 'fine tip, black, .5mm, good quality', NULL, 'Piece', 21.00, 300, 10, 'Available', NULL),
	(106, 'SUP-2026-03-0077', 'Sign Pen', 'fine tip, blue. 5mm, good quality', NULL, 'Piece', 21.00, 300, 10, 'Available', NULL),
	(107, 'SUP-2026-03-0078', 'Sign Pen', 'medium tip, black .7mm, good quality', NULL, 'Piece', 21.00, 200, 10, 'Available', NULL),
	(108, 'SUP-2026-03-0079', 'Sign Pen', 'medium tip, blue .7mm good quality', NULL, 'Piece(s)', 21.00, 200, 10, 'Available', NULL),
	(109, 'SUP-2026-03-0080', 'Specialty Board', 'A4, 200 gsm pale cream 10 pcs/pack', NULL, 'Piece(s)', 43.00, 20, 10, 'Available', NULL),
	(110, 'SUP-2026-03-0081', 'Stapler', 'standard type w/ staple wire remover, good quality', NULL, 'Piece(s)', 105.00, 50, 10, 'Available', NULL),
	(111, 'SUP-2026-03-0082', 'Staple Wire', 'no. 35, good quality', NULL, 'Box', 35.00, 120, 10, 'Available', NULL),
	(112, 'SUP-2026-03-0083', 'Stamp Pad Ink', 'violet, 50ml, good quality', NULL, 'Bottle', 75.00, 26, 10, 'Available', NULL),
	(113, 'SUP-2026-03-0084', 'Tape', 'transparent, 1" thickness, 25 meters length', NULL, 'Roll', 14.00, 120, 10, 'Available', NULL),
	(114, 'SUP-2026-03-0085', 'Tape', 'transparent, 2" thickness, 25 meters length', NULL, 'Roll', 38.00, 120, 10, 'Available', NULL),
	(115, 'SUP-2026-03-0086', 'Tape', 'Masking, 1" thickness, 25 meters length', NULL, 'Roll', 19.00, 120, 10, 'Available', NULL),
	(116, 'SUP-2026-03-0087', 'Tape', 'Masking, 2" thickness, 25 meters length', NULL, 'Roll', 41.00, 100, 10, 'Available', NULL),
	(117, 'SUP-2026-03-0088', 'Tape', 'Packing, 2" thickness, 25 meters length', NULL, 'Roll', 43.00, 120, 10, 'Available', NULL),
	(118, 'SUP-2026-03-0089', 'Teflon Tape', '3/4"', NULL, 'Roll', 45.00, 35, 10, 'Available', NULL),
	(119, 'SUP-2026-03-0090', 'Tissue Paper', '2 ply', NULL, 'Roll', 13.80, 200, 10, 'Available', NULL),
	(120, 'SUP-2026-03-0091', 'Trash Bag', 'XL, black, 10 pcs/ pack', NULL, 'Pack', 42.00, 50, 10, 'Available', NULL),
	(121, 'SUP-2026-03-0092', 'Sign Pen', '(1.0) black, good quality', NULL, 'Piece(s)', 55.00, 300, 10, 'Available', NULL),
	(122, 'SUP-2026-03-0093', 'Sign Pen', '(1.0) blue, good quality', NULL, 'Piece(s)', 55.00, 300, 10, 'Available', NULL),
	(123, 'SUP-2026-03-0094', 'Light Bulb (LED)', '18 watts , good quality', NULL, 'Piece(s)', 95.00, 200, 10, 'Available', NULL),
	(124, 'SUP-2026-03-0095', 'Faucet', 'good quality, brass, 1/2 hose bibb', NULL, 'Piece(s)', 288.00, 10, 10, 'Available', NULL),
	(125, 'SUP-2026-03-0096', 'Flexible Hose', '1/2 x 1/2 x 18"', NULL, 'Piece(s)', 10.00, 67, 10, 'Available', NULL),
	(126, 'SUP-2026-03-0097', 'Flat Cord', '(Electrical) No. 16, 130m per roll', NULL, 'Roll', 1490.00, 2, 10, 'Available', NULL),
	(127, 'SUP-2026-03-0098', 'Outlet', '3 gang Surface Type  universal outlet', NULL, 'Piece(s)', 169.00, 17, 10, 'Available', NULL),
	(128, 'SUP-2026-03-0099', 'Rubber Plug', 'Heavy Duty', NULL, 'Piece(s)', 98.00, 18, 10, 'Available', NULL),
	(129, 'SUP-2026-03-0100', 'Aircon Outlet', 'Wide  universal outlet with ground', NULL, 'Piece(s)', 200.00, 5, 10, 'Available', NULL),
	(130, 'SUP-2026-03-0101', 'Angle valve', '3 way 1/2x1/2 with dual switch', NULL, 'Piece(s)', 570.00, 10, 10, 'Available', NULL),
	(131, 'SUP-2026-03-0102', 'Doorknob', 'HD, branded', NULL, 'Piece/s', 490.00, 20, 10, 'Available', NULL),
	(132, 'SUP-2026-03-0103', 'Air freshener', 'aerosol 280 ml', NULL, 'Can', 280.00, 40, 10, 'Available', NULL),
	(133, 'SUP-2026-03-0104', 'Fastener', 'metal, good quality, 50 sets per box', NULL, 'Box', 49.00, 100, 10, 'Available', NULL),
	(134, 'SUP-2026-03-0105', 'Staple Wire Remover', 'plier type', NULL, 'Piece(s)', 61.00, 12, 10, 'Available', NULL),
	(135, 'SUP-2026-03-0106', 'Hand Sanitizer', '60ml, good quality', NULL, 'Bottle', 45.00, 100, 10, 'Available', NULL),
	(136, 'SUP-2026-03-0107', 'Steel  rack', '5 layers, good quality', NULL, 'Set', 3300.00, 3, 2, 'Available', NULL),
	(137, 'SUP-2026-03-0108', 'Flash drive', '32 GB', NULL, 'Piece(s)', 315.00, 10, 10, 'Available', NULL),
	(138, 'SUP-2026-03-0109', 'Glue', '240 grams, good quality', NULL, 'Bottle', 122.00, 50, 10, 'Available', NULL),
	(139, 'SUP-2026-03-0110', 'Electrical Tape', '3 meters, 19x6mm, black PVC', NULL, 'Roll', 48.00, 20, 10, 'Available', NULL),
	(140, 'SUP-2026-03-0111', 'Marker', 'Permanent, Black, good quality', NULL, 'Piece(s)', 15.00, 366, 10, 'Available', NULL),
	(141, 'SUP-2026-03-0112', 'Marker', 'Permanent, Blue, good quality', NULL, 'Piece(s)', 15.00, 186, 10, 'Available', NULL),
	(142, 'SUP-2026-03-0113', 'Marker', 'Permanent, Red, good quality', NULL, 'Piece(s)', 15.00, 29, 10, 'Available', NULL),
	(143, 'SUP-2026-03-0114', 'Marker', 'Whiteboard, Black, good quality', NULL, 'Piece(s)', 28.00, 166, 10, 'Available', NULL),
	(144, 'SUP-2026-03-0115', 'Marker', 'Whiteboard, Blue, good quality', NULL, 'Piece(s)', 28.00, 166, 10, 'Available', NULL),
	(145, 'SUP-2026-03-0116', 'Marker', 'Whiteboard, Red, good quality', NULL, 'Piece(s)', 28.00, 66, 10, 'Available', NULL),
	(146, 'SUP-2026-03-0117', 'Brother Ink', 'BT D60, black, original', NULL, 'Bottle', 485.00, 74, 10, 'Available', NULL),
	(147, 'SUP-2026-03-0118', 'Brother Ink', 'BT5000C, original', NULL, 'Bottle', 510.00, 64, 10, 'Available', NULL),
	(148, 'SUP-2026-03-0119', 'Brother Ink', 'BT 5000M, original', NULL, 'Bottle', 510.00, 56, 10, 'Available', NULL),
	(149, 'SUP-2026-03-0120', 'Brother Ink', 'BT 5000Y, original', NULL, 'Bottle', 510.00, 56, 10, 'Available', NULL),
	(150, 'SUP-2026-03-0121', 'File Organizer', 'expanding, plastic, 12 pockets', NULL, 'Piece(s)', 151.00, 48, 10, 'Available', NULL),
	(151, 'SUP-2026-03-0122', 'Matrix Ribbon', 'Epson LX-310 Dot', NULL, 'Piece(s)', 229.00, 10, 10, 'Available', NULL),
	(152, 'SUP-2026-03-0123', 'Cartridge', 'HP 46, Black', NULL, 'Piece(s)', 890.00, 5, 10, 'Available', NULL),
	(153, 'SUP-2026-03-0124', 'Cartridge', 'HP 46, tricolor', NULL, 'Piece(s)', 890.00, 5, 10, 'Available', NULL),
	(154, 'SUP-2026-03-0125', 'AMS Testing', 'testing testing', 'Pandayan', 'Piece(s)', 10.00, 47, 10, 'Available', NULL),
	(161, 'SUP-2026-05-0006', 'Tricycle', NULL, NULL, 'Unit', 20000.00, 0, 10, 'Available', NULL),
	(162, 'SUP-2026-05-0007', 'Bond paper', 'A4', 'Skylark Graphics Solution', 'Piece(s)', 500.00, 500, 10, 'Available', NULL),
	(163, 'SUP-2026-05-0008', 'Ballpen', 'Black', NULL, 'Piece(s)', 5.00, 150, 10, 'Available', NULL),
	(164, 'SUP-2026-05-0009', 'Ballpen', NULL, NULL, 'Piece(s)', 4.00, 50, 10, 'Available', NULL),
	(166, 'SUP-2026-05-0011', 'Bond paper', 'A4 short', 'Pandayan', 'Ream', 200.00, 0, 10, 'Available', NULL);

-- Dumping structure for table ams_inventory.system_settings
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ams_inventory.system_settings: ~4 rows (approximately)
INSERT INTO `system_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
	(1, 'seq_ris_no', '26', '2026-04-20 17:21:59', '2026-05-12 00:44:50'),
	(2, 'seq_par_no', '4', '2026-04-20 18:33:04', '2026-05-04 00:23:13'),
	(3, 'seq_sphv_no', '2', '2026-04-20 18:33:04', '2026-05-03 05:24:48'),
	(4, 'seq_splv_no', '2', '2026-04-20 18:33:04', '2026-05-04 00:21:11'),
	(5, 'seq_stock_no', '12', '2026-04-20 21:27:57', '2026-05-12 00:43:08');

-- Dumping structure for table ams_inventory.transactions
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `item_id` int NOT NULL,
  `item_type` enum('assets','supplies') COLLATE utf8mb4_general_ci NOT NULL,
  `transaction_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `quantity` int NOT NULL,
  `supplier` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_date` date DEFAULT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=238 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table ams_inventory.transactions: ~149 rows (approximately)
INSERT INTO `transactions` (`id`, `item_id`, `item_type`, `transaction_type`, `quantity`, `supplier`, `transaction_date`, `remarks`, `date_time`) VALUES
	(70, 28, 'supplies', 'Added', 500, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 03:24:35'),
	(71, 28, 'supplies', 'IN', 1, NULL, '2026-03-17', 'Scanner', '2026-03-17 03:50:14'),
	(72, 29, 'supplies', 'Added', 20, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 06:52:51'),
	(74, 31, 'supplies', 'Added', 20000, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 06:54:09'),
	(75, 32, 'supplies', 'Added', 1200, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 06:59:08'),
	(76, 33, 'supplies', 'Added', 460, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:00:40'),
	(77, 34, 'supplies', 'Added', 386, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:02:30'),
	(78, 35, 'supplies', 'Added', 170, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:05:04'),
	(79, 36, 'supplies', 'Added', 261, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:06:55'),
	(80, 37, 'supplies', 'Added', 217, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:09:20'),
	(81, 38, 'supplies', 'Added', 200, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:11:54'),
	(82, 39, 'supplies', 'Added', 49, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:14:00'),
	(83, 40, 'supplies', 'Added', 20, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:16:52'),
	(84, 41, 'supplies', 'Added', 10, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:19:13'),
	(85, 42, 'supplies', 'Added', 200, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:21:39'),
	(86, 43, 'supplies', 'Added', 200, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:23:08'),
	(87, 44, 'supplies', 'Added', 200, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:24:43'),
	(88, 45, 'supplies', 'Added', 200, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:27:13'),
	(89, 46, 'supplies', 'Added', 200, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:28:20'),
	(90, 47, 'supplies', 'Added', 200, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:29:49'),
	(91, 48, 'supplies', 'Added', 20, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:31:48'),
	(92, 49, 'supplies', 'Added', 20, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 07:33:00'),
	(93, 50, 'supplies', 'Added', 500, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:02:45'),
	(96, 53, 'supplies', 'Added', 200, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:16:56'),
	(97, 54, 'supplies', 'Added', 200, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:28:58'),
	(98, 55, 'supplies', 'Added', 20, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:31:13'),
	(99, 56, 'supplies', 'Added', 21, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:32:48'),
	(100, 57, 'supplies', 'Added', 100, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:34:19'),
	(101, 58, 'supplies', 'Added', 40, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:35:22'),
	(102, 59, 'supplies', 'Added', 310, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:36:06'),
	(103, 60, 'supplies', 'Added', 40, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:36:51'),
	(104, 61, 'supplies', 'Added', 50, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:39:52'),
	(105, 62, 'supplies', 'Added', 4, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:43:12'),
	(106, 63, 'supplies', 'Added', 50, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:45:41'),
	(107, 64, 'supplies', 'Added', 24, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:46:44'),
	(108, 65, 'supplies', 'Added', 24, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:47:41'),
	(109, 66, 'supplies', 'Added', 24, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:49:05'),
	(110, 67, 'supplies', 'Added', 126, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:51:04'),
	(111, 68, 'supplies', 'Added', 400, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:52:09'),
	(112, 69, 'supplies', 'Added', 200, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:53:01'),
	(113, 70, 'supplies', 'Added', 200, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:54:18'),
	(114, 71, 'supplies', 'Added', 32, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:56:22'),
	(115, 72, 'supplies', 'Added', 200, NULL, '2026-03-17', 'Opening Balance / New Item', '2026-03-17 08:57:48'),
	(116, 73, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 00:44:44'),
	(117, 74, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 00:45:50'),
	(118, 75, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 00:47:32'),
	(119, 76, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 00:48:25'),
	(120, 77, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 00:50:07'),
	(121, 78, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 00:53:04'),
	(122, 79, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 00:53:59'),
	(123, 80, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 00:56:53'),
	(124, 81, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 00:57:38'),
	(125, 82, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 00:58:25'),
	(126, 83, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 00:59:24'),
	(127, 84, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 01:00:17'),
	(128, 85, 'supplies', 'Added', 150, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 01:03:48'),
	(129, 86, 'supplies', 'Added', 100, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 01:06:42'),
	(130, 87, 'supplies', 'Added', 100, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 01:09:38'),
	(131, 88, 'supplies', 'Added', 100, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 01:11:07'),
	(132, 89, 'supplies', 'Added', 300, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 01:14:34'),
	(133, 90, 'supplies', 'Added', 50, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 01:15:42'),
	(134, 91, 'supplies', 'Added', 300, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 01:20:24'),
	(136, 93, 'supplies', 'Added', 300, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 01:26:30'),
	(137, 94, 'supplies', 'Added', 10, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 01:28:31'),
	(138, 95, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 01:30:31'),
	(139, 96, 'supplies', 'Added', 30, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 01:54:59'),
	(140, 97, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 01:56:41'),
	(141, 98, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 01:58:59'),
	(142, 99, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:00:11'),
	(144, 100, 'supplies', 'Added', 100, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:01:50'),
	(145, 101, 'supplies', 'Added', 120, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:06:50'),
	(146, 102, 'supplies', 'Added', 120, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:08:23'),
	(147, 103, 'supplies', 'Added', 32, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:10:47'),
	(148, 104, 'supplies', 'Added', 50, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:13:30'),
	(149, 105, 'supplies', 'Added', 300, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:23:16'),
	(150, 106, 'supplies', 'Added', 300, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:24:22'),
	(151, 107, 'supplies', 'Added', 200, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:25:57'),
	(152, 108, 'supplies', 'Added', 200, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:30:04'),
	(153, 109, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:31:34'),
	(154, 110, 'supplies', 'Added', 50, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:33:22'),
	(155, 111, 'supplies', 'Added', 120, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:34:16'),
	(156, 112, 'supplies', 'Added', 26, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:37:01'),
	(157, 113, 'supplies', 'Added', 120, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:38:37'),
	(158, 114, 'supplies', 'Added', 120, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:39:16'),
	(159, 115, 'supplies', 'Added', 120, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:40:17'),
	(160, 116, 'supplies', 'Added', 100, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:41:11'),
	(161, 117, 'supplies', 'Added', 120, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:42:05'),
	(162, 118, 'supplies', 'Added', 35, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:42:56'),
	(163, 119, 'supplies', 'Added', 200, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:43:38'),
	(164, 120, 'supplies', 'Added', 50, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:46:23'),
	(165, 121, 'supplies', 'Added', 300, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:47:05'),
	(166, 122, 'supplies', 'Added', 300, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:47:48'),
	(167, 123, 'supplies', 'Added', 200, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:48:40'),
	(168, 124, 'supplies', 'Added', 10, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:49:35'),
	(169, 125, 'supplies', 'Added', 67, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 02:50:36'),
	(170, 126, 'supplies', 'Added', 2, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 03:44:22'),
	(171, 127, 'supplies', 'Added', 17, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 03:53:19'),
	(172, 128, 'supplies', 'Added', 18, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 03:53:59'),
	(173, 129, 'supplies', 'Added', 11, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 03:55:16'),
	(174, 130, 'supplies', 'Added', 10, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 03:56:38'),
	(175, 131, 'supplies', 'Added', 10, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 03:56:40'),
	(176, 132, 'supplies', 'Added', 40, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 04:00:07'),
	(177, 133, 'supplies', 'Added', 100, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:15:11'),
	(178, 134, 'supplies', 'Added', 12, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:16:14'),
	(179, 135, 'supplies', 'Added', 100, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:17:29'),
	(180, 136, 'supplies', 'Added', 3, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:21:29'),
	(181, 137, 'supplies', 'Added', 10, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:25:04'),
	(182, 138, 'supplies', 'Added', 50, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:25:59'),
	(183, 139, 'supplies', 'Added', 20, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:27:07'),
	(184, 140, 'supplies', 'Added', 366, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:28:53'),
	(185, 141, 'supplies', 'Added', 186, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:29:41'),
	(186, 142, 'supplies', 'Added', 29, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:30:22'),
	(187, 143, 'supplies', 'Added', 166, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:31:14'),
	(188, 144, 'supplies', 'Added', 166, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:31:50'),
	(189, 145, 'supplies', 'Added', 66, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:32:22'),
	(190, 146, 'supplies', 'Added', 74, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:33:56'),
	(191, 147, 'supplies', 'Added', 64, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:35:12'),
	(192, 148, 'supplies', 'Added', 56, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:37:13'),
	(193, 149, 'supplies', 'Added', 56, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:38:31'),
	(194, 150, 'supplies', 'Added', 48, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:40:02'),
	(195, 151, 'supplies', 'Added', 10, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:45:37'),
	(196, 152, 'supplies', 'Added', 5, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:47:18'),
	(197, 153, 'supplies', 'Added', 5, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:48:00'),
	(198, 154, 'supplies', 'Added', 50, NULL, '2026-03-18', 'Opening Balance / New Item', '2026-03-18 05:49:42'),
	(199, 154, 'supplies', 'OUT', 2, NULL, '2026-03-18', 'RIS Auto-Release: RIS-2026-03-0004', '2026-03-18 05:57:49'),
	(203, 154, 'supplies', 'IN', 1, NULL, '2026-04-21', 'Added from duplicate check', '2026-04-21 05:49:21'),
	(204, 154, 'supplies', 'IN', 1, NULL, '2026-04-21', 'Added from duplicate check', '2026-04-21 05:50:06'),
	(207, 55, 'supplies', 'OUT', 101, NULL, '2026-04-27', 'RIS Auto-Release: RIS-2026-04-0004', '2026-04-27 06:05:32'),
	(208, 154, 'supplies', 'IN', 1, NULL, '2026-04-28', 'Scanner', '2026-04-28 01:33:48'),
	(209, 9, 'assets', 'OUT', 1, NULL, '2026-04-29', 'Marked as Defective/Unserviceable', '2026-04-28 19:25:47'),
	(210, 9, 'assets', 'IN', 1, NULL, '2026-04-29', 'Returned (Serviceable)', '2026-04-28 19:25:58'),
	(211, 9, 'assets', 'OUT', 1, NULL, '2026-04-29', 'Marked as Defective/Unserviceable', '2026-04-28 19:28:19'),
	(214, 9, 'assets', 'IN', 1, NULL, '2026-04-30', 'Returned (Serviceable)', '2026-04-29 21:52:12'),
	(215, 9, 'assets', 'OUT', 1, NULL, '2026-04-30', 'Marked as Defective/Unserviceable', '2026-04-29 22:37:21'),
	(216, 161, 'supplies', 'Added', 20000, NULL, '2026-05-04', 'Opening Balance / New Item', '2026-05-04 06:08:42'),
	(220, 162, 'supplies', 'Added', 500, 'Skylark Graphics Solution', '2026-05-04', 'Opening Balance / New Item', '2026-05-04 06:59:54'),
	(221, 163, 'supplies', 'Added', 100, NULL, '2026-05-04', 'Opening Balance / New Item', '2026-05-04 07:00:27'),
	(222, 164, 'supplies', 'Added', 50, NULL, '2026-05-04', 'Opening Balance / New Item', '2026-05-04 07:01:27'),
	(224, 163, 'supplies', 'IN', 50, NULL, '2026-05-04', 'Added from duplicate check', '2026-05-04 07:04:47'),
	(225, 9, 'assets', 'IN', 1, NULL, '2026-05-04', 'Returned (Serviceable)', '2026-05-03 23:16:14'),
	(227, 129, 'supplies', 'OUT', 2, NULL, '2026-05-12', 'RIS Auto-Release: RIS-2026-05-0006', '2026-05-12 00:46:55'),
	(228, 62, 'supplies', 'OUT', 2, NULL, '2026-05-12', 'RIS Auto-Release: RIS-2026-05-0006', '2026-05-12 00:46:55'),
	(229, 7, 'assets', 'IN', 1, NULL, '2026-05-12', 'Returned (Serviceable)', '2026-05-11 22:47:06'),
	(230, 7, 'assets', 'IN', 1, NULL, '2026-05-12', 'Returned (Serviceable)', '2026-05-11 22:47:30'),
	(231, 7, 'assets', 'IN', 1, NULL, '2026-05-12', 'Returned (Serviceable)', '2026-05-11 22:48:00'),
	(232, 7, 'assets', 'OUT', 1, NULL, '2026-05-12', 'Marked as Defective/Unserviceable', '2026-05-11 22:48:32'),
	(233, 7, 'assets', 'IN', 1, NULL, '2026-05-12', 'Returned (Serviceable)', '2026-05-11 22:48:40'),
	(234, 7, 'assets', 'IN', 1, NULL, '2026-05-12', 'Returned (Serviceable)', '2026-05-11 23:19:34'),
	(235, 28, 'supplies', 'OUT', 5, NULL, '2026-05-12', 'RIS Auto-Release: RIS-2026-05-0024', '2026-05-12 08:40:49'),
	(236, 166, 'supplies', 'Added', 5, 'Pandayan', '2026-05-12', 'Opening Balance / New Item', '2026-05-12 08:43:08'),
	(237, 166, 'supplies', 'OUT', 5, 'Pandayan', '2026-05-12', 'RIS Auto-Release: RIS-2026-05-0025', '2026-05-12 08:46:53');

-- Dumping structure for table ams_inventory.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lastname` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','staff','frontuser') COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_general_ci DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `remember_token` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table ams_inventory.users: ~4 rows (approximately)
INSERT INTO `users` (`id`, `firstname`, `lastname`, `department`, `image`, `email`, `password`, `role`, `status`, `created_at`, `remember_token`) VALUES
	(1, 'Karen', 'Ocbian', 'Asset Management Section', '1778417265.jpg', 'admin@deped.gov.ph', '$2y$12$kCuXC3YHhZ3kYyNoaTDqouuDwPDaI22AIX92m1VCve/aaovC9N3rS', 'admin', 'Active', '2026-02-12 15:09:20', 'QKP72IyYSwJ9huVIGLxyf6oI2RzdRtwDdTk2jEBABUiBMhKAz2EjydOhmx4X'),
	(3, 'Asset Management Supply', 'Section', 'Personnel Section', '1777871877.jpg', 'user@deped.gov.ph', '$2y$12$4XoR/gYAcHmf/96zVTQI5eoNOARXoapPYmvSnZ0a54q70N9dL1mTC', 'frontuser', 'Active', '2026-02-12 15:09:20', 'shdNU2HXVXOB7M3cAZd8sqHJLH6XiJVgS4kWCi6lwaVAGXCwf0tC84bp5c27'),
	(4, 'Staff', 'AMS', 'Asset Management Section', '1778417291.jpg', 'staff@deped.gov.ph', '$2y$12$.f5Xurth74amozziJZI0RerBKH9TnAafVeK8mc6Q84I/gNlN/wTfS', 'staff', 'Active', '2026-02-12 15:18:56', 'OS2YWYWxRKc6mbYwrplcSsHxipWoaFHaqcdjL8I5NYElCgFKt9iobbrpTuSG'),
	(6, 'hakdog', 'try', 'General Services Unit', NULL, 'hakdog@deped.gov.ph', '$2y$12$X2nj4CetNGDCxDxi1iNpv.Ybv5Xc.dhe/mrG9DzBkLulnl8ahtuBq', 'frontuser', 'Active', '2026-03-30 18:34:18', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
