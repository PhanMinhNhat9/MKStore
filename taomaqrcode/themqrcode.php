<?php
// require_once 'config.php'; // Import file kết nối CSDL

// $pdo = connectDatabase(); // Kết nối CSDL

// // Duyệt qua ID từ 1 đến 70 để thêm vào CSDL
// for ($idsp = 1; $idsp <= 70; $idsp++) {
//     $qrcode = "sp_$idsp.png"; // Tên file QR (đã có sẵn)

//     // Kiểm tra nếu idsp đã tồn tại trong bảng qrcode
//     $checkQuery = "SELECT idsp FROM qrcode WHERE idsp = ?";
//     $stmt = $pdo->prepare($checkQuery);
//     $stmt->execute([$idsp]);

//     if ($stmt->rowCount() == 0) {
//         // Chèn dữ liệu vào bảng qrcode
//         $insertQuery = "INSERT INTO qrcode (qrcode, idsp) VALUES (?, ?)";
//         $stmt = $pdo->prepare($insertQuery);
//         $stmt->execute([$qrcode, $idsp]);
//     }
// }

// echo "Thêm dữ liệu vào bảng qrcode thành công!";
?>
