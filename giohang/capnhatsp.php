<?php
require_once '../config.php';
$pdo = connectDatabase();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idsp']) && isset($_POST['change'])) {
    $idsp = intval($_POST['idsp']);
    $change = intval($_POST['change']);

     // Kiểm tra sản phẩm có tồn tại không
     $stmt = $pdo->prepare("SELECT giagoc, giagiam, soluong FROM giohang WHERE idsp = :idsp");
     $stmt->execute(['idsp' => $idsp]);
     $giohang = $stmt->fetch(PDO::FETCH_ASSOC);

    //  if (!$giohang) {
    //      echo json_encode(["status" => "error", "message" => "Sản phẩm không tồn tại."]);
    //      exit;
    //  }

     $giaban = $giohang['giagoc'];
     $giagiam = $giohang['giagiam'];
     $soluong = $giohang['soluong'];

    $newQuantity = max(1, $giohang['soluong'] + $change);

    // Cập nhật số lượng và tổng tiền
    $stmt = $pdo->prepare("UPDATE giohang 
                           SET giohang.soluong = :soluong, giohang.thanhtien = :thanhtien 
                           WHERE giohang.idsp = :idsp");
    $stmt->execute([
        'soluong' => $newQuantity,
        'thanhtien' => $newQuantity * $giagiam,
        'idsp' => $idsp
    ]);

    echo json_encode(["status" => "success", "message" => "Cập nhật giỏ hàng thành công."]);
    exit;
}

echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ."]);
exit;
?>
