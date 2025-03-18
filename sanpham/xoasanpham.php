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
                echo "<script src='../trangchuadmin.js'></script>";
                echo "<script> 
                        alert('Xóa thành công!');
                        goBack();
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
