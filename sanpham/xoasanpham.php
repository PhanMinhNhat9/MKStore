<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="../sweetalert2/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../sweetalert2/sweetalert2.min.css">
    <script src="../trangchuadmin.js"></script>
</head>
<body>
    
</body>
</html>
<?php
include '../config.php'; // Kết nối CSDL
$conn = connectDatabase(); // Hàm kết nối trả về đối tượng PDO

if (isset($_GET['id'])) {
    $id = intval(base64_decode($_GET['id'])); // Giải mã và kiểm tra số nguyên

    if ($id > 0) {
        try {
            $sql = "DELETE FROM sanpham WHERE idsp = :idsp";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idsp', $id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                echo "
                <script>
                    showCustomAlert('🐳 Xóa Thành Công!', 'Sản phẩm đã được xóa khỏi danh sách!', '../picture/success.png');
                    setTimeout(function() {
                        goBack();
                    }, 3000); 
                </script>";
            } else {
                echo "Không tìm thấy sản phẩm để xóa!";
            }
        } catch (PDOException $e) {
            echo "Lỗi khi xóa: " . $e->getMessage();
        }
    } else {
        echo "ID không hợp lệ!";
    }
} else {
    echo "Không có ID sản phẩm!";
}
?>
