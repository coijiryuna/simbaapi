-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.11.14-MariaDB-0+deb12u2 - Debian 12
-- Server OS:                    debian-linux-gnu
-- HeidiSQL Version:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table baznas.konfigurasi
CREATE TABLE IF NOT EXISTS `konfigurasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(50) DEFAULT NULL,
  `key` varchar(100) DEFAULT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Dumping data for table baznas.konfigurasi: ~8 rows (approximately)
DELETE FROM `konfigurasi`;
INSERT INTO `konfigurasi` (`id`, `group`, `key`, `value`, `created_at`, `updated_at`) VALUES
	(1, 'demo', 'base_url', 'https://demo-simba.baznas.or.id/', '2025-01-01 22:59:09', '2025-04-07 15:46:34'),
	(2, 'demo', 'org_code', '9977200', '2025-01-01 22:59:09', '2025-04-07 15:46:37'),
	(3, 'demo', 'api_key', 'ZFRNMmVTdFNiMHB6Um1Kc2VtNDJTR3RsVWtoVU9WQkhkVVZFVVVKQlNFWXlkR2xyWVZsVFprcEZTRUZxV2t4TFJqaHNXV2hWTkZsQmEwYzRlRkJxUmtScVVrUTFlamRoWVdObmJGQllRaTgyZVVoUVdYRjJhMDUwZFd0b1QyRkxkVFJxYm5CU2FGaEViRUU5', '2025-01-01 22:59:09', '2025-04-07 15:46:39'),
	(4, 'demo', 'admin_email', 'baznasprov.demo@baznas.or.id', '2025-01-01 22:59:09', '2025-04-07 15:46:45'),
	(5, 'simba', 'base_url', 'https://simba.baznas.go.id/', '2025-04-08 14:35:22', '2025-04-08 14:35:40'),
	(6, 'simba', 'org_code', '3603300', '2025-04-08 14:36:02', '2025-04-08 14:44:37'),
	(7, 'simba', 'api_key', 'tes_api_key', '2025-04-08 14:36:21', '2025-04-08 14:44:49'),
	(8, 'simba', 'admin_email', 'tesapikey@gmail.com', '2025-04-08 14:36:35', '2025-04-08 14:45:00');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

