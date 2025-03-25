<?php
require_once '../config.php';
$pdo = connectDatabase();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idsp'])) {
    $idsp = intval($_POST['idsp']);

    $stmt = $pdo->prepare("DELETE FROM giohang WHERE idsp = :idsp");
    $stmt->execute(['idsp' => $idsp]);

    echo json_encode(["status" => "success", "message" => "Đã xóa sản phẩm khỏi giỏ hàng."]);
    exit;
}

echo json_encode(["status" => "error", "message" => "Lỗi khi xóa sản phẩm."]);
exit;
?>
