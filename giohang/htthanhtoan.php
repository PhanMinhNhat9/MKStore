<?php
require_once '../config.php';
$pdo = connectDatabase();

$data = json_decode(file_get_contents("php://input"), true);

$iddh = $data['iddh'] ?? '';
$trangthai = $data['trangthai'] ?? '';
$phuongthuctt = $data['phuongthuctt'] ?? '';
$thoigian = $data['thoigian'] ?? '';
if ($phuongthuctt=='cash') $phuongthuctt="Tiền mặt" ;
else
if ($phuongthuctt=='bank') $phuongthuctt="Chuyển khoản ngân hàng";
if ($iddh && $trangthai && $phuongthuctt && $thoigian) {
    $stmt = $pdo->prepare("UPDATE donhang SET trangthai = ?, phuongthuctt = ?, thoigian = ? WHERE iddh = ?");
    $success = $stmt->execute([$trangthai, $phuongthuctt, $thoigian, $iddh]);

    if ($success) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không thể cập nhật đơn hàng']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Thiếu dữ liệu đầu vào']);
}
