-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2025 at 04:32 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensi`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('hadir','sakit','izin','alfa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `user_id`, `tanggal`, `status`) VALUES
(6, 6, '2025-04-14', 'sakit'),
(7, 8, '2025-04-14', 'hadir'),
(8, 15, '2025-04-14', 'hadir');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `role` enum('siswa','guru') NOT NULL,
  `kelas` varchar(20) DEFAULT NULL,
  `jurusan` varchar(50) DEFAULT NULL,
  `tahun_masuk` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `foto`, `role`, `kelas`, `jurusan`, `tahun_masuk`) VALUES
(6, 'Faridz Hendrawan', 'farid', '$2y$10$WASk12B/nbKtD7kZPr2uwu4p00uaunP0xY77GsWBPbwO5ba/kvz32', '67fc7b3010b7d.jpg', 'siswa', 'XII', 'TKJ', '2024'),
(7, 'Bapak Tedi', 'guru', '$2y$10$fZ0rcb8F1C4ETWZllm71M.2KG3lWtuc1PHjs52E/VKR756x32th/i', '67fc7db02523a.png', 'guru', '', '', ''),
(8, 'Rayyis Hayatul', 'rayyis', '$2y$10$49xaSmd9u2SSGua0lYlQf.6eSvA7of28UWwh7ZoAl2HJz6gVLN4P2', '1744602209_erd_nilai_siswa.png', 'siswa', 'XII', 'RPL', '2024'),
(12, 'Rangga Dwi', 'rangga', '$2y$10$uNkqrVc9DRQV/yps8NX/zO5MjMNzHozYDP897zX8EvIqitbtEv6Ky', '1744602408_Student Portal Login UI.jpeg', 'siswa', 'XI', 'TKJ', '2024'),
(13, 'pppaaa', 'ppp', '$2y$10$Ip5pGBqWIrWkZQ0YW7UlbONe82Bl3sTizCs8iYJEBSmr1xooj9rWy', '1744603362_erd_nilai_siswa_login.png', 'siswa', 'XI', 'RPL', '2025'),
(14, 'bapak', 'bapak', '$2y$10$JIvtpFPkRrMVrFTuofqmBe2VtYyMV10og.NINmP6MpT7BO97oN6IW', '67fcad226fec9.jpg', 'guru', '', '', ''),
(15, 'Desta Julpaesal', 'desta', '$2y$10$T.0PStrPESWAU6fzWLzqOuJ3i6jp7lQaCgtxXdA2miKIhO/EUZ3Q.', '1744612794_Rencana setelah lulus.png', 'siswa', 'XI', 'TKJ', '2024');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`tanggal`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
