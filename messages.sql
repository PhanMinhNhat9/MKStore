-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 02, 2025 lúc 03:57 PM
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
(12, 1, 17, 'Cái này là của Tuán anh Gửi nè !!', '2025-04-02 13:54:51');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
