<?php
require_once '../config.php';
$pdo = connectDatabase();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idsp']) && isset($_POST['change'])) {
    $idsp = intval($_POST['idsp']);
    $change = intval($_POST['change']);

     // Kiểm tra sản phẩm có tồn tại không
     $stmt = $pdo->prepare("SELECT giaban FROM sanpham WHERE idsp = :idsp");
     $stmt->execute(['idsp' => $idsp]);
     $sanpham = $stmt->fetch(PDO::FETCH_ASSOC);

     if (!$sanpham) {
         echo json_encode(["status" => "error", "message" => "Sản phẩm không tồn tại."]);
         exit;
     }

     $giaban = $sanpham['giaban'];

    // Kiểm tra sản phẩm có trong giỏ hàng chưa
    $stmt = $pdo->prepare("SELECT soluong FROM giohang WHERE idsp = :idsp");
    $stmt->execute(['idsp' => $idsp]);
    $giohang = $stmt->fetch(PDO::FETCH_ASSOC);

    $newQuantity = max(1, $giohang['soluong'] + $change);

    // Cập nhật số lượng và tổng tiền
    $stmt = $pdo->prepare("UPDATE giohang 
                           JOIN sanpham ON giohang.idsp = sanpham.idsp 
                           SET giohang.soluong = :soluong, giohang.tongtien = :tongtien 
                           WHERE giohang.idsp = :idsp");
    $stmt->execute([
        'soluong' => $newQuantity,
        'tongtien' => $newQuantity * $giaban,
        'idsp' => $idsp
    ]);

    echo json_encode(["status" => "success", "message" => "Cập nhật giỏ hàng thành công."]);
    exit;
}

echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ."]);
exit;
?>
