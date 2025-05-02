<?php
require_once '../config.php'; 

// Kết nối database
$pdo = connectDatabase();

// Lấy id mã giảm giá từ GET
$idmgg = isset($_GET['id']) ? intval(base64_decode($_GET['id'])) : 0;

if ($idmgg > 0) {
    try {
        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        $pdo->beginTransaction();

        // Xóa dữ liệu trong bảng magiamgia
        $stmt2 = $pdo->prepare("DELETE FROM magiamgia WHERE idmgg = :idmgg");
        $stmt2->execute([':idmgg' => $idmgg]);

        // Commit transaction nếu mọi thứ thành công
        $pdo->commit();
        echo "<script src='../script.js'></script>";
        echo "<script> 
                alert('Xóa thành công!');
                goBack();
            </script>";
        } catch (Exception $e) {
        // Nếu có lỗi, rollback để không mất dữ liệu quan trọng
        $pdo->rollBack();
        echo "Lỗi: " . $e->getMessage();
    }
} else {
    echo "ID mã giảm giá không hợp lệ!";
}
?>
