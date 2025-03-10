<?php
require_once '../config.php'; 

// Kết nối database
$pdo = connectDatabase();

// Lấy id mã giảm giá từ GET
$idmgg = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idmgg > 0) {
    try {
        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        $pdo->beginTransaction();

        // Xóa dữ liệu trong bảng magiamgia_chitiet trước
        $stmt1 = $pdo->prepare("DELETE FROM magiamgia_chitiet WHERE idmgg = :idmgg");
        $stmt1->execute([':idmgg' => $idmgg]);

        // Xóa dữ liệu trong bảng magiamgia
        $stmt2 = $pdo->prepare("DELETE FROM magiamgia WHERE idmgg = :idmgg");
        $stmt2->execute([':idmgg' => $idmgg]);

        // Commit transaction nếu mọi thứ thành công
        $pdo->commit();

        echo "<script>alert('Xóa thành công!'); window.location.href='hienthimgg.php';</script>";
    } catch (Exception $e) {
        // Nếu có lỗi, rollback để không mất dữ liệu quan trọng
        $pdo->rollBack();
        echo "Lỗi: " . $e->getMessage();
    }
} else {
    echo "ID mã giảm giá không hợp lệ!";
}
?>
