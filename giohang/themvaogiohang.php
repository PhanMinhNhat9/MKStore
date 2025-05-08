<?php
require_once '../config.php';
$pdo = connectDatabase();

if (isset($_GET['id'])) {
    $idsp = base64_decode($_GET['id']);
    $sldaban = $_GET['sldaban'] ?? 0;
    try {
        $pdo->beginTransaction();

        // Kiểm tra sản phẩm có tồn tại không
        $stmt = $pdo->prepare("SELECT tensp, giaban, iddm, soluong FROM sanpham WHERE idsp = :idsp");
        $stmt->execute(['idsp' => $idsp]);
        $sanpham = $stmt->fetch(PDO::FETCH_ASSOC);

        $giaban = $sanpham['giaban'];
        $tensp = $sanpham['tensp'];
        $soluong = $sanpham['soluong'];
        $iddm = $sanpham['iddm'];

        $stmt = $pdo->prepare("SELECT `idmgg`, `code`, `phantram`, `ngayhieuluc`, `ngayketthuc`, 
        `giaapdung`, `iddm`, `soluong`, `thoigian` FROM `magiamgia` WHERE iddm=:iddm");
        $stmt->execute(['iddm' => $iddm]);
        $mgg = $stmt->fetch(PDO::FETCH_ASSOC);
        $giagiam = 0;
        if (!$mgg) {
            $giagiam = $giaban;
        } else
        {
            $phantram = $mgg['phantram'];
            $giagiam = $giaban * (1-$phantram/100);
        }

        // Kiểm tra sản phẩm có trong giỏ hàng chưa
        $stmt = $pdo->prepare("SELECT soluong FROM giohang WHERE idsp = :idsp AND idkh = :idkh");
        $stmt->execute(['idsp' => $idsp, 'idkh' => $_SESSION['user']['iduser']]);
        $giohang = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($giohang) {
            $newQuantity = $giohang['soluong'] + $sldaban;

            if ($newQuantity >= $soluong) {
                echo "null";
                exit; // Không đủ hàng, thoát
            }
        }

        if ($giohang) {
            // Nếu đã có, tăng số lượng lên 1
            $newQuantity = $giohang['soluong'] + 1;

            $stmt = $pdo->prepare("UPDATE giohang SET soluong = :soluong, thanhtien = :thanhtien WHERE idsp = :idsp AND idkh = :idkh");
            $stmt->execute([
                'soluong' => $newQuantity,
                'thanhtien' => $newQuantity * $giaban,
                'idsp' => $idsp,
                'idkh' => $_SESSION['user']['iduser']
            ]);
        } 
        else {
            // Nếu chưa có, thêm mới vào giỏ hàng
            $stmt = $pdo->prepare("INSERT INTO giohang (idkh, idsp, tensp, giagoc, giagiam, soluong, thanhtien) 
                                   VALUES (:idkh, :idsp, :tensp, :giagoc, :giagiam, 1, :thanhtien)");
            $stmt->execute([
                'idkh' => $_SESSION['user']['iduser'],
                'idsp' => $idsp,
                'tensp' => $tensp,
                'giagoc' => $giaban,
                'giagiam' => $giagiam,
                'thanhtien' => $giagiam
            ]);
        }

        $pdo->commit();
        echo "success"; 
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "error";
    }
    exit;
}
?>
