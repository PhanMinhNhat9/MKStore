<?php
require_once '../config.php';
$pdo = connectDatabase();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $iddh = $_POST['iddh'] ?? '';
    $idkh = $_POST['idkh'] ?? '';

    try {
        // Xóa bản ghi từ bảng yeucaudonhang
        $stmt = $pdo->prepare("DELETE FROM yeucaudonhang WHERE idkh = :idkh AND iddh = :iddh");
        $stmt->execute([
            ':idkh' => $idkh,
            ':iddh' => $iddh
        ]);
        echo "<script>window.top.location.href = '../trangchu.php?status=khoiphucdhT';</script>";
    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
        echo "<script>window.top.location.href = '../trangchu.php?status=khoiphucdhF';</script>";
    }
} else {
    echo "Phương thức gửi không hợp lệ.";
}
?>