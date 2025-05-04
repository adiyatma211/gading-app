-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 04, 2025 at 04:06 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gading`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('haris|127.0.0.1', 'i:1;', 1746367367),
('haris|127.0.0.1:timer', 'i:1746367367;', 1746367367);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleteSts` tinyint NOT NULL DEFAULT '0',
  `createdBy` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updatedBy` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `nama`, `telepon`, `email`, `jenis_pelanggan`, `alamat`, `deleteSts`, `createdBy`, `updatedBy`, `created_at`, `updated_at`) VALUES
(22, 'Azzanty', '082226582306', '-', 'baru', 'JL.Gading Printing', 0, 'Haris', NULL, '2025-05-04 07:04:50', '2025-05-04 07:04:50'),
(23, 'Haris', '082226582306', '-', 'baru', 'JL.MUGAS BARAT X NO 21', 0, 'Haris', NULL, '2025-05-04 07:06:31', '2025-05-04 07:06:31'),
(24, 'haris', '0', '-', 'baru', 'tt', 0, 'Haris', NULL, '2025-05-04 07:34:08', '2025-05-04 07:34:08'),
(25, 'haris', '099', '-', 'baru', 'test', 0, 'Haris', NULL, '2025-05-04 07:38:29', '2025-05-04 07:38:29'),
(26, 'HARIS', '321', '-', 'baru', 'ASD', 0, 'Haris', NULL, '2025-05-04 07:41:25', '2025-05-04 07:41:25'),
(27, 'haris', '890', '-', 'baru', 'test', 0, 'Haris', NULL, '2025-05-04 07:43:39', '2025-05-04 07:43:39'),
(28, 'haris', '89080', '-', 'baru', 'test', 0, 'Haris', NULL, '2025-05-04 07:46:45', '2025-05-04 07:46:45'),
(29, 'dsa', '89080', '-', 'baru', 'test', 0, 'Haris', NULL, '2025-05-04 07:47:34', '2025-05-04 07:47:34'),
(30, 'test', '231', '-', 'baru', 'dsa', 0, 'Haris', NULL, '2025-05-04 07:50:45', '2025-05-04 07:50:45'),
(31, 'haris', '980801', '-', 'baru', 'test', 0, 'Haris', NULL, '2025-05-04 07:54:50', '2025-05-04 07:54:50'),
(32, 'haris', '0808', '-', 'baru', 'test', 0, 'Haris', NULL, '2025-05-04 08:01:57', '2025-05-04 08:01:57'),
(33, 'test', '1231', '-', 'baru', 'DASD', 0, 'Haris', NULL, '2025-05-04 08:04:45', '2025-05-04 08:04:45'),
(34, 'haris', '8080', '-', 'baru', 'test', 0, 'Haris', NULL, '2025-05-04 08:07:03', '2025-05-04 08:07:03'),
(35, 'haris', '090', '-', 'baru', 'test', 0, 'Haris', NULL, '2025-05-04 08:12:40', '2025-05-04 08:12:40'),
(36, 'haris', '08080', '-', 'baru', 'test', 0, 'Haris', NULL, '2025-05-04 08:14:53', '2025-05-04 08:14:53'),
(37, 'haris', '12345678', '-', 'baru', 'test', 0, 'Haris', NULL, '2025-05-04 08:20:29', '2025-05-04 08:20:29'),
(38, 'haris', '988', '-', 'baru', 'test', 0, 'Haris', NULL, '2025-05-04 08:24:00', '2025-05-04 08:24:00'),
(39, 'haris', '0000', '-', 'baru', 'tes', 0, 'Haris', NULL, '2025-05-04 08:27:51', '2025-05-04 08:27:51'),
(40, 'haris', '000', '-', 'baru', 'test', 0, 'Haris', NULL, '2025-05-04 08:28:38', '2025-05-04 08:28:38');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `historynotas`
--

CREATE TABLE `historynotas` (
  `id` bigint UNSIGNED NOT NULL,
  `transaction_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `nota_file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_cetak` timestamp NULL DEFAULT NULL,
  `deleteSts` tinyint NOT NULL DEFAULT '0',
  `createdBy` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updatedBy` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `historynotas`
--

INSERT INTO `historynotas` (`id`, `transaction_id`, `customer_id`, `nota_file`, `tanggal_cetak`, `deleteSts`, `createdBy`, `updatedBy`, `created_at`, `updated_at`) VALUES
(25, 46, 22, 'nota_20250504_140450_E2R5s.html', '2025-05-04 07:04:51', 0, 'Haris', 'Haris', '2025-05-04 07:04:51', '2025-05-04 07:04:51'),
(26, 47, 23, 'nota_20250504_140631_WrjWg.html', '2025-05-04 07:06:31', 0, 'Haris', 'Haris', '2025-05-04 07:06:31', '2025-05-04 07:06:31'),
(27, 48, 24, 'nota_20250504_143408_MXDjz.pdf', '2025-05-04 07:34:08', 0, 'Haris', 'Haris', '2025-05-04 07:34:08', '2025-05-04 07:34:08'),
(28, 53, 28, 'nota_20250504_144645_o83Z2.pdf', '2025-05-04 07:46:45', 0, 'Haris', 'Haris', '2025-05-04 07:46:45', '2025-05-04 07:46:45'),
(29, 54, 29, 'nota_20250504_144734_wXHNM.pdf', '2025-05-04 07:47:34', 0, 'Haris', 'Haris', '2025-05-04 07:47:34', '2025-05-04 07:47:34'),
(30, 55, 30, 'nota_20250504_145045_vEvJl.pdf', '2025-05-04 07:51:44', 0, 'Haris', 'Haris', '2025-05-04 07:51:44', '2025-05-04 07:51:44'),
(31, 56, 31, 'nota_20250504_145450_RG6ej.pdf', '2025-05-04 07:54:51', 0, 'Haris', 'Haris', '2025-05-04 07:54:51', '2025-05-04 07:54:51'),
(32, 57, 32, 'nota_20250504_150158_zpqWD.pdf', '2025-05-04 08:01:58', 0, 'Haris', 'Haris', '2025-05-04 08:01:58', '2025-05-04 08:01:58'),
(33, 58, 33, 'nota_20250504_150445_obL1R.pdf', '2025-05-04 08:04:46', 0, 'Haris', 'Haris', '2025-05-04 08:04:46', '2025-05-04 08:04:46'),
(34, 59, 34, 'nota_20250504_150704_haris.pdf', '2025-05-04 08:07:04', 0, 'Haris', 'Haris', '2025-05-04 08:07:04', '2025-05-04 08:07:04'),
(35, 60, 35, 'nota_20250504_151240_harissda .pdf', '2025-05-04 08:12:41', 0, 'Haris', 'Haris', '2025-05-04 08:12:41', '2025-05-04 08:12:41'),
(36, 61, 36, 'nota_20250504_151453_harissda .pdf', '2025-05-04 08:14:54', 0, 'Haris', 'Haris', '2025-05-04 08:14:54', '2025-05-04 08:14:54'),
(37, 62, 37, 'nota_20250504_152029_harissda .pdf', '2025-05-04 08:20:29', 0, 'Haris', 'Haris', '2025-05-04 08:20:29', '2025-05-04 08:20:29'),
(38, 63, 38, 'nota_20250504_152400_haris.pdf', '2025-05-04 08:24:00', 0, 'Haris', 'Haris', '2025-05-04 08:24:00', '2025-05-04 08:24:00'),
(39, 64, 39, 'nota_20250504_152751_haris.pdf', '2025-05-04 08:27:52', 0, 'Haris', 'Haris', '2025-05-04 08:27:52', '2025-05-04 08:27:52'),
(40, 65, 40, 'nota_20250504_152838_haris.pdf', '2025-05-04 08:28:38', 0, 'Haris', 'Haris', '2025-05-04 08:28:38', '2025-05-04 08:28:38'),
(41, 66, 39, 'nota_20250504_153220_haris.pdf', '2025-05-04 08:32:21', 0, 'Haris', 'Haris', '2025-05-04 08:32:21', '2025-05-04 08:32:21');

-- --------------------------------------------------------

--
-- Table structure for table `history_payments`
--

CREATE TABLE `history_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `biaya_desain` decimal(15,2) NOT NULL DEFAULT '0.00',
  `diskon` decimal(15,2) NOT NULL DEFAULT '0.00',
  `dp` decimal(15,2) NOT NULL DEFAULT '0.00',
  `metode_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bukti_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_item` int NOT NULL DEFAULT '0',
  `tanggal_transaksi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleteSts` tinyint NOT NULL DEFAULT '0',
  `createdBy` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updatedBy` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `history_payments`
--

INSERT INTO `history_payments` (`id`, `customer_name`, `telepon`, `email`, `jenis_pelanggan`, `alamat`, `subtotal`, `total`, `biaya_desain`, `diskon`, `dp`, `metode_pembayaran`, `bukti_pembayaran`, `jumlah_item`, `tanggal_transaksi`, `deleteSts`, `createdBy`, `updatedBy`, `created_at`, `updated_at`) VALUES
(40, 'Azzanty', '082226582306', '-', 'baru', 'JL.Gading Printing', '250000.00', '250000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-05-04 07:04:50', 0, 'Haris', 'Haris', '2025-05-04 07:04:50', '2025-05-04 07:04:50'),
(41, 'Haris', '082226582306', '-', 'baru', 'JL.MUGAS BARAT X NO 21', '385000.00', '770000.00', '20000.00', '0.00', '385000.00', 'tunai', NULL, 2, '2025-05-04 07:06:31', 0, 'Haris', 'Haris', '2025-05-04 07:06:31', '2025-05-04 07:06:31'),
(42, 'haris', '0', '-', 'baru', 'tt', '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, 1, '2025-05-04 07:34:08', 0, 'Haris', 'Haris', '2025-05-04 07:34:08', '2025-05-04 07:34:08'),
(43, 'haris', '099', '-', 'baru', 'test', '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, 1, '2025-05-04 07:38:29', 0, 'Haris', 'Haris', '2025-05-04 07:38:29', '2025-05-04 07:38:29'),
(44, 'haris', '099', '-', 'baru', 'test', '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, 1, '2025-05-04 07:40:26', 0, 'Haris', 'Haris', '2025-05-04 07:40:26', '2025-05-04 07:40:26'),
(45, 'HARIS', '321', '-', 'baru', 'ASD', '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, 1, '2025-05-04 07:41:25', 0, 'Haris', 'Haris', '2025-05-04 07:41:25', '2025-05-04 07:41:25'),
(46, 'haris', '890', '-', 'baru', 'test', '250000.00', '500000.00', '0.00', '0.00', '250000.00', 'tunai', NULL, 1, '2025-05-04 07:43:39', 0, 'Haris', 'Haris', '2025-05-04 07:43:39', '2025-05-04 07:43:39'),
(47, 'haris', '89080', '-', 'baru', 'test', '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, 1, '2025-05-04 07:46:45', 0, 'Haris', 'Haris', '2025-05-04 07:46:45', '2025-05-04 07:46:45'),
(48, 'dsa', '89080', '-', 'baru', 'test', '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, 1, '2025-05-04 07:47:34', 0, 'Haris', 'Haris', '2025-05-04 07:47:34', '2025-05-04 07:47:34'),
(49, 'test', '231', '-', 'baru', 'dsa', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-05-04 07:50:45', 0, 'Haris', 'Haris', '2025-05-04 07:50:45', '2025-05-04 07:50:45'),
(50, 'haris', '980801', '-', 'baru', 'test', '250000.00', '500000.00', '0.00', '0.00', '250000.00', 'tunai', NULL, 1, '2025-05-04 07:54:50', 0, 'Haris', 'Haris', '2025-05-04 07:54:50', '2025-05-04 07:54:50'),
(51, 'haris', '0808', '-', 'baru', 'test', '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, 1, '2025-05-04 08:01:57', 0, 'Haris', 'Haris', '2025-05-04 08:01:57', '2025-05-04 08:01:57'),
(52, 'test', '1231', '-', 'baru', 'DASD', '250000.00', '500000.00', '0.00', '0.00', '250000.00', 'tunai', NULL, 1, '2025-05-04 08:04:45', 0, 'Haris', 'Haris', '2025-05-04 08:04:45', '2025-05-04 08:04:45'),
(53, 'haris', '8080', '-', 'baru', 'test', '125000.00', '125000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-05-04 08:07:04', 0, 'Haris', 'Haris', '2025-05-04 08:07:04', '2025-05-04 08:07:04'),
(54, 'haris', '090', '-', 'baru', 'test', '250000.00', '500000.00', '0.00', '0.00', '250000.00', 'tunai', NULL, 1, '2025-05-04 08:12:40', 0, 'Haris', 'Haris', '2025-05-04 08:12:40', '2025-05-04 08:12:40'),
(55, 'haris', '08080', '-', 'baru', 'test', '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, 1, '2025-05-04 08:14:53', 0, 'Haris', 'Haris', '2025-05-04 08:14:53', '2025-05-04 08:14:53'),
(56, 'haris', '12345678', '-', 'baru', 'test', '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, 1, '2025-05-04 08:20:29', 0, 'Haris', 'Haris', '2025-05-04 08:20:29', '2025-05-04 08:20:29'),
(57, 'haris', '988', '-', 'baru', 'test', '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, 1, '2025-05-04 08:24:00', 0, 'Haris', 'Haris', '2025-05-04 08:24:00', '2025-05-04 08:24:00'),
(58, 'haris', '0000', '-', 'baru', 'tes', '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, 1, '2025-05-04 08:27:51', 0, 'Haris', 'Haris', '2025-05-04 08:27:51', '2025-05-04 08:27:51'),
(59, 'haris', '000', '-', 'baru', 'test', '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, 1, '2025-05-04 08:28:38', 0, 'Haris', 'Haris', '2025-05-04 08:28:38', '2025-05-04 08:28:38'),
(60, 'haris', '0000', '-', 'baru', 'tes', '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, 1, '2025-05-04 08:32:20', 0, 'Haris', 'Haris', '2025-05-04 08:32:20', '2025-05-04 08:32:20');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(4, '0001_01_01_000000_create_users_table', 1),
(5, '0001_01_01_000001_create_cache_table', 1),
(6, '0001_01_01_000002_create_jobs_table', 1),
(7, '2025_04_13_134852_create_roles_table', 2),
(8, '2025_04_16_163115_create_produks_table', 3),
(9, '2025_04_16_163230_create_produk_bahans_table', 3),
(10, '2025_04_16_170139_create_produk_bahans_table', 4),
(11, '2025_04_27_134414_create_customers_table', 5),
(12, '2025_04_27_134553_create_transactions_table', 5),
(13, '2025_04_27_140321_create_transaction_items_table', 5),
(14, '2025_04_27_144831_create_histoy_payments_table', 6),
(15, '2025_04_27_164647_create_historynotas_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produks`
--

CREATE TABLE `produks` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_produk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produks`
--

INSERT INTO `produks` (`id`, `nama_produk`, `status`, `created_at`, `updated_at`) VALUES
(11, 'MMT', '1', '2025-05-04 07:03:13', '2025-05-04 07:03:13');

-- --------------------------------------------------------

--
-- Table structure for table `produk_bahans`
--

CREATE TABLE `produk_bahans` (
  `id` bigint UNSIGNED NOT NULL,
  `produk_id` bigint UNSIGNED NOT NULL,
  `nama_bahan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_per_meter` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk_bahans`
--

INSERT INTO `produk_bahans` (`id`, `produk_id`, `nama_bahan`, `harga_per_meter`, `created_at`, `updated_at`) VALUES
(19, 11, 'Frontlite 280', '25000', '2025-05-04 07:03:13', '2025-05-04 07:03:32'),
(20, 11, 'Frontlite 110', '5000', '2025-05-04 07:03:13', '2025-05-04 07:03:13');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `rolesName` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleteSts` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `rolesName`, `keterangan`, `deleteSts`, `created_at`, `updated_at`) VALUES
(1, 'SuperAdmin', 'TEST', 1, '2025-04-13 07:23:38', '2025-04-13 07:41:29'),
(2, 'Super Admin', 'Hak Akses Owner', 0, '2025-04-13 07:44:34', '2025-05-04 05:56:00'),
(3, 'staff', 'staff', 1, '2025-04-16 07:49:01', '2025-04-16 07:57:31'),
(4, 'karyawan', 'kasir', 1, '2025-04-16 07:58:03', '2025-04-16 08:03:13'),
(5, 'Owner', 'Pemilik', 0, '2025-04-16 08:04:04', '2025-04-16 08:04:04'),
(6, 'Finance', 'keuangan', 0, '2025-04-16 08:04:26', '2025-04-16 08:04:26'),
(7, 'kasir', 'Akses Kasir', 0, '2025-05-04 05:55:39', '2025-05-04 05:55:39');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('AGv9GOxCGh3OKfVt4MOagBTpZm8CIBtpAY51jGKg', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoib3lEZzUxZXUzeDQ1eFJBcEtTSW9pVkRrd1VhdkRkeHBMRTFUc1I1dyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9kdWsiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO3M6NDoiYXV0aCI7YToxOntzOjIxOiJwYXNzd29yZF9jb25maXJtZWRfYXQiO2k6MTc0NjM2NzMzNDt9fQ==', 1746367423),
('kXB119IEOLErY4MZBBMPScr3izP3UXUZDFZi9sCb', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNUlRbTVvQ2tKZG1FUktLVXgxd0RqbVVMdTJIVTZMNnlkS3BTZjdBViI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjQ6ImF1dGgiO2E6MTp7czoyMToicGFzc3dvcmRfY29uZmlybWVkX2F0IjtpOjE3NDYzNjcyNDg7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZGFzaGJvYXJkIjt9fQ==', 1746374396);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `biaya_desain` decimal(15,2) NOT NULL DEFAULT '0.00',
  `diskon` decimal(15,2) NOT NULL DEFAULT '0.00',
  `dp` decimal(15,2) NOT NULL DEFAULT '0.00',
  `metode_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bukti_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_ambil` timestamp NULL DEFAULT NULL,
  `tanggal_transaksi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nota_file` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_faktur` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_pembayaran` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diambil_oleh` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bukti_pengambilan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_selesai` timestamp NULL DEFAULT NULL,
  `deleteSts` tinyint NOT NULL DEFAULT '0',
  `createdBy` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updatedBy` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `customer_id`, `subtotal`, `total`, `biaya_desain`, `diskon`, `dp`, `metode_pembayaran`, `bukti_pembayaran`, `tanggal_ambil`, `tanggal_transaksi`, `nota_file`, `nomor_faktur`, `status_pembayaran`, `diambil_oleh`, `bukti_pengambilan`, `tanggal_selesai`, `deleteSts`, `createdBy`, `updatedBy`, `created_at`, `updated_at`) VALUES
(46, 22, '250000.00', '250000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-05-09 17:00:00', '2025-05-04 07:04:50', 'nota_20250504_140450_E2R5s.html', 'GD-MMT-01-20250504', 'lunas', 'Azza', '1746373038_jff1Ncmd4wuDCmmC0IOnpKxp0ycJGmW6T1dCYNg2.png', '2025-05-04 08:37:00', 0, 'Haris', NULL, '2025-05-04 07:04:50', '2025-05-04 08:37:18'),
(47, 23, '385000.00', '770000.00', '20000.00', '0.00', '385000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 07:06:31', 'nota_20250504_140631_WrjWg.html', 'GD-MMT-02-20250504', 'dp', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 07:06:31', '2025-05-04 07:06:31'),
(48, 24, '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 07:34:08', 'nota_20250504_143408_MXDjz.pdf', 'GD-MMT-03-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 07:34:08', '2025-05-04 07:34:08'),
(49, 25, '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 07:38:29', NULL, 'GD-MMT-04-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 07:38:29', '2025-05-04 07:38:29'),
(50, 25, '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 07:40:26', NULL, 'GD-MMT-05-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 07:40:26', '2025-05-04 07:40:26'),
(51, 26, '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 07:41:25', NULL, 'GD-MMT-06-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 07:41:25', '2025-05-04 07:41:25'),
(52, 27, '250000.00', '500000.00', '0.00', '0.00', '250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 07:43:39', NULL, 'GD-MMT-07-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 07:43:39', '2025-05-04 07:43:39'),
(53, 28, '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 07:46:45', 'nota_20250504_144645_o83Z2.pdf', 'GD-MMT-08-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 07:46:45', '2025-05-04 07:46:45'),
(54, 29, '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 07:47:34', 'nota_20250504_144734_wXHNM.pdf', 'GD-MMT-09-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 07:47:34', '2025-05-04 07:47:34'),
(55, 30, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 07:50:45', 'nota_20250504_145045_vEvJl.pdf', 'GD-MMT-10-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 07:50:45', '2025-05-04 07:51:44'),
(56, 31, '250000.00', '500000.00', '0.00', '0.00', '250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 07:54:50', 'nota_20250504_145450_RG6ej.pdf', 'GD-MMT-11-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 07:54:50', '2025-05-04 07:54:51'),
(57, 32, '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 08:01:57', 'nota_20250504_150158_zpqWD.pdf', 'GD-MMT-12-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 08:01:57', '2025-05-04 08:01:58'),
(58, 33, '250000.00', '500000.00', '0.00', '0.00', '250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 08:04:45', 'nota_20250504_150445_obL1R.pdf', 'GD-MMT-13-20250504', 'dp', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 08:04:45', '2025-05-04 08:04:46'),
(59, 34, '125000.00', '125000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 08:07:04', 'nota_20250504_150704_haris.pdf', 'GD-MMT-14-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 08:07:04', '2025-05-04 08:07:04'),
(60, 35, '250000.00', '500000.00', '0.00', '0.00', '250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 08:12:40', 'nota_20250504_151240_harissda .pdf', 'GD-MMT-15-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 08:12:40', '2025-05-04 08:12:41'),
(61, 36, '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 08:14:53', 'nota_20250504_151453_harissda .pdf', 'GD-MMT-16-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 08:14:53', '2025-05-04 08:14:54'),
(62, 37, '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 08:20:29', 'nota_20250504_152029_harissda .pdf', 'GD-MMT-17-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 08:20:29', '2025-05-04 08:20:29'),
(63, 38, '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 08:24:00', 'nota_20250504_152400_haris.pdf', 'GD-MMT-18-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 08:24:00', '2025-05-04 08:24:00'),
(64, 39, '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 08:27:51', 'nota_20250504_152751_haris.pdf', 'GD-MMT-19-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 08:27:51', '2025-05-04 08:27:51'),
(65, 40, '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 08:28:38', 'nota_20250504_152838_haris.pdf', 'GD-MMT-20-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 08:28:38', '2025-05-04 08:28:38'),
(66, 39, '1250000.00', '2500000.00', '0.00', '0.00', '1250000.00', 'tunai', NULL, '2025-05-03 17:00:00', '2025-05-04 08:32:20', 'nota_20250504_153220_haris.pdf', 'GD-MMT-21-20250504', 'lunas', NULL, NULL, NULL, 0, 'Haris', NULL, '2025-05-04 08:32:20', '2025-05-04 08:32:21');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id` bigint UNSIGNED NOT NULL,
  `transaction_id` bigint UNSIGNED NOT NULL,
  `tipe_produk_id` bigint UNSIGNED DEFAULT NULL,
  `panjang` decimal(8,2) NOT NULL DEFAULT '0.00',
  `lebar` decimal(8,2) NOT NULL DEFAULT '0.00',
  `harga_per_meter` decimal(15,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `createdBy` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updatedBy` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleteSts` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`id`, `transaction_id`, `tipe_produk_id`, `panjang`, `lebar`, `harga_per_meter`, `keterangan`, `createdBy`, `updatedBy`, `deleteSts`, `created_at`, `updated_at`) VALUES
(58, 46, 19, '10.00', '1.00', '25000.00', 'LUNAS', 'Haris', NULL, 0, '2025-05-04 07:04:50', '2025-05-04 07:04:50'),
(59, 47, 19, '5.00', '2.00', '25000.00', 'YANG BAGUS', 'Haris', NULL, 0, '2025-05-04 07:06:31', '2025-05-04 07:06:31'),
(60, 47, 20, '10.00', '10.00', '5000.00', 'INI JUGA', 'Haris', NULL, 0, '2025-05-04 07:06:31', '2025-05-04 07:06:31'),
(61, 48, 19, '10.00', '10.00', '25000.00', NULL, 'Haris', NULL, 0, '2025-05-04 07:34:08', '2025-05-04 07:34:08'),
(62, 49, 19, '10.00', '10.00', '25000.00', '321', 'Haris', NULL, 0, '2025-05-04 07:38:29', '2025-05-04 07:38:29'),
(63, 50, 19, '10.00', '10.00', '25000.00', '321', 'Haris', NULL, 0, '2025-05-04 07:40:26', '2025-05-04 07:40:26'),
(64, 51, 19, '10.00', '10.00', '25000.00', NULL, 'Haris', NULL, 0, '2025-05-04 07:41:25', '2025-05-04 07:41:25'),
(65, 52, 20, '10.00', '10.00', '5000.00', NULL, 'Haris', NULL, 0, '2025-05-04 07:43:39', '2025-05-04 07:43:39'),
(66, 53, 19, '10.00', '10.00', '25000.00', NULL, 'Haris', NULL, 0, '2025-05-04 07:46:45', '2025-05-04 07:46:45'),
(67, 54, 19, '10.00', '10.00', '25000.00', NULL, 'Haris', NULL, 0, '2025-05-04 07:47:34', '2025-05-04 07:47:34'),
(68, 55, 19, '1.00', '1.00', '25000.00', NULL, 'Haris', NULL, 0, '2025-05-04 07:50:45', '2025-05-04 07:50:45'),
(69, 56, 20, '10.00', '10.00', '5000.00', NULL, 'Haris', NULL, 0, '2025-05-04 07:54:50', '2025-05-04 07:54:50'),
(70, 57, 19, '10.00', '10.00', '25000.00', NULL, 'Haris', NULL, 0, '2025-05-04 08:01:57', '2025-05-04 08:01:57'),
(71, 58, 20, '10.00', '10.00', '5000.00', 'DSA', 'Haris', NULL, 0, '2025-05-04 08:04:45', '2025-05-04 08:04:45'),
(72, 59, 20, '5.00', '5.00', '5000.00', NULL, 'Haris', NULL, 0, '2025-05-04 08:07:04', '2025-05-04 08:07:04'),
(73, 60, 20, '10.00', '10.00', '5000.00', NULL, 'Haris', NULL, 0, '2025-05-04 08:12:40', '2025-05-04 08:12:40'),
(74, 61, 19, '10.00', '10.00', '25000.00', NULL, 'Haris', NULL, 0, '2025-05-04 08:14:53', '2025-05-04 08:14:53'),
(75, 62, 19, '10.00', '10.00', '25000.00', NULL, 'Haris', NULL, 0, '2025-05-04 08:20:29', '2025-05-04 08:20:29'),
(76, 63, 19, '10.00', '10.00', '25000.00', NULL, 'Haris', NULL, 0, '2025-05-04 08:24:00', '2025-05-04 08:24:00'),
(77, 64, 19, '10.00', '10.00', '25000.00', NULL, 'Haris', NULL, 0, '2025-05-04 08:27:51', '2025-05-04 08:27:51'),
(78, 65, 19, '10.00', '10.00', '25000.00', NULL, 'Haris', NULL, 0, '2025-05-04 08:28:38', '2025-05-04 08:28:38'),
(79, 66, 19, '10.00', '10.00', '25000.00', NULL, 'Haris', NULL, 0, '2025-05-04 08:32:20', '2025-05-04 08:32:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleteSts` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `role_id`, `deleteSts`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Haris', 'haris12', '$2y$12$Xuj3oMxbyWikuecJx2rrk.7uJ8EI78W/kjuhL6Kf.3ehXkDfUuLT.', '7', '0', NULL, '2025-04-13 02:39:51', '2025-04-16 08:52:44'),
(2, 'haris', 'test', '$2y$12$n9WLb0yewdMZG/lChpGCROI3As4z7HG3vLx949ti/8YRQ5agJeKjq', '1', '1', NULL, '2025-04-16 08:24:39', '2025-04-16 09:02:53'),
(3, 'uhuy', 'uhuy', '$2y$12$3/LO86OO2olURWcBOkZEaePRFPHXG.HgiWb6AqrITSwZScdjBqO7m', '1', '0', NULL, '2025-04-16 08:40:36', '2025-04-16 08:40:36'),
(4, 'uhuy', 'uhuyss', '$2y$12$diABOAmm/YrKbu/7WNns1Om2v5X4sL3TV.zjlp5kJBdHexbUWi5S6', NULL, '0', NULL, '2025-04-16 08:43:09', '2025-04-16 08:43:09'),
(5, 'eng ing eng', 'engg', '$2y$12$0mCGuS/87FyndcFTlFhLQePNG3w03GZENM.CrwT5EK7Vfe1UyWmfm', '2', '0', NULL, '2025-04-16 08:44:18', '2025-04-16 08:44:18'),
(6, 'aku baru sekali', 'baru', '$2y$12$yystwAHXmuNnxq6aNmGju.XEnsHW3wS1N0Xl6qi8SmtBE3LoBPoFO', '5', '0', NULL, '2025-04-16 09:00:51', '2025-04-16 09:02:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `historynotas`
--
ALTER TABLE `historynotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `historynotas_transaction_id_foreign` (`transaction_id`),
  ADD KEY `historynotas_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `history_payments`
--
ALTER TABLE `history_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `produks`
--
ALTER TABLE `produks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk_bahans`
--
ALTER TABLE `produk_bahans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produk_bahans_produk_id_foreign` (`produk_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_items_transaction_id_foreign` (`transaction_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `historynotas`
--
ALTER TABLE `historynotas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `history_payments`
--
ALTER TABLE `history_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `produks`
--
ALTER TABLE `produks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `produk_bahans`
--
ALTER TABLE `produk_bahans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `historynotas`
--
ALTER TABLE `historynotas`
  ADD CONSTRAINT `historynotas_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `historynotas_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `produk_bahans`
--
ALTER TABLE `produk_bahans`
  ADD CONSTRAINT `produk_bahans_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
