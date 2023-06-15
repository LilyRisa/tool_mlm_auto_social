-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th6 15, 2023 lúc 04:57 PM
-- Phiên bản máy phục vụ: 10.3.39-MariaDB-cll-lve
-- Phiên bản PHP: 8.1.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `congminh_tool`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `acctoken`
--

CREATE TABLE `acctoken` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `acctoken`
--

INSERT INTO `acctoken` (`id`, `content`) VALUES
(1, 'EAAFdZAtx7v4sBAE2GWShf2MDaVQTvnll6ERbg6z4tOxxxxxxxxxxxxxxxxxxxxxxxxj7L3qWqHbqFTlc90GxaLtPZCPboZD');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `save`
--

CREATE TABLE `save` (
  `id` int(11) NOT NULL,
  `page_id` text NOT NULL,
  `content` text NOT NULL,
  `type` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Chỉ mục cho bảng `acctoken`
--
ALTER TABLE `acctoken`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `save`
--
ALTER TABLE `save`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `acctoken`
--
ALTER TABLE `acctoken`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `save`
--
ALTER TABLE `save`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
