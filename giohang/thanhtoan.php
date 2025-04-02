<?php
require_once '../config.php';
$pdo = connectDatabase();
$phone = isset($_GET['phone']) ? trim($_GET['phone']) : '';
$name = isset($_GET['name']) ? $_GET['name'] : '';
if (!empty($phone)) {

    // Thêm vào bảng giohang
    $sql = "UPDATE giohang SET idgh = :phone WHERE idgh ='0'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        echo "Thêm vào giỏ hàng thành công!";
    } else {
        echo "Lỗi khi thêm vào giỏ hàng.";
    }
    
    try {
        // Bắt đầu transaction
        $pdo->beginTransaction();
        // Tính tổng tiền đơn hàng
        $stmt = $pdo->prepare("SELECT SUM(tongtien) AS tongtien FROM giohang WHERE idgh = ?");
        $stmt->execute([$phone]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $tongtien = $result['tongtien'];
        $phuongthuctt = "Tiền mặt";
        echo "Tổng tiền: ".$tongtien;
        if ($tongtien <= 0) {
            throw new Exception("Giỏ hàng trống!");
        }
    
        // // Thêm đơn hàng mới vào bảng `donhang`
        // $stmt = $pdo->prepare("INSERT INTO donhang (idkh, tongtien, trangthai, phuongthuctt) 
        //                        VALUES (?, ?, 'Chờ xử lý', ?)");
        // $stmt->execute([$idkh, $tongtien, $phuongthuctt]);
    
        // // Lấy ID đơn hàng mới
        // $iddh = $pdo->lastInsertId();
    
        // $stmt = $pdo->prepare("INSERT INTO chitietdonhang (iddh, idsp, soluong, gia) 
        //                        SELECT :iddh, idsp, soluong, tongtien FROM giohang WHERE idgh = :idgh");
        // $stmt->execute([
        // 'iddh' => $iddh,
        // 'idgh' => $idgh
        // ]);
    
    
        // // Xóa sản phẩm trong `giohang`
        // $stmt = $pdo->prepare("DELETE FROM giohang WHERE idgh = ?");
        // $stmt->execute([$idgh]);
    
        // // Hoàn thành transaction
        // $pdo->commit();
    
        // echo json_encode(["status" => "success", "message" => "Đặt hàng thành công!", "iddh" => $iddh]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo "Số điện thoại không hợp lệ!";
}

?>
