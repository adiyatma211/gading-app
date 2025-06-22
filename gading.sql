-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 22, 2025 at 06:01 PM
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
(1, 'haris', '123', '-', 'baru', 'dsa', 0, 'Superadmin', NULL, '2025-06-20 08:49:18', '2025-06-20 08:49:18'),
(2, 'JOKO', '123', '-', 'baru', 'DS', 0, 'Superadmin', NULL, '2025-06-20 08:53:21', '2025-06-20 08:53:21'),
(3, 'dsa', '111', '-', 'baru', '12', 0, 'Superadmin', NULL, '2025-06-20 08:56:35', '2025-06-20 08:56:35'),
(4, 'dsasda', '2121', '-', 'baru', 'sdad', 0, 'Superadmin', NULL, '2025-06-20 08:57:15', '2025-06-20 08:57:15'),
(5, 'dsa', '222', '-', 'baru', 'sss', 0, 'Superadmin', NULL, '2025-06-20 08:59:48', '2025-06-20 08:59:48'),
(6, 'dsad', '231', '-', 'baru', 'dsa', 0, 'Superadmin', NULL, '2025-06-20 09:00:46', '2025-06-20 09:00:46'),
(7, 'ewq', '231', '-', 'baru', 'sdad', 0, 'Superadmin', NULL, '2025-06-20 09:01:47', '2025-06-20 09:01:47'),
(8, 'haris', '8989', '-', 'baru', 'dsa', 0, 'Superadmin', NULL, '2025-06-20 09:09:29', '2025-06-20 09:09:29'),
(9, 'haris', '676868', '-', 'baru', 'test', 0, 'Superadmin', NULL, '2025-06-21 01:56:59', '2025-06-21 01:56:59'),
(10, 'haris', '789797', '-', 'baru', 'test', 0, 'Superadmin', NULL, '2025-06-22 09:19:57', '2025-06-22 09:19:57'),
(11, 'haris', '7898', '-', 'baru', 'test', 0, 'Superadmin', NULL, '2025-06-22 09:28:57', '2025-06-22 09:28:57'),
(12, 'haris', '890', '-', 'baru', 'test', 0, 'Superadmin', NULL, '2025-06-22 09:33:09', '2025-06-22 09:33:09'),
(13, 'haris', '687686', '-', 'baru', 'test', 0, 'Superadmin', NULL, '2025-06-22 09:39:02', '2025-06-22 09:39:02'),
(18, 'hha', '6868687', '-', 'baru', 'test', 0, 'Superadmin', NULL, '2025-06-22 10:00:49', '2025-06-22 10:00:49'),
(19, 'hari', '7979', '-', 'baru', 'yest', 0, 'Superadmin', NULL, '2025-06-22 10:28:27', '2025-06-22 10:28:27'),
(20, 'ha', '67868', '-', 'baru', 'test', 0, 'Superadmin', NULL, '2025-06-22 10:30:52', '2025-06-22 10:30:52'),
(21, 'haris', '7979879', '-', 'baru', 'test', 0, 'Superadmin', NULL, '2025-06-22 10:33:03', '2025-06-22 10:33:03'),
(22, 'hendi', '123132', '-', 'baru', 'ress', 0, 'Superadmin', NULL, '2025-06-22 10:37:01', '2025-06-22 10:37:01');

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
-- Table structure for table `harga_produk`
--

CREATE TABLE `harga_produk` (
  `id` bigint UNSIGNED NOT NULL,
  `produk_id` bigint UNSIGNED NOT NULL,
  `min_qty` int DEFAULT NULL,
  `max_qty` int DEFAULT NULL,
  `sisi` int DEFAULT NULL,
  `laminasi` tinyint(1) NOT NULL DEFAULT '0',
  `harga` decimal(15,2) NOT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `harga_produk_new`
--

CREATE TABLE `harga_produk_new` (
  `id` bigint UNSIGNED NOT NULL,
  `produk_id` bigint UNSIGNED NOT NULL,
  `min_qty` int DEFAULT NULL,
  `max_qty` int DEFAULT NULL,
  `sisi` int DEFAULT NULL,
  `laminasi` tinyint(1) NOT NULL DEFAULT '0',
  `harga` decimal(15,2) NOT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diskon` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `harga_produk_new`
--

INSERT INTO `harga_produk_new` (`id`, `produk_id`, `min_qty`, `max_qty`, `sisi`, `laminasi`, `harga`, `satuan`, `diskon`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, NULL, 0, '25000.00', NULL, 5000, '2025-06-20 08:48:41', '2025-06-20 08:48:41'),
(2, 2, NULL, NULL, NULL, 0, '25000.00', NULL, NULL, '2025-06-22 09:46:31', '2025-06-22 09:46:31'),
(3, 3, 1, 10, NULL, 0, '12000.00', NULL, NULL, '2025-06-22 09:46:57', '2025-06-22 09:46:57'),
(4, 3, 11, 50, NULL, 0, '12000.00', NULL, NULL, '2025-06-22 09:46:57', '2025-06-22 09:46:57'),
(5, 3, 51, 100, NULL, 0, '11000.00', NULL, NULL, '2025-06-22 09:46:57', '2025-06-22 09:46:57'),
(6, 3, 101, 9999, NULL, 0, '10000.00', NULL, NULL, '2025-06-22 09:46:57', '2025-06-22 09:46:57'),
(7, 4, NULL, NULL, 2, 1, '150000.00', NULL, NULL, '2025-06-22 09:47:28', '2025-06-22 09:47:28');

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
(1, 1, 1, 'nota_20250620_154918_haris.pdf', '2025-06-20 08:49:23', 0, 'Superadmin', 'Superadmin', '2025-06-20 08:49:23', '2025-06-20 08:49:23'),
(2, 2, 2, 'nota_20250620_155322_joko.pdf', '2025-06-20 08:53:27', 0, 'Superadmin', 'Superadmin', '2025-06-20 08:53:27', '2025-06-20 08:53:27'),
(3, 3, 3, 'nota_20250620_155635_dsa.pdf', '2025-06-20 08:56:35', 0, 'Superadmin', 'Superadmin', '2025-06-20 08:56:35', '2025-06-20 08:56:35'),
(4, 4, 4, 'nota_20250620_155715_dsasda.pdf', '2025-06-20 08:57:16', 0, 'Superadmin', 'Superadmin', '2025-06-20 08:57:16', '2025-06-20 08:57:16'),
(5, 5, 5, 'nota_20250620_155948_dsa.pdf', '2025-06-20 08:59:48', 0, 'Superadmin', 'Superadmin', '2025-06-20 08:59:48', '2025-06-20 08:59:48'),
(6, 6, 6, 'nota_20250620_160046_dsad.pdf', '2025-06-20 09:00:47', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:00:47', '2025-06-20 09:00:47'),
(7, 7, 6, 'nota_20250620_160110_dsad.pdf', '2025-06-20 09:01:11', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:01:11', '2025-06-20 09:01:11'),
(8, 8, 7, 'nota_20250620_160147_ewq.pdf', '2025-06-20 09:01:47', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:01:47', '2025-06-20 09:01:47'),
(9, 9, 7, 'nota_20250620_160207_ewq.pdf', '2025-06-20 09:02:08', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:02:08', '2025-06-20 09:02:08'),
(10, 10, 7, 'nota_20250620_160242_ewq.pdf', '2025-06-20 09:02:43', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:02:43', '2025-06-20 09:02:43'),
(11, 11, 7, 'nota_20250620_160254_ewq.pdf', '2025-06-20 09:02:54', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:02:54', '2025-06-20 09:02:54'),
(12, 12, 7, 'nota_20250620_160341_ewq.pdf', '2025-06-20 09:03:41', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:03:41', '2025-06-20 09:03:41'),
(13, 13, 7, 'nota_20250620_160442_ewq.pdf', '2025-06-20 09:04:42', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:04:42', '2025-06-20 09:04:42'),
(14, 14, 7, 'nota_20250620_160616_ewq.pdf', '2025-06-20 09:06:16', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:06:16', '2025-06-20 09:06:16'),
(15, 15, 7, 'nota_20250620_160644_ewq.pdf', '2025-06-20 09:06:45', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:06:45', '2025-06-20 09:06:45'),
(16, 16, 7, 'nota_20250620_160831_ewq.pdf', '2025-06-20 09:08:31', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:08:31', '2025-06-20 09:08:31'),
(17, 17, 7, 'nota_20250620_160842_ewq.pdf', '2025-06-20 09:08:42', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:08:42', '2025-06-20 09:08:42'),
(18, 18, 8, 'nota_20250620_160930_haris.pdf', '2025-06-20 09:09:30', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:09:30', '2025-06-20 09:09:30'),
(19, 19, 8, 'nota_20250620_160955_haris.pdf', '2025-06-20 09:09:55', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:09:55', '2025-06-20 09:09:55'),
(20, 20, 8, 'nota_20250620_161007_haris.pdf', '2025-06-20 09:10:07', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:10:07', '2025-06-20 09:10:07'),
(21, 21, 8, 'nota_20250620_161021_haris.pdf', '2025-06-20 09:10:21', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:10:21', '2025-06-20 09:10:21'),
(22, 22, 8, 'nota_20250620_161049_haris.pdf', '2025-06-20 09:10:49', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:10:49', '2025-06-20 09:10:49'),
(23, 23, 8, 'nota_20250620_161112_haris.pdf', '2025-06-20 09:11:13', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:11:13', '2025-06-20 09:11:13'),
(24, 24, 9, 'nota_20250621_085659_haris.pdf', '2025-06-21 01:57:04', 0, 'Superadmin', 'Superadmin', '2025-06-21 01:57:04', '2025-06-21 01:57:04'),
(25, 25, 9, 'nota_20250621_085704_haris.pdf', '2025-06-21 01:57:05', 0, 'Superadmin', 'Superadmin', '2025-06-21 01:57:05', '2025-06-21 01:57:05'),
(26, 26, 9, 'nota_20250621_085709_haris.pdf', '2025-06-21 01:57:09', 0, 'Superadmin', 'Superadmin', '2025-06-21 01:57:09', '2025-06-21 01:57:09'),
(27, 27, 1, 'nota_20250622_161809_haris.pdf', '2025-06-22 09:18:11', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:18:11', '2025-06-22 09:18:11'),
(28, 28, 10, 'nota_20250622_161957_haris.pdf', '2025-06-22 09:19:57', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:19:57', '2025-06-22 09:19:57'),
(29, 29, 11, 'nota_20250622_162857_haris.pdf', '2025-06-22 09:28:58', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:28:58', '2025-06-22 09:28:58'),
(30, 31, 12, 'nota_20250622_163611_haris.pdf', '2025-06-22 09:36:11', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:36:11', '2025-06-22 09:36:11'),
(31, 32, 13, 'nota_20250622_163902_haris.pdf', '2025-06-22 09:39:02', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:39:02', '2025-06-22 09:39:02'),
(32, 33, 13, 'nota_20250622_163909_haris.pdf', '2025-06-22 09:39:09', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:39:09', '2025-06-22 09:39:09'),
(33, 34, 13, 'nota_20250622_163915_haris.pdf', '2025-06-22 09:39:15', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:39:15', '2025-06-22 09:39:15'),
(34, 35, 13, 'nota_20250622_164016_haris.pdf', '2025-06-22 09:40:17', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:40:17', '2025-06-22 09:40:17'),
(35, 40, 18, 'nota_20250622_170049_hha.pdf', '2025-06-22 10:00:49', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:00:49', '2025-06-22 10:00:49'),
(36, 41, 18, 'nota_20250622_170212_hha.pdf', '2025-06-22 10:02:12', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:02:12', '2025-06-22 10:02:12'),
(37, 42, 18, 'nota_20250622_170308_hha.pdf', '2025-06-22 10:03:08', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:03:08', '2025-06-22 10:03:08'),
(38, 43, 18, 'nota_20250622_170734_hha.pdf', '2025-06-22 10:07:34', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:07:34', '2025-06-22 10:07:34'),
(39, 44, 18, 'nota_20250622_170917_hha.pdf', '2025-06-22 10:09:18', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:09:18', '2025-06-22 10:09:18'),
(40, 45, 18, 'nota_20250622_171031_hha.pdf', '2025-06-22 10:10:32', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:10:32', '2025-06-22 10:10:32'),
(41, 46, 18, 'nota_20250622_171045_hha.pdf', '2025-06-22 10:10:45', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:10:45', '2025-06-22 10:10:45'),
(42, 47, 18, 'nota_20250622_171144_hha.pdf', '2025-06-22 10:11:44', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:11:44', '2025-06-22 10:11:44'),
(43, 48, 18, 'nota_20250622_171215_hha.pdf', '2025-06-22 10:12:16', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:12:16', '2025-06-22 10:12:16'),
(44, 49, 18, 'nota_20250622_171242_hha.pdf', '2025-06-22 10:12:42', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:12:42', '2025-06-22 10:12:42'),
(45, 50, 18, 'nota_20250622_171334_hha.pdf', '2025-06-22 10:13:34', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:13:34', '2025-06-22 10:13:34'),
(46, 51, 18, 'nota_20250622_171559_hha.pdf', '2025-06-22 10:16:00', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:16:00', '2025-06-22 10:16:00'),
(47, 52, 18, 'nota_20250622_171712_hha.pdf', '2025-06-22 10:17:12', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:17:12', '2025-06-22 10:17:12'),
(48, 53, 18, 'nota_20250622_171723_hha.pdf', '2025-06-22 10:17:23', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:17:23', '2025-06-22 10:17:23'),
(49, 54, 18, 'nota_20250622_171735_hha.pdf', '2025-06-22 10:17:35', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:17:35', '2025-06-22 10:17:35'),
(50, 55, 18, 'nota_20250622_171813_hha.pdf', '2025-06-22 10:18:13', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:18:13', '2025-06-22 10:18:13'),
(51, 56, 18, 'nota_20250622_171932_hha.pdf', '2025-06-22 10:19:32', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:19:32', '2025-06-22 10:19:32'),
(52, 57, 18, 'nota_20250622_172011_hha.pdf', '2025-06-22 10:20:11', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:20:11', '2025-06-22 10:20:11'),
(53, 58, 19, 'nota_20250622_172827_hari.pdf', '2025-06-22 10:28:57', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:28:57', '2025-06-22 10:28:57'),
(54, 59, 19, 'nota_20250622_172858_hari.pdf', '2025-06-22 10:29:26', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:29:26', '2025-06-22 10:29:26'),
(55, 60, 20, 'nota_20250622_173052_ha.pdf', '2025-06-22 10:31:18', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:31:18', '2025-06-22 10:31:18'),
(56, 61, 21, 'nota_20250622_173303_haris.pdf', '2025-06-22 10:33:29', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:33:29', '2025-06-22 10:33:29'),
(57, 62, 22, 'nota_20250622_173701_hendi.pdf', '2025-06-22 10:37:29', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:37:29', '2025-06-22 10:37:29'),
(58, 63, 22, 'nota_20250622_173729_hendi.pdf', '2025-06-22 10:38:01', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:38:01', '2025-06-22 10:38:01'),
(59, 64, 9, 'nota_20250622_175018_haris.pdf', '2025-06-22 10:50:19', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:50:19', '2025-06-22 10:50:19'),
(60, 65, 9, 'nota_20250622_175127_haris.pdf', '2025-06-22 10:51:27', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:51:27', '2025-06-22 10:51:27'),
(61, 66, 9, 'nota_20250622_175406_haris.pdf', '2025-06-22 10:54:06', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:54:06', '2025-06-22 10:54:06'),
(62, 67, 9, 'nota_20250622_175542_haris.pdf', '2025-06-22 10:55:43', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:55:43', '2025-06-22 10:55:43'),
(63, 68, 9, 'nota_20250622_175705_haris.pdf', '2025-06-22 10:57:05', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:57:05', '2025-06-22 10:57:05'),
(64, 69, 9, 'nota_20250622_175749_haris.pdf', '2025-06-22 10:57:50', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:57:50', '2025-06-22 10:57:50'),
(65, 70, 9, 'nota_20250622_175905_haris.pdf', '2025-06-22 10:59:06', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:59:06', '2025-06-22 10:59:06'),
(66, 71, 9, 'nota_20250622_180025_haris.pdf', '2025-06-22 11:00:25', 0, 'Superadmin', 'Superadmin', '2025-06-22 11:00:25', '2025-06-22 11:00:25'),
(67, 72, 9, 'nota_20250622_180048_haris.pdf', '2025-06-22 11:00:48', 0, 'Superadmin', 'Superadmin', '2025-06-22 11:00:48', '2025-06-22 11:00:48');

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
(1, 'haris', '123', '-', 'baru', 'dsa', '3600000.00', '3600000.00', '0.00', '0.00', '1800000.00', 'tunai', NULL, 1, '2025-06-20 08:49:18', 0, 'Superadmin', 'Superadmin', '2025-06-20 08:49:18', '2025-06-20 08:49:18'),
(2, 'JOKO', '123', '-', 'baru', 'DS', '3025000.00', '3025000.00', '0.00', '0.00', '1512500.00', 'tunai', NULL, 1, '2025-06-20 08:53:22', 0, 'Superadmin', 'Superadmin', '2025-06-20 08:53:22', '2025-06-20 08:53:22'),
(3, 'dsa', '111', '-', 'baru', '12', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 08:56:35', 0, 'Superadmin', 'Superadmin', '2025-06-20 08:56:35', '2025-06-20 08:56:35'),
(4, 'dsasda', '2121', '-', 'baru', 'sdad', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 08:57:15', 0, 'Superadmin', 'Superadmin', '2025-06-20 08:57:15', '2025-06-20 08:57:15'),
(5, 'dsa', '222', '-', 'baru', 'sss', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 08:59:48', 0, 'Superadmin', 'Superadmin', '2025-06-20 08:59:48', '2025-06-20 08:59:48'),
(6, 'dsad', '231', '-', 'baru', 'dsa', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:00:46', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:00:46', '2025-06-20 09:00:46'),
(7, 'dsad', '231', '-', 'baru', 'dsa', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:01:10', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:01:10', '2025-06-20 09:01:10'),
(8, 'ewq', '231', '-', 'baru', 'sdad', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:01:47', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:01:47', '2025-06-20 09:01:47'),
(9, 'ewq', '231', '-', 'baru', 'sdad', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:02:07', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:02:07', '2025-06-20 09:02:07'),
(10, 'ewq', '231', '-', 'baru', 'sdad', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:02:42', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:02:42', '2025-06-20 09:02:42'),
(11, 'ewq', '231', '-', 'baru', 'sdad', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:02:54', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:02:54', '2025-06-20 09:02:54'),
(12, 'ewq', '231', '-', 'baru', 'sdad', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:03:41', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:03:41', '2025-06-20 09:03:41'),
(13, 'ewq', '231', '-', 'baru', 'sdad', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:04:42', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:04:42', '2025-06-20 09:04:42'),
(14, 'ewq', '231', '-', 'baru', 'sdad', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:06:15', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:06:15', '2025-06-20 09:06:15'),
(15, 'ewq', '231', '-', 'baru', 'sdad', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:06:44', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:06:44', '2025-06-20 09:06:44'),
(16, 'ewq', '231', '-', 'baru', 'sdad', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:08:31', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:08:31', '2025-06-20 09:08:31'),
(17, 'ewq', '231', '-', 'baru', 'sdad', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:08:42', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:08:42', '2025-06-20 09:08:42'),
(18, 'haris', '8989', '-', 'baru', 'dsa', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:09:29', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:09:29', '2025-06-20 09:09:29'),
(19, 'haris', '8989', '-', 'baru', 'dsa', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:09:54', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:09:54', '2025-06-20 09:09:54'),
(20, 'haris', '8989', '-', 'baru', 'dsa', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:10:07', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:10:07', '2025-06-20 09:10:07'),
(21, 'haris', '8989', '-', 'baru', 'dsa', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:10:21', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:10:21', '2025-06-20 09:10:21'),
(22, 'haris', '8989', '-', 'baru', 'dsa', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:10:49', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:10:49', '2025-06-20 09:10:49'),
(23, 'haris', '8989', '-', 'baru', 'dsa', '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-20 09:11:12', 0, 'Superadmin', 'Superadmin', '2025-06-20 09:11:12', '2025-06-20 09:11:12'),
(24, 'haris', '676868', '-', 'baru', 'test', '3595000.00', '3595000.00', '0.00', '0.00', '1797500.00', 'tunai', NULL, 1, '2025-06-21 01:56:59', 0, 'Superadmin', 'Superadmin', '2025-06-21 01:56:59', '2025-06-21 01:56:59'),
(25, 'haris', '676868', '-', 'baru', 'test', '3595000.00', '3595000.00', '0.00', '0.00', '1797500.00', 'tunai', NULL, 1, '2025-06-21 01:57:04', 0, 'Superadmin', 'Superadmin', '2025-06-21 01:57:04', '2025-06-21 01:57:04'),
(26, 'haris', '676868', '-', 'baru', 'test', '3595000.00', '3595000.00', '0.00', '0.00', '1797500.00', 'tunai', NULL, 1, '2025-06-21 01:57:09', 0, 'Superadmin', 'Superadmin', '2025-06-21 01:57:09', '2025-06-21 01:57:09'),
(27, 'haris', '123', '-', 'baru', 'dsa', '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 09:18:09', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:18:09', '2025-06-22 09:18:09'),
(28, 'haris', '789797', '-', 'baru', 'test', '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 09:19:57', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:19:57', '2025-06-22 09:19:57'),
(29, 'haris', '7898', '-', 'baru', 'test', '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 09:28:57', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:28:57', '2025-06-22 09:28:57'),
(30, 'haris', '890', '-', 'baru', 'test', '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 09:33:09', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:33:09', '2025-06-22 09:33:09'),
(31, 'haris', '890', '-', 'baru', 'test', '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 09:36:11', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:36:11', '2025-06-22 09:36:11'),
(32, 'haris', '687686', '-', 'baru', 'test', '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 09:39:02', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:39:02', '2025-06-22 09:39:02'),
(33, 'haris', '687686', '-', 'baru', 'test', '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 09:39:09', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:39:09', '2025-06-22 09:39:09'),
(34, 'haris', '687686', '-', 'baru', 'test', '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 09:39:15', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:39:15', '2025-06-22 09:39:15'),
(35, 'haris', '687686', '-', 'baru', 'test', '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 09:40:16', 0, 'Superadmin', 'Superadmin', '2025-06-22 09:40:16', '2025-06-22 09:40:16'),
(36, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 4, '2025-06-22 10:00:49', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:00:49', '2025-06-22 10:00:49'),
(37, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 4, '2025-06-22 10:02:12', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:02:12', '2025-06-22 10:02:12'),
(38, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 4, '2025-06-22 10:03:08', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:03:08', '2025-06-22 10:03:08'),
(39, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 4, '2025-06-22 10:07:34', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:07:34', '2025-06-22 10:07:34'),
(40, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 4, '2025-06-22 10:09:17', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:09:17', '2025-06-22 10:09:17'),
(41, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 4, '2025-06-22 10:10:31', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:10:31', '2025-06-22 10:10:31'),
(42, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 4, '2025-06-22 10:10:45', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:10:45', '2025-06-22 10:10:45'),
(43, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 4, '2025-06-22 10:11:44', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:11:44', '2025-06-22 10:11:44'),
(44, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 4, '2025-06-22 10:12:15', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:12:15', '2025-06-22 10:12:15'),
(45, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 4, '2025-06-22 10:12:41', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:12:41', '2025-06-22 10:12:41'),
(46, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 4, '2025-06-22 10:13:34', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:13:34', '2025-06-22 10:13:34'),
(47, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 4, '2025-06-22 10:15:59', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:15:59', '2025-06-22 10:15:59'),
(48, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 4, '2025-06-22 10:17:11', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:17:11', '2025-06-22 10:17:11'),
(49, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'transfer_bank', NULL, 4, '2025-06-22 10:17:23', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:17:23', '2025-06-22 10:17:23'),
(50, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'qris', NULL, 4, '2025-06-22 10:17:35', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:17:35', '2025-06-22 10:17:35'),
(51, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'qris', NULL, 4, '2025-06-22 10:18:13', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:18:13', '2025-06-22 10:18:13'),
(52, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'qris', NULL, 4, '2025-06-22 10:19:31', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:19:31', '2025-06-22 10:19:31'),
(53, 'hha', '6868687', '-', 'baru', 'test', '207000.00', '207000.00', '0.00', '0.00', '0.00', 'qris', NULL, 4, '2025-06-22 10:20:11', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:20:11', '2025-06-22 10:20:11'),
(54, 'hari', '7979', '-', 'baru', 'yest', '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 10:28:27', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:28:27', '2025-06-22 10:28:27'),
(55, 'hari', '7979', '-', 'baru', 'yest', '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 10:28:58', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:28:58', '2025-06-22 10:28:58'),
(56, 'ha', '67868', '-', 'baru', 'test', '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 10:30:52', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:30:52', '2025-06-22 10:30:52'),
(57, 'haris', '7979879', '-', 'baru', 'test', '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 10:33:03', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:33:03', '2025-06-22 10:33:03'),
(58, 'hendi', '123132', '-', 'baru', 'ress', '150000.00', '150000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 10:37:01', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:37:01', '2025-06-22 10:37:01'),
(59, 'hendi', '123132', '-', 'baru', 'ress', '150000.00', '150000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 10:37:29', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:37:29', '2025-06-22 10:37:29'),
(60, 'haris', '676868', '-', 'baru', 'test', '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 10:50:18', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:50:18', '2025-06-22 10:50:18'),
(61, 'haris', '676868', '-', 'baru', 'test', '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 10:51:27', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:51:27', '2025-06-22 10:51:27'),
(62, 'haris', '676868', '-', 'baru', 'test', '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 10:54:06', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:54:06', '2025-06-22 10:54:06'),
(63, 'haris', '676868', '-', 'baru', 'test', '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 10:55:42', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:55:42', '2025-06-22 10:55:42'),
(64, 'haris', '676868', '-', 'baru', 'test', '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 10:57:05', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:57:05', '2025-06-22 10:57:05'),
(65, 'haris', '676868', '-', 'baru', 'test', '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 10:57:49', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:57:49', '2025-06-22 10:57:49'),
(66, 'haris', '676868', '-', 'baru', 'test', '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 10:59:05', 0, 'Superadmin', 'Superadmin', '2025-06-22 10:59:05', '2025-06-22 10:59:05'),
(67, 'haris', '676868', '-', 'baru', 'test', '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 11:00:25', 0, 'Superadmin', 'Superadmin', '2025-06-22 11:00:25', '2025-06-22 11:00:25'),
(68, 'haris', '676868', '-', 'baru', 'test', '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, 1, '2025-06-22 11:00:48', 0, 'Superadmin', 'Superadmin', '2025-06-22 11:00:48', '2025-06-22 11:00:48');

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
  `tipe_produk` enum('per_meter','tiered','flat','custom') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produks`
--

INSERT INTO `produks` (`id`, `nama_produk`, `tipe_produk`, `status`, `created_at`, `updated_at`) VALUES
(1, 'MMT', 'per_meter', '1', '2025-06-20 08:48:41', '2025-06-20 08:48:41'),
(2, 'BANNER 3X1', 'flat', '1', '2025-06-22 09:46:31', '2025-06-22 09:46:31'),
(3, 'cettak a3', 'tiered', '1', '2025-06-22 09:46:57', '2025-06-22 09:46:57'),
(4, 'Bolak Balik Laminasi', 'custom', '1', '2025-06-22 09:47:28', '2025-06-22 09:47:28');

-- --------------------------------------------------------

--
-- Table structure for table `produk_bahans`
--

CREATE TABLE `produk_bahans` (
  `id` bigint UNSIGNED NOT NULL,
  `produk_id` bigint UNSIGNED NOT NULL,
  `nama_bahan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_per_meter` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `diskon` int DEFAULT NULL,
  `total_harga` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'Owner', 'Owner', 0, NULL, NULL);

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
('A9ZTubO9yLRdf5HYATq6fV5VxdKZxXcdz06hp4Re', 1, '127.0.0.1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Mobile Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiT2lXMFZsWnE2WHNjWXBIOHJoeDdHU3E2UHhpZlI4TElVVWZ3RnJhdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC90cmFuc2Frc2kiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NDoiYXV0aCI7YToxOntzOjIxOiJwYXNzd29yZF9jb25maXJtZWRfYXQiO2k6MTc1MDYwODI2MDt9fQ==', 1750615268);

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
  `nota_file_dua` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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

INSERT INTO `transactions` (`id`, `customer_id`, `subtotal`, `total`, `biaya_desain`, `diskon`, `dp`, `metode_pembayaran`, `bukti_pembayaran`, `tanggal_ambil`, `tanggal_transaksi`, `nota_file`, `nota_file_dua`, `nomor_faktur`, `status_pembayaran`, `diambil_oleh`, `bukti_pengambilan`, `tanggal_selesai`, `deleteSts`, `createdBy`, `updatedBy`, `created_at`, `updated_at`) VALUES
(1, 1, '3600000.00', '3600000.00', '0.00', '0.00', '1800000.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 08:49:18', 'nota_20250620_154918_haris.pdf', NULL, 'GD-MMT-01-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 08:49:18', '2025-06-20 08:49:23'),
(2, 2, '3025000.00', '3025000.00', '0.00', '0.00', '1512500.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 08:53:22', 'nota_20250620_155322_joko.pdf', NULL, 'GD-MMT-02-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 08:53:22', '2025-06-20 08:53:27'),
(3, 3, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 08:56:35', 'nota_20250620_155635_dsa.pdf', NULL, 'GD-MMT-03-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 08:56:35', '2025-06-20 08:56:35'),
(4, 4, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 08:57:15', 'nota_20250620_155715_dsasda.pdf', NULL, 'GD-MMT-04-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 08:57:15', '2025-06-20 08:57:16'),
(5, 5, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 08:59:48', 'nota_20250620_155948_dsa.pdf', NULL, 'GD-MMT-05-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 08:59:48', '2025-06-20 08:59:48'),
(6, 6, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:00:46', 'nota_20250620_160046_dsad.pdf', NULL, 'GD-MMT-06-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:00:46', '2025-06-20 09:00:47'),
(7, 6, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:01:10', 'nota_20250620_160110_dsad.pdf', NULL, 'GD-MMT-07-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:01:10', '2025-06-20 09:01:11'),
(8, 7, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:01:47', 'nota_20250620_160147_ewq.pdf', NULL, 'GD-MMT-08-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:01:47', '2025-06-20 09:01:47'),
(9, 7, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:02:07', 'nota_20250620_160207_ewq.pdf', NULL, 'GD-MMT-09-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:02:07', '2025-06-20 09:02:08'),
(10, 7, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:02:42', 'nota_20250620_160242_ewq.pdf', NULL, 'GD-MMT-10-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:02:42', '2025-06-20 09:02:43'),
(11, 7, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:02:54', 'nota_20250620_160254_ewq.pdf', NULL, 'GD-MMT-11-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:02:54', '2025-06-20 09:02:54'),
(12, 7, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:03:41', 'nota_20250620_160341_ewq.pdf', NULL, 'GD-MMT-12-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:03:41', '2025-06-20 09:03:41'),
(13, 7, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:04:42', 'nota_20250620_160442_ewq.pdf', NULL, 'GD-MMT-13-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:04:42', '2025-06-20 09:04:42'),
(14, 7, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:06:15', 'nota_20250620_160616_ewq.pdf', NULL, 'GD-MMT-14-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:06:15', '2025-06-20 09:06:16'),
(15, 7, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:06:44', 'nota_20250620_160644_ewq.pdf', NULL, 'GD-MMT-15-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:06:44', '2025-06-20 09:06:45'),
(16, 7, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:08:31', 'nota_20250620_160831_ewq.pdf', NULL, 'GD-MMT-16-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:08:31', '2025-06-20 09:08:31'),
(17, 7, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:08:42', 'nota_20250620_160842_ewq.pdf', NULL, 'GD-MMT-17-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:08:42', '2025-06-20 09:08:42'),
(18, 8, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:09:29', 'nota_20250620_160930_haris.pdf', NULL, 'GD-MMT-18-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:09:29', '2025-06-20 09:09:30'),
(19, 8, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:09:54', 'nota_20250620_160955_haris.pdf', NULL, 'GD-MMT-19-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:09:54', '2025-06-20 09:09:55'),
(20, 8, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:10:07', 'nota_20250620_161007_haris.pdf', NULL, 'GD-MMT-20-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:10:07', '2025-06-20 09:10:07'),
(21, 8, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:10:21', 'nota_20250620_161021_haris.pdf', NULL, 'GD-MMT-21-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:10:21', '2025-06-20 09:10:21'),
(22, 8, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:10:49', 'nota_20250620_161049_haris.pdf', NULL, 'GD-MMT-22-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:10:49', '2025-06-20 09:10:49'),
(23, 8, '25000.00', '25000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-19 17:00:00', '2025-06-20 09:11:12', 'nota_20250620_161112_haris.pdf', NULL, 'GD-MMT-23-20250620', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-20 09:11:12', '2025-06-20 09:11:13'),
(24, 9, '3595000.00', '3595000.00', '0.00', '0.00', '1797500.00', 'tunai', NULL, '2025-06-20 17:00:00', '2025-06-21 01:56:59', 'nota_20250621_085659_haris.pdf', NULL, 'GD-MMT-01-20250621', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-21 01:56:59', '2025-06-21 01:57:04'),
(25, 9, '3595000.00', '3595000.00', '0.00', '0.00', '1797500.00', 'tunai', NULL, '2025-06-20 17:00:00', '2025-06-21 01:57:04', 'nota_20250621_085704_haris.pdf', NULL, 'GD-MMT-02-20250621', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-21 01:57:04', '2025-06-21 01:57:05'),
(26, 9, '3595000.00', '3595000.00', '0.00', '0.00', '1797500.00', 'tunai', NULL, '2025-06-20 17:00:00', '2025-06-21 01:57:09', 'nota_20250621_085709_haris.pdf', NULL, 'GD-MMT-03-20250621', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-21 01:57:09', '2025-06-21 01:57:09'),
(27, 1, '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 09:18:09', 'nota_20250622_161809_haris.pdf', NULL, 'GD-MMT-01-20250622', 'dp', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 09:18:09', '2025-06-22 09:18:11'),
(28, 10, '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 09:19:57', 'nota_20250622_161957_haris.pdf', NULL, 'GD-MMT-02-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 09:19:57', '2025-06-22 09:19:57'),
(29, 11, '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 09:28:57', 'nota_20250622_162857_haris.pdf', NULL, 'GD-MMT-03-20250622', 'dp', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 09:28:57', '2025-06-22 09:28:58'),
(30, 12, '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 09:33:09', NULL, NULL, 'GD-MMT-04-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 09:33:09', '2025-06-22 09:33:09'),
(31, 12, '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 09:36:11', 'nota_20250622_163611_haris.pdf', NULL, 'GD-MMT-05-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 09:36:11', '2025-06-22 09:36:11'),
(32, 13, '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 09:39:02', 'nota_20250622_163902_haris.pdf', NULL, 'GD-MMT-06-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 09:39:02', '2025-06-22 09:39:02'),
(33, 13, '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 09:39:09', 'nota_20250622_163909_haris.pdf', NULL, 'GD-MMT-07-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 09:39:09', '2025-06-22 09:39:09'),
(34, 13, '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 09:39:15', 'nota_20250622_163915_haris.pdf', NULL, 'GD-MMT-08-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 09:39:15', '2025-06-22 09:39:15'),
(35, 13, '3595000.00', '3595000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 09:40:16', 'nota_20250622_164016_haris.pdf', NULL, 'GD-MMT-09-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 09:40:16', '2025-06-22 09:40:17'),
(40, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:00:49', 'nota_20250622_170049_hha.pdf', NULL, 'GD-MMT-10-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:00:49', '2025-06-22 10:00:49'),
(41, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:02:12', 'nota_20250622_170212_hha.pdf', NULL, 'GD-MMT-11-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:02:12', '2025-06-22 10:02:12'),
(42, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:03:08', 'nota_20250622_170308_hha.pdf', NULL, 'GD-MMT-12-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:03:08', '2025-06-22 10:03:08'),
(43, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:07:34', 'nota_20250622_170734_hha.pdf', NULL, 'GD-MMT-13-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:07:34', '2025-06-22 10:07:34'),
(44, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:09:17', 'nota_20250622_170917_hha.pdf', NULL, 'GD-MMT-14-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:09:17', '2025-06-22 10:09:18'),
(45, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:10:31', 'nota_20250622_171031_hha.pdf', NULL, 'GD-MMT-15-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:10:31', '2025-06-22 10:10:32'),
(46, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:10:45', 'nota_20250622_171045_hha.pdf', NULL, 'GD-MMT-16-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:10:45', '2025-06-22 10:10:45'),
(47, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:11:44', 'nota_20250622_171144_hha.pdf', NULL, 'GD-MMT-17-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:11:44', '2025-06-22 10:11:44'),
(48, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:12:15', 'nota_20250622_171215_hha.pdf', NULL, 'GD-MMT-18-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:12:15', '2025-06-22 10:12:16'),
(49, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:12:41', 'nota_20250622_171242_hha.pdf', NULL, 'GD-MMT-19-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:12:41', '2025-06-22 10:12:42'),
(50, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:13:34', 'nota_20250622_171334_hha.pdf', NULL, 'GD-MMT-20-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:13:34', '2025-06-22 10:13:34'),
(51, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:15:59', 'nota_20250622_171559_hha.pdf', NULL, 'GD-MMT-21-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:15:59', '2025-06-22 10:16:00'),
(52, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:17:11', 'nota_20250622_171712_hha.pdf', NULL, 'GD-MMT-22-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:17:11', '2025-06-22 10:17:12'),
(53, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'transfer_bank', NULL, '2025-06-21 17:00:00', '2025-06-22 10:17:23', 'nota_20250622_171723_hha.pdf', NULL, 'GD-MMT-23-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:17:23', '2025-06-22 10:17:23'),
(54, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'qris', NULL, '2025-06-21 17:00:00', '2025-06-22 10:17:35', 'nota_20250622_171735_hha.pdf', NULL, 'GD-MMT-24-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:17:35', '2025-06-22 10:17:35'),
(55, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'qris', NULL, '2025-06-21 17:00:00', '2025-06-22 10:18:13', 'nota_20250622_171813_hha.pdf', NULL, 'GD-MMT-25-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:18:13', '2025-06-22 10:18:13'),
(56, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'qris', NULL, '2025-06-21 17:00:00', '2025-06-22 10:19:31', 'nota_20250622_171932_hha.pdf', NULL, 'GD-MMT-26-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:19:31', '2025-06-22 10:19:32'),
(57, 18, '207000.00', '207000.00', '0.00', '0.00', '0.00', 'qris', NULL, '2025-06-21 17:00:00', '2025-06-22 10:20:11', 'nota_20250622_172011_hha.pdf', NULL, 'GD-MMT-27-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:20:11', '2025-06-22 10:20:11'),
(58, 19, '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:28:27', 'nota_20250622_172827_hari.pdf', 'nota_dua_20250622_172828_hari.pdf', 'GD-MMT-28-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:28:27', '2025-06-22 10:28:57'),
(59, 19, '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:28:58', 'nota_20250622_172858_hari.pdf', 'nota_dua_20250622_172858_hari.pdf', 'GD-MMT-29-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:28:58', '2025-06-22 10:29:26'),
(60, 20, '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:30:52', 'nota_20250622_173052_ha.pdf', 'nota_dua_20250622_173052_ha.pdf', 'GD-MMT-30-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:30:52', '2025-06-22 10:31:18'),
(61, 21, '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:33:03', 'nota_20250622_173303_haris.pdf', 'nota_dua_20250622_173303_haris.pdf', 'GD-MMT-31-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:33:03', '2025-06-22 10:33:29'),
(62, 22, '150000.00', '150000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:37:01', 'nota_20250622_173701_hendi.pdf', 'nota_dua_20250622_173701_hendi.pdf', 'GD-MMT-32-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:37:01', '2025-06-22 10:37:29'),
(63, 22, '150000.00', '150000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:37:29', 'nota_20250622_173729_hendi.pdf', 'nota_dua_20250622_173730_hendi.pdf', 'GD-MMT-33-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:37:29', '2025-06-22 10:38:01'),
(64, 9, '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:50:18', 'nota_20250622_175018_haris.pdf', 'nota_dua_20250622_175018_haris.pdf', 'GD-MMT-34-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:50:18', '2025-06-22 10:50:19'),
(65, 9, '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:51:27', 'nota_20250622_175127_haris.pdf', 'nota_dua_20250622_175127_haris.pdf', 'GD-MMT-35-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:51:27', '2025-06-22 10:51:27'),
(66, 9, '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:54:06', 'nota_20250622_175406_haris.pdf', 'nota_dua_20250622_175406_haris.pdf', 'GD-MMT-36-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:54:06', '2025-06-22 10:54:06'),
(67, 9, '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:55:42', 'nota_20250622_175542_haris.pdf', 'nota_dua_20250622_175542_haris.pdf', 'GD-MMT-37-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:55:42', '2025-06-22 10:55:43'),
(68, 9, '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:57:05', 'nota_20250622_175705_haris.pdf', 'nota_dua_20250622_175705_haris.pdf', 'GD-MMT-38-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:57:05', '2025-06-22 10:57:05'),
(69, 9, '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:57:49', 'nota_20250622_175749_haris.pdf', 'nota_dua_20250622_175750_haris.pdf', 'GD-MMT-39-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:57:49', '2025-06-22 10:57:50'),
(70, 9, '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 10:59:05', 'nota_20250622_175905_haris.pdf', 'nota_dua_20250622_175906_haris.pdf', 'GD-MMT-40-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 10:59:05', '2025-06-22 10:59:06'),
(71, 9, '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 11:00:25', 'nota_20250622_180025_haris.pdf', 'nota_dua_20250622_180025_haris.pdf', 'GD-MMT-41-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 11:00:25', '2025-06-22 11:00:25'),
(72, 9, '20000.00', '20000.00', '0.00', '0.00', '0.00', 'tunai', NULL, '2025-06-21 17:00:00', '2025-06-22 11:00:48', 'nota_20250622_180048_haris.pdf', 'nota_dua_20250622_180048_haris.pdf', 'GD-MMT-42-20250622', 'lunas', NULL, NULL, NULL, 0, 'Superadmin', NULL, '2025-06-22 11:00:48', '2025-06-22 11:00:48');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id` bigint UNSIGNED NOT NULL,
  `transaction_id` bigint UNSIGNED NOT NULL,
  `tipe_produk_id` bigint UNSIGNED DEFAULT NULL,
  `panjang` decimal(8,2) DEFAULT '0.00',
  `lebar` decimal(8,2) DEFAULT '0.00',
  `harga_per_meter` decimal(15,2) DEFAULT '0.00',
  `diskon_barang` int DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `sisi` int DEFAULT NULL,
  `laminasi` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `total_harga` int DEFAULT NULL,
  `createdBy` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updatedBy` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleteSts` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`id`, `transaction_id`, `tipe_produk_id`, `panjang`, `lebar`, `harga_per_meter`, `diskon_barang`, `qty`, `sisi`, `laminasi`, `keterangan`, `total_harga`, `createdBy`, `updatedBy`, `deleteSts`, `created_at`, `updated_at`) VALUES
(39, 42, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, 'dsa', 20000, 'Superadmin', NULL, 0, '2025-06-22 10:03:08', '2025-06-22 10:03:08'),
(40, 42, 2, NULL, NULL, '25000.00', 0, 1, NULL, NULL, NULL, 25000, 'Superadmin', NULL, 0, '2025-06-22 10:03:08', '2025-06-22 10:03:08'),
(41, 42, 3, NULL, NULL, '12000.00', 0, 1, NULL, NULL, NULL, 12000, 'Superadmin', NULL, 0, '2025-06-22 10:03:08', '2025-06-22 10:03:08'),
(42, 42, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:03:08', '2025-06-22 10:03:08'),
(43, 43, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, 'dsa', 20000, 'Superadmin', NULL, 0, '2025-06-22 10:07:34', '2025-06-22 10:07:34'),
(44, 43, 2, NULL, NULL, '25000.00', 0, 1, NULL, NULL, NULL, 25000, 'Superadmin', NULL, 0, '2025-06-22 10:07:34', '2025-06-22 10:07:34'),
(45, 43, 3, NULL, NULL, '12000.00', 0, 1, NULL, NULL, NULL, 12000, 'Superadmin', NULL, 0, '2025-06-22 10:07:34', '2025-06-22 10:07:34'),
(46, 43, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:07:34', '2025-06-22 10:07:34'),
(47, 44, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, 'dsa', 20000, 'Superadmin', NULL, 0, '2025-06-22 10:09:17', '2025-06-22 10:09:17'),
(48, 44, 2, NULL, NULL, '25000.00', 0, 1, NULL, NULL, NULL, 25000, 'Superadmin', NULL, 0, '2025-06-22 10:09:17', '2025-06-22 10:09:17'),
(49, 44, 3, NULL, NULL, '12000.00', 0, 1, NULL, NULL, NULL, 12000, 'Superadmin', NULL, 0, '2025-06-22 10:09:17', '2025-06-22 10:09:17'),
(50, 44, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:09:17', '2025-06-22 10:09:17'),
(51, 45, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, 'dsa', 20000, 'Superadmin', NULL, 0, '2025-06-22 10:10:31', '2025-06-22 10:10:31'),
(52, 45, 2, NULL, NULL, '25000.00', 0, 1, NULL, NULL, NULL, 25000, 'Superadmin', NULL, 0, '2025-06-22 10:10:31', '2025-06-22 10:10:31'),
(53, 45, 3, NULL, NULL, '12000.00', 0, 1, NULL, NULL, NULL, 12000, 'Superadmin', NULL, 0, '2025-06-22 10:10:31', '2025-06-22 10:10:31'),
(54, 45, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:10:31', '2025-06-22 10:10:31'),
(55, 46, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, 'dsa', 20000, 'Superadmin', NULL, 0, '2025-06-22 10:10:45', '2025-06-22 10:10:45'),
(56, 46, 2, NULL, NULL, '25000.00', 0, 1, NULL, NULL, NULL, 25000, 'Superadmin', NULL, 0, '2025-06-22 10:10:45', '2025-06-22 10:10:45'),
(57, 46, 3, NULL, NULL, '12000.00', 0, 1, NULL, NULL, NULL, 12000, 'Superadmin', NULL, 0, '2025-06-22 10:10:45', '2025-06-22 10:10:45'),
(58, 46, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:10:45', '2025-06-22 10:10:45'),
(59, 47, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, 'dsa', 20000, 'Superadmin', NULL, 0, '2025-06-22 10:11:44', '2025-06-22 10:11:44'),
(60, 47, 2, NULL, NULL, '25000.00', 0, 1, NULL, NULL, NULL, 25000, 'Superadmin', NULL, 0, '2025-06-22 10:11:44', '2025-06-22 10:11:44'),
(61, 47, 3, NULL, NULL, '12000.00', 0, 1, NULL, NULL, NULL, 12000, 'Superadmin', NULL, 0, '2025-06-22 10:11:44', '2025-06-22 10:11:44'),
(62, 47, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:11:44', '2025-06-22 10:11:44'),
(63, 48, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, 'dsa', 20000, 'Superadmin', NULL, 0, '2025-06-22 10:12:15', '2025-06-22 10:12:15'),
(64, 48, 2, NULL, NULL, '25000.00', 0, 1, NULL, NULL, NULL, 25000, 'Superadmin', NULL, 0, '2025-06-22 10:12:15', '2025-06-22 10:12:15'),
(65, 48, 3, NULL, NULL, '12000.00', 0, 1, NULL, NULL, NULL, 12000, 'Superadmin', NULL, 0, '2025-06-22 10:12:15', '2025-06-22 10:12:15'),
(66, 48, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:12:15', '2025-06-22 10:12:15'),
(67, 49, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, 'dsa', 20000, 'Superadmin', NULL, 0, '2025-06-22 10:12:41', '2025-06-22 10:12:41'),
(68, 49, 2, NULL, NULL, '25000.00', 0, 1, NULL, NULL, NULL, 25000, 'Superadmin', NULL, 0, '2025-06-22 10:12:41', '2025-06-22 10:12:41'),
(69, 49, 3, NULL, NULL, '12000.00', 0, 1, NULL, NULL, NULL, 12000, 'Superadmin', NULL, 0, '2025-06-22 10:12:41', '2025-06-22 10:12:41'),
(70, 49, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:12:41', '2025-06-22 10:12:41'),
(71, 50, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, 'dsa', 20000, 'Superadmin', NULL, 0, '2025-06-22 10:13:34', '2025-06-22 10:13:34'),
(72, 50, 2, NULL, NULL, '25000.00', 0, 1, NULL, NULL, NULL, 25000, 'Superadmin', NULL, 0, '2025-06-22 10:13:34', '2025-06-22 10:13:34'),
(73, 50, 3, NULL, NULL, '12000.00', 0, 1, NULL, NULL, NULL, 12000, 'Superadmin', NULL, 0, '2025-06-22 10:13:34', '2025-06-22 10:13:34'),
(74, 50, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:13:34', '2025-06-22 10:13:34'),
(75, 51, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, 'dsa', 20000, 'Superadmin', NULL, 0, '2025-06-22 10:15:59', '2025-06-22 10:15:59'),
(76, 51, 2, NULL, NULL, '25000.00', 0, 1, NULL, NULL, NULL, 25000, 'Superadmin', NULL, 0, '2025-06-22 10:15:59', '2025-06-22 10:15:59'),
(77, 51, 3, NULL, NULL, '12000.00', 0, 1, NULL, NULL, NULL, 12000, 'Superadmin', NULL, 0, '2025-06-22 10:15:59', '2025-06-22 10:15:59'),
(78, 51, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:15:59', '2025-06-22 10:15:59'),
(79, 52, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, 'dsa', 20000, 'Superadmin', NULL, 0, '2025-06-22 10:17:11', '2025-06-22 10:17:11'),
(80, 52, 2, NULL, NULL, '25000.00', 0, 1, NULL, NULL, NULL, 25000, 'Superadmin', NULL, 0, '2025-06-22 10:17:11', '2025-06-22 10:17:11'),
(81, 52, 3, NULL, NULL, '12000.00', 0, 1, NULL, NULL, NULL, 12000, 'Superadmin', NULL, 0, '2025-06-22 10:17:11', '2025-06-22 10:17:11'),
(82, 52, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:17:11', '2025-06-22 10:17:11'),
(83, 53, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, 'dsa', 20000, 'Superadmin', NULL, 0, '2025-06-22 10:17:23', '2025-06-22 10:17:23'),
(84, 53, 2, NULL, NULL, '25000.00', 0, 1, NULL, NULL, NULL, 25000, 'Superadmin', NULL, 0, '2025-06-22 10:17:23', '2025-06-22 10:17:23'),
(85, 53, 3, NULL, NULL, '12000.00', 0, 1, NULL, NULL, NULL, 12000, 'Superadmin', NULL, 0, '2025-06-22 10:17:23', '2025-06-22 10:17:23'),
(86, 53, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:17:23', '2025-06-22 10:17:23'),
(87, 54, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, 'dsa', 20000, 'Superadmin', NULL, 0, '2025-06-22 10:17:35', '2025-06-22 10:17:35'),
(88, 54, 2, NULL, NULL, '25000.00', 0, 1, NULL, NULL, NULL, 25000, 'Superadmin', NULL, 0, '2025-06-22 10:17:35', '2025-06-22 10:17:35'),
(89, 54, 3, NULL, NULL, '12000.00', 0, 1, NULL, NULL, NULL, 12000, 'Superadmin', NULL, 0, '2025-06-22 10:17:35', '2025-06-22 10:17:35'),
(90, 54, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:17:35', '2025-06-22 10:17:35'),
(91, 55, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, 'dsa', 20000, 'Superadmin', NULL, 0, '2025-06-22 10:18:13', '2025-06-22 10:18:13'),
(92, 55, 2, NULL, NULL, '25000.00', 0, 1, NULL, NULL, NULL, 25000, 'Superadmin', NULL, 0, '2025-06-22 10:18:13', '2025-06-22 10:18:13'),
(93, 55, 3, NULL, NULL, '12000.00', 0, 1, NULL, NULL, NULL, 12000, 'Superadmin', NULL, 0, '2025-06-22 10:18:13', '2025-06-22 10:18:13'),
(94, 55, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:18:13', '2025-06-22 10:18:13'),
(95, 56, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, 'dsa', 20000, 'Superadmin', NULL, 0, '2025-06-22 10:19:31', '2025-06-22 10:19:31'),
(96, 56, 2, NULL, NULL, '25000.00', 0, 1, NULL, NULL, NULL, 25000, 'Superadmin', NULL, 0, '2025-06-22 10:19:31', '2025-06-22 10:19:31'),
(97, 56, 3, NULL, NULL, '12000.00', 0, 1, NULL, NULL, NULL, 12000, 'Superadmin', NULL, 0, '2025-06-22 10:19:31', '2025-06-22 10:19:31'),
(98, 56, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:19:31', '2025-06-22 10:19:31'),
(99, 57, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, 'dsa', 20000, 'Superadmin', NULL, 0, '2025-06-22 10:20:11', '2025-06-22 10:20:11'),
(100, 57, 2, NULL, NULL, '25000.00', 0, 1, NULL, NULL, NULL, 25000, 'Superadmin', NULL, 0, '2025-06-22 10:20:11', '2025-06-22 10:20:11'),
(101, 57, 3, NULL, NULL, '12000.00', 0, 1, NULL, NULL, NULL, 12000, 'Superadmin', NULL, 0, '2025-06-22 10:20:11', '2025-06-22 10:20:11'),
(102, 57, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:20:11', '2025-06-22 10:20:11'),
(103, 58, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, NULL, 20000, 'Superadmin', NULL, 0, '2025-06-22 10:28:27', '2025-06-22 10:28:27'),
(104, 59, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, NULL, 20000, 'Superadmin', NULL, 0, '2025-06-22 10:28:58', '2025-06-22 10:28:58'),
(105, 60, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, NULL, 20000, 'Superadmin', NULL, 0, '2025-06-22 10:30:52', '2025-06-22 10:30:52'),
(106, 61, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, NULL, 20000, 'Superadmin', NULL, 0, '2025-06-22 10:33:03', '2025-06-22 10:33:03'),
(107, 62, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:37:01', '2025-06-22 10:37:01'),
(108, 63, 4, NULL, NULL, '150000.00', 0, 1, 1, 'tidak', NULL, 150000, 'Superadmin', NULL, 0, '2025-06-22 10:37:29', '2025-06-22 10:37:29'),
(109, 64, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, NULL, 20000, 'Superadmin', NULL, 0, '2025-06-22 10:50:18', '2025-06-22 10:50:18'),
(110, 65, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, NULL, 20000, 'Superadmin', NULL, 0, '2025-06-22 10:51:27', '2025-06-22 10:51:27'),
(111, 66, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, NULL, 20000, 'Superadmin', NULL, 0, '2025-06-22 10:54:06', '2025-06-22 10:54:06'),
(112, 67, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, NULL, 20000, 'Superadmin', NULL, 0, '2025-06-22 10:55:42', '2025-06-22 10:55:42'),
(113, 68, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, NULL, 20000, 'Superadmin', NULL, 0, '2025-06-22 10:57:05', '2025-06-22 10:57:05'),
(114, 69, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, NULL, 20000, 'Superadmin', NULL, 0, '2025-06-22 10:57:49', '2025-06-22 10:57:49'),
(115, 70, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, NULL, 20000, 'Superadmin', NULL, 0, '2025-06-22 10:59:05', '2025-06-22 10:59:05'),
(116, 71, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, NULL, 20000, 'Superadmin', NULL, 0, '2025-06-22 11:00:25', '2025-06-22 11:00:25'),
(117, 72, 1, '1.00', '1.00', '25000.00', 5000, NULL, NULL, NULL, NULL, 20000, 'Superadmin', NULL, 0, '2025-06-22 11:00:48', '2025-06-22 11:00:48');

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
(1, 'Superadmin', 'Super.Admin', '$2y$12$Z8DclzYSk.AbvJS3M.tqOuJ.Uh3RSOIN8u8pSwzUyUcxF5cPCXf.G', '1', '0', NULL, '2025-06-14 06:40:46', '2025-06-14 06:40:46');

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
-- Indexes for table `harga_produk`
--
ALTER TABLE `harga_produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `harga_produk_new`
--
ALTER TABLE `harga_produk_new`
  ADD PRIMARY KEY (`id`),
  ADD KEY `harga_produk_new_produk_id_foreign` (`produk_id`);

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `harga_produk`
--
ALTER TABLE `harga_produk`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `harga_produk_new`
--
ALTER TABLE `harga_produk_new`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `historynotas`
--
ALTER TABLE `historynotas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `history_payments`
--
ALTER TABLE `history_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produks`
--
ALTER TABLE `produks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `produk_bahans`
--
ALTER TABLE `produk_bahans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `harga_produk_new`
--
ALTER TABLE `harga_produk_new`
  ADD CONSTRAINT `harga_produk_new_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE CASCADE;

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
