<?php
require_once '../config.php';
$pdo = connectDatabase();
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $idsp = base64_decode($_POST['id']);
    // Trả về phản hồi JSON thành công
    echo json_encode(["status" => "success", "message" => "Sản phẩm đã được thêm vào giỏ hàng!"]);
    exit;

}
echo json_encode(["status" => "error", "message" => "Lỗi khi thêm vào giỏ hàng."]);
exit;
?>
