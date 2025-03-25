<?php
// require_once 'phpqrcode-master/qrlib.php'; // Import thư viện phpqrcode

// // Tạo thư mục lưu QR nếu chưa có
// $qrDir = "qrcodes/";
// if (!is_dir($qrDir)) {
//     mkdir($qrDir, 0777, true);
// }

// for ($idsp = 1; $idsp <= 70; $idsp++) {
//     $file = $qrDir . "sp_$idsp.png"; // Tạo đường dẫn lưu QR

//     // Kiểm tra nếu chưa có thì tạo mới
//     if (!file_exists($file)) {
//         QRcode::png($idsp, $file, QR_ECLEVEL_L, 5);
//     }
// }

// echo "Đã tạo mã QR từ 1 đến 70 trong thư mục qrcodes/";
?>
