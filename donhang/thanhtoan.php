<?php
require_once '../config.php';
$pdo = connectDatabase();

try {
    // Bắt đầu transaction
    $pdo->beginTransaction();

    // Tính tổng tiền đơn hàng
    $stmt = $pdo->prepare("SELECT SUM(tongtien) AS tongtien FROM giohang WHERE idkh = ?");
    $stmt->execute([$idkh]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $tongtien = $result['tongtien'];

    if ($tongtien <= 0) {
        throw new Exception("Giỏ hàng trống!");
    }

    // Thêm đơn hàng mới vào bảng `donhang`
    $stmt = $pdo->prepare("INSERT INTO donhang (idkh, tongtien, trangthai, phuongthuctt, thoigian) 
                           VALUES (?, ?, 'Chờ xử lý', ?, NOW())");
    $stmt->execute([$idkh, $tongtien, $phuongthuctt]);

    // Lấy ID đơn hàng mới
    $iddh = $pdo->lastInsertId();

    // Chuyển dữ liệu từ `giohang` sang `chitietdonhang`
    $stmt = $pdo->prepare("INSERT INTO chitietdonhang (iddh, idsp, soluong, gia) 
                           SELECT ?, idsp, soluong, tongtien FROM giohang WHERE idkh = ?");
    $stmt->execute([$iddh, $idkh]);

    // Xóa sản phẩm trong `giohang`
    $stmt = $pdo->prepare("DELETE FROM giohang WHERE idkh = ?");
    $stmt->execute([$idkh]);

    // Hoàn thành transaction
    $pdo->commit();

    echo json_encode(["status" => "success", "message" => "Đặt hàng thành công!", "iddh" => $iddh]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
