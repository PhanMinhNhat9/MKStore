<?php
require_once '../config.php';
$pdo = connectDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idmgg = $_POST['idmgg'];
    $code = $_POST['code'];
    $phantram = $_POST['phantram'];
    $ngayhieuluc = $_POST['ngayhieuluc'];
    $ngayketthuc = $_POST['ngayketthuc'];
    
    // Cập nhật thông tin mã giảm giá
    $sqlUpdate = "UPDATE magiamgia SET code = :code, phantram = :phantram, ngayhieuluc = :ngayhieuluc, ngayketthuc = :ngayketthuc WHERE idmgg = :idmgg";
    $stmt = $pdo->prepare($sqlUpdate);
    $stmt->execute([
        'code' => $code,
        'phantram' => $phantram,
        'ngayhieuluc' => $ngayhieuluc,
        'ngayketthuc' => $ngayketthuc,
        'idmgg' => $idmgg
    ]);
    
    // Lấy danh sách sản phẩm và danh mục đã được áp dụng
    $sqlApplied = "SELECT idsp, iddm FROM magiamgia_chitiet WHERE idmgg = :idmgg";
    $stmtApplied = $pdo->prepare($sqlApplied);
    $stmtApplied->execute(['idmgg' => $idmgg]);
    $appliedItems = $stmtApplied->fetchAll(PDO::FETCH_ASSOC);

    $appliedProducts = array_column($appliedItems, 'idsp');
    $appliedCategories = array_column($appliedItems, 'iddm');

    // Lấy danh sách sản phẩm và danh mục mới từ form
    $selectedProducts = isset($_POST['sanpham']) ? $_POST['sanpham'] : [];
    $selectedCategories = isset($_POST['danhmuc']) ? $_POST['danhmuc'] : [];

    // Xóa sản phẩm không còn được chọn
    foreach ($appliedProducts as $idsp) {
        if (!in_array($idsp, $selectedProducts)) {
            $sqlDelete = "DELETE FROM magiamgia_chitiet WHERE idmgg = :idmgg AND idsp = :idsp";
            $stmtDelete = $pdo->prepare($sqlDelete);
            $stmtDelete->execute(['idmgg' => $idmgg, 'idsp' => $idsp]);
        }
    }
    
    // Xóa danh mục không còn được chọn
    foreach ($appliedCategories as $iddm) {
        if (!in_array($iddm, $selectedCategories)) {
            $sqlDelete = "DELETE FROM magiamgia_chitiet WHERE idmgg = :idmgg AND iddm = :iddm";
            $stmtDelete = $pdo->prepare($sqlDelete);
            $stmtDelete->execute(['idmgg' => $idmgg, 'iddm' => $iddm]);
        }
    }

    // Thêm sản phẩm mới
    foreach ($selectedProducts as $idsp) {
        if (!in_array($idsp, $appliedProducts)) {
            $sqlInsert = "INSERT INTO magiamgia_chitiet (idmgg, idsp) VALUES (:idmgg, :idsp)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->execute(['idmgg' => $idmgg, 'idsp' => $idsp]);
        }
    }
    
    // Thêm danh mục mới
    foreach ($selectedCategories as $iddm) {
        if (!in_array($iddm, $appliedCategories)) {
            $sqlInsert = "INSERT INTO magiamgia_chitiet (idmgg, iddm) VALUES (:idmgg, :iddm)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->execute(['idmgg' => $idmgg, 'iddm' => $iddm]);
        }
    }

    // Chuyển hướng sau khi cập nhật
    header("Location: capnhatmgg.php?id=$idmgg");
    exit();
} else {
    header("Location: capnhatmgg.php");
    exit();
}
