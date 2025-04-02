-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 02, 2025 lúc 04:09 PM
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
-- Cơ sở dữ liệu: `quanlybanpk`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cauhinh_giohang`
--

CREATE TABLE `cauhinh_giohang` (
  `id` int(11) NOT NULL,
  `bat_tat` tinyint(1) DEFAULT 1,
  `gioihan_soluong` int(11) DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cauhinh_giohang`
--

INSERT INTO `cauhinh_giohang` (`id`, `bat_tat`, `gioihan_soluong`) VALUES
(1, 0, 10);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chattructuyen`
--

CREATE TABLE `chattructuyen` (
  `idchat` int(11) NOT NULL,
  `idgui` int(11) NOT NULL,
  `idnhan` int(11) NOT NULL,
  `cuoc_tro_chuyen` int(11) NOT NULL,
  `noidung` text NOT NULL,
  `loaithongdiep` enum('text','image','file') DEFAULT 'text',
  `file_dinh_kem` varchar(255) DEFAULT NULL,
  `trangthai` tinyint(1) DEFAULT 0,
  `daxem` tinyint(1) DEFAULT 0,
  `phan_loai` enum('admin','user') NOT NULL,
  `thoigian` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chattructuyen`
--

INSERT INTO `chattructuyen` (`idchat`, `idgui`, `idnhan`, `cuoc_tro_chuyen`, `noidung`, `loaithongdiep`, `file_dinh_kem`, `trangthai`, `daxem`, `phan_loai`, `thoigian`) VALUES
(1, 1, 3, 1001, 'Chào bạn, tôi có thể giúp gì?', 'text', NULL, 1, 0, 'admin', '2025-03-10 14:02:52'),
(2, 3, 1, 1001, 'Tôi muốn hỏi về chính sách bảo hành.', 'text', NULL, 1, 1, 'user', '2025-03-10 14:02:52'),
(3, 1, 5, 1002, 'Bạn cần hỗ trợ gì không?', 'text', NULL, 1, 0, 'admin', '2025-03-10 14:02:52'),
(4, 5, 1, 1002, 'Sản phẩm này có khuyến mãi không ạ?', 'text', NULL, 1, 0, 'user', '2025-03-10 14:02:52'),
(5, 1, 7, 1003, 'Xin chào! Bạn cần hỗ trợ về vấn đề gì?', 'text', NULL, 1, 0, 'admin', '2025-03-10 14:02:52'),
(6, 7, 1, 1003, 'Tôi không nhận được mã xác nhận đơn hàng.', 'text', NULL, 1, 0, 'user', '2025-03-10 14:02:52'),
(7, 1, 8, 1004, 'Đơn hàng của bạn đang được xử lý nhé.', 'text', NULL, 1, 1, 'admin', '2025-03-10 14:02:52'),
(8, 8, 1, 1004, 'Cảm ơn, khi nào nhận được hàng ạ?', 'text', NULL, 1, 1, 'user', '2025-03-10 14:02:52'),
(9, 1, 14, 1005, 'Bạn có thể gửi ảnh sản phẩm lỗi không?', 'text', NULL, 1, 0, 'admin', '2025-03-10 14:02:52'),
(10, 14, 1, 1005, 'Đây là ảnh sản phẩm bị lỗi.', 'image', 'uploads/error.jpg', 1, 1, 'user', '2025-03-10 14:02:52'),
(11, 1, 15, 1006, 'Bạn cần thêm thông tin gì không?', 'text', NULL, 1, 0, 'admin', '2025-03-10 14:02:52'),
(12, 15, 1, 1006, 'Tôi muốn đổi sản phẩm.', 'text', NULL, 1, 0, 'user', '2025-03-10 14:02:52'),
(13, 1, 16, 1007, 'Bên mình có nhiều ưu đãi hấp dẫn, bạn quan tâm sản phẩm nào?', 'text', NULL, 1, 0, 'admin', '2025-03-10 14:02:52'),
(14, 16, 1, 1007, 'Có ưu đãi nào cho thành viên VIP không?', 'text', NULL, 1, 0, 'user', '2025-03-10 14:02:52'),
(15, 1, 8, 0, 'khoảng 2 ngày nữa ạ', 'text', NULL, 0, 0, 'admin', '2025-03-10 14:45:58');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdonhang`
--

CREATE TABLE `chitietdonhang` (
  `idctdh` int(11) NOT NULL,
  `iddh` int(11) NOT NULL,
  `idsp` int(11) NOT NULL,
  `soluong` int(11) NOT NULL,
  `gia` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietdonhang`
--

INSERT INTO `chitietdonhang` (`idctdh`, `iddh`, `idsp`, `soluong`, `gia`) VALUES
(2, 1, 4, 1, 18000),
(3, 4, 4, 2, 40000),
(4, 1, 6, 2, 20000),
(5, 5, 4, 2, 500000),
(6, 5, 5, 1, 500000),
(7, 6, 6, 3, 400000),
(8, 6, 7, 1, 600000),
(9, 7, 8, 2, 700000),
(10, 7, 9, 1, 350000),
(11, 8, 10, 4, 150000),
(12, 8, 4, 1, 890000),
(13, 9, 5, 3, 320000),
(14, 9, 6, 2, 300000),
(15, 10, 7, 1, 450000),
(16, 10, 8, 2, 650000),
(17, 11, 9, 2, 550000),
(18, 11, 10, 1, 1000000),
(19, 12, 4, 1, 760000),
(20, 12, 5, 2, 400000),
(21, 13, 6, 1, 500000),
(22, 13, 7, 3, 600000),
(23, 14, 8, 2, 700000),
(24, 14, 9, 1, 650000),
(25, 15, 70, 1, 20900),
(26, 16, 69, 1, 45000),
(27, 17, 69, 1, 45000),
(28, 18, 5, 1, 109000),
(29, 18, 7, 1, 149000),
(30, 19, 5, 3, 109000),
(31, 19, 6, 1, 109000),
(32, 20, 9, 1, 76360),
(33, 20, 10, 1, 97000),
(34, 21, 9, 1, 76360),
(35, 21, 5, 1, 109000),
(36, 22, 5, 1, 109000),
(37, 23, 6, 2, 109000),
(38, 23, 5, 1, 109000),
(39, 23, 12, 1, 115000),
(40, 24, 6, 1, 109000),
(41, 24, 7, 1, 149000),
(42, 25, 5, 3, 109000),
(43, 25, 6, 1, 109000),
(44, 25, 4, 5, 159000),
(45, 25, 9, 1, 76360),
(46, 25, 11, 12, 89460),
(47, 25, 7, 1, 149000),
(48, 26, 5, 6, 109000),
(49, 27, 5, 20, 109000),
(50, 27, 4, 2, 159000),
(51, 28, 7, 23, 149000),
(52, 29, 5, 4, 109000),
(53, 29, 4, 13, 159000),
(54, 29, 11, 4, 89460),
(55, 29, 16, 3, 50400),
(56, 29, 21, 4, 79000),
(57, 30, 72, 8, 14300),
(58, 30, 7, 5, 149000),
(59, 31, 6, 7, 109000),
(60, 31, 7, 2, 149000),
(61, 31, 5, 1, 109000),
(62, 31, 4, 1, 159000),
(63, 31, 17, 2, 195000),
(64, 31, 8, 1, 250000),
(65, 31, 13, 1, 70840),
(66, 31, 22, 1, 35640),
(67, 31, 32, 1, 163000),
(68, 33, 7, 7, 149000),
(69, 33, 6, 1, 109000),
(70, 33, 5, 1, 109000),
(71, 33, 4, 100, 159000),
(72, 33, 10, 1, 97000),
(73, 34, 6, 46, 109000),
(74, 34, 10, 10, 97000),
(75, 35, 5, 5, 109000),
(76, 35, 11, 2, 89460),
(77, 36, 6, 1, 109000),
(78, 36, 5, 1, 109000),
(79, 36, 47, 1, 139000),
(80, 36, 46, 6, 140800),
(81, 36, 45, 3, 85000),
(82, 37, 11, 1, 89460),
(83, 37, 31, 1, 120000),
(84, 37, 32, 1, 163000),
(85, 37, 46, 1, 140800),
(86, 37, 71, 1, 65000),
(87, 37, 8, 1, 250000),
(88, 37, 9, 1, 76360),
(89, 37, 47, 1, 139000),
(90, 37, 48, 100, 119000),
(91, 38, 13, 1, 70840),
(92, 38, 27, 5, 255000),
(93, 38, 40, 1, 24200),
(94, 38, 50, 1, 197000),
(95, 39, 17, 30, 195000),
(96, 39, 15, 20, 195000),
(97, 39, 39, 1000, 350000),
(98, 40, 6, 1, 109000),
(99, 40, 7, 1, 149000),
(100, 41, 6, 1, 109000),
(101, 41, 7, 4, 149000),
(102, 42, 5, 1, 109000),
(103, 42, 6, 1, 109000),
(104, 43, 5, 1, 109000),
(105, 43, 4, 1, 159000),
(106, 44, 6, 1000, 109000),
(107, 44, 5, 1, 109000),
(108, 45, 4, 1000000, 159000),
(109, 45, 8, 3, 250000),
(110, 46, 16, 3, 50400),
(111, 46, 17, 5, 195000),
(112, 46, 47, 10, 139000),
(113, 47, 6, 16, 109000),
(114, 48, 46, 30, 140800),
(115, 49, 6, 5, 109000),
(116, 49, 17, 5, 195000),
(117, 49, 22, 8, 35640),
(118, 49, 39, 5, 350000),
(119, 50, 25, 10, 430000),
(120, 50, 28, 8, 275000),
(121, 50, 20, 10, 171000),
(122, 51, 7, 1, 149000),
(123, 52, 6, 1, 109000),
(124, 53, 5, 1, 109000),
(125, 53, 7, 1, 149000),
(126, 54, 7, 1, 149000),
(127, 55, 46, 1, 140800),
(128, 56, 6, 1, 109000),
(129, 57, 7, 1, 149000),
(130, 58, 7, 1, 149000),
(131, 59, 7, 1, 149000),
(132, 60, 4, 1, 159000),
(133, 60, 7, 1, 149000),
(134, 61, 7, 2, 149000),
(135, 62, 7, 1, 149000),
(136, 63, 12, 3, 115000),
(137, 63, 42, 2, 69000),
(138, 64, 7, 5, 149000),
(139, 64, 6, 3, 109000),
(140, 65, 7, 1, 149000),
(141, 65, 15, 1, 195000),
(142, 65, 22, 1, 35640),
(143, 66, 13, 1, 70840),
(144, 67, 12, 1, 115000),
(145, 67, 10, 1, 97000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhgia`
--

CREATE TABLE `danhgia` (
  `iddg` int(11) NOT NULL,
  `idkh` int(11) NOT NULL,
  `idsp` int(11) NOT NULL,
  `sosao` int(11) NOT NULL,
  `noidung` text NOT NULL,
  `thoigian` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danhgia`
--

INSERT INTO `danhgia` (`iddg`, `idkh`, `idsp`, `sosao`, `noidung`, `thoigian`) VALUES
(2, 3, 4, 4, 'Bé nhà m rất thik', '2025-02-27 09:26:34'),
(3, 3, 4, 4, 'Rất ok luôn ạ!', '2025-02-27 09:27:06'),
(4, 3, 5, 5, 'Tốt sẽ ủng hộ tiếp', '2025-02-27 09:27:16'),
(5, 3, 5, 3, 'ok', '2025-02-27 09:27:24'),
(6, 3, 5, 5, 'Tốt', '2025-02-27 09:27:30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhmucsp`
--

CREATE TABLE `danhmucsp` (
  `iddm` int(11) NOT NULL,
  `tendm` varchar(50) NOT NULL,
  `loaidm` int(11) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `mota` text NOT NULL,
  `thoigian` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danhmucsp`
--

INSERT INTO `danhmucsp` (`iddm`, `tendm`, `loaidm`, `icon`, `mota`, `thoigian`) VALUES
(1, 'Gấu bông', 0, 'icon/bee.png', 'Chuyên các loại gấu bông mọi kích cỡ!', '2025-03-09 12:04:51'),
(2, 'Balo - Tú - Ví', 0, 'icon\\lucky-bag.png', '', '2025-03-01 17:13:32'),
(3, 'Balo thời trang Nam Nữ', 2, 'icon\\schoolbag.png', '', '2025-03-01 17:00:52'),
(4, 'Túi Nam Nữ', 2, 'icon\\package.png', '', '2025-03-01 17:10:15'),
(5, 'Ví thời trang cao cấp', 2, 'icon\\wallet.png', '', '2025-03-01 17:10:27'),
(6, 'Đồng hồ', 0, 'icon\\wristwatch.png', '', '2025-03-01 17:20:16'),
(7, 'Đồng hồ Nam Nữ', 6, 'icon\\wrist-watch.png', '', '2025-03-01 17:20:19'),
(8, 'Đồng hồ Thông minh', 6, 'icon\\fitness-tracker.png', '', '2025-03-01 17:18:52'),
(9, 'Trang điểm', 0, 'icon\\makeup.png', '', '2025-03-01 17:22:42'),
(10, 'Dành cho da', 9, 'icon\\night-cream.png', '', '2025-03-01 17:28:49'),
(11, 'Dành cho mắt', 9, 'icon\\eye.png', '', '2025-03-01 17:27:44'),
(12, 'Dành cho môi', 9, 'icon\\lipstick.png', '', '2025-03-01 17:26:13'),
(13, 'Dụng cụ trang điểm', 9, 'icon\\cosmetics.png', '', '2025-03-01 17:24:31'),
(14, 'Trang trí', 0, 'icon\\stars.png', '', '2025-03-01 20:57:04'),
(15, 'Vòng/Lắc', 14, 'icon\\bracelet.png', '', '2025-03-01 20:54:06'),
(16, 'Nhẫn', 14, 'icon\\wedding-ring.png', '', '2025-03-01 20:55:03'),
(17, 'Hoa tai', 14, 'icon\\earrings.png', '', '2025-03-01 20:55:52'),
(18, 'Khác', 0, 'icon\\other.png', 'Bán gì cũng được! chài ai', '2025-03-06 03:10:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `diachigiaohang`
--

CREATE TABLE `diachigiaohang` (
  `iddc` int(11) NOT NULL,
  `idkh` int(11) NOT NULL,
  `diachicuthe` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `diachigiaohang`
--

INSERT INTO `diachigiaohang` (`iddc`, `idkh`, `diachicuthe`) VALUES
(1, 3, 'xã Đông thới, huyện Cái Nước, tỉnh Cà Mau');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donhang`
--

CREATE TABLE `donhang` (
  `iddh` int(11) NOT NULL,
  `idkh` int(11) NOT NULL,
  `tongtien` decimal(10,0) NOT NULL,
  `trangthai` varchar(50) NOT NULL,
  `phuongthuctt` varchar(50) NOT NULL,
  `thoigian` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `donhang`
--

INSERT INTO `donhang` (`iddh`, `idkh`, `tongtien`, `trangthai`, `phuongthuctt`, `thoigian`) VALUES
(1, 3, 120000, 'Đã thanh toán', 'VNPay', '2025-02-25 07:46:39'),
(4, 3, 20000, 'Chưa thanh toán', 'momo', '2025-03-06 07:05:42'),
(5, 3, 1500000, 'Chờ xử lý', 'Tiền mặt', '2025-03-06 07:20:43'),
(6, 5, 2200000, 'Hoàn thành', 'Chuyển khoản', '2025-03-06 07:20:43'),
(7, 7, 1750000, 'Đang giao', 'Tiền mặt', '2025-03-06 07:20:43'),
(8, 8, 890000, 'Chờ xử lý', 'Ví điện tử', '2025-03-06 07:20:43'),
(9, 3, 3200000, 'Hoàn thành', 'Tiền mặt', '2025-03-06 07:20:43'),
(10, 5, 450000, 'Đã hủy', 'Chuyển khoản', '2025-03-06 07:20:43'),
(11, 7, 1900000, 'Chờ xử lý', 'Ví điện tử', '2025-03-06 07:20:43'),
(12, 8, 2100000, 'Hoàn thành', 'Tiền mặt', '2025-03-06 07:20:43'),
(13, 3, 760000, 'Đang giao', 'Chuyển khoản', '2025-03-06 07:20:43'),
(14, 5, 1350000, 'Chờ xử lý', 'Ví điện tử', '2025-03-06 07:20:43'),
(15, 1, 20900, 'Chờ xử lý', 'Tiền mặt', '2025-03-11 10:39:47'),
(16, 1, 45000, 'Chờ xử lý', 'Tiền mặt', '2025-03-11 10:41:10'),
(17, 17, 45000, 'Chờ xử lý', 'Tiền mặt', '2025-03-11 10:51:34'),
(18, 17, 258000, 'Chờ xử lý', 'Tiền mặt', '2025-03-11 10:54:04'),
(19, 17, 436000, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 08:44:22'),
(20, 1, 173360, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 08:45:23'),
(21, 1, 185360, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 08:45:57'),
(22, 17, 109000, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 08:54:18'),
(23, 17, 442000, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 08:59:23'),
(24, 17, 258000, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 09:03:33'),
(25, 17, 2529880, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 09:15:24'),
(26, 17, 654000, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 09:18:15'),
(27, 17, 2498000, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 09:22:38'),
(28, 1, 3427000, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 09:31:02'),
(29, 1, 3328040, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 10:35:51'),
(30, 1, 859400, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 10:37:57'),
(31, 1, 2238480, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 11:06:29'),
(32, 1, 0, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 14:01:57'),
(33, 1, 17258000, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 14:02:52'),
(34, 17, 5984000, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 14:20:41'),
(35, 17, 723920, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 14:38:38'),
(36, 17, 1456800, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 14:40:41'),
(37, 1, 12943620, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 14:44:05'),
(38, 17, 1567040, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 16:41:06'),
(39, 17, 359750000, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 16:46:51'),
(40, 1, 258000, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 17:01:34'),
(41, 1, 705000, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 17:10:19'),
(42, 1, 218000, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 17:11:46'),
(43, 1, 268000, 'Chờ xử lý', 'Tiền mặt', '2025-03-12 17:13:41'),
(44, 17, 109109000, 'Chờ xử lý', 'Tiền mặt', '2025-03-14 08:42:30'),
(45, 17, 9999999999, 'Đã thanh toán', 'Tiền mặt', '2025-03-17 11:48:43'),
(46, 17, 2516200, 'Đã xác nhận', 'Tiền mặt', '2025-03-24 05:18:26'),
(47, 17, 1744000, 'Đã thanh toán', 'Tiền mặt', '2025-03-20 06:52:37'),
(48, 17, 4224000, 'Đã thanh toán', 'Tiền mặt', '2025-03-24 05:18:18'),
(49, 17, 3555120, 'Chờ xử lý', 'Tiền mặt', '2025-03-24 05:20:28'),
(50, 17, 8210000, 'Đã xác nhận', 'Tiền mặt', '2025-03-25 03:59:55'),
(51, 17, 149000, 'Chờ xử lý', 'Tiền mặt', '2025-03-25 04:00:23'),
(52, 17, 109000, 'Chờ xử lý', 'Tiền mặt', '2025-03-25 04:01:58'),
(53, 17, 258000, 'Chờ xử lý', 'Tiền mặt', '2025-03-25 04:08:40'),
(54, 17, 149000, 'Chờ xử lý', 'Tiền mặt', '2025-03-25 04:11:23'),
(55, 17, 140800, 'Chờ xử lý', 'Tiền mặt', '2025-03-25 04:21:51'),
(56, 17, 109000, 'Chờ xử lý', 'Tiền mặt', '2025-03-25 04:25:48'),
(57, 17, 149000, 'Chờ xử lý', 'Tiền mặt', '2025-03-25 04:26:02'),
(58, 17, 149000, 'Chờ xử lý', 'Tiền mặt', '2025-03-25 04:34:42'),
(59, 17, 149000, 'Chờ xử lý', 'Tiền mặt', '2025-03-25 05:15:19'),
(60, 17, 308000, 'Chờ xử lý', 'Tiền mặt', '2025-03-25 09:31:30'),
(61, 17, 298000, 'Chờ xử lý', 'Tiền mặt', '2025-03-25 10:01:20'),
(62, 17, 149000, 'Chờ xử lý', 'Tiền mặt', '2025-03-27 09:57:48'),
(63, 17, 483000, 'Đã thanh toán', 'Tiền mặt', '2025-04-02 09:49:42'),
(64, 17, 1072000, 'Chưa thanh toán', 'Tiền mặt', '2025-04-02 09:49:54'),
(65, 17, 379640, 'Chờ xử lý', 'Tiền mặt', '2025-04-02 09:19:15'),
(66, 17, 70840, 'Đã thanh toán', 'Tiền mặt', '2025-04-02 09:49:35'),
(67, 17, 212000, 'Đã xác nhận', 'Tiền mặt', '2025-04-02 09:49:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giohang`
--

CREATE TABLE `giohang` (
  `idgh` int(11) NOT NULL,
  `idkh` int(11) NOT NULL,
  `idsp` int(11) NOT NULL,
  `soluong` int(11) NOT NULL,
  `thoigian` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `trangthai` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `magiamgia`
--

CREATE TABLE `magiamgia` (
  `idmgg` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `phantram` int(11) NOT NULL,
  `ngayhieuluc` date NOT NULL,
  `ngayketthuc` date NOT NULL,
  `thoigian` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `magiamgia`
--

INSERT INTO `magiamgia` (`idmgg`, `code`, `phantram`, `ngayhieuluc`, `ngayketthuc`, `thoigian`) VALUES
(1, 'sale2025', 10, '2025-02-01', '2025-03-11', '2025-03-10 12:03:26'),
(2, 'salesp001', 5, '2025-02-26', '2025-03-02', '2025-02-28 19:07:19'),
(4, 'DISCOUNT20', 20, '2025-03-05', '2025-03-15', '2025-03-06 06:55:49'),
(5, 'NEWYEAR30', 30, '2025-03-10', '2025-03-20', '2025-03-06 06:56:15'),
(6, 'FREESHIP', 100, '2025-03-01', '2025-03-31', '2025-03-06 05:41:57'),
(7, 'VIP50', 50, '2025-03-15', '2025-03-25', '2025-03-06 06:56:26'),
(8, 'sale8/3', 20, '2025-03-10', '2025-03-11', '2025-03-10 13:05:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `magiamgia_chitiet`
--

CREATE TABLE `magiamgia_chitiet` (
  `id` int(11) NOT NULL,
  `idmgg` int(11) NOT NULL,
  `idsp` int(11) DEFAULT NULL,
  `iddm` int(11) DEFAULT NULL,
  `soluong` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `magiamgia_chitiet`
--

INSERT INTO `magiamgia_chitiet` (`id`, `idmgg`, `idsp`, `iddm`, `soluong`) VALUES
(4, 4, NULL, 2, 5),
(7, 1, 4, NULL, 1),
(8, 1, 5, NULL, 1),
(9, 1, 6, NULL, 1),
(10, 1, 7, NULL, 1),
(11, 1, 8, NULL, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `timestamp`) VALUES
(1, 17, 1, 'tôi tên là phan minh nhật và tôi vừa gửi tin nhắn cho admin ', '2025-04-02 10:31:59'),
(3, 1, 17, 'tôi là admin và tôi vừa gửi tin nhắn cho minh nhật', '2025-04-02 10:33:12'),
(4, 1, 17, 'bạn thấy sản phẩm hôm nay sử dụng thế nào ?? có thắc mắc vui lòng liên hệ chúng tôi qua sdt trên !!', '2025-04-02 10:36:29'),
(5, 14, 1, 'Nguyễn văn a gửi tin nhắn cho admin đây nè!!', '2025-04-02 10:39:02'),
(6, 14, 1, 'sao nó không hiển thì gì vậy tar', '2025-04-02 10:44:53'),
(7, 17, 1, 'nhập xem nó nhận trên csdl không ', '2025-04-02 11:08:09'),
(8, 17, 1, 'Nhập thử xem có vào cơ sở dữ liệu không ', '2025-04-02 12:56:32'),
(9, 1, 17, 'thử admin nhắn cho minh nhật xem nó có được không ', '2025-04-02 13:00:28'),
(10, 1, 14, 'admin vừa gửi tin nhắn cho Nguyễn Văn ah', '2025-04-02 13:39:23'),
(11, 17, 1, 'Nhật gửi tin nhắn', '2025-04-02 13:53:02'),
(12, 1, 17, 'Cái này là của Tuán anh Gửi nè !!', '2025-04-02 13:54:51'),
(13, 1, 17, 'bạn thấy sản phẩm bên mình thế nào ạh', '2025-04-02 14:08:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham`
--

CREATE TABLE `sanpham` (
  `idsp` int(11) NOT NULL,
  `tensp` varchar(50) NOT NULL,
  `mota` text NOT NULL,
  `giaban` decimal(10,0) NOT NULL,
  `soluong` int(11) NOT NULL,
  `anh` varchar(50) NOT NULL,
  `iddm` int(11) NOT NULL,
  `thoigianthemsp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham`
--

INSERT INTO `sanpham` (`idsp`, `tensp`, `mota`, `giaban`, `soluong`, `anh`, `iddm`, `thoigianthemsp`) VALUES
(4, 'Balo1', 'Balo Thời Trang Kiểu Ulzzang Vải Chống Thấm Cao Cấp. Cặp Balo Cho Nam Nữ Đa năng Mintas 284', 159000, 10, 'picture\\balo\\balo1.png', 5, '2025-02-28 18:57:17'),
(5, 'Balo2', 'Mẫu Balo Thời Trang Unisex Siêu Rộng Chống Nước, Có ngăn laptop chống sóc, Cỡ lớn đa năng đựng laptop hoặc đựng quần áo', 109000, 10, 'picture\\balo\\balo2.png', 5, '2025-02-23 07:54:24'),
(6, 'Balo3', 'BALO UNISEX BoyMusic Và OnePiece THỜI TRANG. Balo Vải 3 Lớp Dày Chống Thấm Nước Mintas 300.', 109000, 10, 'picture\\balo\\balo3.png', 5, '2025-02-23 07:54:29'),
(7, 'Balo4', 'Ba Lô Laptop Tích Hợp USB Cao Cấp PRAZA BL174', 149000, 10, 'picture\\balo\\balo4.png', 5, '2025-02-23 07:54:35'),
(8, 'Balo5', 'Balo nam nữ đi học cặp đi học thời trang nam nữ đựng laptop 15.6inh Ba lô nhiều ngăn ulzzang chống nước', 250000, 10, 'picture\\balo\\balo5.png', 5, '2025-02-23 07:54:41'),
(9, 'Gấu bông 1', 'Gấu bông Lena stick cute dễ thương Lotso đủ size ADA S HOUSE', 76360, 5, 'picture\\gaubong\\gaubong1.png', 4, '2025-02-23 07:53:39'),
(10, 'Gấu bông 2', '( Có Size Siêu To 80cm) Chó Bông Corgi Cosplay Gấu Bông', 97000, 5, 'picture\\gaubong\\gaubong2.png', 4, '2025-02-23 07:53:45'),
(11, 'Gấu bông 3', 'Capybara kéo mũi gấu bông mini, cabibara, capipara chảy nước mũi màu hồng Ada s House', 89460, 5, 'picture\\gaubong\\gaubong3.png', 4, '2025-02-23 07:53:53'),
(12, 'Gấu bông 4', 'Gấu bông chó Husky Siêu to khổng Lồ Size 1M5, Gối ôm hình thú Gòn 100%. đồ chơi nhồi bông cute cho bé, GABO TEDDY BEAR', 115000, 5, 'picture\\gaubong\\gaubong4.png', 4, '2025-02-23 07:53:59'),
(13, 'Gấu bông 5', 'Gấu nhồi bông Baby Three siêu dễ thương mẫu mới hottrend', 70840, 5, 'picture\\gaubong\\gaubong5.png', 4, '2025-02-23 07:54:06'),
(14, 'Túi 1', 'Túi Đeo Chéo Nam Nữ MLB Chính Hãng Logo NY, Túi Xách Nam Boy Phố Dạng Hộp Nhiều Ngăn Vải Da Chống Nước 100% _GONZEN', 89000, 5, 'picture\\tui\\tui1.png', 6, '2025-02-20 07:15:28'),
(15, 'Túi 2', 'Túi đeo chéo BRANDON thời trang cao cấp Nam Nữ chất liệu chống thấm nước | Midori For Man', 195000, 5, 'picture\\tui\\tui2.png', 6, '2025-02-20 07:16:55'),
(16, 'Túi 3', '[Siêu Sale] Túi đeo chéo kiểu ngang da ipad MÀU ĐẬM da loại 1 đẹp dẻo mịn K501 Shalla [Hình thật]', 50400, 5, 'picture\\tui\\tui3.png', 6, '2025-02-20 07:17:03'),
(17, 'Túi 4', 'Túi Đeo Chéo Nam Nữ Nhiều Ngăn, Nhỏ Gọn LUVIN, Thiết Kế Basic, Tiện Dụng Đi Chơi, Đi Học Đều Đẹp', 195000, 5, 'picture\\tui\\tui4.png', 6, '2025-02-20 07:17:08'),
(18, 'Túi 5', 'Túi Xách Đeo Chéo KRIXI Da Nam Nữ Cao Cấp Thời Trang Unisex Chính Hãng Local Brand M STUDIO', 249000, 5, 'picture\\tui\\tui5.png', 6, '2025-02-20 07:17:14'),
(19, 'Ví 1', 'Ví nam nhỏ gọn kiểu ví ngang Hàn Quốc chất liệu vải Canvas cao cấp nhiều ngăn tiện lợi', 36200, 5, 'picture\\vi\\vi1.png', 7, '2025-02-20 07:34:35'),
(20, 'Ví 2', 'Ví đựng tiền mini SEEME Lace Wallet dáng ngắn', 171000, 5, 'picture\\vi\\vi2.png', 7, '2025-02-20 07:34:43'),
(21, 'Ví 3', 'Ví dài nam nữ cầm tay nhiều ngăn đựng có ngăn kéo ví da mềm dập nổi cao cấp Hàn Quốc MSP 3088-3', 79000, 5, 'picture\\vi\\vi3.png', 7, '2025-02-20 07:34:50'),
(22, 'Ví 4', 'Ví Ngắn Siêu Mỏng Đựng Thẻ / Bằng Lái Xe Sức Chứa Lớn Siêu Mỏng Cho Nam Và Nữ', 35640, 5, 'picture\\vi\\vi4.png', 7, '2025-02-20 07:34:56'),
(23, 'Ví 5', 'Ví Da Nữ JENNIE Chống Thấm Nước Cao Cấp Chính Hãng Local Brand M Midori', 159000, 5, 'picture\\vi\\vi5.png', 7, '2025-02-20 07:35:03'),
(24, 'Đồng hồ 1', 'Đồng hồ nam dây da PABLO RAEZ dạ quang chống nước lịch sự đơn giản U850 CARIENT', 209000, 5, 'picture\\dongho\\dongho1.png', 8, '2025-02-20 07:35:29'),
(25, 'Đồng hồ 2', 'Đồng hồ đôi nam nữ đeo tay cặp chính hãng Halei dây kim loại đẹp vàng giá rẻ thời trang', 430000, 5, 'picture\\dongho\\dongho2.png', 8, '2025-03-12 15:00:21'),
(26, 'Đồng hồ 3', 'ĐỒNG HỒ NAM NỮ CHENXI-Hàng Chính Hãng - Dây Thép Đúc Đặc Không Gỉ - Mặt Chống Xước - Đồng Hồ Chống Nước ( Mã: CX01)', 139000, 5, 'picture\\dongho\\dongho3.png', 8, '2025-02-20 07:35:41'),
(27, 'Đồng hồ 4', 'Đồng hồ đôi nam nữ Halei dây da đeo tay cao cấp đẹp mặt nhỏ số đá viền vàng chính hãng', 255000, 5, 'picture\\dongho\\dongho4.png', 8, '2025-02-20 07:35:52'),
(28, 'Đồng hồ 5', 'Skmei Chính Thức 1688 Phong Trào Nhật Bản Thạch Anh Kỹ Thuật Số Nam Đồng Hồ Đeo Tay Lịch Chrono LED Hiển Thị Nam Nữ Đồng Hồ', 275000, 5, 'picture\\dongho\\dongho5.png', 8, '2025-02-20 07:35:58'),
(29, 'Đồng hồ TM 1', 'Đồng hồ thông minh LAXASFIT S9 Max Đồng hồ thể thao nam nữ chính hãng 2.19 ” HD Nhận màn hình cảm ứng / Gọi số Phát nhạc Tin', 156000, 5, 'picture\\donghotm\\donghotm1.png', 9, '2025-02-20 07:47:14'),
(30, 'Đồng hồ TM 2', 'Đồng hồ thông minh PKSAIGON T800 PRM Nghe gọi Chơi game Nhận thông báo Theo dõi sức khỏe Chống nước cho Nam và Nữ', 118000, 5, 'picture\\donghotm\\donghotm2.png', 9, '2025-02-20 07:47:21'),
(31, 'Đồng hồ TM 3', 'Đồng Hồ Thông Minh Thế Hệ Mới T800, Nghe Gọi Kết Nối Điện Thoại Nhận Thông Báo không cần lắp sim', 120000, 5, 'picture\\donghotm\\donghotm3.png', 9, '2025-02-20 07:47:30'),
(32, 'Đồng hồ TM 4', 'Đồng hồ thông minh 8 Ultra Series 8 Đồng hồ thông minh kỹ thuật số dành cho nam Gps Smartwatch Bluetooth chống nước Chế độ thể thao', 163000, 5, 'picture\\donghotm\\donghotm4.png', 9, '2025-02-20 07:47:45'),
(33, 'Đồng hồ TM 5', 'Đồng Hồ Thông Minh Nghe Gọi Bluetooth T800 ProMax , Viền Thép Tràn Viền,Thay hình nền, Dành cho mọi lứa tuổi', 239000, 5, 'picture\\donghotm\\donghotm5.png', 9, '2025-02-20 07:47:54'),
(34, 'Da 1', 'Kem dưỡng da mặt nhau thai Seimy - Diamond Luxury Cream 30g', 140100, 5, 'picture\\da\\da1.png', 10, '2025-02-20 08:27:03'),
(35, 'Da 2', 'Sữa rửa mặt cho nam sạch dầu nhờn ngừa mụn Men Stay Simplicity Facial Cleanser 100g', 141000, 5, 'picture\\da\\da2.png', 10, '2025-02-20 08:27:08'),
(36, 'Da 3', 'Kem body trắng da Herbal Natural [ 300GR ][ Trắng bật tông chỉ sau 14 ngày ]', 148000, 5, 'picture\\da\\da3.png', 10, '2025-02-20 08:27:14'),
(37, 'Da 4', 'Nước tẩy trang làm sạch, dưỡng ẩm cho mọi loại da Loreal LOreal 3-in-1 Micellar Water 400ml', 89000, 5, 'picture\\da\\da4.png', 10, '2025-02-20 08:27:21'),
(38, 'Da 5', 'Gel Giảm Mụn Và Thâm Cafuné Essence 15gram', 136000, 5, 'picture\\da\\da5.png', 10, '2025-02-20 08:27:26'),
(39, 'Mắt 1', 'Phấn phủ CARSLAN dạng bột từ tính kiềm dầu màu đen chống nước chống mồ hôi che phủ bóng dầu cho mặt 8g', 350000, 5, 'picture\\mat\\mat1.png', 11, '2025-03-12 14:49:39'),
(40, 'Mắt 2', 'Bảng phấn mắt 9 màu Matte Pearlescent Earth Color Fine Flashing Blue Purple Smoky Eyeshadow', 24200, 5, 'picture\\mat\\mat2.png', 11, '2025-02-20 08:29:10'),
(41, 'Mắt 3', 'Set 2 Kính Áp Tròng 0~8.00 Màu Xám Nâu 14.0mm Với Tròng Kính Mềm', 55350, 5, 'picture\\mat\\mat3.png', 11, '2025-02-20 08:29:36'),
(42, 'Mắt 4', 'FOCALLURE Bút kẻ mắt nước siêu mượt chống thấm nước 0.6g', 69000, 5, 'picture\\mat\\mat4.png', 11, '2025-02-20 08:29:43'),
(43, 'Mắt 5', 'Mi Giả Cụm Tự Nhiên Douyin Tái Sử Dụng Nhiều Lần Thuỷ Mi GREEN (Tặng 1 keo nhíp cho 1 đơn hàng)', 19500, 5, 'picture\\mat\\mat5.png', 11, '2025-02-20 08:29:50'),
(44, 'Môi 1', 'Tinh Chất (Serum) Giảm Thâm Môi, Dưỡng Hồng Môi Dạng Lăn Giúp Dưỡng Ẩm, Môi Sáng Màu SKINLAX ( 10ml )', 161200, 5, 'picture\\moi\\moi1.png', 12, '2025-02-20 08:39:02'),
(45, 'Môi 2', 'Son Dưỡng Môi DHC LipCream Không Màu Giúp Môi Mềm Mại Giảm Thâm Và Hồng Môi 1.5g Sammishop', 85000, 5, 'picture\\moi\\moi2.png', 12, '2025-02-20 08:39:08'),
(46, 'Môi 3', 'Mặt Nạ Ngủ Dưỡng Môi SKINLAX (10g)', 140800, 5, 'picture\\moi\\moi3.png', 12, '2025-02-20 08:39:12'),
(47, 'Môi 4', 'Son COLORKEY Watery Tint Bền Màu Lâu Trôi, Không Dính Cốc, Siêu Mịn Môi 1.8g', 139000, 5, 'picture\\moi\\moi4.png', 12, '2025-02-20 08:39:19'),
(48, 'Môi 5', 'FOCALLURE Son Tint Siêu Căng Bóng Mọng Nước Lâu Trôi 2g', 119000, 5, 'picture\\moi\\moi5.png', 12, '2025-02-20 08:39:27'),
(49, 'Dụng cụ 1', '(Tặng Đệm Cao Su Thay Thế) FOCALLURE Dụng Cụ Bấm Lông Mi Giúp Hàng Mi Cong 32g', 39000, 5, 'picture\\dungcu\\dungcu1.png', 13, '2025-02-20 08:50:33'),
(50, 'Dụng cụ 2', 'Bộ cọ trang điểm cá nhân GUVIET set 14 cây màu xám bạc có bao da (cốc)', 197000, 5, 'picture\\dungcu\\dungcu2.png', 13, '2025-02-20 08:50:40'),
(51, 'Dụng cụ 3', 'FOCALLURE Mút tán trang điểm đa chức năng không mùi cao su mút tán mềm mịn 20g', 26999, 5, 'picture\\dungcu\\dungcu3.png', 13, '2025-02-20 08:50:48'),
(52, 'Dụng cụ 4', 'Derf Set 10 Cọ Trang Điểm Derf Chuyên Nghiệp Chất Lượng Cao', 57000, 5, 'picture\\dungcu\\dungcu4.png', 13, '2025-02-20 08:50:55'),
(53, 'Dụng cụ 5', 'Bán Chạy Trong Dòng Cọ Nền Siêu Mỏng Không Ăn Bột Đầu Phẳng Liền Mạch Chất Lỏng Nền Cọ Trang Điểm Đầu Phẳng', 15000, 5, 'picture\\dungcu\\dungcu5.png', 13, '2025-02-20 08:51:01'),
(54, 'Bông tai 1', 'Bông Tai Bạc 925 Hanada Mang 2 Đầu Đá/Bi Tròn Chui Vặn Đeo Nam Nữ 0801 E6', 56000, 5, 'picture\\bongtai\\bongtai1.png', 17, '2025-02-20 08:59:57'),
(55, 'Bông tai 2', 'Khuyên tai nam bạc 925 thanh thẳng Henry đính đá nhiều size unisex (1 chiếc) | GEMY SILVER KN100', 75, 5, 'picture\\bongtai\\bongtai2.png', 17, '2025-02-20 09:00:04'),
(56, 'Bông tai 3', 'Khuyên tai bạc unisex TLEE nạm đá tròn bản to đính đá sang trọng TLEE JEWELRY B0138', 80, 5, 'picture\\bongtai\\bongtai3.png', 17, '2025-02-20 09:00:10'),
(57, 'Bông tai 4', 'Khuyên tai tròn titan G-dragon cực chất', 8900, 5, 'picture\\bongtai\\bongtai4.png', 17, '2025-02-20 09:00:14'),
(58, 'Bông tai 5', 'Khuyên tai nam nữ bạc 925 hình chữ thập thánh giá mắt xích unisex dáng dài cá tính (1 chiếc) | GEMY SILVER K114', 65, 5, 'picture\\bongtai\\bongtai5.png', 17, '2025-02-20 09:00:18'),
(59, 'Nhẫn 1', '[R2] Nhẫn nam nữ Basic Cuban Ring V2 - Thép không gỉ - Phụ kiện trang sức Unisex Apous', 48000, 5, 'picture\\nhan\\nhan1.png', 16, '2025-02-20 15:22:11'),
(60, 'Nhẫn 2', 'N008 - Nhẫn nam nữ Basic trơn 4mm màu bạc - Thép Titan - Phụ kiện trang sức Unisex Apous', 23000, 5, 'picture\\nhan\\nhan2.png', 16, '2025-02-20 15:22:17'),
(61, 'Nhẫn 3', 'Nhẫn BẠC 925 Đá Trụ 6MM Chuẩn 6A 120 Lát Cắt (Bảo hành Trọn Đời) Grace Trang Sức Bạc Đi Tiêc 1028 N11', 89000, 5, 'picture\\nhan\\nhan3.png', 16, '2025-02-20 15:22:23'),
(62, 'Nhẫn 4', 'Thiết kế thích hợp mở tình yêu đan xen đôi nhẫn nam nữ cá tính cặp nhẫn lụa xanh quấn nhẫn cưới', 29700, 5, 'picture\\nhan\\nhan4.png', 16, '2025-02-20 15:22:27'),
(63, 'Nhẫn 5', '2 Chiếc Hoạt Hình Anime Cặp Đôi Bộ Nhẫn Hợp Kim Graffiti Phụ Kiện Trang Sức Có Thể Điều Chỉnh', 19000, 5, 'picture\\nhan\\nhan05.png', 16, '2025-02-22 09:48:45'),
(64, 'Vòng cổ 1', 'Dây Chuyền Nam Titan Không Gỉ Tavi Studio Thời Trang Cá Tính Dây Kim Loại Màu Bạc Cao Cấp - Vòng Cổ Tổng hợp', 9000, 5, 'picture\\vongco\\vongco1.png', 15, '2025-02-20 15:22:54'),
(65, 'Vòng cổ 2', 'Vòng cổ MAYEBE LAVEND Mạ Bạc Nhiều Lớp unisex y2k Phong Cách hip hop', 30000, 5, 'picture\\vongco\\vongco2.png', 15, '2025-02-20 15:22:59'),
(66, 'Vòng cổ 3', 'Z Vòng cổ Mạ Vàng 18K Mặt Hình Nhện Cá Tính Cho Nam', 22000, 5, 'picture\\vongco\\vongco3.png', 15, '2025-02-20 15:23:03'),
(67, 'Vòng cổ 4', 'Vòng Cổ Đen Trắng Ma Cặp Đôi Vòng Cổ Đôi Vòng Cổ Ngọt Ngào Mát Bạn Gái Vòng Cổ Cha Mẹ-Con Tất Cả Trận Đấu Phong Cách Mới Dây', 11000, 5, 'picture\\vongco\\vongco4.png', 15, '2025-02-20 15:23:07'),
(68, 'Vòng cổ 5', '1 Thời Trang Hip Hop Phong Cách Thoáng Mát Thép Không Gỉ Hình Học Cặp Đôi Mặt Dây Chuyền Vuông Tam Giác Mặt Dây Chuyền', 19800, 5, 'picture\\vongco\\vongco5.png', 15, '2025-02-20 15:23:12'),
(69, 'Vòng lắc 1', 'Vòng Tay Nam Nữ Lắc Tay Thép Titan Không Gỉ Phong Cách Hàn Quốc Đơn Giản Thời Trang Tavi Studio Nhiều Mẫu Tùy Chọn', 45000, 5, 'picture\\vonglac\\vonglac1.png', 14, '2025-02-22 09:39:34'),
(70, 'Vòng lắc 2', 'Vintage Rock Cross Vòng Tay Da Nam Nữ Thời Trang Thép Không Gỉ Nhiều Lớp Đính Hạt Vòng Tay PU Punk Đảng Phụ Kiện Trang', 20900, 5, 'picture\\vonglac\\vonglac2.png', 14, '2025-02-22 09:39:45'),
(71, 'Vòng lắc 3', '[KHÔNG ĐEN GỈ] Vòng tay thép Titan mạ vàng cỏ bốn lá may mắn thời trang nữ tính Mely TT71', 65000, 5, 'picture\\vonglac\\vonglac3.png', 14, '2025-02-22 09:39:58'),
(72, 'Vòng lắc 4', '1 Thời Trang Dày Foxtail Nam Vòng Tay Hợp Thời Trang Độc Đáo Phong Cách Hip-Hop Vòng Tay Đơn Giản Độc Đoán Tròn Xương Rắn', 14300, 5, 'picture\\vonglac\\vonglac4.png', 14, '2025-02-22 09:40:14'),
(73, 'Vòng lắc 5', 'Vòng tay mạ bạc 925 mặt dát đá pha lê phong cách nữ Hàn Quốc', 23100, 5, 'picture\\vonglac\\vonglac5.png', 14, '2025-02-22 09:40:30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `spyeuthich`
--

CREATE TABLE `spyeuthich` (
  `idsplike` int(11) NOT NULL,
  `idkh` int(11) NOT NULL,
  `idsp` int(11) NOT NULL,
  `thoigian` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thanhtoan`
--

CREATE TABLE `thanhtoan` (
  `idtt` int(11) NOT NULL,
  `iddh` int(11) NOT NULL,
  `phuongthuctt` varchar(50) NOT NULL,
  `trangthai` varchar(50) NOT NULL,
  `magiaodich` varchar(50) NOT NULL,
  `thoigian` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thanhtoan`
--

INSERT INTO `thanhtoan` (`idtt`, `iddh`, `phuongthuctt`, `trangthai`, `magiaodich`, `thoigian`) VALUES
(1, 1, 'VNPay', 'Đã thanh toán', 'VNPay_20250216001', '2025-02-25 07:10:37');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
--

CREATE TABLE `user` (
  `iduser` int(11) NOT NULL,
  `hoten` varchar(50) NOT NULL,
  `tendn` varchar(50) NOT NULL,
  `anh` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `matkhau` varchar(100) NOT NULL,
  `sdt` varchar(11) NOT NULL,
  `diachi` text NOT NULL,
  `quyen` varchar(50) NOT NULL,
  `thoigian` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`iduser`, `hoten`, `tendn`, `anh`, `email`, `matkhau`, `sdt`, `diachi`, `quyen`, `thoigian`) VALUES
(1, 'Nguyễn Tuấn Anh', 'anh', 'picture\\\\conkhi.png', 'anh@gmail.com', '$2y$10$YbGRLgwr4RAAHleeMpc4NeBRFQzOBT7vMN89sIAPaeRsAirBSMmlu', '0987654321', 'Cà Mau', '0', '2025-03-08 06:27:30'),
(2, 'Trần Phương Thế', 'the', 'picture\\\\conkhi01.png', 'the@gmail.com', '$2b$12$NcJCQMHH9/PaALCMAsHvZetQ8kXlUqwUkMo0NZW98acki53OOSzU6\n', '0987654321', 'Vĩnh Long', '0', '2025-03-07 13:12:43'),
(3, 'Nguyễn Thị Thùy Dương', 'duong', 'picture\\\\\\\\conkhi02.png', 'duong@gmail.com', '$2b$12$NcJCQMHH9/PaALCMAsHvZetQ8kXlUqwUkMo0NZW98acki53OOSzU6\n', '0987654321', 'Bạc Liêu', '1', '2025-03-07 13:13:18'),
(5, 'Trần Ngọc Thắng', 'thang', 'picture\\\\conkhi.png', 'thang@gmail.com', '$2b$12$NcJCQMHH9/PaALCMAsHvZetQ8kXlUqwUkMo0NZW98acki53OOSzU6\n', '1234567890', 'Trà Vinh', '1', '2025-03-07 13:13:33'),
(7, 'Trần Thanh Tâm', 'tam', 'picture\\\\conkhi01.png', 'tam@gmail.com', '$2b$12$NcJCQMHH9/PaALCMAsHvZetQ8kXlUqwUkMo0NZW98acki53OOSzU6', '0987654321', 'Đồng Nai', '1', '2025-03-07 13:13:45'),
(8, 'Bùi Hữu Phước', 'phuoc', 'picture\\\\conkhi02.png', 'phuoc@gmail.com', '$2b$12$NcJCQMHH9/PaALCMAsHvZetQ8kXlUqwUkMo0NZW98acki53OOSzU6', '0987654321', 'Bình Dương', '1', '2025-03-07 13:14:01'),
(14, 'nguyễn văn a', 'a', 'picture\\\\conkhi01.png', 'a@gmail.com', '$2y$10$fIo8YoZA7XALmIMTMyysyOvB/q3Jf0ev9xPQCEUMDEDAqzyjZMjOu', '0702804594', 'vl', '1', '2025-03-08 09:48:36'),
(15, 'nguyễn văn b', 'b', 'picture/logoTD.png', 'b@gmail.com', '$2y$10$F8JfvvYcyK1ghv2DUJaI7uD/HwgXijLHH0g/xOMafUji80EjBicG6', '0702804594', 'vl', '1', '2025-03-08 09:30:45'),
(16, 'nguyễn văn c', 'c', 'picture/conkhi.png', 'c@gmail.com', '$2y$10$8fZNH8D0WFy8LWfanTWszO2NcB6RE/ZOiW6fVHZxM/k2cTq7Lj0xK', '0702804594', 'cà mau', '1', '2025-03-08 15:10:01'),
(17, 'Phan Minh Nhật', 'nhat', 'picture\\\\\\\\screenshot_1731862949.png', 'minecraftnhat9@gmail.com', '$2y$10$wfbBgMLf2FkY07fMQT2M4uICVQJEEdj0cpbHSIHSTh7YmmQRXKzFa', '0975540614', 'Hau Giang', '1', '2025-03-24 05:15:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vanchuyen`
--

CREATE TABLE `vanchuyen` (
  `idvc` int(11) NOT NULL,
  `phuongthuc` varchar(50) NOT NULL,
  `trangthai` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `vanchuyen`
--

INSERT INTO `vanchuyen` (`idvc`, `phuongthuc`, `trangthai`) VALUES
(1, 'Giao hàng nhanh', 1),
(2, 'Giao hàng tiết kiệm', 1),
(3, 'Miễn phí vận chuyển', 1);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cauhinh_giohang`
--
ALTER TABLE `cauhinh_giohang`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `chattructuyen`
--
ALTER TABLE `chattructuyen`
  ADD PRIMARY KEY (`idchat`);

--
-- Chỉ mục cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD PRIMARY KEY (`idctdh`),
  ADD KEY `fkdonhang` (`iddh`),
  ADD KEY `fksanpham` (`idsp`);

--
-- Chỉ mục cho bảng `danhgia`
--
ALTER TABLE `danhgia`
  ADD PRIMARY KEY (`iddg`);

--
-- Chỉ mục cho bảng `danhmucsp`
--
ALTER TABLE `danhmucsp`
  ADD PRIMARY KEY (`iddm`);

--
-- Chỉ mục cho bảng `diachigiaohang`
--
ALTER TABLE `diachigiaohang`
  ADD PRIMARY KEY (`iddc`),
  ADD KEY `fkkhhang` (`idkh`);

--
-- Chỉ mục cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD PRIMARY KEY (`iddh`),
  ADD KEY `fkkhachhang` (`idkh`);

--
-- Chỉ mục cho bảng `giohang`
--
ALTER TABLE `giohang`
  ADD PRIMARY KEY (`idgh`),
  ADD KEY `fkkh` (`idkh`),
  ADD KEY `fksp` (`idsp`);

--
-- Chỉ mục cho bảng `magiamgia`
--
ALTER TABLE `magiamgia`
  ADD PRIMARY KEY (`idmgg`);

--
-- Chỉ mục cho bảng `magiamgia_chitiet`
--
ALTER TABLE `magiamgia_chitiet`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Chỉ mục cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`idsp`),
  ADD KEY `fkdanhmuc` (`iddm`);

--
-- Chỉ mục cho bảng `spyeuthich`
--
ALTER TABLE `spyeuthich`
  ADD PRIMARY KEY (`idsplike`),
  ADD KEY `fk_khachhang` (`idkh`);

--
-- Chỉ mục cho bảng `thanhtoan`
--
ALTER TABLE `thanhtoan`
  ADD PRIMARY KEY (`idtt`),
  ADD KEY `fkdonh` (`iddh`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`iduser`);

--
-- Chỉ mục cho bảng `vanchuyen`
--
ALTER TABLE `vanchuyen`
  ADD PRIMARY KEY (`idvc`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `cauhinh_giohang`
--
ALTER TABLE `cauhinh_giohang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `chattructuyen`
--
ALTER TABLE `chattructuyen`
  MODIFY `idchat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  MODIFY `idctdh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT cho bảng `danhgia`
--
ALTER TABLE `danhgia`
  MODIFY `iddg` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `danhmucsp`
--
ALTER TABLE `danhmucsp`
  MODIFY `iddm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT cho bảng `diachigiaohang`
--
ALTER TABLE `diachigiaohang`
  MODIFY `iddc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `donhang`
--
ALTER TABLE `donhang`
  MODIFY `iddh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT cho bảng `giohang`
--
ALTER TABLE `giohang`
  MODIFY `idgh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `magiamgia`
--
ALTER TABLE `magiamgia`
  MODIFY `idmgg` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `magiamgia_chitiet`
--
ALTER TABLE `magiamgia_chitiet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  MODIFY `idsp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT cho bảng `spyeuthich`
--
ALTER TABLE `spyeuthich`
  MODIFY `idsplike` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `thanhtoan`
--
ALTER TABLE `thanhtoan`
  MODIFY `idtt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `user`
--
ALTER TABLE `user`
  MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `vanchuyen`
--
ALTER TABLE `vanchuyen`
  MODIFY `idvc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `user` (`iduser`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `user` (`iduser`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
