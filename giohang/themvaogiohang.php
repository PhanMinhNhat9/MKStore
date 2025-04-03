<?php
require_once '../config.php';
$pdo = connectDatabase();

if (isset($_GET['id'])) {
    $idsp = base64_decode($_GET['id']);

    try {
        $pdo->beginTransaction();

        // Kiểm tra sản phẩm có tồn tại không
        $stmt = $pdo->prepare("SELECT tensp, giaban FROM sanpham WHERE idsp = :idsp");
        $stmt->execute(['idsp' => $idsp]);
        $sanpham = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sanpham) {
            echo "error"; // Sản phẩm không tồn tại
            exit;
        }

        $giaban = $sanpham['giaban'];
        $tensp = $sanpham['tensp'];
        
        // Kiểm tra sản phẩm có trong giỏ hàng chưa
        $stmt = $pdo->prepare("SELECT soluong FROM giohang WHERE idsp = :idsp");
        $stmt->execute(['idsp' => $idsp]);
        $giohang = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($giohang) {
            // Nếu đã có, tăng số lượng lên 1
            $newQuantity = $giohang['soluong'] + 1;
            $stmt = $pdo->prepare("UPDATE giohang SET soluong = :soluong, tongtien = :tongtien WHERE idsp = :idsp");
            $stmt->execute([
                'soluong' => $newQuantity,
                'tongtien' => $newQuantity * $giaban,
                'idsp' => $idsp
            ]);
        } else {
            // Nếu chưa có, thêm mới vào giỏ hàng
            $stmt = $pdo->prepare("INSERT INTO giohang (idsp, soluong, tongtien, thoigian) 
                                   VALUES (:idsp, 1, :tongtien, NOW())");
            $stmt->execute([
                'idsp' => $idsp,
                'tongtien' => $giaban
            ]);
        }

        $pdo->commit();
        echo "success"; // ✅ Thêm thành công
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "error"; // ❌ Thêm thất bại
    }
    exit;
}
?>
