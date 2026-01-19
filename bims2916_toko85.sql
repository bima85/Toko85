-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 11 Jan 2026 pada 09.52
-- Versi server: 11.4.9-MariaDB-cll-lve
-- Versi PHP: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bims2916_toko85`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_kategori` varchar(255) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `kode_kategori`, `nama_kategori`, `description`, `created_at`, `updated_at`) VALUES
(1, 'BERAS', 'BERAS', 'Kategori produk beras', '2025-11-01 14:08:59', '2025-11-01 14:08:59'),
(2, 'LN', 'Lain', NULL, '2025-11-02 08:23:10', '2025-11-02 08:23:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_pelanggan` varchar(50) NOT NULL,
  `nama_pelanggan` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `customers`
--

INSERT INTO `customers` (`id`, `kode_pelanggan`, `nama_pelanggan`, `alamat`, `telepon`, `email`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'PLGN', 'PT GRESIK', 'solo', '089023432', 'testing@testing.com', NULL, '2025-11-06 13:00:28', '2025-11-06 13:00:28'),
(2, 'SUROSO44', 'Suroso', 'solo', '085673241111', '', NULL, '2026-01-02 08:24:11', '2026-01-02 08:24:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
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
-- Struktur dari tabel `jobs`
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
-- Struktur dari tabel `job_batches`
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
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_09_22_145432_add_two_factor_columns_to_users_table', 1),
(5, '2025_10_30_054915_create_permission_tables', 1),
(6, '2025_10_30_101200_add_username_to_users_table', 1),
(7, '2025_10_30_115742_create_categories_table', 1),
(8, '2025_10_30_115909_rename_name_to_nama_kategori_in_categories_table', 1),
(9, '2025_10_30_120343_add_kode_kategori_to_categories_table', 1),
(10, '2025_10_30_193348_create_subcategories_table', 1),
(11, '2025_10_30_195043_create_products_table', 1),
(12, '2025_10_30_201136_remove_harga_and_stok_from_products_table', 1),
(13, '2025_10_30_202511_create_units_table', 1),
(14, '2025_10_30_202754_create_suppliers_table', 1),
(15, '2025_10_30_203002_create_customers_table', 1),
(16, '2025_10_30_203419_create_warehouses_table', 1),
(17, '2025_10_30_203848_create_stores_table', 1),
(18, '2025_10_30_205249_add_conversion_fields_to_units_table', 1),
(19, '2025_10_31_134040_create_purchases_table', 1),
(20, '2025_10_31_135039_modify_purchases_table_for_transaction_structure', 1),
(21, '2025_10_31_135045_create_purchase_items_table', 1),
(22, '2025_10_31_150959_add_store_id_to_purchases_table', 1),
(23, '2025_10_31_215125_add_warehouse_id_to_purchases_table', 1),
(24, '2025_10_31_215436_create_stock_adjustments_table', 1),
(25, '2025_11_01_000000_add_unit_id_to_stock_adjustments_table', 1),
(26, '2025_11_01_104405_add_stok_fields_to_stock_adjustments_table', 1),
(27, '2025_11_01_120000_add_qty_gudang_to_purchase_items_table', 1),
(28, '2025_11_03_140000_create_stock_batch_tables', 2),
(29, '2025_11_06_151301_create_sales_table', 3),
(30, '2025_11_06_191354_update_sales_table_schema', 4),
(31, '2025_11_06_191421_create_sale_items_table', 4),
(32, '2025_11_07_133542_add_batch_id_to_sale_items_table', 5),
(33, '2025_11_07_195504_add_total_stok_to_stock_adjustments_table', 6),
(34, '2025_11_07_201620_add_delivery_note_fields_to_sales_table', 7),
(35, '2025_11_15_093704_create_transaction_histories_table', 8),
(37, '2025_11_17_142000_update_stock_cards_table', 9),
(38, '2025_11_29_171354_update_sale_items_batch_id_foreign_key', 10),
(39, '2025_12_09_000500_add_created_at_index_to_stock_batches_table', 11),
(40, '2025_12_12_204004_add_note_to_stock_batches_table', 11),
(41, '2025_12_14_add_hold_timestamps_to_sales', 12),
(42, '2025_12_14_create_status_column_stock_batches', 12),
(43, '2025_12_14_214326_add_hold_to_sales_status_enum', 13),
(44, '2025_12_14_214743_add_hold_types_to_stock_cards_type_enum', 14),
(45, '2025_12_15_131859_add_total_amount_to_sales_table', 15),
(46, '2025_12_22_120000_add_owner_to_suppliers_table', 16),
(47, '2025_12_22_121000_add_supplier_id_to_products_table', 16),
(48, '2026_01_03_120000_remove_unique_transaction_code', 17),
(49, '2026_01_03_140000_add_batches_to_purchase_items_table', 18);

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'manage users', 'web', '2025-11-01 14:08:59', '2025-11-01 14:08:59'),
(2, 'manage settings', 'web', '2025-11-01 14:08:59', '2025-11-01 14:08:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_produk` varchar(255) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `satuan` varchar(255) NOT NULL DEFAULT 'pcs',
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `subcategory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `kode_produk`, `nama_produk`, `description`, `satuan`, `category_id`, `subcategory_id`, `supplier_id`, `created_at`, `updated_at`) VALUES
(1, 'BRS_001_MAWAR', 'MAWAR', 'Produk beras MAWAR', 'pcs', 1, 1, NULL, '2025-11-01 14:08:59', '2025-11-01 14:08:59'),
(2, 'BRS_002_WT', 'WT', 'Produk beras WT', 'pcs', 1, 1, NULL, '2025-11-01 14:08:59', '2025-11-01 14:08:59'),
(3, 'BRS_003_ANGGREK', 'ANGGREK', 'Produk beras ANGGREK', 'pcs', 1, 1, NULL, '2025-11-01 14:08:59', '2025-11-01 14:08:59'),
(4, 'BRS_004_KASTURI', 'KASTURI', 'Produk beras KASTURI', 'pcs', 1, 1, NULL, '2025-11-01 14:08:59', '2025-11-01 14:08:59'),
(5, 'BRS_005_LN', 'LN', 'Produk beras LN', 'pcs', 1, 1, NULL, '2025-11-01 14:08:59', '2025-11-01 14:08:59'),
(6, 'BRS_006_PD', 'PD', 'Produk beras PD', 'sak', 1, 1, NULL, '2025-11-01 14:08:59', '2026-01-04 03:52:19'),
(8, 'BRS_001_5KGKL', '5KG KL', 'Produk beras 5KG KL', 'sak', 1, 2, NULL, '2025-11-01 14:22:06', '2025-11-05 05:35:01'),
(9, 'BRS_002_5KGWG', '5KG WG', 'Produk beras 5KG WG', 'sak', 1, 2, NULL, '2025-11-01 14:22:06', '2025-11-05 05:57:39'),
(10, 'BRS_003_5KGPD', '5KG PD', 'Produk beras 5KG PD', 'sak', 1, 2, NULL, '2025-11-01 14:22:06', '2025-11-10 07:59:38'),
(11, 'BRS_004_5KGORG', '5KG ORG', 'Produk beras 5KG ORG', 'sak', 1, 2, NULL, '2025-11-01 14:22:06', '2025-11-10 08:22:56'),
(12, 'BRS_005_5KGC4', '5KG C4', 'Produk beras 5KG C4', 'sak', 1, 2, NULL, '2025-11-01 14:22:06', '2025-11-10 08:31:19'),
(13, 'BRS_006_5KGPUTIJO', '5KG PUT IJO', 'Produk beras 5KG PUT IJO', 'sak', 1, 2, NULL, '2025-11-01 14:22:06', '2025-11-10 08:32:43'),
(14, 'BRS_007_1KGBM', '1KG BM', 'Produk beras 1KG BM', 'sak', 1, 2, NULL, '2025-11-01 14:22:06', '2025-11-10 08:33:39'),
(15, 'BRS_008_5KGBM', '5KG BM', 'Produk beras 5KG BM', 'sak', 1, 2, NULL, '2025-11-01 14:22:06', '2025-11-10 08:35:20'),
(16, 'BRS_009_JEMPOL', 'JEMPOL', 'Produk beras JEMPOL', 'sak', 1, 2, NULL, '2025-11-01 14:22:06', '2025-12-12 15:50:11'),
(17, 'BRS_010_LAK', 'LAK', 'Produk beras LAK', 'kg', 1, 2, NULL, '2025-11-01 14:22:06', '2025-11-01 14:22:06'),
(18, 'BRS_011_10KGLELE', '10KG LELE', 'Produk beras 10KG LELE', 'sak', 1, 2, NULL, '2025-11-01 14:22:06', '2025-11-10 08:37:03'),
(19, 'BRS_012_10KGKL', '10KG KL', 'Produk beras 10KG KL', 'sak', 1, 2, NULL, '2025-11-01 14:22:06', '2025-11-10 08:37:27'),
(20, 'BRS_013_10KGSIP', '10KG SIP', 'Produk beras 10KG SIP', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 08:51:34'),
(21, 'BRS_014_5KGGAJAH', '5KG GAJAH', 'Produk beras 5KG GAJAH', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 08:52:04'),
(22, 'BRS_015_KELINCI', 'KELINCI', 'Produk beras KELINCI', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 08:54:35'),
(23, 'BRS_016_LA', 'LA', 'Produk beras LA', 'kg', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-01 14:22:07'),
(24, 'BRS_017_GAJAH', 'GAJAH', 'Produk beras GAJAH', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 09:11:18'),
(25, 'BRS_018_LELEB', 'LELE B', 'Produk beras LELE B', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 09:11:56'),
(26, 'BRS_019_NGREMIN', 'NG REMIN', 'Produk beras NG REMIN', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 09:12:34'),
(27, 'BRS_020_PUTRIBIRU', 'PUTRI BIRU', 'Produk beras PUTRI BIRU', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 09:13:10'),
(28, 'BRS_021_WALI9', 'WALI 9', 'Produk beras WALI 9', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 09:14:24'),
(29, 'BRS_022_PRIMA', 'PRIMA', 'Produk beras PRIMA', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 09:14:47'),
(30, 'BRS_023_SIP', 'SIP', 'Produk beras SIP', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 09:15:44'),
(31, 'BRS_024_DEWIAYU', 'DEWI AYU', 'Produk beras DEWI AYU', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 10:59:09'),
(32, 'BRS_025_PACUL', 'PACUL', 'Produk beras PACUL', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 11:00:47'),
(33, 'BRS_026_TKDJADI', 'TKD JADI', 'Produk beras TKD JADI', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 11:03:55'),
(34, 'BRS_027_BENGAWAN', 'BENGAWAN', 'Produk beras BENGAWAN', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 11:04:59'),
(35, 'BRS_028_NGJK', 'NG JK', 'Produk beras NG JK', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 11:10:07'),
(36, 'BRS_029_BAMBU', 'BAMBU', 'Produk beras BAMBU', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 11:11:12'),
(37, 'BRS_030_PUTH', 'PUT H', 'Produk beras PUT H', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-10 11:13:16'),
(38, 'BRS_031_STROBERIA', 'STROBERI A', 'Produk beras STROBERI A', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 01:59:51'),
(39, 'BRS_032_JERUK', 'JERUK', 'Produk beras JERUK', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 02:03:40'),
(40, 'BRS_033_SENYUM', 'SENYUM', 'Produk beras SENYUM', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 02:06:52'),
(41, 'BRS_034_GNR', 'GNR', 'Produk beras GNR', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 02:07:57'),
(42, 'BRS_035_AS', 'AS', 'Produk beras AS', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 02:09:56'),
(43, 'BRS_036_KOI', 'KOI', 'Produk beras KOI', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 02:10:39'),
(44, 'BRS_037_BMERAH', 'B MERAH', 'Produk beras B MERAH', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 02:11:06'),
(45, 'BRS_038_ALPUKAT', 'ALPUKAT', 'Produk beras ALPUKAT', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 02:12:02'),
(46, 'BRS_039_RAJABARU', 'RAJA BARU', 'Produk beras RAJA BARU', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 02:13:25'),
(47, 'BRS_040_BERUANG', 'BERUANG', 'Produk beras BERUANG', 'kg', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-01 14:22:07'),
(48, 'BRS_041_BONSAI', 'BONSAI', 'Produk beras BONSAI', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 02:13:53'),
(49, 'BRS_042_ANGSA', 'ANGSA', 'Produk beras ANGSA', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:36:11'),
(50, 'BRS_043_SAFIRA', 'SAFIRA', 'Produk beras SAFIRA', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 02:23:51'),
(51, 'BRS_044_NN', 'NN', 'Produk beras NN', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 02:25:27'),
(52, 'BRS_045_PANDA', 'PANDA', 'Produk beras PANDA', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 02:55:39'),
(53, 'BRS_046_TGH', 'TGH', 'Produk beras TGH', 'kg', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-01 14:22:07'),
(54, 'BRS_047_DORY', 'DORY', 'Produk beras DORY', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:07:45'),
(55, 'BRS_048_TOMO', 'TOMO', 'Produk beras TOMO', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:12:41'),
(56, 'BRS_049_PATAHAY', 'PATAH AY', 'Produk beras PATAH AY', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:16:10'),
(57, 'BRS_050_BANDENGB', 'BANDENG B', 'Produk beras BANDENG B', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:19:52'),
(58, 'BRS_051_SIMBOK', 'SIMBOK', 'Produk beras SIMBOK', 'kg', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-01 14:22:07'),
(59, 'BRS_052_PULEN', 'PULEN', 'Produk beras PULEN', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:29:51'),
(60, 'BRS_053_HTM', 'HT M', 'Produk beras HT M', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:24:27'),
(61, 'BRS_054_BRMOR', 'BRM OR', 'Produk beras BRM OR', 'kg', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-01 14:22:07'),
(62, 'BRS_055_AN', 'AN', 'Produk beras AN', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:26:16'),
(63, 'BRS_056_MENIR', 'MENIR', 'Produk beras MENIR', 'kg', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-01 14:22:07'),
(64, 'BRS_057_NGPR', 'NG PR', 'Produk beras NG PR', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:35:18'),
(65, 'BRS_058_PATAHJK', 'PATAH JK', 'Produk beras PATAH JK', 'kg', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-01 14:22:07'),
(66, 'BRS_059_NGYY', 'NG YY', 'Produk beras NG YY', 'kg', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-01 14:22:07'),
(67, 'BRS_060_DIMAS', 'DIMAS', 'Produk beras DIMAS', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:31:27'),
(68, 'BRS_061_JAGO', 'JAGO', 'Produk beras JAGO', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:32:13'),
(69, 'BRS_062_HEPPY', 'HEPPY', 'Produk beras HEPPY', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:32:47'),
(70, 'BRS_063_DOKAR', 'Dokar', 'Produk beras Dokar', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:33:57'),
(71, 'BRS_064_BRM', 'BRM', 'Produk beras BRM', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:34:46'),
(72, 'BRS_065_NGRPR', 'NGRPR', 'Produk beras NGRPR', 'kg', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-01 14:22:07'),
(73, 'BRS_066_KACER', 'KACER', 'Produk beras KACER', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:35:47'),
(74, 'BRS_068_PKDJADI', 'PKD JADI', 'Produk beras PKD JADI', 'kg', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-01 14:22:07'),
(75, 'BRS_070_BANDENG', 'BANDENG', 'Produk beras BANDENG', 'kg', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-01 14:22:07'),
(76, 'BRS_073_KL', 'KL', 'Produk beras KL', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:47:02'),
(77, 'BRS_074_DEWI', 'DEWI', 'Produk beras DEWI', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:47:46'),
(78, 'BRS_075_PUTIJO', 'PUT IJO', 'Produk beras PUT IJO', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:49:24'),
(79, 'BRS_077_DOKER', 'DOKER', 'Produk beras DOKER', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:53:43'),
(80, 'BRS_078_SIOMAY', 'SIOMAY', 'Produk beras SIOMAY', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:25:49'),
(81, 'BRS_079_NGMR', 'NG MR', 'Produk beras NG MR', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 04:12:14'),
(82, 'BRS_080_HTM', 'HTM', 'Produk beras HTM', 'kg', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-01 14:22:07'),
(83, 'BRS_081_PTHWG50', 'PTH WG 50', 'Produk beras PTH WG 50', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 04:14:01'),
(84, 'BRS_082_NGDOL', 'NG DOL', 'Produk beras NG DOL', 'sak', 1, 2, NULL, '2025-11-01 14:22:07', '2025-11-16 03:58:00'),
(85, 'BRS_001_KEMBANGA', 'KEMBANG A', 'Produk beras KEMBANG A', 'pcs', 1, 3, NULL, '2025-11-01 14:22:48', '2025-11-01 14:22:48'),
(86, 'BRS_002_KEMBANGB', 'KEMBANG B', 'Produk beras KEMBANG B', 'pcs', 1, 3, NULL, '2025-11-01 14:22:48', '2025-11-01 14:22:48'),
(87, 'BRS_003_HERLINA', 'HERLINA', 'Produk beras HERLINA', 'pcs', 1, 3, NULL, '2025-11-01 14:22:48', '2025-11-01 14:22:48'),
(88, 'BRS_004_DPJ', 'DPJ', 'Produk beras DPJ', 'pcs', 1, 3, NULL, '2025-11-01 14:22:48', '2025-11-01 14:22:48'),
(89, 'BRS_005_DJ', 'DJ', 'Produk beras DJ', 'pcs', 1, 3, NULL, '2025-11-01 14:22:48', '2025-11-01 14:22:48'),
(90, 'BRS_006_NANAS', 'NANAS', 'Produk beras NANAS', 'pcs', 1, 3, NULL, '2025-11-01 14:22:48', '2025-11-01 14:22:48'),
(91, 'BRS_007_MJ', 'MJ', 'Produk beras MJ', 'pcs', 1, 3, NULL, '2025-11-01 14:22:48', '2025-11-01 14:22:48'),
(92, 'BRS_008_SJM', 'SJM', 'Produk beras SJM', 'pcs', 1, 3, NULL, '2025-11-01 14:22:48', '2025-11-01 14:22:48'),
(93, 'BRS_009_SWAN', 'SWAN', 'Produk beras SWAN', 'pcs', 1, 3, NULL, '2025-11-01 14:22:48', '2025-11-01 14:22:48'),
(94, 'BRS_010_KEMBANGC', 'KEMBANG C', 'Produk beras KEMBANG C', 'pcs', 1, 3, NULL, '2025-11-01 14:22:48', '2025-11-01 14:22:48'),
(95, 'BRS_011_JAGO', 'JAGO', 'Produk beras JAGO', 'pcs', 1, 3, NULL, '2025-11-01 14:22:48', '2025-11-01 14:22:48'),
(96, 'BRS_014_KEMBANG', 'KEMBANG', 'Produk beras KEMBANG', 'pcs', 1, 3, NULL, '2025-11-01 14:22:48', '2025-11-01 14:22:48'),
(97, 'BRS_015_WALET', 'WALET', 'Produk beras WALET', 'pcs', 1, 3, NULL, '2025-11-01 14:22:48', '2025-11-01 14:22:48'),
(98, 'LLK_001_LLK', 'LLK', NULL, 'sak', 1, 2, NULL, '2025-11-01 14:41:16', '2025-11-16 08:30:39'),
(99, 'PUT_001_PUTAYU', 'put ayu', 'Beras c4', 'sak', 1, 2, NULL, '2025-11-02 03:15:40', '2025-11-16 02:57:45'),
(100, 'BER_100_NGJA', 'NG JA', NULL, 'sak', 1, 2, NULL, '2025-11-02 10:16:38', '2025-11-16 02:22:34'),
(101, 'BER_101_BANDENGM', 'BANDENG M', NULL, 'sak', 1, 2, NULL, '2025-11-02 10:24:09', '2025-11-16 03:18:29'),
(102, 'BER_102_SAYUR', 'SAYUR ', NULL, 'sak', 1, 2, NULL, '2025-11-02 10:25:48', '2025-11-16 03:21:38'),
(103, 'BER_103_PULEN', 'PULEN', NULL, 'pcs', 1, 2, NULL, '2025-11-02 10:28:52', '2025-11-02 10:28:52'),
(104, 'BER_104_BIMA', 'BIMA', NULL, 'sak', 1, 2, NULL, '2025-11-02 10:33:44', '2025-11-16 03:30:54'),
(106, 'BER_106_DOLPIN', 'DOLPIN', NULL, 'sak', 1, 2, NULL, '2025-11-02 10:58:33', '2025-11-16 04:06:44'),
(107, 'BER_107_BRONIS', 'BRONIS', NULL, 'sak', 1, 2, NULL, '2025-11-02 10:59:12', '2025-11-16 04:29:11'),
(108, 'BER_108_BAMBU', 'BAMBU', NULL, 'pcs', 1, 2, NULL, '2025-11-02 11:12:46', '2025-11-02 11:12:46'),
(110, 'RJLL A', 'Rojolele A', NULL, 'sak', 1, 2, NULL, '2025-12-07 13:50:32', '2025-12-07 13:50:32'),
(111, '5KGSR', '5kg SR', NULL, 'sak', 1, 2, NULL, '2025-12-10 09:16:39', '2025-12-10 09:16:39'),
(112, 'STRBRY', 'STROBERI', NULL, 'sak', 1, 2, NULL, '2025-12-11 12:23:58', '2025-12-11 12:23:58'),
(113, 'RJEK', 'RIJEk', NULL, 'sak', 1, 2, NULL, '2025-12-11 14:13:00', '2025-12-11 14:13:00'),
(114, 'PTHB', 'PATAH B', NULL, 'sak', 1, 2, NULL, '2025-12-11 14:16:33', '2025-12-11 14:16:33'),
(115, 'TMUN', 'TIMUN', NULL, 'sak', 1, 2, NULL, '2025-12-11 14:17:20', '2025-12-11 14:17:20'),
(116, 'DMR', 'DAMAR', NULL, 'sak', 1, 2, NULL, '2025-12-11 14:18:37', '2025-12-11 14:18:37'),
(117, 'APL', 'APEL', NULL, 'sak', 1, 2, NULL, '2025-12-11 14:20:19', '2025-12-11 14:20:19'),
(118, 'MRANGGREK', 'MR ANGGREK', NULL, 'sak', 1, 2, NULL, '2025-12-11 14:21:53', '2025-12-11 14:21:53'),
(119, 'STRBRIM', 'STRAOBERI M', NULL, 'sak', 1, 2, NULL, '2025-12-11 15:35:28', '2025-12-11 15:35:28'),
(120, 'PTHBD', 'PTH BD', NULL, 'sak', 1, 2, NULL, '2025-12-11 15:38:07', '2025-12-11 15:38:07'),
(121, 'RJHT', 'RJ HT', NULL, 'sak', 1, 2, NULL, '2025-12-12 15:20:39', '2025-12-12 15:20:39'),
(122, 'BRNIR', 'BRONIR', NULL, 'sak', 1, 2, NULL, '2025-12-12 15:24:34', '2025-12-12 15:24:34'),
(123, 'PTHPD', 'PTH PD', NULL, 'sak', 1, 2, NULL, '2025-12-12 15:25:37', '2025-12-12 15:25:37'),
(124, 'PTHAY', 'PTH AY', NULL, 'sak', 1, 2, NULL, '2025-12-12 15:26:29', '2025-12-12 15:26:29'),
(125, 'PUTBIRU', 'PUT BIRU', NULL, 'sak', 1, 2, NULL, '2025-12-12 15:28:44', '2025-12-12 15:28:44'),
(126, 'BG', 'BG', NULL, 'sak', 1, 2, NULL, '2025-12-13 08:10:15', '2025-12-13 08:10:15'),
(127, 'STRBRIMM', 'STROBERI MM', NULL, 'sak', 1, 2, NULL, '2025-12-16 13:25:00', '2025-12-16 13:25:00'),
(128, 'PTH_B', 'PTH B', NULL, 'sak', 1, 2, NULL, '2025-12-16 14:55:21', '2025-12-16 14:55:21'),
(129, 'PRD-ZSKF2F', 'Naga Jaya Abadi', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(130, 'PRD-UHUZIM', 'Rojolele A', NULL, 'Kg', 1, 4, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(131, 'PRD-ZXUN37', 'Mentik Wangi Kasturi', NULL, 'Kg', 1, 5, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(132, 'PRD-A2PC2Z', 'Patah Dua Doro', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(133, 'PRD-TE16HG', 'Hijab', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(134, 'PRD-TXNNKM', 'Lele Barokah', NULL, 'Kg', 1, 4, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(135, 'PRD-9JKNPH', 'Mentik Gatot', NULL, 'Kg', 1, 5, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(136, 'PRD-4RWWMC', 'Siip', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(137, 'PRD-5JBUWZ', 'Bestie', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(138, 'PRD-SXSUHZ', 'Naga Bu Remin', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(139, 'PRD-D0ADFD', 'Raja Remin', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(140, 'PRD-KHZ0UM', 'Wangi PT', NULL, 'Kg', 1, 5, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(141, 'PRD-IXXX9G', 'Ikan Dory', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(142, 'PRD-ISGWZT', 'Putri Indonesia Biru', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(143, 'PRD-I0NZ2X', 'Naga Mas Tekad Jadi', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(144, 'PRD-0IVJQ4', 'WaliSongo', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(145, 'PRD-ZNJLNF', 'Patah Wangi', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(146, 'PRD-6G1RSU', 'Mentik WT', NULL, 'Kg', 1, 5, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(147, 'PRD-FNPEJI', 'Cething Mas', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(148, 'PRD-A7GMUJ', 'Wangi SW', NULL, 'Kg', 1, 5, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(149, 'PRD-LDJMID', 'Kendil', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(150, 'PRD-WDPYWD', 'Naga Mas JK', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(151, 'PRD-AZ2BWM', 'Patah PAB', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(152, 'PRD-2GKNCO', 'Angsa Terbang', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(153, 'PRD-2UD6YJ', 'Dolphin', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(154, 'PRD-TYBB7W', 'Pari Ijo', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(155, 'PRD-4KC8DI', 'Anggur', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(156, 'PRD-I6PPSK', 'Jago Biru', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(157, 'PRD-IYG1CL', 'Lele Hitam', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(159, 'PRD-KJP7SX', 'Mbok Ben', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(160, 'PRD-LCYEAD', 'Kinclong', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(161, 'PRD-HQBRS7', 'Untung', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(162, 'PRD-FRLDEO', 'Pandawa', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(163, 'PRD-ZS0NTO', 'Indokoki', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(164, 'PRD-NVYWYC', 'Mentik Semongko', NULL, 'Kg', 1, 5, NULL, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(165, 'PRD-ZXRPW6', 'Bandeng Biru', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:19', '2025-12-22 04:22:19'),
(166, 'PRD-5NRIYD', 'Bandeng Pink', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:19', '2025-12-22 04:22:19'),
(167, 'PRD-ZNVVZU', 'Patah Polos', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:19', '2025-12-22 04:22:19'),
(168, 'PRD-ECK97P', 'Naga Sandy Putra', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:19', '2025-12-22 04:22:19'),
(169, 'PRD-SXA1NQ', 'Putri Indonesia Hijau', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:19', '2025-12-22 04:22:19'),
(170, 'PRD-DJAPAQ', 'Stobery A', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:19', '2025-12-22 04:22:19'),
(171, 'PRD-UOI5Y3', 'Raja Baru Makmur', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:19', '2025-12-22 04:22:19'),
(172, 'PRD-Z9HDML', 'Wangi GNR', NULL, 'Kg', 1, 5, NULL, '2025-12-22 04:22:19', '2025-12-22 04:22:19'),
(173, 'PRD-IVMN4D', 'C4 AN', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:19', '2025-12-22 04:22:19'),
(174, 'PRD-J5NZKK', 'Raja HT', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:19', '2025-12-22 04:22:19'),
(175, 'PRD-YG5AXI', 'HT Pink', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:19', '2025-12-22 04:22:19'),
(176, 'PRD-IWVPGI', 'Apel HT', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:19', '2025-12-22 04:22:19'),
(177, 'PRD-MDR8XT', 'Mawar HT', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:19', '2025-12-22 04:22:19'),
(182, 'PRD-1I2WD2', 'Dua Mawar', NULL, 'Kg', 1, 3, NULL, '2025-12-22 04:22:19', '2026-01-04 04:15:38'),
(190, 'PRD-BRNOF5', 'Muray Batu', NULL, 'Kg', 1, 2, NULL, '2025-12-22 04:22:19', '2025-12-22 04:22:19'),
(191, 'BRS', 'PT', NULL, 'sak', 1, 1, NULL, '2026-01-04 03:46:44', '2026-01-04 03:46:44'),
(192, 'BRSPR', 'PR', NULL, 'sak', 1, 1, NULL, '2026-01-04 03:48:39', '2026-01-04 03:48:39'),
(193, 'BRSSMUT', 'Semut', NULL, 'sak', 1, 3, NULL, '2026-01-04 04:20:40', '2026-01-04 04:20:40'),
(194, 'BRSRJWALI', 'Raja wali', NULL, 'sak', 1, 3, NULL, '2026-01-04 07:36:02', '2026-01-04 07:36:02'),
(195, 'BRDGLTIK', 'Galatik', NULL, 'sak', 1, 3, NULL, '2026-01-04 07:38:59', '2026-01-04 07:38:59'),
(198, 'LJ_001_LJ', 'LJ', NULL, 'pcs', 1, 3, NULL, '2026-01-06 02:35:21', '2026-01-06 02:35:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_invoice` varchar(50) NOT NULL,
  `tanggal_pembelian` date NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'pending',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `store_id` bigint(20) UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `subcategory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL,
  `qty_gudang` int(11) NOT NULL DEFAULT 0,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `harga_beli` decimal(12,2) NOT NULL,
  `batches` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'JSON array of batch data [{name, qty}, ...]' CHECK (json_valid(`batches`)),
  `total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2025-11-01 14:08:59', '2025-11-01 14:08:59'),
(2, 'user', 'web', '2025-11-01 14:08:59', '2025-11-01 14:08:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_invoice` varchar(50) NOT NULL,
  `delivery_note_number` varchar(50) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_notes` text DEFAULT NULL,
  `tanggal_penjualan` datetime DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `store_id` bigint(20) UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT NULL,
  `status` enum('pending','completed','cancelled','hold') NOT NULL DEFAULT 'pending',
  `held_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sale_items`
--

CREATE TABLE `sale_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `harga_jual` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `batch_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
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
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0xVBfI4ivIg87SYrVGj7Jq85dMccrDThkZDlP0kr', NULL, '84.37.240.53', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZUFGcXJWc01vSVNBZlM0ZFdJblNNTzdSMjhTQ2N6VWxoWXRVZXVkbiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767517976),
('0ZRFDsbkpaHFOmlUUhItvDIouqAgspz6dPpBhC1F', NULL, '85.254.138.190', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidDhHUW5udnpDSktybGpWUml6V1pOSlM0WmFBRms3SHdZNUozMzBzVyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvbG9naW4iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767517828),
('26JOCkTOENvHf0PkGn8OmvgX130TUilcRKotPEqm', NULL, '85.254.138.190', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicEl5bEhQMmVwVWtmNW9jN3NNS09Ca2psMHFUQkpDS2ZtaENWeGpkdSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767517829),
('2Ipkzhd9dEbykcLw9TiMscpQySY4cXJ04hu22dC8', NULL, '74.125.182.111', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUEVJQWtJN2xHSzJwU3l1SEdyYmVCamM2VUduazVhUXl5Zlh4QmRidiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767511696),
('3AKyzQ5ipOYWvSgPnKVonWLCQmmuMTH6SH7XLfFG', NULL, '104.174.60.137', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMDVRYjRWdEJacUNMZXdodGdiVnp5WGMxRUR3THBrZDE0YWpOM1RuayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvbG9naW4iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767518405),
('641pN2TidPI7gOFoGLbcObUaMOROXLNyBNa0KFxt', 2, '182.9.1.50', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibk1TazRLM2xjMmhOeGV4bFkzaFk2Y2pSVVU2MHBtZ1JETUVmanVMaiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0ODoiaHR0cHM6Ly90b2tvODUuYmltYXRlY2guc2l0ZS9hZG1pbi9zdG9jay1iYXRjaGVzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvYWRtaW4vc3RvY2stYmF0Y2hlcyI7czo1OiJyb3V0ZSI7czoyNToiYWRtaW4uc3RvY2stYmF0Y2hlcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1767526044),
('8ljDtcdYMeA1zM3JnMaWOEQIsVB5J05njtEbpAtR', NULL, '108.212.105.222', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.0 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibnVBeURvWmY3bmpMdno1ZzZLbVphb3NydzFxRGVuNGRhS1RtRWQ1MiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767450600),
('93m7Jau9Lel9YVgudb206cABCp4tLnROdUoZCReY', NULL, '213.201.148.169', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN2NFOU5VTGNxMXFwYWhFZFRBWlBScVF5QlJSdU44Y3YxY0x2OXVFeSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767513387),
('awlU6RyAewcIAXF8Zvvq0PsGPUmY73nRUmUnGTnc', 2, '182.9.1.50', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoib2RqR1FGNDlMNHo5TWtsWXVzZ0RvWE5jcTZnWFRnMzRXMlB6TlFocyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozODoiaHR0cHM6Ly90b2tvODUuYmltYXRlY2guc2l0ZS9kYXNoYm9hcmQiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czoyODoiaHR0cHM6Ly90b2tvODUuYmltYXRlY2guc2l0ZSI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1767532672),
('BEYo8kUMNcSMGU2kJ4e0sQZFlsjvM1wR0W7dZe9M', NULL, '182.9.1.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSWthcHdCdmtPRUVqcGh0NHU2NUMwY3h3M25BZ0t1WUlxNUNIdXZCWSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fX0=', 1767434218),
('BjWOGYtEOB0To3An1YMHV5l08BqkTZUySU5UDkXZ', NULL, '104.174.60.137', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVXRJNVFDdFdqR3BOV3g5WVo3VzgwWjU2SGVyUzNvaU54aDNXQml3WCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0ODoiaHR0cHM6Ly90b2tvODUuYmltYXRlY2guc2l0ZS9hZG1pbi9zdG9jay1iYXRjaGVzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvYWRtaW4vc3RvY2stYmF0Y2hlcyI7czo1OiJyb3V0ZSI7czoyNToiYWRtaW4uc3RvY2stYmF0Y2hlcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767518404),
('BStmIkdlCy5g7T2dsXTu60BokB7TO0sGqONLGtCD', NULL, '67.207.85.153', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV3RXamNqelFFZ241Q011Z3RoTnRVcFhNcUJ4UTlJbUVWbTRzUzBqWCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767518426),
('bW5IRx5SVewoysN0Su7DRvqa6ozAeCzkj0Qg3X68', NULL, '182.9.1.50', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiV1V5MlF2NHlGdkFsY2RoRVRQbDNWdGFxSFpqc1Rtbjl6RlhLTHdxTiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1767531794),
('bZAXOmY9v5nRIqyB6qfnl2brsKYrrqeVTHseFhBm', NULL, '182.9.1.50', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUjNyUlV2YnN2ak1WU0JmUHV2cVpLaHdVVjVlNVhwNllwTXE0bFdFWSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fX0=', 1767511704),
('Cwc98JlKicqFpCsRHur6lRHkvn0vbPlOMLbJPU4B', NULL, '172.252.47.250', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZFNScUNYRlNpSHl5RnRnOThOOFFUWjlXaU9tVjFHV0IzSFB3d3VubSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo2MDoiaHR0cHM6Ly90b2tvODUuYmltYXRlY2guc2l0ZS9hZG1pbi9zdG9jay1iYXRjaGVzP3NlYXJjaD1qYWdvIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjA6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvYWRtaW4vc3RvY2stYmF0Y2hlcz9zZWFyY2g9amFnbyI7czo1OiJyb3V0ZSI7czoyNToiYWRtaW4uc3RvY2stYmF0Y2hlcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767517829),
('DdpbXbps7WZUQO1VppN1DpaR5L5y4qrL9gcWf2Ue', NULL, '182.9.1.50', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiczYydVdQV0ZoZ0cybHAzUUtDcm1EU0FCZlJZYnBWWHJvVGpYNVRmSSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1767515427),
('e20C8ms7Y5mu56EaOyBWEgiAA6V8EuGZOZKrPCPX', 2, '182.9.1.50', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoieGRFakMxYjJVOXVPdmpkTnEwSWtIS3kwaGN5MldlZ1FMS2k0ZXhkbSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1767529023),
('gAQr2oIW1rPhH51HLs3vd2mUDCXLBAfScPUnRFF2', 2, '114.79.32.248', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZGJVZGNOQUJkdExJRHhZa1BRYjRFMTA4dDlhYzZtWnh6M3RHUUhzRSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvYWRtaW4vc3RvY2stcmVwb3J0cyI7czo1OiJyb3V0ZSI7czoxOToiYWRtaW4uc3RvY2stcmVwb3J0cyI7fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6NDg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvYWRtaW4vc3RvY2stYmF0Y2hlcyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1767440432),
('gooGqXEq1q78HY2DgbBDtcXZKJiy3bk3AMUDBXXg', 2, '182.9.1.50', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiYmFWRm9CT2VVRnd4SlN1eXBmNU1jUzFIazJjZ2duMllmR3BpVER3MCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0ODoiaHR0cHM6Ly90b2tvODUuYmltYXRlY2guc2l0ZS9hZG1pbi9zdG9jay1iYXRjaGVzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvYWRtaW4vc3RvY2stYmF0Y2hlcyI7czo1OiJyb3V0ZSI7czoyNToiYWRtaW4uc3RvY2stYmF0Y2hlcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjE6e2k6MDtzOjU6ImVycm9yIjt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1767526824),
('HeVaUDWqvtINJri8UyOyrEa8kaUz4Ktogg9e3d01', NULL, '74.7.227.128', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQjdreHJMYWh5TTNpQTlESTJGcjdsSW15c0taSVdKNnhlZHkzenFpMyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767525805),
('iAAcMlZrc8PPPsZXOVvrGVtWklzTGBkus3i7Yix6', 2, '182.9.1.50', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoieGpVMXFpc0hFMklwQUF2Rk95T2xiRnlWWmpDSmtobVpxbzNqWHIzeCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvYWRtaW4vc3RvY2stYmF0Y2hlcyI7czo1OiJyb3V0ZSI7czoyNToiYWRtaW4uc3RvY2stYmF0Y2hlcy5pbmRleCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1767525587),
('IpfUTTXv8rUghRtvX3E1BNN224gTM2t4HPXv7ZjJ', 2, '182.9.1.50', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNHlheE9YMmx3SE5naE9TNUY5SnZ5T0lFMU1PbTZaM3d2NndmdWNwOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1767531796),
('IXunzl12RWgd5lUNMeBqUzyunVRujsWTXVMFgrol', NULL, '182.9.1.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicUNTVGhsWWpSSTRZTDkxbEJLeHE2YzF2eXliMTBPNHFZRHdpQ3NUbyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767448295),
('JAsc3raNjsSteWzBHp5eQNlOfQ8U4WAdypPSfbAh', NULL, '85.254.138.190', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWGVYQXdGN3c2WjBHMjVudk4xSGZYRVljNE5sakZwT0JtM3VKVmtjcyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo2MDoiaHR0cHM6Ly90b2tvODUuYmltYXRlY2guc2l0ZS9hZG1pbi9zdG9jay1iYXRjaGVzP3NlYXJjaD1qYWdvIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjA6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvYWRtaW4vc3RvY2stYmF0Y2hlcz9zZWFyY2g9amFnbyI7czo1OiJyb3V0ZSI7czoyNToiYWRtaW4uc3RvY2stYmF0Y2hlcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767517828),
('JVWAvxN8L5V0IeNLjSzXcRpbuSTTwfitTRT2YJst', 2, '182.9.1.50', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiN0k0Y2VuVVc1dDlFZGJZOUZpSTNaQ0g3Vnk1ZjYxMmgyWVNrSFNXRiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozODoiaHR0cHM6Ly90b2tvODUuYmltYXRlY2guc2l0ZS9kYXNoYm9hcmQiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czoyODoiaHR0cHM6Ly90b2tvODUuYmltYXRlY2guc2l0ZSI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1767531928),
('kxPHTuaJw4GBSqn4Jg8nuDTSIdxIHHaocpAOdyNt', NULL, '185.211.96.88', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ1E2SWxCSmRqWVl4cmRscnRlUDk2ZzU0WHpDWUFGRUxuWGpWclcydyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767450605),
('LA1GgZY0AHWZ42BxOw0UOgAs1VXlnPC0RgoN6LKe', NULL, '104.174.60.137', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVVJyWk1CeU4xZmFobTN4ck1EbEszN3ZwWDh2MFJvQWNodlM2cGJOcyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767518406),
('llVQa0CBLQCfGNP90OlefhQQN2crPTlsPVmwADth', 2, '182.9.1.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaTZ2RDM1MEFza0xqUnlLQ0Q0a3oyNmF3bVQ2bjA3aGxFU3ZpelFObyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1767433661),
('N8U2RfxJqaWlLuPDnpKKQsUiahaRLSpRimti1gSr', NULL, '114.79.51.100', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT01TUU15ZGhaTXVDTm5uc1Y2R2JTSjFObDBodmNnaGZSZUtRUm05RCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fX0=', 1767437548),
('NUshAYoxtT8bVCL33nqcmrR3xVqSngYZjWGFC2Rt', NULL, '166.88.140.102', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWGFYNTdGM0xVM0pkUnZRNno0WFJyQlZRNGM1NHFIV3k3VG8zTDN5ViI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0ODoiaHR0cHM6Ly90b2tvODUuYmltYXRlY2guc2l0ZS9hZG1pbi9zdG9jay1iYXRjaGVzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvYWRtaW4vc3RvY2stYmF0Y2hlcyI7czo1OiJyb3V0ZSI7czoyNToiYWRtaW4uc3RvY2stYmF0Y2hlcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767518404),
('oqfQXT0lGnmbMIaNi8gi4z39BROt4QOgdxt1PlOO', NULL, '108.212.105.222', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.0 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiamNWWmpYbndqM3VoWllhVUZXVVM5V041cUVKb1JORk4xdjFVSDVsRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767450602),
('rGVGNqqtrta2RdX4slsaqDGu9RANawoUj5opdxqZ', NULL, '114.79.47.140', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoibjVHNlpjamxvb04wTjJtTTR6ZjRmaGZYTjFyS0dkSGl6WHo3RVF4ZCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1767436585),
('SzAetVSXNuolEr2SYZN23gJKudpNsSWkljpkW8QQ', 2, '182.9.1.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidk1WU2hMbURwTTdXbDA0aHN3RmNOTzFpbWJURjBjWHFZamxud1luVyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvYWRtaW4vc3RvY2stYmF0Y2hlcyI7czo1OiJyb3V0ZSI7czoxOToic3RvY2stYmF0Y2hlcy5pbmRleCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1767450826),
('T6DMPsF8bDB332hwlczcsrIBVwT1Ky4pRzYL4s1t', NULL, '182.9.1.50', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiblYydno3dEZhVkU4OU5oU2xYVnVQdVBPQ1VIa0lyREVFRDNPSkRSWSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fX0=', 1767489308),
('tXqIbNDmJmFXjkVewTqETyDqhYL1ddbLV9FgiSro', NULL, '182.9.1.50', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTm5lVWVub3ZOeWhkbE0xeWNMY2pqalhsOE9wR2xIcTRmcUZEOWh0OCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvbG9naW4iO3M6NToicm91dGUiO047fX0=', 1767527257),
('UbMvSqsNOI3kMpzxARqoUmenwIyspiJHWxdt3lxG', 2, '114.79.47.140', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidUozb2xvZUhqendLMnNXRUpIUDNPUzJRSExHNUZYenFnaEtZbFI4SiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvYWRtaW4vcHVyY2hhc2VzIjtzOjU6InJvdXRlIjtzOjE1OiJhZG1pbi5wdXJjaGFzZXMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1767436699),
('uNqCltiLqQmIlq259ZgnD3hkCiHLvCZQ5OpPOdFq', NULL, '84.37.229.69', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYk1UTVJoN21YNDJoV25Ob2FHdW5EOUxGeFIyek9SUnNrU3J5THg5VSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvbG9naW4iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767517865),
('Vaj2dFJy6kGaZqh9eoxXlaPwMEhxsS0kmMLvSgcz', 2, '182.9.1.50', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQ1ZwN1hqblk2Sll0Vk9MbDN6ZExMSkpDUTFkRms5NTdWTnFrWmNhRiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1767514530),
('VaMQ4CH6WY6N83YlqyjnoafHlpBGVWc37vzda6dq', NULL, '182.9.1.50', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiMW53eGpRYWZ4MkNOVERoSXEyVDBuNGRvQXp5ZTU0N21CMDdvUU4xayI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1767532688),
('VywQxzJ1Jx1iygMab9HJg36mZuinD2Ltk8n0185x', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoianE0Wk94Yk82SWxhT1FjcHNHSVZMRHVURzk5MGpBVTVFTTVET25MQSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM4OiJodHRwOi8vc2hvcDg1LnRlc3QvYWRtaW4vc3RvY2stYmF0Y2hlcyI7czo1OiJyb3V0ZSI7czoxOToic3RvY2stYmF0Y2hlcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1767429243),
('WEDLE9Ykc5l976H36ay6YrtU1bHQfxtOSHXZtcif', NULL, '182.9.1.50', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWGhWS1ByekY4MkFzeXRVZ2EwaVI5WWtTMmpSS21qY0RrSndCWXBKMCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vdG9rbzg1LmJpbWF0ZWNoLnNpdGUiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fX0=', 1767529104),
('xCzNmpxEWRGAoxZF8GxXZDkcyha7y0B1hkP90DsN', 2, '182.9.1.50', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUlJCVXRVQzk4aVprMUFEM0Q2UjJ3YzJ0ZEFZaFBtQVhlODdsbGxOUCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNDoiaHR0cHM6Ly90b2tvODUuYmltYXRlY2guc2l0ZS9hZG1pbiI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI4OiJodHRwczovL3Rva284NS5iaW1hdGVjaC5zaXRlIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1767531872),
('ycfqoPt9nJqCkn4LD8IcNogpAFiuWtWxr7Jr1bEb', 2, '182.9.1.50', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiN0RGOVBPRzJiazQ1M2ZqRmllejY5ajVmbUxSdzJ3SFBJMHV3Vk5vUyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNDoiaHR0cHM6Ly90b2tvODUuYmltYXRlY2guc2l0ZS9hZG1pbiI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI4OiJodHRwczovL3Rva284NS5iaW1hdGVjaC5zaXRlIjtzOjU6InJvdXRlIjtzOjQ6ImhvbWUiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1767532350);

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock_adjustments`
--

CREATE TABLE `stock_adjustments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `store_id` bigint(20) UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `adjustment_type` enum('add','remove') NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `stok_awal` decimal(10,2) DEFAULT 0.00,
  `stok_masuk` decimal(10,2) DEFAULT 0.00,
  `total_stok` decimal(10,2) DEFAULT 0.00,
  `unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `adjustment_date` date NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `stock_adjustments`
--

INSERT INTO `stock_adjustments` (`id`, `product_id`, `store_id`, `warehouse_id`, `adjustment_type`, `quantity`, `stok_awal`, `stok_masuk`, `total_stok`, `unit_id`, `reason`, `adjustment_date`, `user_id`, `created_at`, `updated_at`) VALUES
(449, 98, 1, NULL, 'add', 20.00, 0.00, 20.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 11:52:57', '2025-12-16 11:52:57'),
(450, 98, 1, NULL, 'add', 3.00, 0.00, 3.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 11:53:08', '2025-12-16 11:53:08'),
(451, 8, 1, NULL, 'add', 50.00, 0.00, 50.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 11:54:29', '2025-12-16 11:54:29'),
(452, 12, 1, NULL, 'add', 6.00, 0.00, 6.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 11:54:54', '2025-12-16 11:54:54'),
(453, 12, 1, NULL, 'add', 15.00, 0.00, 15.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 11:55:05', '2025-12-16 11:55:05'),
(454, 111, 1, NULL, 'add', 1.00, 0.00, 1.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 11:56:07', '2025-12-16 11:56:07'),
(455, 14, 1, NULL, 'add', 43.00, 0.00, 43.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:06:27', '2025-12-16 12:06:27'),
(456, 14, 1, NULL, 'add', 50.00, 0.00, 50.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:06:42', '2025-12-16 12:06:42'),
(457, 15, 1, NULL, 'add', 5.00, 0.00, 5.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:08:40', '2025-12-16 12:08:40'),
(458, 16, 1, NULL, 'add', 7.00, 0.00, 7.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:12:55', '2025-12-16 12:12:55'),
(459, 16, 1, NULL, 'add', 5.00, 0.00, 5.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:13:06', '2025-12-16 12:13:06'),
(460, 19, 1, NULL, 'add', 160.00, 0.00, 160.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:16:36', '2025-12-16 12:16:36'),
(461, 21, 1, NULL, 'add', 2.00, 0.00, 2.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:18:41', '2025-12-16 12:18:41'),
(462, 21, 1, NULL, 'add', 45.00, 0.00, 45.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:19:38', '2025-12-16 12:19:38'),
(463, 22, 1, NULL, 'add', 120.00, 0.00, 120.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:20:50', '2025-12-16 12:20:50'),
(464, 22, 1, NULL, 'add', 128.00, 0.00, 128.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:21:00', '2025-12-16 12:21:00'),
(465, 22, 1, NULL, 'add', 89.00, 0.00, 89.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:21:09', '2025-12-16 12:21:09'),
(466, 24, 1, NULL, 'add', 150.00, 0.00, 150.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:21:36', '2025-12-16 12:21:36'),
(467, 25, 1, NULL, 'add', 20.00, 0.00, 20.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:22:15', '2025-12-16 12:22:15'),
(468, 26, 1, NULL, 'add', 95.00, 0.00, 95.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:23:00', '2025-12-16 12:23:00'),
(469, 28, 1, NULL, 'add', 8.00, 0.00, 8.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:23:27', '2025-12-16 12:23:27'),
(470, 28, 1, NULL, 'add', 24.00, 0.00, 24.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:23:37', '2025-12-16 12:23:37'),
(471, 29, 1, NULL, 'add', 29.00, 0.00, 29.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:24:12', '2025-12-16 12:24:12'),
(472, 30, 1, NULL, 'add', 50.00, 0.00, 50.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:24:30', '2025-12-16 12:24:30'),
(473, 30, 1, NULL, 'add', 64.00, 0.00, 64.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:24:40', '2025-12-16 12:24:40'),
(474, 31, 1, NULL, 'add', 132.00, 0.00, 132.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:25:01', '2025-12-16 12:25:01'),
(475, 31, 1, NULL, 'add', 61.00, 0.00, 61.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:25:51', '2025-12-16 12:25:51'),
(476, 32, 1, NULL, 'add', 95.00, 0.00, 95.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:27:34', '2025-12-16 12:27:34'),
(477, 32, 1, NULL, 'add', 12.00, 0.00, 12.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:27:45', '2025-12-16 12:27:45'),
(478, 32, 1, NULL, 'add', 61.00, 0.00, 61.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:28:07', '2025-12-16 12:28:07'),
(479, 33, 1, NULL, 'add', 8.00, 0.00, 8.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:28:37', '2025-12-16 12:28:37'),
(480, 99, 1, NULL, 'add', 46.00, 0.00, 46.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:29:13', '2025-12-16 12:29:13'),
(481, 35, 1, NULL, 'add', 1.00, 0.00, 1.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:30:06', '2025-12-16 12:30:06'),
(482, 35, 1, NULL, 'add', 120.00, 0.00, 120.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:30:16', '2025-12-16 12:30:16'),
(483, 35, 1, NULL, 'add', 60.00, 0.00, 60.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:30:27', '2025-12-16 12:30:27'),
(484, 36, 1, NULL, 'add', 110.00, 0.00, 110.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:31:36', '2025-12-16 12:31:36'),
(485, 36, 1, NULL, 'add', 83.00, 0.00, 83.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:32:47', '2025-12-16 12:32:47'),
(486, 37, 1, NULL, 'add', 8.00, 0.00, 8.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:33:30', '2025-12-16 12:33:30'),
(487, 112, 1, NULL, 'add', 20.00, 0.00, 20.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:33:51', '2025-12-16 12:33:51'),
(488, 39, 1, NULL, 'add', 9.00, 0.00, 9.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:34:08', '2025-12-16 12:34:08'),
(489, 39, 1, NULL, 'add', 13.00, 0.00, 13.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:34:15', '2025-12-16 12:34:15'),
(490, 39, 1, NULL, 'add', 120.00, 0.00, 120.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:34:28', '2025-12-16 12:34:28'),
(491, 40, 1, NULL, 'add', 3.00, 0.00, 3.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:35:12', '2025-12-16 12:35:12'),
(492, 40, 1, NULL, 'add', 7.00, 0.00, 7.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:35:23', '2025-12-16 12:35:23'),
(493, 40, 1, NULL, 'add', 80.00, 0.00, 80.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:35:33', '2025-12-16 12:35:33'),
(494, 41, 1, NULL, 'add', 10.00, 0.00, 10.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:40:01', '2025-12-16 12:40:01'),
(495, 41, 1, NULL, 'add', 37.00, 0.00, 37.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:40:10', '2025-12-16 12:40:10'),
(496, 42, 1, NULL, 'add', 55.00, 0.00, 55.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:40:28', '2025-12-16 12:40:28'),
(497, 42, 1, NULL, 'add', 24.00, 0.00, 24.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:40:37', '2025-12-16 12:40:37'),
(498, 43, 1, NULL, 'add', 100.00, 0.00, 100.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:41:02', '2025-12-16 12:41:02'),
(499, 43, 1, NULL, 'add', 20.00, 0.00, 20.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:41:13', '2025-12-16 12:41:13'),
(500, 44, 1, NULL, 'add', 1.00, 0.00, 1.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:41:33', '2025-12-16 12:41:33'),
(501, 102, 1, NULL, 'add', 20.00, 0.00, 20.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:42:00', '2025-12-16 12:42:00'),
(502, 46, 1, NULL, 'add', 5.00, 0.00, 5.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:42:17', '2025-12-16 12:42:17'),
(503, 46, 1, NULL, 'add', 107.00, 0.00, 107.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:42:28', '2025-12-16 12:42:28'),
(504, 48, 1, NULL, 'add', 119.00, 0.00, 119.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:44:01', '2025-12-16 12:44:01'),
(506, 50, 1, NULL, 'add', 120.00, 0.00, 120.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:44:36', '2025-12-16 12:44:36'),
(507, 50, 1, NULL, 'add', 11.00, 0.00, 11.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:44:46', '2025-12-16 12:44:46'),
(508, 51, 1, NULL, 'add', 20.00, 0.00, 20.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:45:10', '2025-12-16 12:45:10'),
(509, 52, 1, NULL, 'add', 220.00, 0.00, 220.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:45:38', '2025-12-16 12:45:38'),
(510, 52, 1, NULL, 'add', 1.00, 0.00, 1.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 12:45:58', '2025-12-16 12:45:58'),
(511, 53, 1, NULL, 'add', 32.00, 0.00, 32.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:08:49', '2025-12-16 13:08:49'),
(513, 54, 1, NULL, 'add', 77.00, 0.00, 77.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:10:16', '2025-12-16 13:10:16'),
(514, 55, 1, NULL, 'add', 78.00, 0.00, 78.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:13:42', '2025-12-16 13:13:42'),
(515, 55, 1, NULL, 'add', 1.00, 0.00, 1.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:13:47', '2025-12-16 13:13:47'),
(516, 56, 1, NULL, 'add', 72.00, 0.00, 72.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:14:05', '2025-12-16 13:14:05'),
(517, 58, 1, NULL, 'add', 160.00, 0.00, 160.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:14:37', '2025-12-16 13:14:37'),
(518, 113, 1, NULL, 'add', 1.00, 0.00, 1.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:14:55', '2025-12-16 13:14:55'),
(519, 82, 1, NULL, 'add', 99.00, 0.00, 99.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:15:07', '2025-12-16 13:15:07'),
(521, 62, 1, NULL, 'add', 1.00, 0.00, 1.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:15:35', '2025-12-16 13:15:35'),
(522, 62, 1, NULL, 'add', 144.00, 0.00, 144.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:15:40', '2025-12-16 13:15:40'),
(523, 114, 1, NULL, 'add', 4.00, 0.00, 4.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:15:58', '2025-12-16 13:15:58'),
(524, 115, 1, NULL, 'add', 3.00, 0.00, 3.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:16:09', '2025-12-16 13:16:09'),
(525, 65, 1, NULL, 'add', 119.00, 0.00, 119.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:16:25', '2025-12-16 13:16:25'),
(526, 116, 1, NULL, 'add', 116.00, 0.00, 116.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:16:36', '2025-12-16 13:16:36'),
(527, 67, 1, NULL, 'add', 20.00, 0.00, 20.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:17:00', '2025-12-16 13:17:00'),
(528, 68, 1, NULL, 'add', 101.00, 0.00, 101.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:17:12', '2025-12-16 13:17:12'),
(529, 117, 1, NULL, 'add', 95.00, 0.00, 95.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:17:50', '2025-12-16 13:17:50'),
(530, 117, 1, NULL, 'add', 22.00, 0.00, 22.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:17:54', '2025-12-16 13:17:54'),
(531, 101, 1, NULL, 'add', 24.00, 0.00, 24.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:18:14', '2025-12-16 13:18:14'),
(532, 127, NULL, 1, 'add', 42.00, 0.00, 42.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:25:34', '2025-12-16 13:25:34'),
(534, 28, NULL, 1, 'add', 37.00, 0.00, 37.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:26:28', '2025-12-16 13:26:28'),
(536, 101, NULL, 1, 'add', 16.00, 0.00, 16.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:27:16', '2025-12-16 13:27:16'),
(537, 24, NULL, 1, 'add', 80.00, 0.00, 80.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:27:57', '2025-12-16 13:27:57'),
(538, 76, NULL, 1, 'add', 92.00, 0.00, 92.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:28:17', '2025-12-16 13:28:17'),
(539, 77, NULL, 1, 'add', 89.00, 0.00, 89.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:28:42', '2025-12-16 13:28:42'),
(540, 121, NULL, 1, 'add', 54.00, 0.00, 54.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:29:05', '2025-12-16 13:29:05'),
(541, 30, NULL, 1, 'add', 73.00, 0.00, 73.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:29:17', '2025-12-16 13:29:17'),
(542, 106, NULL, 1, 'add', 55.00, 0.00, 55.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:29:28', '2025-12-16 13:29:28'),
(543, 106, NULL, 1, 'add', 6.00, 0.00, 6.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:29:46', '2025-12-16 13:29:46'),
(544, 52, NULL, 1, 'add', 16.00, 0.00, 16.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:30:01', '2025-12-16 13:30:01'),
(546, 48, NULL, 1, 'add', 32.00, 0.00, 32.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:30:35', '2025-12-16 13:30:35'),
(547, 115, NULL, 1, 'add', 7.00, 0.00, 7.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:31:04', '2025-12-16 13:31:04'),
(548, 113, NULL, 1, 'add', 80.00, 0.00, 80.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:31:17', '2025-12-16 13:31:17'),
(549, 122, NULL, 1, 'add', 6.00, 0.00, 6.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:31:36', '2025-12-16 13:31:36'),
(550, 68, NULL, 1, 'add', 249.00, 0.00, 249.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:31:51', '2025-12-16 13:31:51'),
(551, 120, NULL, 1, 'add', 37.00, 37.00, 0.00, 0.00, 7, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:32:15', '2025-12-18 03:47:32'),
(552, 124, NULL, 1, 'add', 60.00, 0.00, 60.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:32:32', '2025-12-16 13:32:32'),
(553, 25, NULL, 1, 'add', 44.00, 0.00, 44.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:32:48', '2025-12-16 13:32:48'),
(554, 36, NULL, 1, 'add', 34.00, 0.00, 34.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:33:01', '2025-12-16 13:33:01'),
(555, 104, NULL, 1, 'add', 6.00, 0.00, 6.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:33:12', '2025-12-16 13:33:12'),
(556, 125, NULL, 1, 'add', 2.00, 0.00, 2.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:33:22', '2025-12-16 13:33:22'),
(557, 45, NULL, 1, 'add', 120.00, 0.00, 120.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:33:31', '2025-12-16 13:33:31'),
(558, 126, NULL, 1, 'add', 232.00, 0.00, 232.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:33:47', '2025-12-16 13:33:47'),
(559, 100, NULL, 1, 'add', 66.00, 0.00, 66.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:34:01', '2025-12-16 13:34:01'),
(560, 67, NULL, 1, 'add', 10.00, 0.00, 10.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:34:17', '2025-12-16 13:34:17'),
(561, 9, 1, NULL, 'add', 4.00, 0.00, 4.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:42:32', '2025-12-16 13:42:32'),
(562, 9, 1, NULL, 'add', 25.00, 0.00, 25.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:42:47', '2025-12-16 13:42:47'),
(563, 10, 1, NULL, 'add', 3.00, 0.00, 3.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 13:46:15', '2025-12-16 13:46:15'),
(564, 73, 1, NULL, 'add', 1.00, 0.00, 1.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 14:42:51', '2025-12-16 14:42:51'),
(565, 71, 1, NULL, 'add', 79.00, 0.00, 79.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2025-12-16 14:53:37', '2025-12-16 14:53:37'),
(584, 2, 1, NULL, 'add', 16.00, 0.00, 16.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-03 09:26:50', '2026-01-03 09:26:50'),
(587, 1, 1, NULL, 'add', 169.00, 0.00, 169.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 03:45:58', '2026-01-04 03:45:58'),
(588, 1, 1, NULL, 'add', 3.00, 0.00, 3.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 03:45:58', '2026-01-04 03:45:58'),
(589, 191, 1, NULL, 'add', 22.00, 0.00, 22.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 03:47:50', '2026-01-04 03:47:50'),
(590, 192, 1, NULL, 'add', 38.00, 0.00, 38.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 03:48:58', '2026-01-04 03:48:58'),
(591, 4, 1, NULL, 'add', 60.00, 0.00, 60.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 03:49:52', '2026-01-04 03:49:52'),
(592, 6, 1, NULL, 'add', 29.00, 0.00, 29.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 03:50:28', '2026-01-04 03:50:28'),
(593, 6, NULL, 1, 'add', 67.00, 0.00, 67.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 03:51:25', '2026-01-04 03:51:25'),
(594, 1, NULL, 1, 'add', 116.00, 0.00, 116.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 03:53:52', '2026-01-04 03:53:52'),
(595, 192, NULL, 1, 'add', 1.00, 0.00, 1.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 03:54:36', '2026-01-04 03:54:36'),
(596, 96, 1, NULL, 'add', 38.00, 0.00, 38.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 04:19:51', '2026-01-04 04:19:51'),
(597, 193, 1, NULL, 'add', 3.00, 0.00, 3.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 04:21:44', '2026-01-04 04:21:44'),
(598, 193, 1, NULL, 'add', 1.00, 0.00, 1.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 04:21:44', '2026-01-04 04:21:44'),
(599, 193, 1, NULL, 'add', 13.00, 0.00, 13.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 04:21:44', '2026-01-04 04:21:44'),
(600, 193, 1, NULL, 'add', 80.00, 0.00, 80.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 04:21:44', '2026-01-04 04:21:44'),
(602, 22, 1, NULL, 'add', 4.00, 0.00, 4.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 05:22:33', '2026-01-04 05:22:33'),
(603, 87, 1, NULL, 'add', 994.00, 0.00, 994.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 07:34:06', '2026-01-04 07:34:06'),
(604, 87, 1, NULL, 'add', 4.00, 0.00, 4.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 07:34:06', '2026-01-04 07:34:06'),
(605, 194, 1, NULL, 'add', 24.00, 0.00, 24.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 07:36:48', '2026-01-04 07:36:48'),
(606, 194, 1, NULL, 'add', 11.00, 0.00, 11.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 07:36:49', '2026-01-04 07:36:49'),
(607, 90, 1, NULL, 'add', 12.00, 0.00, 12.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 07:37:58', '2026-01-04 07:37:58'),
(608, 195, 1, NULL, 'add', 8.00, 0.00, 8.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 07:40:18', '2026-01-04 07:40:18'),
(609, 195, 1, NULL, 'add', 92.00, 0.00, 92.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-04 07:40:18', '2026-01-04 07:40:18'),
(611, 198, 1, NULL, 'add', 57.00, 0.00, 57.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-06 02:43:25', '2026-01-06 02:43:25'),
(612, 92, 1, NULL, 'add', 40.00, 0.00, 40.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-06 02:44:17', '2026-01-06 02:44:17'),
(613, 93, 1, NULL, 'add', 2.00, 0.00, 2.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-06 02:45:02', '2026-01-06 02:45:02'),
(614, 87, NULL, 1, 'add', 232.00, 0.00, 232.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-06 02:45:52', '2026-01-06 02:45:52'),
(615, 89, NULL, 1, 'add', 79.00, 0.00, 79.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-06 02:46:40', '2026-01-06 02:46:40'),
(616, 89, NULL, 1, 'add', 22.00, 0.00, 22.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-06 02:47:57', '2026-01-06 02:47:57'),
(617, 92, NULL, 1, 'add', 3.00, 0.00, 3.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-06 02:48:38', '2026-01-06 02:48:38'),
(618, 22, NULL, 1, 'add', 54.00, 0.00, 54.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-06 02:49:09', '2026-01-06 02:49:09'),
(619, 193, NULL, 1, 'add', 63.00, 0.00, 63.00, 0.00, NULL, 'Penambahan stok dari tumpukan', '2025-12-05', 2, '2026-01-06 02:49:52', '2026-01-06 02:49:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock_batches`
--

CREATE TABLE `stock_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subcategory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `location_type` varchar(255) DEFAULT NULL,
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nama_tumpukan` varchar(255) NOT NULL DEFAULT 'Tumpukan',
  `qty` decimal(10,2) NOT NULL DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `status` enum('aktual','hold') NOT NULL DEFAULT 'aktual',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `stock_batches`
--

INSERT INTO `stock_batches` (`id`, `product_id`, `category_id`, `subcategory_id`, `location_type`, `location_id`, `nama_tumpukan`, `qty`, `note`, `status`, `created_at`, `updated_at`) VALUES
(479, 98, NULL, NULL, 'store', 1, 'T1', 20.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(480, 98, NULL, NULL, 'store', 1, 'T2', 3.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(481, 8, NULL, NULL, 'store', 1, 'T1', 50.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(482, 12, NULL, NULL, 'store', 1, 'T1', 6.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(483, 12, NULL, NULL, 'store', 1, 'T2', 15.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(484, 111, NULL, NULL, 'store', 1, 'T1', 1.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(485, 14, NULL, NULL, 'store', 1, 'T1', 43.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(486, 14, NULL, NULL, 'store', 1, 'T2', 50.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(487, 15, NULL, NULL, 'store', 1, 'T1', 5.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(488, 16, NULL, NULL, 'store', 1, 'T1', 7.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(489, 16, NULL, NULL, 'store', 1, 'T2', 5.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(490, 19, 1, 2, 'store', 1, 'T1', 160.00, '', 'aktual', '2025-12-04 17:00:00', '2026-01-04 12:18:49'),
(491, 21, NULL, NULL, 'store', 1, 'T1', 2.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(492, 21, NULL, NULL, 'store', 1, 'T2', 45.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(493, 22, NULL, NULL, 'store', 1, 'T1', 120.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(494, 22, NULL, NULL, 'store', 1, 'T2', 128.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(495, 22, NULL, NULL, 'store', 1, 'T3', 89.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(496, 24, NULL, NULL, 'store', 1, 'T1', 150.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(497, 25, NULL, NULL, 'store', 1, 'T1', 20.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(498, 26, NULL, NULL, 'store', 1, 'T1', 95.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(499, 28, NULL, NULL, 'store', 1, 'T1', 8.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(500, 28, NULL, NULL, 'store', 1, 'T2', 24.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(501, 29, NULL, NULL, 'store', 1, 'T1', 29.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(502, 30, NULL, NULL, 'store', 1, 'T1', 50.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(503, 30, NULL, NULL, 'store', 1, 'T2', 64.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(504, 31, NULL, NULL, 'store', 1, 'T1', 132.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(505, 31, NULL, NULL, 'store', 1, 'T2', 61.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(506, 32, NULL, NULL, 'store', 1, 'T1', 95.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(507, 32, NULL, NULL, 'store', 1, 'T2', 12.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(508, 32, NULL, NULL, 'store', 1, 'T3', 6.00, '', 'aktual', '2025-12-04 17:00:00', '2025-12-16 14:13:45'),
(509, 33, NULL, NULL, 'store', 1, 'T1', 8.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(510, 99, NULL, NULL, 'store', 1, 'T1', 46.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(511, 35, NULL, NULL, 'store', 1, 'T1', 1.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(512, 35, NULL, NULL, 'store', 1, 'T2', 120.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(513, 35, NULL, NULL, 'store', 1, 'T3', 60.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(514, 36, NULL, NULL, 'store', 1, 'T1', 110.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(515, 36, NULL, NULL, 'store', 1, 'T2', 83.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(516, 37, NULL, NULL, 'store', 1, 'T1', 8.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(517, 112, NULL, NULL, 'store', 1, 'T1', 20.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(518, 39, NULL, NULL, 'store', 1, 'T1', 9.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(519, 39, NULL, NULL, 'store', 1, 'T2', 13.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(520, 39, NULL, NULL, 'store', 1, 'T3', 120.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(521, 40, NULL, NULL, 'store', 1, 'T1', 3.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(522, 40, NULL, NULL, 'store', 1, 'T2', 7.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(523, 40, NULL, NULL, 'store', 1, 'T3', 80.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(524, 41, NULL, NULL, 'store', 1, 'T1', 10.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(525, 41, NULL, NULL, 'store', 1, 'T2', 37.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(526, 42, NULL, NULL, 'store', 1, 'T1', 55.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(527, 42, NULL, NULL, 'store', 1, 'T2', 24.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(528, 43, NULL, NULL, 'store', 1, 'T1', 100.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(529, 43, NULL, NULL, 'store', 1, 'T2', 20.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(530, 44, NULL, NULL, 'store', 1, 'T1', 1.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(531, 102, NULL, NULL, 'store', 1, 'T1', 20.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(532, 46, NULL, NULL, 'store', 1, 'T1', 5.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(533, 46, NULL, NULL, 'store', 1, 'T2', 107.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(534, 48, NULL, NULL, 'store', 1, 'T1', 119.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(536, 50, NULL, NULL, 'store', 1, 'T1', 120.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(537, 50, NULL, NULL, 'store', 1, 'T2', 11.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(538, 51, NULL, NULL, 'store', 1, 'T1', 20.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(539, 52, NULL, NULL, 'store', 1, 'T1', 220.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(540, 52, NULL, NULL, 'store', 1, 'T2', 1.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(541, 53, NULL, NULL, 'store', 1, 'T1', 32.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(543, 54, NULL, NULL, 'store', 1, 'T2', 77.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(544, 55, NULL, NULL, 'store', 1, 'T1', 78.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(545, 55, NULL, NULL, 'store', 1, 'T2', 1.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(546, 56, NULL, NULL, 'store', 1, 'T1', 72.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(547, 58, NULL, NULL, 'store', 1, 'T1', 160.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(548, 113, NULL, NULL, 'store', 1, 'T1', 1.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(549, 82, NULL, NULL, 'store', 1, 'T1', 99.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(551, 62, NULL, NULL, 'store', 1, 'T1', 1.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(552, 62, NULL, NULL, 'store', 1, 'T2', 144.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(553, 114, NULL, NULL, 'store', 1, 'T1', 4.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(554, 115, NULL, NULL, 'store', 1, 'T1', 3.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(555, 65, NULL, NULL, 'store', 1, 'T1', 119.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(556, 116, NULL, NULL, 'store', 1, 'T1', 116.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(557, 67, NULL, NULL, 'store', 1, 'T1', 30.00, '', 'aktual', '2025-12-04 17:00:00', '2025-12-16 14:58:22'),
(558, 68, NULL, NULL, 'store', 1, 'T1', 101.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(559, 117, NULL, NULL, 'store', 1, 'T1', 95.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(560, 117, NULL, NULL, 'store', 1, 'T2', 22.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(561, 101, NULL, NULL, 'store', 1, 'T1', 24.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(562, 127, NULL, NULL, 'warehouse', 1, 'T1', 42.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(563, 120, NULL, NULL, 'warehouse', 1, 'T1', 37.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(564, 28, NULL, NULL, 'warehouse', 1, 'T1', 37.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(566, 101, NULL, NULL, 'warehouse', 1, 'T1', 16.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(567, 24, NULL, NULL, 'warehouse', 1, 'T1', 80.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(568, 76, NULL, NULL, 'warehouse', 1, 'T1', 92.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(569, 77, NULL, NULL, 'warehouse', 1, 'T1', 89.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(570, 121, NULL, NULL, 'warehouse', 1, 'T1', 54.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(571, 30, NULL, NULL, 'warehouse', 1, 'T1', 73.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(572, 106, NULL, NULL, 'warehouse', 1, 'T1', 55.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(573, 106, NULL, NULL, 'warehouse', 1, 'T1', 6.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(574, 52, NULL, NULL, 'warehouse', 1, 'T1', 16.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(576, 48, NULL, NULL, 'warehouse', 1, 'T1', 32.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(577, 115, NULL, NULL, 'warehouse', 1, 'T1', 7.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(578, 113, NULL, NULL, 'warehouse', 1, 'T1', 80.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(579, 122, NULL, NULL, 'warehouse', 1, 'T1', 6.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(580, 68, NULL, NULL, 'warehouse', 1, 'T1', 249.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(582, 124, NULL, NULL, 'warehouse', 1, 'T1', 60.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(583, 25, NULL, NULL, 'warehouse', 1, 'T1', 44.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(584, 36, NULL, NULL, 'warehouse', 1, 'T1', 34.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(585, 104, NULL, NULL, 'warehouse', 1, 'T1', 6.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(586, 125, NULL, NULL, 'warehouse', 1, 'T1', 2.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(587, 45, NULL, NULL, 'warehouse', 1, 'T1', 120.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(588, 126, NULL, NULL, 'warehouse', 1, 'T1', 232.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(589, 100, NULL, NULL, 'warehouse', 1, 'T1', 66.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(590, 67, NULL, NULL, 'warehouse', 1, 'T1', 10.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(591, 9, NULL, NULL, 'store', 1, 'T1', 4.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(592, 9, NULL, NULL, 'store', 1, 'T2', 25.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(593, 10, NULL, NULL, 'store', 1, 'T1', 3.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(594, 73, NULL, NULL, 'store', 1, 'T1', 1.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(595, 71, NULL, NULL, 'store', 1, 'T1', 79.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(598, 123, NULL, NULL, 'warehouse', 1, 'T1', 59.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(599, 120, NULL, NULL, 'warehouse', 1, 'T1', 20.00, 'belum di ambil sama pelanggan', 'hold', '2025-12-18 04:08:30', '2025-12-18 04:08:30'),
(600, 123, NULL, NULL, 'warehouse', 1, 'T1', 14.00, 'tapi belum diambil', 'hold', '2025-12-18 04:10:20', '2025-12-18 04:10:20'),
(613, 2, NULL, NULL, 'store', 1, 'T1', 16.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(616, 1, NULL, NULL, 'store', 1, 'T1', 169.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(617, 1, NULL, NULL, 'store', 1, 'T2', 3.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(618, 191, NULL, NULL, 'store', 1, 'Tumpukan 1', 22.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(619, 192, NULL, NULL, 'store', 1, 'Tumpukan 2', 38.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(620, 4, NULL, NULL, 'store', 1, 'Tumpukan 3', 60.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(621, 6, NULL, NULL, 'store', 1, 'T1', 29.00, '', 'aktual', '2025-12-04 17:00:00', '2026-01-04 03:52:19'),
(622, 6, NULL, NULL, 'warehouse', 1, 'T1', 67.00, '', 'aktual', '2025-12-04 17:00:00', '2026-01-04 03:52:32'),
(623, 1, NULL, NULL, 'warehouse', 1, 'T1', 116.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(624, 192, NULL, NULL, 'warehouse', 1, 'T1', 1.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(625, 96, NULL, NULL, 'store', 1, 'T1', 38.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(626, 193, NULL, NULL, 'store', 1, 'T1', 3.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(627, 193, NULL, NULL, 'store', 1, 'T2', 1.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(628, 193, NULL, NULL, 'store', 1, 'T3', 13.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(629, 193, NULL, NULL, 'store', 1, 'T4', 80.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(631, 22, NULL, NULL, 'store', 1, 'T1', 4.00, '', 'aktual', '2025-12-04 17:00:00', '2026-01-04 05:28:35'),
(632, 87, NULL, NULL, 'store', 1, 'T1', 994.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(633, 87, NULL, NULL, 'store', 1, 'T2', 4.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(634, 194, NULL, NULL, 'store', 1, 'T1', 24.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(635, 194, NULL, NULL, 'store', 1, 'T2', 11.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(636, 90, NULL, NULL, 'store', 1, 'T1', 12.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(637, 195, NULL, NULL, 'store', 1, 'T1', 8.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(638, 195, NULL, NULL, 'store', 1, 'T2', 92.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(640, 198, NULL, NULL, 'store', 1, 'T1', 57.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(641, 92, NULL, NULL, 'store', 1, 'T1', 40.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(642, 93, NULL, NULL, 'store', 1, 'T1', 2.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(643, 87, NULL, NULL, 'warehouse', 1, 'T1', 232.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(644, 89, NULL, NULL, 'warehouse', 1, 'T1', 79.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(645, 89, NULL, NULL, 'warehouse', 1, 'T1', 22.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(646, 92, NULL, NULL, 'warehouse', 1, 'T1', 3.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(647, 22, NULL, NULL, 'warehouse', 1, 'T1', 54.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(648, 193, NULL, NULL, 'warehouse', 1, 'T1', 63.00, NULL, 'aktual', '2025-12-04 17:00:00', '2025-12-04 17:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock_cards`
--

CREATE TABLE `stock_cards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('in','out','adjustment','move','hold','cancel_hold','sale') NOT NULL,
  `qty` decimal(10,2) NOT NULL,
  `from_location` varchar(255) DEFAULT NULL COMMENT 'Lokasi asal (Supplier, Toko, Gudang, Customer)',
  `to_location` varchar(255) DEFAULT NULL COMMENT 'Lokasi tujuan (Toko, Gudang, Customer)',
  `note` text DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `reference_type` varchar(255) DEFAULT NULL COMMENT 'Tipe referensi: purchase, sale, adjustment',
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID referensi (purchase_id, sale_id, etc)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `stock_cards`
--

INSERT INTO `stock_cards` (`id`, `stock_batch_id`, `product_id`, `batch_id`, `type`, `qty`, `from_location`, `to_location`, `note`, `reference`, `reference_type`, `reference_id`, `created_at`, `updated_at`) VALUES
(203, NULL, 98, 479, 'in', 20.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 479, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(204, NULL, 98, 480, 'in', 3.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 480, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(205, NULL, 8, 481, 'in', 50.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 481, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(206, NULL, 12, 482, 'in', 6.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 482, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(207, NULL, 12, 483, 'in', 15.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 483, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(208, NULL, 111, 484, 'in', 1.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 484, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(209, NULL, 14, 485, 'in', 43.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 485, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(210, NULL, 14, 486, 'in', 50.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 486, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(211, NULL, 15, 487, 'in', 5.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 487, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(212, NULL, 16, 488, 'in', 7.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 488, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(213, NULL, 16, 489, 'in', 5.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 489, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(214, NULL, 19, 490, 'in', 160.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 490, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(215, NULL, 21, 491, 'in', 2.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 491, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(216, NULL, 21, 492, 'in', 45.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 492, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(217, NULL, 22, 493, 'in', 120.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 493, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(218, NULL, 22, 494, 'in', 128.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 494, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(219, NULL, 22, 495, 'in', 89.00, NULL, 'Toko #1', 'Penambahan stok: T3', NULL, 'stock_batch', 495, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(220, NULL, 24, 496, 'in', 150.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 496, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(221, NULL, 25, 497, 'in', 20.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 497, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(222, NULL, 26, 498, 'in', 95.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 498, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(223, NULL, 28, 499, 'in', 8.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 499, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(224, NULL, 28, 500, 'in', 24.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 500, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(225, NULL, 29, 501, 'in', 29.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 501, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(226, NULL, 30, 502, 'in', 50.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 502, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(227, NULL, 30, 503, 'in', 64.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 503, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(228, NULL, 31, 504, 'in', 132.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 504, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(229, NULL, 31, 505, 'in', 61.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 505, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(230, NULL, 32, 506, 'in', 95.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 506, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(231, NULL, 32, 507, 'in', 12.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 507, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(232, NULL, 32, 508, 'in', 6.00, NULL, 'Toko #1', 'Penambahan stok: T3', NULL, 'stock_batch', 508, '2025-12-04 17:00:00', '2025-12-16 14:28:16'),
(233, NULL, 33, 509, 'in', 8.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 509, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(234, NULL, 99, 510, 'in', 46.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 510, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(235, NULL, 35, 511, 'in', 1.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 511, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(236, NULL, 35, 512, 'in', 120.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 512, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(237, NULL, 35, 513, 'in', 60.00, NULL, 'Toko #1', 'Penambahan stok: T3', NULL, 'stock_batch', 513, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(238, NULL, 36, 514, 'in', 110.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 514, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(239, NULL, 36, 515, 'in', 83.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 515, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(240, NULL, 37, 516, 'in', 8.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 516, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(241, NULL, 112, 517, 'in', 20.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 517, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(242, NULL, 39, 518, 'in', 9.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 518, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(243, NULL, 39, 519, 'in', 13.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 519, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(244, NULL, 39, 520, 'in', 120.00, NULL, 'Toko #1', 'Penambahan stok: T3', NULL, 'stock_batch', 520, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(245, NULL, 40, 521, 'in', 3.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 521, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(246, NULL, 40, 522, 'in', 7.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 522, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(247, NULL, 40, 523, 'in', 80.00, NULL, 'Toko #1', 'Penambahan stok: T3', NULL, 'stock_batch', 523, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(248, NULL, 41, 524, 'in', 10.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 524, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(249, NULL, 41, 525, 'in', 37.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 525, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(250, NULL, 42, 526, 'in', 55.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 526, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(251, NULL, 42, 527, 'in', 24.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 527, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(252, NULL, 43, 528, 'in', 100.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 528, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(253, NULL, 43, 529, 'in', 20.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 529, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(254, NULL, 44, 530, 'in', 1.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 530, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(255, NULL, 102, 531, 'in', 20.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 531, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(256, NULL, 46, 532, 'in', 5.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 532, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(257, NULL, 46, 533, 'in', 107.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 533, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(258, NULL, 48, 534, 'in', 119.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 534, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(260, NULL, 50, 536, 'in', 120.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 536, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(261, NULL, 50, 537, 'in', 11.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 537, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(262, NULL, 51, 538, 'in', 20.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 538, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(263, NULL, 52, 539, 'in', 220.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 539, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(264, NULL, 52, 540, 'in', 1.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 540, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(265, NULL, 53, 541, 'in', 32.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 541, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(267, NULL, 54, 543, 'in', 77.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 543, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(268, NULL, 55, 544, 'in', 78.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 544, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(269, NULL, 55, 545, 'in', 1.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 545, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(270, NULL, 56, 546, 'in', 72.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 546, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(271, NULL, 58, 547, 'in', 160.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 547, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(272, NULL, 113, 548, 'in', 1.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 548, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(273, NULL, 82, 549, 'in', 99.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 549, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(275, NULL, 62, 551, 'in', 1.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 551, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(276, NULL, 62, 552, 'in', 144.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 552, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(277, NULL, 114, 553, 'in', 4.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 553, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(278, NULL, 115, 554, 'in', 3.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 554, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(279, NULL, 65, 555, 'in', 119.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 555, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(280, NULL, 116, 556, 'in', 116.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 556, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(281, NULL, 67, 557, 'in', 20.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 557, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(282, NULL, 68, 558, 'in', 101.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 558, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(283, NULL, 117, 559, 'in', 95.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 559, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(284, NULL, 117, 560, 'in', 22.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 560, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(285, NULL, 101, 561, 'in', 24.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 561, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(286, NULL, 127, 562, 'in', 42.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 562, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(287, NULL, 120, 563, 'in', 37.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 563, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(288, NULL, 28, 564, 'in', 37.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 564, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(290, NULL, 101, 566, 'in', 16.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 566, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(291, NULL, 24, 567, 'in', 80.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 567, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(292, NULL, 76, 568, 'in', 92.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 568, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(293, NULL, 77, 569, 'in', 89.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 569, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(294, NULL, 121, 570, 'in', 54.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 570, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(295, NULL, 30, 571, 'in', 73.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 571, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(296, NULL, 106, 572, 'in', 55.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 572, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(297, NULL, 106, 573, 'in', 6.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 573, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(298, NULL, 52, 574, 'in', 16.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 574, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(300, NULL, 48, 576, 'in', 32.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 576, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(301, NULL, 115, 577, 'in', 7.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 577, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(302, NULL, 113, 578, 'in', 80.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 578, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(303, NULL, 122, 579, 'in', 6.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 579, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(304, NULL, 68, 580, 'in', 249.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 580, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(306, NULL, 124, 582, 'in', 60.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 582, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(307, NULL, 25, 583, 'in', 44.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 583, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(308, NULL, 36, 584, 'in', 34.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 584, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(309, NULL, 104, 585, 'in', 6.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 585, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(310, NULL, 125, 586, 'in', 2.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 586, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(311, NULL, 45, 587, 'in', 120.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 587, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(312, NULL, 126, 588, 'in', 232.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 588, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(313, NULL, 100, 589, 'in', 66.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 589, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(314, NULL, 67, 590, 'in', 10.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 590, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(315, NULL, 9, 591, 'in', 4.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 591, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(316, NULL, 9, 592, 'in', 25.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 592, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(317, NULL, 10, 593, 'in', 3.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 593, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(318, NULL, 73, 594, 'in', 1.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 594, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(319, NULL, 71, 595, 'in', 79.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 595, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(320, NULL, 120, NULL, 'hold', 20.00, NULL, 'Gudang #1', 'Tumpukan hold dibuat: menunggu konfirmasi lebih lanjut', NULL, 'manual_hold', 597, '2025-12-17 01:38:09', '2025-12-17 01:38:09'),
(321, NULL, 123, 598, 'in', 59.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 598, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(322, NULL, 120, 599, 'hold', 20.00, NULL, 'Gudang #1', 'Tumpukan hold dibuat: hold sudah di bayar ', NULL, 'manual_hold', 599, '2025-12-18 04:08:30', '2025-12-18 04:08:30'),
(323, NULL, 123, 600, 'hold', 14.00, NULL, 'Gudang #1', 'Tumpukan hold dibuat: sudah laku', NULL, 'manual_hold', 600, '2025-12-18 04:10:20', '2025-12-18 04:10:20'),
(346, NULL, 2, 613, 'in', 16.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 613, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(349, NULL, 1, 616, 'in', 169.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 616, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(350, NULL, 1, 617, 'in', 3.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 617, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(351, NULL, 191, 618, 'in', 22.00, NULL, 'Toko #1', 'Penambahan stok: Tumpukan 1', NULL, 'stock_batch', 618, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(352, NULL, 192, 619, 'in', 38.00, NULL, 'Toko #1', 'Penambahan stok: Tumpukan 2', NULL, 'stock_batch', 619, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(353, NULL, 4, 620, 'in', 60.00, NULL, 'Toko #1', 'Penambahan stok: Tumpukan 3', NULL, 'stock_batch', 620, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(354, NULL, 6, 621, 'in', 29.00, NULL, 'Toko #1', 'Penambahan stok: Tumpukan 4', NULL, 'stock_batch', 621, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(355, NULL, 6, 622, 'in', 67.00, NULL, 'Gudang #1', 'Penambahan stok: Tumpukan 5', NULL, 'stock_batch', 622, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(356, NULL, 1, 623, 'in', 116.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 623, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(357, NULL, 192, 624, 'in', 1.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 624, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(358, NULL, 96, 625, 'in', 38.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 625, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(359, NULL, 193, 626, 'in', 3.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 626, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(360, NULL, 193, 627, 'in', 1.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 627, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(361, NULL, 193, 628, 'in', 13.00, NULL, 'Toko #1', 'Penambahan stok: T3', NULL, 'stock_batch', 628, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(362, NULL, 193, 629, 'in', 80.00, NULL, 'Toko #1', 'Penambahan stok: T4', NULL, 'stock_batch', 629, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(364, NULL, 22, 631, 'in', 4.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 631, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(365, NULL, 87, 632, 'in', 994.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 632, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(366, NULL, 87, 633, 'in', 4.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 633, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(367, NULL, 194, 634, 'in', 24.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 634, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(368, NULL, 194, 635, 'in', 11.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 635, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(369, NULL, 90, 636, 'in', 12.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 636, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(370, NULL, 195, 637, 'in', 8.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 637, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(371, NULL, 195, 638, 'in', 92.00, NULL, 'Toko #1', 'Penambahan stok: T2', NULL, 'stock_batch', 638, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(373, NULL, 198, 640, 'in', 57.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 640, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(374, NULL, 92, 641, 'in', 40.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 641, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(375, NULL, 93, 642, 'in', 2.00, NULL, 'Toko #1', 'Penambahan stok: T1', NULL, 'stock_batch', 642, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(376, NULL, 87, 643, 'in', 232.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 643, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(377, NULL, 89, 644, 'in', 79.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 644, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(378, NULL, 89, 645, 'in', 22.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 645, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(379, NULL, 92, 646, 'in', 3.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 646, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(380, NULL, 22, 647, 'in', 54.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 647, '2025-12-04 17:00:00', '2025-12-04 17:00:00'),
(381, NULL, 193, 648, 'in', 63.00, NULL, 'Gudang #1', 'Penambahan stok: T1', NULL, 'stock_batch', 648, '2025-12-04 17:00:00', '2025-12-04 17:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stores`
--

CREATE TABLE `stores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_toko` varchar(50) NOT NULL,
  `nama_toko` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `tipe_toko` enum('retail','wholesale','online','outlet') NOT NULL DEFAULT 'retail',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `stores`
--

INSERT INTO `stores` (`id`, `kode_toko`, `nama_toko`, `alamat`, `telepon`, `pic`, `tipe_toko`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'TP', 'Toko Pusat', 'Jl. Merdeka No. 1', NULL, NULL, 'retail', NULL, '2025-11-01 14:08:59', '2025-11-01 14:08:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `subcategories`
--

CREATE TABLE `subcategories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_subkategori` varchar(255) NOT NULL,
  `nama_subkategori` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `subcategories`
--

INSERT INTO `subcategories` (`id`, `kode_subkategori`, `nama_subkategori`, `description`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 'MT', 'MENTIK', 'Subkategori MENTIK untuk beras', 1, '2025-11-01 14:08:59', '2025-11-01 14:08:59'),
(2, 'C4', 'C4', 'Subkategori C4 untuk beras', 1, '2025-11-01 14:22:06', '2025-11-01 14:22:06'),
(3, 'KT', 'KETAN', 'Subkategori KETAN untuk beras', 1, '2025-11-01 14:22:48', '2025-11-01 14:22:48'),
(4, 'ROJOLE-CQ4B', 'Rojolele', 'Rojolele', 1, '2025-12-22 04:22:18', '2025-12-22 04:22:18'),
(5, 'WANGI-PM1A', 'Wangi', 'Wangi', 1, '2025-12-22 04:22:18', '2025-12-22 04:22:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_supplier` varchar(50) NOT NULL,
  `nama_supplier` varchar(255) NOT NULL,
  `owner` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `suppliers`
--

INSERT INTO `suppliers` (`id`, `kode_supplier`, `nama_supplier`, `owner`, `alamat`, `telepon`, `email`, `keterangan`, `created_at`, `updated_at`) VALUES
(48, 'SUP-10NL1V', 'PB Jaya Abadi - Karang Pandan', 'Mbak Dwi Suyatmi Athaya, Mas Harsono', NULL, NULL, NULL, 'Owner: Mbak Dwi Suyatmi Athaya, Mas Harsono', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(49, 'SUP-HQBNVW', 'CV Cakra Adhistara Sejahtera - Tasikmadu', 'Mbak Ayu Sigit Aribowo', NULL, NULL, NULL, 'Owner: Mbak Ayu Sigit Aribowo', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(50, 'SUP-CXII9J', 'PB Sumber Rejeki - Karang Anyar', 'Pak Sapto Giri (Aufa)', NULL, NULL, NULL, 'Owner: Pak Sapto Giri (Aufa)', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(51, 'SUP-BOOBU7', 'PB Gatot Gondangmanis - Dompyong Karang Pandan', 'Bu Gatot Sumarni', NULL, NULL, NULL, 'Owner: Bu Gatot Sumarni', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(52, 'SUP-YDV1NN', 'CV Fortuna (PB Makmur Jaya) Sragen', 'Bu Winarti, Dr Puji Setiawan', NULL, NULL, NULL, 'Owner: Bu Winarti, Dr Puji Setiawan', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(53, 'SUP-OVNV0H', 'PB Mitra Tani Karang Pandan', 'Eko Wahyono Remin', NULL, NULL, NULL, 'Owner: Eko Wahyono Remin', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(54, 'SUP-ZRMMPB', 'HMI (Himawari Group) Sragen', 'Setyo Bayu Haji', NULL, NULL, NULL, 'Owner: Setyo Bayu Haji', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(55, 'SUP-WQXFGQ', 'PB Dewi Ayu Ngrawoh Matesih', 'Pak Wardi, Bu Tri Warsini', NULL, NULL, NULL, 'Owner: Pak Wardi, Bu Tri Warsini', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(56, 'SUP-FO6EUN', 'PB Sapenkec Mojolaban', 'Sarwanto Ngiri', NULL, NULL, NULL, 'Owner: Sarwanto Ngiri', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(57, 'SUP-EQ6DNM', 'PB Ladang Mas Sukorejo Sragen', 'Ambar Nguwer', NULL, NULL, NULL, 'Owner: Ambar Nguwer', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(58, 'SUP-JJ1IF4', 'PB Mega Perkasa Sragen', 'Mas Eksan, Mas Daryono', NULL, NULL, NULL, 'Owner: Mas Eksan, Mas Daryono', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(59, 'SUP-N9UQ0G', 'PB Salmaira Matesih Karang Anyar', 'Pak Parwitto, Mbak Nanik', NULL, NULL, NULL, 'Owner: Pak Parwitto, Mbak Nanik', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(60, 'SUP-Y8TIWP', 'PB Citra Abadi Bermartabat Mojogedang', 'Pak Joko CAB', NULL, NULL, NULL, 'Owner: Pak Joko CAB', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(61, 'SUP-PCGDOS', 'PB Ragil Nguripi Sragen', 'Pak Maryadi', NULL, NULL, NULL, 'Owner: Pak Maryadi', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(62, 'SUP-YPJUJK', 'PB Trisno Makmur Matesih Karang Pandan', 'Mbak Eki Mulyani, Pak Sutrisno', NULL, NULL, NULL, 'Owner: Mbak Eki Mulyani, Pak Sutrisno', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(63, 'SUP-TWWDLU', 'PB Gantari Karang Anyar', 'Pak Darto, Yeni Pebriana', NULL, NULL, NULL, 'Owner: Pak Darto, Yeni Pebriana', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(64, 'SUP-NVYZUN', 'PB Tomo Juwiring', 'Apringga Tri Nugraha', NULL, NULL, NULL, 'Owner: Apringga Tri Nugraha', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(65, 'SUP-RYLJVH', 'PB Rukun Hasil Tani (Wonosari, Delanggu)', 'Bu HJ Harti Umy', NULL, NULL, NULL, 'Owner: Bu HJ Harti Umy', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(66, 'SUP-UADNHQ', 'PB Ira Putri Jaya Kudus', 'Bu Tini Kudus', NULL, NULL, NULL, 'Owner: Bu Tini Kudus', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(67, 'SUP-LHUL4S', 'PB HD Herlina Subang', 'Pak Paimo HD', NULL, NULL, NULL, 'Owner: Pak Paimo HD', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(68, 'SUP-T2DOUQ', 'Makelar Mixed Theme!!s', 'Dandil, Sartono', NULL, NULL, NULL, 'Owner: Dandil, Sartono', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(69, 'SUP-HO3HXQ', 'Ketan Subang Batang', 'Mas Epis Rizq Zia', NULL, NULL, NULL, 'Owner: Mas Epis Rizq Zia', '2025-12-23 08:55:29', '2025-12-23 08:55:29'),
(70, 'SUP-MPWGPG', 'CV Aditama Delanggu', 'Pak Eddy, Fendi Delanggu', NULL, NULL, NULL, 'Owner: Pak Eddy, Fendi Delanggu', '2025-12-23 08:55:29', '2025-12-23 08:55:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `suppliers_backup`
--

CREATE TABLE `suppliers_backup` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_supplier` varchar(50) NOT NULL,
  `nama_supplier` varchar(255) NOT NULL,
  `owner` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `suppliers_backup`
--

INSERT INTO `suppliers_backup` (`id`, `kode_supplier`, `nama_supplier`, `owner`, `alamat`, `telepon`, `email`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'SUP001', 'Ibu Sari', NULL, 'Solo', '0271655555', 'sari@sari.com', 'Create dari pembelian', '2025-11-29 19:46:56', '2025-11-29 19:46:56'),
(2, 'SUP-R1WTE6', 'Mbak Dwi Suyatmi Athaya, Mas Harsono', 'Mbak Dwi Suyatmi Athaya, Mas Harsono', NULL, NULL, NULL, 'Owner: Mbak Dwi Suyatmi Athaya, Mas Harsono', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(3, 'SUP-IC5MO2', 'Mbak Ayu Sigit Aribowo', 'Mbak Ayu Sigit Aribowo', NULL, NULL, NULL, 'Owner: Mbak Ayu Sigit Aribowo', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(4, 'SUP-LF5BLU', 'Pak Sapto Giri (Aufa)', 'Pak Sapto Giri (Aufa)', NULL, NULL, NULL, 'Owner: Pak Sapto Giri (Aufa)', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(5, 'SUP-HYM3NF', 'Bu Gatot Sumarni', 'Bu Gatot Sumarni', NULL, NULL, NULL, 'Owner: Bu Gatot Sumarni', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(6, 'SUP-JYXMFN', 'Bu Winarti, Dr Puji Setiawan', 'Bu Winarti, Dr Puji Setiawan', NULL, NULL, NULL, 'Owner: Bu Winarti, Dr Puji Setiawan', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(7, 'SUP-P9EDSC', 'Eko Wahyono Remin', 'Eko Wahyono Remin', NULL, NULL, NULL, 'Owner: Eko Wahyono Remin', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(8, 'SUP-MVBRKO', 'Setyo Bayu Haji', 'Setyo Bayu Haji', NULL, NULL, NULL, 'Owner: Setyo Bayu Haji', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(9, 'SUP-ANSXTQ', 'Pak Wardi, Bu Tri Warsini', 'Pak Wardi, Bu Tri Warsini', NULL, NULL, NULL, 'Owner: Pak Wardi, Bu Tri Warsini', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(10, 'SUP-74GOKU', 'Sarwanto Ngiri', 'Sarwanto Ngiri', NULL, NULL, NULL, 'Owner: Sarwanto Ngiri', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(11, 'SUP-HYYIKZ', 'Ambar Nguwer', 'Ambar Nguwer', NULL, NULL, NULL, 'Owner: Ambar Nguwer', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(12, 'SUP-I0AOAD', 'Mas Eksan, Mas Daryono', 'Mas Eksan, Mas Daryono', NULL, NULL, NULL, 'Owner: Mas Eksan, Mas Daryono', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(13, 'SUP-E9L6MQ', 'Pak Parwitto, Mbak Nanik', 'Pak Parwitto, Mbak Nanik', NULL, NULL, NULL, 'Owner: Pak Parwitto, Mbak Nanik', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(14, 'SUP-RCKZRM', 'Pak Joko CAB', 'Pak Joko CAB', NULL, NULL, NULL, 'Owner: Pak Joko CAB', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(15, 'SUP-NPQRDH', 'Pak Maryadi', 'Pak Maryadi', NULL, NULL, NULL, 'Owner: Pak Maryadi', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(16, 'SUP-TETFHE', 'Mbak Eki Mulyani, Pak Sutrisno', 'Mbak Eki Mulyani, Pak Sutrisno', NULL, NULL, NULL, 'Owner: Mbak Eki Mulyani, Pak Sutrisno', '2025-12-22 04:22:19', '2025-12-22 06:20:40'),
(17, 'SUP-2XJPQP', 'Pak Darto, Yeni Pebriana', 'Pak Darto, Yeni Pebriana', NULL, NULL, NULL, 'Owner: Pak Darto, Yeni Pebriana', '2025-12-22 04:22:19', '2025-12-22 06:20:40'),
(18, 'SUP-UFT8MC', 'Apringga Tri Nugraha', 'Apringga Tri Nugraha', NULL, NULL, NULL, 'Owner: Apringga Tri Nugraha', '2025-12-22 04:22:19', '2025-12-22 06:20:40'),
(19, 'SUP-WLWC0T', 'Bu HJ Harti Umy', 'Bu HJ Harti Umy', NULL, NULL, NULL, 'Owner: Bu HJ Harti Umy', '2025-12-22 04:22:19', '2025-12-22 06:20:40'),
(20, 'SUP-1YWSS2', 'Bu Tini Kudus', 'Bu Tini Kudus', NULL, NULL, NULL, 'Owner: Bu Tini Kudus', '2025-12-22 04:22:19', '2025-12-22 06:20:40'),
(21, 'SUP-JA3F0J', 'Pak Paimo HD', 'Pak Paimo HD', NULL, NULL, NULL, 'Owner: Pak Paimo HD', '2025-12-22 04:22:19', '2025-12-22 06:20:40'),
(22, 'SUP-ST9GJO', 'Dandil, Sartono', 'Dandil, Sartono', NULL, NULL, NULL, 'Owner: Dandil, Sartono', '2025-12-22 04:22:19', '2025-12-22 06:20:40'),
(23, 'SUP-9OKJED', 'Mas Epis Rizq Zia', 'Mas Epis Rizq Zia', NULL, NULL, NULL, 'Owner: Mas Epis Rizq Zia', '2025-12-22 04:22:19', '2025-12-22 06:20:40'),
(24, 'SUP-NAPAIU', 'Pak Eddy, Fendi Delanggu', 'Pak Eddy, Fendi Delanggu', NULL, NULL, NULL, 'Owner: Pak Eddy, Fendi Delanggu', '2025-12-22 04:22:19', '2025-12-22 06:20:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `suppliers_pre_restore`
--

CREATE TABLE `suppliers_pre_restore` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_supplier` varchar(50) NOT NULL,
  `nama_supplier` varchar(255) NOT NULL,
  `owner` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `suppliers_pre_restore`
--

INSERT INTO `suppliers_pre_restore` (`id`, `kode_supplier`, `nama_supplier`, `owner`, `alamat`, `telepon`, `email`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'SUP001', 'Ibu Sari', NULL, 'Solo', '0271655555', 'sari@sari.com', 'Create dari pembelian', '2025-11-29 19:46:56', '2025-11-29 19:46:56'),
(2, 'SUP-R1WTE6', 'Mbak Dwi Suyatmi Athaya, Mas Harsono', 'Mbak Dwi Suyatmi Athaya, Mas Harsono', NULL, NULL, NULL, 'Owner: Mbak Dwi Suyatmi Athaya, Mas Harsono', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(3, 'SUP-IC5MO2', 'Mbak Ayu Sigit Aribowo', 'Mbak Ayu Sigit Aribowo', NULL, NULL, NULL, 'Owner: Mbak Ayu Sigit Aribowo', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(4, 'SUP-LF5BLU', 'Pak Sapto Giri (Aufa)', 'Pak Sapto Giri (Aufa)', NULL, NULL, NULL, 'Owner: Pak Sapto Giri (Aufa)', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(5, 'SUP-HYM3NF', 'Bu Gatot Sumarni', 'Bu Gatot Sumarni', NULL, NULL, NULL, 'Owner: Bu Gatot Sumarni', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(6, 'SUP-JYXMFN', 'Bu Winarti, Dr Puji Setiawan', 'Bu Winarti, Dr Puji Setiawan', NULL, NULL, NULL, 'Owner: Bu Winarti, Dr Puji Setiawan', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(7, 'SUP-P9EDSC', 'Eko Wahyono Remin', 'Eko Wahyono Remin', NULL, NULL, NULL, 'Owner: Eko Wahyono Remin', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(8, 'SUP-MVBRKO', 'Setyo Bayu Haji', 'Setyo Bayu Haji', NULL, NULL, NULL, 'Owner: Setyo Bayu Haji', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(9, 'SUP-ANSXTQ', 'Pak Wardi, Bu Tri Warsini', 'Pak Wardi, Bu Tri Warsini', NULL, NULL, NULL, 'Owner: Pak Wardi, Bu Tri Warsini', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(10, 'SUP-74GOKU', 'Sarwanto Ngiri', 'Sarwanto Ngiri', NULL, NULL, NULL, 'Owner: Sarwanto Ngiri', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(11, 'SUP-HYYIKZ', 'Ambar Nguwer', 'Ambar Nguwer', NULL, NULL, NULL, 'Owner: Ambar Nguwer', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(12, 'SUP-I0AOAD', 'Mas Eksan, Mas Daryono', 'Mas Eksan, Mas Daryono', NULL, NULL, NULL, 'Owner: Mas Eksan, Mas Daryono', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(13, 'SUP-E9L6MQ', 'Pak Parwitto, Mbak Nanik', 'Pak Parwitto, Mbak Nanik', NULL, NULL, NULL, 'Owner: Pak Parwitto, Mbak Nanik', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(14, 'SUP-RCKZRM', 'Pak Joko CAB', 'Pak Joko CAB', NULL, NULL, NULL, 'Owner: Pak Joko CAB', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(15, 'SUP-NPQRDH', 'Pak Maryadi', 'Pak Maryadi', NULL, NULL, NULL, 'Owner: Pak Maryadi', '2025-12-22 04:22:18', '2025-12-22 06:20:40'),
(16, 'SUP-TETFHE', 'Mbak Eki Mulyani, Pak Sutrisno', 'Mbak Eki Mulyani, Pak Sutrisno', NULL, NULL, NULL, 'Owner: Mbak Eki Mulyani, Pak Sutrisno', '2025-12-22 04:22:19', '2025-12-22 06:20:40'),
(17, 'SUP-2XJPQP', 'Pak Darto, Yeni Pebriana', 'Pak Darto, Yeni Pebriana', NULL, NULL, NULL, 'Owner: Pak Darto, Yeni Pebriana', '2025-12-22 04:22:19', '2025-12-22 06:20:40'),
(18, 'SUP-UFT8MC', 'Apringga Tri Nugraha', 'Apringga Tri Nugraha', NULL, NULL, NULL, 'Owner: Apringga Tri Nugraha', '2025-12-22 04:22:19', '2025-12-22 06:20:40'),
(19, 'SUP-WLWC0T', 'Bu HJ Harti Umy', 'Bu HJ Harti Umy', NULL, NULL, NULL, 'Owner: Bu HJ Harti Umy', '2025-12-22 04:22:19', '2025-12-22 06:20:40'),
(20, 'SUP-1YWSS2', 'Bu Tini Kudus', 'Bu Tini Kudus', NULL, NULL, NULL, 'Owner: Bu Tini Kudus', '2025-12-22 04:22:19', '2025-12-22 06:20:40'),
(21, 'SUP-JA3F0J', 'Pak Paimo HD', 'Pak Paimo HD', NULL, NULL, NULL, 'Owner: Pak Paimo HD', '2025-12-22 04:22:19', '2025-12-22 06:20:40'),
(22, 'SUP-ST9GJO', 'Dandil, Sartono', 'Dandil, Sartono', NULL, NULL, NULL, 'Owner: Dandil, Sartono', '2025-12-22 04:22:19', '2025-12-22 06:20:40'),
(23, 'SUP-9OKJED', 'Mas Epis Rizq Zia', 'Mas Epis Rizq Zia', NULL, NULL, NULL, 'Owner: Mas Epis Rizq Zia', '2025-12-22 04:22:19', '2025-12-22 06:20:40'),
(24, 'SUP-NAPAIU', 'Pak Eddy, Fendi Delanggu', 'Pak Eddy, Fendi Delanggu', NULL, NULL, NULL, 'Owner: Pak Eddy, Fendi Delanggu', '2025-12-22 04:22:19', '2025-12-22 06:20:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaction_histories`
--

CREATE TABLE `transaction_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_code` varchar(255) NOT NULL COMMENT 'Kode transaksi unik',
  `transaction_type` varchar(255) NOT NULL COMMENT 'Tipe transaksi: penjualan, pembelian, adjustment, dll',
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID referensi (sale_id, purchase_id, etc)',
  `reference_type` varchar(255) DEFAULT NULL COMMENT 'Tipe referensi: Sale, Purchase, StockAdjustment, dll',
  `transaction_date` datetime NOT NULL COMMENT 'Tanggal transaksi',
  `amount` decimal(15,2) NOT NULL COMMENT 'Jumlah transaksi',
  `currency` varchar(3) NOT NULL DEFAULT 'IDR' COMMENT 'Mata uang',
  `description` varchar(255) DEFAULT NULL COMMENT 'Deskripsi transaksi',
  `status` varchar(255) NOT NULL DEFAULT 'completed' COMMENT 'Status: pending, completed, failed, cancelled',
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'User yang melakukan transaksi',
  `payment_method` varchar(255) DEFAULT NULL COMMENT 'Metode pembayaran',
  `notes` text DEFAULT NULL COMMENT 'Catatan tambahan',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Data tambahan dalam format JSON' CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `units`
--

CREATE TABLE `units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_unit` varchar(50) NOT NULL,
  `nama_unit` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `parent_unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `conversion_value` decimal(15,6) DEFAULT NULL,
  `is_base_unit` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `units`
--

INSERT INTO `units` (`id`, `kode_unit`, `nama_unit`, `description`, `created_at`, `updated_at`, `parent_unit_id`, `conversion_value`, `is_base_unit`) VALUES
(5, 'Kg', 'kg', NULL, '2025-11-01 14:28:08', '2025-11-01 14:28:08', NULL, 1.000000, 1),
(6, 'Ton', 'Ton', NULL, '2025-11-01 14:28:56', '2025-11-01 14:28:56', 5, 1000.000000, 0),
(7, 'Sak', 'sak', NULL, '2025-11-01 14:30:05', '2025-11-01 14:30:05', 5, 25.000000, 0),
(9, 'PCS', 'Piece/Buah', NULL, '2025-11-17 07:37:55', '2025-11-17 07:37:55', NULL, NULL, 1),
(11, 'BOX', 'Box', NULL, '2025-11-17 07:37:55', '2025-11-17 07:37:55', NULL, NULL, 1),
(12, 'PACK', 'Paket', NULL, '2025-11-17 07:37:55', '2025-11-17 07:37:55', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test User', NULL, 'test@example.com', '2025-11-01 14:08:58', '$2y$12$kT48redIfSSzOQ7CcsoHsucv7sp9qlIBkX1ztZiHrdY81mCzfydOe', 'foSkc9d3pD', 'iGit74yZrP', '2025-11-01 14:08:58', 'JkrJP6PPoK', '2025-11-01 14:08:59', '2025-11-01 14:08:59'),
(2, 'Administrator', 'admin', 'admin@example.test', NULL, '$2y$12$enPc4q0phMoqsPnmt2zBmepuk4Xc3Tu1A4z4XND6JGuuw/vomcwRa', NULL, NULL, NULL, NULL, '2025-11-01 14:08:59', '2025-11-01 14:08:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_gudang` varchar(50) NOT NULL,
  `nama_gudang` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `warehouses`
--

INSERT INTO `warehouses` (`id`, `kode_gudang`, `nama_gudang`, `alamat`, `telepon`, `pic`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'GP', 'Gudang Pusat', 'Jl. Pusat No. 1', NULL, NULL, NULL, '2025-11-01 14:08:59', '2025-11-01 14:08:59');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_kode_kategori_unique` (`kode_kategori`);

--
-- Indeks untuk tabel `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_kode_pelanggan_unique` (`kode_pelanggan`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_kode_produk_unique` (`kode_produk`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_subcategory_id_foreign` (`subcategory_id`),
  ADD KEY `products_supplier_id_foreign` (`supplier_id`);

--
-- Indeks untuk tabel `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchases_kode_pembelian_unique` (`no_invoice`),
  ADD KEY `purchases_supplier_id_foreign` (`supplier_id`),
  ADD KEY `purchases_store_id_foreign` (`store_id`),
  ADD KEY `purchases_warehouse_id_foreign` (`warehouse_id`);

--
-- Indeks untuk tabel `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_items_purchase_id_foreign` (`purchase_id`),
  ADD KEY `purchase_items_category_id_foreign` (`category_id`),
  ADD KEY `purchase_items_subcategory_id_foreign` (`subcategory_id`),
  ADD KEY `purchase_items_product_id_foreign` (`product_id`),
  ADD KEY `purchase_items_unit_id_foreign` (`unit_id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_user_id_foreign` (`user_id`),
  ADD KEY `sales_customer_id_foreign` (`customer_id`),
  ADD KEY `sales_store_id_foreign` (`store_id`),
  ADD KEY `sales_warehouse_id_foreign` (`warehouse_id`);

--
-- Indeks untuk tabel `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_items_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_items_product_id_foreign` (`product_id`),
  ADD KEY `sale_items_unit_id_foreign` (`unit_id`),
  ADD KEY `sale_items_batch_id_foreign` (`batch_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_adjustments_product_id_foreign` (`product_id`),
  ADD KEY `stock_adjustments_store_id_foreign` (`store_id`),
  ADD KEY `stock_adjustments_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `stock_adjustments_user_id_foreign` (`user_id`),
  ADD KEY `stock_adjustments_unit_id_foreign` (`unit_id`);

--
-- Indeks untuk tabel `stock_batches`
--
ALTER TABLE `stock_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_batches_location_type_location_id_index` (`location_type`,`location_id`),
  ADD KEY `stock_batches_product_id_location_type_index` (`product_id`,`location_type`),
  ADD KEY `stock_batches_updated_at_index` (`updated_at`),
  ADD KEY `stock_batches_created_at_index` (`created_at`),
  ADD KEY `stock_batches_status_index` (`status`),
  ADD KEY `stock_batches_category_id_foreign` (`category_id`),
  ADD KEY `stock_batches_subcategory_id_foreign` (`subcategory_id`);

--
-- Indeks untuk tabel `stock_cards`
--
ALTER TABLE `stock_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_cards_stock_batch_id_type_index` (`stock_batch_id`,`type`),
  ADD KEY `stock_cards_created_at_index` (`created_at`),
  ADD KEY `stock_cards_product_id_index` (`product_id`),
  ADD KEY `stock_cards_batch_id_index` (`batch_id`),
  ADD KEY `stock_cards_reference_type_index` (`reference_type`),
  ADD KEY `stock_cards_reference_id_index` (`reference_id`);

--
-- Indeks untuk tabel `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stores_kode_toko_unique` (`kode_toko`);

--
-- Indeks untuk tabel `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subcategories_kode_subkategori_unique` (`kode_subkategori`),
  ADD KEY `subcategories_category_id_foreign` (`category_id`);

--
-- Indeks untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_kode_supplier_unique` (`kode_supplier`);

--
-- Indeks untuk tabel `suppliers_backup`
--
ALTER TABLE `suppliers_backup`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_kode_supplier_unique` (`kode_supplier`);

--
-- Indeks untuk tabel `suppliers_pre_restore`
--
ALTER TABLE `suppliers_pre_restore`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_kode_supplier_unique` (`kode_supplier`);

--
-- Indeks untuk tabel `transaction_histories`
--
ALTER TABLE `transaction_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_histories_transaction_type_index` (`transaction_type`),
  ADD KEY `transaction_histories_transaction_date_index` (`transaction_date`),
  ADD KEY `transaction_histories_user_id_index` (`user_id`),
  ADD KEY `transaction_histories_status_index` (`status`),
  ADD KEY `transaction_histories_reference_type_index` (`reference_type`),
  ADD KEY `transaction_histories_created_at_index` (`created_at`),
  ADD KEY `transaction_histories_transaction_code_index` (`transaction_code`);

--
-- Indeks untuk tabel `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `units_kode_unit_unique` (`kode_unit`),
  ADD KEY `units_parent_unit_id_foreign` (`parent_unit_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- Indeks untuk tabel `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `warehouses_kode_gudang_unique` (`kode_gudang`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT untuk tabel `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT untuk tabel `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT untuk tabel `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=620;

--
-- AUTO_INCREMENT untuk tabel `stock_batches`
--
ALTER TABLE `stock_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=649;

--
-- AUTO_INCREMENT untuk tabel `stock_cards`
--
ALTER TABLE `stock_cards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=382;

--
-- AUTO_INCREMENT untuk tabel `stores`
--
ALTER TABLE `stores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT untuk tabel `suppliers_backup`
--
ALTER TABLE `suppliers_backup`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `suppliers_pre_restore`
--
ALTER TABLE `suppliers_pre_restore`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `transaction_histories`
--
ALTER TABLE `transaction_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;

--
-- AUTO_INCREMENT untuk tabel `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchases_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_items_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `stock_batches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`);

--
-- Ketidakleluasaan untuk tabel `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD CONSTRAINT `stock_adjustments_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_adjustments_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_adjustments_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_adjustments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_adjustments_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `stock_batches`
--
ALTER TABLE `stock_batches`
  ADD CONSTRAINT `stock_batches_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_batches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_batches_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `stock_cards`
--
ALTER TABLE `stock_cards`
  ADD CONSTRAINT `stock_cards_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `stock_batches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_cards_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_cards_stock_batch_id_foreign` FOREIGN KEY (`stock_batch_id`) REFERENCES `stock_batches` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaction_histories`
--
ALTER TABLE `transaction_histories`
  ADD CONSTRAINT `transaction_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `units_parent_unit_id_foreign` FOREIGN KEY (`parent_unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
