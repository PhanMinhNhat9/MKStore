-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2025 at 05:01 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quanlybanpk`
--
CREATE DATABASE IF NOT EXISTS `quanlybanpk` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `quanlybanpk`;

-- --------------------------------------------------------

--
-- Table structure for table `chattructuyen`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chitietdonhang`
--

CREATE TABLE `chitietdonhang` (
  `idctdh` int(11) NOT NULL,
  `iddh` int(11) NOT NULL,
  `idsp` int(11) NOT NULL,
  `soluong` int(11) NOT NULL,
  `gia` decimal(10,0) NOT NULL,
  `giagoc` decimal(10,0) NOT NULL,
  `giagiam` decimal(10,0) NOT NULL,
  `danhgia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `chitietdonhang`
--

INSERT INTO `chitietdonhang` (`idctdh`, `iddh`, `idsp`, `soluong`, `gia`, `giagoc`, `giagiam`, `danhgia`) VALUES
(1, 1, 8, 1, 115000, 115000, 115000, 0),
(2, 2, 8, 1, 115000, 115000, 115000, 1),
(3, 2, 8, 1, 70840, 70840, 70840, 1),
(4, 2, 10, 1, 89000, 89000, 89000, 1),
(5, 2, 11, 1, 195000, 195000, 195000, 1),
(6, 3, 10, 2, 178000, 89000, 89000, 1),
(7, 3, 8, 2, 230000, 115000, 115000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `danhgia`
--

CREATE TABLE `danhgia` (
  `iddg` int(11) NOT NULL,
  `idctdh` int(11) NOT NULL,
  `idkh` int(11) NOT NULL,
  `idsp` int(11) NOT NULL,
  `sosao` int(11) NOT NULL,
  `noidung` text NOT NULL,
  `thoigian` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `danhgia`
--

INSERT INTO `danhgia` (`iddg`, `idctdh`, `idkh`, `idsp`, `sosao`, `noidung`, `thoigian`) VALUES
(4, 2, 3, 10, 5, 'ghrr', '2025-05-01 06:58:25'),
(5, 4, 3, 8, 4, 'xấu hoắt', '2025-05-01 06:59:32'),
(6, 5, 3, 11, 3, 'gớm', '2025-05-01 00:00:40');

-- --------------------------------------------------------

--
-- Table structure for table `danhmucsp`
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
-- Dumping data for table `danhmucsp`
--

INSERT INTO `danhmucsp` (`iddm`, `tendm`, `loaidm`, `icon`, `mota`, `thoigian`) VALUES
(1, 'Gấu bông', 0, 'icon/bee.png', 'Chuyên các loại gấu bông mọi kích cỡ!', '2025-04-29 17:09:09'),
(2, 'Gấu bông trang trí', 1, 'icon/conkhi02.png', 'Chuyên các loại gấu bông có kích thước nhỏ và vừa phải, với mục đích trang trí.', '2025-04-29 17:09:09'),
(3, 'Balo - Tú - Ví', 0, 'icon/lucky-bag.png', '', '2025-04-29 17:09:09'),
(4, 'Balo thời trang Nam Nữ', 3, 'icon/schoolbag.png', '', '2025-04-30 06:33:36'),
(5, 'Túi Nam Nữ', 3, 'icon/package.png', '', '2025-04-30 06:33:49'),
(6, 'Ví thời trang cao cấp', 3, 'icon/wallet.png', '', '2025-04-30 06:33:56'),
(7, 'Đồng hồ', 0, 'icon/wristwatch.png', '', '2025-04-29 17:09:09'),
(8, 'Đồng hồ Nam Nữ', 7, 'icon/wrist-watch.png', '', '2025-04-30 06:34:02'),
(9, 'Đồng hồ Thông minh', 7, 'icon/fitness-tracker.png', '', '2025-04-30 06:34:08'),
(10, 'Trang điểm', 0, 'icon/makeup.png', '', '2025-04-29 17:09:09'),
(11, 'Dành cho da', 10, 'icon/night-cream.png', '', '2025-04-30 06:34:15'),
(12, 'Dành cho mắt', 10, 'icon/eye.png', '', '2025-04-30 06:34:19'),
(13, 'Dành cho môi', 10, 'icon/lipstick.png', '', '2025-04-30 06:34:23'),
(14, 'Dụng cụ trang điểm', 10, 'icon/cosmetics.png', '', '2025-04-30 06:34:26'),
(15, 'Trang trí', 0, 'icon/stars.png', '', '2025-04-29 17:09:09'),
(16, 'Vòng/Lắc', 15, 'icon/bracelet.png', '', '2025-04-30 06:34:33'),
(17, 'Nhẫn', 15, 'icon/wedding-ring.png', '', '2025-04-30 06:34:36'),
(18, 'Hoa tai', 15, 'icon/earrings.png', '', '2025-04-30 06:34:39');

-- --------------------------------------------------------

--
-- Table structure for table `donhang`
--

CREATE TABLE `donhang` (
  `iddh` int(11) NOT NULL,
  `sdt` varchar(30) NOT NULL,
  `tenkh` varchar(30) NOT NULL,
  `tongtien` decimal(10,0) NOT NULL,
  `trangthai` varchar(50) NOT NULL,
  `phuongthuctt` varchar(50) NOT NULL,
  `thoigian` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `donhang`
--

INSERT INTO `donhang` (`iddh`, `sdt`, `tenkh`, `tongtien`, `trangthai`, `phuongthuctt`, `thoigian`) VALUES
(1, '0987654321', 'Trần Thanh Tâm', 115000, 'Đã thanh toán', 'Chuyển khoản ngân hàng', '2025-05-01 00:01:52'),
(2, '0987654321', 'Trần Thanh Tâm', 469840, 'Đã thanh toán', 'Chuyển khoản ngân hàng', '2025-05-01 00:38:21'),
(3, '0987654321', 'Trần Thanh Tâm', 408000, 'Đã thanh toán', 'Chuyển khoản ngân hàng', '2025-05-01 11:51:30');

-- --------------------------------------------------------

--
-- Table structure for table `giohang`
--

CREATE TABLE `giohang` (
  `idgh` int(11) NOT NULL,
  `idkh` int(11) NOT NULL,
  `idsp` int(11) NOT NULL,
  `tensp` varchar(30) NOT NULL,
  `giagoc` int(11) NOT NULL,
  `giagiam` int(11) NOT NULL,
  `soluong` int(11) NOT NULL,
  `thanhtien` decimal(10,0) NOT NULL,
  `thoigian` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `giohang`
--

INSERT INTO `giohang` (`idgh`, `idkh`, `idsp`, `tensp`, `giagoc`, `giagiam`, `soluong`, `thanhtien`, `thoigian`) VALUES
(8, 3, 10, 'Túi Đeo Chéo Nam Nữ MLB Chính ', 89000, 89000, 1, 89000, '2025-05-01 08:40:35'),
(9, 3, 11, 'Túi đeo chéo BRANDON thời tr', 195000, 195000, 1, 195000, '2025-05-02 05:04:39');

-- --------------------------------------------------------

--
-- Table structure for table `hoadon`
--

CREATE TABLE `hoadon` (
  `idhd` int(11) NOT NULL,
  `iddh` int(11) NOT NULL,
  `idnv` int(11) NOT NULL,
  `tiennhan` decimal(10,0) NOT NULL,
  `tienthoi` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `hoadon`
--

INSERT INTO `hoadon` (`idhd`, `iddh`, `idnv`, `tiennhan`, `tienthoi`) VALUES
(1, 3, 3, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `khxoatk`
--

CREATE TABLE `khxoatk` (
  `idkh` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `trangthai` int(11) NOT NULL,
  `ngaykh` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `magiamgia`
--

CREATE TABLE `magiamgia` (
  `idmgg` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `phantram` int(11) NOT NULL,
  `ngayhieuluc` date NOT NULL,
  `ngayketthuc` date NOT NULL,
  `giaapdung` int(11) NOT NULL,
  `iddm` int(11) NOT NULL,
  `soluong` int(11) NOT NULL,
  `thoigian` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `qrcode`
--

CREATE TABLE `qrcode` (
  `idqr` int(11) NOT NULL,
  `qrcode` varchar(50) NOT NULL,
  `idsp` int(11) NOT NULL,
  `thoigian` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `qrcode`
--

INSERT INTO `qrcode` (`idqr`, `qrcode`, `idsp`, `thoigian`) VALUES
(1, 'sp_1.png', 1, '2025-03-25 10:31:49'),
(2, 'sp_2.png', 2, '2025-03-25 10:31:49'),
(3, 'sp_3.png', 3, '2025-03-25 10:31:49'),
(4, 'sp_4.png', 4, '2025-03-25 10:31:49'),
(5, 'sp_5.png', 5, '2025-03-25 10:31:49'),
(6, 'sp_6.png', 6, '2025-03-25 10:31:49'),
(7, 'sp_7.png', 7, '2025-03-25 10:31:49'),
(8, 'sp_8.png', 8, '2025-03-25 10:31:49'),
(9, 'sp_9.png', 9, '2025-03-25 10:31:49'),
(10, 'sp_10.png', 10, '2025-03-25 10:31:49'),
(11, 'sp_11.png', 11, '2025-03-25 10:31:49'),
(12, 'sp_12.png', 12, '2025-03-25 10:31:49'),
(13, 'sp_13.png', 13, '2025-03-25 10:31:49'),
(14, 'sp_14.png', 14, '2025-03-25 10:31:49'),
(15, 'sp_15.png', 15, '2025-03-25 10:31:49'),
(16, 'sp_16.png', 16, '2025-03-25 10:31:49'),
(17, 'sp_17.png', 17, '2025-03-25 10:31:49'),
(18, 'sp_18.png', 18, '2025-03-25 10:31:49'),
(19, 'sp_19.png', 19, '2025-03-25 10:31:49'),
(20, 'sp_20.png', 20, '2025-03-25 10:31:49'),
(21, 'sp_21.png', 21, '2025-03-25 10:31:49'),
(22, 'sp_22.png', 22, '2025-03-25 10:31:49'),
(23, 'sp_23.png', 23, '2025-03-25 10:31:49'),
(24, 'sp_24.png', 24, '2025-03-25 10:31:49'),
(25, 'sp_25.png', 25, '2025-03-25 10:31:49'),
(26, 'sp_26.png', 26, '2025-03-25 10:31:49'),
(27, 'sp_27.png', 27, '2025-03-25 10:31:49'),
(28, 'sp_28.png', 28, '2025-03-25 10:31:49'),
(29, 'sp_29.png', 29, '2025-03-25 10:31:49'),
(30, 'sp_30.png', 30, '2025-03-25 10:31:49'),
(31, 'sp_31.png', 31, '2025-03-25 10:31:49'),
(32, 'sp_32.png', 32, '2025-03-25 10:31:49'),
(33, 'sp_33.png', 33, '2025-03-25 10:31:49'),
(34, 'sp_34.png', 34, '2025-03-25 10:31:49'),
(35, 'sp_35.png', 35, '2025-03-25 10:31:49'),
(36, 'sp_36.png', 36, '2025-03-25 10:31:49'),
(37, 'sp_37.png', 37, '2025-03-25 10:31:49'),
(38, 'sp_38.png', 38, '2025-03-25 10:31:49'),
(39, 'sp_39.png', 39, '2025-03-25 10:31:49'),
(40, 'sp_40.png', 40, '2025-03-25 10:31:49'),
(41, 'sp_41.png', 41, '2025-03-25 10:31:49'),
(42, 'sp_42.png', 42, '2025-03-25 10:31:49'),
(43, 'sp_43.png', 43, '2025-03-25 10:31:49'),
(44, 'sp_44.png', 44, '2025-03-25 10:31:49'),
(45, 'sp_45.png', 45, '2025-03-25 10:31:49'),
(46, 'sp_46.png', 46, '2025-03-25 10:31:49'),
(47, 'sp_47.png', 47, '2025-03-25 10:31:49'),
(48, 'sp_48.png', 48, '2025-03-25 10:31:49'),
(49, 'sp_49.png', 49, '2025-03-25 10:31:49'),
(50, 'sp_50.png', 50, '2025-03-25 10:31:49'),
(51, 'sp_51.png', 51, '2025-03-25 10:31:49'),
(52, 'sp_52.png', 52, '2025-03-25 10:31:49'),
(53, 'sp_53.png', 53, '2025-03-25 10:31:49'),
(54, 'sp_54.png', 54, '2025-03-25 10:31:49'),
(55, 'sp_55.png', 55, '2025-03-25 10:31:49'),
(56, 'sp_56.png', 56, '2025-03-25 10:31:49'),
(57, 'sp_57.png', 57, '2025-03-25 10:31:49'),
(58, 'sp_58.png', 58, '2025-03-25 10:31:49'),
(59, 'sp_59.png', 59, '2025-03-25 10:31:49'),
(60, 'sp_60.png', 60, '2025-03-25 10:31:49'),
(61, 'sp_61.png', 61, '2025-03-25 10:31:49'),
(62, 'sp_62.png', 62, '2025-03-25 10:31:49'),
(63, 'sp_63.png', 63, '2025-03-25 10:31:49'),
(64, 'sp_64.png', 64, '2025-03-25 10:31:49'),
(65, 'sp_65.png', 65, '2025-03-25 10:31:49'),
(66, 'sp_66.png', 66, '2025-03-25 10:31:49'),
(67, 'sp_67.png', 67, '2025-03-25 10:31:49'),
(68, 'sp_68.png', 68, '2025-03-25 10:31:49');

-- --------------------------------------------------------

--
-- Table structure for table `sanpham`
--

CREATE TABLE `sanpham` (
  `idsp` int(11) NOT NULL,
  `tensp` text NOT NULL,
  `giaban` decimal(10,0) NOT NULL,
  `soluong` int(11) NOT NULL,
  `anh` varchar(50) NOT NULL,
  `iddm` int(11) NOT NULL,
  `thoigianthemsp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `sanpham`
--

INSERT INTO `sanpham` (`idsp`, `tensp`, `giaban`, `soluong`, `anh`, `iddm`, `thoigianthemsp`) VALUES
(1, 'Balo Thời Trang Kiểu Ulzzang Vải Chống Thấm Cao Cấp. Cặp Balo Cho Nam Nữ Đa năng Mintas 284', 159000, 10, 'picture/balo1.png', 3, '2025-04-24 16:07:42'),
(2, 'Mẫu Balo Thời Trang Unisex Siêu Rộng Chống Nước, Có ngăn laptop chống sóc, Cỡ lớn đa năng đựng laptop hoặc đựng quần áo', 109000, 10, 'picture/balo2.png', 3, '2025-04-06 14:25:59'),
(3, 'BALO UNISEX BoyMusic Và OnePiece THỜI TRANG. Balo Vải 3 Lớp Dày Chống Thấm Nước Mintas 300.', 109000, 10, 'picture/balo3.png', 3, '2025-04-06 14:25:59'),
(4, 'Ba Lô Laptop Tích Hợp USB Cao Cấp PRAZA BL174', 149000, 10, 'picture/balo4.png', 3, '2025-04-06 14:25:59'),
(5, 'Balo nam nữ đi học cặp đi học thời trang nam nữ đựng laptop 15.6inh Ba lô nhiều ngăn ulzzang chống nước', 250000, 10, 'picture/balo5.png', 3, '2025-04-06 14:25:59'),
(6, 'Gấu bông Lena stick cute dễ thương Lotso đủ size ADA S HOUSE', 76360, 5, 'picture/gaubong1.png', 39, '2025-04-06 14:25:59'),
(7, 'Capybara kéo mũi gấu bông mini, cabibara, capipara chảy nước mũi màu hồng Ada s House', 89460, 5, 'picture/gaubong3.png', 39, '2025-04-29 16:52:56'),
(8, 'Gấu bông chó Husky Siêu to khổng Lồ Size 1M5, Gối ôm hình thú Gòn 100%. đồ chơi nhồi bông cute cho bé, GABO TEDDY BEAR', 115000, 5, 'picture/gaubong4.png', 39, '2025-04-29 16:56:26'),
(9, 'Gấu nhồi bông Baby Three siêu dễ thương mẫu mới hottrend', 70840, 5, 'picture/gaubong5.png', 39, '2025-04-29 16:56:26'),
(10, 'Túi Đeo Chéo Nam Nữ MLB Chính Hãng Logo NY, Túi Xách Nam Boy Phố Dạng Hộp Nhiều Ngăn Vải Da Chống Nước 100% _GONZEN', 89000, 5, 'picture/tui1.png', 4, '2025-04-29 16:56:26'),
(11, 'Túi đeo chéo BRANDON thời trang cao cấp Nam Nữ chất liệu chống thấm nước | Midori For Man', 195000, 5, 'picture/tui2.png', 4, '2025-04-29 16:56:26'),
(12, '[Siêu Sale] Túi đeo chéo kiểu ngang da ipad MÀU ĐẬM da loại 1 đẹp dẻo mịn K501 Shalla [Hình thật]', 50400, 5, 'picture/tui3.png', 4, '2025-04-29 16:56:26'),
(13, 'Túi Đeo Chéo Nam Nữ Nhiều Ngăn, Nhỏ Gọn LUVIN, Thiết Kế Basic, Tiện Dụng Đi Chơi, Đi Học Đều Đẹp', 195000, 5, 'picture/tui4.png', 4, '2025-04-29 16:56:26'),
(14, 'Túi Xách Đeo Chéo KRIXI Da Nam Nữ Cao Cấp Thời Trang Unisex Chính Hãng Local Brand M STUDIO', 249000, 5, 'picture/tui5.png', 4, '2025-04-29 16:56:26'),
(15, 'Ví nam nhỏ gọn kiểu ví ngang Hàn Quốc chất liệu vải Canvas cao cấp nhiều ngăn tiện lợi', 36200, 5, 'picture/vi1.png', 5, '2025-04-29 16:56:26'),
(16, 'Ví đựng tiền mini SEEME Lace Wallet dáng ngắn', 171000, 5, 'picture/vi2.png', 5, '2025-04-29 16:56:26'),
(17, 'Ví dài nam nữ cầm tay nhiều ngăn đựng có ngăn kéo ví da mềm dập nổi cao cấp Hàn Quốc MSP 3088-3', 79000, 5, 'picture/vi3.png', 5, '2025-04-29 16:56:26'),
(18, 'Ví Ngắn Siêu Mỏng Đựng Thẻ / Bằng Lái Xe Sức Chứa Lớn Siêu Mỏng Cho Nam Và Nữ', 35640, 5, 'picture/vi4.png', 5, '2025-04-29 16:56:26'),
(19, 'Ví Da Nữ JENNIE Chống Thấm Nước Cao Cấp Chính Hãng Local Brand M Midori', 159000, 5, 'picture/vi5.png', 5, '2025-04-29 16:56:26'),
(20, 'Đồng hồ nam dây da PABLO RAEZ dạ quang chống nước lịch sự đơn giản U850 CARIENT', 209000, 5, 'picture/dongho1.png', 7, '2025-04-29 16:56:26'),
(21, 'Đồng hồ đôi nam nữ đeo tay cặp chính hãng Halei dây kim loại đẹp vàng giá rẻ thời trang', 435000, 5, 'picture/dongho2.png', 7, '2025-04-29 16:56:26'),
(22, 'ĐỒNG HỒ NAM NỮ CHENXI-Hàng Chính Hãng - Dây Thép Đúc Đặc Không Gỉ - Mặt Chống Xước - Đồng Hồ Chống Nước ( Mã: CX01)', 139000, 5, 'picture/dongho3.png', 7, '2025-04-29 16:56:26'),
(23, 'Đồng hồ đôi nam nữ Halei dây da đeo tay cao cấp đẹp mặt nhỏ số đá viền vàng chính hãng', 255000, 5, 'picture/dongho4.png', 7, '2025-04-29 16:56:26'),
(24, 'Skmei Chính Thức 1688 Phong Trào Nhật Bản Thạch Anh Kỹ Thuật Số Nam Đồng Hồ Đeo Tay Lịch Chrono LED Hiển Thị Nam Nữ Đồng Hồ', 275000, 5, 'picture/dongho5.png', 7, '2025-04-29 16:56:26'),
(25, 'Đồng hồ thông minh LAXASFIT S9 Max Đồng hồ thể thao nam nữ chính hãng 2.19 ” HD Nhận màn hình cảm ứng / Gọi số Phát nhạc Tin', 156000, 5, 'picture/donghotm1.png', 8, '2025-04-29 16:56:26'),
(26, 'Đồng hồ thông minh PKSAIGON T800 PRM Nghe gọi Chơi game Nhận thông báo Theo dõi sức khỏe Chống nước cho Nam và Nữ', 118000, 5, 'picture/donghotm2.png', 8, '2025-04-29 16:56:26'),
(27, 'Đồng Hồ Thông Minh Thế Hệ Mới T800, Nghe Gọi Kết Nối Điện Thoại Nhận Thông Báo không cần lắp sim', 120000, 5, 'picture/donghotm3.png', 8, '2025-04-29 16:56:26'),
(28, 'Đồng hồ thông minh 8 Ultra Series 8 Đồng hồ thông minh kỹ thuật số dành cho nam Gps Smartwatch Bluetooth chống nước Chế độ thể thao', 163000, 5, 'picture/donghotm4.png', 8, '2025-04-29 16:56:26'),
(29, 'Đồng Hồ Thông Minh Nghe Gọi Bluetooth T800 ProMax , Viền Thép Tràn Viền,Thay hình nền, Dành cho mọi lứa tuổi', 239000, 5, 'picture/donghotm5.png', 8, '2025-04-29 16:56:26'),
(30, 'Kem dưỡng da mặt nhau thai Seimy - Diamond Luxury Cream 30g', 140100, 6, 'picture/da1.png', 10, '2025-04-29 16:56:26'),
(31, 'Sữa rửa mặt cho nam sạch dầu nhờn ngừa mụn Men Stay Simplicity Facial Cleanser 100g', 141000, 5, 'picture/da2.png', 10, '2025-04-29 16:56:26'),
(32, 'Kem body trắng da Herbal Natural [ 300GR ][ Trắng bật tông chỉ sau 14 ngày ]', 148000, 5, 'picture/da3.png', 10, '2025-04-29 16:56:26'),
(33, 'Nước tẩy trang làm sạch, dưỡng ẩm cho mọi loại da Loreal LOreal 3-in-1 Micellar Water 400ml', 89000, 5, 'picture/da4.png', 10, '2025-04-29 16:56:26'),
(34, 'Gel Giảm Mụn Và Thâm Cafuné Essence 15gram', 136000, 5, 'picture/da5.png', 10, '2025-04-29 16:56:26'),
(35, 'Phấn phủ CARSLAN dạng bột từ tính kiềm dầu màu đen chống nước chống mồ hôi che phủ bóng dầu cho mặt 8g', 239000, 5, 'picture/mat1.png', 11, '2025-04-29 16:56:26'),
(36, 'Bảng phấn mắt 9 màu Matte Pearlescent Earth Color Fine Flashing Blue Purple Smoky Eyeshadow', 24200, 5, 'picture/mat2.png', 11, '2025-04-29 16:56:26'),
(37, 'Set 2 Kính Áp Tròng 0~8.00 Màu Xám Nâu 14.0mm Với Tròng Kính Mềm', 55350, 5, 'picture/mat3.png', 11, '2025-04-29 16:56:26'),
(38, 'FOCALLURE Bút kẻ mắt nước siêu mượt chống thấm nước 0.6g', 69000, 5, 'picture/mat4.png', 11, '2025-04-29 16:56:26'),
(39, 'Mi Giả Cụm Tự Nhiên Douyin Tái Sử Dụng Nhiều Lần Thuỷ Mi GREEN (Tặng 1 keo nhíp cho 1 đơn hàng)', 19500, 5, 'picture/mat5.png', 11, '2025-04-29 16:56:26'),
(40, 'Tinh Chất (Serum) Giảm Thâm Môi, Dưỡng Hồng Môi Dạng Lăn Giúp Dưỡng Ẩm, Môi Sáng Màu SKINLAX ( 10ml )', 161200, 5, 'picture/moi1.png', 12, '2025-04-29 16:56:26'),
(41, 'Son Dưỡng Môi DHC LipCream Không Màu Giúp Môi Mềm Mại Giảm Thâm Và Hồng Môi 1.5g Sammishop', 85000, 5, 'picture/moi2.png', 12, '2025-04-29 16:56:26'),
(42, 'Mặt Nạ Ngủ Dưỡng Môi SKINLAX (10g)', 140800, 5, 'picture/moi3.png', 12, '2025-04-29 16:56:26'),
(43, 'Son COLORKEY Watery Tint Bền Màu Lâu Trôi, Không Dính Cốc, Siêu Mịn Môi 1.8g', 139000, 5, 'picture/moi4.png', 12, '2025-04-29 16:56:26'),
(44, 'FOCALLURE Son Tint Siêu Căng Bóng Mọng Nước Lâu Trôi 2g', 119000, 5, 'picture/moi5.png', 12, '2025-04-29 16:56:26'),
(45, '(Tặng Đệm Cao Su Thay Thế) FOCALLURE Dụng Cụ Bấm Lông Mi Giúp Hàng Mi Cong 32g', 39000, 5, 'picture/dungcu1.png', 13, '2025-04-29 16:56:26'),
(46, 'Bộ cọ trang điểm cá nhân GUVIET set 14 cây màu xám bạc có bao da (cốc)', 197000, 5, 'picture/dungcu2.png', 13, '2025-04-29 16:56:26'),
(47, 'FOCALLURE Mút tán trang điểm đa chức năng không mùi cao su mút tán mềm mịn 20g', 26999, 5, 'picture/dungcu3.png', 13, '2025-04-29 16:56:26'),
(48, 'Derf Set 10 Cọ Trang Điểm Derf Chuyên Nghiệp Chất Lượng Cao', 57000, 5, 'picture/dungcu4.png', 13, '2025-04-29 16:56:26'),
(49, 'Bán Chạy Trong Dòng Cọ Nền Siêu Mỏng Không Ăn Bột Đầu Phẳng Liền Mạch Chất Lỏng Nền Cọ Trang Điểm Đầu Phẳng', 15000, 5, 'picture/dungcu5.png', 13, '2025-04-29 16:56:26'),
(50, 'Bông Tai Bạc 925 Hanada Mang 2 Đầu Đá/Bi Tròn Chui Vặn Đeo Nam Nữ 0801 E6', 56000, 5, 'picture/bongtai1.png', 17, '2025-04-29 16:56:26'),
(51, 'Khuyên tai nam bạc 925 thanh thẳng Henry đính đá nhiều size unisex (1 chiếc) | GEMY SILVER KN100', 75, 5, 'picture/bongtai2.png', 17, '2025-04-29 16:56:26'),
(52, 'Khuyên tai bạc unisex TLEE nạm đá tròn bản to đính đá sang trọng TLEE JEWELRY B0138', 80, 5, 'picture/bongtai3.png', 17, '2025-04-29 16:56:26'),
(53, 'Khuyên tai tròn titan G-dragon cực chất', 8900, 5, 'picture/bongtai4.png', 17, '2025-04-29 16:56:26'),
(54, 'Khuyên tai nam nữ bạc 925 hình chữ thập thánh giá mắt xích unisex dáng dài cá tính (1 chiếc) | GEMY SILVER K114', 65, 5, 'picture/bongtai5.png', 17, '2025-04-29 16:56:26'),
(55, '[R2] Nhẫn nam nữ Basic Cuban Ring V2 - Thép không gỉ - Phụ kiện trang sức Unisex Apous', 48000, 5, 'picture/nhan1.png', 16, '2025-04-29 16:56:26'),
(56, 'N008 - Nhẫn nam nữ Basic trơn 4mm màu bạc - Thép Titan - Phụ kiện trang sức Unisex Apous', 23000, 5, 'picture/nhan2.png', 16, '2025-04-29 16:56:26'),
(57, 'Nhẫn BẠC 925 Đá Trụ 6MM Chuẩn 6A 120 Lát Cắt (Bảo hành Trọn Đời) Grace Trang Sức Bạc Đi Tiêc 1028 N11', 89000, 5, 'picture/nhan3.png', 16, '2025-04-29 16:56:26'),
(58, 'Thiết kế thích hợp mở tình yêu đan xen đôi nhẫn nam nữ cá tính cặp nhẫn lụa xanh quấn nhẫn cưới', 29700, 5, 'picture/nhan4.png', 16, '2025-04-29 16:56:26'),
(59, '2 Chiếc Hoạt Hình Anime Cặp Đôi Bộ Nhẫn Hợp Kim Graffiti Phụ Kiện Trang Sức Có Thể Điều Chỉnh', 19000, 5, 'picture/nhan05.png', 16, '2025-04-29 16:56:26'),
(60, 'Dây Chuyền Nam Titan Không Gỉ Tavi Studio Thời Trang Cá Tính Dây Kim Loại Màu Bạc Cao Cấp - Vòng Cổ Tổng hợp', 9000, 5, 'picture/vongco1.png', 15, '2025-04-29 16:56:26'),
(61, 'Vòng cổ MAYEBE LAVEND Mạ Bạc Nhiều Lớp unisex y2k Phong Cách hip hop', 30000, 5, 'picture/vongco2.png', 15, '2025-04-29 16:56:26'),
(62, 'Z Vòng cổ Mạ Vàng 18K Mặt Hình Nhện Cá Tính Cho Nam', 22000, 5, 'picture/vongco3.png', 15, '2025-04-29 16:56:26'),
(63, 'Vòng Cổ Đen Trắng Ma Cặp Đôi Vòng Cổ Đôi Vòng Cổ Ngọt Ngào Mát Bạn Gái Vòng Cổ Cha Mẹ-Con Tất Cả Trận Đấu Phong Cách Mới Dây', 11000, 5, 'picture/vongco4.png', 15, '2025-04-29 16:56:26'),
(64, '1 Thời Trang Hip Hop Phong Cách Thoáng Mát Thép Không Gỉ Hình Học Cặp Đôi Mặt Dây Chuyền Vuông Tam Giác Mặt Dây Chuyền', 19800, 5, 'picture/vongco5.png', 15, '2025-04-29 16:56:26'),
(65, 'Vòng Tay Nam Nữ Lắc Tay Thép Titan Không Gỉ Phong Cách Hàn Quốc Đơn Giản Thời Trang Tavi Studio Nhiều Mẫu Tùy Chọn', 45000, 5, 'picture/vonglac1.png', 15, '2025-04-29 16:56:26'),
(66, 'Vintage Rock Cross Vòng Tay Da Nam Nữ Thời Trang Thép Không Gỉ Nhiều Lớp Đính Hạt Vòng Tay PU Punk Đảng Phụ Kiện Trang', 20900, 5, 'picture/vonglac2.png', 15, '2025-04-29 16:56:26'),
(67, '[KHÔNG ĐEN GỈ] Vòng tay thép Titan mạ vàng cỏ bốn lá may mắn thời trang nữ tính Mely TT71', 65000, 5, 'picture/vonglac3.png', 15, '2025-04-29 16:56:26'),
(68, '1 Thời Trang Dày Foxtail Nam Vòng Tay Hợp Thời Trang Độc Đáo Phong Cách Hip-Hop Vòng Tay Đơn Giản Độc Đoán Tròn Xương Rắn', 14300, 5, 'picture/vonglac4.png', 15, '2025-04-29 16:56:26');

-- --------------------------------------------------------

--
-- Table structure for table `thanhtoan`
--

CREATE TABLE `thanhtoan` (
  `idtt` int(11) NOT NULL,
  `iddh` int(11) NOT NULL,
  `phuongthuctt` varchar(50) NOT NULL,
  `trangthai` varchar(50) NOT NULL,
  `magiaodich` varchar(50) NOT NULL,
  `thoigian` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
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
-- Dumping data for table `user`
--

INSERT INTO `user` (`iduser`, `hoten`, `tendn`, `anh`, `email`, `matkhau`, `sdt`, `diachi`, `quyen`, `thoigian`) VALUES
(1, 'Nguyễn Tuấn Anh', 'anh', 'picture/avt.jpg', '22004335@st.vlute.edu.vn', '$2y$10$YbGRLgwr4RAAHleeMpc4NeBRFQzOBT7vMN89sIAPaeRsAirBSMmlu', '0702804594', 'Cà Mau', '2589', '2025-05-02 04:28:26'),
(2, 'Trần Phương Thế', 'the', 'picture/bee.png', '23004194@st.vlute.edu.vn', '$2y$10$YbGRLgwr4RAAHleeMpc4NeBRFQzOBT7vMN89sIAPaeRsAirBSMmlu', '0987654321', 'Vĩnh Long', '0', '2025-05-02 04:28:40'),
(3, 'Trần Thanh Tâm', 'tamtt', 'picture/bee.png', 'nguyentuanand2589@gmail.com', '$2y$10$DOHYjHV2okk5CCzsS3THwe/8GIASQ49cSJxH9mjnPDQoQoq/IVB9a', '0987654322', 'Vinh Long', '1', '2025-05-02 14:37:46');

-- --------------------------------------------------------

--
-- Table structure for table `yeucaudonhang`
--

CREATE TABLE `yeucaudonhang` (
  `idyc` int(11) NOT NULL,
  `idkh` int(11) NOT NULL,
  `lydo` text NOT NULL,
  `iddh` int(11) NOT NULL,
  `thoigian` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `trangthai` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chattructuyen`
--
ALTER TABLE `chattructuyen`
  ADD PRIMARY KEY (`idchat`);

--
-- Indexes for table `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD PRIMARY KEY (`idctdh`);

--
-- Indexes for table `danhgia`
--
ALTER TABLE `danhgia`
  ADD PRIMARY KEY (`iddg`);

--
-- Indexes for table `danhmucsp`
--
ALTER TABLE `danhmucsp`
  ADD PRIMARY KEY (`iddm`);

--
-- Indexes for table `donhang`
--
ALTER TABLE `donhang`
  ADD PRIMARY KEY (`iddh`);

--
-- Indexes for table `giohang`
--
ALTER TABLE `giohang`
  ADD PRIMARY KEY (`idgh`);

--
-- Indexes for table `hoadon`
--
ALTER TABLE `hoadon`
  ADD PRIMARY KEY (`idhd`);

--
-- Indexes for table `khxoatk`
--
ALTER TABLE `khxoatk`
  ADD PRIMARY KEY (`idkh`);

--
-- Indexes for table `magiamgia`
--
ALTER TABLE `magiamgia`
  ADD PRIMARY KEY (`idmgg`);

--
-- Indexes for table `qrcode`
--
ALTER TABLE `qrcode`
  ADD PRIMARY KEY (`idqr`);

--
-- Indexes for table `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`idsp`);

--
-- Indexes for table `thanhtoan`
--
ALTER TABLE `thanhtoan`
  ADD PRIMARY KEY (`idtt`),
  ADD KEY `fkdonh` (`iddh`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`iduser`);

--
-- Indexes for table `yeucaudonhang`
--
ALTER TABLE `yeucaudonhang`
  ADD PRIMARY KEY (`idyc`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chattructuyen`
--
ALTER TABLE `chattructuyen`
  MODIFY `idchat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  MODIFY `idctdh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `danhgia`
--
ALTER TABLE `danhgia`
  MODIFY `iddg` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `danhmucsp`
--
ALTER TABLE `danhmucsp`
  MODIFY `iddm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `donhang`
--
ALTER TABLE `donhang`
  MODIFY `iddh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `giohang`
--
ALTER TABLE `giohang`
  MODIFY `idgh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `hoadon`
--
ALTER TABLE `hoadon`
  MODIFY `idhd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `khxoatk`
--
ALTER TABLE `khxoatk`
  MODIFY `idkh` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `magiamgia`
--
ALTER TABLE `magiamgia`
  MODIFY `idmgg` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `qrcode`
--
ALTER TABLE `qrcode`
  MODIFY `idqr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `sanpham`
--
ALTER TABLE `sanpham`
  MODIFY `idsp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `thanhtoan`
--
ALTER TABLE `thanhtoan`
  MODIFY `idtt` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `yeucaudonhang`
--
ALTER TABLE `yeucaudonhang`
  MODIFY `idyc` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
