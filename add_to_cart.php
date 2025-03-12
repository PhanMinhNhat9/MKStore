<?php
require_once 'config.php';
if (session_status() == PHP_SESSION_NONE) session_start();

header('Content-Type: application/json'); // << BẮT BUỘC

if (!isset($_POST['idsp'])) {
    echo json_encode(['status' => 'error', 'message' => 'Thiếu ID sản phẩm']);
    exit;
}

$idsp = $_POST['idsp'];

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_SESSION['cart'][$idsp])) {
    $_SESSION['cart'][$idsp]++;
} else {
    $_SESSION['cart'][$idsp] = 1;
}

echo json_encode(['status' => 'success', 'message' => 'Đã thêm sản phẩm vào giỏ hàng']);
?>