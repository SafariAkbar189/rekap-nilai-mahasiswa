-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 27, 2026 at 12:57 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db5e`
--

-- --------------------------------------------------------

--
-- Table structure for table `nilaimhsw`
--

CREATE TABLE `nilaimhsw` (
  `nim` varchar(20) NOT NULL,
  `namalengkap` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `nilaiakhir` decimal(5,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `nilaimhsw`
--

INSERT INTO `nilaimhsw` (`nim`, `namalengkap`, `foto`, `nilaiakhir`, `created_at`, `updated_at`) VALUES
('3202316003', 'Habil', '1793062008_6adff4783cea2.jpg', 70.00, '2026-10-09 09:51:51', '2026-10-27 07:46:48'),
('3202316007', 'Fadhil', '1793062090_6adff4ca57ab3.jpg', 45.65, '2026-10-13 11:28:09', '2026-10-27 07:48:10'),
('3202316008', 'Surya', '1793062078_6adff4be02879.jpg', 46.73, '2026-10-13 11:29:57', '2026-10-27 07:47:58'),
('3202316011', 'Budi Santoso', '1793062053_6adff4a5d6c0f.jpg', 55.40, '2026-10-14 08:30:00', '2026-10-27 07:47:33'),
('3202316013', 'Eko Prasetyo', '1793062037_6adff495077ca.jpg', 63.25, '2026-10-14 09:30:00', '2026-10-27 07:47:17'),
('3202316014', 'Nadia Putri', '1793061878_6adff3f6a61b3.png', 95.00, '2026-10-14 10:00:00', '2026-10-27 07:44:38'),
('3202316015', 'Andi Pratama', '1793062104_6adff4d829455.jpg', 40.00, '2026-10-14 10:30:00', '2026-10-27 07:48:24'),
('3202316016', 'Rina Wijaya', '1793061994_6adff46ac8683.jpg', 73.80, '2026-10-14 11:00:00', '2026-10-27 07:46:34'),
('3202316018', 'Hendra Setiawan', '1793062024_6adff48813c32.png', 68.90, '2026-10-14 13:00:00', '2026-10-27 07:47:04'),
('3202316020', 'Taufik Hidayat', '1793062065_6adff4b16d7c3.jpg', 52.10, '2026-10-14 14:00:00', '2026-10-27 07:47:45'),
('3202416051', 'Safari Arbain Akbar WIbowo', '1792995272_6adeefc80fcb4.jpg', 100.00, '2026-10-13 11:44:39', '2026-10-26 13:14:32'),
('3202416056', 'Surya Hanan Nur Alim', '1793061827_6adff3c3333f1.png', 100.00, '2026-10-15 07:26:06', '2026-10-27 07:43:47'),
('3202416058', 'Handy Setiawan', '1793061843_6adff3d30f142.png', 100.00, '2026-10-15 07:26:44', '2026-10-27 07:44:03'),
('3202416118', 'Naifa Maulyzahrita Putri', '1793061865_6adff3e9ab116.jpg', 100.00, '2026-10-15 07:26:27', '2026-10-27 07:44:25'),
('4202416058', 'Setiawan', '1793062115_6adff4e3ecfa9.jpg', 40.00, '2026-10-15 12:36:40', '2026-10-27 07:48:35');

-- --------------------------------------------------------

--
-- Table structure for table `photo`
--

CREATE TABLE `photo` (
  `id` int NOT NULL,
  `filename` varchar(255) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `photo`
--

INSERT INTO `photo` (`id`, `filename`, `uploaded_at`) VALUES
(1, 'img/Safari Arbain Akbar Wibowo - BG Merah.jpg', '2026-10-20 06:38:33'),
(2, 'Safari Akbar HMJTE.png', '2026-10-20 06:45:40'),
(3, 'Safari Arbain HMJTE.jpg', '2026-10-20 06:46:17'),
(4, 'Safari Akbar HMJTE.jpg', '2026-10-20 06:52:11'),
(5, 'Safari Arbain Akbar Wibowo - BG Merah.png', '2026-10-20 06:58:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `nilaimhsw`
--
ALTER TABLE `nilaimhsw`
  ADD PRIMARY KEY (`nim`);

--
-- Indexes for table `photo`
--
ALTER TABLE `photo`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `photo`
--
ALTER TABLE `photo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
