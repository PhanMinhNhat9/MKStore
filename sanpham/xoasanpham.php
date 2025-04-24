<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="../trangchuadmin.js"></script>
</head>
<body>
    
</body>
</html>
<?php
require_once '../config.php'; // Kết nối CSDL
$conn = connectDatabase(); // Hàm kết nối trả về đối tượng PDO

if (isset($_GET['id'])) {
    $id = intval(base64_decode($_GET['id'])); // Giải mã và kiểm tra số nguyên

    if ($id > 0) {
        try {
            $conn->beginTransaction(); // Bắt đầu transaction

            // Lấy tên file QR code từ bảng qrcode
            $qrSelect = "SELECT qrcode FROM qrcode WHERE idsp = :idsp";
            $qrStmt = $conn->prepare($qrSelect);
            $qrStmt->bindParam(':idsp', $id, PDO::PARAM_INT);
            $qrStmt->execute();
            $qrData = $qrStmt->fetch(PDO::FETCH_ASSOC);

            // Nếu có QR code, xóa file vật lý
            if ($qrData && isset($qrData['qrcode'])) {
                $qrFilePath = "../qrcodes/" . $qrData['qrcode'];
                if (file_exists($qrFilePath)) {
                    unlink($qrFilePath); // Xóa file QR code
                }
            }

            // Xóa QR record
            $sqlQR = "DELETE FROM qrcode WHERE idsp = :idsp";
            $stmtQR = $conn->prepare($sqlQR);
            $stmtQR->bindParam(':idsp', $id, PDO::PARAM_INT);
            $stmtQR->execute();

            // Xóa sản phẩm
            $sql = "DELETE FROM sanpham WHERE idsp = :idsp";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idsp', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $conn->commit(); // Nếu mọi thao tác đều OK
                echo "
                <script>
                    window.top.location.href = '../trangchuadmin.php?status=xoaspT';
                </script>";
            } else {
                $conn->rollBack();
                echo "
                <script>
                    window.top.location.href = '../trangchuadmin.php?status=xoaspT';
                </script>";
            }
        } catch (PDOException $e) {
            $conn->rollBack();
            echo "Lỗi khi xóa: " . $e->getMessage();
        }
    } else {
        echo "ID không hợp lệ!";
    }
} else {
    echo "Không có ID sản phẩm!";
}
?>

