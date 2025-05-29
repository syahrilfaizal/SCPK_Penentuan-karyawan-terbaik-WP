-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 29 Bulan Mei 2025 pada 14.16
-- Versi server: 8.0.30
-- Versi PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `scpk`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `alternatif`
--

CREATE TABLE `alternatif` (
  `id_alternatif` int NOT NULL,
  `alternatif` varchar(20) NOT NULL,
  `k1` varchar(20) NOT NULL,
  `k2` varchar(20) NOT NULL,
  `k3` varchar(20) NOT NULL,
  `k4` varchar(20) NOT NULL,
  `k5` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `alternatif`
--

INSERT INTO `alternatif` (`id_alternatif`, `alternatif`, `k1`, `k2`, `k3`, `k4`, `k5`) VALUES
(1, 'Adita', '5', '5', '4', '2', '5'),
(2, 'Daffa', '3', '2', '5', '2', '5'),
(3, 'Kiky', '5', '4', '3', '5', '5'),
(4, 'Ucup', '4', '4', '4', '4', '4'),
(5, 'Eja', '4', '3', '2', '3', '5'),
(6, 'Hana', '5', '4', '5', '4', '5'),
(7, 'Desnita', '5', '2', '5', '3', '5');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kriteria`
--

CREATE TABLE `kriteria` (
  `id_kriteria` varchar(20) NOT NULL,
  `kriteria` varchar(50) NOT NULL,
  `kepentingan` varchar(20) NOT NULL,
  `cost_benefit` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `kriteria`
--

INSERT INTO `kriteria` (`id_kriteria`, `kriteria`, `kepentingan`, `cost_benefit`) VALUES
('1', 'Pengetahuan', '3', 'benefit'),
('2', 'Kemampuan Hasil Kerja', '5', 'benefit'),
('3', 'Absensi', '4', 'cost'),
('4', 'Sikap / Kedisiplinan', '5', 'benefit'),
('5', 'Kerja Sama', '4', 'benefit');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `alternatif`
--
ALTER TABLE `alternatif`
  ADD PRIMARY KEY (`id_alternatif`);

--
-- Indeks untuk tabel `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id_kriteria`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `alternatif`
--
ALTER TABLE `alternatif`
  MODIFY `id_alternatif` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
